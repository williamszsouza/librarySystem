import './bootstrap';
import { createApp } from 'vue';
import { createPinia } from 'pinia';
import App from './src/views/App.vue'; 
import router from './src/router/index'; 

const app = createApp(App);
const pinia = createPinia()
app.use(pinia);
app.use(router);
app.mount('#app');