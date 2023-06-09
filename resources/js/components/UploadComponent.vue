<template>
     <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-body">
                    <h3 class="text-center mt-2 upload-text">Upload Your Files</h3>
                    <!-- Drap and Drop -->
                    <div class="drag-area" @dragover.prevent="onDragover" @dragleave.prevent="onDragleave" @drop.prevent="onDrop">
                        <div class="icon text-center">
                            <img :src="'/backend/images/admin/folder.png'" alt="">
                        </div>
                        <p class="text-center drag-text" v-if="!isDragging">
                            Drag and drop or
                            <span role="button" class="select"  @click="selectFiles">
                                browse files
                            </span>
                        </p>
                        <p v-else class="select text-center text-muted">Drop here</p>
                        <input type="file" name="file" class="file" ref="fileInput" multiple @change="uploadFile" hidden/>
                    </div>      
                    <!-- Error Message -->
                    <p class="text-danger" v-if="errorMessager">{{ errorMessager }}</p>
                    <!-- Loading Section -->
                    <div class="loading-area mb-3" v-for="(file,index) in files" :key="index">
                        <div class="card shadow-sm"  :class="{showProgress : file.loading == 100 }">
                            <div class="card-body py-3">
                                <li class="d-flex justify-content-between align-items-center">
                                    <i class="icon-grid ti-zip"></i>
                                    <div class="content">
                                        <div class="details">
                                            <span class="name">{{ file.name }}</span>
                                            <span class="percent text-muted">{{ file.loading }}%</span>
                                        </div>
                                        <div class="loading-bar">
                                            <div class="loading" :style="{width:file.loading + '%'}"></div>
                                        </div>
                                    </div>
                                </li>   
                            </div>
                        </div>
                    </div>  
                    <!-- Upload Section -->
                    <div class="uploaded-area mb-3" v-for="(file,index) in uploadFiles" :key="index">
                        <div class="card shadow-sm">
                            <div class="card-body py-3">
                                <li class="d-flex justify-content-between align-items-center">                    
                                    <div class="content upload">
                                        <i class="icon-grid ti-zip"></i>
                                        <div class="details">
                                            <span class="name">{{ file.name }}</span>
                                            <span class="size">{{ file.size }}</span>
                                        </div>
                                    </div>
                                    <i class="icon-grid ti-check"></i>
                                </li>
                            </div>
                        </div> 
                  </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data(){        
        return {
            isDragging : false,
            inputFiles : [],
            files : [],
            fileSize : null,
            uploadFiles : [],
            showProgress : false,
            errorMessager : null
        }
    },
    methods:{
        selectFiles(){
            this.$refs.fileInput.click();
        },
        onDragover(event){
            event.preventDefault();
            this.isDragging = true;
            event.dataTransfer.dropEffect = "copy";
        },
        onDragleave(event){
            event.preventDefault();
            this.isDragging = false;
        },
        onDrop(event){
            event.preventDefault();
            this.isDragging = false;
            this.files = [];
            this.uploadFiles = [];
            this.inputFiles = event.dataTransfer.files;

            if (this.inputFiles.length > 5) {
                this.errorMessager = "Your uploaded files must be at least 5.";
                alert(this.errorMessager);
                return false;
            }

            for (let i = 0; i < this.inputFiles.length  ; i++) {
                this.upload(i,this.inputFiles[i]);
            }
        },
        uploadFile(event){
            this.files = [];
            this.uploadFiles = [];
            this.inputFiles = event.target.files;

            if (this.inputFiles.length > 5) {
                this.errorMessager = "Your uploaded files must be at least 5.";
                alert(this.errorMessager);
                return false;
            }

            for (let i = 0; i < this.inputFiles.length  ; i++) {
                this.upload(i,this.inputFiles[i]);
            }
        },
        upload(index,file){

            this.errorMessager = null;
            let fileName = file.name.length >= 12 ? file.name.substring(0,13) + "... ." + file.name.split('.')[1] : file.name;
            this.files[index] = {name : fileName , loading : 0};    
            this.showProgress = true;
            var totalBytes = file.size;
            if(totalBytes < 1000000){
                this.fileSize = Math.floor(totalBytes/1000) + ' KB';
            }
            else
            {
                this.fileSize = Math.floor(totalBytes/1000000) + ' MB';  
            } 

            let formData = new FormData();
            formData.append('file',file);
            formData.append('size',this.fileSize);
            formData.append('name',file.name);

            axios.post('/upload',formData, {
            onUploadProgress : event =>{
            this.files[index].loading = Math.round((event.loaded/event.total) * 100);
            if(event.loaded == event.total){
                var totalBytes = file.size;
                if(totalBytes < 1000000){
                    var fileSize = Math.floor(totalBytes/1000) + ' KB';
                    this.uploadFiles.push({name: fileName , size: fileSize});
                }
                else
                {
                    var fileSize = Math.floor(totalBytes/1000000) + ' MB';  
                    this.uploadFiles.push({name: fileName , size: fileSize});
                }
                         
            }
        }
              })
        }
    }
}
</script>

<style scoped>

.upload-text{
    margin-bottom: 35px;
    color: #0504348f;
}
.drag-area{
    width: 100%;
    padding: 70px;
    border: 2px dashed #4b49ac65;
    border-radius: 5px;
    margin-bottom: 15px;
    background-color: rgba(193, 213, 230, 0.057);
}

.icon img{
    width: 95px;
}

.drag-text{
    color: #4946ec;
}

.select{
    color: #000;
}

.loading-area i,
.uploaded-area i{
    font-size: 22px;
}

.ti-check{
    color: #11c668;
    font-weight: bold;
}

.details span{
  font-size: 14px;;
}

.loading-area .percent{
    font-size: 13px;
}
.loading-area .content{
  width: 100%;
  margin-left: 15px;
}


.loading-area .details{
  display: flex;
  align-items: center;
  margin-bottom: 7px;
  justify-content: space-between;
}

.loading-area .content .loading-bar{
  height: 8px;
  margin-bottom: 4px;
  background-color: #0000001a !important;
  border-radius: 10px;
}

.content .loading-bar .loading{
  height: 100%;
  width: 20%;
  background: #11c668;
  border-radius: inherit;
}


.uploaded-area .content{
  display: flex;
  align-items: center;   
}

.uploaded-area  .details{
  display: flex;
  margin-left: 15px;
  flex-direction: column;
}

.uploaded-area  .details .size{
  color:#404040 ;
  font-size: 11px;
}

.uploaded-area i .fa-check{
  font-size: 10px !important;
}

.showProgress{
  display:  none !important;
}

.tex-danger{
    font-size: 14px !important;
    letter-spacing: 0.6px;
    color: crimson !important;
}
</style>

