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

    public static function updateAllFolders($directory,$fileSize){
        if($directory->main_folder){
            if($directory->main_folder->size){
                $size =  Helper::unFormatFileSize($directory->main_folder->size) + $fileSize;
            }else{
                $size =  0 + $fileSize;
            }
            
            $directory->main_folder->size = Helper::formatFileSize($size);
            $directory->main_folder->update();
        }else{
            $subDirectory = SubFolder::where('id',$directory->main_sub_id)->first();
            if($subDirectory){
                if($subDirectory->size){
                    $size =  Helper::unFormatFileSize($subDirectory->size) + $fileSize;
                }else{
                    $size = 0 + $fileSize;
                }
                $subDirectory->size = Helper::formatFileSize($size);
                $subDirectory->update();
                self::updateAllFolders($subDirectory,$fileSize);
            }
        }
    }

    public static function subtrationAllFolder($directory,$fileSize){
        if($directory->main_folder){
            $size =  Helper::unFormatFileSize($directory->main_folder->size) - $fileSize;
            if($size == 0){
                $directory->main_folder->size = null;
            }else{
                $directory->main_folder->size = Helper::formatFileSize($size);
            }
            $directory->main_folder->update();
        }else{
            $subDirectory = SubFolder::where('id',$directory->main_sub_id)->first();
            if($subDirectory){
                $size =  Helper::unFormatFileSize($subDirectory->size) - $fileSize;
                if($size == 0){
                    $subDirectory->size = null;
                }else{
                    $subDirectory->size = Helper::formatFileSize($size);
                }
                $subDirectory->update();
                self::subtrationAllFolder($subDirectory,$fileSize);
            }
        }
    }

    public static function deleteAllFiles($file,$fileSize){
        $directory= SubFolder::where('id',$file->id)->first();
        if($directory->main_folder){
            if($directory->main_folder->size == null){
                $mainFolderSize = 0;
            }else{
                $mainFolderSize = Helper::unFormatFileSize($directory->main_folder->size);
            }
            $size =  $mainFolderSize- $fileSize;
            if($size == 0){
                $directory->main_folder->size = null;
            }else{
                $directory->main_folder->size = Helper::formatFileSize($size);
            }
            $directory->main_folder->update();
        }else{
            $subDirectory = SubFolder::where('id',$directory->main_sub_id)->first();
            if($subDirectory){
                if($subDirectory->size == null){
                    $subDirectorySize = 0;
                }else{
                    $subDirectorySize =  Helper::unFormatFileSize($subDirectory->size);
                }
                $size =   $subDirectorySize - $fileSize;
                if($size == 0){
                    $subDirectory->size = null;
                }else{
                    $subDirectory->size = Helper::formatFileSize($size);
                }
                $subDirectory->update();
                self::subtrationAllFolder($subDirectory,$fileSize);
            }
        }
    }

    public static function unFormatFileSize($value){
        $sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
        $search_value = explode(' ',$value)[1];
        $size = explode(' ',$value)[0];
        $index = array_search($search_value,$sizes);
        return round($size * pow (1024 , $index));
    }

    public static function formatFileSize($bytes)
    {
        $sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
        if ($bytes === 0) return '0 Byte';
        $i = floor(log($bytes, 1024));
        return round($bytes / pow(1024, $i)).' '. $sizes[$i];
    }
}