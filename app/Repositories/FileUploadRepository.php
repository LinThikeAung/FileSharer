<?php

namespace App\Repositories;

use App\Models\UploadFile;

//use Your Model

/**
 * Class FileUploadRepository.
 */
class FileUploadRepository 
{
    /**
     * @return string
     *  Return the model
     */
    public function storeFile(array $data){
        return UploadFile::create([
            'name'=>$data['name'],
            'user_id'=>$data['user_id'],
            'size'=>$data['size'],
            'type'=>$data['type'],
            'file'=>$data['file'],
            'url'=>$data['url']
        ]);
    }

}
