<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $table="orders";

    protected $fillable =[
        'id',
        'amount',
        'payment',
        'status',
        'product_id',
        'store_id',
        'user_id'
    ];

    public $timestamps= false;

    public function store(){
        return $this->belongsTo(Store::class,"store_id","id");
    }
    public function product(){
       return $this->hasOne(product::class, "id");
       // return $this->belongsTo(Product::class, "product_id", "id");
    }
}
