<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Tools') }}
            </h2>
            @auth
                <button type="button" @click="open = true; editToolId = null" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2">
                    {{ __('Add Tool') }}
                </button>
            @endauth
        </div>
    </x-slot>

    <div class="space-y-5" x-init="if (window.location.hash === '#add' || {{ json_encode($errors->any() && old('name') !== null) }}) { open = true }; if (window.location.hash === '#add') history.replaceState(null, '', location.pathname + location.search)" @close-add-tool-modal.window="open = false">
        {{-- Top filters (Search + Category) --}}
        <div class="bg-white rounded-xl shadow-sm ring-1 ring-slate-200">
            <form method="GET" action="{{ route('tools.index') }}" class="p-4 sm:p-5">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-end">
                    <div class="flex-1">
                        <label for="search" class="block text-sm font-medium text-slate-700 mb-1">{{ __('Search') }}</label>
                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                                <svg viewBox="0 0 20 20" fill="currentColor" class="h-5 w-5" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 104.33 8.9l2.64 2.64a.75.75 0 101.06-1.06l-2.64-2.64A5.5 5.5 0 009 3.5zm-4 5.5a4 4 0 118 0 4 4 0 01-8 0z" clip-rule="evenodd" />
                                </svg>
                            </span>
                            <input
                                type="text"
                                name="search"
                                id="search"
                                value="{{ request('search') }}"
                                placeholder="{{ __('Search tools...') }}"
                                class="block w-full rounded-lg border-slate-300 pl-10 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                        </div>
                    </div>

                    <div class="sm:w-64">
                        <label for="category_id" class="block text-sm font-medium text-slate-700 mb-1">{{ __('Category') }}</label>
                        <select
                            name="category_id"
                            id="category_id"
                            class="block w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                            <option value="">{{ __('All categories') }}</option>
                            @foreach ($categories as $c)
                                <option value="{{ $c->id }}" {{ request('category_id') == (string) $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="sm:w-48">
                        <label for="type" class="block text-sm font-medium text-slate-700 mb-1">{{ __('Type') }}</label>
                        <select
                            name="type"
                            id="type"
                            class="block w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                            <option value="">{{ __('All types') }}</option>
                            @foreach (\App\Models\Tool::RESOURCE_TYPES as $t)
                                <option value="{{ $t }}" {{ request('type') === $t ? 'selected' : '' }}>{{ __(\App\Models\Tool::TYPE_LABELS[$t] ?? $t) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex gap-2 sm:justify-end">
                        <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                            {{ __('Filter') }}
                        </button>
                        <a href="{{ route('tools.index') }}" class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                            {{ __('Reset') }}
                        </a>
                    </div>
                </div>
            </form>
        </div>

        {{-- Recommended tools for your role (when logged in) --}}
        @auth
            @if(isset($userRecommendedRole) && $userRecommendedRole && $recommendedForRoleTools->isNotEmpty())
                <div class="bg-indigo-50 rounded-xl shadow-sm ring-1 ring-indigo-100 p-4 sm:p-5">
                    <h3 class="text-sm font-semibold text-indigo-900 mb-3">{{ __('Recommended tools for your role') }}</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach($recommendedForRoleTools as $rt)
                            <a href="{{ route('tools.show', $rt) }}" class="inline-flex items-center rounded-lg bg-white px-3 py-2 text-sm font-medium text-indigo-700 ring-1 ring-indigo-200 hover:bg-indigo-100 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                {{ $rt->name }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        @endauth

        {{-- Grid of tool cards --}}
        @if ($tools->isEmpty())
            <div class="bg-white rounded-xl shadow-sm ring-1 ring-slate-200 p-12 text-center text-slate-600">
                {{ __('No tools found. Try searching or filtering by category.') }}
            </div>
        @else
            @php
                $categoryBadgeClasses = [
                    'bg-sky-50 text-sky-700 ring-sky-200',
                    'bg-emerald-50 text-emerald-700 ring-emerald-200',
                    'bg-amber-50 text-amber-800 ring-amber-200',
                    'bg-violet-50 text-violet-700 ring-violet-200',
                    'bg-rose-50 text-rose-700 ring-rose-200',
                ];

                $roleBadgeClasses = [
                    'bg-indigo-50 text-indigo-700 ring-indigo-200',
                    'bg-teal-50 text-teal-700 ring-teal-200',
                    'bg-fuchsia-50 text-fuchsia-700 ring-fuchsia-200',
                    'bg-lime-50 text-lime-800 ring-lime-200',
                    'bg-orange-50 text-orange-700 ring-orange-200',
                ];

                $recommendedRoleBadgeClasses = [
                    'Backend' => 'bg-blue-100 text-blue-800 ring-blue-200',
                    'Frontend' => 'bg-sky-100 text-sky-800 ring-sky-200',
                    'QA' => 'bg-emerald-100 text-emerald-800 ring-emerald-200',
                    'Design' => 'bg-pink-100 text-pink-800 ring-pink-200',
                    'PM' => 'bg-amber-100 text-amber-800 ring-amber-200',
                ];
            @endphp

            <div class="space-y-4" x-data="{ activeFilter: 'All' }">
                {{-- Filter by Role bar --}}
                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-sm font-medium text-slate-700 mr-1">{{ __('Filter by Role') }}:</span>
                    <nav class="flex flex-wrap items-center gap-2" aria-label="{{ __('Filter by role') }}">
                        <button type="button"
                            @click="activeFilter = 'All'"
                            :class="activeFilter === 'All' ? 'bg-indigo-600 text-white' : 'bg-white text-slate-700 hover:bg-slate-50 border border-slate-300'"
                            class="rounded-lg px-4 py-2 text-sm font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            All
                        </button>
                        <button type="button"
                            @click="activeFilter = 'Backend'"
                            :class="activeFilter === 'Backend' ? 'bg-indigo-600 text-white' : 'bg-white text-slate-700 hover:bg-slate-50 border border-slate-300'"
                            class="rounded-lg px-4 py-2 text-sm font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            Backend
                        </button>
                        <button type="button"
                            @click="activeFilter = 'Frontend'"
                            :class="activeFilter === 'Frontend' ? 'bg-indigo-600 text-white' : 'bg-white text-slate-700 hover:bg-slate-50 border border-slate-300'"
                            class="rounded-lg px-4 py-2 text-sm font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            Frontend
                        </button>
                        <button type="button"
                            @click="activeFilter = 'QA'"
                            :class="activeFilter === 'QA' ? 'bg-indigo-600 text-white' : 'bg-white text-slate-700 hover:bg-slate-50 border border-slate-300'"
                            class="rounded-lg px-4 py-2 text-sm font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            QA
                        </button>
                        <button type="button"
                            @click="activeFilter = 'Design'"
                            :class="activeFilter === 'Design' ? 'bg-indigo-600 text-white' : 'bg-white text-slate-700 hover:bg-slate-50 border border-slate-300'"
                            class="rounded-lg px-4 py-2 text-sm font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            Design
                        </button>
                        <button type="button"
                            @click="activeFilter = 'PM'"
                            :class="activeFilter === 'PM' ? 'bg-indigo-600 text-white' : 'bg-white text-slate-700 hover:bg-slate-50 border border-slate-300'"
                            class="rounded-lg px-4 py-2 text-sm font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            PM
                        </button>
                    </nav>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($tools as $tool)
                    <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow ring-1 ring-slate-200 overflow-hidden flex flex-col relative"
                        x-show="activeFilter === 'All' || activeFilter === '{{ $tool->recommended_role ?? '' }}'"
                        x-transition:enter="ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="ease-in duration-150"
                        x-transition:leave-start="opacity-100 scale-100"
                        x-transition:leave-end="opacity-0 scale-95">
                        {{-- Edit / Delete / Approve: driven by Policy (@can) and helpers --}}
                        @if(auth()->user() && (auth()->user()->isAdmin() || auth()->user()->isOwner()))
                            <div class="absolute top-3 right-3 z-10 flex items-center gap-1">
                                @can('update', $tool)
                                <button type="button"
                                    @click="open = true; editToolId = {{ $tool->id }}"
                                    title="{{ __('Edit') }}"
                                    class="rounded-lg p-1.5 text-gray-400 transition-colors hover:bg-slate-100 hover:text-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                    </svg>
                                </button>
                                @endcan
                                @can('delete', $tool)
                                <form method="POST" action="{{ route('tools.destroy', $tool) }}" class="inline" onsubmit="return confirm('{{ __('Are you sure you want to delete this tool?') }}');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        title="{{ __('Delete') }}"
                                        class="rounded-lg p-1.5 text-gray-400 transition-colors hover:bg-slate-100 hover:text-red-600 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-1">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                                @endcan
                                @can('approve', $tool)
                                @if (($tool->status ?? 'pending') !== 'approved')
                                    <form method="POST" action="{{ route('admin.tools.toggle-status', $tool) }}" class="inline" onsubmit="return confirm('{{ __('Approve this tool?') }}');">
                                        @csrf
                                        <input type="hidden" name="status" value="approved">
                                        <button type="submit" title="{{ __('Approve') }}" class="rounded-lg p-1.5 text-gray-400 transition-colors hover:bg-slate-100 hover:text-emerald-600 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-1">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        </button>
                                    </form>
                                @endif
                                @endcan
                            </div>
                        @endif
                        <div class="p-4 flex gap-4">
                            {{-- Logo / Image placeholder --}}
                            <div class="h-14 w-14 shrink-0 rounded-xl overflow-hidden ring-1 ring-black/5">
                                @if ($tool->getFirstMediaUrl('image', 'thumb'))
                                    <img
                                        src="{{ $tool->getFirstMediaUrl('image', 'thumb') }}"
                                        alt=""
                                        class="h-full w-full object-cover"
                                    >
                                @else
                                    <div class="h-full w-full bg-gradient-to-br from-indigo-500 via-sky-500 to-emerald-400 flex items-center justify-center">
                                        <svg viewBox="0 0 20 20" fill="currentColor" class="h-7 w-7 text-white/90" aria-hidden="true">
                                            <path d="M11.983 1.5a1.5 1.5 0 00-1.966 0l-7.5 6.5A1.5 1.5 0 002 9.134V16.5A2.5 2.5 0 004.5 19h11a2.5 2.5 0 002.5-2.5V9.134a1.5 1.5 0 00-.517-1.134l-7.5-6.5z" />
                                        </svg>
                                    </div>
                                @endif
                            </div>

                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h3 class="font-bold text-slate-900 truncate">
                                        {{ $tool->name }}
                                    </h3>
                                    @if ($tool->comments_avg_rating !== null)
                                        <span class="inline-flex items-center gap-1 text-amber-600 text-sm font-medium shrink-0" title="{{ __('Average rating') }}">
                                            <svg class="h-4 w-4 text-amber-500" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                            {{ number_format((float) $tool->comments_avg_rating, 1) }}
                                        </span>
                                    @else
                                        <span class="text-sm text-slate-500 shrink-0">Няма оценки</span>
                                    @endif
                                </div>
                                <p class="mt-1 text-sm text-slate-600 line-clamp-2">
                                    {{ $tool->description ?? '' }}
                                </p>

                                {{-- Type badge + Recommended role badge --}}
                                <div class="mt-2 flex flex-wrap items-center gap-2">
                                    @php
                                        $toolType = $tool->type ?? 'tool';
                                        $typeBadgeClass = \App\Models\Tool::TYPE_BADGE_CLASSES[$toolType] ?? 'bg-slate-100 text-slate-800';
                                    @endphp
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $typeBadgeClass }}">
                                        {{ __(\App\Models\Tool::TYPE_LABELS[$toolType] ?? $toolType) }}
                                    </span>
                                    @if ($tool->recommended_role)
                                        @php
                                            $badgeClass = $recommendedRoleBadgeClasses[$tool->recommended_role] ?? 'bg-slate-50 text-slate-700 ring-slate-200';
                                        @endphp
                                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold ring-1 ring-inset {{ $badgeClass }}">
                                            {{ __('Best for') }}: {{ $tool->recommended_role }}
                                        </span>
                                    @endif
                                </div>
                                {{-- Badges: Category + Roles --}}
                                <div class="mt-3 flex flex-wrap gap-2">
                                    @if ($tool->category)
                                        @php
                                            $catIndex = (int) $tool->category_id % count($categoryBadgeClasses);
                                        @endphp
                                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold ring-1 ring-inset {{ $categoryBadgeClasses[$catIndex] ?? 'bg-slate-50 text-slate-700 ring-slate-200' }}">
                                            {{ $tool->category->name }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold ring-1 ring-inset bg-slate-50 text-slate-700 ring-slate-200">
                                            {{ __('Uncategorized') }}
                                        </span>
                                    @endif

                                    @foreach (optional($tool->roles)->take(3) ?? [] as $role)
                                        @php $roleIndex = (int) $role->id % count($roleBadgeClasses); @endphp
                                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold ring-1 ring-inset {{ $roleBadgeClasses[$roleIndex] ?? 'bg-slate-50 text-slate-700 ring-slate-200' }}">
                                            {{ ucfirst($role->name) }}
                                        </span>
                                    @endforeach

                                    @if (optional($tool->roles)->count() > 3)
                                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold ring-1 ring-inset bg-slate-50 text-slate-700 ring-slate-200">
                                            +{{ optional($tool->roles)->count() - 3 }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="mt-auto px-4 pb-4">
                            <p class="mb-2 text-xs text-slate-500">
                                {{ __('Shared by') }}: {{ optional($tool->user)->name ?? 'System' }}
                            </p>
                            <a
                                href="{{ route('tools.show', $tool) }}"
                                class="inline-flex w-full items-center justify-center rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800"
                            >
                                {{ __('View Details') }}
                            </a>
                        </div>
                    </div>
                @endforeach
                </div>

                <div class="mt-6">
                    {{ $tools->links() }}
                </div>
            </div>
        @endif
    </div>

    {{-- Add / Edit Tool Modal --}}
    @auth
            <div x-show="open" x-cloak class="fixed inset-0 z-50 overflow-y-auto px-4 sm:px-6" aria-modal="true" role="dialog"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0">
                <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="open = false; editToolId = null" x-show="open"></div>
                <div class="flex min-h-full items-center justify-center py-8">
                    <div x-show="open"
                        @click.outside="open = false; editToolId = null"
                        @keydown.escape.window="open = false; editToolId = null"
                        class="relative w-full max-w-2xl transform overflow-hidden rounded-2xl bg-white shadow-xl transition-all"
                        x-transition:enter="ease-out duration-300"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="ease-in duration-200"
                        x-transition:leave-start="opacity-100 scale-100"
                        x-transition:leave-end="opacity-0 scale-95">
                        <div class="border-b border-slate-200 bg-white px-6 py-4">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-semibold text-slate-900" x-text="editToolId ? '{{ __('Edit Tool') }}' : '{{ __('Add New Tool') }}'"></h3>
                                <button type="button" @click="open = false; editToolId = null" class="rounded-lg p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-500" aria-label="{{ __('Close') }}">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                            <p class="mt-1 text-sm text-slate-500" x-text="editToolId ? '{{ __('Update the details below.') }}' : '{{ __('Fill in the details below.') }}'"></p>
                        </div>
                        <div class="max-h-[calc(90vh-8rem)] overflow-y-auto px-6 py-5">
                            {{-- Add form --}}
                            <div x-show="!editToolId">
                                @include('tools.partials.add-tool-form', ['inModal' => true, 'categories' => $categories, 'tags' => $tags, 'roles' => $roles])
                            </div>
                            {{-- Edit forms (one per tool on current page) --}}
                            @foreach($tools as $t)
                                <div x-show="editToolId === {{ $t->id }}" style="display: none;">
                                    <form method="POST" action="{{ route('tools.update', $t) }}" enctype="multipart/form-data" class="space-y-4">
                                        @csrf
                                        @method('PUT')
                                        @include('tools._form', ['tool' => $t, 'categories' => $categories, 'tags' => $tags, 'roles' => $roles])
                                        <div class="flex items-center gap-3 pt-4 border-t border-slate-200">
                                            <button type="submit" class="rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2">
                                                {{ __('Update Tool') }}
                                            </button>
                                            <button type="button" @click="open = false; editToolId = null" class="rounded-lg border border-slate-300 bg-white px-5 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2">
                                                {{ __('Cancel') }}
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
    @endauth
</x-app-layout>
