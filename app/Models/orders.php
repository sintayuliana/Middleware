<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class orders extends Model
{
    use HasFactory;
    protected $primaryKey = 'order_id';

    public $incrementing = false;
    protected $table    = "orders";
    protected $fillable = [
        'order_id',
        'order_date',
        'total',
        'user_id',
        'status',
        'created_at',
        'updated_at'
    ];
}
