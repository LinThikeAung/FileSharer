<template>
  <file-pond 
    name="file"
    ref="pond"
    v-on:initfile="handleFilePondInit"
    v-on:warning="handleFilePond"
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
            showError : false
        }
    },
    methods : {
        handleFilePond(){
            this.showError = true;
        },
        handleFilePondInit(){
            this.showError = false;
        }
    }
}
</script>
<style scoped>
.filepond--root {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial,
        sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol';
}
</style>