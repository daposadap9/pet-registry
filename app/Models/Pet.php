<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pet extends Model
{
    protected $fillable = ['name', 'status', 'category_id', 'image_url']; // Añade 'image_url'
}

