<?php

namespace App\Http\Controllers\Admin;

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
        $name = $request->name;
        $size =Crypt::encryptString($request->size);
        if($request->file('file')){
            $file = $request->file('file');
            $fileName = uniqid(). "_" .uniqid() . ".".$file->getClientOriginalExtension();
            $type = $file->getClientOriginalExtension();
            Storage::disk('public')->put('backend/admin/fileUploads/'.$fileName, file_get_contents($file));
        }
        $url = public_path('storage/backend/admin/fileUploads/'.$fileName);
        $files = [
            "name" =>$name,
            "user_id"=>$id,
            "size"=>$size,
            "type"=>$type,
            "file"=>$fileName,
            "url"=>Crypt::encryptString($url)
        ];
        $this->file_upload->storeFile($files);

        return response()->json([
            'status'=>'success',
            'message'=>'Successfully Created'
        ]);
    }
}
