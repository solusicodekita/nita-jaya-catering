<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterRecipe extends Model
{
    protected $fillable = [
        'recipe_number',
        'name',
        'category_id',
        'property',
        'yield_quantity',
        'yield_unit',
        'total_cost'
    ];

    public function category()
    {
        return $this->belongsTo(RecipeCategory::class, 'category_id');
    }

    public function details()
    {
        return $this->hasMany(RecipeDetail::class, 'recipe_id');
    }

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($recipe) {
            if (empty($recipe->recipe_number)) {
                $recipe->recipe_number = self::generateRecipeNumber($recipe);
            }
        });
    }

    protected static function generateRecipeNumber($recipe)
    {
        $prefix = 'RCP';
        $category = RecipeCategory::find($recipe->category_id);
        if ($category) {
            $categoryCode = strtoupper(substr($category->name, 0, 3));
        } else {
            $categoryCode = 'GEN';
        }
        
        $lastRecipe = self::orderBy('id', 'desc')->first();
        $number = $lastRecipe ? ($lastRecipe->id + 1) : 1;
        
        return sprintf('%s/%s/%s/%s', $prefix, $categoryCode, date('Y'), str_pad($number, 4, '0', STR_PAD_LEFT));
    }
}
