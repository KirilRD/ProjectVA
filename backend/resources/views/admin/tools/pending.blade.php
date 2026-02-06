<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center gap-3 justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Одобрение на инструменти') }}
            </h2>
            <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-1 px-4 py-2 rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200 transition text-sm font-medium">
                ← {{ __('Към Таблото') }}
            </a>
        </div>
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

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <p class="text-sm text-gray-600 mb-4">{{ __('Tools waiting for approval. Both Owner and Admin can approve.') }}</p>

                    @if ($pendingTools->isNotEmpty())
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-700">{{ __('Name') }}</th>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-700">{{ __('Type') }}</th>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-700">{{ __('Submitter') }}</th>
                                        <th scope="col" class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-700">{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($pendingTools as $tool)
                                        <tr>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                                                <a href="{{ route('tools.show', $tool) }}" class="text-indigo-600 hover:text-indigo-900">{{ $tool->name }}</a>
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">
                                                {{ __(\App\Models\Tool::TYPE_LABELS[$tool->type ?? 'tool'] ?? $tool->type ?? '—') }}
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">{{ optional($tool->user)->name ?? '—' }}</td>
                                            <td class="px-4 py-3 whitespace-nowrap text-right text-sm">
                                                <form method="POST" action="{{ route('admin.tools.toggle-status', $tool) }}" class="inline">
                                                    @csrf
                                                    <input type="hidden" name="status" value="approved">
                                                    <button type="submit" class="rounded bg-emerald-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-emerald-700">{{ __('Approve') }}</button>
                                                </form>
                                                <form method="POST" action="{{ route('admin.tools.toggle-status', $tool) }}" class="inline ml-2">
                                                    @csrf
                                                    <input type="hidden" name="status" value="rejected">
                                                    <button type="submit" class="rounded bg-red-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-red-700">{{ __('Reject') }}</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-500 text-sm">{{ __('No pending tools.') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
