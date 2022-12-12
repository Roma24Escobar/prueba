<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $table="comments";

    protected $fillable=[
        'id',
        'description',
        'photo',
        'store_id',
        'user_id'
    ];
}
