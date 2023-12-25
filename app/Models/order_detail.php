<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class order_detail extends Model
{
    use HasFactory;
    protected $primaryKey = null;

    public $incrementing = false;
    protected $table    = "order_detail";
    protected $fillable = [
        'order_id',
        'product_id',
        'price',
        'qty',
        'subtotal',
        'created_at',
        'updated_at'
    ];
}
