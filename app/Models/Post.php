<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class Post extends Model {
    protected $fillable = [
        'title',
        'content',
        'scheduled_at',
        'status',
    ];

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function scopeForFeed(Builder $query): Builder {
        // Today: It shows everything for us to see the cards on the screen, but in production, it should only show published posts.
        return $query->whereIn('status', ['pending', 'published']);

        // In the future (when Redis is active), you will only need to comment the line above and uncomment this one:
        // return $query->where('status', 'published');
    }
}
