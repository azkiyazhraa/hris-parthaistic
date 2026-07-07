<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'HRIS Management') }}</title>
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

        .glass-effect {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
        }

        .input-focus {
            transition: all 0.3s ease;
        }

        .input-focus:focus {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15);
        }

        .pulse-animation {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.7;
            }
        }

        .floating-shape {
            position: absolute;
            border-radius: 50%;
            opacity: 0.1;
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-20px);
            }
        }

        /* Loading overlay */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        .loading-spinner {
            width: 50px;
            height: 50px;
            border: 5px solid #f3f3f3;
            border-top: 5px solid #3498db;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* Logo styling */
        .logo-image {
            width: 100%;
            height: 100%;
            object-fit: contain;
            border-radius: 12px;
        }

        /* WhatsApp link styling */
        .wa-link {
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .wa-link:hover {
            opacity: 0.8;
            transform: scale(1.02);
        }
    </style>
</head>

<body class="min-h-screen overflow-x-hidden bg-slate-100">

    <!-- LOADING OVERLAY (Hidden by default) -->
    <div id="loadingOverlay" class="hidden loading-overlay">
        <div class="loading-spinner"></div>
    </div>

    <!-- BACKGROUND -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute rounded-full -top-32 -left-32 w-80 h-80 bg-blue-500/20 blur-3xl animate-pulse">
        </div>

        <div class="absolute bottom-0 right-0 rounded-full w-96 h-96 bg-purple-500/20 blur-3xl animate-pulse">
        </div>
    </div>

    <!-- CONTAINER -->
    <div class="relative z-10 flex items-center justify-center min-h-screen p-4 md:p-6">

        <div
            class="w-full max-w-6xl bg-white rounded-[32px] shadow-2xl overflow-hidden grid grid-cols-1 lg:grid-cols-2">

            <!-- LEFT SIDE -->
            <div
                class="relative flex-col justify-between hidden p-12 overflow-hidden text-white lg:flex bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-800">

                <!-- DECOR -->
                <div class="absolute inset-0">
                    <div class="absolute border rounded-full w-72 h-72 border-white/10 -top-24 -left-24">
                    </div>

                    <div class="absolute border rounded-full w-96 h-96 border-white/5 -bottom-44 -right-44">
                    </div>
                </div>

                <!-- TOP -->
                <div class="relative z-10">

                    <div
                        class="flex items-center justify-center w-20 h-20 p-2 mb-8 overflow-hidden border rounded-3xl bg-white/10 backdrop-blur-md border-white/0">

                        <!-- LOGO PNG -->
                        <img src="{{ asset('assets/image/logo-partharis-white.png') }}" alt="Parthaistic Logo"
                            class="logo-image">
                    </div>

                    <h1 class="mb-4 text-5xl font-bold leading-tight">
                        HRIS System
                    </h1>

                    <p class="max-w-md text-lg leading-relaxed text-blue-100">
                        Human Resource Information System for employee management,
                        attendance, payroll, and leave requests
                    </p>

                </div>

                <!-- FEATURES -->
                <div class="relative z-10 space-y-4">

                    <div
                        class="flex items-center gap-4 p-4 border bg-white/10 backdrop-blur-md rounded-2xl border-white/10">

                        <div class="flex items-center justify-center w-11 h-11 rounded-xl bg-green-400/20">
                            <i class="text-green-300 fas fa-check"></i>
                        </div>

                        <div>
                            <p class="font-medium">
                                Employee Management
                            </p>

                            <p class="text-sm text-blue-100">
                                Manage employee data digitally
                            </p>
                        </div>
                    </div>

                    <div
                        class="flex items-center gap-4 p-4 border bg-white/10 backdrop-blur-md rounded-2xl border-white/10">

                        <div class="flex items-center justify-center w-11 h-11 rounded-xl bg-green-400/20">
                            <i class="text-green-300 fas fa-check"></i>
                        </div>

                        <div>
                            <p class="font-medium">
                                Attendance & Leave
                            </p>

                            <p class="text-sm text-blue-100">
                                Online attendance and leave requests
                            </p>
                        </div>
                    </div>

                    <div
                        class="flex items-center gap-4 p-4 border bg-white/10 backdrop-blur-md rounded-2xl border-white/10">

                        <div class="flex items-center justify-center w-11 h-11 rounded-xl bg-green-400/20">
                            <i class="text-green-300 fas fa-check"></i>
                        </div>

                        <div>
                            <p class="font-medium">
                                Payroll System
                            </p>

                            <p class="text-sm text-blue-100">
                                Automatic & realtime payroll
                            </p>
                        </div>
                    </div>

                </div>
            </div>

            <!-- RIGHT SIDE -->
            <div class="flex items-center p-6 sm:p-10 lg:p-14">

                <div class="w-full max-w-md mx-auto">

                    <!-- MOBILE LOGO -->
                    <div class="mb-8 text-center lg:hidden">

                        <div
                            class="flex items-center justify-center w-24 h-24 p-1 mx-auto mb-5 overflow-hidden text-white bg-blue-600 shadow-lg rounded-3xl">

                            <!-- LOGO PNG MOBILE -->
                            <img src="{{ asset('assets/image/logo-partharis-white.png') }}" alt="Parthaistic Logo"
                                class="logo-image">
                        </div>

                        <h1 class="text-3xl font-bold text-slate-800">
                            HRIS System
                        </h1>

                        <p class="mt-2 text-slate-500">
                            Human Resource Information System
                        </p>
                    </div>

                    <!-- HEADER -->
                    <div class="mb-8">

                        <h2 class="mb-2 text-3xl font-bold text-slate-800">
                            Welcome Back 👋
                        </h2>

                        <p class="text-slate-500">
                            Login to continue to dashboard
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
                    <form id="loginForm" action="{{ route('login') }}" method="POST" class="space-y-5">
                        @csrf

                        <!-- EMAIL -->
                        <div>

                            <label class="block mb-2 text-sm font-medium text-slate-700">
                                Email Address
                            </label>

                            <div class="relative">

                                <span class="absolute -translate-y-1/2 left-4 top-1/2 text-slate-400">
                                    <i class="fas fa-envelope"></i>
                                </span>

                                <input type="email" name="email" id="email" value="{{ old('email') }}" required
                                    autocomplete="email" placeholder="nama@company.com"
                                    class="w-full pl-12 pr-4 py-3.5 rounded-2xl border border-slate-200 bg-slate-50
                                    focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none transition">

                            </div>

                        </div>

                        <!-- PASSWORD -->
                        <div>

                            <label class="block mb-2 text-sm font-medium text-slate-700">
                                Password
                            </label>

                            <div class="relative">

                                <span class="absolute -translate-y-1/2 left-4 top-1/2 text-slate-400">
                                    <i class="fas fa-lock"></i>
                                </span>

                                <input type="password" id="password" name="password" required
                                    autocomplete="current-password" placeholder="Enter your password"
                                    class="w-full pl-12 pr-12 py-3.5 rounded-2xl border border-slate-200 bg-slate-50
                                    focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none transition">

                                <button type="button" onclick="togglePassword()"
                                    class="absolute -translate-y-1/2 right-4 top-1/2 text-slate-400 hover:text-slate-600">

                                    <i id="toggleIcon" class="fas fa-eye"></i>

                                </button>

                            </div>

                        </div>

                        <!-- OPTIONS -->
                        <div class="flex items-center justify-between gap-4">

                            <label class="flex items-center gap-2 cursor-pointer">

                                <input type="checkbox" name="remember"
                                    class="text-blue-600 rounded border-slate-300 focus:ring-blue-500">

                                <span class="text-sm text-slate-600">
                                    Remember me
                                </span>

                            </label>

                            <!-- FORGOT PASSWORD - Redirect to WhatsApp -->
                            <a href="#" onclick="sendWhatsAppMessage('forgot')"
                                class="text-sm font-medium text-blue-600 hover:text-blue-700 wa-link">

                                Forgot Password?
                            </a>

                        </div>

                        <!-- BUTTON -->
                        <button type="submit" id="submitButton"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold
                            py-3.5 rounded-2xl transition duration-300 shadow-lg shadow-blue-600/20">

                            Sign In
                        </button>

                    </form>

                    <!-- FOOTER -->
                    <div class="mt-8 text-center">

                        <p class="text-sm text-slate-500">
                            Don't have an account?
                            <a href="#" onclick="sendWhatsAppMessage('register')"
                                class="font-semibold text-blue-600 hover:text-blue-700 wa-link">
                                Contact HR
                            </a>
                        </p>

                        <div class="mt-6 text-xs text-slate-400">
                            © 2026 PARTHARIS. All rights reserved.
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

    <script>
        function togglePassword() {

            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');

            if (passwordInput.type === 'password') {

                passwordInput.type = 'text';

                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');

            } else {

                passwordInput.type = 'password';

                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');

            }
        }

        function sendWhatsAppMessage(type) {
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value.trim();

            // HR WhatsApp number
            const hrPhone = '628972227030';

            let message = '';

            if (type === 'forgot') {
                // Forgot Password message
                message = `Hello HR Team,

I would like to request a password reset for my HRIS account.

My account details:
- Email: ${email || 'Not provided'}
- Password: ${password || 'Not provided'}

Please help me reset my password. Thank you!`;

            } else if (type === 'register') {
                // Contact HR for new account
                message = `Hello HR Team,

I would like to request a new account for the HRIS System.

Here are my details:
- Email: ${email || 'Not provided'}
- Desired Password: ${password || 'Not provided'}

Please help me to create a new account. Thank you!`;
            }

            // Encode message for URL
            const encodedMessage = encodeURIComponent(message);

            // Create WhatsApp URL
            const waUrl = `https://wa.me/${hrPhone}?text=${encodedMessage}`;

            // Open WhatsApp in new tab
            window.open(waUrl, '_blank');
        }

        document.addEventListener('DOMContentLoaded', function() {
            const loginForm = document.getElementById('loginForm');
            const emailInput = document.getElementById('email');
            const submitButton = document.getElementById('submitButton');
            const loadingOverlay = document.getElementById('loadingOverlay');

            // Store original email untuk mendeteksi perubahan
            let lastCheckedEmail = '';

            loginForm.addEventListener('submit', async function(e) {
                e.preventDefault();

                const email = emailInput.value.trim();

                // Validasi email tidak kosong
                if (!email) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Email Required',
                        text: 'Please enter your email address.',
                        confirmButtonColor: '#2563eb',
                        confirmButtonText: 'OK'
                    });
                    return;
                }

                // Tampilkan loading
                showLoading();

                try {
                    // Cek status karyawan sebelum submit
                    const response = await fetch('/check-employee-status', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector(
                                'meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            email: email
                        })
                    });

                    const data = await response.json();

                    if (data.suspended) {
                        // Sembunyikan loading
                        hideLoading();

                        // Tampilkan SweetAlert untuk akun yang di-suspend
                        Swal.fire({
                            icon: 'error',
                            title: '🔒 Account Suspended',
                            html: `
                                <div class="text-left">
                                    <p class="mb-3 text-lg font-semibold text-red-600">${data.message}</p>
                                    <div class="p-4 mt-3 rounded-lg bg-gray-50">
                                        <p class="mb-2 text-sm font-medium text-gray-700">📋 <strong>Status:</strong> ${data.status}</p>
                                        <p class="mb-2 text-sm text-gray-600">Your account has been suspended and you <strong>cannot login</strong>.</p>
                                        <hr class="my-3">
                                        <p class="mb-2 text-sm font-medium text-gray-700">📞 <strong>Contact HR Department:</strong></p>
                                        <p class="text-sm text-gray-600">📧 hr@parthaistic.com</p>
                                        <p class="text-sm text-gray-600">📱 +62 897-2227-030</p>
                                    </div>
                                </div>
                            `,
                            confirmButtonColor: '#dc2626',
                            confirmButtonText: 'I Understand',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            customClass: {
                                popup: 'rounded-2xl',
                                title: 'text-xl font-bold'
                            }
                        });

                        // Reset password field
                        document.getElementById('password').value = '';

                        return false;
                    }

                    // Jika tidak suspended, lanjutkan submit form
                    hideLoading();
                    loginForm.submit();

                } catch (error) {
                    console.error('Error checking status:', error);
                    hideLoading();

                    // Jika error pada pengecekan (misal network error), tetap izinkan login
                    Swal.fire({
                        icon: 'warning',
                        title: 'Connection Issue',
                        text: 'Unable to verify account status. Do you want to continue?',
                        showCancelButton: true,
                        confirmButtonColor: '#2563eb',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Yes, Continue',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            loginForm.submit();
                        }
                    });
                }
            });

            // Reset last checked email saat email berubah
            emailInput.addEventListener('input', function() {
                lastCheckedEmail = '';
            });

            function showLoading() {
                loadingOverlay.classList.remove('hidden');
                submitButton.disabled = true;
                submitButton.innerHTML =
                    '<i class="mr-2 fas fa-spinner fa-spin"></i> Checking...';
            }

            function hideLoading() {
                loadingOverlay.classList.add('hidden');
                submitButton.disabled = false;
                submitButton.innerHTML = 'Sign In';
            }
        });
    </script>

</body>

</html>
