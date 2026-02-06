<header class="fixed top-0 right-0 left-0 lg:left-64 z-50 bg-white border-b border-gray-200 shadow-sm transition-[left] duration-200">
    @auth
        {{-- Greeting + live clock bar (right-aligned, light gray, small) --}}
        <div class="flex h-8 items-center justify-end gap-4 px-4 sm:px-6 border-b border-gray-100 bg-gray-50/80">
            <p class="text-sm text-gray-800 truncate max-w-[min(100%,40rem)] text-right font-semibold">
                Добре дошъл, {{ auth()->user()->name }}! Ти си с роля: {{ auth()->user()->role_name }}.
            </p>
            <time id="header-clock" class="text-xs text-gray-500 tabular-nums shrink-0" datetime="{{ now()->toIso8601String() }}">
                {{ now()->format('d.m.Y H:i') }}
            </time>
        </div>
    @endauth
    <div class="flex h-16 items-center justify-between gap-4 px-4 sm:px-6">
        {{-- Left: mobile hamburger + search --}}
        <div class="flex items-center gap-3 lg:gap-4 flex-1 min-w-0">
            @auth
                <button
                    type="button"
                    @click="open = !open"
                    class="inline-flex items-center justify-center p-2 rounded-lg text-gray-500 hover:bg-gray-100 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-600 lg:hidden shrink-0"
                    :aria-expanded="open"
                    aria-label="{{ __('Toggle menu') }}"
                    aria-controls="sidebar"
                >
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="!open"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="open" x-cloak style="display: none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            @endauth
            <form method="GET" action="{{ route('tools.index') }}" class="flex-1 min-w-0 max-w-md">
                <label for="header-search" class="sr-only">{{ __('Search') }}</label>
                <div class="relative">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input
                        type="search"
                        id="header-search"
                        name="search"
                        value="{{ request('search') }}"
                        class="block w-full rounded-lg border border-gray-300 bg-gray-50 py-2 pl-10 pr-3 text-sm text-gray-900 placeholder-gray-500 focus:border-indigo-600 focus:ring-indigo-600 focus:outline-none"
                        placeholder="{{ __('Search…') }}"
                    />
                </div>
            </form>
        </div>

        <div class="shrink-0">
            @auth
                {{-- Right: user profile dropdown --}}
                <x-dropdown align="right" width="48" contentClasses="py-1 bg-white rounded-lg shadow-lg ring-1 ring-black ring-opacity-5">
                    <x-slot name="trigger">
                        <button type="button" class="flex items-center gap-2 rounded-lg px-2 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2">
                            <span class="sr-only">{{ __('Open user menu') }}</span>
                            @php
                                $roleLabel = Auth::user()->role_name;
                                $roleBadgeClass = Auth::user()->role ? (\App\Models\User::ROLE_BADGE_CLASSES[Auth::user()->role] ?? 'bg-gray-100 text-gray-800') : 'bg-gray-100 text-gray-800';
                            @endphp
                            <span class="hidden sm:inline flex items-center gap-1.5">
                                <span>{{ $roleLabel }}: {{ Auth::user()->name }}</span>
                                <span class="inline-flex items-center rounded px-1.5 py-0.5 text-xs font-medium {{ $roleBadgeClass }}">{{ $roleLabel }}</span>
                            </span>
                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-indigo-600 text-white text-sm font-semibold">
                                {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                            </div>
                            <svg class="h-4 w-4 text-gray-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            @else
                {{-- Guest: Login/Register buttons --}}
                <div class="flex items-center gap-2">
                    <a href="{{ route('login') }}" class="inline-flex items-center rounded-lg px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2">
                        {{ __('Login') }}
                    </a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="inline-flex items-center rounded-lg bg-indigo-600 px-3 py-2 text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2">
                            {{ __('Register') }}
                        </a>
                    @endif
                </div>
            @endauth
        </div>
    </div>
</header>

@auth
    {{-- Live clock: update every second (format d.m.Y H:i) --}}
    <script>
        (function () {
            var el = document.getElementById('header-clock');
            if (!el) return;
            function pad(n) { return (n < 10 ? '0' : '') + n; }
            function format() {
                var d = new Date();
                el.textContent = pad(d.getDate()) + '.' + pad(d.getMonth() + 1) + '.' + d.getFullYear() + ' ' + pad(d.getHours()) + ':' + pad(d.getMinutes()) + ':' + pad(d.getSeconds());
            }
            setInterval(format, 1000);
            format();
        })();
    </script>
@endauth
