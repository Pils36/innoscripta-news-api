<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $fillable = [
        'title',
        'author',
        'author',
        'content',
        'url',
        'source',
        'category',
        'published_at'
    ];
}
