<template>
    <x-inertia-link :class="`${baseClasses} ${$page.url.startsWith(getPathName($attrs.href)) ? activeClasses : inactiveClasses}`">
        <slot></slot>
    </x-inertia-link>
</template>

<script>
import { Link } from '@inertiajs/vue3';

export default {
    name: "VerticalMenuItemComponent",
    components: {
        "x-inertia-link": Link,
    },
    data() {
        return {
            baseClasses: "v-vertical-menu-item rounded-md flex items-center justify-start px-4 py-2.5 font-medium text-sm border whitespace-nowrap truncate focus:outline-none focus:ring-0 focus:ring-offset-0",
            activeClasses: "bg-primary-500 text-white border-primary-500",
            inactiveClasses: "text-gray-500  border-transparent hover:border-primary-500 hover:bg-primary-500 hover:text-white",
        };
    },
    methods: {
        getPathName(str) {
            return (new URL(str)).pathname;
        },
    },
};
</script>
