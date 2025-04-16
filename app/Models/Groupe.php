<?php

namespace App\Models;

use App\Models\Ceremonie;
use App\Models\Invitation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Groupe extends Model
{
    use HasFactory;

	// protected $guarder = [];
    protected $fillable = ['nom', 'description', 'ceremonie_id'];


    public function invitation()
	{

		return $this->hasMany(Invitation::class);

	}
    public function ceremonie()
	{

		return $this->belongsTo(Ceremonie::class);

	}
}
