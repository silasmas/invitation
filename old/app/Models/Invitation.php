<?php

namespace App\Models;

use App\Models\Ceremonie;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Invitation extends Model
{
    use HasFactory;
    protected $casts = [
        'boissons' => 'array',
    ];
	public function guests()
	{

		return $this->belongsTo(\App\Models\Guest::class,'guest_id');

	}


	public function ceremonies()
	{

		return $this->belongsTo(Ceremonie::class,'ceremonie_id');

	}
	public function groupe()
	{

		return $this->belongsTo(Groupe::class,'groupe_id');

	}

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($invitation) {
            do {
                $reference = "INV-" . date('Ymd') . "-" . strtoupper(Str::random(6));
            } while (DB::table('invitations')->where('reference', $reference)->exists());

            $invitation->reference = $reference;
        });
    }
	protected $guarded = [];
}
