<?php

namespace App\Http\Controllers\Admin;

use ZipArchive;
use App\Models\File;
use App\Models\User;
use App\Models\SubFolder;
use App\Models\MainFolder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use App\Repositories\FileUploadRepository;
use Illuminate\Support\Facades\File as Facade;
use Illuminate\Support\Facades\File as FacadeFile;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
class UploadController extends Controller
{
    protected $file_upload;
    public function __construct(FileUploadRepository $file_upload)
    {
        $this->middleware('auth');
        $this->file_upload = $file_upload;
    }

    public function index(){
        $user = auth()->user();
        return view('admin.upload',compact('user'));
    }

    public function store(Request $request){
        $files = [];
        $id = auth()->id();
        $file = $request->file('file');
        $fileSize = $file->getSize();
        $formattedSize = $this->formatFileSize($fileSize);
        $size =$formattedSize;
        $unique_name = $file->getClientOriginalName();
        if($file){
            $fileName = uniqid(). "_" .uniqid() . ".".$file->getClientOriginalExtension();
            $type = $file->getClientOriginalExtension();
            $name = auth()->user()->id;
            Storage::disk('chitmaymay')->put(date('Y').'/'.date('m').'/'.date('d').'/'."$name/".$fileName, file_get_contents($file));
        }
        $path = "$name/".$fileName;
        $url = Storage::disk('chitmaymay')->url(date('Y').'/'.date('m').'/'.date('d').'/'.$path);
        $files = [
            "name" =>$unique_name,
            "user_id"=>$id,
            "size"=>$size,
            "type"=>$type,
            "file"=>$fileName,
            "url"=>$url
        ];
        $this->file_upload->storeFile($files);
        return $fileName;
    }

    private function formatFileSize($bytes)
    {
        $sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
        if ($bytes === 0) return '0 Byte';
        $i = floor(log($bytes, 1024));
        return round($bytes / pow(1024, $i)).' '. $sizes[$i];
    }

    public function upload(Request $request){ 
        $fileName = MainFolder::firstWhere('name',$request->fileName);
        if($fileName){
            return response()->json([
                'status'=>'success',
                'name'=>$fileName->name
            ]);
        }else{
            return response()->json([
                'status'=> 'fail'
            ]);
        }
    } 

    public function uploadExist(Request $request){
        $fileName = MainFolder::where('id',$request->file_id)->where('name',$request->file_name)->first();
        $folderArray = [];
        if($fileName){
            $sub_folder = SubFolder::where('parent_id',$fileName->id)->get('name');
            $folders = $sub_folder->toArray();
            foreach($folders as $folder){
                $folderArray[] = $folder['name'];
            }
            if (in_array($request->fileName, $folderArray)) {
                return response()->json([
                    'status'=>'success',
                ]);
            } else {
                return response()->json([
                    'status'=> 'fail'
                ]);
            }
            //  if($sub_folder){
            //     return response()->json([
            //         'status'=>'success',
            //     ]);
            // }else{
            //     return response()->json([
            //         'status'=> 'fail'
            //     ]);
            // }
        }
    }

    public function delete(){
        $data = MainFolder::where('file',request()->getContent())->first();
        $real_time = $data->created_at->format('Y-m-d');
        $array = explode('-',$real_time);
        $year = $array[0];
        $month = $array[1];
        $day = $array[2];
        if($data){
            $data->delete();
            $name = auth()->user()->id;
            Storage::disk('chitmaymay')->delete($year.'/'.$month.'/'.$day.'/'."$name/".$data->file);
            return response()->json([
                'status'=>'success',
                'message'=>'Successfully Created'
            ]);
        }
    }

    public function uploadList(){
        $user = auth()->user();
        return view('admin.upload_list',compact('user'));
    }

    public function uploadData(){
        return  $this->file_upload->getAllFiles();
    }

    public function getType(){
        $files = $this->file_upload->getTypeData();
        return response()->json([
            'data'=>$files,
            'success'=>'Successfully Submitted'
        ]);
    }

