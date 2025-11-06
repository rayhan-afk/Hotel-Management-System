<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'unit', 'stock', 'min_stock', 'status'
    ];

    public function transactions()
    {
        return $this->hasMany(IngredientTransaction::class);
    }
}
