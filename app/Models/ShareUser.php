<?php

namespace App\Models;

use App\Models\User;
use App\Models\MainFolder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ShareUser extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function share_user(){
        return $this->belongsTo(User::class,'share_id');
    }

    public function other_user(){
        return $this->belongsTo(User::class,'other_id');
    }

    public function main_folder(){
        return $this->belongsTo(MainFolder::class,'main_folder_id');
    }
}
