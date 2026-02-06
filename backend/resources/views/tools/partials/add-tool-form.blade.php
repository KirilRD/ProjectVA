@props(['inModal' => false, 'categories' => collect(), 'tags' => collect(), 'roles' => collect()])
@php
    $idPrefix = $inModal ? 'modal_' : '';
    $initialIsActive = filter_var(old('is_active', true), FILTER_VALIDATE_BOOLEAN);
@endphp
<form action="{{ route('tools.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5" x-data="{ isActive: {{ $initialIsActive ? 'true' : 'false' }} }">
    @csrf

    <div>
        <label for="{{ $idPrefix }}name" class="block text-sm font-medium text-slate-700">{{ __('Name') }} <span class="text-red-500">*</span></label>
        <input type="text" name="name" id="{{ $idPrefix }}name" value="{{ old('name') }}" required
            class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-600 focus:ring-indigo-600 @error('name') border-red-500 @enderror">
        @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="{{ $idPrefix }}type" class="block text-sm font-medium text-slate-700 mb-1">{{ __('Type') }}</label>
        <div class="relative">
            <select name="type" id="{{ $idPrefix }}type" required
                class="block w-full appearance-none rounded-xl border border-slate-300 bg-white py-2.5 pl-4 pr-10 text-sm text-slate-900 shadow-sm transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 @error('type') border-red-500 @enderror">
                @foreach (\App\Models\Tool::RESOURCE_TYPES as $t)
                    <option value="{{ $t }}" {{ old('type', 'tool') === $t ? 'selected' : '' }}>{{ __(\App\Models\Tool::TYPE_LABELS[$t] ?? $t) }}</option>
                @endforeach
            </select>
            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </div>
        </div>
        @error('type')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="{{ $idPrefix }}link" class="block text-sm font-medium text-slate-700">{{ __('Link (URL)') }} <span class="text-red-500">*</span></label>
        <input type="url" name="link" id="{{ $idPrefix }}link" value="{{ old('link') }}" required placeholder="https://..."
            class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-600 focus:ring-indigo-600 @error('link') border-red-500 @enderror">
        @error('link')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="{{ $idPrefix }}official_docs_link" class="block text-sm font-medium text-slate-700">{{ __('Docs Link') }}</label>
        <input type="url" name="official_docs_link" id="{{ $idPrefix }}official_docs_link" value="{{ old('official_docs_link') }}" placeholder="https://..."
            class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-600 focus:ring-indigo-600 @error('official_docs_link') border-red-500 @enderror">
        @error('official_docs_link')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="{{ $idPrefix }}examples_link" class="block text-sm font-medium text-slate-700">{{ __('Examples Link') }}</label>
        <input type="url" name="examples_link" id="{{ $idPrefix }}examples_link" value="{{ old('examples_link') }}" placeholder="https://..."
            class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-600 focus:ring-indigo-600 @error('examples_link') border-red-500 @enderror">
        @error('examples_link')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="{{ $idPrefix }}description" class="block text-sm font-medium text-slate-700">{{ __('Description') }} <span class="text-red-500">*</span></label>
        <textarea name="description" id="{{ $idPrefix }}description" rows="2" required class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-600 focus:ring-indigo-600 @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
        @error('description')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="{{ $idPrefix }}how_to_use" class="block text-sm font-medium text-slate-700">{{ __('How to use') }} <span class="text-red-500">*</span></label>
        <textarea name="how_to_use" id="{{ $idPrefix }}how_to_use" rows="2" required class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-600 focus:ring-indigo-600 @error('how_to_use') border-red-500 @enderror">{{ old('how_to_use') }}</textarea>
        @error('how_to_use')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="{{ $idPrefix }}usage_instructions" class="block text-sm font-medium text-slate-700">{{ __('Usage Instructions') }}</label>
        <textarea name="usage_instructions" id="{{ $idPrefix }}usage_instructions" rows="3" placeholder="Markdown supported..."
            class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-600 focus:ring-indigo-600 font-mono text-sm @error('usage_instructions') border-red-500 @enderror">{{ old('usage_instructions') }}</textarea>
        @error('usage_instructions')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div class="flex items-center justify-between rounded-lg border border-slate-200 bg-slate-50/50 px-4 py-3">
        <div>
            <span class="text-sm font-medium text-slate-700">{{ __('Active') }}</span>
            <p class="text-xs text-slate-500">{{ __('Show this tool in the list.') }}</p>
        </div>
        <input type="hidden" name="is_active" :value="isActive ? '1' : '0'">
        <button type="button" role="switch" :aria-checked="isActive" @click="isActive = !isActive"
            class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2"
            :class="isActive ? 'bg-indigo-600' : 'bg-slate-200'">
            <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out translate-x-0.5"
                :class="isActive ? 'translate-x-5' : 'translate-x-0.5'"></span>
        </button>
    </div>

    {{-- Category: professional dropdown --}}
    <div>
        <label for="{{ $idPrefix }}category_id" class="block text-sm font-medium text-slate-700 mb-1">{{ __('Category') }}</label>
        <div class="relative">
            <select name="category_id" id="{{ $idPrefix }}category_id"
                class="block w-full appearance-none rounded-xl border border-slate-300 bg-white py-2.5 pl-4 pr-10 text-sm text-slate-900 shadow-sm transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 @error('category_id') border-red-500 @enderror">
                <option value="">{{ __('None') }}</option>
                @foreach ($categories as $c)
                    <option value="{{ $c->id }}" {{ old('category_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                @endforeach
            </select>
            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </div>
        </div>
        @error('category_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    {{-- Recommended role: single select --}}
    @php
        $recommendedRoles = ['Backend', 'Frontend', 'QA', 'Design', 'PM'];
    @endphp
    <div>
        <label for="{{ $idPrefix }}recommended_role" class="block text-sm font-medium text-slate-700 mb-1">{{ __('Recommended role') }}</label>
        <div class="relative">
            <select name="recommended_role" id="{{ $idPrefix }}recommended_role"
                class="block w-full appearance-none rounded-xl border border-slate-300 bg-white py-2.5 pl-4 pr-10 text-sm text-slate-900 shadow-sm transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 @error('recommended_role') border-red-500 @enderror">
                <option value="">{{ __('None') }}</option>
                @foreach ($recommendedRoles as $role)
                    <option value="{{ $role }}" {{ old('recommended_role') === $role ? 'selected' : '' }}>{{ $role }}</option>
                @endforeach
            </select>
            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </div>
        </div>
        @error('recommended_role')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    {{-- Roles: professional checkbox group --}}
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-2">{{ __('Roles') }}</label>
        <div class="rounded-xl border border-slate-200 bg-slate-50/30 p-4">
            <div class="flex flex-wrap gap-x-6 gap-y-3">
                @foreach ($roles as $r)
                    <label class="inline-flex items-center gap-2.5 cursor-pointer rounded-lg px-3 py-2 hover:bg-white/80 transition-colors">
                        <input type="checkbox" name="role_ids[]" value="{{ $r->id }}" {{ in_array($r->id, old('role_ids', [])) ? 'checked' : '' }}
                            class="h-4 w-4 rounded border-slate-300 text-indigo-600 shadow-sm focus:ring-indigo-500 focus:ring-offset-0">
                        <span class="text-sm font-medium text-slate-700">{{ ucfirst($r->name) }}</span>
                    </label>
                @endforeach
            </div>
        </div>
        @error('role_ids')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    {{-- Tags --}}
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-2">{{ __('Tags') }}</label>
        <div class="flex flex-wrap gap-3">
            @foreach ($tags as $t)
                <label class="inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="tag_ids[]" value="{{ $t->id }}" {{ in_array($t->id, old('tag_ids', [])) ? 'checked' : '' }}
                        class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-600">
                    <span class="ml-2 text-sm text-slate-700">{{ $t->name }}</span>
                </label>
            @endforeach
        </div>
        @error('tag_ids')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    {{-- Image: basic file input (no Alpine, no preview) --}}
    <div>
        <label for="{{ $idPrefix }}image" class="block text-sm font-medium text-slate-700 mb-1">{{ __('Image') }}</label>
        <input type="file" name="image" id="{{ $idPrefix }}image" class="form-control block w-full text-sm text-slate-500 file:mr-4 file:rounded-lg file:border-0 file:bg-indigo-50 file:px-4 file:py-2 file:text-sm file:font-medium file:text-indigo-700 hover:file:bg-indigo-100" accept="image/*">
        <p class="text-xs text-slate-500 mt-1">PNG, JPG, GIF (max 2MB)</p>
        @error('image')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div class="flex items-center gap-3 pt-4 border-t border-slate-200">
        <button type="submit" class="rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2">
            {{ __('Create Tool') }}
        </button>
        @if ($inModal)
            <button type="button" @click="$dispatch('close-add-tool-modal')" class="rounded-lg border border-slate-300 bg-white px-5 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2">
                {{ __('Cancel') }}
            </button>
        @else
            <a href="{{ route('tools.index') }}" class="inline-flex items-center px-4 py-2 rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200 transition text-sm font-medium">
                {{ __('Отказ') }}
            </a>
        @endif
    </div>
</form>
