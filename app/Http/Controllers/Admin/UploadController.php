<?php

namespace App\Http\Controllers\Admin;

use App\Models\File;
use App\Models\SubFolder;
use App\Models\MainFolder;
use App\Models\UploadFile;
use Illuminate\Http\Request;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
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
        $size =Crypt::encryptString($formattedSize);
        if($file){
            $name = $file->getClientOriginalName();
            $fileName = uniqid(). "_" .uniqid() . ".".$file->getClientOriginalExtension();
            $type = $file->getClientOriginalExtension();
            Storage::disk('public')->put('backend/admin/fileUploads/'.$fileName, file_get_contents($file));
        }
        $url = asset('/storage/backend/admin/fileUploads/'.$fileName);
        $files = [
            "name" =>$name,
            "user_id"=>$id,
            "size"=>$size,
            "type"=>$type,
            "file"=>$fileName,
            "url"=>Crypt::encryptString($url)
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

    public function delete(){
        $data = UploadFile::where('file',request()->getContent())->first();
        if($data){
            $data->delete();
            Storage::disk('public')->delete('backend/admin/fileUploads/'.$data->file);
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
            if(!is_dir($dir)){
                mkdir($dir,0777,true);
            } 
            move_uploaded_file($_FILES['folder']['tmp_name'][$index],$dir.$name);
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
        $this->listFolderFiles(public_path($parent[0]),$main_folder->id);
        return "Success";
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
                    $file = new File();   
                    $file->name = $folder;
                    $file->main_folder_id = $main_id??null;
                    $file->sub_folder_id = $sub_id??null;
                    $file->save();
                }
            }
        }
}
}
