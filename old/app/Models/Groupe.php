<?php

namespace App\Models;

use App\Models\Invitation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Groupe extends Model
{
    use HasFactory;

	protected $fillable = ['nom', 'description'];

    public function invitation()
	{

		return $this->hasMany(Invitation::class);

	}
}
