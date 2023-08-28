<script setup>
import Balance from '../../components/Balance.vue';
</script>
<script>
export default {
  data() {
    return {
      cryptos: [],
      balance: [],
      usercrypto: [],
      role: false,
      is_admin: false,
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
      .get('/api/users')
      .then(response => {this.cryptos = response.data.result})
  },
  methods: {
      open(slug) {
        this.dialog = true;
        this.$axios
        .get('/api/users/'+ slug +'/crypto')
        .then(response => {
            this.usercrypto = response.data.result;
        })
        this.role = false;
      },
      Crole(slug) {
        this.dialog = true;
        this.uid = slug;
        this.role = true
      },
      request() {
        this.loading = true;
        this.$axios.post('/api/users/' + this.uid + '/role', {
          admin: this.is_admin,
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
      delet(slug) {
        this.$axios.delete('/api/users/' + slug, {
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
        <h1>Users</h1>
    </div>
    <v-dialog
      v-model="dialog"
      width="30%"
    >
    <v-card class="pa-7 rounded-lg">
        <h1 class="mb-5">{{ role ? 'Change Role' : 'Crypto Balance'}}</h1>
        <v-form v-if="role" @submit.prevent="request">
        <v-switch
          v-model="is_admin"
          hide-details
          true-value="1"
          false-value="0"
          inset
          label="Admin"
        ></v-switch>
        <v-btn
            :loading="loading"
            type="submit"
            block
            text="Submit"
            size="large"></v-btn>
        </v-form>
        <Balance v-for="crypto in usercrypto" :name="crypto.name" :price="crypto.balance" logo="mdi-currency-btc" :to="'/dashboard/crypto/'+ crypto.slug"></Balance>
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
            Name
          </th>
          <th class="text-left">
            Email
          </th>
          <th class="text-left">
            Balance
          </th>
          <th class="text-left">
            Admin
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
            <td>{{ crypto.name }}</td>
            <td>{{ crypto.email }}</td>
            <td><p v-if="crypto.balance">{{ new Intl.NumberFormat('en-US').format(crypto.balance) }} Tomans</p>
                <p v-else>--</p></td>
            <td><v-icon :icon="crypto.admin ? 'mdi-check' : 'mdi-window-close'"></v-icon></td>
            <td>
                <v-btn icon="mdi-pencil" variant="plain" color="white" @click="Crole(crypto.id)"></v-btn>
                <v-btn icon="mdi-wallet" variant="plain" color="white" @click="open(crypto.id)"></v-btn>
                <v-btn icon="mdi-delete" variant="plain" color='red' @click="delet(crypto.id)"></v-btn>
            </td>
        </tr>
      </tbody>
    </v-table>
</template>