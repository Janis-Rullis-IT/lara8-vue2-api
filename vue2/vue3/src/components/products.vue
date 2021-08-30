<template>
  <div>
    <div class="row alerts">
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
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent } from 'vue';

interface ProductResponse {
    data: Array<Product>;
    success: boolean;
}

interface Product {
    hash: string;
    price: number;
    slug: string;
    title: string;
    type: string;
}

export default defineComponent({
  name: 'products',
//   props: {
//     msg: String,
//   },
   data() {
    return { loading: true, alerts: { success: "", errors: [] as Array<string> }, products:  [] as Array<Product> };
  },
  created() {
    this.getProducts();
  },
  methods: {
    getProducts: function(): void {
      this.clearAlerts();
      this.loading = true;

      fetch("http://api.ruu.local/products")
      .then(res => res.json())
      .then((data: ProductResponse) => {
           this.products = data.data;
           this.loading = false;
           console.log(this.products)
      })
      .catch(err => this.showError(err.message))
    },
    // TODO: Move to common lib.
    clearAlerts: function(): void {
      this.alerts.success = "";
      this.alerts.errors = [];
    },
    showSuccess: function(success = "Saved!"): void {
      this.alerts.success = this.getTranslatedMessage(success);
      this.loading = false;
     },
    showError: function(errors = ["Sorry, but there's a problem."]): void {
      for (let i = 0; i < errors.length; i++) {
        this.alerts.errors.push(this.getTranslatedMessage(errors[i]));
        this.loading = false;
      }
    },
    
    getTranslatedMessage: function(messageKey: string): string {
        return messageKey;
        // TODO: Plug translations.
      //return this.doesTranslationExist(messageKey) ? window.translations[messageKey] : messageKey;
    },
    // doesTranslationExist(messageKey) {
    //   return (
    //     messageKey && typeof window.translations != "undefined" &&  typeof window.translations[messageKey] != "undefined" && window.translations[messageKey] != null
    //   );
    // }
  }
});
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
.width-50{
	width: 50px;
}
.width-100{
	width: 100px;
}
.width-150{
	width: 150px;
}
.body{
	min-height: 450px;
	background: lightgray;
	padding: 25px;
}
.no-bullet{
	list-style: none;
}
.float-right{
	float: right;
}
.float-left{
	float: left;
}
.txt-center{
	text-align: center;
}
.txt-right{
	text-align: right;
}

/* https://github.com/SortableJS/Vue.Draggable/blob/master/example/components/simple.vue */
.list-group-item:first-child {
    border-top-left-radius: .25rem;
    border-top-right-radius: .25rem;
}
.list-group-item {
    position: relative;
    display: block;
    padding: .75rem 1.25rem;
    margin-bottom: -1px;
    background-color: #fff;
    border: 1px solid rgba(0,0,0,.125);
}
.list-group-item {
    cursor: move;
}
.ghost {
  opacity: 0.5;
  background: #c8ebfb;
}
</style>