<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

    use AuthenticatesUsers {
        logout as performLogout;
    }


    protected function attemptLogin(Request $request)
    {
        return Auth::attempt([
            'email' => $request->email,
            'password' => $request->password,
            'role' => 'admin' // Change 'admin' to the desired role
        ], $request->filled('remember'));

    }


    protected function authenticated(Request $request, $user)
    {
        return redirect()->route('admin.index'); // Change to your default user route
    }

    public function logout(Request $request)
    {
        $this->performLogout($request);
        return redirect()->route('admin.index');
    }



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
}
