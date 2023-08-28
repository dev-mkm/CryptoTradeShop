<script setup>
import Balance from '../components/Balance.vue';
import Section from '../components/Section.vue';
</script>
<script>
export default {
  data() {
    return {
      cryptos: [],
      user: []
    };
  },
  mounted () {
    if(!localStorage.getItem('token')) {
        this.$router.push(`/login`)
    }
    this.$axios
      .get('/api/users/me/crypto')
      .then(response => {this.cryptos = response.data.result; console.log(response.data)})
      .catch((error) => {this.$swal(error.response.data.error, {
            closeOnClickOutside: false
            });
            console.log(error.response.data)
          });
    this.$axios
      .get('/api/user')
      .then(response => {this.user = response.data})
  }
};
</script>
<template>
    <h1 class="px-5 ma-5 mb-0">Welcome {{ user.name }}</h1>
    <v-row no-gutters class="ma-5">
        <v-col
        class="pa-4"
        cols="12"
        sm="4"
        >
        <Balance name="Balance" :price="user.balance" logo="mdi-cash" suffix="Tomans" to="/dashboard/transactions"></Balance>
        </v-col>
        <v-col v-for="crypto in cryptos"
        class="pa-4"
        cols="12"
        sm="4"
        >
        <Balance :name="crypto.name" :price="crypto.balance" logo="mdi-currency-btc" :to="'/dashboard/crypto/'+ crypto.slug"></Balance>
        </v-col>
    </v-row>
    <h1 class="px-5 ma-5 mb-0">Actions</h1>
    <v-row no-gutters class="ma-5">
        <v-col
        v-if="user.admin"
        class="pa-4"
        cols="12"
        sm="4"
        >
        <Section name="Cryptos" logo="mdi-currency-btc" to="/dashboard/cryptos"></Section>
        </v-col>
        <v-col
        v-if="user.admin"
        class="pa-4"
        cols="12"
        sm="4"
        >
        <Section name="Users" logo="mdi-account-multiple" to="/dashboard/users"></Section>
        </v-col>
        <v-col
        v-if="user.admin && false"
        class="pa-4"
        cols="12"
        sm="4"
        >
        <Section name="Stats" logo="mdi-chart-line-variant" to="/dashboard/stats"></Section>
        </v-col>
        <v-col
        class="pa-4"
        cols="12"
        sm="4"
        >
        <Section name="Trades" logo="mdi-history" to="/dashboard/trades"></Section>
        </v-col>
        <v-col
        class="pa-4"
        cols="12"
        sm="4"
        >
        <Section name="Offers" logo="mdi-wallet" to="/dashboard/offers"></Section>
        </v-col>
        <v-col
        class="pa-4"
        cols="12"
        sm="4"
        >
        <Section name="Account" logo="mdi-account" to="/dashboard/settings"></Section>
        </v-col>
    </v-row>
</template>