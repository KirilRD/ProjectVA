<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Add Tool') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white p-8 rounded-2xl shadow-lg">
                <p class="mb-6 text-sm text-gray-500">{{ __('Fill in the details below. Markdown is supported in Usage Instructions.') }}</p>
                @include('tools.partials.add-tool-form', ['inModal' => false, 'categories' => $categories, 'tags' => $tags, 'roles' => $roles])
            </div>
        </div>
    </div>
</x-app-layout>
