<?php

namespace App\Models;

use App\Models\Ceremonie;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ShortLink extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function ceremonie()
    {
        return $this->belongsTo(Ceremonie::class);
    }
}
