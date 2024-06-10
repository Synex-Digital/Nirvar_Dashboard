<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;
    public function prescription(){
        return $this->hasMany(Prescription::class);
    }
    public function user(){
        return $this->belongsTo(User::class , 'user_id', 'id');
    }
}
