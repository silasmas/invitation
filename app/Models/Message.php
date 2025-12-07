<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class Message extends Model
{
    use HasFactory;


	public function ceremonie()
	{

		return $this->belongsTo(\App\Models\Ceremonie::class);

	}


protected static function boot()
    {
        parent::boot();

        static::creating(function ($message) {
            $ceremonieId = $message->ceremonie_id ?? null;
            if ($ceremonieId) {
                $ceremonie = Ceremonie::find($ceremonieId);
                if ($ceremonie && $ceremonie->event && $ceremonie->event->status === 'termine') {
                    throw ValidationException::withMessages([
                        'ceremonie_id' => ["Impossible d'ajouter un message pour un événement terminé."],
                    ]);
                }
            }
        });

        static::updating(function ($message) {
            $ceremonieId = $message->ceremonie_id ?? $message->getOriginal('ceremonie_id');
            if ($ceremonieId) {
                $ceremonie = Ceremonie::find($ceremonieId);
                if ($ceremonie && $ceremonie->event && $ceremonie->event->status === 'termine') {
                    throw ValidationException::withMessages([
                        'ceremonie_id' => ["Impossible de modifier un message lié à un événement terminé."],
                    ]);
                }
            }
        });
    }

	protected $guarded = [];
}
