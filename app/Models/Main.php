<?php

namespace App\Models;


use App\Helpers\Children;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Illuminate\Database\Eloquent\Model;

class Main
{
    // Main - ВСПОМОГАТЕЛЬНЫЙ СТАТИЧНЫЙ КЛАСС

    /*
     * Пример использования паттерна Реестр (использовать в видах с \App\):
     * Main::$registry->set('test', 'testing'); - положить
     * dump(Main::$registry->get('test')); - достать
     * dump(Main::$registry->getAll); - достать всё
     */
    public static $registry;


    /*
     * Упрощение вызова паттерна Реестр (использовать в видах с \App\):
     * Main::set('test', 'testing'); - положить
     * dump(Main::get('test')); - достать
     */
    public static function set($name, $value)
    {
        if ($name) {
            self::$registry->set($name, $value);
        }
        return;
    }
    public static function get($value)
    {
        return self::$registry->get($value) ?? null;
    }


    /*
     * Возвращает настройку сайта.
     * Main::site('name') - достать настройку.
     * $settingName - название настройки.
     */
    public static function site($settingName)
    {
        if ($settingName) {
            return self::$registry->get('settings')[$settingName] ?? null;
        }
        return null;
    }


    /*
     * Подключает файл из /app/Modules/views/inc с название написаном в контенте ##!!!inc_name (название файла inc_name.blade.php).
     * $content - если передаётся контент, то в нём будет искаться ##!!!inc_name и заменяется на файл из папки inc.
     * $values - $values5 - Можно передать данные в подключаемый файл.
     */
    public static function inc(string $content = null, $values = null, $values2 = null, $values3 = null, $values4 = null, $values5 = null)
    {
        if ($content) {

            $search = '##!!!'; // \w+(?=##!!!) test##!!!    (?<=##!!!)\w+ ##!!!test
            $pattern = '/(?<=' . $search . ')\w+/';
            preg_match_all($pattern, $content, $matches, PREG_SET_ORDER);

            if ($matches) {
                $views = 'views.inc';

                foreach ($matches as $v) {
                    $view = "{$views}.inc_{$v[0]}";
                    $pattern_inner = '/' . $search . $v[0] . '/';

                    if (view()->exists($view)) {

                        $output = view($view, compact('values', 'values2', 'values3', 'values4', 'values5'))->render();
                        $content = preg_replace($pattern_inner, $output, $content, 1);
                    } else {
                        $content = preg_replace($pattern_inner, '', $content);
                    }

                    /*$inc = config('modules.path') . "/views/inc/inc_{$v[0]}.blade.php";
                    $pattern_inner = '/' . $search . $v[0] . '/';

                    if (File::isFile($inc)) {

                        ob_start();
                        include_once $inc;
                        $output = ob_get_clean();

                        // Замена произойдёт 1 раз;
                        $content = preg_replace($pattern_inner, $output, $content, 1);

                    } else {
                        $content = preg_replace($pattern_inner, '', $content);
                    }*/
                }
            }
        }
        return $content;
    }


    /*
     * Использовать скрипты в контенте, они будут перенесены вниз страницы.
     * $content - контент, в котором удалиться скрипты и перенести их вниз страницы.
     * В шаблоне вида получить скрипты с помощью Main::get('scripts').
     */
    public static function getDownScript($content)
    {
        if ($content) {
            $scripts = [];
            $pattern = "#<script.*?>.*?</script>#si";
            preg_match_all($pattern, $content, $scripts);

            if (!empty($scripts[0])) {
                self::$registry->set('scripts', $scripts[0]);
                $content = preg_replace($pattern, '', $content);
            }
            return $content;
        }
        return false;
    }


    /*
     * Метод вывода мета тегов в head, для использования в шаблоне.
     * Вызовите вместо html тегов title, description и keywords, для этого в /app/Http/Controllers/Controller.php создате:
     protected function setMeta($title, $description = '', $titleSeo = null, $keywords = null)
    {
        Main::setMeta($title, $description, $titleSeo, $keywords);
    }
     *
     * $title - строка для вывода title.
     * $description - строка для вывода description, по-умолчанию пустая строка, необязательный параметр.
     * $titleSeo - если title для окна браузера отличается от title передаваемого в шаблон, то передать его здесь, по-умолчанию берётся title, необязательный параметр.
     * $keywords - ключевые слова для страницы, по-умолчанию не выводятся, необязательный параметр.
     */
    public static function setMeta($title, $description = '', $titleSeo = null, $keywords = null)
    {
        $siteName = self::site('name') ?: ' ';

        // Если нет $title, то передадим название сайта
        if (!$title) $title = $siteName;

        // Если нет $titleSeo, то передадим в неё $title
        if (!$titleSeo) $titleSeo = $title;

        // Для главной страницы сначала название сайта, а для остальных - сначала title, потом название
        $titleSeo = request()->is('/') ? "{$siteName} | {$titleSeo}" : "{$titleSeo} | {$siteName}";

        // Формируем метатеги
        $getMeta = "<title>{$titleSeo}</title>\n\t";
        $getMeta .= "<meta name=\"description\" content=\"{$description}\" />\n";

        if ($keywords) {
            $getMeta .= "<meta name=\"keywords\" content=\"{$keywords}\" />\n";
        }

        // Переменные передаются в виды
        View::share(compact('title', 'titleSeo', 'description', 'getMeta'));
        return;
    }


    /*
     * Возвращает строку: URL, Email, IP пользователя.
     * $referer - передать true, если нужно вывести страницу, с которой перешёл пользователь, необязательный параметр.
     */
    public static function dataUser($referer = null)
    {
        $email = auth()->check() && isset(auth()->user()->email) ? '. Email: ' . auth()->user()->email . '.' : null;
        if ($referer) {
            $referer = !empty(request()->server('HTTP_REFERER')) ? '. Referer: ' . request()->server('HTTP_REFERER') . '. ' : null;
        }
        return "URL: " . request()->url() . "{$email} IP: " . request()->ip() . ". {$referer}";
    }


    /*
     * Если вида не существует, то записывает в логи ошибку и выбрасывает исключение.
     * $view - название вида (page.index).
     * $method - передать __METHOD__.
     */
    public static function viewExists(string $view, $method)
    {
        if (!view()->exists($view)) {
            $message = "View $view not found. " . self::dataUser() . "Error in {$method}";
            Log::critical($message);
            abort('404', $message);
        }
        return;
    }


    /*
     * Записывает в логи ошибку и выбрасывает исключение (если выбрано).
     * $message - текст сообщения.
     * $method - передать __METHOD__.
     * $abort - выбросывать исключение, по-умолчанию true, необязательный параметр.
     * $error - в каком виде записать ошибку, может быть: emergency, alert, critical, error, warning, notice, info, debug. По-умолчанию error, необязательный параметр.
     */
    public static function getError(string $message, $method, $abort = true, $error = 'error')
    {
        $message = "{$message}. " . self::dataUser() . "In {$method}";
        Log::$error($message);
        if ($abort) {
            abort('404', $message);
        }
        return;
    }


    /*
     * Проверить существует ли модуль, возвращает true или false.
     * $module - передать строкой название модуля.
     */
    public static function issetModule(string $module)
    {
        if ($module) {
            $modules = config('modules.modules');
            return $modules && is_array($modules) && key_exists($module, $modules);
        }
        return false;
    }


    // Возвращает URL без префикса языка и без папки public.
    public static function notPublicInURL()
    {
        $url = request()->url();
        $public = '/public';
        if (stripos($url, $public)) {
            $url = str_replace($public, '', $url);
        }
        return $url;
    }
}
