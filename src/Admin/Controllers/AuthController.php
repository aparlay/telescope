<?php

namespace Aparlay\Core\Admin\Controllers;

use Aparlay\Core\Admin\Requests\AuthRequest;
use Aparlay\Core\Models\Enums\UserType;
use Illuminate\Http\Request;
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
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postLogin(AuthRequest $request)
    {
        $credentials         = $request->only(['email', 'password']);
        $remember            = $request->get('remember');

        // make sure only admin type user can do login
        $credentials['type'] = UserType::ADMIN->value;

        if (Auth::guard('admin')->attempt($credentials, $remember)) {
            return redirect()->intended('dashboard');
        }

        return back()->withErrors([
            'error' => 'The provided credentials are incorrect.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('core.admin.login');
    }
}
