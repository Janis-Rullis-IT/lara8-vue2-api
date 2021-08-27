import Vue from 'vue';
//import Router from 'vue-router';
// #16 https://next.router.vuejs.org/guide/migration/#new-router-becomes-createrouter
import { createRouter, createWebHistory } from 'vue-router'
//Vue.use(Router);
const router = createRouter({history: createWebHistory('/'), routes: [
		{path: '/', name: 'HomePage', component: () => import(/* webpackChunkName: "ProductsPage" */ '../pages/Products.vue')},
		{path: '/products', name: 'ProductsPage', component: () => import(/* webpackChunkName: "ProductsPage" */ '../pages/Products.vue')},
		{path: '/products/:type/:slug', name: 'ProductPage', component: () => import(/* webpackChunkName: "ProductPage" */ '../pages/Product.vue')},
	]
});
export default router;
