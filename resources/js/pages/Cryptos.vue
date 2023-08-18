<script setup>
import CryptoCard from '../components/CryptoCard.vue'
</script>
<script>
import axios from 'axios'

export default {
  data() {
    return {
      cryptos: [],
    };
  },
  mounted () {
    axios
      .get('/api/cryptos')
      .then(response => {this.cryptos = response.data.result})
      .catch((error) => console.log(error))
  }
};
</script>

<template>
    <h1 class="px-5">Cryptos</h1>
    <v-row no-gutters>
        <v-col v-for="crypto in cryptos"
        :key="crypto.id"
        cols="12"
        sm="4"
        >
        <CryptoCard :name="crypto.name" :price="crypto.price" :offer="crypto.offer" :slug="crypto.slug" :logo="crypto.logo"></CryptoCard>
        </v-col>
    </v-row>
</template>
