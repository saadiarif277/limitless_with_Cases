<template>
    <v-inertia-head title="Role Management" />

    <div class="h-full flex flex-col">
        <v-content-body class="border-b border-gray-200">
            <v-section-heading>
                <template #title>
                    Roles &amp; Permissions
                </template>

                <template #description>
                    Manage roles available in the application and their permissions.
                </template>

                <template #actions>
                    <v-button :href="route('panel.admin.roles.create')">
                        Create Role
                    </v-button>
                </template>
            </v-section-heading>
        </v-content-body>

        <v-app-model-table :columns="columns" :data="roles.data" :links="roles.links" :meta="roles.meta">
            <template v-slot:column_id="{ row: role }">
                <v-link :href="route('panel.admin.roles.show', { role: role.id })" class="font-semibold">
                    {{ role.name }}
                </v-link>
            </template>
        </v-app-model-table>
    </div>
</template>

<script>
import Layout from "@/layouts/panel/admin/index.vue";

export default {
    layout: Layout,
    props: {
        roles: {
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
                id: {
                    label: "Name",
                },
                users_count: {
                    label: "# of Users",
                    align: "right"
                },
            },
        };
    },
};
</script>
