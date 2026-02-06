@props([
    'message' => session('success'),
])

@if ($message)
    <div
        x-data="{ show: true }"
        x-init="setTimeout(() => show = false, 3000)"
        x-show="show"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-x-8"
        x-transition:enter-end="opacity-100 translate-x-0"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-x-0"
        x-transition:leave-end="opacity-0 translate-x-4"
        x-cloak
        class="fixed bottom-6 right-6 z-[100]"
        role="status"
        aria-live="polite"
    >
        <div class="flex items-start gap-3 rounded-2xl bg-emerald-600 shadow-lg ring-1 ring-emerald-700/50 px-4 py-3 w-[22rem] max-w-[calc(100vw-2rem)]">
            <div class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-white/20 text-white">
                <svg viewBox="0 0 20 20" fill="currentColor" class="h-5 w-5" aria-hidden="true">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.02 4.153-2.01-1.507a.75.75 0 10-.9 1.2l2.625 1.969a.75.75 0 001.057-.159l3.462-4.774z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="min-w-0 flex-1 py-0.5">
                <p class="text-sm font-semibold text-white">{{ __('Success') }}</p>
                <p class="mt-0.5 text-sm text-emerald-50/95 break-words">{{ $message }}</p>
            </div>
            <button
                type="button"
                class="shrink-0 rounded-lg p-1.5 text-white/80 hover:text-white hover:bg-white/10 transition-colors"
                @click="show = false"
                aria-label="{{ __('Dismiss') }}"
            >
                <svg viewBox="0 0 20 20" fill="currentColor" class="h-5 w-5" aria-hidden="true">
                    <path d="M6.28 5.22a.75.75 0 011.06 0L10 7.94l2.66-2.72a.75.75 0 111.08 1.04L11.06 9l2.68 2.74a.75.75 0 11-1.08 1.04L10 10.06l-2.66 2.72a.75.75 0 11-1.08-1.04L8.94 9 6.28 6.26a.75.75 0 010-1.04z" />
                </svg>
            </button>
        </div>
    </div>
@endif

