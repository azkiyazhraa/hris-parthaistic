<?php
// app/Http/Controllers/Auth/ForgotPasswordController.php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ForgotPasswordController extends Controller
{
    /**
     * Tampilkan halaman forgot password
     */
    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Proses cek email dan tampilkan captcha
     */
    public function checkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:karyawans,email'
        ], [
            'email.exists' => 'Email tidak terdaftar dalam sistem.'
        ]);

        // Cek apakah email terdaftar
        $karyawan = Karyawan::where('email', $request->email)->first();

        // Cek status karyawan
        if (!$karyawan->isActive()) {
            return back()->withErrors([
                'email' => 'Akun Anda tidak aktif. Silakan hubungi HR.'
            ])->withInput();
        }

        // Generate soal matematika random
        $num1 = rand(1, 20);
        $num2 = rand(1, 20);
        $num3 = rand(1, 10);
        $operator1 = ['+', '-'][rand(0, 1)];
        $operator2 = ['+', '-'][rand(0, 1)];

        // Hitung hasil
        $result = $this->calculateExpression($num1, $num2, $num3, $operator1, $operator2);
        $expression = "$num1 $operator1 $num2 $operator2 $num3";

        // Simpan ke session
        session([
            'captcha_result' => $result,
            'captcha_expression' => $expression,
            'reset_email' => $request->email
        ]);

        return view('auth.captcha-verification', [
            'expression' => $expression,
            'email' => $request->email
        ]);
    }

    /**
     * Verifikasi captcha
     */
    public function verifyCaptcha(Request $request)
    {
        $request->validate([
            'captcha_answer' => 'required|numeric'
        ]);

        $expectedResult = session('captcha_result');
        $email = session('reset_email');

        if (!$expectedResult || !$email) {
            return redirect()->route('password.request')
                ->withErrors(['error' => 'Sesi expired, silakan ulangi lagi.']);
        }

        if ((int)$request->captcha_answer !== (int)$expectedResult) {
            return back()->withErrors([
                'captcha_answer' => 'Jawaban salah. Silakan coba lagi.'
            ])->withInput();
        }

        // Generate token reset password
        $token = Str::random(60);

        // Simpan token ke database
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email],
            [
                'token' => $token,
                'created_at' => Carbon::now()
            ]
        );

        // Clear session captcha
        session()->forget(['captcha_result', 'captcha_expression', 'reset_email']);

        return redirect()->route('password.reset', ['token' => $token])
            ->with('email', $email);
    }

    /**
     * Tampilkan halaman reset password
     */
    public function showResetForm($token)
    {
        // Cek token valid
        $reset = DB::table('password_reset_tokens')
            ->where('token', $token)
            ->first();

        if (!$reset) {
            return redirect()->route('password.request')
                ->withErrors(['error' => 'Token reset password tidak valid.']);
        }

        // Cek expired (1 jam)
        $createdAt = Carbon::parse($reset->created_at);
        if ($createdAt->diffInMinutes(Carbon::now()) > 60) {
            DB::table('password_reset_tokens')->where('token', $token)->delete();
            return redirect()->route('password.request')
                ->withErrors(['error' => 'Token telah kadaluarsa. Silakan ulangi lagi.']);
        }

        return view('auth.reset-password', [
            'token' => $token,
            'email' => $reset->email
        ]);
    }

    /**
     * Proses reset password
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
            'password_confirmation' => 'required'
        ]);

        // Cek token
        $reset = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (!$reset) {
            return back()->withErrors([
                'email' => 'Token reset password tidak valid.'
            ]);
        }

        // Cek expired
        $createdAt = Carbon::parse($reset->created_at);
        if ($createdAt->diffInMinutes(Carbon::now()) > 60) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return back()->withErrors([
                'email' => 'Token telah kadaluarsa. Silakan ulangi lagi.'
            ]);
        }

        // Update password
        $karyawan = Karyawan::where('email', $request->email)->first();

        if (!$karyawan) {
            return back()->withErrors([
                'email' => 'Email tidak ditemukan.'
            ]);
        }

        $karyawan->kata_sandi = Hash::make($request->password);
        $karyawan->save();

        // Hapus token
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')
            ->with('status', 'Password berhasil direset. Silakan login dengan password baru Anda.');
    }

    /**
     * Helper untuk menghitung ekspresi matematika
     * (Kiri ke kanan untuk kesederhanaan)
     */
    private function calculateExpression($num1, $num2, $num3, $operator1, $operator2)
    {
        $result = $num1;

        if ($operator1 === '+') {
            $result += $num2;
        } else {
            $result -= $num2;
        }

        if ($operator2 === '+') {
            $result += $num3;
        } else {
            $result -= $num3;
        }

        return $result;
    }
}
