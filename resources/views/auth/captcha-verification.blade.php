<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'HRIS Management') }} - Verifikasi Captcha</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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

        .captcha-box {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border: 2px dashed #94a3b8;
        }

        .captcha-box .expression {
            font-size: 2.5rem;
            font-weight: 700;
            color: #1e293b;
            letter-spacing: 4px;
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
                        Verifikasi Captcha
                    </h1>
                    <p class="max-w-md text-lg leading-relaxed text-blue-100">
                        Selesaikan perhitungan matematika untuk verifikasi keamanan.
                    </p>
                </div>

                <div class="relative z-10 space-y-4">
                    <div class="flex items-center gap-4 p-4 border bg-white/10 backdrop-blur-md rounded-2xl border-white/10">
                        <div class="flex items-center justify-center w-11 h-11 rounded-xl bg-green-400/20">
                            <i class="text-green-300 fas fa-check-circle"></i>
                        </div>
                        <div>
                            <p class="font-medium">Email Terverifikasi</p>
                            <p class="text-sm text-blue-100">{{ $email ?? 'Email terdaftar' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4 p-4 border bg-white/10 backdrop-blur-md rounded-2xl border-white/10">
                        <div class="flex items-center justify-center w-11 h-11 rounded-xl bg-yellow-400/20">
                            <i class="text-yellow-300 fas fa-shield-alt"></i>
                        </div>
                        <div>
                            <p class="font-medium">Keamanan</p>
                            <p class="text-sm text-blue-100">Verifikasi manusia dengan captcha</p>
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
                        <h1 class="text-3xl font-bold text-slate-800">Verifikasi Captcha</h1>
                        <p class="mt-2 text-slate-500">Selesaikan perhitungan di bawah</p>
                    </div>

                    <!-- HEADER -->
                    <div class="mb-8">
                        <h2 class="mb-2 text-3xl font-bold text-slate-800">
                            Verifikasi Keamanan 🛡️
                        </h2>
                        <p class="text-slate-500">
                            Silakan selesaikan perhitungan di bawah ini
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

                    <!-- CAPTCHA BOX -->
                    <div class="p-6 mb-6 text-center rounded-2xl captcha-box">
                        <div class="flex items-center justify-center gap-2 mb-2">
                            <i class="text-2xl text-blue-500 fas fa-calculator"></i>
                            <span class="text-sm font-medium text-slate-500">Selesaikan perhitungan:</span>
                        </div>
                        <div class="expression">
                            {{ $expression ?? '2 + 6 - 7' }}
                        </div>
                        <div class="mt-2 text-sm text-slate-400">
                            <i class="fas fa-info-circle"></i>
                            Kerjakan dari kiri ke kanan
                        </div>
                    </div>

                    <!-- FORM -->
                    <form action="{{ route('password.verify-captcha') }}" method="POST" class="space-y-5">
                        @csrf

                        <!-- CAPTCHA ANSWER -->
                        <div>
                            <label class="block mb-2 text-sm font-medium text-slate-700">
                                Hasil Perhitungan
                            </label>
                            <div class="relative">
                                <span class="absolute -translate-y-1/2 left-4 top-1/2 text-slate-400">
                                    <i class="fas fa-equals"></i>
                                </span>
                                <input type="number" name="captcha_answer" id="captcha_answer" required
                                    placeholder="Masukkan hasil perhitungan"
                                    class="w-full pl-12 pr-4 py-3.5 rounded-2xl border border-slate-200 bg-slate-50
                                    focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none transition input-focus">
                            </div>
                            <p class="mt-2 text-xs text-slate-400">
                                <i class="fas fa-lightbulb"></i>
                                Contoh: jika 2 + 6 - 7 = 1, maka jawabannya 1
                            </p>
                        </div>

                        <!-- HIDDEN EMAIL -->
                        <input type="hidden" name="email" value="{{ $email ?? '' }}">

                        <!-- BUTTONS -->
                        <div class="flex flex-col gap-3">
                            <button type="submit"
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold
                                py-3.5 rounded-2xl transition duration-300 shadow-lg shadow-blue-600/20">

                                <i class="mr-2 fas fa-check-circle"></i>
                                Verifikasi
                            </button>

                            <a href="{{ route('password.request') }}"
                                class="w-full text-center text-slate-600 hover:text-blue-600 font-medium
                                py-3 rounded-2xl transition duration-300 border border-slate-200 hover:border-blue-200">

                                <i class="mr-2 fas fa-arrow-left"></i>
                                Kembali
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

</body>

</html>
