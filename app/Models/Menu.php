<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Menu extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'menus';
    protected $guarded = [];
    public $timestamps = true;

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function menuDetails()
    {
        return $this->hasMany(MenuDetail::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_menu', 'menu_id', 'category_id');
    }

    public function transaksiMenus()
    {
        return $this->hasMany(TransaksiMenu::class);
    }
}
