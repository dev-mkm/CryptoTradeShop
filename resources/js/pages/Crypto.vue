<script setup>

</script>
<script>
import axios from 'axios'

export default {
  data() {
    return {
        loading: false,
        crypto: [],
        sellOffers: [],
        buyOffers: [],
        prices: [],
        pricesdates: [],
        dialog: false,
        selling: false,
        price: 0,
        amount: 0
    };
  },
  mounted () {
      this.$axios.all([
      this.$axios
          .get('/api/cryptos/' + this.$route.params.id)
        ,this.$axios
          .get('/api/cryptos/' + this.$route.params.id + '/offers', { params: { selling: 0 } })
        ,this.$axios
          .get('/api/cryptos/' + this.$route.params.id + '/offers', { params: { selling: 1 } })
        ,this.$axios
          .get('/api/cryptos/' + this.$route.params.id + '/prices')
      ])
      .then(this.$axios.spread((data1, data2, data3, data4) => {
        this.crypto = data1.data.result
        this.buyOffers = data2.data.result
        this.sellOffers = data3.data.result
        this.prices = data4.data.result.map(function (item) {
          return item.price
        })
        this.pricesdates = data4.data.result.map(function (item) {
          return item.time
        })
      }))
  },
  methods: {
      open(selling) {
        if(localStorage.getItem('token')){
          this.dialog = true;
          this.selling = selling
        }
      },
      login() {
        this.loading = true;
        this.$axios.post('/api/cryptos/' + this.$route.params.id + '/offers', {
          price: this.price,
          amount: this.amount,
          selling: this.selling
        }).then(response => this.$swal('Offer Submited', {
            closeOnClickOutside: false
            }))
        .catch((error) => {this.$swal(error.response.data.error, {
            closeOnClickOutside: false
            });
            console.log(error.response.data)
          });
        this.loading = false;
        this.dialog = false;
      }
    },
};
</script>

<template>
  <div class="d-flex align-center pa-10">
    <v-avatar :image="crypto.logo ? crypto.logo.replace('public', 'http://127.0.0.1:8000/storage') : ''" size="150"></v-avatar><h1 class="text-h1 text-center pl-10">{{ crypto.name }}</h1>
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
    <v-btn variant="outlined" @click="open(false)">
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
    <v-btn variant="outlined" @click="open(true)">
      Sell Crypto
    </v-btn>
    </v-card>
  </div>
  <v-dialog
      v-model="dialog"
      width="50%"
      style="right: -30%;"
    >
      <v-card class="pa-7 w-50 rounded-lg">
        <h1 class="mb-5">{{ selling ? 'Sell' : 'Buy' }}</h1>
        <v-form @submit.prevent="login">
        <v-text-field v-model="price" label="Price" type="number" suffix="Tomans" prepend-icon="mdi-wallet" variant="solo-filled"></v-text-field>
        <v-text-field v-model="amount" label="Amount" type="number" prepend-icon="mdi-checkbox-multiple-blank-circle" variant="solo-filled"></v-text-field>

        <v-btn
            :loading="loading"
            type="submit"
            block
            text="Submit"
            size="large"></v-btn>
        </v-form>
      </v-card>
    </v-dialog>
  <div id="chart" class="px-15">
    <apexchart type="area" height="350" :options="{
            chart: {
              height: 350,
              type: 'area',
              foreColor: '#ccc',
              toolbar: {
                show: false
              },
            },
            dataLabels: {
              enabled: false
            },
            stroke: {
              curve: 'smooth'
            },
            xaxis: {
              type: 'datetime',
              categories: pricesdates
            },
            tooltip: {
              x: {
                format: 'dd/MM/yy HH:mm'
              },
              y: {
                formatter: function(value, { series, seriesIndex, dataPointIndex, w }) {
                  return new Intl.NumberFormat('en-US', {
                        style: 'currency',
                        currency: 'USD',
                    }).format(value);
                }
              },
              theme: 'dark'
            },
          }" :series="[{
            name: 'Price',
            data: prices
          }]"></apexchart>
  </div>
</template>
