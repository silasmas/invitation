<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

	public function user()
	{

		return $this->belongsTo(\App\Models\User::class);

	}
	public function ceremonie()
	{

		return $this->hasMany(\App\Models\Ceremonie::class);

	}


	protected $guarded = [];

	public function message()
	{

		return $this->hasMany(\App\Models\Message::class);

	}

}
