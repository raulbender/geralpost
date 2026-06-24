<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProcessedSource extends Model {
    protected $table = 'processed_sources';

    protected $fillable = [
        'url',
        'url_hash',
        'source',
    ];
}
