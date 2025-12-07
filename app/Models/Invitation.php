<?php

namespace App\Models;

use App\Models\Ceremonie;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;
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
            $ceremonieId = $invitation->ceremonie_id ?? null;
            if ($ceremonieId) {
                $ceremonie = Ceremonie::find($ceremonieId);
                if ($ceremonie && $ceremonie->event && $ceremonie->event->status === 'termine') {
                    throw ValidationException::withMessages([
                        'ceremonie_id' => ["Impossible d'ajouter une invitation pour un événement terminé."],
                    ]);
                }
            }
        });

        static::updating(function ($invitation) {
            $ceremonieId = $invitation->ceremonie_id ?? $invitation->getOriginal('ceremonie_id');
            if ($ceremonieId) {
                $ceremonie = Ceremonie::find($ceremonieId);
                if ($ceremonie && $ceremonie->event && $ceremonie->event->status === 'termine') {
                    throw ValidationException::withMessages([
                        'ceremonie_id' => ["Impossible de modifier une invitation liée à un événement terminé."],
                    ]);
                }
            }
        });
    }
	protected $guarded = [];
}
