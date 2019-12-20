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
<script>
export default {
  data() {
    return { loading: true, alerts: { success: "", errors: [] }, products:  [] };
  },
  created() {
    this.getProducts();
  },
  methods: {
    getProducts: function() {
      this.clearAlerts();
      this.loading = true;

      this.$http.get("products").then(
        function(response) {
          console.log(response);
          this.products = response.data.data;
          this.loading = false;
        },
        function(response) {
          this.showError(response.data.errors);
        }
      );
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
        this.alerts.errors.push(this.getTranslatedMessage(error));
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
        typeof window.translations[messageKey] != "undefined" &&
        window.translations[messageKey] != null
      );
    }
  }
};
</script>