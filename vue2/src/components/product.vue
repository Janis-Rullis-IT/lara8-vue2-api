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
              <draggable
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
              </draggable>
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
<script>
// https://github.com/SortableJS/Vue.Draggable/blob/master/example/components/simple.vue
import draggable from "vuedraggable";
export default {
  components: {
    draggable
  },
  data() {
    return {
      loading: true,
      alerts: { success: "", errors: [] },
      slug: this.$route.params.slug,
      type: this.$route.params.type,
      product: {},
      ingredients: [],
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

      this.$http.get("products/" + this.type + "/" + this.slug).then(
        function(response) {
          this.product = response.data.data;
          // TODO: Later return dec_ field for this from API.
          this.product.price = this.intToDec(this.product.price);

          this.$http.get("products/" + this.product.hash + "/ingredients").then(
            function(response) {
              this.loading = false;
              this.ingredients = response.data.data;
            },
            function() {
              this.loading = false;
              this.showError(response.data.errors);
            }
          );
        },
        function() {
          this.showError(response.data.errors);
        }
      );
    },
    addNew() {
      this.clearAlerts();
      this.loading = true;

      this.title = this.title.trim();
      this.price = this.price;

      if (this.title.length < 1 || this.price.length < 1) {
        this.showError(["Sorry, no empty values."]);
        return false;
      }

      this.$http
        .post("products/" + this.product.hash + "/ingredients", {
          title: this.title,
          price: this.price * 100
        })
        .then(
          function onSuccess(response) {
            this.loading = false;
            this.ingredients.push(response.data.data);

            // Total price: total collected from API - maybe they don't have the ingredient or a tax is added or a discount.
            // Also the logic should be in one common place - in this specifc case MySQL. Various APIs can use the same data and get the same result.
            // But if needed the price logic can be updated after tbe data/price has been collected like here or in the API that called the DB.
            this.product.price = this.intToDec(response.data.data.product_price);
          },
          function onFail(response) {
            this.loading = false;
            this.showError(response.data.errors);
          }
        );
    },
    remove(item) {
      if (confirm("Are you sure?")) {
        this.clearAlerts();

        this.$http
          .delete("products/" + this.product.hash + "/ingredients/" + item.hash)
          .then(
            function onSuccess(response) {
              this.loading = false;   
              this.ingredients.splice(this.ingredients.indexOf(item), 1);
              this.product.price = this.intToDec(response.data.data.product_price);
            },
            function onFail(response) {
              this.loading = false;
              this.showError(response.data.errors);
            }
          );
      }
    },
    pushSequence: function() {
      var sequence = [];
      var index = 0;
      var icnt = this.ingredients.length;

      if (icnt > 0) {
        var i;

        for (i = 0; i < icnt; i++) {
          sequence.push(this.ingredients[i].hash);
        }

        this.$http
          .put(
            "products/" + this.product.hash + "/ingredients/sequence",
            sequence
          )
          .then(response => {}, response => {});
      }
    },
    intToDec(val){
      return parseFloat(val / 100).toFixed(2);
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
    // TODO: Plug translations.
    getTranslatedMessage: function(messageKey) {
      return this.doesTranslationExist(messageKey)
        ? window.translations[messageKey]
        : messageKey;
    },
    doesTranslationExist(messageKey) {
      return (
        messageKey &&
        typeof window.translations != "undefined" &&
        typeof window.translations[messageKey] != "undefined" &&
        window.translations[messageKey] != null
      );
    }
  }
};
</script> 