<template>
    <div class="h-full min-h-screen flex bg-gray-100">
        <div class="hidden lg:flex w-full min-h-full items-center justify-center bg-cover" style="background-image: url('/assets/image/auth_bg.png')">
            <!-- -->
        </div>

        <div class="flex-1 flex flex-col justify-center py-12 px-4 sm:px-6 lg:flex-none lg:px-20 xl:px-24 bg-gray-50 border-l border-gray-200">
            <div class="mx-auto w-full max-w-sm lg:w-96">
                <div class="pb-6">
                    <v-link href="/login">
                        <img src="/assets/image/logo.svg" class="h-24 mx-auto w-auto">
                    </v-link>
                </div>
                
                <slot></slot>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "LayoutsAuth",
};
</script>
