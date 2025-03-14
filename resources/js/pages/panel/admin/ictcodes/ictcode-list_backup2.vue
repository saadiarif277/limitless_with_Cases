<template>
    <v-inertia-head title="ICT Codes List" />

    <div class="h-full flex flex-col">
        <v-content-body class="border-b border-gray-200">
            <v-section-heading>
                <template #title>
                    ICT Codes Listing
                </template>

                <template #description>
                    Manage ICT Codes available in the application.
                </template>
                <template #actions>
                    <v-button :href="route('panel.admin.ictcodes.create')">
                        Create ICT Code
                    </v-button>
                </template>
            </v-section-heading>
        </v-content-body>

        <!-- Table for ICT Codes -->
        <v-app-model-table
            :columns="columns"
            :data="ictCodes.data || ictCodes"
            :links="ictCodes.links || []"
            :meta="ictCodes.meta || []"
        >
            <!-- Custom template for the 'code' column -->
            <template v-slot:column_id="{ row: ictCode }">
                <!-- <v-row class="font-semibold"> -->
                    {{ ictCode.code }}
                <!-- </v-row> -->
            </template>
        </v-app-model-table>
    </div>
</template>

<script>
import Layout from "@/layouts/panel/admin/index.vue";

export default {
    layout: Layout,
    props: {
        ictCodes: {
            type: [Object, Array], // Allow ictCodes to be an array or an object
            required: true,
            default: () => [],
        },

    },
    data() {
        return {
            columns: {
                code: {
                    label: "ICT Code",
                },
                description: {
                    label: "Description",
                },
                created_at: {
                    label: "Created At",
                },
            },
        };
    },
};
</script>
