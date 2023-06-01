<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header"> Dashboard </div>
                    <div class="card-body">
                        You are logged in! <p>{{ name }}</p>
                        <p>{{ title }}</p>
                        <button @click="create()">Create</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import Form from 'vform'
export default {
    name: "Dashboard",
    data() {
        return {
            products: {},
            name: "Hello World",
            title: "Vue Js",
            categories: {},
        }
    },
    mounted() {
        const newData = { "title": "Hello World!", "name": "Thant Zin Lay" };
        const categories = { id: 1, "name": "Erke", "made": "China" };
    },

    methods: {
        async create()
        {
            const form = new FormData();
            form.append('brand',"APPLE");
            form.append('made',"USA");
            await axios.post('/category/save',form).then(
                response => console.log(response.data)
            ).catch(
                error => console.log(error)
            );
        }
    }
}
</script>