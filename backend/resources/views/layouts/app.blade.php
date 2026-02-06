<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3/dist/cdn.min.js"></script>
    </head>
    <body class="font-sans antialiased" @if(auth()->check()) data-auth="1" @endif x-data="{ open: false, editToolId: null, showBackModal: false }" x-init="window.addEventListener('back-button-prompt', function() { showBackModal = true })">
        <div class="min-h-screen bg-slate-50">
            <x-toast-success />
            @auth
                @include('layouts.sidebar')
                @include('layouts.header')
            @endauth

            <!-- Main content: offset by fixed header and sidebar on desktop -->
            <main class="@auth pt-24 lg:pl-64 @else pt-6 @endauth min-h-screen bg-slate-50">
                <!-- Page Heading -->
                @isset($header)
                    <header class="bg-white shadow-sm border-b border-gray-200">
                        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endisset

                <!-- Page Content -->
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $slot }}
                </div>
            </main>
        </div>

        {{-- Back-button confirmation modal (authenticated users only) --}}
        @auth
        <div x-show="showBackModal" x-cloak
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-[100] flex items-center justify-center p-4"
            style="display: none;">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @@click="showBackModal = false; history.pushState(null, null, null)"></div>
            <div class="relative bg-white rounded-2xl shadow-xl max-w-md w-full p-6 ring-1 ring-slate-200"
                @@click.stop>
                <p class="text-lg font-semibold text-slate-800 text-center mb-6">
                    Искате ли да излезете от профила си?
                </p>
                <div class="flex gap-3 justify-center">
                    <button type="button" @@click="showBackModal = false; history.pushState(null, null, null)"
                        class="rounded-xl bg-slate-100 px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-400 focus:ring-offset-2">
                        НЕ
                    </button>
                    <form id="back-confirm-logout-form" action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            ДА
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <style>[x-cloak] { display: none !important; }</style>

        {{-- Back button: push state on load; popstate shows logout modal and pushes again so user stays until they click YES. --}}
        <script>
        (function() {
            if (!document.body.hasAttribute('data-auth')) return;

            history.pushState(null, null, null);

            window.addEventListener('popstate', function() {
                window.dispatchEvent(new Event('back-button-prompt'));
                history.pushState(null, null, null);
            });
        })();
        </script>

        {{-- Inactivity timeout: 5 minutes, then redirect to logout-inactive (authenticated users only) --}}
        <script>
            (function() {
                var INACTIVITY_MS = 5 * 60 * 1000;
                var logoutUrl = {{ json_encode(route('logout.inactive')) }};
                var timer = null;
                function resetTimer() {
                    if (timer) clearTimeout(timer);
                    timer = setTimeout(function() {
                        window.location.href = logoutUrl;
                    }, INACTIVITY_MS);
                }
                ['mousemove', 'mousedown', 'keydown', 'scroll', 'touchstart'].forEach(function(ev) {
                    document.addEventListener(ev, resetTimer);
                });
                resetTimer();
            })();
        </script>
        @endauth
    </body>
</html>
