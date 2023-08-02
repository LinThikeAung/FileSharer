<?php
namespace App\Helpers;
use App\Models\SubFolder;

class Helper{
    public static function  getAllFolders($directory){
        $directories = [];
        $subDirectories = SubFolder::where('main_sub_id',$directory->id)->get();
            foreach($subDirectories as $subDirectory){
                $directories[] = $subDirectory;
                $folder = self::getAllFolders($subDirectory);
                $directories = array_merge($directories,$folder);
            }
            return $directories;
    }
}