    public function test(Request $request){
        $folders = array_combine($_FILES['folder']['full_path'],$_FILES['folder']['name']);
        $index   = 0;
        $dirs = [];
        $parent = [];
        $filename = [];
        $file_size = 0;
        foreach($folders as $path=>$name)
        {
            $dir = dirname($path).'/';
            Storage::disk('chitmaymay')->put(date('Y').'/'.date('m').'/'.date('d').'/'.auth()->id().'/'.$dir.$name,file_get_contents($_FILES['folder']['tmp_name'][$index]));
            $file_size += $_FILES['folder']['size'][$index];
            $index++;
            $parent = explode('/',$dir);
            $sub = ltrim($dir,$parent[0]);
            $dirs[] = $parent[0].$sub.$name;
            $filename[] = $name;
        }
        $size =$this->formatFileSize($file_size);
        $main_folder = new MainFolder();
        $main_folder->user_id = auth()->id();
        $main_folder->name = $parent[0];
        $main_folder->type = "folder";
        $main_folder->size = $size;
        $main_folder->save();
        MainFolder::updateOrCreate(
            [
                'id'=>$main_folder->id
            ],
            [
                'url'=>env('APP_URL').'/upload-list/folders/'.$main_folder->id
            ]
        );
        $path = Storage::disk('chitmaymay')->path(date('Y').'/'.date('m').'/'.date('d').'/'.auth()->id().'/'.$parent[0]);
        $path = str_replace("\\", "/", $path);
        $this->listFolderFiles($path,$main_folder->id,null,$main_folder->id);
        return response()->json([
            'status'=>'success'
        ]);
    }

    public function listFolderFiles($dir,$main_id,$sub_id = null,$main){
        $directory = scandir($dir);
        foreach($directory as $folder){
            if($folder != '.' && $folder != '..'){
                if(is_dir($dir.'/'.$folder)){
                    $folderPath = $dir.'/'.$folder;
                    $file_size = 0;
                    $files =  FacadeFile::allFiles($folderPath);
                    foreach($files as $file){
                        $file_size += $file->getSize();
                    }
                    $size =$this->formatFileSize($file_size);
                    $array = explode('/',$dir);
                    $real_path = implode('/',$array);
                    $sub_folder = new SubFolder();
                    $sub_folder->main_id = $main;
                    $sub_folder->parent_id = $main_id??null;
                    $sub_folder->main_sub_id = $sub_id??null;
                    $sub_folder->name = $folder;
                    $sub_folder->type = 'folder';
                    $sub_folder->path = $real_path;
                    $sub_folder->size = $size;
                    $sub_folder->save();
                    SubFolder::updateOrCreate(
                        [
                            'id'=>$sub_folder->id
                        ],
                        [
                            'url' => env('APP_URL').'/upload-list/folders/sub-folders/'.$sub_folder->id
                        ]
                    );
                    $this->listFolderFiles($dir.'/'.$folder,null,$sub_folder->id,$sub_folder->main_id);
                }else{
                    $array = $_FILES['folder']['name'];
                    $index = null;
                    foreach ($array as $key => $value) {
                        if($value == $folder){
                            $index = $key;
                            break;
                        }
                    }
                    $url =  Storage::disk('chitmaymay')->url(date('Y').'/'.date('m').'/'.date('d').'/'.auth()->id().'/'.$_FILES['folder']['full_path'][$index]);
                    $size =$this->formatFileSize($_FILES['folder']['size'][$index]);
                    $edit_type = explode('.',$folder);
                    $count = count($edit_type)-1;
                    $type = $edit_type[$count];
                    $file = new File();   
                    $file->name = $folder;
                    $file->url = $url;
                    $file->file = $folder;
                    $file->size = $size;
                    $file->type = $type;
                    $file->main_folder_id = $main_id??null;
                    $file->sub_folder_id = $sub_id??null;
                    $file->save();
                }
            }
        }
    }
    public function uploadOption(Request $request){
        $file = MainFolder::firstWhere('name',$request->fileName);
        $real_time = $file->created_at->format('Y-m-d');
        $array = explode('-',$real_time);
        $year = $array[0];
        $month = $array[1];
        $day = $array[2];
        if($file){
                FacadeFile::deleteDirectory(public_path('storage/media/dkmads-upload/'.$year.'/'.$month.'/'.$day.'/'.auth()->id().'/'.$file->name));           
                $file->delete();     
                return response()->json([
                    'status'=>'success'
                ]);
        }
    }

