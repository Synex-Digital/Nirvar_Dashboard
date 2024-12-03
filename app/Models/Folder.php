<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Folder extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
    ];
    public function files()
    {
        return $this->hasMany(File::class, 'folder_id');  // 'folder_id' is the foreign key
    }
    public function prescription_files()
    {
        return $this->hasMany(File::class, 'folder_id')->where('type', 'prescription')->latest();
    }
    public function test_report_files()
    {
        return $this->hasMany(File::class, 'folder_id')->where('type', 'test-report')->latest();
    }
}
