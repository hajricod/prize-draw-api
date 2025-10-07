<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'draw_id', 'code'];

    protected static function booted()
    {
        static::creating(function ($ticket) {
            $ticket->code = strtoupper(Str::random(10));
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function draw()
    {
        return $this->belongsTo(Draw::class);
    }
}
