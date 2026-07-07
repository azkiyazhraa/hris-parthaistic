{{-- resources/views/auth/reset-password.blade.php --}}
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'HRIS Management') }} - Reset Password</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');

        * {
            font-family: 'Inter', sans-serif;
        }

        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .logo-image {
            width: 100%;
            height: 100%;
            object-fit: contain;
            border-radius: 12px;
        }

        .input-focus:focus {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15);
        }
    </style>
</head>

<body class="min-h-screen overflow-x-hidden bg-slate-100">

    <!-- BACKGROUND -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute rounded-full -top-32 -left-32 w-80 h-80 bg-blue-500/20 blur-3xl animate-pulse">
        </div>
        <div class="absolute bottom-0 right-0 rounded-full w-96 h-96 bg-purple-500/20 blur-3xl animate-pulse">
        </div>
    </div>

    <!-- CONTAINER -->
    <div class="relative z-10 flex items-center justify-center min-h-screen p-4 md:p-6">

        <div class="w-full max-w-6xl bg-white rounded-[32px] shadow-2xl overflow-hidden grid grid-cols-1 lg:grid-cols-2">

            <!-- LEFT SIDE -->
            <div class="relative flex-col justify-between hidden p-12 overflow-hidden text-white lg:flex bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-800">

                <div class="absolute inset-0">
                    <div class="absolute border rounded-full w-72 h-72 border-white/10 -top-24 -left-24"></div>
                    <div class="absolute border rounded-full w-96 h-96 border-white/5 -bottom-44 -right-44"></div>
                </div>

                <div class="relative z-10">
                    <div class="flex items-center justify-center w-20 h-20 p-2 mb-8 overflow-hidden border rounded-3xl bg-white/10 backdrop-blur-md border-white/0">
                        <img src="{{ asset('assets/image/logo-partharis-white.png') }}" alt="Parthaistic Logo" class="logo-image">
                    </div>

                    <h1 class="mb-4 text-5xl font-bold leading-tight">
                        Buat Password Baru
                    </h1>
                    <p class="max-w-md text-lg leading-relaxed text-blue-100">
                        Masukkan password baru untuk akun Anda.
                    </p>
                </div>

                <div class="relative z-10 space-y-4">
                    <div class="flex items-center gap-4 p-4 border bg-white/10 backdrop-blur-md rounded-2xl border-white/10">
                        <div class="flex items-center justify-center w-11 h-11 rounded-xl bg-green-400/20">
                            <i class="text-green-300 fas fa-check-circle"></i>
                        </div>
                        <div>
                            <p class="font-medium">Password Baru</p>
                            <p class="text-sm text-blue-100">Minimal 8 karakter</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4 p-4 border bg-white/10 backdrop-blur-md rounded-2xl border-white/10">
                        <div class="flex items-center justify-center w-11 h-11 rounded-xl bg-green-400/20">
                            <i class="text-green-300 fas fa-check-circle"></i>
                        </div>
                        <div>
                            <p class="font-medium">Konfirmasi Password</p>
                            <p class="text-sm text-blue-100">Pastikan password sama</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT SIDE -->
            <div class="flex items-center p-6 sm:p-10 lg:p-14">

                <div class="w-full max-w-md mx-auto">

                    <!-- MOBILE LOGO -->
                    <div class="mb-8 text-center lg:hidden">
                        <div class="flex items-center justify-center w-24 h-24 p-1 mx-auto mb-5 overflow-hidden text-white bg-blue-600 shadow-lg rounded-3xl">
                            <img src="{{ asset('assets/image/logo-partharis-white.png') }}" alt="Parthaistic Logo" class="logo-image">
                        </div>
                        <h1 class="text-3xl font-bold text-slate-800">Reset Password</h1>
                        <p class="mt-2 text-slate-500">Buat password baru</p>
                    </div>

                    <!-- HEADER -->
                    <div class="mb-8">
                        <h2 class="mb-2 text-3xl font-bold text-slate-800">
                            Password Baru 🔐
                        </h2>
                        <p class="text-slate-500">
                            Buat password baru untuk akun Anda
                        </p>
                        <p class="mt-2 text-sm text-slate-400">
                            <i class="fas fa-envelope"></i>
                            {{ $email ?? 'Email terdaftar' }}
                        </p>
                    </div>

                    <!-- ERROR MESSAGES -->
                    @if ($errors->any())
                        <div class="px-4 py-3 mb-6 text-red-700 border border-red-200 bg-red-50 rounded-2xl">
                            @foreach ($errors->all() as $error)
                                <p class="text-sm">{{ $error }}</p>
                            @endforeach
                        </div>
                    @endif

                    <!-- FORM -->
                    <form action="{{ route('password.update') }}" method="POST" class="space-y-5">
                        @csrf                        @method('POST')

                        <input type="hidden" name="token" value="{{ $token }}">
                        <input type="hidden" name="email" value="{{ $email }}">

                        <!-- NEW PASSWORD -->
                        <div>
                            <label class="block mb-2 text-sm font-medium text-slate-700">
                                Password Baru
                            </label>
                            <div class="relative">
                                <span class="absolute -translate-y-1/2 left-4 top-1/2 text-slate-400">
                                    <i class="fas fa-lock"></i>
                                </span>
                                <input type="password" name="password" id="password" required
                                    placeholder="Minimal 8 karakter"
                                    class="w-full pl-12 pr-12 py-3.5 rounded-2xl border border-slate-200 bg-slate-50
                                    focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none transition input-focus">
                                <button type="button" onclick="togglePassword('password', 'toggleIcon1')"
                                    class="absolute -translate-y-1/2 right-4 top-1/2 text-slate-400 hover:text-slate-600">
                                    <i id="toggleIcon1" class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <!-- CONFIRM PASSWORD -->
                        <div>
                            <label class="block mb-2 text-sm font-medium text-slate-700">
                                Konfirmasi Password
                            </label>
                            <div class="relative">
                                <span class="absolute -translate-y-1/2 left-4 top-1/2 text-slate-400">
                                    <i class="fas fa-lock"></i>
                                </span>
                                <input type="password" name="password_confirmation" id="password_confirmation" required
                                    placeholder="Konfirmasi password"
                                    class="w-full pl-12 pr-12 py-3.5 rounded-2xl border border-slate-200 bg-slate-50
                                    focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none transition input-focus">
                                <button type="button" onclick="togglePassword('password_confirmation', 'toggleIcon2')"
                                    class="absolute -translate-y-1/2 right-4 top-1/2 text-slate-400 hover:text-slate-600">
                                    <i id="toggleIcon2" class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <!-- BUTTONS -->
                        <div class="flex flex-col gap-3">
                            <button type="submit"
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold
                                py-3.5 rounded-2xl transition duration-300 shadow-lg shadow-blue-600/20">

                                <i class="mr-2 fas fa-save"></i>
                                Reset Password
                            </button>

                            <a href="{{ route('login') }}"
                                class="w-full text-center text-slate-600 hover:text-blue-600 font-medium
                                py-3 rounded-2xl transition duration-300 border border-slate-200 hover:border-blue-200">

                                <i class="mr-2 fas fa-arrow-left"></i>
                                Kembali ke Login
                            </a>
                        </div>

                    </form>

                    <!-- FOOTER -->
                    <div class="mt-8 text-center">
                        <div class="mt-6 text-xs text-slate-400">
                            © 2026 PARTHARIS. All rights reserved.
                        </div>
                    </div>

                </div>

            </div>

        </div>

    </div>

    <script>
        function togglePassword(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);

            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        // Auto-focus first input
        document.addEventListener('DOMContentLoaded', function() {
            const firstInput = document.querySelector('input');
            if (firstInput) {
                firstInput.focus();
            }
        });
    </script>

</body>

</html>
