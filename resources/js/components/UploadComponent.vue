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
        <div class="table-responsive ">
            <table class="table table-hover" id="datatable">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Size</th>
                        <th>Type</th>
                        <th>Uploaded Time</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
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
        @update-parent-data="updateParentData"
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
</template>

<script>
import axios from 'axios';
import Swal from 'sweetalert2'
import FilePondComponent from './FilePondComponent.vue';
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
            showOptionComponent : false,
            showFilePond : false,
            folders : [],
            showFilter : false
        }
    },
    methods:{
        onChangeEvent(event){  
            this.files = [];
            this.files = Array.from(event.target.files);   
            if(this.files.length > 50){
                alert('You files less than 50');
            }
            let file = this.files[0].webkitRelativePath;
            this.fileName = file.split('/')[0];
            axios.get(`/upload-exist?fileName=${this.fileName}`)
            .then(response=>{
                    if(response.data.status == 'success'){
                        this.showOptionComponent = true;
                        this.fileName = response.data.name;
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
               if(response.data.status == 'success'){
                $('#datatable').DataTable().ajax.url('/upload-list/data').load();
               }
            })
            .catch(error=>{
                if (axios.isCancel(error)) {
                    console.log(error);
                } else {
                    console.log('Error:', error.message);
                }
            });
        },
        updateParentData(){
            if (this.uploadCancelToken) {
                this.uploadCancelToken.cancel('Upload canceled by the user.');
                this.uploadProgress = 0;
                this.files = [];
                this.$refs.fileInput.value = '';
                this.uploading = false;
                this.uploadCancel = true;
                this.files = [];
            }
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
            console.log(this.files);
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
            console.log(this.files[i]);
            }
            
            // for (let i = 0; i < this.formattedUsers.length; i++) {
            //     let name = this.formattedUsers[i].webkitRelativePath.split('/')[1];
            //     let real_path = value + "/" + name;
            //     this.formattedUsers[i].webkitRelativePath = real_path;
            // }
            // this.files = this.formattedUsers;
            // this.uploadData();
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
                    ],
                    
                });

                // $('#datatable').on('click', '.copy', (event) => {
                //     let value = event.target.id;
                //     let input = document.createElement("input");
                //     input.value = value;
                //     document.body.appendChild(input);
                //     input.select();
                //     if(document.execCommand('copy')) {
                //         document.body.removeChild(input);
                //     }
                // });
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

        }
    },
    mounted(){
        this.getAllFiles();     
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