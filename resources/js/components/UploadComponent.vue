<template>
    <input type="file" name="folder" webkitdirectory directory multiple @change="onChangeEvent">
    <div class="progress-bar-container">
      <div class="progress-bar" :style="{ width: uploadProgress + '%' }">{{ uploadProgress }}%</div>
    </div>
</template>

<script>
import axios from 'axios';
// import { filter, map } from 'lodash';
export default {
    data(){
        return {
            files : [],
            uploadProgress: 0,
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