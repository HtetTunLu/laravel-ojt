<?php

namespace App\Admin\Controllers;

use App\Models\AdminRoleUser;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Facades\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\AdminUser;
use Illuminate\Support\Facades\Route;

class RegisterController extends AdminController
{
    public function showRegistrationForm()
    {
        return view('admin.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            // 'email' => 'required|string|email|unique:admin_users|max:255',
            'password' => 'required|string|min:8|confirmed',
        ]);
        $user = AdminUser::create([
            'username' => $request->input('username'),
            'name' => $request->input('name'),
            // 'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
        ]);

        AdminRoleUser::create([
            'role_id' => 1,
            'user_id' => $user->id
        ]);

        // $this->loginValidator($request->all())->validate();

        $credentials = $request->only(['username', 'password']);
        $remember = $request->get('remember', false);

        if (Admin::guard()->attempt($credentials, $remember)) {
            admin_toastr(__('registered_successful'));

            $request->session()->regenerate();
            return redirect('/admin');
        }

        return back()->withInput()->withErrors([
            $this->username() => $this->getFailedLoginMessage(),
        ]);

    }
}
