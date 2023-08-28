<script setup>
defineProps(['name', 'slug', 'logo', 'price', 'suffix', 'to'])
</script>

<template>
    <v-card class="py-2 px-5 rounded-lg" variant="tonal" :to="to">
        <div class="d-flex flex-nowrap">
            <v-icon class="pa-10" :icon="logo" size="50"></v-icon>
            <div class="d-flex flex-column flex-grow-1 pl-5">
                <h2 class="text-h6 font-weight-bold text-left">{{ name }}</h2>
                <div class="text-h6 text-left px-2 pt-1 d-flex flex-wrap justify-space-between text-primary">
                    <p class="text-right ml-2" v-if="price">{{ new Intl.NumberFormat('en-US').format(price) }} </p>
                    <p class="text-right ml-2" v-else>0 </p>
                    {{ suffix }}
                </div>
            </div>
        </div>
    </v-card>
</template>
