<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 rounded-lg bg-emerald-50 p-4 text-sm text-emerald-800">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="mb-4 rounded-lg bg-amber-50 p-4 text-sm text-amber-800">
                    {{ session('error') }}
                </div>
            @endif

            {{-- User Management --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">{{ __('User Management') }}</h3>
                            <p class="text-sm text-gray-600 mt-1">{{ __('Only Owner can manage users. Admin role can manage tools but not users.') }}</p>
                        </div>
                        @if ((auth()->user()->role ?? '') === 'owner')
                            <a href="{{ route('admin.users.create') }}" class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                {{ __('Add New User') }}
                            </a>
                        @endif
                    </div>
                    @if ($users->isEmpty())
                        <p class="text-gray-500 text-sm">{{ __('No users.') }}</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-700">{{ __('Name') }}</th>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-700">{{ __('Email') }}</th>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-700">{{ __('Role') }}</th>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-700">{{ __('Admin') }}</th>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-700">{{ __('Status') }}</th>
                                        <th scope="col" class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-700">{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($users as $u)
                                        @php $isCurrentUser = $u->id === auth()->id(); @endphp
                                        <tr>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">{{ $u->name }}</td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">{{ $u->email }}</td>
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                @php
                                                    $roleClass = \App\Models\User::ROLE_BADGE_CLASSES[$u->role ?? 'frontend'] ?? 'bg-gray-100 text-gray-800';
                                                @endphp
                                                <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium {{ $roleClass }}">
                                                    {{ __(ucfirst(str_replace('_', ' ', $u->role ?? 'frontend'))) }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                @if ($u->is_admin)
                                                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium bg-amber-100 text-amber-800">{{ __('Yes') }}</span>
                                                @else
                                                    <span class="text-sm text-gray-500">—</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                @if ($u->is_active ?? true)
                                                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium bg-emerald-100 text-emerald-800">{{ __('Active') }}</span>
                                                @else
                                                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium bg-slate-100 text-slate-700">{{ __('Inactive') }}</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-right text-sm">
                                                @if ($isCurrentUser)
                                                    <span class="text-gray-400 text-xs">{{ __('You') }}</span>
                                                @elseif ((auth()->user()->role ?? '') === 'owner')
                                                    <a href="{{ route('admin.users.edit', $u) }}" class="inline-flex rounded-lg bg-slate-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2">
                                                        {{ __('Edit') }}
                                                    </a>
                                                    <form method="POST" action="{{ route('admin.users.toggle-active', $u) }}" class="inline ml-2">
                                                        @csrf
                                                        <button type="submit" class="rounded-lg bg-amber-500 px-3 py-1.5 text-xs font-medium text-white hover:bg-amber-600 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2">
                                                            {{ ($u->is_active ?? true) ? __('Deactivate') : __('Activate') }}
                                                        </button>
                                                    </form>
                                                    <form method="POST" action="{{ route('admin.users.destroy', $u) }}" class="inline ml-2" onsubmit="return confirm('{{ __('Are you sure you want to delete this user? This action cannot be undone.') }}');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="rounded-lg bg-red-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                                                            {{ __('Delete') }}
                                                        </button>
                                                    </form>
                                                @else
                                                    <span class="text-gray-400 text-xs">—</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">
                            {{ $users->links() }}
                        </div>
                    @endif
                </div>
            </div>

            {{-- Audit Log (Activity) --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ __('Activity') }}</h3>
                    <p class="text-sm text-gray-600 mb-4">{{ __('User submissions (audit log).') }}</p>
                    @if ($submissions->isEmpty())
                        <p class="text-gray-500 text-sm">{{ __('No submissions yet.') }}</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-4 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-700">{{ __('Activity') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($submissions as $sub)
                                        <tr>
                                            <td class="px-4 py-2 text-sm text-gray-900">
                                                {{ __('User :name submitted :tool at :time.', [
                                                    'name' => $sub->user?->name ?? __('Unknown'),
                                                    'tool' => $sub->tool?->name ?? __('[deleted]'),
                                                    'time' => $sub->created_at->translatedFormat('M j, Y H:i'),
                                                ]) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">{{ __('Одобрение на инструменти') }}</h3>
                    <p class="text-sm text-gray-600 mb-4">{{ __('Single list for approving or rejecting tools. Both Owner and Admin use this page.') }}</p>
                    <a href="{{ route('admin.tools.pending') }}" class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        {{ __('Одобрение на инструменти') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
