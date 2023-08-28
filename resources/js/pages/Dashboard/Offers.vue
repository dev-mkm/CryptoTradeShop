<script>
export default {
  data() {
    return {
      cryptos: [],
      price: 0,
      amount: 0,
      uslug: '',
      uid: 0,
      dialog: false,
      loading: false
    };
  },
  mounted () {
    if(!localStorage.getItem('token')) {
        this.$router.push(`/login`)
    }
    this.$axios
      .get('/api/offers')
      .then(response => {this.cryptos = response.data.result});
  },
  methods: {
      open(slug, id) {
        this.dialog = true;
        this.uslug = slug;
        this.uid = id;
        this.$axios
        .get('/api/cryptos/' + slug + '/offers/' + id)
        .then(response => {
            this.price = response.data.result.price;
            this.amount = response.data.result.amount;
        })
      },
      request() {
        this.loading = true;
        this.$axios.put('/api/cryptos/' + this.uslug + '/offers/' + this.uid, {
            price: this.price,
            amount: this.amount,
        }).then(response => this.$swal('Updated', {
            closeOnClickOutside: false
            }))
        .catch((error) => {this.$swal(error.response.data.error, {
            closeOnClickOutside: false
            });
            console.log(error.response.data)
        });
        this.loading = false;
        this.dialog = false;
      },
      delet(slug, id) {
        this.$axios.delete('/api/cryptos/' + slug + '/offers/' + id, {
          }).then(response => this.$swal('Deleted', {
              closeOnClickOutside: false
              }))
          .catch((error) => {this.$swal(error.response.data.error, {
              closeOnClickOutside: false
              });
              console.log(error.response.data)
          });
      }
    },
};
</script>
<template>
    <div class="d-flex pa-10 justify-space-between">
        <h1>Offers</h1>
    </div>
    <v-dialog
      v-model="dialog"
      width="30%"
    >
    <v-card class="pa-7 rounded-lg">
        <h1 class="mb-5">Update</h1>
        <v-form @submit.prevent="request">
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
            Selling
          </th>
          <th class="text-left">
            Actions
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
            <td><v-icon :icon="crypto.selling ? 'mdi-check' : 'mdi-window-close'"></v-icon></td>
            <td>
                <v-btn icon="mdi-pencil" variant="plain" color="white" @click="open(crypto.crypto_id,crypto.id)"></v-btn>
                <v-btn icon="mdi-delete" variant="plain" color='red' @click="delet(crypto.crypto_id,crypto.id)"></v-btn>
            </td>
        </tr>
      </tbody>
    </v-table>
</template>