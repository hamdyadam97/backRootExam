<?php

namespace App\Http\Controllers\Auth;

use Session;
use App\User;
use Illuminate\Http\Request;
use Validator, Redirect, Response;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Auth;

class LoginController extends Controller
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
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
  
     /**
     * Write code on Method
     *
     * @return response()
     */

    protected function validateLogin(Request $request)
    {
        $messages = [];
        $this->validate($request, [
            'mobile' => 'required|numeric|exists:users,mobile',
            'password' => 'required',
        ], $messages);
    }
    protected function credentials(Request $request)
    {
        return [
            'mobile' => $request->get('mobile'),
            'password' => $request->get('password'),
            'status' => 1,
            'role_type' =>1,
        ];
    }
    public function authenticate(Request $request)
    {
        if (Auth::attempt(['mobile' => $mobile, 'password' => $password])) {
            return redirect()->intended($this->redirectPath());
        }
        return redirect("login")->with('error', 'Oppes! You have entered invalid credentials');
    }

    
    public function logout()
    {
        $locale = session()->get('lang');
        $locale = isset($locale) && !empty($locale) ? $locale : 'en';
        Session::flush();
        App::setLocale($locale);
        Session::put('lang', $locale);
        Session::save();
        \Auth::logout();
        return Redirect('login');
    }
}
