<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ToolScreenshot extends Model
{
    protected $fillable = ['tool_id', 'path'];

    public function tool(): BelongsTo
    {
        return $this->belongsTo(Tool::class);
    }
}
