<template>
    <v-content-body v-if="$page.props.auth.user">
        <v-section-heading>
            <template #title>
                <v-link :href="route('panel.user.profile.edit')" class="font-medium" @click="$emit('toggleSidebar')">
                    {{ $page.props.auth.user.name }}
                </v-link>
            </template>
            
            <template #description>
                {{ $page.props.auth.user.email.length > 20 ? $page.props.auth.user.email.substring(0, 20) + '...' : $page.props.auth.user.email }}
            </template>

            <template #actions>
                <v-button :href="route('auth.logout')" method="post" as="button" color="dark" size="xs">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-white">
                        <path fill-rule="evenodd" d="M12 2.25a.75.75 0 0 1 .75.75v9a.75.75 0 0 1-1.5 0V3a.75.75 0 0 1 .75-.75ZM6.166 5.106a.75.75 0 0 1 0 1.06 8.25 8.25 0 1 0 11.668 0 .75.75 0 1 1 1.06-1.06c3.808 3.807 3.808 9.98 0 13.788-3.807 3.808-9.98 3.808-13.788 0-3.808-3.807-3.808-9.98 0-13.788a.75.75 0 0 1 1.06 0Z" clip-rule="evenodd" />
                    </svg>
                </v-button>
            </template>
        </v-section-heading>
    </v-content-body>
</template>

<script>
export default {
    name: "PanelUserMenuFragment",
    emits: ["toggleSidebar"],
};
</script>
