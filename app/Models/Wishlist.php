<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    protected $fillable = [
        'user_id',
        'product_id',
        'product_type',
        'session_id'
    ];

    public function product()
    {
        // Since product can be from different tables (Product, SellerProduct, DigitalProduct),
        // we might need a more complex relationship or just fetch manually in the controller.
        // For now, we'll handle the data fetching in the controller based on product_type.
        return null;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
