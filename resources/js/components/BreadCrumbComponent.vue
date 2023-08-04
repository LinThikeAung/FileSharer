<template>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/upload-list" class="text-dark breadcrumb-item">Upload Lists</a></li>
            <li class="breadcrumb-item" v-for="folder in folderName" :key="folder.id"><a :href="folder.url" class="text-dark breadcrumb-item">{{ folder.name
            }}</a></li>
        </ol>
    </nav>
</template>

<script>
export default {
    data(){
        return {
            fileId : null,
            subFolderName : null,
            folderName : []
        }
    },
    mounted() {
        this.fileId = $('#folder_id').val();
        this.subFolderName = $('#folder_name').val();
        this.axios.get(`/getFolderPath?file_id=${this.fileId}&file_name=${this.subFolderName}`)
        .then(response=>{
            if(response.data.status == 'success'){
                this.folderName = response.data.data;
            }
        })
        .catch(console.error());
    },
}
</script>

<style></style>