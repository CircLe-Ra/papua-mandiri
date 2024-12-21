<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    protected $guarded = ['id'];

    public function openings()
    {
        return $this->hasMany(Opening::class);
    }

    public function participants()
    {
        return $this->hasMany(Participant::class);
    }

}
