<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;


	public function ceremonie()
	{

		return $this->belongsTo(\App\Models\Ceremonie::class);

	}




	protected $guarded = [];
}
