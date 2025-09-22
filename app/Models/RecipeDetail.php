<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecipeDetail extends Model
{
    protected $fillable = [
        'recipe_id',
        'ingredient_id',
        'quantity',
        'unit',
        'price_per_unit',
        'total_cost'
    ];

    public function recipe()
    {
        return $this->belongsTo(MasterRecipe::class, 'recipe_id');
    }

    public function ingredient()
    {
        return $this->belongsTo(Item::class, 'ingredient_id');
    }

    protected static function boot()
    {
        parent::boot();
        
        static::saving(function ($detail) {
            $detail->total_cost = $detail->quantity * $detail->price_per_unit;
        });
    }
}