@php
    $generalNavItems = [
        ['href' => route('dashboard'), 'label' => __('Dashboard'), 'active' => request()->routeIs('dashboard')],
        ['href' => route('tools.index'), 'label' => __('AI Tools'), 'active' => request()->routeIs('tools.*')],
        ['href' => route('categories.index'), 'label' => __('Categories'), 'active' => request()->routeIs('categories.*')],
    ];
    $managementNavItems = [
        ['href' => route('tools.index') . '#add', 'label' => __('Add New Tool'), 'active' => false],
    ];
@endphp
{{-- Backdrop: visible on mobile when sidebar is open --}}
<div
    id="sidebar-backdrop"
    class="fixed inset-0 z-30 bg-slate-900/50 backdrop-blur-sm lg:hidden"
    x-show="open"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    @click="open = false"
    aria-hidden="true"
    style="display: none;"
></div>

<aside
    id="sidebar"
    class="fixed top-0 left-0 z-40 w-64 h-screen pt-16 transition-transform duration-200 ease-out bg-slate-900 text-white border-r border-slate-700 -translate-x-full lg:translate-x-0"
    :class="{ 'translate-x-0': open }"
    aria-label="Sidebar"
>
    <div class="h-full px-3 pb-4 overflow-y-auto">
        <ul class="space-y-1 pt-4">
            @foreach($generalNavItems as $item)
                <li>
                    <a
                        href="{{ $item['href'] }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors duration-150 {{ $item['active'] ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}"
                    >
                        @if($item['label'] === __('Dashboard'))
                            {{-- Heroicon: squares-2x2 / dashboard --}}
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                        @elseif($item['label'] === __('Categories'))
                            {{-- Heroicon: folder --}}
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                        @else
                            {{-- Heroicon: cpu-chip / tool --}}
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        @endif
                        {{ $item['label'] }}
                    </a>
                </li>
            @endforeach
        </ul>

        @auth
            <div class="pt-4 mt-4 border-t border-slate-700">
                <p class="px-3 mb-2 text-xs font-semibold uppercase tracking-wider text-slate-500">
                    {{ __('MANAGEMENT') }}
                </p>
                <ul class="space-y-1">
                    @foreach($managementNavItems as $item)
                        <li>
                            @if($item['label'] === __('Add New Tool'))
                                @if(request()->routeIs('tools.index'))
                                    <button type="button"
                                        @click="open = true; editToolId = null"
                                        class="flex w-full items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors duration-150 text-slate-300 hover:bg-slate-800 hover:text-white">
                                        {{-- Heroicon: plus --}}
                                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                        {{ $item['label'] }}
                                    </button>
                                @else
                                    <a href="{{ route('tools.index') }}#add"
                                        class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors duration-150 text-slate-300 hover:bg-slate-800 hover:text-white">
                                        {{-- Heroicon: plus --}}
                                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                        {{ $item['label'] }}
                                    </a>
                                @endif
                            @else
                                <a
                                    href="{{ $item['href'] }}"
                                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors duration-150 {{ $item['active'] ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}"
                                >
                                    {{-- Heroicon: tag / categories --}}
                                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                    {{ $item['label'] }}
                                </a>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>

            @if(auth()->user()?->canAccessAdminArea())
                <div class="pt-4 mt-4 border-t border-slate-700">
                    <p class="px-3 mb-2 text-xs font-semibold uppercase tracking-wider text-slate-500">
                        {{ __('ADMIN') }}
                    </p>
                    <ul class="space-y-1">
                        <li>
                            <a href="{{ route('admin.dashboard') }}"
                                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors duration-150 {{ request()->routeIs('admin.*') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                                {{ __('Admin Dashboard') }}
                            </a>
                        </li>
                    </ul>
                </div>
            @endif
        @endauth
    </div>
</aside>
