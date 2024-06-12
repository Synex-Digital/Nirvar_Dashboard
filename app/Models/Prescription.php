<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prescription extends Model
{
    use HasFactory;
    public function medicine(){
        return $this->hasMany(Medicine::class, 'prescription_id');
    }
    public function patient(){
        return $this->belongsTo(Patient::class, 'patient_id', 'id');
    }
    public function doctor(){
        return $this->belongsTo(Doctor::class, 'doctor_id', 'id');
    }

}
