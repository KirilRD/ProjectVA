<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Category extends Model
{
    protected $fillable = ['name', 'slug'];

    protected static function booted(): void
    {
        static::saving(function (Category $category): void {
            if (empty($category->slug) || $category->isDirty('name')) {
                $category->slug = Str::slug($category->name ?? '');
            }
        });
    }

    public function tools(): HasMany
    {
        return $this->hasMany(Tool::class);
    }
}
