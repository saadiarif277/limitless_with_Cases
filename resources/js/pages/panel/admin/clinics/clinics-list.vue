<template>
    <v-inertia-head title="Clinic Management" />

    <div class="h-full flex flex-col">
        <v-content-body class="border-b border-gray-200">
            <v-section-heading>
                <template #title>
                    Clinics
                </template>

                <template #description>
                    Manage clinics available in the application.
                </template>

                <template #actions>
                    <v-button :href="route('panel.admin.clinics.create')">
                        Create Clinic
                    </v-button>
                </template>
            </v-section-heading>
        </v-content-body>

        <v-app-model-table :columns="columns" :data="clinics.data" :links="clinics.links" :meta="clinics.meta">
            <template v-slot:column_clinic_id="{ row: clinic }">
                <v-link :href="`/admin/clinics/${clinic.clinic_id}`" class="font-semibold">
                    {{ clinic.name }}
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
        clinics: {
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
                clinic_id: {
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
                price_read: {
                    label: "Read Price",
                    formatter: (row) => `$${parseInt(row.price_read || 0).toFixed(2)}`,
                    align: "right",
                },
                price_scan: {
                    label: "Scan Price",
                    formatter: (row) => `$${parseInt(row.price_scan || 0).toFixed(2)}`,
                    align: "right",
                },
            },
        };
    },
};
</script>
