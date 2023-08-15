import './bootstrap';
import {createApp} from 'vue/dist/vue.esm-bundler.js';
import { createRouter, createWebHistory } from 'vue-router'
import App from './App.vue'
import Home from './pages/Home.vue'
import Dashboard from './pages/Dashboard.vue'

const app = createApp(App);

//Router
const routes = [
    { path: '/', component: Home },
    { path: '/about', component: Dashboard },
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

app.use(router).use(vuetify).mount("#app");