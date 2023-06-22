<?php

namespace App\Admin\Controllers;

use App\Mail\ForgotPasswordMail;
use App\Models\AdminRoleUser;
use App\Models\ForgetPassword;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Facades\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\AdminUser;
use Mail;

class RegisterController extends AdminController
{
    public function showSigninForm()
    {
        return view('admin.signin');
    }

    public function showRegistrationForm()
    {
        return view('admin.register');
    }

    public function showForgotForm()
    {
        return view('admin.forgot');
    }

    public function showResetForm()
    {
        return view('admin.reset');
    }

    public function register(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:admin_users|max:255',
            'password' => 'required|string|min:8|confirmed',
        ]);
        $user = AdminUser::create([
            'username' => $request->input('username'),
            'name' => $request->input('name'),
            'email' => $request->input('email'),
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
    public function forgotPassword(Request $request)
    {
        $admin = AdminUser::where('email', $request->email)->first();
        if ($admin) {
            // dd($admin->email);
            ForgetPassword::create([
                'email' => $admin->email,
                'token' => $request->_token
            ]);
            $mailData = [
                'title' => 'Send mail from Htet Tun Lu',
                'body' => 'This is forgot password mail',
                'token' => $request->_token
            ];

            Mail::to($request->email)->send(new ForgotPasswordMail($mailData));
            return redirect()->route('admin.signin')->with('success', 'Email send successfully');
        } else {
            return view('admin.forgot')->with('fail', 'User does not exist with this email');
        }

    }

    public function resetPassword(Request $request)
    {
        $isValid = ForgetPassword::where('token', $request->_token)->first();
        if($isValid) {
            $request->validate([
                'password' => 'required|string|min:8|confirmed',
            ]);
            $admin = AdminUser::where('email', $isValid->email)->first();
            $admin->password = Hash::make($request->password);
            $admin->save();
            return redirect()->route('admin.signin')->with('success', 'Password updated successfully');
        }else {
            return view('admin.reset')->with('fail', 'Invilid token');
        }
    }
}
