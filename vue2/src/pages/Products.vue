<template>
	<default-layout>
		<products></products>
	</default-layout>
</template>
<script>
	import defaultLayout from '../layouts/Default.vue';
	import products from '../components/products.vue';
	export default {name: 'ProductsPage', components: {defaultLayout, products}}
</script>
