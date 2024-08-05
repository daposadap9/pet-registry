<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pet extends Model
{
    protected $fillable = ['name', 'category_id', 'state_id', 'image_url'];

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}

