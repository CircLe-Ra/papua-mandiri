<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Opening extends Model
{
    protected $guarded = ['id'];

    public function reception()
    {
        return $this->belongsTo(Reception::class);
    }

    public function program()
    {
        return $this->belongsTo(Program::class);
    }
}
