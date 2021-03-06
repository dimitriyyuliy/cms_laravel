<?php

namespace App\Modules\Auth\Controllers;

use App\Models\{Main, User};
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use App\Helpers\User as userHelpers;

class LoginController extends AppController
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;
    //use ThrottlesLogins;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->middleware('guest')->except('logout');

        $class = $this->class = str_replace('Controller', '', class_basename(__CLASS__));
        $c = $this->c = Str::lower($this->class);
        $view = $this->view = Str::snake($this->class);
        Main::set('c', $c);
        View::share(compact('class', 'c', 'view'));
    }


    public function showLoginForm(Request $request)
    {
        Main::viewExists("{$this->viewPathModule}.{$this->view}", __METHOD__);
        return view("{$this->viewPathModule}.{$this->view}");
    }


    // Поля для валидации
    protected function validateLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
            //'g-recaptcha-response' => 'required|recaptcha',
        ]);
    }


    // Действия после успешной авторизации
    protected function authenticated(Request $request, $user)
    {
        // Записать ip пользователя в БД
        $email = $user->email;
        $ip = $request->ip();

        // Записать ip пользователя в БД
        User::saveIpStatic($email, $ip);
        //$user->saveIp();

        // Если пользователь админ или редактор запишем в логи об авторизации
        if (isset($user->role_id) && in_array($user->role_id, User::roleIdAdmin())) {
            Log::info('Authorization of user with access Admin. ' . Main::dataUser());
        }
    }


    /*public function logout(Request $request)
    {
        $this->guard()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }


    public function login(Request $request)
    {
        if ($request->isMethod('post')) {

            $rules = [
                'email' => 'required|string|email',
                'password' => 'required|string',
            ];
            $this->validate($request, $rules);

            // Laravel блокирует неправильные попытки входа
            if ($this->hasTooManyLoginAttempts($request)) {
                $this->fireLockoutEvent($request);

                return $this->sendLockoutResponse($request);
            }

            if ($this->attemptLogin($request)) {
                return $this->sendLoginResponse($request);
            }

            $this->incrementLoginAttempts($request);

            return $this->sendFailedLoginResponse($request);
        }
        Main::getError('No post request', __METHOD__);
    }


    protected function attemptLogin(Request $request)
    {
        return $this->guard()->attempt(
            $this->credentials($request), $request->filled('remember')
        );
    }


    protected function credentials(Request $request)
    {
        return $request->only($this->username(), 'password');
    }


    protected function sendLoginResponse(Request $request)
    {
        $request->session()->regenerate();
        $this->clearLoginAttempts($request);

        return $this->authenticated($request, $this->guard()->user())
            ?: redirect()->intended($this->redirectPath());
    }


    // Действия после успешной авторизации
    protected function authenticated(Request $request, $user)
    {
        $email = $request->email;
        $ip = $request->ip();

        // Записать ip пользователя в БД
        User::saveIpStatic($email, $ip);

        // Если пользователь админ или редактор запишем в логи об авторизации
        if (isset($user->role_id) && in_array($user->role_id, User::roleIdAdmin())) {
            Log::info('Authorization of user with access Admin. ' . Main::dataUser());
        }

        return redirect()->route('home');
    }


    protected function sendFailedLoginResponse(Request $request)
    {
        throw ValidationException::withMessages([
            $this->username() => [trans('auth.failed')],
        ]);
    }*/
}
