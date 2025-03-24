<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

	public function events()
	{

		return $this->belongsTo(\App\Models\Event::class);

	}


	public function guests()
	{

		return $this->belongsTo(\App\Models\Guest::class);

	}


	protected $guarded = [];
}
