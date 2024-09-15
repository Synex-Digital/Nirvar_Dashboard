<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory;
    public function folder()
    {
        return $this->belongsTo(Folder::class, 'folder_id');  // 'folder_id' is the foreign key
    }
}
