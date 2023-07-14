<?php

namespace App\Models;

use App\Models\MainFolder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class File extends Model
{
    protected $fillable = ['name', 'path'];
    use HasFactory;
    
    public function folder()
    {
        return $this->belongsTo(MainFolder::class);
    }
}
