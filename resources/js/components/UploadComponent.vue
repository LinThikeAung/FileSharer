<template>
    <div class="d-flex justify-content-between align-items-center upload-container">
    <h5>Upload Lists</h5>
        <div class="d-flex align-items-center">
            <button class="btn btn-theme mr-2" type="button" aria-expanded="false" @click="showFilter = !showFilter">
                <i class="bi bi-filter"></i> <span>Filter</span>
            </button>
            <div class="dropdown dropstart">
                <button class="btn btn-theme " type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-plus"></i> <span>New</span>
                </button>
                <ul class="dropdown-menu mr-1 shadow" style="border-radius: 8px;z-index: 999;">
                    <input type="file" hidden ref="fileInput" webkitdirectory directory multiple @change="onChangeEvent" :disabled="uploading">
                    <li class="text-dark" @click="onCreateEvent"><button class="dropdown-item px-3" type="button"><img :src="newFolderUrl" class="image"/><span class="mr-4">New Folder</span></button></li>
                    <hr>
                    <li class="mb-2 text-dark" @click="onFileUpload"><button class="dropdown-item px-3" type="button"><img :src="fileImageUrl" class="image"/><span class="mr-4">File upload</span></button></li>
                    <li class="mb-2" @click="onClickHandler"><button class="dropdown-item px-3" type="button"><img :src="folderImageUrl" class="image"/><span class="mr-4">Folder upload</span></button></li>
                </ul>
            </div>
        </div>
    </div>
    <div v-if="showFilter">
        <filter-component @search="searEventHandler" @clear="clearEvent"></filter-component>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card mt-3 mb-3 shadow">
     <div class="card-body p-2">
        <!-- <div class="table-responsive"> -->
            <table class="table table-hover" id="datatable">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Size</th>
                        <th>Type</th>
                        <th>Uploaded Time</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        <!-- </div> -->
    </div>
</div>
        </div>
    </div>
    <div v-if="showProgress">
        <progress-component 
        :fileName = "fileName" 
        :uploadProgress="uploadProgress" 
        :uploading="uploading" 
        :uploadCancel="uploadCancel" 
        :showSuccess="showSuccess"  
        @close-dialoag="onCloseDialoag"
        @close-upload = "onCloseUpload"
        >
        </progress-component>
    </div>
    <div v-if="showOptionComponent">
        <uploadOption-component
        :fileName = this.fileName
        @replace-data = onReplaceData
        @close-upload-option = "closeUploadOption"
        @update-option-both="onUpdateOptionBoth"
        ></uploadOption-component>
    </div>
    <div v-if="showFilePond">
        <file-pond-component
          @close-file-pond = "onCloseFilePond"
        ></file-pond-component>
    </div>
   <div v-if="showShareComponent">
    <share-component :shareName = "shareName" :users="users" @close="closeShareModal"></share-component>
   </div>
   <div v-if="showCreateFolder">
        <create-folder-component @closeCreateFolder="closeCreateFolder"></create-folder-component>
   </div>
</template>

