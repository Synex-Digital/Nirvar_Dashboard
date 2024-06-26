<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    use HasFactory;

    protected $fillable = ['user_id'];

    public function docHasSpec(){
        return $this->hasOne(Doctor_has_speciality::class, 'doctor_id', 'id');
    }
    public function user(){
        return $this->belongsTo(User::class, 'user_id', 'id')->withDefault([
            'name'      => 'Unknown',
            'email'     => 'Unknown',
            'number'    => 'Unknown',

        ]);
    }
    public function prescription(){
        return $this->hasMany(Prescription::class, 'doctor_id', 'id');
    }

}
