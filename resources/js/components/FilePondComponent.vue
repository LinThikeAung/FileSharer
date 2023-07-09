<template>
    <div class="modal-content">
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
            v-on:processfilestart="handleFilePondProcessFile"
            v-on:processfile="stopFileClose"
            v-on:processfileabort="onProcessfileabort"
            allowMultiple = true
            maxFiles = 5
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
            <p v-if="showError" class="text-danger">The maximum number of files is 5</p>
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
            showError : false,
            showClose : false
        }
    },
    methods : {
        handleFilePond(){
            this.showError = true;
        },
        handleFilePondInit(){
            this.showError = false;
        },
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

.modal-content {
    width: 500px;
    left: 0px;
    right: 0px;
    margin: 0 auto;
}

.btn-close{
    border: 1px solid rgb(35, 35, 35) !important;
    font-weight: bold;
}
</style>