<script setup lang="ts">
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import { Link } from "@inertiajs/vue3";
</script>

<template>
    <div
        class="min-h-screen flex flex-col sm:justify-center items-center sm:pt-0 bg-gray-100"
    >
        <div
            class="w-full sm:max-w-md px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg"
        >
            <slot />
        </div>
    </div>
</template>
