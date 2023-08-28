<script>
export default {
  data() {
    return {
      cryptos: [],
      Uslug: '',
      type: '',
      slug: '',
      name: '',
      price: '',
      dialog: false,
      loading: false
    };
  },
  mounted () {
    if(!localStorage.getItem('token')) {
        this.$router.push(`/login`)
    }
    this.$axios
      .get('/api/cryptos')
      .then(response => {this.cryptos = response.data.result})
  },
  methods: {
      open(slug) {
        this.dialog = true;
        this.Uslug = slug;
        this.$axios
        .get('/api/cryptos/' + slug)
        .then(response => {
            this.name = response.data.result.name;
            this.slug = response.data.result.slug;
        })
        this.type = 'Update'
      },
      Cphoto(slug) {
        this.dialog = true;
        this.Uslug = slug;
        this.type = 'Update Photo'
      },
      Cprice(slug) {
        this.dialog = true;
        this.Uslug = slug;
        this.$axios
        .get('/api/cryptos/' + slug)
        .then(response => {
            this.price = response.data.result.price;
        })
        this.type = 'Price'
      },
      create() {
        this.Uslug = '';
        this.dialog = true;
        this.type = 'Create'
      },
      request() {
        this.loading = true;
        if(this.type == 'Create') {
            var form = document.getElementById('form');
            var formData = new FormData(form);
            this.$axios.post('/api/cryptos/', formData).then(response => this.$swal('Created', {
                closeOnClickOutside: false
                }))
            .catch((error) => {this.$swal(error.response.data.error, {
                closeOnClickOutside: false
                });
                console.log(error.response.data)
            });
        } else if (this.type == 'Update') {
            this.$axios.put('/api/cryptos/' + this.Uslug + '', {
                slug: this.slug,
                name: this.name,
            }).then(response => this.$swal('Updated', {
                closeOnClickOutside: false
                }))
            .catch((error) => {this.$swal(error.response.data.error, {
                closeOnClickOutside: false
                });
                console.log(error.response.data)
            });
        } else if (this.type == 'Update Photo') {
            var form = document.getElementById('form');
            var formData = new FormData(form);
            this.$axios.post('/api/cryptos/' + this.Uslug + '/photo', formData).then(response => this.$swal('Updated Photo', {
                closeOnClickOutside: false
                }))
            .catch((error) => {this.$swal(error.response.data.error, {
                closeOnClickOutside: false
                });
                console.log(error.response.data)
            });
        } else if (this.type == 'Price') {
            this.$axios.post('/api/cryptos/' + this.Uslug + '/prices', {
                price: this.price,
            }).then(response => this.$swal('Updated Price', {
                closeOnClickOutside: false
                }))
            .catch((error) => {this.$swal(error.response.data.error, {
                closeOnClickOutside: false
                });
                console.log(error.response.data)
            });
        }
        this.loading = false;
        this.dialog = false;
      },
      delet(slug) {
        this.$axios.delete('/api/cryptos/' + slug, {
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
        <h1>Cryptos</h1>
        <v-btn class="mt-3" prepend-icon="mdi-plus" @click="create">
            New Crypto
        </v-btn>
    </div>
    <v-dialog
      v-model="dialog"
      width="30%"
    >
    <v-card class="pa-7 rounded-lg">
        <h1 class="mb-5">{{ type }}</h1>
        <v-form @submit.prevent="request" id="form">
        <v-text-field v-if="type == 'Create' || type == 'Update'" v-model="name" label="Name" name="name" prepend-icon="mdi-currency-btc" variant="solo-filled"></v-text-field>
        <v-text-field v-if="type == 'Create' || type == 'Update'" v-model="slug" label="Slug" name="slug" prepend-icon="mdi-link" variant="solo-filled"></v-text-field>
        <v-file-input v-if="type == 'Create' || type == 'Update Photo'" accept="image/*" label="Photo" name="photo" variant="solo-filled"></v-file-input>
        <v-text-field v-if="type == 'Price'" v-model="price" label="Price" type="number" prefix="$" prepend-icon="mdi-currency-usd" variant="solo-filled"></v-text-field>
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
            Name
          </th>
          <th class="text-left">
            Price
          </th>
          <th class="text-left">
            Offer
          </th>
          <th class="text-left">
            Actions
          </th>
        </tr>
      </thead>
      <tbody>
        <tr
          v-for="crypto in cryptos"
          :key="crypto.slug"
        >
            <td>{{ crypto.name }}</td>
            <td><p v-if="crypto.price">{{ new Intl.NumberFormat('en-US', {
                        style: 'currency',
                        currency: 'USD',
                    }).format(crypto.price) }}</p>
                    <p v-else>--</p></td>
            <td><p v-if="crypto.offer">{{ new Intl.NumberFormat('en-US').format(crypto.offer) }} Tomans</p>
                <p v-else>--</p></td>
            <td>
                <v-btn icon="mdi-pencil" variant="plain" color="white" @click="open(crypto.slug)"></v-btn>
                <v-btn icon="mdi-panorama" variant="plain" color="white" @click="Cphoto(crypto.slug)"></v-btn>
                <v-btn icon="mdi-credit-card-multiple" variant="plain" color="white" @click="Cprice(crypto.slug)"></v-btn>
                <v-btn icon="mdi-delete" variant="plain" color='red' @click="delet(crypto.slug)"></v-btn>
            </td>
        </tr>
      </tbody>
    </v-table>
</template>