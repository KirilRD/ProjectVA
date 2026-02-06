<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $tool->name }}
            </h2>
            @if (auth()->user() && (auth()->user()->isAdmin() || auth()->user()->isOwner()))
                <div class="flex gap-2 flex-wrap">
                    @can('update', $tool)
                    <a href="{{ route('tools.edit', $tool) }}" class="rounded-md bg-indigo-600 px-4 py-2 text-sm text-white hover:bg-indigo-700">{{ __('Edit') }}</a>
                    @endcan
                    @can('delete', $tool)
                    <form method="POST" action="{{ route('tools.destroy', $tool) }}" class="inline" onsubmit="return confirm('{{ __('Are you sure you want to delete this tool?') }}');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="rounded-md bg-red-600 px-4 py-2 text-sm text-white hover:bg-red-700">{{ __('Delete') }}</button>
                    </form>
                    @endcan
                    @can('approve', $tool)
                    @if (($tool->status ?? 'pending') !== 'approved')
                        <form method="POST" action="{{ route('admin.tools.toggle-status', $tool) }}" class="inline">
                            @csrf
                            <input type="hidden" name="status" value="approved">
                            <button type="submit" class="rounded-md bg-emerald-600 px-4 py-2 text-sm text-white hover:bg-emerald-700">{{ __('Approve') }}</button>
                        </form>
                    @endif
                    @endcan
                </div>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 space-y-4">
                <p class="pb-2">
                    <a href="{{ route('tools.index') }}" class="inline-flex items-center gap-1 px-4 py-2 rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200 transition text-sm font-medium">
                        ← {{ __('Обратно към каталога') }}
                    </a>
                </p>
                <p><strong>{{ __('Link') }}:</strong> <a href="{{ $tool->link }}" target="_blank" rel="noopener" class="text-indigo-600">{{ $tool->link }}</a></p>
                @if ($tool->official_docs_link)
                    <p><strong>{{ __('Official docs') }}:</strong> <a href="{{ $tool->official_docs_link }}" target="_blank" rel="noopener" class="text-indigo-600">{{ $tool->official_docs_link }}</a></p>
                @endif
                <p><strong>{{ __('Category') }}:</strong> {{ $tool->category?->name ?? '—' }}</p>
                <p><strong>{{ __('Description') }}:</strong><br>{{ $tool->description }}</p>
                <p><strong>{{ __('How to use') }}:</strong><br>{{ $tool->how_to_use }}</p>
                @if ($tool->usage_instructions)
                    <div><strong>{{ __('Usage instructions') }}:</strong><br><div class="prose mt-1">{!! \Illuminate\Support\Str::markdown($tool->usage_instructions) !!}</div></div>
                @endif
                @if ($tool->examples_link)
                    <p><strong>{{ __('Examples link') }}:</strong> <a href="{{ $tool->examples_link }}" target="_blank" rel="noopener" class="text-indigo-600">{{ $tool->examples_link }}</a></p>
                @endif
                @php
                    $roles = $tool->relationLoaded('roles') ? $tool->getRelation('roles') : collect();
                    $tags = $tool->relationLoaded('tags') ? $tool->getRelation('tags') : collect();
                    $rolesList = collect($roles)->pluck('name')->filter()->join(', ');
                    $tagsList = collect($tags)->pluck('name')->filter()->join(', ');
                @endphp
                <p><strong>{{ __('Roles') }}:</strong> {{ $rolesList ?: '—' }}</p>
                <p><strong>{{ __('Tags') }}:</strong> {{ $tagsList ?: '—' }}</p>
                @if ($tool->getMedia('image')->isNotEmpty())
                    <div><strong>{{ __('Screenshots') }}:</strong>
                        <div class="mt-2 flex flex-wrap gap-2">
                            @foreach ($tool->getMedia('image') as $media)
                                <img src="{{ $media->getUrl() }}" alt="" class="max-h-40 rounded border">
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Reviews & Ratings --}}
                <div class="pt-6 mt-6 border-t border-slate-200">
                    <h3 class="text-lg font-semibold text-slate-900 mb-4">{{ __('Reviews & Ratings') }}</h3>

                    {{-- Average rating summary --}}
                    @php
                        $avgRating = $tool->comments->avg('rating');
                        $reviewCount = $tool->comments->count();
                    @endphp
                    @if ($reviewCount > 0)
                        <div class="flex items-center gap-2 mb-4 text-slate-700">
                            <span class="inline-flex items-center gap-1 text-amber-500" aria-hidden="true">
                                @for ($i = 1; $i <= 5; $i++)
                                    <svg class="h-5 w-5 {{ $i <= round($avgRating) ? 'text-amber-500' : 'text-slate-300' }}" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                @endfor
                            </span>
                            <span class="font-medium">{{ number_format($avgRating, 1) }} / 5</span>
                            <span class="text-sm text-slate-500">({{ $reviewCount }} {{ $reviewCount === 1 ? __('review') : __('reviews') }})</span>
                        </div>
                    @endif

                    {{-- Logged-in: tool owner sees message; others: thank-you if already commented, else form --}}
                    @auth
                        @php
                            $isToolOwner = auth()->id() === (int) $tool->user_id;
                            $userHasCommented = $tool->comments->contains('user_id', auth()->id());
                        @endphp
                        @if ($isToolOwner)
                            <div class="rounded-xl p-5 mb-6 ring-1 ring-slate-200 bg-slate-50 border border-slate-200" role="status">
                                <p class="text-slate-700 font-medium">Това е вашият инструмент. Оценките са достъпни само за други потребители.</p>
                            </div>
                        @elseif ($userHasCommented)
                            <div class="rounded-xl p-5 mb-6 ring-1 ring-emerald-200 bg-emerald-50 border border-emerald-100" role="alert">
                                <p class="text-emerald-800 font-medium flex items-center gap-2">
                                    <span>Благодарим ви за вашата оценка!</span>
                                    <span class="text-amber-500" aria-hidden="true">⭐</span>
                                </p>
                                <p class="text-sm text-emerald-700 mt-1">{{ __('You have already left a review for this tool. You can delete it below to leave a new one.') }}</p>
                            </div>
                        @else
                            <div class="bg-slate-50 rounded-xl p-5 mb-6 ring-1 ring-slate-200">
                                <h4 class="text-sm font-semibold text-slate-800 mb-3">{{ __('Leave a Review') }}</h4>
                                @if (session('error'))
                                    <p class="text-sm text-red-600 mb-3">{{ session('error') }}</p>
                                @endif
                                @if (session('success'))
                                    <p class="text-sm text-emerald-600 mb-3">{{ session('success') }}</p>
                                @endif
                                <form action="{{ route('tools.comments.store', $tool) }}" method="POST" class="space-y-4">
                                    @csrf
                                    <div>
                                        <label for="rating" class="block text-sm font-medium text-slate-700 mb-2">{{ __('Rating') }} (1–5) <span class="text-red-500">*</span></label>
                                        <select name="rating" id="rating" required
                                            class="block w-full max-w-xs rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            <option value="" disabled {{ old('rating') === null ? 'selected' : '' }}>{{ __('Choose rating') }}</option>
                                            @for ($i = 1; $i <= 5; $i++)
                                                <option value="{{ $i }}" {{ (int) old('rating') === $i ? 'selected' : '' }}>{{ $i }} {{ $i === 1 ? __('star') : __('stars') }}</option>
                                            @endfor
                                        </select>
                                        @error('rating')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="comment_text" class="block text-sm font-medium text-slate-700 mb-2">{{ __('Your review') }} <span class="text-red-500">*</span></label>
                                        <textarea name="comment_text" id="comment_text" rows="4" required maxlength="2000"
                                            placeholder="{{ __('Share your experience with this tool...') }}"
                                            class="block w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('comment_text') }}</textarea>
                                        @error('comment_text')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                        {{ __('Submit Review') }}
                                    </button>
                                </form>
                            </div>
                        @endif
                    @endauth

                    {{-- Comments list (user name, star rating, date; delete own) --}}
                    <ul class="space-y-4">
                        @forelse ($tool->comments as $comment)
                            <li class="bg-white rounded-lg p-4 ring-1 ring-slate-200">
                                <div class="flex items-start justify-between gap-2 flex-wrap mb-2">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="font-medium text-slate-900">{{ $comment->user->name ?? __('User') }}</span>
                                        <span class="inline-flex text-amber-500" aria-hidden="true">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <svg class="h-4 w-4 {{ $i <= $comment->rating ? 'text-amber-500' : 'text-slate-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                </svg>
                                            @endfor
                                        </span>
                                        <span class="text-sm text-slate-500">{{ $comment->created_at->format('M j, Y') }}</span>
                                    </div>
                                    @auth
                                        @if ($comment->user_id === auth()->id())
                                            <form method="POST" action="{{ route('tools.comments.destroy', [$tool, $comment]) }}" class="inline" onsubmit="return confirm('{{ __('Are you sure you want to delete your review? You can leave a new one after.') }}');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-sm text-slate-500 hover:text-red-600 focus:outline-none focus:underline" title="{{ __('Delete your review') }}">
                                                    {{ __('Delete') }}
                                                </button>
                                            </form>
                                        @endif
                                    @endauth
                                </div>
                                <p class="text-slate-700 text-sm whitespace-pre-wrap">{{ $comment->comment_text }}</p>
                            </li>
                        @empty
                            <li class="text-slate-500 text-sm py-4">{{ __('No reviews yet. Be the first to leave a review!') }}</li>
                        @endforelse
                    </ul>
                </div>

                <p class="pt-4">
                    <a href="{{ route('tools.index') }}" class="inline-flex items-center gap-1 px-4 py-2 rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200 transition text-sm font-medium">
                        ← {{ __('Обратно към каталога') }}
                    </a>
                </p>
            </div>
        </div>
    </div>
</x-app-layout>
