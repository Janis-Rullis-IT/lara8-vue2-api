<template>
    <products></products>
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import products from '../components/products.vue';

export default defineComponent({
  name: 'ProductsPage',
  components: {
    products,
  },
});
</script>

