<template>
    <input type="file" name="folder" webkitdirectory directory multiple @change="onChangeEvent">
    <div v-if="showProgress">
        <progress-component :fileName = "fileName" :uploadProgress="uploadProgress"></progress-component>
    </div>
</template>

<script>
import axios from 'axios';
export default {
    data(){
        return {
            files : [],
            uploadProgress: 0,
            showProgress : false,
            fileName : null
        }
    },
    methods:{
        onChangeEvent(event){  
            this.files = event.target.files;   
            let formData = new FormData();
            for(var i=0;i < this.files.length;i++)
            {
                formData.append('folder[]',this.files[i]);
            }
            this.showProgress = true;
            let file = event.target.files[0].webkitRelativePath;
            this.fileName = file.split('/')[0];
            axios.post('/reset',formData,{
                    headers : {
                        'Content-Type': 'multipart/form-data'
                    },
                        onUploadProgress: (progressEvent) => {
                        this.uploadProgress = Math.round(
                            (progressEvent.loaded / progressEvent.total) * 100
                        );
                    },
            })
            .then(response=>{
                console.log(response);
            })
            .catch(console.error());
        },
    }
}
</script>

<style>

</style>