<?php

namespace App\Repositories;

use datatables;
use App\Models\MainFile;
use App\Models\MainFolder;
use App\Models\UploadFile;
use Illuminate\Support\Facades\Crypt;

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
        return MainFolder::create([
            'name'=>$data['name'],
            'user_id'=>$data['user_id'],
            'size'=>$data['size'],
            'type'=>$data['type'],
            'file'=>$data['file'],
            'url'=>$data['url']
        ]);
    }

    public function getAllFiles(){
       
        $id = auth()->id();
        $query = MainFolder::where('user_id',$id)->latest();

        if(request()->name){
            $query = $query->where('name','LIKE','%'.request()->name.'%');
        }

        if(request()->type){
            $query = $query->where('type',request()->type);
        }

        if(request()->date){
            $date = explode("-",request()->date);
            $from = $date[0];
            $to = $date[1];
            $query = $query->whereDate('created_at','>=',$from)->whereDate('created_at','<=',$to);
        }

        return datatables($query)
        ->editColumn('name',function($each){
            if($each->type == 'png' || $each->type == 'jpg' || $each->type == 'svg' || $each->type == 'jpeg' || $each->type == 'gig'){
              return '<img src="'.asset('/backend/images/image.png').'" class="mr-3"/> <span>'.$each->name.'</span>';
            }
            if($each->type == 'mp4' || $each->type == 'mov'){
                return '<img src="'.asset('/backend/images/video.png').'" class="mr-3"/> <span>'.$each->name.'</span>';
            }
            if($each->type == 'folder'){
                return '<img src="'.asset('/backend/images/folder_icon.png').'" class="mr-3"/> <span>'.$each->name.'</span>';
            }

            if($each->type == 'pdf'){
                return '<img src="'.asset('/backend/images/pdf.png').'" class="mr-3"/> <span>'.$each->name.'</span>';
            }

            if($each->type == 'sql'){
                return '<img src="'.asset('/backend/images/sql.png').'" class="mr-3"/> <span>'.$each->name.'</span>';
            }

            if($each->type == 'txt'){
                return '<img src="'.asset('/backend/images/txt.png').'" class="mr-3"/> <span>'.$each->name.'</span>';
            }

            if($each->type == 'zip'){
                return '<img src="'.asset('/backend/images/zip.png').'" class="mr-3"/> <span>'.$each->name.'</span>';
            }
        })
        ->editColumn('size',function($each){
            return $each->size??"-";
        })
        ->editColumn('created_at',function($each){
            return $each->created_at->format('d-m-Y h:i A');
        })
        ->rawColumns(['action','name'])
        ->toJson();
    }

    public function getTypeData(){
        return MainFolder::distinct()->get('type');
    }
}
