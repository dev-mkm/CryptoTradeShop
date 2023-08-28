<script setup>
defineProps(['name', 'to', 'logo'])
</script>

<template>
    <v-btn :prepend-icon="logo" stacked rounded="lg" variant="tonal" :to="to" size="x-large" color="white" class="w-100">
        {{ name }}
    </v-btn>
</template>