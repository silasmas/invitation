<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Boisson extends Model
{
    use HasFactory;
    //
    protected $fillable = ['nom', 'description'];
}
