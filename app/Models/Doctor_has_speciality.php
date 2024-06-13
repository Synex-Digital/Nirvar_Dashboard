<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Doctor_has_speciality extends Model
{
    use HasFactory;
    public function doctor(){
        return $this->belongsTo(Doctor::class, 'doctor_id', 'id');
    }

    public function speciality(){
        return $this->belongsTo(Specialist::class, 'specialist_id', 'id')->withDefault([
            'name'      => 'Unknown',
        ]);
    }

}
