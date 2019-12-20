import Vue from 'vue';
import Router from 'vue-router';
Vue.use(Router);
const router = new Router({mode: 'history', base: '/', routes: [
		{path: '/', name: 'HomePage', component: () => import(/* webpackChunkName: "ProductsPage" */ '../pages/Products.vue')},
		{path: '/products', name: 'ProductsPage', component: () => import(/* webpackChunkName: "ProductsPage" */ '../pages/Products.vue')},
		{path: '/products/:type/:slug', name: 'ProductPage', component: () => import(/* webpackChunkName: "ProductPage" */ '../pages/Product.vue')},
	]
});
export default router;
