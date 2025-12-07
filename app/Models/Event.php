<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
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

protected static function booted()
{
    static::addGlobalScope('userEventScope', function (Builder $query) {
        if (auth()->check() && !auth()->user()->hasRole('super_admin')) {
            $query->where('user_id', auth()->id());
        }
    });
}
	protected $guarded = [];

	public function message()
	{

		return $this->hasMany(\App\Models\Message::class);

	}
 /**
     * Scope pour exclure les événements terminés.
     */
    public function scopeNotTermine(Builder $query): Builder
    {
        return $query->where('status', '!=', 'termine');
    }

    public static function getEloquentQuery(): Builder
{
    $query = parent::getEloquentQuery();

    if (auth()->user()?->hasRole('super_admin')) {
        return $query;
    }

    return $query->where('user_id', auth()->id());
}

}
