<template>
<h3 class="upload-header">Upload Lists</h3>
<div class="card mb-3 shadow">
    <div class="card-body py-2">
        <div class="row">
    <div class="col-12 col-lg-3">
        <input type="text" class="form-control" placeholder="What are you looking for?" v-model="name">
    </div>
    <div class="col-12 col-lg-3">
        <input type="text" class="form-control date" placeholder="All" @change="filterByDate"/>
    </div>
    <div class="col-12 col-lg-3">
       <select class="custom-select" @change="filterByType">
            <option disabled selected class="text-muted">Choose your type</option>
            <option value="">All</option>
            <option v-for="(file,index) in fileTypes" :key="index" :value=" file ">{{ file }}</option>
       </select>
    </div>
    <div class="col-12 col-lg-3">
        <button class="btn btn-primary btn-block" @click="searchEvent">Search</button>
    </div>
</div>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-borderless" id="datatable">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Size</th>
                                <th>Type</th>
                                <th>Uploaded Time</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
</template>

<script>
import $ from "jquery";
import Datatable from 'datatables.net-dt';
export default {
    data(){
        return {
            name : '',
            myType : '',
            fileTypes : []
        }
    },
    methods:{
        getAllFiles(){
            $(document).ready(function(){
                $('#datatable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: '/upload-list/data',
                    columns : [
                        { data : 'name' , name : 'name' },
                        { data : 'size' , name : 'size' },
                        { data : 'type' , name : 'type' },
                        { data : 'created_at' , name : 'created_at' },
                        { data: 'action' , name : 'action'}
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
                    }
                });
            })
        },
        getFileTypes(){
            this.axios.get('/upload-list/type')
            .then(response=>{
                let items = response.data.data;
                items.forEach(item => {
                    this.fileTypes.push(item.type)
                });
            })
        },
        filterByType(event){
            this.myType = event.target.value;
        },
        searchEvent(){
            let name = this.name;
            let type = this.myType;
            let date = $('.date').val();
            $('#datatable').DataTable().ajax.url(`/upload-list/data?name=${name}&type=${type}&date=${date}`).load();
        }
    },
    mounted(){
        this.getAllFiles();     
    },
    created(){
        this.getFileTypes();
    }
}
</script>

<style scoped>
.upload-header{
    margin-bottom: 20px;
}

input{
    margin: 10px 0px;
    padding: 18.7px 15px !important;
    height: 0px;
    border-radius: 3px;
}

select{
    margin: 10px 0px;
    border-radius: 3px;
    cursor: pointer;
    color: #808181 !important;
}

select:focus{
    outline: none !important;
    box-shadow: none !important;
}

button{
    margin: 10px;
    border-radius: 3px !important;
    padding: 11.6px;
}
</style>

