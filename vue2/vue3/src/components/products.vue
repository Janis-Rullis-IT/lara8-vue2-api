<template>
  <div>
    <!-- <div class="row alerts">
      <div v-if="alerts.success" class="row">
        <div class="col-sm-12">
          <div class="alert success">{{alerts.success}}</div>
        </div>
      </div>
       <div v-if=alerts.errors class="row"><div class="col"><div v-for="(error, index) in alerts.errors" :key="index" class="alert errorr">{{error}}</div></div></div>
    </div>

    <div class="row content products">
      <div class="row header">
        <div class="col-sm-12"></div>
      </div>
      <div class="row body">
        <div class="col-sm-4" v-for="product in products" :key="product.slug">
          <div class="row thumbnail">
            <div class="col-sm-12">
              <a :href="loading ? 'javascript:;' : 'products/' + product.type + '/' + product.slug">{{product.title}}</a>
            </div>
          </div>          
        </div>
      </div>
    </div> -->
  </div>
</template>

<script lang="ts">
import { defineComponent } from 'vue';

export default defineComponent({
  name: 'products',
  props: {
    msg: String,
  },
});
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
h3 {
  margin: 40px 0 0;
}
ul {
  list-style-type: none;
  padding: 0;
}
li {
  display: inline-block;
  margin: 0 10px;
}
a {
  color: #42b983;
}
</style>