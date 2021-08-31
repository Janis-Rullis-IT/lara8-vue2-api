<template>
    <product></product>
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import product from '../components/product.vue';

export default defineComponent({
  name: 'ProductPage',
  components: {
    product,
  },
});
</script>

