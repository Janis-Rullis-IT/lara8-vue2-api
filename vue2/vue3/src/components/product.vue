<template>
  <div>
    <div class="row alerts">
      <div v-if="alerts.success" class="row">
        <div class="col-sm-12">
          <div class="alert success">{{alerts.success}}</div>
        </div>
      </div>
      <div v-if="alerts.errors" class="row">
        <div class="col">
          <div v-for="(error, index) in alerts.errors" :key="index" class="alert errorr">{{error}}</div>
        </div>
      </div>
    </div>

    <div class="row content product">
      <div class="row body">
        <div class="col-sm-12">
          <div class="row title">
            <div class="col-sm-12">
              <h1>{{product.title}}</h1>
            </div>
          </div>
          <div class="row thumbnail">
            <div class="col-sm-12"></div>
          </div>
          <div class="row rating">
            <input
              id="name"
              style="width: 100%;"
              type="text"
              v-model="title"
              placeholder="Name. Ex., Cheese"
            />
            <input
              id="price"
              type="number"
              style="width: 100%;"
              v-model="price"
              placeholder="Price. Ex., 0.3"
            />
            <button type="button" @click="addNew()">Add</button>

            <div class="col-sm-12 list-groupe">
              <!-- <draggable
                :list="ingredients"
                :disabled="!enabled"
                class="list-group"
                ghost-class="ghost"
                @start="dragging = true"
                @end="pushSequence()"
              >
                <div
                  class="list-group-item"
                  v-for="ingredient in ingredients"
                  :key="ingredient.hash"
                >
                  {{ ingredient.title }} {{ intToDec(ingredient.price) }}
                  <button type="button" @click="remove(ingredient)">Delete</button>
                </div>
              </draggable> -->
            </div>
          </div>
          <div class="row text">
            <div class="col-sm-12">
              <strong>Total price: {{product.price}}</strong>
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
    data: Product;
    success: boolean;
}

interface IngredientResponse {
    data: Array<Ingredient>;
    success: boolean;
}

interface Product {
    hash: string;
    price: number;
    slug: string;
    title: string;
    type: string;
}

interface Ingredient {
    hash: string;
    price: number;
    product_price: number;
    slug: string;
    title: string;
    type: string;
}

export default defineComponent({
  name: 'product',
  data() {
    return {
      loading: true,
      alerts: { success: "", errors: [] as Array<string> },
      slug: this.$route.params.slug as string,
      type: this.$route.params.type as string,
      product: {} as Product,
      ingredients: [] as Array<Product>,
      dragging: false,
      enabled: true,
      title: "",
      price: ""
    };
  },
  created() {
    this.getProduct();
  },
  methods: {
    getProduct: function() {
      this.clearAlerts();
      this.loading = true;

      fetch(`http://api.ruu.local/products/${this.type}/${this.slug}`)
      .then(res => res.json())
      .then((data: ProductResponse) => {
           this.product = data.data;
          // TODO: Later return dec_ field for this from API.
          this.product.price = this.intToDec(this.product.price);
        return fetch("http://api.ruu.local/products/" + this.product.hash + "/ingredients");
      })
      .then(res => res.json())
      .then((data: IngredientResponse) => { 
            this.loading = false;
            this.ingredients = data.data;
      })
      .catch((err) => {
          this.loading = false;
          console.log(err);
          this.showError(err.errors)
      })
    },
    addNew() {
      this.clearAlerts();
      this.loading = true;

      this.title = this.title.trim();

      if (this.title.length < 1 || this.price.length < 1) {
        this.showError(["Sorry, no empty values."]);
        return false;
      }

        fetch("http://api.ruu.local/products/" + this.product.hash + "/ingredients", {method: 'POST', body: JSON.stringify( {
            title: this.title, price: 100 * parseInt(this.price)
        })})
        .then(res => res.json())
        .then((data: Ingredient) => { 
            this.loading = false;
            this.ingredients.push(data);

            // Total price: total collected from API - maybe they don't have the ingredient or a tax is added or a discount.
            // Also the logic should be in one common place - in this specific case MySQL. Various APIs can use the same data and get the same result.
            // But if needed the price logic can be updated after tbe data/price has been collected like here or in the API that called the DB.
            this.product.price = this.intToDec(data.product_price);
        })
        .catch((err) => {
            this.loading = false;
            console.log(err);
            this.showError(err.errors)
        })
    },
    remove(item: Ingredient) {
      if (confirm("Are you sure?")) {
        this.clearAlerts();

        fetch("http://api.ruu.local/products/" + this.product.hash + "/ingredients/" + item.hash, {method: 'DELETE'})
        .then(res => res.json())
        .then((data: Ingredient) => { 
            this.loading = false;   
            this.ingredients.splice(this.ingredients.indexOf(item), 1);
            this.product.price = this.intToDec(data.product_price);
        })
        .catch((err) => {
            this.loading = false;
            console.log(err);
            this.showError(err.errors)
        })
      }
    },
    pushSequence: function() {
      var sequence = [];
      var icnt = this.ingredients.length;

      if (icnt > 0) {
        var i;

        for (i = 0; i < icnt; i++) {
          sequence.push(this.ingredients[i].hash);
        }

        return fetch("http://api.ruu.local/products/" + this.product.hash + "/ingredients/sequence", {method: 'PUT', body: JSON.stringify(sequence)});
      }
    },
    intToDec(val: number): number{
      let str:string = (val / 100).toFixed(2);
      return parseFloat(str);
    },
    // TODO: Move to common lib.
    clearAlerts: function() {
      (this.alerts.success = ""), (this.alerts.errors = []);
    },
    showSuccess: function(success = "Saved!") {
      this.alerts.success = this.getTranslatedMessage(success);
      this.loading = false;
    },
    showError: function(errors = ["Sorry, but there's a problem."]) {
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
})
</script> 