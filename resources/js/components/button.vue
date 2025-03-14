<template>
    <!--
        Return an href element if there's an href.
    -->
    <a :href="href" :class="classes" v-bind="attrs" v-if="href && anchor">
        <slot></slot>
    </a>
    <!--
        Return an href element if there's an href.
    -->
    <x-inertia-link :href="href" :class="classes" v-bind="attrs" v-else-if="href && !anchor">
        <slot></slot>
    </x-inertia-link>

    <!--
        Return a button element if there's no href.
    -->
    <button :class="classes" v-bind="attrs" v-else>
        <slot></slot>
    </button>
</template>

<script>
import { Link } from '@inertiajs/vue3';

export default {
    name: "ButtonComponent",
    inheritAttrs: false,
    components: {
        "x-inertia-link": Link,
    },
    props: {
        anchor: {
            type: Boolean,
            required: false,
            default: () => false,
        },
        href: {
            type: String,
            required: false,
            default: () => null,
        },
        color: {
            type: String,
            required: false,
            default: () => "primary",
        },
        size: {
            type: String,
            required: false,
            default: () => "md",
        },
    },
    computed: {
        attrs() {
            return this.$attrs;
        },
    },
    setup(props) {
        const baseClasses = "v-button text-center inline-flex items-center justify-center border rounded-md shadow-sm focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed";

        const colors = {
            primary: "border-primary-500 hover:border-primary-600 text-white bg-primary-500 hover:bg-primary-600",
            secondary: "border-gray-200 bg-white hover:bg-gray-50 focus:ring-gray-200",
            danger: "border-red-500 text-white bg-red-500 hover:bg-red-600",
            white: "border-gray-200 hover:border-gray-800 text-gray-500 bg-white hover:bg-gray-800 hover:text-white",
            dark: "border-gray-800 hover:border-gray-900 bg-gray-800 hover:bg-gray-900  text-gray-100 hover:text-white",
        };

        const sizes = {
            xs: "px-2.5 py-1.5 text-xs font-medium",
            sm: "px-3 py-1.5 text-sm font-medium",
            md: "px-4 py-2.5 text-sm font-medium",
            lg: "px-4 py-2 text-base font-medium",
            xl: "px-5 py-2.5 text-lg font-medium",
        };

        const compiledClasses = () => {
            return `${baseClasses} ${colors[props.color]} ${sizes[props.size]}`;
        };

        return {
            classes: compiledClasses(),
        };
    },
};
</script>
