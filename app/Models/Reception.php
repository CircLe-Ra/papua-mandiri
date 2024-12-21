<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reception extends Model
{
    protected $guarded = ['id'];

    public function openings()
    {
        return $this->hasMany(Opening::class);
    }
}
