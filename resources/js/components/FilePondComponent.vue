<template>
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
                v-on:processfileprogress="handleFilePondProcessFile"
                v-on:processfile="stopFileClose"
                v-on:processfileabort="onProcessfileabort"
                allowMultiple = true
                :server="{
                    url : '',
                    process : {
                        url : '/upload',
                        method : 'POST'
                    },
                    revert : {
                        url : '/upload-delete',
                        method : 'POST'
                    },
                    headers : {
                            'X-CSRF-TOKEN' : this.token
                    },
                }"
                />
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
            token  : csrf_token,
            showClose : false
        }
    },
    methods : {
        onCloseFilePond(){
            this.$emit('close-file-pond')
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
    }
}
</script>
<style scoped>
.filepond--root {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial,
        sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol';
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

.modal-body{
    max-height: 400px !important;
    overflow: auto;
}
</style>