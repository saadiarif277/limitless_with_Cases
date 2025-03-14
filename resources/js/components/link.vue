<template>
    <x-inertia-link class="v-link text-primary-500 hover:text-primary-600">
        <slot />
    </x-inertia-link>
</template>

<script>
import { Link } from '@inertiajs/vue3';

export default {
    name: "LinkComponent",
    components: {
        "x-inertia-link": Link,
    },
};
</script>
