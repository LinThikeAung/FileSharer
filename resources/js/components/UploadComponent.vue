<template>
    <input type="file" name="folder" webkitdirectory directory multiple @change="onChangeEvent">
</template>

<script>
import axios from 'axios';
// import { filter, map } from 'lodash';
export default {
    data(){
        return {
            files : [],
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