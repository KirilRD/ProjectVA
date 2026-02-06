<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Categories') }}
        </h2>
    </x-slot>

    <div class="space-y-8">
        {{-- Resource Types filter bar --}}
        @php
            $resourceTypes = [
                null => __('All'),
                'tool' => __('Tools'),
                'ai_library' => __('AI Libraries'),
                'application' => __('Applications'),
            ];
        @endphp
        <div class="rounded-xl bg-white shadow-sm ring-1 ring-slate-200 overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-100">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Filter by resource type') }}</p>
            </div>
            <div class="p-4 flex flex-wrap gap-2">
                @foreach($resourceTypes as $type => $label)
                    <a
                        href="{{ route('categories.index', $type ? ['type' => $type] : []) }}"
                        class="inline-flex items-center rounded-lg px-4 py-2.5 text-sm font-medium transition-colors duration-150 {{ ($currentType ?? null) === $type ? 'bg-indigo-600 text-white shadow-sm' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}"
                    >
                        {{ $label }}
                    </a>
                @endforeach
            </div>
        </div>

        {{-- Categories grid --}}
        @if($categories->isEmpty())
            <div class="rounded-xl bg-white shadow-sm ring-1 ring-slate-200 p-12 text-center">
                <p class="text-slate-600">{{ __('No categories yet.') }}</p>
                @if(isset($currentType) && $currentType)
                    <a href="{{ route('categories.index') }}" class="mt-4 inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                        {{ __('Show all categories') }}
                    </a>
                @endif
            </div>
        @else
            @php
                $categoryIcons = [
                    'ai-assistants' => 'cpu',
                    'development' => 'code',
                    'design' => 'paint-brush',
                    'productivity' => 'document',
                    'writing-content' => 'pencil',
                ];
            @endphp
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($categories as $category)
                    @php
                        $icon = $categoryIcons[$category->slug ?? ''] ?? 'tag';
                        $count = $category->tools_count ?? 0;
                        $viewAllParams = ['category_id' => $category->id];
                        if (isset($currentType) && $currentType) {
                            $viewAllParams['type'] = $currentType;
                        }
                    @endphp
                    <div class="group rounded-xl bg-white shadow-sm ring-1 ring-slate-200 overflow-hidden hover:shadow-md hover:ring-slate-300 transition-all duration-200 {{ $count === 0 ? 'opacity-75' : '' }}">
                        <div class="p-6 flex flex-col h-full">
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-slate-100 flex items-center justify-center text-slate-600 group-hover:bg-indigo-50 group-hover:text-indigo-600 transition-colors">
                                    @if($icon === 'cpu')
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                    @elseif($icon === 'code')
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.25 6.75L22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3l-4.5 16.5"/></svg>
                                    @elseif($icon === 'paint-brush')
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.53 16.122a3 3 0 00-5.78 1.128 3 3 0 01-2.9 1.833H2.25a.75.75 0 00-.75.75v-.378a3 3 0 012.25-2.703 3 3 0 002.25-2.703V9.75a.75.75 0 00-.75-.75H2.25a.75.75 0 00-.75.75v.378a3 3 0 01-2.25 2.703 3 3 0 00-2.25 2.703v.378a.75.75 0 00.75.75h.878a3 3 0 002.9 1.833 3 3 0 005.78-1.128M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.5 10.5c0 1.575-.75 2.985-1.91 3.923L17.25 21 21 17.25l-6.587-6.587A4.5 4.5 0 1019.5 10.5z"/></svg>
                                    @elseif($icon === 'document')
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                                    @elseif($icon === 'pencil')
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125"/></svg>
                                    @else
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                    @endif
                                </div>
                                <div class="min-w-0 flex-1">
                                    <h3 class="text-lg font-semibold text-slate-900 truncate">{{ $category->name }}</h3>
                                    <p class="mt-1 text-sm text-slate-500">
                                        {{ $count === 0 ? __('No tools') : ($count === 1 ? __('1 tool') : __(':count tools', ['count' => $count])) }}
                                    </p>
                                </div>
                            </div>
                            <div class="mt-5 flex-shrink-0">
                                <a
                                    href="{{ route('tools.index', $viewAllParams) }}"
                                    class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors"
                                >
                                    {{ __('View All') }}
                                    <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-app-layout>
