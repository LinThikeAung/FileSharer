@extends('layouts.admin')
@section('upload-list-active','side-active')
@section('title','Upload Lists')
@section('content')
<div class="content-wrapper">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="/upload-list">Upload Lists</a></li>
          <li class="breadcrumb-item">{{ $main->name }}</li>
        </ol>
    </nav>
    <div class="card shadow">
        <div class="card-body p-2">
            <table class="table table-hover" id="table" style="width: 100%">
                <thead>
                  <tr>
                    <th scope="col" class="pl-3">Name</th>
                    <th scope="col" class="pl-3">Size</th>
                    <th scope="col" class="pl-3">Type</th>
                    <th scope="col" class="pl-3">Uploaded Time</th>
                    <th scope="col" class="pl-3">Action</th>
                  </tr>
                </thead>
                <tbody>
                    @if (count($folders) > 0)
                       @foreach ($folders as $item)
                            <tr data-id="{{ $item->id }}">
                                <td>
                                        @if ($item->type == 'folder')
                                            <img src="{{ asset('/backend/images/folder_icon.png') }}" class="mr-3"/> 
                                            <span>{{ $item->name }}</span>
                                        @endif
                                </td>
                                <td>
                                        {{ $item->size??"-" }}
                                </td>
                                <td>
                                        {{ $item->type }}
                                </td>
                                <td>
                                        {{ $item->created_at->format('d-m-Y h:i A') }}
                                </td>
                                <td>
                                    <div class="btn-group dropstart">
                                        <button type="button" class="dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li class="dropdown-item copy" id="{{ $item->url }}"><i class="bi bi-link copy" id="{{ $item->url }}"></i> <p class="copy" id="{{ $item->url }}">Copy Link</p></li>
                                            <a  href="/upload-subFolder-zip?fileName={{ $item->name }}" class="dropdown-item" style="padding:11px 20px;"><i class="bi bi-download"></i> <p>Download</p></a>
                                            <li class="dropdown-item delete"><i class="bi bi-share delete"></i> <p class="delete">Share</p></li>
                                            <li class="dropdown-item delete_folder" id="{{ $item->name }}"><i class="bi bi-trash delete_folder" id="{{ $item->name }}"></i> <p class="delete_folder" id="{{ $item->name }}">Delete</p></li>
                                        </ul> 
                                    </div>
                                </td>
                            </tr>
                       @endforeach
                    @endif
                    @if (count($files) > 0)
                    @foreach ($files as $item)
                         <tr>
                             <td>
                                @if ($item->type == 'png' || $item->type == 'jpg' || $item->type == 'svg' || $item->type == 'jpeg' || $item->type == 'gif'  || $item->type == 'webp' || $item->type == 'com_wallpaper' )
                                    <img src="{{ asset('/backend/images/image.png') }}" class="mr-3"/> 
                                    <span>{{ $item->name }}</span>
                                @endif
                                @if ($item->type == 'mp4' || $item->type == 'mov')
                                    <img src="{{ asset('/backend/images/video.png') }}" class="mr-3"/> 
                                    <span>{{ $item->name }}</span>
                                @endif
                                @if ($item->type == 'pdf')
                                    <img src="{{ asset('/backend/images/pdf.png') }}" class="mr-3"/> 
                                    <span>{{ $item->name }}</span>
                                @endif
                                @if ($item->type == 'sql')
                                    <img src="{{ asset('/backend/images/sql.png') }}" class="mr-3"/> 
                                    <span>{{ $item->name }}</span>
                                @endif
                                @if ($item->type == 'txt')
                                    <img src="{{ asset('/backend/images/txt.png') }}" class="mr-3"/> 
                                    <span>{{ $item->name }}</span>
                                @endif
                                @if ($item->type == 'zip')
                                    <img src="{{ asset('/backend/images/zip.png') }}" class="mr-3"/> 
                                    <span>{{ $item->name }}</span>
                                @endif
                            </td>
                             <td>{{ $item->size }}</td>
                             <td>{{ $item->type }}</td>
                             <td>{{ $item->created_at->format('d-m-Y h:i A') }}</td>
                             <td>
                                <div class="btn-group dropstart">
                                    <button type="button" class="dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li class="dropdown-item copy_file" id="{{ $item->url }}"><i class="bi bi-link copy_file" id="{{ $item->url }}"></i> <p class="copy_file" id="{{ $item->url }}">Copy Link</p></li>
                                        <a  href="/download-subFile?name={{ $item->url }}" class="dropdown-item" style="padding:11px 20px;"><i class="bi bi-download"></i> <p>Download</p></a>
                                        <li class="dropdown-item "><i class="bi bi-share"></i> <p >Share</p></li>
                                        <li class="dropdown-item delete" id="{{ $item->url }}"><i class="bi bi-trash delete" id="{{ $item->url }}"></i> <p class="delete" id="{{ $item->url }}">Delete</p></li>
                                    </ul> 
                                </div>
                             </td>
                         </tr>
                    @endforeach
                 @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script>
     $(document).ready(function(){
        $(document.getElementById('table')).on('dblclick', 'tr', function(event) {
               let id = $(this).data('id');
               if(id){
                window.location.replace(`/upload-list/folders/sub-folders/${id}`);
               }
        }); 
       })

    $('#table').on('click', '.copy', (event) => {
        let value = event.target.id;
        let input = document.createElement("input");
        input.value = value;
        document.body.appendChild(input);
        input.select();
        if(document.execCommand('copy')) {
            document.body.removeChild(input);
            swal({
                text: "copied!",
                buttons : false,
                timer : 1000
            });
        }
    });

    $('#table').on('click', '.copy_file', (event) => {
        let value = event.target.id;
        let input = document.createElement("input");
        input.value = value;
        document.body.appendChild(input);
        input.select();
        if(document.execCommand('copy')) {
            document.body.removeChild(input);
            swal({
                text: "copied!",
                buttons : false,
                timer : 1000
            });
        }
    });

    $('#table').on('click', '.delete_folder', (event) => {
        let value = event.target.id;
        Swal.fire({
        title: 'Are you sure?',
        text: "you want to delete!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: 'red',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!',
        reverseButtons : true,
        focusConfirm : false
        }).then((result) => {
        if (result.isConfirmed) {
           axios.post(`/delete-subFolder?fileName=${value}`)
           .then(response=>{
                if(response.data.status == 'success'){
                   window.location.reload();
                }
           })
           .catch(console.error());
        }
    })

    $('#datatable').on('click', '.delete', (event) => {
        let value = event.target.id;
        Swal.fire({
        title: 'Are you sure?',
        text: "you want to delete!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: 'red',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!',
        reverseButtons : true,
        focusConfirm : false
        }).then((result) => {
        if (result.isConfirmed) {
           axios.post(`/upload-subFile-delete?fileName=${value}`)
           .then(response=>{
                if(response.data.status == 'success'){
                    window.location.reload();
                }
           })
           .catch(console.error());
        }
        })
    });
});
</script>    
@endsection