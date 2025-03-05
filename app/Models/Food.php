<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Food extends Model
{
  protected $casts = [

      'customer_group_discounts' => 'array'

  ];
  protected $with = ['category'];
  protected $fillable = [
        'name',
        'code',
        'hsn',
        'cost',
        'price',
        'price_include_gst',
        'sale_price',
        'gst_percentage',
        'food_category_id',
        'cost_include_gst',
        'image',
        'kitchen_id',
        'food_brand_id',
        'created_by',
        'updated_by',

    ];

    public function locations()
    {
      return $this->belongsToMany(Location::class)->withPivot('quantity','supplier_id')->withTimestamps();
    }

    public function products()
    {
      return $this->belongsToMany(Product::class)->withPivot('quantity','product_name');
    }
    public function modifiers()
    {
      return $this->belongsToMany(Modifier::class);
    }
    // public function category()
    // {
    //   return $this->belongsTo(ProductCategory::class,'product_category_id');
    // }

    public function category()
    {
      return $this->belongsTo(FoodCategory::class,'food_category_id');
    }
    public function kitchen()
    {
      return $this->belongsTo(Kitchen::class);
    }



}