    public function deleteSubFolder(){
        $file = SubFolder::firstWhere('id',request()->fileName);
        if($file){
            $sub_folder = SubFolder::where('main_sub_id',$file->id)->delete();
            $array = explode(",",$file->path);
            $name = implode('/',$array);
            // dd($name);
            FacadeFile::deleteDirectory(public_path('storage/'.$name.'/'.$file->name));                
            $file->delete();
            return response()->json([
                'status'=>'success'
            ]);
        }
    }

    public function uploadOptionBoth(Request $request){
        $file = MainFolder::where('name',$request->fileName)->get();
        if($file){
            $count = count($file);
            return response()->json([
                'status'=>'success',
                'data'=>$request->fileName ." " . "(" . $count .")"
            ]);
        }
    }

  public function getFolder($id){ 
    $main = MainFolder::firstWhere('id',$id);
    $id = $main->id;
    $name = $main->name;
    $array = [];
    $array[] = $main;
    $folders = SubFolder::where('parent_id',$id)->get();
    $files = File::where('main_folder_id',$id)->get();
    $user = auth()->user();
    return view('admin.sub_folder',compact('array','folders','files','user','id','name'));
  }

  public function getSubFolder($id){
    $unique = [];
    $main = SubFolder::firstWhere('id',$id);
    $id = $main->id;
    $name = $main->name;
    $data = $main->toArray();
    $init_array = explode('/',$main->path);
    $splice_array = array_splice($init_array,7);
    $main_folder = MainFolder::whereIn('name',$splice_array)->get();
    $change_array = $main_folder->toArray();
    array_push($unique,$change_array[0]);
    $sub_folder = SubFolder::whereIn('name',$splice_array)->get();
    $edit_array = $sub_folder->toArray();
    if(count($edit_array) > 0){
        foreach($edit_array as $item){
            array_push($unique,$item);
        }
    }
    array_push($unique,$data);
    $collection = collect($unique);
    $array = $collection->unique();
    $folders = SubFolder::where('main_sub_id',$id)->get();
    $files = File::where('sub_folder_id',$id)->get();
    $user = auth()->user();
    return view('admin.sub_folder',compact('array','folders','files','user','id','name'));
  }

