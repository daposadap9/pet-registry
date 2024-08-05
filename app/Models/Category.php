<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    // Define los campos que se pueden rellenar masivamente
    protected $fillable = ['name'];

    // Puedes agregar relaciones aquí si es necesario
    // Por ejemplo, si una categoría tiene muchas mascotas:
    // public function pets()
    // {
    //     return $this->hasMany(Pet::class);
    // }
}
