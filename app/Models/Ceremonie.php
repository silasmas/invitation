<?php
namespace App\Models;

use Carbon\Carbon;
use App\Models\Groupe;
use App\Models\Invitation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Ceremonie extends Model
{
    use HasFactory;

    public function event()
    {

        return $this->belongsTo(\App\Models\Event::class, 'event_id');

    }

    protected $guarded = [];

    protected $casts = [
        'dressCode' => 'array',
        'date' => 'datetime',
    ];


    public function invitation()
    {

        return $this->hasMany(Invitation::class);

    }
    public function groupe()
    {

        return $this->hasMany(Groupe::class);

    }
    public function getDayAttribute()
    {
        return $this->date?->day;
    }

    public function getMonthAttribute()
    {
        return $this->date?->locale('fr')->translatedFormat('F');
    }

    public function getYearAttribute()
    {
        return $this->date?->year;
    }

    public function getMonthNameAttribute()
    {
        return $this->date?->locale('fr')->translatedFormat('l');
    }
    public function getDayOfWeekAttribute()
{
    return $this->date?->locale('fr')->translatedFormat('l'); // "l" = jour de la semaine en toutes lettres
}

public function getTimeAttribute()
{
    return $this->date?->format('H\hi'); // ex: 14:30
}
}
