<template>
    <component :is="'h' + size" :class="compiledClasses">
        <slot></slot>
    </component>
</template>

<script>
export default {
    name: "HeadingComponent",
    props: {
        size: {
            type: Number,
            required: true,
            validator: (value) => (value >= 1 && value <= 6),
        },
    },
    computed: {
        compiledClasses() {
            const baseClasses = "v-heading text-gray-800";

            const sizeClasses = {
                1: "text-4xl font-bold",     // h1
                2: "text-3xl font-semibold", // h2
                3: "text-2xl font-semibold", // h3
                4: "text-xl font-medium",    // h4
                5: "text-lg font-medium",   // h5
                6: "text-base font-normal",  // h6
            };

            return `${baseClasses} ${sizeClasses[this.size]}`;
        }
    },
};
</script>
