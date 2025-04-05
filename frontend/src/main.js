import { createApp } from 'vue'
import App from './App.vue'
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap/dist/js/bootstrap.bundle.min.js';
import router from './router';
import store from './store';
import BootstrapVue3 from 'bootstrap-vue-3';

const app = createApp(App);
app.use(store).use(router).use(BootstrapVue3).mount('#app');