<script setup>
defineProps(['name', 'slug', 'logo', 'price', 'offer'])
</script>

<template>
    <v-card class="pa-5 rounded-xl ma-5" variant="tonal" :to="'/cryptos/' + slug">
        <div class="d-flex flex-nowrap">
            <v-avatar :image="logo" size="80"></v-avatar>
            <v-divider vertical :thickness="2" class="border-opacity-50 mx-4"></v-divider>
            <div class="d-flex flex-column flex-grow-1">
                <h2 class="text-h5 font-weight-bold text-left">{{ name }}</h2>
                <div class="text-h6 text-left px-2 pt-3 d-flex flex-wrap justify-space-between">
                    Price
                    <p class="text-right ml-2" v-if="price">{{ new Intl.NumberFormat('en-US', {
                        style: 'currency',
                        currency: 'USD',
                    }).format(price) }}$</p>
                    <p class="text-right ml-2" v-else>--</p>
                </div>
                <div class="text-h6 text-left px-2 pt-3 d-flex flex-wrap justify-space-between">
                    Offer
                    <p class="text-right ml-2" v-if="offer">{{ new Intl.NumberFormat('en-US').format(offer) }} Tomans</p>
                    <p class="text-right ml-2" v-else>--</p>
                </div>
            </div>
        </div>
    </v-card>
</template>
