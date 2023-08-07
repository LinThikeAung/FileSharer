<template>
    <div id="content-container">
        <div class="modal-header">
          <h5 class="modal-title">Folder upload</h5>
          <button type="button" class="close" aria-label="Close" v-if="uploading"  @click="closeDialoag">
            <i class="bi bi-x"></i>
          </button>
          <button type="button" class="close" aria-label="Close" v-if="uploadCancel" @click="closeUpload">
            <i class="bi bi-x"></i>
          </button>
          <button type="button" class="close" aria-label="Close" v-if="showSuccess" @click="closeUpload">
            <i class="bi bi-x"></i>
          </button>
        </div>
        <div class="modal-body">    
            <div v-if="uploading">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <img :src="imageUrl" class="folder-image mr-4"/>
                        <span>{{ fileName }}</span>
                        <span class="ml-4">{{ count }} of {{ totalFile }} </span>
                    </div>
                </div>
                    <div class="progress my-1" role="progressbar" aria-label="Example with label" aria-valuemin="0" aria-valuemax="100">
                        <div class="progress-bar" :style="{width :uploadProgress+'%' }">{{ uploadProgress }}%</div>
                    </div>
            </div>
            <div v-if="uploadCancel">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <img :src="imageUrl" class="folder-image mr-4"/>
                        <span>{{ fileName }}</span>
                    </div>
                    <span>Upload Canceled</span>
                </div>            
            </div>
            <div v-if="showSuccess">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <img :src="imageUrl" class="folder-image mr-4"/>
                        <span>{{ fileName }}</span>
                    </div>
                    <i class="bi bi-check2"></i>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
props : ['fileName','uploadProgress','uploading','uploadCancel','showSuccess','count','totalFile'],
data(){
    return  {
        imageUrl : '/backend/images/folder.png'
    }
},
methods:{
    closeDialoag(){
        this.$emit('close-dialoag');
    },
    closeUpload(){
        this.$emit('close-upload');
    }
}
}
</script>

<style scoped>
#content-container {
    position: fixed;
    display: -webkit-box;
    display: -ms-flexbox;
    display: flex;
    -webkit-box-orient: vertical;
    -webkit-box-direction: normal;
    -ms-flex-direction: column;
    flex-direction: column;
    width: 350px;
    max-height: 600px;
    bottom: 0;
    right: 20px !important;
    pointer-events: auto;
    background-color: #fff;
    background-clip: padding-box;
    border: 1px solid rgba(0,0,0,.2);
    border-radius: 0.3rem 0.3rem 0 0;
    outline: 0;
    z-index: 99999;
}

.modal-header {
    display: -webkit-box;
    display: -ms-flexbox;
    display: flex;
    -webkit-box-align: center;
    -ms-flex-align: center;
    align-items: center ;
    -webkit-box-pack: justify;
    -ms-flex-pack: justify;
    justify-content: space-between;
    padding: 1rem;
    border-bottom: 1px solid #e9ecef;
    border-top-left-radius: 0.3rem;
    border-top-right-radius: 0.3rem;
}

i{
    font-size: 22px;
    margin-top: 3px;
}
.progress{
    height: 15px !important;
    border-radius: 0px !important;
}

.progress-bar{
    border-radius: 0px !important;
    background-color: green !important;
    font-size: 12px !important;
    padding-left: 5px;
}

.folder-image{
    width: 20px;
}
.modal-header .bi-x{
    font-weight: bold !important;
}
.modal-body .bi-x{
    padding: 3px 5px;
    background-color: rgba(224, 224, 224, 0.34);
    border-radius: 50%;
    font-size: 18px;
    margin-bottom: 5px;
}
.modal-body .bi-check2{
    padding: 3px 5px;
    background-color: rgba(11, 151, 63, 0.756);
    border-radius: 50%;
    font-size: 16px;
    color: white;
}
</style>