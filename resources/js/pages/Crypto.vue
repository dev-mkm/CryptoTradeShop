<script setup>

</script>
<script>
import axios from 'axios'

export default {
  data() {
    return {
        crypto: [],
        sellOffers: [],
        buyOffers: [],
        prices: [],
    };
  },
  mounted () {
      axios.all([
         axios
          .get('/api/cryptos/' + this.$route.params.id)
        ,axios
          .get('/api/cryptos/' + this.$route.params.id + '/offers', { params: { selling: 0 } })
        ,axios
          .get('/api/cryptos/' + this.$route.params.id + '/offers', { params: { selling: 1 } })
        ,axios
          .get('/api/cryptos/' + this.$route.params.id + '/prices')
      ])
      .then(axios.spread((data1, data2, data3, data4) => {
        this.crypto = data1.data.result
        this.buyOffers = data2.data.result
        this.sellOffers = data3.data.result
        this.prices = data4.data.result
      })).catch((error) => console.log(error))
  }
};
</script>

<template>
  <div class="d-flex align-center pa-10">
    <v-avatar :image="crypto.logo" size="150"></v-avatar><h1 class="text-h1 text-center pl-10">{{ crypto.name }}</h1>
  </div>
  <div class="d-flex pa-10 justify-space-between">
    <v-card title="Selling" variant="outlined" class="d-flex flex-column pa-3" width="40%">
      <v-table
      fixed-header
      height="300px"
    >
      <thead>
        <tr>
          <th class="text-left">
            Amount
          </th>
          <th class="text-left">
            Price
          </th>
        </tr>
      </thead>
      <tbody>
        <tr
          v-for="offer in sellOffers"
          :key="offer.id"
        >
          <td>{{ offer.amount }}</td>
          <td>{{ new Intl.NumberFormat('en-US').format(offer.price) }} Tomans</td>
        </tr>
      </tbody>
    </v-table>
    <v-btn variant="outlined">
      Buy Crypto
    </v-btn>
    </v-card>
    <v-card title="Buying" variant="outlined" class="d-flex flex-column pa-3" width="40%">
      <v-table
      fixed-header
      height="300px"
    >
      <thead>
        <tr>
          <th class="text-left">
            Amount
          </th>
          <th class="text-left">
            Price
          </th>
        </tr>
      </thead>
      <tbody>
        <tr
          v-for="offer in buyOffers"
          :key="offer.id"
        >
          <td>{{ offer.amount }}</td>
          <td>{{ new Intl.NumberFormat('en-US').format(offer.price) }} Tomans</td>
        </tr>
      </tbody>
    </v-table>
    <v-btn variant="outlined">
      Sell Crypto
    </v-btn>
    </v-card>
  </div>
</template>
