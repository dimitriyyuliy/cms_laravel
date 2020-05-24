<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Сообщения для админки
    |--------------------------------------------------------------------------
    */

    'Dashboard' => 'Панель управления',
    'Website' => 'Сайт',
    'Home' => 'Главная',
    'en' => 'Английский',
    'ru' => 'Русский',

    'Pages' => 'Страницы',
    'IPages' => 'страница|страницы|страниц',
    'Order' => 'Заказ',
    'Orders' => 'Заказы',
    'Forms' => 'Формы',
    'IForms' => 'форма|формы|форм',
    'Categories' => 'Категории',
    'category_many' => 'Категории',
    'Products' => 'Товары',
    'product_many' => 'Товары',
    'Files' => 'Файлы',
    'File_manager' => 'Файловый менеджер',
    'Users' => 'Пользователи',
    'user_many' => 'Пользователи',
    'Menu' => 'Меню',
    'Menu_name' => 'Список меню',
    'Settings' => 'Настройки',
    'Additionally' => 'Дополнительно',
    'Cache' => 'Кэш',
    'Commands' => 'Команды',
    'Logs' => 'Логи',
    'Backup' => 'Резервное копирование',

    'Create' => 'Создать',
    'Change' => 'Изменить',
    'Remove' => 'Удалить',
    'List' => 'Список',
    'List_item' => 'Список элементов',
    'Confirm' => 'Подтвердить',
    'Code' => 'Код',
    'Status' => 'Статус',
    'Note' => 'Заметки',
    'Create_item' => 'Создать элемент',
    'Create_group' => 'Создать группу',
    'Create_new_menu' => 'Создать новое меню',

    'Server_space' => 'Место на сервере',
    'Busy' => 'Занято',
    'Freely' => 'Свободно',
    'Total' => 'Всего',
    'Be_careful_when_editing' => 'Будьте осторожны при правках файлов и папок, от этого зависит правильная работа этого веб‑приложения!',
    'backup_update' => 'Резервное копирование и обновление веб-сайта',
    'Has_been_executed' => 'Было выполнено:',
    'Next_date' => 'Дата следующего:',
    'path_archive' => 'Путь к архиву:',
    'Key_use_site' => 'Новый ключ для сайта ',

    'Profile' => 'Профиль',
    'Exit' => 'Выход',
    'title' => 'Название',
    'slug' => 'Ссылка',
    'status' => 'Статус',
    'action' => 'Действия',
    'show' => 'Редактировать',
    'edit' => 'Редактировать',
    'view' => 'Просмотр',

    'shown' => 'Показано ',
    'of' => ' из ',
    'cyrillic_to_latin' => 'Меняются кириллические символы на латинские',
    'transliterator' => 'Транслитератор',
    'generate' => 'Генерировать',
    'generate_link' => 'Сгенерировать ссылку',
    'you_sure' => 'Вы уверены?',
    'example_use_in_views' => 'Пример использования в видах:',

    'cache_deleted' => 'Кэш успешно удалён',
    'db_caches' => 'Кэши запросов в БД',
    'view_caches' => 'Кэши видов',
    'route_caches' => 'Кэши маршрутов',
    'config_caches' => 'Кэши конфигурации',
    'select_command' => 'Выберите команду',
    'run' => 'Выполнить',
    'completed_successfully' => 'Успешно выполнено',
    'command' => 'Команда',

    'example_commands' => 'Примеры команд',
    'make:module' => 'Создать модуль',
    'module_add_text' => ' Name с моделью и миграцией. Без параметров только файлы, --model - с моделью, --migration - с миграцией',
    'make:controller' => 'Создать контроллер',
    'controller' => ' контроллер',
    'make:model' => 'Создать модель',
    'model' => ' модель',
    'with_model' => ' с моделью',
    'make:middleware' => 'Создать middleware',
    'migration_' => ' миграцию',
    'in' => ' в ',
    'for' => ' для ',
    'migrate' => 'Запустить мигриции',
    'migrate:rollback' => 'Удалить последнюю миграцию',
    'create_migration_table' => 'Создать миграцию и таблицу',
    'change_migration_keep_data_table' => 'Изменить миграцию, чтобы данные в таблице сохранились',
    'change_add_column_migration_keep_data_table' => 'Изменить миграцию, добавив колонку, чтобы данные в таблице сохранились',
    'make:migration' => 'Добавить, изменить миграцию',

    'key_to_enter' => 'Ключ для входа',
    'key_description' => 'При изменении ключа, он будет выслан всем пользователям административного раздела',
    'key_success' => 'Новый ключ успешно создан',
    'search' => 'Поиск',
    'selected' => 'Выбрано',
    'Banned_ip' => 'Блокированные IP',
    'notes' => 'Заметки',
    'main' => 'Основное',
    'content' => 'Контент',
    'gallery' => 'Галерея',
    'image_gallery' => 'Галерея картинок',
    'backup_files_db' => 'Резервное копирование файлов и БД',

    'ImportExport' => 'Импорт экспорт',
    'import' => 'Импорт',
    'export' => 'Экспорт',
    'choose_file' => 'Выберите файл',
    'upload_success' => 'Загрузка успешна',
    'rows_were_skipped' => 'Были пропущены ряды, в которых не заполнены обязательные поля: ',
    'updated_elements' => '. Обновлено элементов: ',
    'new_elements_inserted' => '. Вставлено новых элементов: ',
    'not_unique_element' => 'В элементе ID :id не уникально: ',
    'creating_model_to_import' => 'Создадим модель для импорта из эксель',
    'creating_model_to_export' => 'Создадим модель для экспорта из эксель',

    'Translate' => 'Переводы',
    'first_create_menu' => 'Сначала создайте меню.',
    'Filter_group' => 'Группы фильтров',
    'Filter_value' => 'Фильтры',
    'online_users' => 'Пользователи онлайн',

];
