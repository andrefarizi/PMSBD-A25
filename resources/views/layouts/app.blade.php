<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'TokoKita - MSBD Lab')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">

    <style>
        * {
            font-family: 'Poppins', sans-serif !important;
        }
        body {
            font-family: 'Poppins', sans-serif !important;
            background-color: #e6eef8;
            color: #334155;
        }
        .neu-card {
            background: #e6eef8;
            box-shadow: 10px 10px 24px #c5d2e8, -10px -10px 24px #ffffff;
            border-radius: 1.75rem;
        }
        .neu-inset {
            background: #e6eef8;
            box-shadow: inset 3px 3px 6px #c5d2e8, inset -3px -3px 6px #ffffff;
            border-radius: 0.85rem;
        }
        .neu-input-inset {
            background: #e6eef8;
            box-shadow: inset 3px 3px 6px #c5d2e8, inset -3px -3px 6px #ffffff;
            transition: all 0.2s ease-in-out;
        }
        .neu-input-inset:focus {
            box-shadow: inset 4px 4px 8px #b8c7df, inset -4px -4px 8px #ffffff;
        }
        .neu-pill {
            background: #e6eef8;
            box-shadow: 3px 3px 6px #c5d2e8, -3px -3px 6px #ffffff;
        }
        .neu-btn-primary {
            background: linear-gradient(135deg, #4f96f6, #3b82f6);
            box-shadow: 5px 5px 12px #b8c8e2, -5px -5px 12px #ffffff;
            transition: all 0.2s ease;
        }
        .neu-btn-primary:hover {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            box-shadow: 2px 2px 6px #b8c8e2, -2px -2px 6px #ffffff;
            transform: translateY(1px);
        }
        .neu-btn-danger {
            background: #e6eef8;
            box-shadow: 4px 4px 10px #c5d2e8, -4px -4px 10px #ffffff;
            transition: all 0.2s ease;
        }
        .neu-btn-danger:hover {
            background: #fef2f2;
            box-shadow: 2px 2px 5px #c5d2e8, -2px -2px 5px #ffffff;
        }
        .neu-btn-danger-solid {
            background: linear-gradient(135deg, #f43f5e, #e11d48);
            box-shadow: 5px 5px 12px #b8c8e2, -5px -5px 12px #ffffff;
            transition: all 0.2s ease;
            color: #ffffff;
        }
        .neu-btn-danger-solid:hover {
            background: linear-gradient(135deg, #e11d48, #be123c);
            box-shadow: 2px 2px 6px #b8c8e2, -2px -2px 6px #ffffff;
            transform: translateY(1px);
        }
    </style>
</head>
<body class="min-h-screen pb-16 antialiased">

    @if(!View::hasSection('hide-nav'))
    <!-- Top Navigation Bar -->
    <nav class="py-4 px-4 sm:px-8 max-w-7xl mx-auto">
        <div class="neu-card px-6 py-3.5 flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center space-x-3.5">
                <div class="w-10 h-10 neu-pill rounded-xl flex items-center justify-center text-blue-500 font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <div>
                    <span class="text-base font-extrabold text-slate-800 tracking-tight block">TokoKita</span>
                    <span class="text-[11px] font-medium text-slate-500 block -mt-1">Sistem Operasional Basis Data</span>
                </div>
            </a>

            <div class="flex items-center space-x-3">

                @auth
                    <div class="flex items-center space-x-2">
                        <span class="neu-pill px-3 py-1.5 rounded-xl text-xs font-bold text-slate-700">{{ Auth::user()->name }}</span>
                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" title="Logout" class="neu-btn-danger-solid px-4 py-2 rounded-xl text-xs font-bold text-white flex items-center gap-1.5 cursor-pointer">
                                <span>Logout</span>
                            </button>
                        </form>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="neu-btn-primary px-4 py-2 rounded-xl text-xs font-bold text-white flex items-center gap-1.5 cursor-pointer">
                        <span>Login</span>
                    </a>
                @endauth
            </div>
        </div>
    </nav>
    @endif

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-8">

        <!-- Session Flash Messages -->
        @if(session('success'))
            <div class="neu-card p-4 text-emerald-800 text-xs flex items-center space-x-3">
                <div class="flex-1 font-semibold">{{ session('success') }}</div>
            </div>
        @endif

        @if(session('error'))
            <div class="neu-card p-4 text-rose-800 text-xs flex items-center space-x-3">
                <div class="flex-1 font-semibold">{{ session('error') }}</div>
            </div>
        @endif

        @if(session('info'))
            <div class="neu-card p-4 text-blue-800 text-xs flex items-center space-x-3">
                <div class="flex-1 font-semibold">{{ session('info') }}</div>
            </div>
        @endif

        @yield('content')
    </div>

</body>
</html>
