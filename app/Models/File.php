<?php

namespace App\Models;

use App\Models\SubFolder;
use App\Models\MainFolder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class File extends Model
{
    protected $fillable = ['name', 'path'];
    use HasFactory;
    
    public function folder()
    {
        return $this->belongsTo(MainFolder::class,'main_folder_id');
    }

    public function subFolder(){
        return $this->belongsTo(SubFolder::class,'sub_folder_id');
    }
}
