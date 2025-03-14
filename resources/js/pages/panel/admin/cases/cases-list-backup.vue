<template>
    <v-inertia-head title="Cases Management" />

    <div class="h-full flex flex-col">
        <!-- Section Heading -->
        <v-content-body class="border-b border-gray-200">
            <v-section-heading>
                <template #title>
                    Cases
                </template>

                <template #description>
                    Manage Cases available in the application.
                </template>

                <template #actions>
                    <v-button :href="route('panel.admin.cases.create')">
                        Create Cases
                    </v-button>
                </template>
            </v-section-heading>
        </v-content-body>

        <!-- Status Filters -->
        <v-content-body class="border-b border-gray-200">
            <v-horizontal-menu class="grid grid-cols-2 sm:grid-cols-4 xl:grid-cols-8 gap-2">
                <v-horizontal-menu-item href="#" :active="!$page.props.query.caseStatusId"
                    @click.stop="selectCaseStatusId()">
                    All Cases
                </v-horizontal-menu-item>
                <v-horizontal-menu-item href="#" @click.stop="selectCaseStatusId()">
                    Pending
                </v-horizontal-menu-item>
                <v-horizontal-menu-item href="#" @click.stop="selectCaseStatusId()">
                    Reduction Request
                </v-horizontal-menu-item>
                <v-horizontal-menu-item href="#" @click.stop="selectCaseStatusId()">
                    Won
                </v-horizontal-menu-item>
                <v-horizontal-menu-item href="#" @click.stop="selectCaseStatusId()">
                    Lost
                </v-horizontal-menu-item>
            </v-horizontal-menu>
        </v-content-body>

        <!-- Cases Table -->
        <v-app-model-table :columns="columns" :data="cases.data" :links="cases.links" :meta="cases.meta">
            <!-- Case ID -->
            <template v-slot:column_case_id="{ row: caseItem }">
                <v-link :href="route('panel.admin.cases.show', { case: caseItem.case_id })" class="font-semibold">
                    CASE#{{ caseItem.case_id }}
                </v-link>
            </template>

            <!-- Patient Name -->
            <template v-slot:column_patient_name="{ row: caseItem }">
                <div>

                        {{ caseItem.patient_name }}

                </div>
            </template>

            <!-- Attorney Name -->
            <template v-slot:column_attorney_name="{ row: caseItem }">
                <div>

                        {{ caseItem.attorney_name }}

                </div>
            </template>
            <template v-slot:column_piloting_physician="{ row: caseItem }">
                <div>

                        {{ caseItem.piloting_physician }}

                </div>
            </template>
            <template v-slot:column_bill_type="{ row: caseItem }">
                <div>

                        {{ caseItem.bill_type }}

                </div>
            </template>
        </v-app-model-table>
    </div>
</template>

<script>
import Layout from "@/layouts/panel/admin/index.vue";

export default {
    layout: Layout,
    props: {
        cases: {
            type: Object,
            required: false,
            default: () => { },
        },

        links: {
            type: Object,
            required: false,
            default: () => { },
        },
    },
    data() {
        return {
            columns: {
                case_id: {
                    label: "Case ID",
                    align: "right",
                },
                patient_name: {
                    label: "Patient Name",
                },
                attorney_name: {
                    label: "Attorney Name",
                },
                piloting_physician: {
                    label: "Piloting Physician Name",
                },
                bill_type: {
                    label: "Billing Type",
                },

            },
        };
    },
    mounted() {
        // Log the cases when the component is mounted to see if data is correctly loaded
        console.log('Cases loaded:', this.cases);
    },
    updated() {
        // Log updated cases when data changes
        console.log('Updated Cases:', this.cases);
    },
    methods: {
        selectCaseStatusId(caseStatusId = undefined) {
            console.log('Fetching cases with status:', caseStatusId); // Log status selection
            this.$inertia.get(route(route().current()), {
                ...this.$page.props.query,
                caseStatusId: caseStatusId,
                page: undefined,
            }, {
                preserveState: true,
                preserveScroll: true,
            });
        }
    },
};
</script>
