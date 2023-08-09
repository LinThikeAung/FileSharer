<template>
    <div class="modal-backdrop">
        <div class="modal-content shadow">
            <div class="modal-body">
                <h4 class="mb-4">New Folder</h4>
                <input type="text" autofocus v-model="folderName" class="form-control mb-3">
                <div class="d-flex justify-content-end">
                    <button type="button" class="btn btn-default" @click="onCloseEvent">Cancel</button>
                    <button type="button" class="btn btn-default" @click="onClickHandler" :disabled="folderName == ''">Create</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Swal from 'sweetalert2'
export default {
    props : ['fileId','subFolderName','time'],
    data(){
        return {
            folderName : 'Untitled folder' 
        }
    },
    methods : {
        onCloseEvent(){
            this.$emit('closeCreateFolder');
        },
        onClickHandler(){
            this.axios.post(`/create-subFolder?folderName=${this.folderName}&fileId=${this.fileId}&subFolderName=${this.subFolderName}&created_at=${this.time}`)
            .then(response=>{
                if(response.data.status == 'fail'){
                    Swal.fire({
                        title: response.data.data + ' is already exists.',
                        focusConfirm : false,
                    })
                }
                if(response.data.status == "success"){
                    window.location.reload();
                }
            })               
            .catch(console.error());            
        }
    }
}
</script>

<style scoped>
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
    width: 350px;
    left: 0px !important;
    right: 0px !important;
    margin: 0 auto !important;
    border: none !important;
    transition: opacity 0.3s ease;
}

input:focus{
    border: 2px solid rgb(14, 14, 173);
}

button{
    padding: 10px !important;
    color:rgb(14, 14, 173) ;
    font-weight: bold;
}

button:hover{
    background-color: rgba(112, 112, 112, 0.132);
    color:rgb(14, 14, 173) ;
}

button:disabled{
    color: gray;
    background-color: transparent;
}

</style>