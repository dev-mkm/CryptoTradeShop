<script>
export default {
  data() {
    return {
      cryptos: [],
      crypto: [],
      pay: false,
      dialog: false,
      amount: 0,
      code: '',
      id: 0,
    };
  },
  mounted () {
    if(!localStorage.getItem('token')) {
        this.$router.push(`/login`)
    }
    this.$axios
      .get('/api/cryptos/' + this.$route.params.id)
      .then(response => {this.crypto = response.data.result;
        this.$axios
      .get('/api/cryptos/' + response.data.result.id + '/ctransactions')
      .then(response => {this.cryptos = response.data.result})
      .catch((error) => {this.$swal(error.response.data.error, {
                closeOnClickOutside: false
                });
                console.log(error.response.data)
            });})
  },
  methods: {
      create() {
        this.dialog = true;
      },
      request() {
        this.loading = true;
            this.$axios.post('/api/cryptos/' + this.crypto.id + '/ctransactions', {
                amount: this.amount,
            }).then(response => {
              this.code = response.data.result.code;
              this.id = response.data.result.id;
              this.pay = true;
            })
            .catch((error) => {this.$swal(error.response.data.error, {
                closeOnClickOutside: false
                });
                console.log(error.response.data)
            });
        this.loading = false;
        
      },
      paying(payed) {
        this.$axios.put('/api/cpay', {
                id: this.id,
                code: this.code,
                status: payed ? 'success' : 'failed'
            }).then(response => this.$swal(payed ? 'Payment Successful' : 'Payment Failed', {
                closeOnClickOutside: false
                }))
            .catch((error) => {this.$swal(error.response.data.error, {
                closeOnClickOutside: false
                });
                console.log(error.response.data)
            });
            this.dialog = false;
            this.pay = false;
      }
    }
};
</script>
<template>
    <div class="d-flex pa-10 justify-space-between">
        <h1>{{ crypto.name }}</h1>
        <v-btn class="mt-3" prepend-icon="mdi-plus" @click="create">
            New Transaction
        </v-btn>
    </div>
    <v-dialog
      v-model="dialog"
      width="30%"
    >
    <v-card class="pa-7 rounded-lg">
        <h1 class="mb-5">{{ pay ? 'Complete Payment' : 'Add Money'}}</h1>
        <v-form v-if="!pay" @submit.prevent="request">
        <v-text-field v-model="amount" label="Amount" type="number" suffix="Tomans" prepend-icon="mdi-wallet" variant="solo-filled"></v-text-field>
        <v-btn
            :loading="loading"
            type="submit"
            block
            text="Submit"
            size="large"></v-btn>
        </v-form>
        <div v-else>
          <v-btn class="mt-3 w-100" color="green" prepend-icon="mdi-check" @click="paying(true)">
            complete
          </v-btn>
          <v-btn class="mt-3 w-100" color="red" prepend-icon="mdi-window-close" @click="paying(false)">
            Cancel
          </v-btn>
        </div>
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
            Amount
          </th>
          <th class="text-left">
            State
          </th>
          <th class="text-left">
            Date
          </th>
          <th class="text-left">
            In / Out
          </th>
        </tr>
      </thead>
      <tbody>
        <tr
          v-for="crypto in cryptos"
          :key="crypto.id"
        >
            <td>{{ new Intl.NumberFormat('en-US').format(crypto.amount) }}</td>
            <td>{{ crypto.state }}</td>
            <td>{{ crypto.date }}</td>
            <td><v-icon :icon="crypto.in_out ? 'mdi-arrow-down' : 'mdi-arrow-up'"></v-icon></td>
        </tr>
      </tbody>
    </v-table>
</template>