<template>
    <div class="upload">
        <div class="dropdown dropstart">
            <button class="btn btn-theme " type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-plus"></i> <span>New</span>
            </button>
            <ul class="dropdown-menu mr-1 shadow" style="border-radius: 8px;z-index: 999;">
                <input type="file" hidden ref="fileInput" webkitdirectory directory multiple @change="onChangeEvent">
                <li class="text-dark" @click="onCreateEvent"><button class="dropdown-item px-3" type="button"><img :src="newFolderUrl" class="image"/><span class="mr-4">New Folder</span></button></li>
                    <hr>
                <li class="mb-2 text-dark" @click="onFileUpload"><button class="dropdown-item px-3" type="button"><img
                            :src="fileImageUrl" class="image" /><span class="mr-4">File upload</span></button></li>
                <li class="mb-2" @click="onClickHandler"><button class="dropdown-item px-3" type="button"><img
                            :src="folderImageUrl" class="image" /><span class="mr-4">Folder upload</span></button></li>
            </ul>
        </div>
    </div>
    <div v-if="showProgress">
        <progress-component :fileName="fileName" :uploadProgress="uploadProgress" :uploading="uploading"
            :uploadCancel="uploadCancel" :showSuccess="showSuccess" @close-dialoag="onCloseDialoag"
            @close-upload="onCloseUpload">
        </progress-component>
    </div>
    <div v-if="showFilePond">
        <div class="modal-backdrop">
            <div class="modal-content shadow">
                <div class="modal-header">
                    <h5 class="modal-title">File Pond</h5>
                    <button type="button" class="btn-close" @click="onCloseFilePond" :disabled="showClose"><i
                            class="bi bi-x"></i></button>
                </div>
                <div class="modal-body">
                    <file-pond name="file" ref="pond" 
                        v-on:processfileprogress="handleFilePondProcessFile" v-on:processfile="stopFileClose"
                        v-on:processfileabort="onProcessfileabort" allowMultiple=true :server="{
                            url: '',
                            process: {
                                url: `/subfolder-upload?file_id=${fileId}&file_name=${subFolderName}`,
                                method: 'POST'
                            },
                            revert: {
                                url: `/upload-subFolder-delete?file_id=${fileId}&file_name=${subFolderName}`,
                                method: 'POST'
                            },
                            headers: {
                                'X-CSRF-TOKEN': this.token
                            },
                        }" />
                </div>
            </div>
        </div>
    </div>
    <div v-if="showCreateSubFolder">
        <create-subFolder-component @closeCreateFolder="onCloseCreateFolder" :fileId="fileId" :subFolderName="subFolderName" :time="time"></create-subFolder-component>
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
    data() {
        return {
            fileImageUrl: "/backend/images/upload-file.png",
            folderImageUrl: "/backend/images/upload-folder.png",
            newFolderUrl : "/backend/images/new-folder.png",
            showFilePond: false,
            token: csrf_token,
            showClose: false,
            fileId: "",
            subFolderName: "",
            fileName: "",
            folderName: "",
            files: [],
            uploadCancel: false,
            showProgress: false,
            showSuccess: false,
            uploadProgress: 0,
            uploading: false,
            uploadCancelToken: null,
            showCreateSubFolder : false,
            time : null,
        }
    },
    methods: {
        onFileUpload() {
            this.showFilePond = true;
        },
        onCloseFilePond() {
            this.showFilePond = false;
            window.location.reload();
        },
        handleFilePondProcessFile() {
            this.showClose = true;
        },
        stopFileClose() {
            this.showClose = false;
        },
        onProcessfileabort() {
            this.showClose = false;
        },
        onClickHandler() {
            this.$refs.fileInput.click();
        },
        onChangeEvent(event) {
            this.files = [];
            this.files = Array.from(event.target.files);
            let file = this.files[0].webkitRelativePath;
            this.folderName = file.split('/')[0];
            axios.get(`/upload-subFolder-exist?fileName=${this.folderName}&file_id=${this.fileId}&file_name=${this.subFolderName}&created_at=${this.time}`)
                .then(response => {
                    if (response.data.status == 'success') {
                        Swal.fire({
                            title: response.data.data + ' is already exists.',
                            focusConfirm : false,
                        })
                    } else {
                        this.uploadData();
                    }
                })
                .catch(error => {
                    console.log(error);
                });
        },
        async uploadData() {
            let formData = new FormData();
            for (var i = 0; i < this.files.length; i++) {
                formData.append('folder[]', this.files[i]);
            }
            console.log(this.files);
            this.fileName = this.files[0].webkitRelativePath.split('/')[0];
            this.uploadCancel = false;
            this.showProgress = true;
            this.showSuccess = false;
            this.uploadProgress = 0;
            this.uploading = true;
            this.uploadCancelToken = axios.CancelToken.source();
            await axios.post(`/subFolder-upload?file_id=${this.fileId}&file_name=${this.subFolderName}`, formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                },
                onUploadProgress: (progressEvent) => {
                    this.uploadProgress = Math.round(
                        (progressEvent.loaded / progressEvent.total) * 100
                    );
                    if (this.uploadProgress === 100) {
                        this.uploading = false;
                        this.showSuccess = true;
                        this.$refs.fileInput.value = '';
                    }
                },
                cancelToken: this.uploadCancelToken.token
            })
                .then(response => {
                    if(response.data.status == 'fail'){
                    Swal.fire({
                        title: "You are exceeded",
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.reload();
                        } 
                    })
                    }else{
                        window.location.reload();
                    }
                })
                .catch(error => {
                    if (axios.isCancel(error)) {
                        console.log(error);
                    } else {
                        console.log('Error:', error.message);
                    }
                });
        },
        onCloseDialoag() {
            Swal.fire({
                title: 'Cancel upload?',
                text: "Your upload is not complete. Would you like to cancel the upload?",
                showCancelButton: true,
                confirmButtonText: 'Cancel Upload',
                cancelButtonText: 'Continue Upload',
                reverseButtons: true,
                focusConfirm: false,
                focusCancel: false
            }).then((result) => {
                if (result.isConfirmed) {
                    this.cancelUpload();
                }
            })
        },
        cancelUpload() {
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
        onCreateEvent(){
            this.showCreateSubFolder = true;
        },
        onCloseCreateFolder(){
            this.showCreateSubFolder = false;
        }
    },
    mounted() {
        this.fileId = $('#folder_id').val();
        this.subFolderName = $('#folder_name').val();
        this.time = $('#created_at').val();
    },
}
</script>

<style scoped>
.btn-theme {
    background-color: white;
    display: flex;
    align-items: center;
    justify-content: space-between !important;
    box-shadow: rgba(0, 0, 0, 0.24) 0px 3px 8px;
    border-radius: 8px !important;
    padding: 13px 15px;
}

.btn-theme:hover {
    background-color: rgba(212, 217, 242, 0.235);
}

.btn-theme span {
    display: inline-block;
    font-size: 16px;
}

.btn-theme i {
    font-size: 25px;
    font-weight: bold;
    margin-right: 5px;
}

.image {
    width: 18px;
    margin-right: 12px;
}

.card {
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

.btn-close {
    border: 1px solid rgb(35, 35, 35) !important;
    font-weight: bold;
}

.modal-body{
    max-height: 400px !important;
    overflow: auto;
}
</style>