<template>
    <div class="flex">
        <div class="fixed top-0 left-0 w-full h-full bg-black bg-opacity-75 transition-all duration-500 transform z-50" :class="isSidebarOpen ? 'translate-x-0 opacity-100' : '-translate-x-full opacity-0'" @click.self="toggleSidebar(false)">
            <div class="ml-72">
                <v-content-body>
                    <a href="#" class="block py-2 text-gray-50 hover:text-gray-200 font-extrabold" @click.stop="toggleSidebar(false)">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-8 h-8">
                            <path fill-rule="evenodd" d="M5.47 5.47a.75.75 0 0 1 1.06 0L12 10.94l5.47-5.47a.75.75 0 1 1 1.06 1.06L13.06 12l5.47 5.47a.75.75 0 1 1-1.06 1.06L12 13.06l-5.47 5.47a.75.75 0 0 1-1.06-1.06L10.94 12 5.47 6.53a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                        </svg>
                    </a>
                </v-content-body>
            </div>
        </div>

        <div class="flex flex-col w-72 h-screen justify-between bg-gray-100 fixed top-0 left-0 z-50  transition-all duration-500 transform shadow-lg" :class="isSidebarOpen ? 'translate-x-0' : '-translate-x-full'">
            <v-sidebar-menu @toggle-sidebar="toggleSidebar(false)" />
        </div>
    </div>

    <div class="w-full h-screen flex" v-if="true">
        <div class="hidden lg:flex flex-col w-72 h-screen justify-between bg-gray-100 border-r border-gray-200 flex-shrink-0">
            <v-sidebar-menu @toggle-sidebar="toggleSidebar(false)" />
        </div>

        <div class="w-full max-h-screen overflow-y-auto flex flex-col shadow-xl z-40">
            <div class="w-full h-20 flex items-center justify-between lg:hidden bg-gray-100 border-b border-gray-200 px-6">
                <div>
                    <!-- Toggle Sidebar -->
                    <a href="#" class="text-gray-700 hover:text-primary-500" @click.prevent="toggleSidebar">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6">
                            <path fill-rule="evenodd" d="M3 6.75A.75.75 0 0 1 3.75 6h16.5a.75.75 0 0 1 0 1.5H3.75A.75.75 0 0 1 3 6.75ZM3 12a.75.75 0 0 1 .75-.75h16.5a.75.75 0 0 1 0 1.5H3.75A.75.75 0 0 1 3 12Zm0 5.25a.75.75 0 0 1 .75-.75h16.5a.75.75 0 0 1 0 1.5H3.75a.75.75 0 0 1-.75-.75Z" clip-rule="evenodd" />
                        </svg>
                    </a>
                </div>

                <v-link :href="route('panel.admin.users.index')">
                    <v-app-logo />
                </v-link>

                <div class="opacity-0">
                    <v-button color="dark">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4">
                            <path fill-rule="evenodd" d="M3 6.75A.75.75 0 0 1 3.75 6h16.5a.75.75 0 0 1 0 1.5H3.75A.75.75 0 0 1 3 6.75ZM3 12a.75.75 0 0 1 .75-.75h16.5a.75.75 0 0 1 0 1.5H3.75A.75.75 0 0 1 3 12Zm0 5.25a.75.75 0 0 1 .75-.75h16.5a.75.75 0 0 1 0 1.5H3.75a.75.75 0 0 1-.75-.75Z" clip-rule="evenodd" />
                        </svg>
                    </v-button>
                </div>
            </div>
            
            <slot></slot>
        </div>
    </div>
</template>

<script>
import SidebarMenu from "./_sidebar-menu.vue";

export default {
    name: "LayoutPanelAdmin",
    components: {
        "v-sidebar-menu": SidebarMenu,
    },
    data() {
        return {
            isSidebarOpen: false,
        };
    },
    methods: {
        toggleSidebar(state = null) {
            if (state !== null) {
                this.isSidebarOpen = state;
                return;
            }

            this.isSidebarOpen = !this.isSidebarOpen;
        },
    },
};
</script>