<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;


class Product extends Model
{
    //
    protected $fillable = [
        'code',
        'name',
        'slug',
        'price',
        'stock',
        'description',
        'image',
        'is_active',
    ];
    public static function generateUniqueSlug($name)
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $count = 1;

        // Check for existing slug and append a number if needed
        while (self::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }

        return $slug;
    }
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
