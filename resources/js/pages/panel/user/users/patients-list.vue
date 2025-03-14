<template>
    <v-inertia-head title="Patients" />

    <div class="h-full flex flex-col">
        <v-content-body class="border-b border-gray-200">
            <v-section-heading>
                <template #title>
                    User Management
                </template>

                <template #description>
                    Manage users available in the application.
                </template>
            </v-section-heading>
        </v-content-body>

        <v-app-model-table :columns="columns" :data="users.data" :links="users.links" :meta="users.meta">
            <template v-slot:column_user_id="{ row: user }">
                <v-link :href="route('panel.user.patients.show', { user: user.user_id })" class="font-semibold">
                    {{ user.name }}
                </v-link>
            </template>
        </v-app-model-table>
    </div>
</template>

<script>
import Layout from "@/layouts/panel/user/index.vue";

export default {
    layout: Layout,
    props: {
        users: {
            type: Object,
            required: false,
            default: () => {},
        },
        links: {
            type: Object,
            required: false,
            default: () => {},
        },
    },
    data(){
        return {
            columns: {
                user_id: {
                    label: "Name",
                },
                email: {
                    label: "Email Address",
                },
                roles: {
                    label: "Roles",
                },
            },
        };
    },
};
</script>
