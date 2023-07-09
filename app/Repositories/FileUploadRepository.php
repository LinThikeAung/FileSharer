<?php

namespace App\Repositories;

use datatables;
use App\Models\MainFile;
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
        return MainFile::create([
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
        $query = UploadFile::where('user_id',$id);

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
        ->addColumn('action',function($each){
            $drop_icon = '  <div class="btn-group dropstart">
                                <button type="button" class="dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li class="dropdown-item"><i class="bi bi-link"></i> <p>Copy Link</p></li>
                                    <li class="dropdown-item"><i class="bi bi-download"></i> <p>Download</p></li>
                                    <li class="dropdown-item"><i class="bi bi-share"></i> <p>Share</p></li>
                                    <li class="dropdown-item"><i class="bi bi-trash"></i> <p>Delete</p></li>
                                </ul>
                            </div>';
            return $drop_icon;
        })
        ->editColumn('size',function($each){
            return Crypt::decryptString($each->size);
        })
        ->editColumn('created_at',function($each){
            return $each->created_at->format('d-m-Y h:i A');
        })
        ->rawColumns(['action'])
        ->toJson();
    }

    public function getTypeData(){
        return UploadFile::distinct()->get('type');
    }
}
