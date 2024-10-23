<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Movie extends Model
{
    use HasFactory;

    protected $fillable = [
        'movie_title',
        'description',
        'thumbnail',
        'genre',
        'release_date'
    ];

    public function reviews():HasMany{
     return  $this->hasMany(Review::class);
    }

}
