<div class="space-y-4">
    <div>
        <label class="block text-sm font-medium text-gray-700">{{ __('Name') }} *</label>
        <input type="text" name="name" value="{{ old('name', $tool?->name) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
        @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">{{ __('Type') }}</label>
        <select name="type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
            @foreach (\App\Models\Tool::RESOURCE_TYPES as $t)
                <option value="{{ $t }}" {{ old('type', $tool?->type ?? 'tool') === $t ? 'selected' : '' }}>{{ __(\App\Models\Tool::TYPE_LABELS[$t] ?? $t) }}</option>
            @endforeach
        </select>
        @error('type')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">{{ __('Link (URL)') }} *</label>
        <input type="url" name="link" value="{{ old('link', $tool?->link) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
        @error('link')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">{{ __('Official docs link') }}</label>
        <input type="url" name="official_docs_link" value="{{ old('official_docs_link', $tool?->official_docs_link) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
        @error('official_docs_link')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">{{ __('Description') }} *</label>
        <textarea name="description" rows="3" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('description', $tool?->description) }}</textarea>
        @error('description')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">{{ __('How to use') }} *</label>
        <textarea name="how_to_use" rows="3" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('how_to_use', $tool?->how_to_use) }}</textarea>
        @error('how_to_use')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">{{ __('Usage instructions (markdown)') }}</label>
        <textarea name="usage_instructions" rows="5" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('usage_instructions', $tool?->usage_instructions) }}</textarea>
        @error('usage_instructions')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">{{ __('Examples link') }}</label>
        <input type="url" name="examples_link" value="{{ old('examples_link', $tool?->examples_link) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
        @error('examples_link')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">{{ __('Category') }}</label>
        <select name="category_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
            <option value="">{{ __('None') }}</option>
            @foreach ($categories as $c)
                <option value="{{ $c->id }}" {{ old('category_id', $tool?->category_id) == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
            @endforeach
        </select>
        @error('category_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    @php
        $recommendedRoles = ['Backend', 'Frontend', 'QA', 'Design', 'PM'];
    @endphp
    <div>
        <label class="block text-sm font-medium text-gray-700">{{ __('Recommended role') }}</label>
        <select name="recommended_role" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
            <option value="">{{ __('None') }}</option>
            @foreach ($recommendedRoles as $role)
                <option value="{{ $role }}" {{ old('recommended_role', $tool?->recommended_role) === $role ? 'selected' : '' }}>{{ $role }}</option>
            @endforeach
        </select>
        @error('recommended_role')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    @php
        $currentRoleIds = $tool && $tool->relationLoaded('roles') ? collect($tool->getRelation('roles'))->pluck('id')->toArray() : [];
        $currentTagIds = $tool && $tool->relationLoaded('tags') ? collect($tool->getRelation('tags'))->pluck('id')->toArray() : [];
    @endphp
    <div>
        <label class="block text-sm font-medium text-gray-700">{{ __('Roles') }}</label>
        <div class="flex flex-wrap gap-2 mt-1">
            @foreach ($roles as $r)
                <label class="inline-flex items-center">
                    <input type="checkbox" name="role_ids[]" value="{{ $r->id }}" {{ in_array($r->id, old('role_ids', $currentRoleIds)) ? 'checked' : '' }} class="rounded border-gray-300">
                    <span class="ml-1">{{ $r->name }}</span>
                </label>
            @endforeach
        </div>
        @error('role_ids')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">{{ __('Tags') }}</label>
        <div class="flex flex-wrap gap-2 mt-1">
            @foreach ($tags as $tag)
                <label class="inline-flex items-center">
                    <input type="checkbox" name="tag_ids[]" value="{{ $tag->id }}" {{ in_array($tag->id, old('tag_ids', $currentTagIds)) ? 'checked' : '' }} class="rounded border-gray-300">
                    <span class="ml-1">{{ $tag->name }}</span>
                </label>
            @endforeach
        </div>
        @error('tag_ids')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">{{ __('Image') }}</label>
        <input type="file" name="image" accept="image/*" class="mt-1 block w-full">
        @error('image')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
</div>
