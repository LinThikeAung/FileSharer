<template>
    <div class="float-right">
        <div class="dropdown dropstart">
            <button class="btn btn-theme " type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-plus"></i> <span>New</span>
            </button>
            <ul class="dropdown-menu mr-1 shadow" style="border-radius: 8px;z-index: 999;">
                <input type="file" hidden ref="fileInput" webkitdirectory directory multiple @change="onChangeEvent" :disabled="uploading">
                <li class="mb-2 text-dark" @click="onFileUpload"><button class="dropdown-item px-3" type="button"><img :src="fileImageUrl" class="image"/><span class="mr-4">File upload</span></button></li>
                <!-- <li class="mb-2" @click="onClickHandler"><button class="dropdown-item px-3" type="button"><img :src="folderImageUrl" class="image"/><span class="mr-4">Folder upload</span></button></li> -->
            </ul>
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
        <div class="modal-backdrop">
            <div class="modal-content shadow">
                <div class="modal-header">
                    <h5 class="modal-title">File Pond</h5>
                    <button type="button" class="btn-close" @click="onCloseFilePond" :disabled="showClose"><i class="bi bi-x"></i></button>
                </div>
                <div class="modal-body">
                    <file-pond 
                        name="file"
                        ref="pond"
                        v-on:initfile="handleFilePondInit"
                        v-on:warning="handleFilePond"
                        v-on:processfileprogress="handleFilePondProcessFile"
                        v-on:processfile="stopFileClose"
                        v-on:processfileabort="onProcessfileabort"
                        allowMultiple = true
                        maxFiles = 5
                        :server="{
                            url : '',
                            process : {
                                url : `/subfolder-upload?file_id=${fileId}&file_name=${fileName}`,
                                method : 'POST'
                            },
                            revert : {
                                url : `/upload-subFolder-delete?file_id=${fileId}&file_name=${fileName}`,
                                method : 'POST'
                            },
                            headers : {
                                    'X-CSRF-TOKEN' : this.token
                            },
                        }"
                    />
                    <p v-if="showError" class="text-danger">The maximum number of files is 5</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
// import vueFilePond from 'vue-filepond';
import vueFilePond from 'vue-filepond';
import 'filepond/dist/filepond.min.css';
import $ from "jquery";
var csrf_token = $('meta[name="csrf-token"]').attr('content');
export default {
    components: {
        FilePond: vueFilePond()
    },
    data(){
        return {
            fileImageUrl: "/backend/images/upload-file.png",
            folderImageUrl : "/backend/images/upload-folder.png",
            showFilePond : false,
            token  : csrf_token,
            showError : false,
            showClose : false,
            fileId : "",
            fileName : "",
        }
    },
    methods:{
        onFileUpload(){
            this.showFilePond = true;
        },
        handleFilePond(){
            this.showError = true;
        },
        handleFilePondInit(){
            this.showError = false;
        },
        onCloseFilePond(){
            this.showFilePond = false;
            window.location.reload();
        },
        handleFilePondProcessFile(){
           this.showClose = true;
        },
        stopFileClose(){
            this.showClose = false;
        },
        onProcessfileabort(){
            this.showClose = false;
        }
    },
    mounted(){
        this.fileId = $('#folder_id').val();
        this.fileName = $('#folder_name').val();
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


.modal-backdrop {
    position: fixed !important;
    left: 0;
    right: 0;
    background-color: rgba(0, 0, 0, 0.3);
    display: flex;
    justify-content: center;
    align-items: center;
  }

.modal-content {
    width: 500px;
    left: 0px !important;
    right: 0px !important;
    margin: 0 auto !important;
    border: none !important;
    transition: opacity 0.3s ease;
}

.btn-close{
    border: 1px solid rgb(35, 35, 35) !important;
    font-weight: bold;
}
</style>