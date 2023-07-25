<?php

namespace App\Models;

use App\Models\MainFolder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SubFolder extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $casts = [
        'path' => 'array',
    ];

    public function main_folder(){
        return $this->belongsTo(MainFolder::class,'parent_id');
    }
}
