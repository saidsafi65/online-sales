<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;

class CustomerAuthController extends Controller
{
    public function showRegisterForm()
    {
        return view('customer.auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'phone'   => 'required|string|unique:customers,phone',
            'email'   => 'required|email|unique:customers,email',
            'password'=> 'required|string|min:6|confirmed',
            'address' => 'nullable|string|max:255',
            'city'    => 'nullable|string|max:255',
        ]);

        $customer = Customer::create([
            'name'     => $validated['name'],
            'phone'    => $validated['phone'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'address'  => $validated['address'] ?? null,
            'city'     => $validated['city'] ?? null,
        ]);

        Auth::guard('customer')->login($customer);

        return redirect()->route('customer.account')->with('success', 'تم إنشاء الحساب بنجاح');
    }

    public function showLoginForm()
    {
        return view('customer.auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'login'    => 'required|string',
            'password' => 'required|string',
        ]);

        // نحدد إذا يلي دخله المستخدم إيميل أو رقم جوال، ونصادق على العمود الصحيح
        $loginField = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';
        $credentials = [
            $loginField => $request->login,
            'password'  => $request->password,
        ];

        if (Auth::guard('customer')->attempt($credentials, $request->boolean('remember'))) {
            if (! Auth::guard('customer')->user()->is_active) {
                Auth::guard('customer')->logout();
                return back()->withErrors(['login' => 'هذا الحساب معطّل حالياً'])->onlyInput('login');
            }

            $request->session()->regenerate();
            return redirect()->intended(route('customer.account'));
        }

        return back()->withErrors(['login' => 'البريد الإلكتروني أو رقم الهاتف أو كلمة المرور غير صحيحة'])->onlyInput('login');
    }

    public function logout(Request $request)
    {
        Auth::guard('customer')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('products.index');
    }

    /**
     * ===== استعادة كلمة المرور =====
     */

    public function showForgotPasswordForm()
    {
        return view('customer.auth.forgot-password');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::broker('customers')->sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with('success', 'تم إرسال رابط استعادة كلمة المرور إلى بريدك الإلكتروني')
            : back()->withErrors(['email' => 'لا يوجد حساب مرتبط بهذا البريد الإلكتروني']);
    }

    public function showResetPasswordForm(Request $request, string $token)
    {
        return view('customer.auth.reset-password', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token'    => 'required',
            'email'    => 'required|email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $status = Password::broker('customers')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($customer, $password) {
                $customer->forceFill([
                    'password' => Hash::make($password),
                ])->save();

                event(new PasswordReset($customer));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('customer.login')->with('success', 'تم تغيير كلمة المرور بنجاح، سجل دخولك الآن')
            : back()->withErrors(['email' => 'حدث خطأ، الرابط منتهي الصلاحية أو غير صحيح']);
    }
}