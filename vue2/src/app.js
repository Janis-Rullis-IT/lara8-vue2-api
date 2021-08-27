// import Vue from 'vue';
// import router from "./router";
// import App from './App.vue';
// // import VueResource from "vue-resource";
// // Vue.use(VueResource);

// Vue.http.options.root = 'http://api.ruu.local';
// new Vue({router, render: h => h(App)}).$mount("#app");

// #16 https://www.vuemastery.com/blog/vue-3-migration-build/
import { createApp } from 'vue';
import router from "./router";
import App from './App.vue'

createApp(App).mount('#app')
// createApp({router, render: h => h(App)}).mount('#app')