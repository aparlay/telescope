<?php

namespace Aparlay\Core\Admin\Controllers;

use Aparlay\Core\Admin\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use function redirect;
use Aparlay\Core\Admin\Requests\AuthRequest;

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
     * @param  Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postLogin(AuthRequest $request)
    {
        $credentials = $request->only(['email', 'password']);
        $remember = $request->get('remember');

        //make sure only admin type user can do login
        $credentials['type'] = User::TYPE_ADMIN;

        if (Auth::guard('admin')->attempt($credentials, $remember)) {
            return redirect()->intended('dashboard');
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

        return redirect()->route('core.admin.login');
    }
}
