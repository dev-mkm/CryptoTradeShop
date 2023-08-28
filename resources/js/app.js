import './bootstrap';
import {createApp} from 'vue/dist/vue.esm-bundler.js';
import { createRouter, createWebHistory } from 'vue-router'
import App from './App.vue'
import Cryptos from './pages/Cryptos.vue'
import AdminCryptos from './pages/Admin/AdminCryptos.vue'
import Users from './pages/Admin/Users.vue'
import Stats from './pages/Admin/Stats.vue'
import Trades from './pages/Dashboard/Trades.vue'
import Offers from './pages/Dashboard/Offers.vue'
import Transactions from './pages/Dashboard/Transactions.vue'
import CTransactions from './pages/Dashboard/CTransactions.vue'
import Settings from './pages/Dashboard/Settings.vue'
import Dashboard from './pages/Dashboard.vue'
import Crypto from './pages/Crypto.vue'
import Login from './pages/Login.vue'
import Signup from './pages/Signup.vue'

const app = createApp(App);

//Router
const routes = [
    { path: '/dashboard', component: Dashboard },
    { path: '/dashboard/cryptos', component: AdminCryptos },
    { path: '/dashboard/transactions', component: Transactions },
    { path: '/dashboard/crypto/:id', component: CTransactions },
    { path: '/dashboard/users', component: Users },
    { path: '/dashboard/stats', component: Stats },
    { path: '/dashboard/trades', component: Trades },
    { path: '/dashboard/offers', component: Offers },
    { path: '/dashboard/settings', component: Settings },
    { path: '/cryptos/:id', component: Crypto },
    { path: '/cryptos', component: Cryptos },
    { path: '/login', component: Login },
    { path: '/signup', component: Signup },
]

//Router
const router = createRouter({
    history: createWebHistory(),
    routes, 
})

//Vuetify
import 'vuetify/styles'
import { createVuetify } from 'vuetify'
import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'
import { md2 } from 'vuetify/blueprints'
import '@mdi/font/css/materialdesignicons.css'

const vuetify = createVuetify({
    theme: {
        defaultTheme: 'dark'
    },
    icons: {
        defaultSet: 'mdi', // This is already the default value - only for display purposes
    },
    blueprint: md2,
    components,
    directives,
})

import VueSweetalert2 from 'vue-sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';
import 'vue-loading-overlay/dist/css/index.css';
import axios from 'axios';
import VueApexCharts from 'vue3-apexcharts';
axios.defaults.headers.common['Authorization'] = `Bearer ${localStorage.getItem('token')}`;
app.config.globalProperties.$axios = axios
app.config.globalProperties.$token = localStorage.getItem("token");
app.use(router).use(vuetify).use(VueSweetalert2).use(VueApexCharts).mount("#app");