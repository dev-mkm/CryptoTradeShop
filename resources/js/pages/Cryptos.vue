<script setup>
import CryptoCard from '../components/CryptoCard.vue'
</script>
<script>
export default {
  data() {
    return {
      cryptos: [],
    };
  },
  mounted () {
    this.$axios
      .get('/api/cryptos')
      .then(response => {this.cryptos = response.data.result})
  }
};
</script>

<template>
    <h1 class="px-5 ma-5 mb-0">Cryptos</h1>
    <v-row no-gutters class="ma-5">
        <v-col v-for="crypto in cryptos"
        :key="crypto.id"
        cols="12"
        sm="4"
        >
        <CryptoCard :name="crypto.name" :price="crypto.price" :offer="crypto.offer" :slug="crypto.slug" :logo="crypto.logo.replace('public', 'http://127.0.0.1:8000/storage')"></CryptoCard>
        </v-col>
    </v-row>
</template>
