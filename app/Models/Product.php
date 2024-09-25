<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'product'; 

    protected $fillable=[
        'category_id',
        'brand_id',
        'name',
        'price',
        'amount',
        'discount',
        'image',
        'is_trendy',
        'ia_available'
    ];


    public function categries(){
        return $this->belongsTo(categries::class,'category_id');
      }

      public function brand(){
        return $this->belongsTo(Brands::class,'brand_id');
      }
}
