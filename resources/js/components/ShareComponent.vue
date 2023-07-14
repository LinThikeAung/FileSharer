<template>
    <div class="modal-backdrop">
       <div class="modal-content shadow">
        <div class="modal-header d-flex justify-content-between align-items-center">
            <h5>{{ shareName }}</h5>
            <button type="button" class="close" aria-label="Close" @click="closeShareModal">
            <i class="bi bi-x"></i>
          </button>
        </div>
       <div class="modal-body">   
            <form class="mb-2" @submit.prevent="submitEvent">
                <div class="input" id="add-input">
                    Add people you want to send
                </div>
                <p class="text-danger mb-0" v-if="error">{{ error }}</p>
                <textarea rows="2" class="form-control mb-3 mt-3" placeholder="Message" v-model="message">
                </textarea>
               <div class="d-flex justify-content-end">
                <button class="btn btn-primary" type="submit">Send</button>
               </div>
            </form>
            <h5>People with acccess</h5>
            <div class="user-section">
                <ul>
                    <li v-for="user in users" :key="user.id" class="mt-3" @click="clickHandler(user)">
                        <div class="d-flex align-items-center">   
                            <img :src="'https://ui-avatars.com/api/background=random?name='+user.email" alt="">
                            <div>
                                <p class="mb-0">{{ user.name }}</p>
                                <p class="mb-0">{{ user.email }}</p>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
       </div>
   </div>
    </div>
</template>

<script>
import axios from 'axios'
import { filter } from 'lodash';
import swal from 'sweetalert';
export default {
   data(){
       return {
            shareUsers : [],
            message : null,
            error : null,
       }
   },
   props : ['shareName','users'],
   methods : {
    clickHandler(user){
        if(document.getElementById('add-input').innerText == 'Add people you want to send'){
                document.getElementById('add-input').innerText = " ";
                if(document.getElementById('add-input').innerText.includes(user.email)){
                    swal({
                            text: "This user is already added",
                            buttons : false,
                            timer : 1000
                    });
                    return false;
                }
                document.getElementById('add-input').innerHTML = `<span class="badge rounded-pill bg-light text-dark m-2" id="${user.id}" style="cursor:pointer;">${user.email}</span>`;
                this.error = null;
                this.shareUsers.push(user.id);
        }else{
                if(document.getElementById('add-input').innerText.includes(user.email)){
                    swal({
                            text: "This user is already added",
                            buttons : false,
                            timer : 1000
                    });
                    return false;
                }
                document.getElementById('add-input').innerHTML +=`<span class="badge rounded-pill bg-light text-dark m-1" id="${user.id}" style="cursor:pointer;">${user.email}</span>`;
                this.error = null;
                this.shareUsers.push(user.id);
        }
    },
    closeShareModal(){
        this.$emit('close');
    },
    submitEvent(){
        if(document.getElementById('add-input').innerText == "Add people you want to send"){
            this.error = 'Please choose at least one user';
        }
        let formdata = new FormData();
        formdata.append('user',this.shareUsers);
        formdata.append('shareName',this.shareName);
        formdata.append('message',this.message);
        axios.post('/store-share-user',formdata)
        .then(response=>{
            if(response.data.status == 'success'){
                this.$emit('close');
            }
        })
        .catch(console.error());
    }
   },
   mounted(){
        document.getElementById('add-input').addEventListener('click',event=>{
            if(event.target.id != 'add-input'){
                let id = event.target.id;
                this.shareUsers  = this.shareUsers.filter(user=>{
                  return  user != id;
                })
                event.target.innerHTML = "";
                document.getElementById('add-input').removeChild(event.target);
                if(document.getElementById('add-input').innerText == ""){
                    document.getElementById('add-input').innerHTML = "Add people you want to send";
                }
            }
        })
   },
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

.modal-backdrop {
   position: fixed !important;
   left: 0;
   right: 0;
   background-color: rgba(0, 0, 0, 0.3);
   display: flex;
   justify-content: center;
   align-items: center;
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
   border-radius: 5px !important;
}
.input{
    border: 1px solid #ccc;
    max-height: 60px !important;
    border-radius: 5px;
    padding: 8px 20px;
    color: rgb(187 190 192);
    font-size: 14px;
    overflow: auto;
}

li{
    list-style-type:none;
    padding: 5px;
}

li:hover{
    background-color: rgba(128, 128, 128, 0.361);
    
}

.user-section{
    max-height: 200px !important;
    overflow: auto;
}

ul{
   padding-left:0rem !important ;
}
img{
    width: 40px;
    border-radius: 50%;
    margin-right: 20px;
}

p{
    font-size: 14px;
}

.input span{
    display: inline-block;
    padding: 5px;
    border: 1px solid beige;
    border-radius: 20px;
}

i{
    font-size: 20px !important;
}
</style>