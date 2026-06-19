<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class Post extends Model {
    protected $fillable = [
        'user_id',
        'title',
        'content',
        'scheduled_at',
        'status',
    ];

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function scopeForFeed(Builder $query): Builder {
        return $query->where('status', 'published');
    }    

    public function scopeOnlyRevised(Builder $query): Builder {
        return $query->where('status', 'revised');
    }

}
