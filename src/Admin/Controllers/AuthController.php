<?php

namespace Aparlay\Core\Admin\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use function redirect;

class AuthController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\View
     */
    public function viewLogin()
    {
        return view('default_view::admin.pages.auth.login');
    }

    /**
     * @param Request $request
     */
    public function postLogin(Request $request)
    {
        $credentials = $request->only(['email', 'password']);
        $remember = $request->get('remember');

        //make sure only admin type user can login
        $credentials['type'] = 1;

        if (Auth::guard('admin')->attempt($credentials, $remember)) {
            return redirect()->intended('admin/dashboard');
        } else {
            return back()->withErrors([
                'error' => 'The provided credentials are incorrect.',
            ]);
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
