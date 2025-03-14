<template>
    <v-inertia-head :title="title" />
    
    <div class="w-full h-full flex items-center justify-center bg-gray-50">
        <div class="hidden md:flex w-full max-w-sm lg:w-96 h-full items-center justify-center bg-gray-100">
            <!-- -->
        </div>

        <div class="w-full grid grid-cols-1 gap-4 p-16">
            <div>
                <h1 class="text-4xl sm:text-5xl font-extrabold text-center md:text-left">
                    <span class="text-gray-700 uppercase">{{ title }}</span>
                </h1>

                <h2 class="text-lg sm:text-xl text-center md:text-left">
                    <span class="text-gray-500">
                        {{ description }}
                    </span>
                </h2>
            </div>

            <div class="text-center md:text-left">
                <v-button href="/login" color="primary">
                    Back to Home
                </v-button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({ status: Number })

const title = computed(() => {
    return {
        503: '503: Service Unavailable',
        500: '500: Server Error',
        419: '419: Page Expired',
        404: '404: Page Not Found',
        403: '403: Forbidden',
    }[props.status]
})

const description = computed(() => {
  return {
        503: 'Sorry, we are doing some maintenance. Please check back soon.',
        500: 'Whoops, something went wrong on our servers.',
        419: 'The page expired, please try again.',
        404: 'Sorry, the page you are looking for could not be found.',
        403: 'Sorry, you are forbidden from accessing this page.',
    }[props.status]
})
</script>