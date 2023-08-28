<script>
export default {
  data() {
    return {
        loading: false,
        username: "",
        password: ""
    };
  },
  methods: {
      login() {
        this.loading = true;
        this.$axios.post('/api/login', {
            email: this.username,
            password: this.password
        }).then(response => {localStorage.setItem("token", response.data.result); this.$router.push(`/dashboard`)})
        .catch((error) => {this.$swal(error.response.data.error, {
            closeOnClickOutside: false
            })
            console.log(error);
        });
        this.loading = false;
        }
    },
  mounted () {
    if(localStorage.getItem('token')) {
        this.$router.push(`/dashboard`)
    }
  }
};
</script>

<template>
    <v-parallax
    src="https://cdn.vuetifyjs.com/images/backgrounds/vbanner.jpg"
  >
    <div class="d-flex flex-column fill-height justify-center align-center text-white">
        <v-card class="pa-7 w-50 rounded-lg">
            <h1 class="mb-5">Login</h1>
            <v-form @submit.prevent="login">
            <v-text-field v-model="username" label="Email" prepend-icon="mdi-email" variant="solo-filled"></v-text-field>
            <v-text-field v-model="password" label="Password" prepend-icon="mdi-key" variant="solo-filled"></v-text-field>

            <v-btn
                :loading="loading"
                type="submit"
                block
                text="Submit"
                size="large"></v-btn>
            </v-form>
            <v-btn
                to="/signup"
                class="mt-5"
                block
                size="small" variant="text">signup instead?</v-btn>
        </v-card>
    </div>
  </v-parallax>
</template>