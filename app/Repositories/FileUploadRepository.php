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
            if($each->type == 'png' || $each->type == 'jpg' || $each->type == 'svg' || $each->type == 'jpeg' || $each->type == 'gif' || $each->type == 'webp'){
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
        ->addColumn('action',function($each){
            if ($each->type == 'folder') {
                $drop_icon = '  <div class="btn-group dropstart">
                <button type="button" class="dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-three-dots-vertical"></i>
                </button>
                <ul class="dropdown-menu">
                    <li class="dropdown-item copy" id="'.$each->url.'"><i class="bi bi-link copy" id="'.$each->url.'"></i> <p class="copy" id="'.$each->url.'">Copy Link</p></li>
                    <a href="/upload-zip?fileName='.$each->name.'" class="dropdown-item"><i class="bi bi-download download"></i> <p class="download"">Download</p></a>
                    <li class="dropdown-item"><i class="bi bi-share"></i> <p>Share</p></li>
                    <li class="dropdown-item delete_folder"  id="'.$each->name.'"><i class="bi bi-trash delete_folder" id="'.$each->name.'"></i> <p class="delete_folder" id="'.$each->name.'">Delete</p></li>
                </ul> 
            </div>';
            }else{
                $drop_icon = '  <div class="btn-group dropstart">
                <button type="button" class="dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-three-dots-vertical"></i>
                </button>
                <ul class="dropdown-menu">
                    <li class="dropdown-item copy" id="'.$each->url.'"><i class="bi bi-link copy" id="'.$each->url.'"></i> <p class="copy" id="'.$each->url.'">Copy Link</p></li>
                    <a  href="/download-file?name='.$each->file.'" class="dropdown-item" style="padding:11px 20px;"><i class="bi bi-download"></i> <p>Download</p></a>
                    <li class="dropdown-item delete" id="'.$each->id.'"><i class="bi bi-share delete" id="'.$each->id.'"></i> <p class="delete" id="'.$each->id.'">Share</p></li>
                    <li class="dropdown-item delete"  id="'.$each->id.'"><i class="bi bi-trash delete" id="'.$each->id.'"></i> <p class="delete" id="'.$each->id.'">Delete</p></li>
                </ul> 
            </div>';
            }         
            return $drop_icon;
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
