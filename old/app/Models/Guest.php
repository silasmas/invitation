<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guest extends Model
{
    use HasFactory;

	protected $guarded = [];

	public function event()
	{

		return $this->belongsTo(\App\Models\Event::class);

	}
    public function invitation()
	{

		return $this->hasMany(\App\Models\Invitation::class);

	}

	public function message()
	{

		return $this->hasMany(\App\Models\Message::class);

	}
    public static function withValidEmail()
{
    return self::whereNotNull('email')
        ->where('email', '!=', '')
        ->where('email', 'like', '%@%');
}


}
