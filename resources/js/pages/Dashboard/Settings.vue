<script>
export default {
  data() {
    return {
        loading: false,
        name: "",
        email: "",
        password: ""
    };
  },
  methods: {
      login() {
        this.loading = true;
        this.$axios.put('/api/account', {
            name: this.name,
            email: this.email,
            password: this.password
        }).then(response => {this.$swal('Updated', {
            closeOnClickOutside: false
            })})
        .catch((error) => this.$swal(error.response.data.error, {
            closeOnClickOutside: false
            }));
        this.loading = false;
      }
    },
  mounted () {
    if(!localStorage.getItem('token')) {
        this.$router.push(`/login`)
    }
    this.$axios
      .get('/api/user')
      .then(response => {
        this.name = response.data.name;
        this.email = response.data.email;
    }).catch((error) => {this.$swal(error.response.data.error, {
                closeOnClickOutside: false
                });
                console.log(error.response.data)
            });
  }
};
</script>
<template>
    <h1 class="px-5 ma-5 mb-0">Dashboard</h1>
    <v-form @submit.prevent="login" class="pa-5 ma-5">
    <v-text-field v-model="name" label="Name" prepend-icon="mdi-account" variant="solo-filled"></v-text-field>
    <v-text-field v-model="email" label="Email" prepend-icon="mdi-email" variant="solo-filled"></v-text-field>
    <v-text-field v-model="password" label="New Password" prepend-icon="mdi-key" variant="solo-filled"></v-text-field>

    <v-btn
        :loading="loading"
        type="submit"
        block
        text="Submit"
        size="large"></v-btn>
    </v-form>
</template>