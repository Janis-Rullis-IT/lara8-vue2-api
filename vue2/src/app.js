// Vue.http.options.root = 'http://api.ruu.local';

// #16 https://www.vuemastery.com/blog/vue-3-migration-build/
// #16 https://v3.vuejs.org/guide/migration/global-api.html#a-new-global-api-createapp
import { createApp } from 'vue';
import router from "./router";
import App from './App.vue'

// #16 https://github.com/vuejs/vue-router-next/issues/203#issuecomment-661073394
const app = createApp(App);
app.use(router);
app.mount('#app')