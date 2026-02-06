<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Tool extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    /**
     * Allowed resource types (stored in DB).
     *
     * @var list<string>
     */
    public const RESOURCE_TYPES = [
        'tool',
        'ai_library',
        'application',
        'resource',
    ];

    /**
     * Short display labels for type badges (e.g. 'Library', 'App').
     *
     * @var array<string, string>
     */
    public const TYPE_LABELS = [
        'tool' => 'Tool',
        'ai_library' => 'Library',
        'application' => 'App',
        'resource' => 'Resource',
    ];

    /**
     * Badge CSS classes per type for UI.
     *
     * @var array<string, string>
     */
    public const TYPE_BADGE_CLASSES = [
        'tool' => 'bg-indigo-100 text-indigo-800',
        'ai_library' => 'bg-violet-100 text-violet-800',
        'application' => 'bg-emerald-100 text-emerald-800',
        'resource' => 'bg-slate-100 text-slate-800',
    ];

    protected $fillable = [
        'name',
        'type',
        'link',
        'official_docs_link',
        'description',
        'how_to_use',
        'usage_instructions',
        'examples',
        'examples_link',
        'category_id',
        'user_id',
        'recommended_role',
        'is_active',
        'status',
        'is_approved',
    ];

    protected function casts(): array
    {
        return [
            'examples' => 'array',
            'is_active' => 'boolean',
            'is_approved' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'tag_tool');
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_tool');
    }

    public function screenshots(): HasMany
    {
        return $this->hasMany(ToolScreenshot::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image')
            ->useDisk(env('MEDIA_DISK', 'public'))
            ->singleFile();
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->fit(Fit::Contain, 400, 400);
    }
}