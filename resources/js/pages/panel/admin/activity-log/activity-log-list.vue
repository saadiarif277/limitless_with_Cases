
<template>
    <v-inertia-head title="Activity Log" />

    <div class="h-full flex flex-col">
        <v-content-body class="border-b border-gray-200">
            <v-section-heading>
                <template #title>
                    Activity Log
                </template>

                <template #description>
                    Browse through user activity across the application.
                </template>
            </v-section-heading>
        </v-content-body>

        <v-app-model-table :columns="columns" :data="activityLog.data" :links="activityLog.links" :meta="activityLog.meta">
            <!--
                Dynamic Slots Here
            -->
        </v-app-model-table>
    </div>
</template>

<script>
import Layout from "@/layouts/panel/admin/index.vue";

export default {
    layout: Layout,
    props: {
        activityLog: {
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
                description: {
                    label: "Description",
                },
                created_at: {
                    label: "Created At",
                    align: "right"
                },
            },
        };
    },
};
</script>
