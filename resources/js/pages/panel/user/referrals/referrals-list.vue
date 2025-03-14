<template>
    <v-inertia-head title="Referral Management" />

    <div class="h-full flex flex-col">
        <v-content-body class="border-b border-gray-200">
            <v-section-heading>
                <template #title>
                    Referrals
                </template>

                <template #description>
                    Manage referrals available in the application.
                </template>

                <template #actions>
                    <v-button :href="route('panel.user.referrals.create')">
                        Create Referral
                    </v-button>
                </template>
            </v-section-heading>
        </v-content-body>

        <v-content-body class="border-b border-gray-200">
            <v-horizontal-menu class="grid grid-cols-2 sm:grid-cols-4 xl:grid-cols-8 gap-2">
                <v-horizontal-menu-item href="#" :active="!$page.props.query.referralStatusId" @click.stop="selectReferralStatusId()">
                    All Referrals
                </v-horizontal-menu-item>

                <template v-for="(referralStatus, referralStatusIndex) in referralStatuses.data" :key="'referralStatus_' + referralStatusIndex">    
                    <v-horizontal-menu-item href="#" :active="$page.props.query.referralStatusId == referralStatus.referral_status_id" @click.stop="selectReferralStatusId(referralStatus.referral_status_id)">
                        {{ referralStatus.name }}
                    </v-horizontal-menu-item>
                </template>
            </v-horizontal-menu>
        </v-content-body>

        <v-app-model-table :columns="columns" :data="referrals.data" :links="referrals.links" :meta="referrals.meta">
            <template v-slot:column_referral_id="{ row: referral }">
                <v-link :href="route('panel.user.referrals.show', { referral: referral.referral_id })" class="font-semibold">
                    REF#{{ referral.referral_id }}
                </v-link>
            </template>

            <template v-slot:column_patient_user="{ row: referral }">
                <div>
                    <v-link :href="route('panel.user.patients.show', { user: referral.patient_user.user_id })" class="font-semibold">
                        {{ referral.patient_user.name }}
                    </v-link>
                </div>
            </template>

            <template v-slot:column_clinic="{ row: referral }">
                <template v-if="referral.clinic">
                    {{ referral.clinic.name }}
                </template>

                <template v-else>
                    <span class="italic">No Clinic</span>
                </template>

                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4 mr-1">
                        <path fill-rule="evenodd" d="M5.75 2a.75.75 0 0 1 .75.75V4h7V2.75a.75.75 0 0 1 1.5 0V4h.25A2.75 2.75 0 0 1 18 6.75v8.5A2.75 2.75 0 0 1 15.25 18H4.75A2.75 2.75 0 0 1 2 15.25v-8.5A2.75 2.75 0 0 1 4.75 4H5V2.75A.75.75 0 0 1 5.75 2Zm-1 5.5c-.69 0-1.25.56-1.25 1.25v6.5c0 .69.56 1.25 1.25 1.25h10.5c.69 0 1.25-.56 1.25-1.25v-6.5c0-.69-.56-1.25-1.25-1.25H4.75Z" clip-rule="evenodd" />
                    </svg>

                    <template v-if="!referral.appointment || !referral.appointment.start_date">
                        <v-link :href="route('panel.user.appointments.create', { referral_id: referral.referral_id })">
                            Create Appointment
                        </v-link>
                    </template>

                    <template v-else>
                        {{ referral.appointment_start_date }}
                    </template>
                </div>
            </template>
        </v-app-model-table>
    </div>
</template>

<script>
import Layout from "@/layouts/panel/user/index.vue";

export default {
    layout: Layout,
    props: {
        referrals: {
            type: Object,
            required: false,
            default: () => {},
        },
        referralStatuses: {
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
                referral_id: {
                    label: "Reference",
                    align: "right",
                },
                referral_date: {
                    label: "Referral Date",
                },
                patient_user: {
                    label: "Patient",
                },
                attorney_user: {
                    label: "Attorney",
                    formatter: (row) => row.attorney_user.name,
                },
                doctor_user: {
                    label: "Doctor",
                    formatter: (row) => row.doctor_user.name,
                },
                clinic: {
                    label: "Clinic & Appointment",
                },
                referral_status_id: {
                    label: "Status",
                    formatter: (row) => row.referral_status.name,
                },
            },
        };
    },
    methods: {
        selectReferralStatusId(referralStatusId = undefined) {
            this.$inertia.get(route(route().current()), {
                ...this.$page.props.query,
                referralStatusId: referralStatusId,
                page: undefined,
            }, {
                preserveState: true,
                preserveScroll: true,
            });
        }
    },
};
</script>
