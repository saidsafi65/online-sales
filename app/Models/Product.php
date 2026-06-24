<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['name', 'price', 'discount', 'category', 'description', 'image', 'is_out_of_stock'];

    /**
     * احسب السعر بعد الخصم
     */
    public function getFinalPrice()
    {
        return $this->price - ($this->price * $this->discount / 100);
    }

    /**
     * احسب قيمة الخصم
     */
    public function getDiscountAmount()
    {
        return $this->price * $this->discount / 100;
    }
}
