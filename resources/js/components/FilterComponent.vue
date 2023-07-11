<template>
<div class="card mt-3 shadow">
    <div class="card-body py-2">
        <div class="row">
    <div class="col-12 col-lg-3">
        <div class="input-group mb-3">
            <i class="bi bi-x" id="clear_btn" style="position: absolute;top: 20px;right: 8px;z-index: 999;cursor: pointer;" @click="clearSearch"></i>
            <input type="text" class="form-control" placeholder="What are you looking for?" v-model="name" >
        </div>
    </div>
    <div class="col-12 col-lg-3">
        <input type="text" class="form-control date" placeholder="All"  @change="filterByDate"/>
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
</template>

<script>
import $ from "jquery";
import Datatable from 'datatables.net-dt';
import 'daterangepicker';
import 'daterangepicker/daterangepicker.css';
export default {
    data(){
        return {
            name : '',
            myType : '',
            fileTypes : []
        }
    },
    methods:{
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
            this.$emit('search',{
                name : name,
                type : type,
                date : date
            });
        },
        datatable(){
                   //DaterangePicker
        $('.date').daterangepicker({
                autoUpdateInput: false,
                locale: {
                    cancelLabel: 'Clear'
                }
            });

            $('.date').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('YYYY/MM/DD') + ' - ' + picker.endDate.format('YYYY/MM/DD'));
            });

            $('.date').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
            });
        },
        clearSearch(){
            this.name = "";
           this.$emit('clear',this.name); 
        }
    },
    mounted(){
        this.getFileTypes();
        this.datatable();
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

#clear_btn{
    display: none;
}
</style>

