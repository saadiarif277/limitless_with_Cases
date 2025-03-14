<template>
    <v-inertia-head title="State Management" />

    <div class="h-full flex flex-col">
        <v-content-body class="border-b border-gray-200">
            <v-section-heading>
                <template #title>
                    States &amp; Document Types
                </template>

                <template #description>
                    Manage states available in the application and their document_types.
                </template>
            </v-section-heading>
        </v-content-body>

        <v-app-model-table :columns="columns" :data="states.data" :links="states.links" :meta="states.meta">
            <template v-slot:column_id="{ row: state }">
                <v-link :href="route('panel.admin.states.show', { state: state.state_id })" class="font-semibold">
                    {{ state.name }}
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
        states: {
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
