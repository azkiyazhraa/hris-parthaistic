<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    /**
     * Menampilkan halaman login
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle login request
     * Melakukan pengecekan status suspend sebelum autentikasi
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Cari karyawan berdasarkan email
        $karyawan = Karyawan::where('email', $credentials['email'])->first();

        // Jika karyawan tidak ditemukan
        if (!$karyawan) {
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ])->onlyInput('email');
        }

        // Cek apakah karyawan di-suspend
        if ($karyawan->isSuspended()) {
            $suspendMessages = [
                'Resigned' => 'Your account has been suspended because you have resigned from the company.',
                'Contract Ended' => 'Your account has been suspended because your employment contract has ended.',
                'Internship Completed' => 'Your account has been suspended because your internship program has been completed.',
                'Terminated' => 'Your account has been suspended due to employment termination.',
            ];

            $message = $suspendMessages[$karyawan->status] ?? 'Your account has been suspended. Please contact HR for more information.';

            return back()->withErrors([
                'email' => $message,
            ])->onlyInput('email');
        }

        // Cek password
        if (Hash::check($credentials['password'], $karyawan->kata_sandi)) {
            Auth::login($karyawan, $request->boolean('remember'));
            $request->session()->regenerate();

            if ($karyawan->isAdmin() || $karyawan->isHR()) {
                return redirect()->intended(route('admin.dashboard'));
            }

            return redirect()->intended(route('karyawan.dashboard'));
        }

        // Password salah
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Cek status karyawan (untuk AJAX request dari halaman login)
     * Digunakan untuk menampilkan pop-up sebelum form disubmit
     */
    public function checkEmployeeStatus(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $karyawan = Karyawan::where('email', $request->email)->first();

        // Jika karyawan tidak ditemukan
        if (!$karyawan) {
            return response()->json([
                'suspended' => false,
                'found' => false,
                'message' => 'No account found with this email.'
            ]);
        }

        // Jika karyawan di-suspend
        if ($karyawan->isSuspended()) {
            $suspendMessages = [
                'Resigned' => 'Your account has been suspended because you have resigned from the company.',
                'Contract Ended' => 'Your account has been suspended because your employment contract has ended.',
                'Internship Completed' => 'Your account has been suspended because your internship program has been completed.',
                'Terminated' => 'Your account has been suspended due to employment termination.',
            ];

            return response()->json([
                'suspended' => true,
                'found' => true,
                'status' => $karyawan->status,
                'status_badge' => $karyawan->status_badge,
                'message' => $suspendMessages[$karyawan->status] ?? 'Your account has been suspended. Please contact HR for more information.',
            ]);
        }

        // Karyawan aktif
        return response()->json([
            'suspended' => false,
            'found' => true,
            'message' => 'Account is active.'
        ]);
    }

    /**
     * Handle logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
