<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderSync extends Model
{
    protected $fillable = [
        'shopify_order_id',
        'status',
    ];
}
