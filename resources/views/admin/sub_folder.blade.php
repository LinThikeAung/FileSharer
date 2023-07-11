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
            <table class="table table-hover" id="table">
                <thead>
                  <tr>
                    <th scope="col" class="pl-3">Name</th>
                    <th scope="col" class="pl-3">Size</th>
                    <th scope="col" class="pl-3">Type</th>
                    <th scope="col" class="pl-3">Uploaded Time</th>
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
                            </tr>
                       @endforeach
                    @endif
                    @if (count($files) > 0)
                    @foreach ($files as $item)
                         <tr>
                             <td>
                                @if ($item->type == 'png' || $item->type == 'jpg' || $item->type == 'svg' || $item->type == 'jpeg' || $item->type == 'gif')
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
<script>
     $(document).ready(function(){
        $(document.getElementById('table')).on('dblclick', 'tr', function(event) {
               let id = $(this).data('id');
               if(id){
                window.location.replace(`/upload-list/folders/sub-folders/${id}`);
               }
        }); 
       })
</script>
@endsection