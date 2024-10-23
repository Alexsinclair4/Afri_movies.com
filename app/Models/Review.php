<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'movie_id',
        'user_id',
        'rating',
        'comment',
        'status',
    ];


    const pending = 'pending';
    const approved = 'approved';
    const rejected = 'rejected';

    protected $casts =[
    'status'=>'string',
    ];

    public function movies():BelongsTo{
        return $this->belongsTo(Movie::class);
    }
}
