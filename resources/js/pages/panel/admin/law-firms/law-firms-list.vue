<template>
    <v-inertia-head title="Law Firm Management" />

    <div class="h-full flex flex-col">
        <v-content-body class="border-b border-gray-200">
            <v-section-heading>
                <template #title>
                    Law Firms
                </template>

                <template #description>
                    Manage law firms available in the application.
                </template>

                <template #actions>
                    <v-button :href="route('panel.admin.law-firms.create')">
                        Create Law Firm
                    </v-button>
                </template>
            </v-section-heading>
        </v-content-body>

        <v-app-model-table :columns="columns" :data="lawFirms.data" :links="lawFirms.links" :meta="lawFirms.meta">
            <template v-slot:column_law_firm_id="{ row: lawFirm }">
                <v-link :href="`/admin/law-firms/${lawFirm.law_firm_id}`" class="font-semibold">
                    {{ lawFirm.name }}
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
        lawFirms: {
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
                law_firm_id: {
                    label: "Name",
                },
                address_line_1: {
                    label: "Address",
                    formatter: (row) => {
                        let address = "";

                        if (row.address_line_1) address = `${address} ${row.address_line_1}`;
                        if (row.address_line_2) address = `${address} ${row.address_line_2}`;
                        if (row.city) address = `${address}, ${row.city}`;
                        if (row.state && row.state.name) address = `${address}, ${row.state.name}`;
                        if (row.zip_code) address = `${address}, ${row.zip_code}`;

                        return address;
                    },
                },
            },
        };
    },
};
</script>
