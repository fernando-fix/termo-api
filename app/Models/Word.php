<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Word extends Model
{
    protected $table = 'words';

    protected $fillable = [
        'word',
        'length',
        'definition',
        'is_verified',
        'is_valid',
    ];
}
