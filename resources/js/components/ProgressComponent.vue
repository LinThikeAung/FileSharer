<template>
    <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">1 upload complete</h5>
          <button type="button" class="close" aria-label="Close">
            <i class="bi bi-x"></i>
          </button>
        </div>
        <div class="modal-body">    
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <i class="fa-solid fa-folder mr-4"></i>
                    <span>{{ fileName }}</span>
                </div>
                <button class="btn-default">Cancel</button>
            </div>
            {{ upload }}
                <!-- <div class="progress mt-3">
                    {{  }} -->
                    <!-- <div class="progress-bar" role="progressbar" :style="{width: uploadProgress}" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">{{ uploadProgress }}</div> -->
                <!-- </div> -->
        </div>
      </div>
</template>

<script>
export default {
props : ['fileName','uploadProgress']
}
</script>

<style scoped>
.modal-content {
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
    right: 20px;
    pointer-events: auto;
    background-color: #fff;
    background-clip: padding-box;
    border: 1px solid rgba(0,0,0,.2);
    border-radius: 0.3rem 0.3rem 0 0;
    outline: 0;
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
    font-size: 25px;
}

button.btn-default{
    border: none;
    background: rgba(130, 130, 130, 0.219);
    border-radius: 15px;
    padding: 5px 12px;
    color:rgba(9, 84, 183, 0.847) ;
    font-size: 14px;
    font-weight: bold;
}
.progress{
    height: 13px !important;
    border-radius: 0px !important;
}

.progress-bar{
    border-radius: 0px !important;
    background-color: green !important;
}
</style>