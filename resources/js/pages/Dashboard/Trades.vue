<script>
export default {
  data() {
    return {
      cryptos: []
    };
  },
  mounted () {
    if(!localStorage.getItem('token')) {
        this.$router.push(`/login`)
    }
    this.$axios
      .get('/api/trades')
      .then(response => {this.cryptos = response.data.result})
      .catch((error) => {this.$swal(error.response.data.error, {
                closeOnClickOutside: false
                });
                console.log(error.response.data)
            });
  },
};
</script>
<template>
    <div class="d-flex pa-10 justify-space-between">
        <h1>Trades</h1>
    </div>
    <v-table
      class="pa-10"
      fixed-header
      height="500px"
    >
      <thead>
        <tr>
          <th class="text-left">
            Crypto
          </th>
          <th class="text-left">
            Amount
          </th>
          <th class="text-left">
            Price
          </th>
          <th class="text-left">
            Buyer
          </th>
        </tr>
      </thead>
      <tbody>
        <tr
          v-for="crypto in cryptos"
          :key="crypto.id"
        >
            <td>
                <v-btn variant="plain" color="white" :to="'/cryptos/' + crypto.crypto_id">{{ crypto.crypto_name }}</v-btn>
            </td>
            <td>{{ new Intl.NumberFormat('en-US').format(crypto.amount) }}</td>
            <td>{{ new Intl.NumberFormat('en-US').format(crypto.price) }} Tomans</td>
            <td><v-icon :icon="crypto.is_buyer ? 'mdi-check' : 'mdi-window-close'"></v-icon></td>
        </tr>
      </tbody>
    </v-table>
</template>