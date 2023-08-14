<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Cart extends Model
{
    use HasFactory;

    protected $guarded = [];
    
    /**
     * One to one relationship with User model
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Many to many relationship with Product model
     */
    public function items()
    {
        return $this->belongsToMany(Product::class, 'cart_items')->withPivot('quantity');
    }


    public function getTotalPriceAttribute()
    {
        return $this->items->sum(function ($item) {
            return $item->pivot->quantity * $item->price;
        });
    }
}
