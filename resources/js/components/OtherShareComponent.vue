<template>
    <div class="d-flex justify-content-between align-items-center upload-container">
    <h5>OtherShare Lists</h5>
    <button class="btn btn-theme mr-2" type="button" aria-expanded="false" @click="showFilter = !showFilter">
                <i class="bi bi-filter"></i> <span>Filter</span>
    </button>
    </div>
    <div v-if="showFilter">
        <other-shareFilter-component @search="searEventHandler" @clear="clearEvent"></other-shareFilter-component>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card mt-3 mb-3 shadow">
        <div class="card-body p-2">
        <!-- <div class="table-responsive"> -->
            <table class="table table-hover" id="datatable">
                <thead>
                    <tr>
                        <th>Share User</th>
                        <th>filename</th>
                        <th>Type</th>
                        <th>Share Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        <!-- </div> -->
    </div>
</div>
        </div>
    </div>
   <div v-if="showShareComponent">
    <share-component :shareName = "shareName" :users="users" @close="closeShareModal"></share-component>
   </div>
</template>

<script>
import axios from 'axios';
import Swal from 'sweetalert2'
import FilePondComponent from './FilePondComponent.vue';
import swal from 'sweetalert';
export default {
  components: { FilePondComponent },
    data(){
        return {
            folders : [],
            showFilter : false,
            showShareComponent : false,
            shareName : null,
            users : []
        }
    },
    methods:{
        getAllFiles(){
            $(document).ready(function(){
                $('#datatable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: '/other-share-list/data',
                    columns : [
                        { data : 'share_user' , name : 'share_user' },
                        { data : 'name' , name : 'name' },
                        { data : 'type' , name : 'type' },
                        { data : 'created_at' , name : 'created_at' },
                        { data : 'action' , name : 'action'}
                    ],
                    
                });

                $('#datatable').on('click', '.copy', (event) => {
                    let value = event.target.id;
                    let input = document.createElement("input");
                    input.value = value;
                    document.body.appendChild(input);
                    input.select();
                    if(document.execCommand('copy')) {
                        document.body.removeChild(input);
                        swal({
                            text: "copied!",
                            buttons : false,
                            timer : 1000
                        });
                    }
                });
            })

            $('#datatable').on('click','.share',event=>{
                let fileName = event.target.id;
                this.shareName = fileName;
                axios.get(`/otherShare-getUser?fileName=${fileName}`)
                .then(response=>{
                    this.users = response.data.data;
                    this.showShareComponent = true;
                })
                .catch(console.error());
                
            })
        },
        searEventHandler(option){
            let name = option.name;
            let type = option.type;
            let date = option.date;
            if(option.name){
                document.getElementById('clear_btn').style.display = "block";
            }
            $('#datatable').DataTable().ajax.url(`/other-share-list/data?name=${name}&type=${type}&date=${date}`).load();
        },
        clearEvent(data){
            $('#datatable').DataTable().ajax.url(`/other-share-list/data?name=${data}`).load();
            document.getElementById('clear_btn').style.display = "none";

        },
        dbClickEvent(){
            alert('hit');
        },
        closeShareModal(){
            this.showShareComponent = false;
            window.location.reload();
        }
    },
    mounted(){
        this.getAllFiles();     
    }
}
</script>

<style scoped>
.btn-theme{
    background-color:white ;
    display: flex;
    align-items: center;
    justify-content: space-between !important;
    box-shadow: rgba(0, 0, 0, 0.24) 0px 3px 8px;
    border-radius: 8px !important;
    padding: 13px 15px;
}

.btn-theme:hover{
    background-color: rgba(212, 217, 242, 0.235);
}
.btn-theme span{
    display: inline-block;
    font-size: 16px;
}
.btn-theme i{
    font-size: 25px;
    font-weight: bold;
    margin-right: 5px;
}

.image{
    width: 18px;
    margin-right: 12px;
}

.card{
    border-radius: 8px !important;
}

</style>