  public function uploadZip(){
    $main_folder = MainFolder::firstWhere('name',request()->fileName);
    $real_time = $main_folder->created_at->format('Y-m-d');
    $array = explode('-',$real_time);
    $year = $array[0];
    $month = $array[1];
    $day = $array[2];
    $folderPath = '/media/dkmads-upload/'.$year.'/'.$month.'/'.$day.'/'.auth()->id().'/'.request()->fileName; 
        $zipFileName = request()->fileName.'.zip';
        $zip = new ZipArchive();

        if ($zip->open(public_path('storage/'.$zipFileName), ZipArchive::CREATE | ZipArchive::OVERWRITE)) {
	    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($folderPath));
	   while ($iterator->valid()) {
            if (!$iterator->isDot()) {
                $filePath = $iterator->getPathName();
                $relativePath = substr($filePath, strlen($folderPath) + 1);
                if (!$iterator->isDir()) {
                    $zip->addFile($filePath, $relativePath);
                } else {
                    if ($relativePath !== false) {
                        $zip->addEmptyDir($relativePath);
                    }
                }
            }
            $iterator->next();
        }
            $zip->close();
        }
	$fileurl = public_path('storage/'.$zipFileName);
	if (file_exists($fileurl)) {
    	return Response::download($fileurl, $zipFileName, array('Content-Type: application/zip','Content-Length: '. filesize($fileurl)))->deleteFileAfterSend(true);
	} else {
    	return ['status'=>'zip file does not exist'];
	}	
  }

  public function uploadSubFolderZip(){
    $sub_folder = SubFolder::firstWhere('name',request()->fileName);
    $array = explode("/",$sub_folder->path);
    $new_name = implode('/',$array);
    $folderPath = $new_name.'/'.request()->fileName; // Specify the path of the folder you want to download
    $edit_folderPath = explode("/",$folderPath);
    $count = count(array_slice($edit_folderPath,1));
    $zipFileName = request()->fileName.'.zip';
    $zip = new ZipArchive();

    if ($zip->open(public_path('storage/'.$zipFileName), ZipArchive::CREATE | ZipArchive::OVERWRITE)) {
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($folderPath));
	   while ($iterator->valid()) {
            if (!$iterator->isDot()) {
                $filePath = $iterator->getPathName();
                $relativePath = substr($filePath, strlen($folderPath) + 1);
                if (!$iterator->isDir()) {
                    $zip->addFile($filePath, $relativePath);
                } else {
                    if ($relativePath !== false) {
                        $zip->addEmptyDir($relativePath);
                    }
                }
            }
            $iterator->next();
        }
            $zip->close();
        }
	$fileurl = public_path('storage/'.$zipFileName);
	if (file_exists($fileurl)) {
    		return Response::download($fileurl, $zipFileName, array('Content-Type: application/zip','Content-Length: '. filesize($fileurl)))->deleteFileAfterSend(true);
	} else {
    		return ['status'=>'zip file does not exist'];
	}	
  }

  public function download(){
    $main_folder = MainFolder::firstWhere('file',request()->name);
    $real_time = $main_folder->created_at->format('Y-m-d');
    $array = explode('-',$real_time);
    $year = $array[0];
    $month = $array[1];
    $day = $array[2];
    return response()->download(public_path('storage/media/dkmads-upload/'.$year.'/'.$month.'/'.$day.'/'.auth()->user()->id.'/'.request()->name));
  }

  public function downloadSubFile(){
    $sub_file = request()->name;
    $array = explode('//',$sub_file);
    $new_array = array_slice($array,1);
    $path = explode('/',$new_array[0]);
    $new_path = array_slice($path,1);
    $real_path = implode('/',$new_path);
    return Response::download(public_path($real_path));
  }

  public function deleteFile(){
        $file = MainFolder::firstWhere('id',request()->fileName);
        $real_time = $file->created_at->format('Y-m-d');
        $array = explode('-',$real_time);
        $year = $array[0];
        $month = $array[1];
        $day = $array[2];
        FacadeFile::delete(public_path('storage/media/dkmads-upload/'.$year.'/'.$month.'/'.$day.'/'.auth()->user()->id.'/'.$file->file));
        $file->delete();
        return response()->json([
            'status'=>'success'
        ]);
  }

  public function subFileDelete(){
        $file = File::firstWhere('id',request()->fileName);
        $array = explode('/',$file->url);
        $new_array = array_slice($array,3);
        $path = implode('/',$new_array);
        FacadeFile::delete(public_path($path));
        $file->delete();
        return response()->json([
            'status'=>'success'
        ]);
  }

  public function getUser(){
        $id = auth()->id();
        $users = User::whereNotIn('id', [$id])->get();
        return response()->json([
            'data'=>$users
        ]);
    }

    public function deleteConfirm(){
        if(request()->input == ""){
            return response()->json([
                'status'=>'fail',
                'message'=>'Please filled in the box'
            ]);
        }

        if(request()->input != request()->message){
            return response()->json([
                'status'=>'fail',
                'message'=>'Your text is wrong'
            ]);
        }

        return response()->json([
            'status'=>'success',
            'message'=>'success'
        ]);
    }

  public function FileUpload(Request $request)
  {
    $main_folder = MainFolder::where('id',$request->file_id)->where('name',$request->file_name)->first();
    $main_folder_id = null;
    $sub_folder_id = null;
    $folder_name = null;
    if($main_folder){
       $main_folder_id = $main_folder->id;
       $folder_name = $main_folder->name;
    }else{
        $sub_folder = SubFolder::where('id',$request->file_id)->where('name',$request->file_name)->first();
        $sub_folder_id = $sub_folder->id;
        $sub_folder_split = explode('/',$sub_folder->path);
        $folder_name = implode('/',array_slice($sub_folder_split,7))."/".$sub_folder->name;
    }
        $files = [];
        $id = auth()->id();
        $file = $request->file('file');
        $fileSize = $file->getSize();
        $formattedSize = $this->formatFileSize($fileSize);
        $size =$formattedSize;
        $unique_name = $file->getClientOriginalName();
        if($file){
            $fileName = uniqid(). "_" .uniqid() . ".".$file->getClientOriginalExtension();
            $type = $file->getClientOriginalExtension();
            $name = auth()->user()->id;
            Storage::disk('chitmaymay')->put(date('Y').'/'.date('m').'/'.date('d').'/'."$name/$folder_name/".$fileName, file_get_contents($file));
        }
        $path = "$name/$folder_name/".$fileName;
        $url = Storage::disk('chitmaymay')->url(date('Y').'/'.date('m').'/'.date('d').'/'.$path);

        $file_upload = new File();
        $file_upload->name =$unique_name;
        $file_upload->file = $fileName;
        $file_upload->url = $url;
        $file_upload->size = $size;
        $file_upload->type = $type;
        $file_upload->main_folder_id = $main_folder_id;
        $file_upload->sub_folder_id = $sub_folder_id;
        $file_upload->save();
        return $fileName;
    }

    public function deleteUploadFolder(Request $request){
        $main_folder = MainFolder::where('id',$request->file_id)->where('name',$request->file_name)->first();
        $folder_name = null;
        if($main_folder){
           $main_folder_id = $main_folder->id;
           $folder_name = $main_folder->name;
        }else{
            $sub_folder = SubFolder::where('id',$request->file_id)->where('name',$request->file_name)->first();
            $sub_folder_split = explode('/',$sub_folder->path);
            $folder_name = implode('/',array_slice($sub_folder_split,7))."/".$sub_folder->name;
        }

        $data = File::where('file',request()->getContent())->first();
        $real_time = $data->created_at->format('Y-m-d');
        $array = explode('-',$real_time);
        $year = $array[0];
        $month = $array[1];
        $day = $array[2];
        if($data){
            $data->delete();
            $name = auth()->user()->id;
            Storage::disk('chitmaymay')->delete($year.'/'.$month.'/'.$day.'/'."$name/$folder_name/".$data->file);
            return response()->json([
                'status'=>'success',
                'message'=>'Successfully Created'
            ]);
        }
    }

    public function SubFolderUpload(Request $request){
        $main_folder = MainFolder::where('id',$request->file_id)->where('name',$request->file_name)->first();
        $folderName      = null;
        $main_folder_id  = null;
        $main_sub_id     = null;
        $main_id         = null;

        if($main_folder)
        {
            $folderName      = $main_folder->name;
            $main_folder_id  = $main_folder->id;
            $main_id         = $main_folder->id; 
        }
        else
        {
            $sub_folder       = SubFolder::where('id',$request->file_id)->where('name',$request->file_name)->first();
            $main_sub_id      = $sub_folder->id;
            $sub_folder_split = explode('/',$sub_folder->path);
            $folderName      = implode('/',array_slice($sub_folder_split,7))."/".$sub_folder->name;
            $main_id          = $sub_folder->main_id;
        }

        $folders   = array_combine($_FILES['folder']['full_path'],$_FILES['folder']['name']);
        $index     = 0;
        $dirs      = [];
        $parent    = [];
        $filename  = [];
        $file_size = 0;
        foreach($folders as $path=>$name)
        {
            $dir = dirname($path).'/';
            Storage::disk('chitmaymay')->put(date('Y').'/'.date('m').'/'.date('d').'/'.auth()->id().'/'.$folderName.'/'.$dir.$name,file_get_contents($_FILES['folder']['tmp_name'][$index]));
            $file_size += $_FILES['folder']['size'][$index];
            $index++;
            $parent     = explode('/',$dir);
            $sub        = ltrim($dir,$parent[0]);
            $dirs[]     = $parent[0].$sub.$name;
            $filename[] = $name;
        }
        
        $size                    = $this->formatFileSize($file_size);
        $filePath                = Storage::disk('chitmaymay')->path(date('Y').'/'.date('m').'/'.date('d').'/'.auth()->id().'/'.$folderName);
        $filePath                = str_replace("\\", "/", $filePath);
        $sub_folder              = new SubFolder();
        $sub_folder->parent_id   = $main_folder_id;
        $sub_folder->main_sub_id = $main_sub_id;
        $sub_folder->name        = $parent[0]; 
        $sub_folder->type        = "folder";
        $sub_folder->size        = $size; 
        $sub_folder->path        = $filePath;
        $sub_folder->main_id     = $main_id;
        $sub_folder->save();
        SubFolder::updateOrCreate(
            [
                'id'=>$sub_folder->id
            ],
            [
                'url'=>env('APP_URL').'/upload-list/folders/sub-folders/'.$sub_folder->id
            ]
        );
        $path = Storage::disk('chitmaymay')->path(date('Y').'/'.date('m').'/'.date('d').'/'.auth()->id().'/'.$folderName.'/'.$parent[0]);
        $path = str_replace("\\", "/", $path);
        $this->listSubFolderFiles($path,null,$sub_folder,$main_id,$folderName);
        return response()->json([
            'status'=>'success'
        ]);
    }

    public function listSubFolderFiles($dir,$main_id,$sub_id = null,$main,$folderName){
        $directory = scandir($dir);
        foreach($directory as $folder){
            if($folder != '.' && $folder != '..'){
                if(is_dir($dir.'/'.$folder)){
                    $folderPath = $dir.'/'.$folder;
                    $file_size = 0;
                    $files =  FacadeFile::allFiles($folderPath);
                    foreach($files as $file){
                        $file_size += $file->getSize();
                    }
                    $size =$this->formatFileSize($file_size);
                    $array = explode('/',$dir);
                    $real_path = implode('/',$array);
                    $sub_folder = new SubFolder();
                    $sub_folder->main_id = $main;
                    $sub_folder->parent_id = $main_id??null;
                    $sub_folder->main_sub_id = $sub_id->id??null;
                    $sub_folder->name = $folder;
                    $sub_folder->type = 'folder';
                    $sub_folder->path = $real_path;
                    $sub_folder->size = $size;
                    $sub_folder->save();
                    SubFolder::updateOrCreate(
                        [
                            'id'=>$sub_folder->id
                        ],
                        [
                            'url' => env('APP_URL').'/upload-list/folders/sub-folders/'.$sub_folder->id
                        ]
                    );
                    $this->listSubFolderFiles($dir.'/'.$folder,null,$sub_folder,$sub_folder->main_id,null);
                }else{
                    $array = $_FILES['folder']['name'];
                    $index = null;
                    foreach ($array as $key => $value) {
                        if($value == $folder){
                            $index = $key;
                            break;
                        }
                    }
                    $url                    =    $sub_id->path;
                    $url                    =    explode('/',$url);
                    $urlArray               =    array_slice($url,3);
                    $fileUrl                =    implode('/',$urlArray)."/".$sub_id->name;
                    $fileUrl                =   Storage::disk('chitmaymay')->url($fileUrl."/".$folder);
                    $size                   =    $this->formatFileSize($_FILES['folder']['size'][$index]);
                    $edit_type              =    explode('.',$folder);
                    $count                  =    count($edit_type)-1;
                    $type                   =    $edit_type[$count];
                    $file                   =   new File();   
                    $file->name             =   $folder;
                    $file->url              =   $fileUrl;
                    $file->file             =   $folder;
                    $file->size             =   $size;
                    $file->type             =   $type;
                    $file->main_folder_id   =   $main_id??null;
                    $file->sub_folder_id    =   $sub_id->id??null;
                    $file->save();
                }
            }
        }
    }
}
