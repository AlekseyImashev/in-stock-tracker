<?php

namespace App;

class Product extends Model
{
    protected $guarded = [];

    public function inStock()
    {
        return $this
            ->stock()
            ->where('in_stock', true)
            ->exists();
    }

    public function stock()
    {
        return $this->hasMany(Stock::class);
    }
}
