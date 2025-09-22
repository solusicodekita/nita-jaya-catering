<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecipeCategory extends Model
{
    protected $fillable = ['name', 'description'];

    public function recipes()
    {
        return $this->hasMany(MasterRecipe::class, 'category_id');
    }
}
