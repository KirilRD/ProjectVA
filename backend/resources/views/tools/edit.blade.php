<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Tool') }}: {{ $tool->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('tools.update', $tool) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    @include('tools._form', ['tool' => $tool])
                    <div class="mt-6 flex items-center gap-2 flex-wrap">
                        <button type="submit" class="rounded-md bg-indigo-600 px-4 py-2 text-sm text-white hover:bg-indigo-700">{{ __('Update Tool') }}</button>
                        <a href="{{ route('tools.show', $tool) }}" class="inline-flex items-center px-4 py-2 rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200 transition text-sm font-medium">
                            {{ __('Отказ') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
