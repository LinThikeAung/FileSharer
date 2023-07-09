<template>
      <div class="modal-content shadow">
        <div class="modal-body">   
            <h3 class="mb-4">Upload options</h3> 
            <p class="mb-4">{{ fileName }} already exists in this location.Do you want to replace the existing folder with a new version or keep both folders?Replacing the folder won't change sharing settings.</p>
            <div class="ml-4">
                <div class="form-check mb-3">
                    <input class="form-check-input" type="radio" name="flexRadioDefault" v-model="selectedOption"  value="option1" id="flexRadioDefault1" :checked="selectedOption === 'option1'">
                    <label class="form-check-label ml-2" for="flexRadioDefault1">
                        Replace existing folder
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="flexRadioDefault" v-model="selectedOption" value="option2" id="flexRadioDefault2">
                    <label class="form-check-label ml-2" for="flexRadioDefault2">
                        Keep both folders
                    </label>
                </div>
            </div>
            <div class="d-flex justify-content-end mt-4">
                <button type="button" class="btn btn-default mr-2" @click="canCelUpload">Cancel</button>
                <button type="button" class="btn btn-primary" @click="uploadOptionData">Upload</button>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios'
export default {
    data(){
        return {
            selectedOption : 'option1',
        }
    },
    props : ['fileName'],
    methods : {
        canCelUpload(){
            this.$emit('close-upload-option')
        },
        uploadOptionData(){
           if(this.selectedOption == 'option1'){
                axios.get(`/upload-option-check?fileName=${this.fileName}`)
                .then(response=>{
                    if(response.data.status == 'success'){
                        this.$emit('replace-data');
                    }
                })
                .catch(console.error());
           }else{
            axios.get(`/upload-option-both?fileName=${this.fileName}`)
           .then(response=>{
                if(response.data.status == 'success'){
                    let data = response.data.data;
                    this.$emit('update-option-both',data);
                }
           })
           .catch(console.error());
           }
        }
    }
}
</script>

<style scoped>
.modal-content {
    width: 500px;
    left: 0px !important;
    right: 0px !important;
    margin: 0 auto !important;
    border: none !important;
}


.btn-default{
    color: #4B49AC;
}

.btn-default:hover{
    background-color: rgba(177, 177, 177, 0.425);
}

button{
    font-size: 14px !important;
    padding: 11px 20px;
    font-weight: 600px;
    border-radius: 25px !important;
}
</style>