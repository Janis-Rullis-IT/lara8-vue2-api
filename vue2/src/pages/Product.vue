<template>
	<default-layout>
		<product></product>
	</default-layout>
</template>
<script>
	import defaultLayout from '../layouts/Default.vue';
	import product from '../components/product.vue';
	export default {name: 'ProductPage', components: {defaultLayout, product}}
</script>
