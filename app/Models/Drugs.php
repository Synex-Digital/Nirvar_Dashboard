<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Drugs extends Model
{
    use HasFactory;
    public function medicine()
    {
        return $this->hasMany(Medicine::class, 'drug_id');
    }
}
