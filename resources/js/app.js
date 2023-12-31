/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');
/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */
// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))


/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */
import { createApp } from 'vue'
import VueAxios from 'vue-axios';
import axios from 'axios';
import Swal from 'sweetalert2'
//component
import App from './components/App.vue';
import UploadComponent from './components/UploadComponent.vue';
import FilterComponent from './components/FilterComponent.vue';
import ProgressComponent from './components/ProgressComponent.vue';
import UploadOptionComponent from './components/UploadOptionComponent.vue';
import FilePondComponent from './components/FilePondComponent.vue';
import ShareComponent from './components/ShareComponent.vue';
import MyShareComponent from './components/MyShareComponent.vue';
import MyShareFilterComponent from './components/MyShareFilterComponent.vue';
import OtherShareComponent from './components/OtherShareComponent.vue';
import OtherShareFilterComponent from './components/OtherShareFilterComponent.vue';
import ConfirmComponent from './components/ConfirmComponent.vue';
import SubFolderUploadComponent from './components/SubFolderUploadComponent.vue';
import BreadCrumbComponent from './components/BreadCrumbComponent.vue';
import CreateFolderComponent from './components/CreateFolderComponent.vue';
import CreateSubFolderComponent from './components/CreateSubFolderComponent.vue';

const app = createApp({});

app.use(VueAxios, axios);
app.use('Swal',Swal);
app.component('app',App);
app.component('upload-component',UploadComponent);
app.component('filter-component',FilterComponent);
app.component('progress-component',ProgressComponent);
app.component('uploadOption-component',UploadOptionComponent);
app.component('file-pond-component',FilePondComponent);
app.component('my-share-component',MyShareComponent);
app.component('share-component',ShareComponent);
app.component('my-shareFilter-component',MyShareFilterComponent);
app.component('other-share-component',OtherShareComponent)
app.component('other-shareFilter-component',OtherShareFilterComponent);
app.component('confirm-component',ConfirmComponent);
app.component('sub-folder-upload',SubFolderUploadComponent);
app.component('breadcrumb-component',BreadCrumbComponent);
app.component('create-folder-component',CreateFolderComponent);
app.component('create-subFolder-component',CreateSubFolderComponent);

app.mount("#app");