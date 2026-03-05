<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Giriş formunu göster
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Giriş işlemi
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ], [
            'email.required'    => 'E-posta adresi gereklidir.',
            'email.email'       => 'Geçerli bir e-posta adresi girin.',
            'password.required' => 'Şifre gereklidir.',
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            // Tenant aktiflik kontrolü
            $user = Auth::user();
            if ($user->tenant && !$user->tenant->isActive() && !$user->tenant->isOnTrial()) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'email' => 'Hesabınız askıya alınmıştır. Destek ile iletişime geçin.',
                ]);
            }

            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'email' => 'Girdiğiniz bilgiler kayıtlarımızla eşleşmiyor.',
        ])->onlyInput('email', 'remember');
    }

    /**
     * Çıkış işlemi
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Başarıyla çıkış yaptınız.');
    }
}
