<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Absent extends Model
{
   protected $guarded = ['id'];

   public function absent_details()
   {
       return $this->hasMany(AbsentDetail::class);
   }
}
