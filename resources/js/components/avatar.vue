<template>
    <div
        :class="[
            'inline-flex items-center justify-center rounded-full font-medium',
            sizeClasses[size] || sizeClasses.md,
            customClass
        ]"
        :style="customStyle"
    >
        <slot>
            <span v-if="text">{{ text }}</span>
            <img v-else-if="src" :src="src" :alt="alt" class="w-full h-full object-cover rounded-full" />
        </slot>
    </div>
</template>

<script>
export default {
    name: 'Avatar',
    props: {
        size: {
            type: String,
            default: 'md',
            validator: (value) => ['xs', 'sm', 'md', 'lg', 'xl'].includes(value)
        },
        src: {
            type: String,
            default: null
        },
        alt: {
            type: String,
            default: ''
        },
        text: {
            type: String,
            default: ''
        },
        customClass: {
            type: String,
            default: ''
        },
        customStyle: {
            type: Object,
            default: () => ({})
        }
    },
    computed: {
        sizeClasses() {
            return {
                xs: 'w-6 h-6 text-xs',
                sm: 'w-8 h-8 text-sm',
                md: 'w-10 h-10 text-base',
                lg: 'w-12 h-12 text-lg',
                xl: 'w-16 h-16 text-xl'
            }
        }
    }
}
</script>
