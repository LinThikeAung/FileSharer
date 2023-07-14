<?php

namespace App\Http\Controllers\Admin;

use datatables;
use App\Models\User;
use App\Models\ShareUser;
use App\Models\MainFolder;
use App\Http\Controllers\Controller;

class ShareController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function storeShare(){
        $user = request()->user;
        $array = explode(',',$user);
        $file_id = MainFolder::firstWhere('name',request()->shareName);
        $message = request()->message;
        foreach ($array as $user) {
            $share = new ShareUser();
            $share->share_id = auth()->id();
            $share->other_id = $user;
            $share->main_folder_id = $file_id->id;
            $share->message = $message;
            $share->save();
        }
        return response()->json([
            'status'=>'success'
        ]);
    }

    public function myShare(){
        $user = auth()->user();
        return view('admin.my_share',compact('user'));
    }

    public function uploadData(){
        $id = auth()->id();
        $query = ShareUser::where('share_id',$id)->latest();

        if(request()->name){
            $value = request()->name;
            $query = $query->whereHas('main_folder',function($query) use($value){
                $query->where('name','LIKE','%'.$value.'%');   
            });
        }

        if(request()->type){
            $value = request()->type;
            $query = $query->whereHas('main_folder',function($query) use($value){
                $query->where('type',$value);   
            });
        }

        if(request()->date){
            $date = explode("-",request()->date);
            $from = $date[0];
            $to = $date[1];
            $query = $query->whereDate('created_at','>=',$from)->whereDate('created_at','<=',$to);
        }

        return datatables($query)
        ->addColumn('other',function($each){
            return $each->other_user->name;
        })
        ->addColumn('name',function($each){
            if($each->main_folder->type == 'png' || $each->main_folder->type == 'jpg' || $each->main_folder->type == 'svg' || $each->main_folder->type == 'jpeg' || $each->main_folder->type == 'gif' || $each->main_folder->type == 'webp'){
              return '<img src="'.asset('/backend/images/image.png').'" class="mr-3"/> <span>'.$each->main_folder->name.'</span>';
            }
            if($each->main_folder->type == 'mp4' || $each->main_folder->type == 'mov'){
                return '<img src="'.asset('/backend/images/video.png').'" class="mr-3"/> <span>'.$each->main_folder->name.'</span>';
            }
            if($each->main_folder->type == 'folder'){
                return '<img src="'.asset('/backend/images/folder_icon.png').'" class="mr-3"/> <span>'.$each->main_folder->name.'</span>';
            }

            if($each->main_folder->type == 'pdf'){
                return '<img src="'.asset('/backend/images/pdf.png').'" class="mr-3"/> <span>'.$each->main_folder->name.'</span>';
            }

            if($each->main_folder->type == 'sql'){
                return '<img src="'.asset('/backend/images/sql.png').'" class="mr-3"/> <span>'.$each->main_folder->name.'</span>';
            }

            if($each->main_folder->type == 'txt'){
                return '<img src="'.asset('/backend/images/txt.png').'" class="mr-3"/> <span>'.$each->main_folder->name.'</span>';
            }

            if($each->main_folder->type == 'zip'){
                return '<img src="'.asset('/backend/images/zip.png').'" class="mr-3"/> <span>'.$each->main_folder->name.'</span>';
            }
        })
        ->addColumn('action',function($each){
            if ($each->main_folder->type == 'folder') {
                $drop_icon = '  <div class="btn-group dropstart">
                <button type="button" class="dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-three-dots-vertical"></i>
                </button>
                <ul class="dropdown-menu">
                    <li class="dropdown-item copy" id="'.$each->main_folder->url.'"><i class="bi bi-link copy" id="'.$each->main_folder->url.'"></i> <p class="copy" id="'.$each->main_folder->url.'">Copy Link</p></li>
                    <a href="/upload-zip?fileName='.$each->main_folder->name.'" class="dropdown-item"><i class="bi bi-download download"></i> <p class="download"">Download</p></a>
                    <li class="dropdown-item share" id="'.$each->main_folder->name.'"><i class="bi bi-share share" id="'.$each->main_folder->name.'"></i> <p class="share" id="'.$each->main_folder->name.'">Share</p></li>
                    <li class="dropdown-item delete"  id="'.$each->id.'"><i class="bi bi-trash delete" id="'.$each->id.'"></i> <p class="delete" id="'.$each->id.'">Delete</p></li>
                </ul> 
            </div>';
            }else{
                $drop_icon = '  <div class="btn-group dropstart">
                <button type="button" class="dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-three-dots-vertical"></i>
                </button>
                <ul class="dropdown-menu">
                    <li class="dropdown-item copy" id="'.$each->main_folder->url.'"><i class="bi bi-link copy" id="'.$each->main_folder->url.'"></i> <p class="copy" id="'.$each->main_folder->url.'">Copy Link</p></li>
                    <a  href="/download-file?name='.$each->main_folder->file.'" class="dropdown-item" style="padding:11px 20px;"><i class="bi bi-download"></i> <p>Download</p></a>
                    <li class="dropdown-item share" id="'.$each->main_folder->name.'"><i class="bi bi-share share" id="'.$each->main_folder->name.'"></i> <p class="share" id="'.$each->main_folder->name.'">Share</p></li>
                    <li class="dropdown-item delete"  id="'.$each->id.'"><i class="bi bi-trash delete" id="'.$each->id.'"></i> <p class="delete" id="'.$each->id.'">Delete</p></li>
                </ul> 
            </div>';
            }         
            return $drop_icon;
        })
        ->editColumn('type',function($each){
            return $each->main_folder->type??"-";
        })
        ->editColumn('created_at',function($each){
            return $each->created_at->format('d-m-Y h:i A');
        })
        ->rawColumns(['action','name'])
        ->toJson();
    }

    public function getType(){
        $myshare = ShareUser::where('share_id',auth()->id())->get('main_folder_id');
        $array =$myshare->toArray();
        $files = MainFolder::whereIn('id',$array)->distinct()->get('type');
        return response()->json([
            'data'=>$files,
            'success'=>'Successfully Submitted'
        ]);
    }

    public function deleteFile(){
        $file = ShareUser::firstWhere('id',request()->fileName);
        $file->delete();
        return response()->json([
            'status'=>'success'
        ]);
    }

    public function getUser(){
        $id = auth()->id();
        $fileName = request()->fileName;
        $file = MainFolder::firstWhere('name',$fileName);
        $user = ShareUser::where('share_id',auth()->id())->where('main_folder_id',$file->id)->get('other_id');
        $array = $user->toArray();
        array_push($array,$id);
        $users = User::whereNotIn('id', $array)->get();
        return response()->json([
            'data'=>$users
        ]);
    }

    public function getOtherUser(){
        $id = auth()->id();
        $fileName = request()->fileName;
        $file = MainFolder::firstWhere('name',$fileName);
        $user = ShareUser::where('other_id',auth()->id())->where('main_folder_id',$file->id)->get('share_id');
        $array = $user->toArray();
        array_push($array,$id);
        $users = User::whereNotIn('id', $array)->get();
        return response()->json([
            'data'=>$users
        ]);
    }

    public function otherShare(){
        $user = auth()->user();
        return view('admin.other_share',compact('user'));
    }

    public function otherShareData(){
        $id = auth()->id();
        $query = ShareUser::where('other_id',$id)->latest();

        if(request()->name){
            $value = request()->name;
            $query = $query->whereHas('main_folder',function($query) use($value){
                $query->where('name','LIKE','%'.$value.'%');   
            });
        }

        if(request()->type){
            $value = request()->type;
            $query = $query->whereHas('main_folder',function($query) use($value){
                $query->where('type',$value);   
            });
        }

        if(request()->date){
            $date = explode("-",request()->date);
            $from = $date[0];
            $to = $date[1];
            $query = $query->whereDate('created_at','>=',$from)->whereDate('created_at','<=',$to);
        }

        return datatables($query)
        ->addColumn('share_user',function($each){
            return $each->share_user->name;
        })
        ->addColumn('name',function($each){
            if($each->main_folder->type == 'png' || $each->main_folder->type == 'jpg' || $each->main_folder->type == 'svg' || $each->main_folder->type == 'jpeg' || $each->main_folder->type == 'gif' || $each->main_folder->type == 'webp'){
              return '<img src="'.asset('/backend/images/image.png').'" class="mr-3"/> <span>'.$each->main_folder->name.'</span>';
            }
            if($each->main_folder->type == 'mp4' || $each->main_folder->type == 'mov'){
                return '<img src="'.asset('/backend/images/video.png').'" class="mr-3"/> <span>'.$each->main_folder->name.'</span>';
            }
            if($each->main_folder->type == 'folder'){
                return '<img src="'.asset('/backend/images/folder_icon.png').'" class="mr-3"/> <span>'.$each->main_folder->name.'</span>';
            }

            if($each->main_folder->type == 'pdf'){
                return '<img src="'.asset('/backend/images/pdf.png').'" class="mr-3"/> <span>'.$each->main_folder->name.'</span>';
            }

            if($each->main_folder->type == 'sql'){
                return '<img src="'.asset('/backend/images/sql.png').'" class="mr-3"/> <span>'.$each->main_folder->name.'</span>';
            }

            if($each->main_folder->type == 'txt'){
                return '<img src="'.asset('/backend/images/txt.png').'" class="mr-3"/> <span>'.$each->main_folder->name.'</span>';
            }

            if($each->main_folder->type == 'zip'){
                return '<img src="'.asset('/backend/images/zip.png').'" class="mr-3"/> <span>'.$each->main_folder->name.'</span>';
            }
        })
        ->addColumn('action',function($each){
            if ($each->main_folder->type == 'folder') {
                $drop_icon = '  <div class="btn-group dropstart">
                <button type="button" class="dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-three-dots-vertical"></i>
                </button>
                <ul class="dropdown-menu">
                    <li class="dropdown-item copy" id="'.$each->main_folder->url.'"><i class="bi bi-link copy" id="'.$each->main_folder->url.'"></i> <p class="copy" id="'.$each->main_folder->url.'">Copy Link</p></li>
                    <a href="/upload-zip?fileName='.$each->main_folder->name.'" class="dropdown-item"><i class="bi bi-download download"></i> <p class="download"">Download</p></a>
                    <li class="dropdown-item share" id="'.$each->main_folder->name.'"><i class="bi bi-share share" id="'.$each->main_folder->name.'"></i> <p class="share" id="'.$each->main_folder->name.'">Share</p></li>
                </ul> 
            </div>';
            }else{
                $drop_icon = '  <div class="btn-group dropstart">
                <button type="button" class="dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-three-dots-vertical"></i>
                </button>
                <ul class="dropdown-menu">
                    <li class="dropdown-item copy" id="'.$each->main_folder->url.'"><i class="bi bi-link copy" id="'.$each->main_folder->url.'"></i> <p class="copy" id="'.$each->main_folder->url.'">Copy Link</p></li>
                    <a  href="/download-file?name='.$each->main_folder->file.'" class="dropdown-item" style="padding:11px 20px;"><i class="bi bi-download"></i> <p>Download</p></a>
                    <li class="dropdown-item share" id="'.$each->main_folder->name.'"><i class="bi bi-share share" id="'.$each->main_folder->name.'"></i> <p class="share" id="'.$each->main_folder->name.'">Share</p></li>
                </ul> 
            </div>';
            }         
            return $drop_icon;
        })
        ->editColumn('type',function($each){
            return $each->main_folder->type??"-";
        })
        ->editColumn('created_at',function($each){
            return $each->created_at->format('d-m-Y h:i A');
        })
        ->rawColumns(['action','name'])
        ->toJson();
    }

    public function getOtherShare(){
        $myshare = ShareUser::where('other_id',auth()->id())->get('main_folder_id');
        $array =$myshare->toArray();
        $files = MainFolder::whereIn('id',$array)->distinct()->get('type');
        return response()->json([
            'data'=>$files,
            'success'=>'Successfully Submitted'
        ]);
    }
}
