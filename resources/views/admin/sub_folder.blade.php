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
          {{-- <div class="table-responsive"> --}}
            <table class="table table-hover" id="table">
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
                                    <img src='{{ asset("/backend/images/$item->type.png") }}' class="mr-3"/> 
                                    <span>{{ $item->name }}</span>
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
                                    <?php
                                    $backendPath = public_path('backend/images/'.$item->type.".png");
                                   if(File::exists($backendPath)){
                               ?>
                                      <img src='{{ asset("/backend/images/$item->type.png") }}' class="mr-3"/> 
                                      <span>{{ $item->name }}</span>
                               <?php
                                   }else{
                               ?>
                                   <img src='{{ asset("/backend/images/unknown.png") }}' class="mr-3"/> 
                                   <span>{{ $item->name }}</span>
                               <?php
                                   }
                               ?>
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
                                        <li class="dropdown-item file_delete" id="{{ $item->name }}"><i class="bi bi-trash file_delete" id="{{ $item->name }}"></i> <p class="file_delete" id="{{ $item->name }}">Delete</p></li>
                                    </ul> 
                                </div>
                             </td>
                         </tr>
                    @endforeach
                    @else
                    <tr>
                        <td colspan="5" class="text-center">No data available in table</td>
                    </tr>
                    @endif

                </tbody>
            </table>
          {{-- </div> --}}
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

    $('#table').on('click','.file_delete',event=>{
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
    })

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

});
</script>    
@endsection