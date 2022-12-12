<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shelve extends Model
{
    use HasFactory;
    protected $table = 'shelves';
    protected $fillable = ['id', 'name'];

    public $timestamps = false;
}
