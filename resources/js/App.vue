<template>
  <v-app>
    <v-container class="flex-column fill-height" style="max-width: 1200px;">
      <v-card class="ma-1 rounded-lg w-100" variant="tonal">
        <div class="d-flex justify-space-between">
          <v-btn v-if="false" color="white" to="/login" class="ma-1" icon="mdi-login" variant="text" size="x-large" density="comfortable"></v-btn>
          <v-menu v-else transition="slide-x-transition" :close-on-content-click="false">
            <template v-slot:activator="{ props }">
              <v-btn color="white" class="ma-1" icon="mdi-account" variant="text" size="x-large" density="comfortable" v-bind="props"></v-btn>
            </template>

            <v-list class="pa-0">
              <v-list-item class="pa-0"><v-btn to="/dashboard" class="px-3" prepend-icon="mdi-wallet" variant="plain" color="white">Dashboard</v-btn></v-list-item>
              <v-list-item class="pa-0"><v-btn to="/logout" class="px-3" prepend-icon="mdi-logout" variant="plain" color="white">Logout</v-btn></v-list-item>
            </v-list>
          </v-menu>
        <div>
          <v-btn color="white" to="/cryptos" variant="text" class="h-100">Cryptos</v-btn>
          <v-btn color="white" to="/" variant="text" class="h-100">Home</v-btn>
        </div>
        </div>
      </v-card>
      <v-card class="ma-3 pa-7 rounded-lg w-100">
        <router-view></router-view>
      </v-card>
    </v-container>
  </v-app>
</template>