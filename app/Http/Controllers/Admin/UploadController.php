<?php

namespace App\Http\Controllers\Admin;

use App\Models\File;
use App\Models\MainFile;
use App\Models\SubFolder;
use App\Models\MainFolder;
use App\Models\UploadFile;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use App\Repositories\FileUploadRepository;

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
        if($file){
            $name = $file->getClientOriginalName();
            $fileName = uniqid(). "_" .uniqid() . ".".$file->getClientOriginalExtension();
            $type = $file->getClientOriginalExtension();
            $name = auth()->user()->name;
            Storage::disk('chitmaymay')->put("$name/".$fileName, file_get_contents($file));
        }
        $path = "$name/".$fileName;
        $url = Storage::disk('chitmaymay')->url($path);
        $files = [
            "name" =>$name,
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
    public function delete(){
        $data = MainFile::where('file',request()->getContent())->first();
        if($data){
            $data->delete();
            $name = auth()->user()->name;
            Storage::disk('chitmaymay')->delete("$name/".$data->file);
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
        foreach($folders as $path=>$name)
        {
            $dir = dirname($path).'/';
            Storage::disk('chitmaymay')->put($dir.$name,file_get_contents($_FILES['folder']['tmp_name'][$index]));
            $index++;
            $parent = explode('/',$dir);
            $sub = ltrim($dir,$parent[0]);
            $dirs[] = $parent[0].$sub.$name;
            $filename[] = $name;
        }
        $main_folder = new MainFolder();
        $main_folder->user_id = auth()->id();
        $main_folder->name = $parent[0];
        $main_folder->save();
        $path = Storage::disk('chitmaymay')->path($parent[0]);
        return $this->listFolderFiles($path,$main_folder->id);
        return "Success";
    }

    public function uploadOption(Request $request){
        $file = MainFolder::firstWhere('name',$request->fileName);
        if($file){
                $sub_folder = SubFolder::where('parent_id',$file->id)->get('id');
                $array  = $sub_folder->toArray();
                SubFolder::whereIn('main_sub_id',$array)->delete();
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
    public function listFolderFiles($dir,$main_id,$sub_id = null){
        $directory = scandir($dir);
        foreach($directory as $folder){
            if($folder != '.' && $folder != '..'){
                if(is_dir($dir.'/'.$folder)){
                    $sub_folder = new SubFolder();
                    $sub_folder->parent_id = $main_id??null;
                    $sub_folder->main_sub_id = $sub_id??null;
                    $sub_folder->name = $folder;
                    $sub_folder->save();
                    $this->listFolderFiles($dir.'/'.$folder,null,$sub_folder->id);
                }else{
                    $array = $_FILES['folder']['name'];
                    $index = null;
                    foreach ($array as $key => $value) {
                        if($value == $folder){
                            $index = $key;
                            break;
                        }
                    }
                    $path = $_FILES['folder']['full_path'][$index];
                    $url =  Storage::disk('chitmaymay')->url($path);
                    $size =$this->formatFileSize($_FILES['folder']['size'][$index]);
                    $type = explode('.',$folder)[1];
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
}
