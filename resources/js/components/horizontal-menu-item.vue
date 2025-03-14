<template>
    <x-inertia-link :class="`${baseClasses} ${active || $page.url.startsWith(getPathName($attrs.href)) ? activeClasses : inactiveClasses}`">
        <slot></slot>
    </x-inertia-link>
</template>

<script>
import { Link } from '@inertiajs/vue3';

export default {
    name: "HorizontalMenuItemComponent",
    components: {
        "x-inertia-link": Link,
    },
    props: {
        active: {
            type: Boolean,
            required: false,
            default: () => false,
        },
    },
    data() {
        return {
            baseClasses: "v-horizontal-menu-item inline-block rounded-md flex items-center justify-center px-4 py-2.5 font-medium text-sm border whitespace-nowrap truncate focus:outline-none focus:ring-0 focus:ring-offset-0",
            activeClasses: "bg-primary-500 text-white border-primary-500",
            inactiveClasses: "bg-gray-50 text-gray-500  border-transparent hover:border-gray-800 hover:bg-gray-800 hover:text-white",
        };
    },
    methods: {
        getPathName(str) {
            if (str == "#") return "#";
            return (new URL(str)).pathname;
        },
    },
};
</script>
