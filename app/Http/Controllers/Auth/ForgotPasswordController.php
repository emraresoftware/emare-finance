<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;

class ForgotPasswordController extends Controller
{
    /**
     * Şifremi unuttum formunu göster
     */
    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Şifre sıfırlama bağlantısı gönder
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ], [
            'email.required' => 'E-posta adresi gereklidir.',
            'email.email'    => 'Geçerli bir e-posta adresi girin.',
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', 'Şifre sıfırlama bağlantısı e-posta adresinize gönderildi.')
            : back()->withErrors(['email' => 'Bu e-posta adresine ait bir hesap bulunamadı.']);
    }

    /**
     * Şifre sıfırlama formunu göster
     */
    public function showResetForm(Request $request, string $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    /**
     * Şifre sıfırlama işlemi
     */
    public function reset(Request $request)
    {
        $request->validate([
            'token'    => ['required'],
            'email'    => ['required', 'email'],
            'password' => ['required', 'confirmed', 'min:8'],
        ], [
            'email.required'    => 'E-posta adresi gereklidir.',
            'password.required' => 'Şifre gereklidir.',
            'password.confirmed' => 'Şifreler eşleşmiyor.',
            'password.min'       => 'Şifre en az 8 karakter olmalıdır.',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, string $password) {
                $user->forceFill([
                    'password'       => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('success', 'Şifreniz başarıyla sıfırlandı. Giriş yapabilirsiniz.')
            : back()->withErrors(['email' => 'Şifre sıfırlama bağlantısı geçersiz veya süresi dolmuş.']);
    }
}
