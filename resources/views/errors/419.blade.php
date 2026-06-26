<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>419 - Session Expired | HRIS System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');

        * {
            font-family: 'Inter', sans-serif;
        }

        .logo-image {
            width: 100%;
            height: 100%;
            object-fit: contain;
            border-radius: 12px;
        }

        .error-code {
            font-size: 8rem;
            font-weight: 800;
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1;
        }

        .btn-back {
            transition: all 0.3s ease;
        }

        .btn-back:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(245, 158, 11, 0.3);
        }
    </style>
</head>

<body class="min-h-screen bg-slate-100 flex items-center justify-center p-4">

    <!-- BACKGROUND DECORATION -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-32 -left-32 w-80 h-80 bg-yellow-500/20 rounded-full blur-3xl animate-pulse">
        </div>
        <div class="absolute bottom-0 right-0 w-96 h-96 bg-orange-500/20 rounded-full blur-3xl animate-pulse">
        </div>
    </div>

    <!-- CONTENT -->
    <div class="relative z-10 w-full max-w-2xl bg-white rounded-[32px] shadow-2xl p-8 md:p-12 text-center">

        <!-- LOGO -->
        <div class="flex justify-center mb-6">
            <div class="w-20 h-20 rounded-3xl bg-gradient-to-br from-blue-600 to-indigo-800 flex items-center justify-center overflow-hidden p-2 shadow-lg">
                <img src="{{ asset('assets/image/logo-partharis.png') }}" alt="Parthaistic Logo" class="logo-image">
            </div>
        </div>

        <!-- ERROR CODE -->
        <div class="error-code">419</div>

        <!-- TITLE -->
        <h1 class="text-3xl font-bold text-slate-800 mt-2 mb-3">
            Session Expired
        </h1>

        <!-- DESCRIPTION -->
        <p class="text-slate-500 text-lg max-w-md mx-auto mb-8">
            Your session has expired due to inactivity.
            Please refresh the page and try again.
        </p>

        <!-- SUGGESTIONS -->
        <div class="bg-yellow-50 rounded-2xl p-6 mb-8 text-left border border-yellow-100">
            <p class="text-sm font-medium text-yellow-700 mb-3">
                <i class="fas fa-clock text-yellow-500 mr-2"></i>
                What to do:
            </p>
            <ul class="text-sm text-yellow-600 space-y-2">
                <li class="flex items-center gap-2">
                    <i class="fas fa-arrow-right text-yellow-500 text-xs"></i>
                    Refresh the page to get a new session
                </li>
                <li class="flex items-center gap-2">
                    <i class="fas fa-arrow-right text-yellow-500 text-xs"></i>
                    Login again if the page doesn't reload
                </li>
                <li class="flex items-center gap-2">
                    <i class="fas fa-arrow-right text-yellow-500 text-xs"></i>
                    Clear your browser cache if the issue persists
                </li>
            </ul>
        </div>

        <!-- BUTTONS -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <button onclick="location.reload()"
                class="px-6 py-3 rounded-2xl border border-slate-200 text-slate-700 hover:bg-slate-50 transition flex items-center justify-center gap-2">
                <i class="fas fa-sync-alt"></i>
                Refresh Page
            </button>
            <a href="{{ route('login') }}"
                class="px-6 py-3 rounded-2xl bg-yellow-600 hover:bg-yellow-700 text-white font-semibold transition shadow-lg shadow-yellow-600/20 btn-back flex items-center justify-center gap-2">
                <i class="fas fa-sign-in-alt"></i>
                Back to Login
            </a>
        </div>

        <!-- FOOTER -->
        <div class="mt-8 pt-6 border-t border-slate-100">
            <p class="text-xs text-slate-400">
                © {{ date('Y') }} HRIS Management System
            </p>
        </div>

    </div>

</body>

</html>