<script>
import axios from 'axios';
import Swal from 'sweetalert2'
import FilePondComponent from './FilePondComponent.vue';
import swal from 'sweetalert';
export default {
  components: { FilePondComponent },
    data(){
        return {
            files : [],
            uploadProgress: 0,
            showProgress : false,
            fileName : null,
            uploadCancelToken : null,
            uploading : false,
            uploadCancel : false,
            showSuccess : false,
            fileImageUrl: "/backend/images/upload-file.png",
            folderImageUrl : "/backend/images/upload-folder.png",
            newFolderUrl : "/backend/images/new-folder.png",
            showOptionComponent : false,
            showFilePond : false,
            folders : [],
            showFilter : false,
            showShareComponent : false,
            shareName : null,
            users : [],
            folderName : "",
            showCreateFolder : false,
        }
    },
    methods:{
        onChangeEvent(event){  
            this.files = [];
            this.files = Array.from(event.target.files);   
            let file = this.files[0].webkitRelativePath;
            this.fileName = file.split('/')[0];
            axios.get(`/upload-exist?fileName=${this.fileName}`)
            .then(response=>{
                    if(response.data.status == 'success'){
                        alert('This folder is already exists');
                        // this.showOptionComponent = true;
                        // this.fileName = response.data.name;
                    }else{
                        this.uploadData();
                    }
            })
            .catch(error=>{
                console.log(error);
            });
            
        },
        async uploadData(){
            let formData = new FormData();
            for(var i=0;i < this.files.length;i++)
            {
                formData.append('folder[]',this.files[i]);
            }
            this.folderName = this.files[0].webkitRelativePath.split('/')[0];
            this.uploadCancel = false;
            this.showProgress = true;
            this.showSuccess = false;
            this.uploadProgress = 0;
            this.uploading = true;
            this.uploadCancelToken = axios.CancelToken.source();
            await axios.post('/reset',formData,{
                    headers : {
                        'Content-Type': 'multipart/form-data'
                    },
                    onUploadProgress: (progressEvent) => {
                        this.uploadProgress = Math.round(
                            (progressEvent.loaded / progressEvent.total) * 100
                        );
                        if(this.uploadProgress === 100){
                            this.uploading = false;
                            this.showSuccess = true;
                            this.$refs.fileInput.value = '';
                        }
                    },
                    cancelToken: this.uploadCancelToken.token
            })
            .then(response=>{
                window.location.reload();
            })
            .catch(error=>{
                if (axios.isCancel(error)) {
                    console.log(error);
                } else {
                    console.log('Error:', error.message);
                }
            });
        },
        onCloseDialoag(){
            Swal.fire({
                title: 'Cancel upload?',
                text: "Your upload is not complete. Would you like to cancel the upload?",
                showCancelButton: true,
                confirmButtonText: 'Cancel Upload',
                cancelButtonText : 'Continue Upload',
                reverseButtons : true,
                focusConfirm : false,
                focusCancel : false
                }).then((result) => {
                if (result.isConfirmed) {
                    this.cancelUpload();
                }
                })  
        },
        cancelUpload(){
            this.showProgress = false;
            if (this.uploadCancelToken) {
                this.uploadCancelToken.cancel('Upload canceled by the user.');
                this.fileName = null;
                this.uploadProgress = 0;
                this.files = [];
                this.$refs.fileInput.value = '';
                this.uploading = false;
            }
        },
        onCloseUpload(){
            this.$refs.fileInput.value = '';
            this.files = [];
            this.showProgress  = false;
            this.uploadCancel = false;
            this.showSuccess = false;
        },
        onClickHandler(){
            this.$refs.fileInput.click();
        },
        closeUploadOption(){
            this.showOptionComponent = false;
            this.fileName = null;
            this.$refs.fileInput.value = '';
            this.files = [];
        },
        onReplaceData(){
            this.showOptionComponent = false;
            this.uploadData();
        },
        onUpdateOptionBoth(value){
            if (!this.files) return;
            for (let i = 0; i < this.files.length; i++) {
                const fileReader = new FileReader();
                fileReader.onload = (event) => {
                const modifiedContent = event.target.result.toLowerCase();
                const modifiedFile = new File([modifiedContent], this.files[i].name, {
                type: this.files[i].type,
                lastModified: this.files[i].lastModified
                });
            }
            }
        },
        getAllFiles(){
            $(document).ready(function(){
                $('#datatable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: '/upload-list/data',
                    columns : [
                        { data : 'name' , name : 'name' },
                        { data : 'size' , name : 'size' },
                        { data : 'type' , name : 'type' },
                        { data : 'created_at' , name : 'created_at' },
                        { data : 'action' , name : 'action'}
                    ],
                    
                });
            })

                $('#datatable').on('click', '.copy', (event) => {
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

                $('#datatable').on('click', '.delete_folder', (event) => {
                    let value = event.target.id;
                    let message = "Delete";
                    Swal.fire({
                        title: `To confirm, type "${message}" in the box below`,
                        input: 'text',
                        inputAttributes: {
                            autocapitalize: 'off'
                        },
                        showCancelButton: false,
                        confirmButtonText: 'Delete this file',
                        focusConfirm : false,
                        preConfirm: (input) => {
                            return axios.get(`/delete-confirm?input=${input}&message=${message}`)
                            .then(response => {
                                if(response.data.status == 'fail'){
                                    Swal.showValidationMessage( 
                                        response.data.message
                                    );
                                }
                                if(response.data.status == 'success'){
                                    Swal.fire({
                                        title: 'Are you sure?',
                                        text: "you want to delete!",
                                        icon: 'warning',
                                        showCancelButton: true,
                                        confirmButtonColor: 'red',
                                        cancelButtonColor: '#d33',
                                        confirmButtonText: 'Yes, delete it!',
                                        reverseButtons : true,
                                        focusConfirm : false,
                                        showLoaderOnConfirm: true,
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            axios.post(`/upload-option-check?fileName=${value}`)
                                            .then(response=>{
                                                    if(response.data.status == 'success'){
                                                        $('#datatable').DataTable().ajax.url('/upload-list/data').load();
                                                    }
                                            })
                                            .catch(console.error());
                                        }
                                    })
                                }
                            })
                            .catch(console.error());
                        },
                    })
                });

            $('#datatable').on('click', '.delete', (event) => {
                let value = event.target.id;
                let message = "Delete";
                Swal.fire({
                    title: `To confirm, type "${message}" in the box below`,
                    input: 'text',
                    inputAttributes: {
                        autocapitalize: 'off'
                    },
                    showCancelButton: false,
                    confirmButtonText: 'Delete this file',
                    focusConfirm : false,
                    preConfirm: (input) => {
                        return axios.get(`/delete-confirm?input=${input}&message=${message}`)
                        .then(response => {
                            if(response.data.status == 'fail'){
                                Swal.showValidationMessage( 
                                    response.data.message
                                );
                            }
                            if(response.data.status == 'success'){
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
                                        axios.post(`/upload-file-delete?fileName=${value}`)
                                        .then(response=>{
                                                if(response.data.status == 'success'){
                                                    $('#datatable').DataTable().ajax.url('/upload-list/data').load();
                                                }
                                        })
                                        .catch(console.error());
                                    }
                                })
                            }
                        })
                        .catch(console.error());
                    },
                })
            })

            $('#datatable').on('click','.share',event=>{
                let fileName = event.target.id;
                this.shareName = fileName;
                axios.get('/getUser')
                .then(response=>{
                    this.users = response.data.data;
                    this.showShareComponent = true;
                })
                .catch(console.error());
                })
        },
        onFileUpload(){
            this.showFilePond = true;
        },
        onCloseFilePond(){
            this.showFilePond = false;
            $('#datatable').DataTable().ajax.url('/upload-list/data').load();
        },
        searEventHandler(option){
            let name = option.name;
            let type = option.type;
            let date = option.date;
            if(option.name){
                document.getElementById('clear_btn').style.display = "block";
            }
            $('#datatable').DataTable().ajax.url(`/upload-list/data?name=${name}&type=${type}&date=${date}`).load();
        },
        clearEvent(data){
            $('#datatable').DataTable().ajax.url(`/upload-list/data?name=${data}`).load();
            document.getElementById('clear_btn').style.display = "none";

        },
        closeShareModal(){
            this.showShareComponent = false;
            window.location.reload();
        },
        onCreateEvent(){
            this.showCreateFolder = true;
        },
        closeCreateFolder(){
            this.showCreateFolder = false;
        }
    },
    mounted(){
        this.getAllFiles();     
       $(document).ready(function(){
        $(document.getElementById('datatable')).on('dblclick', 'tr', function(event) {
            var table  = $(document.getElementById('datatable')).DataTable();
            let rowData = table.row(this).data();
            if(rowData.type == 'folder'){
               window.location.replace(`/upload-list/folders/${rowData.id}`);
            }
            let createElement = document.createElement('a');
            createElement.setAttribute('href',`${rowData.url}`);
            createElement.setAttribute('target','_blank');
            createElement.click();
            }); 
       })
    }
}
</script>

<style scoped>
.btn-theme{
    background-color:white ;
    display: flex;
    align-items: center;
    justify-content: space-between !important;
    box-shadow: rgba(0, 0, 0, 0.24) 0px 3px 8px;
    border-radius: 8px !important;
    padding: 13px 15px;
}

.btn-theme:hover{
    background-color: rgba(212, 217, 242, 0.235);
}
.btn-theme span{
    display: inline-block;
    font-size: 16px;
}
.btn-theme i{
    font-size: 25px;
    font-weight: bold;
    margin-right: 5px;
}

.image{
    width: 18px;
    margin-right: 12px;
}

.card{
    border-radius: 8px !important;
}

</style>