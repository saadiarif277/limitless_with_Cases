<template>
    <x-inertia-link :href="href" class="block group overflow-hidden bg-gray-50 hover:bg-primary-500 text-gray-700 hover:text-gray-100 font-medium flex items-center justify-between cursor-pointer rounded-md">
        <div class="h-full p-5 sm:px-6 bg-primary-500 flex items-center justify-between border-r-2 border-gray-200">
            <slot name="icon">ICON</slot>
        </div>

        <div class="w-full p-5 sm:px-6 flex items-center justify-between">
            <div class="text-sm uppercase">
                <slot></slot>
            </div>
            <div>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 group-hover:text-white">
                    <path fill-rule="evenodd" d="M16.28 11.47a.75.75 0 0 1 0 1.06l-7.5 7.5a.75.75 0 0 1-1.06-1.06L14.69 12 7.72 5.03a.75.75 0 0 1 1.06-1.06l7.5 7.5Z" clip-rule="evenodd" />
                </svg>
            </div>
        </div>
    </x-inertia-link>
</template>

<script>
import { Link } from '@inertiajs/vue3';

export default {
    name: "ButtonWidgetComponent",
    components: {
        "x-inertia-link": Link,
    },
    props: {
        href: {
            type: String,
            required: false,
            default: () => null,
        },
    },
};
</script>
