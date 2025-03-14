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
                    <v-button :href="route('panel.admin.referrals.create')">
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
                <v-link :href="route('panel.admin.referrals.show', { referral: referral.referral_id })" class="font-semibold">
                    REF#{{ referral.referral_id }}
                </v-link>
            </template>

            <template v-slot:column_patient_user="{ row: referral }">
                <div>
                    <v-link :href="route('panel.admin.users.show', { user: referral.patient_user.user_id })" class="font-semibold">
                        {{ referral.patient_user.name }}
                    </v-link>
                </div>
            </template>

            <template v-slot:column_attorney_user="{ row: referral }">
                <div>
                    <div>
                        <v-link :href="route('panel.admin.users.show', { user: referral.attorney_user.user_id })" class="font-semibold">
                            {{ referral.attorney_user.name }}
                        </v-link>
                    </div>

                    <template v-if="referral.attorney_user.law_firm">
                        <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4 mr-1">
                                <path fill-rule="evenodd" d="M12 2.25a.75.75 0 0 1 .75.75v.756a49.106 49.106 0 0 1 9.152 1 .75.75 0 0 1-.152 1.485h-1.918l2.474 10.124a.75.75 0 0 1-.375.84A6.723 6.723 0 0 1 18.75 18a6.723 6.723 0 0 1-3.181-.795.75.75 0 0 1-.375-.84l2.474-10.124H12.75v13.28c1.293.076 2.534.343 3.697.776a.75.75 0 0 1-.262 1.453h-8.37a.75.75 0 0 1-.262-1.453c1.162-.433 2.404-.7 3.697-.775V6.24H6.332l2.474 10.124a.75.75 0 0 1-.375.84A6.723 6.723 0 0 1 5.25 18a6.723 6.723 0 0 1-3.181-.795.75.75 0 0 1-.375-.84L4.168 6.241H2.25a.75.75 0 0 1-.152-1.485 49.105 49.105 0 0 1 9.152-1V3a.75.75 0 0 1 .75-.75Zm4.878 13.543 1.872-7.662 1.872 7.662h-3.744Zm-9.756 0L5.25 8.131l-1.872 7.662h3.744Z" clip-rule="evenodd" />
                            </svg>

                            <v-link :href="route('panel.admin.law-firms.show', { law_firm: referral.attorney_user.law_firm_id })">
                                <span class="italic">{{ referral.attorney_user.law_firm.name }}</span>
                            </v-link>
                        </div>
                    </template>

                    <template v-else>
                        <span class="italic">(No Law Firm)</span>
                    </template>
                </div>
            </template>

            <template v-slot:column_doctor_user="{ row: referral }">
                <div>
                    <div>
                        <v-link :href="route('panel.admin.users.show', { user: referral.doctor_user.user_id })" class="font-semibold">
                            {{ referral.doctor_user.name }}
                        </v-link>
                    </div>

                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4 mr-1">
                            <path d="M11.7 2.805a.75.75 0 0 1 .6 0A60.65 60.65 0 0 1 22.83 8.72a.75.75 0 0 1-.231 1.337 49.948 49.948 0 0 0-9.902 3.912l-.003.002c-.114.06-.227.119-.34.18a.75.75 0 0 1-.707 0A50.88 50.88 0 0 0 7.5 12.173v-.224c0-.131.067-.248.172-.311a54.615 54.615 0 0 1 4.653-2.52.75.75 0 0 0-.65-1.352 56.123 56.123 0 0 0-4.78 2.589 1.858 1.858 0 0 0-.859 1.228 49.803 49.803 0 0 0-4.634-1.527.75.75 0 0 1-.231-1.337A60.653 60.653 0 0 1 11.7 2.805Z" />
                            <path d="M13.06 15.473a48.45 48.45 0 0 1 7.666-3.282c.134 1.414.22 2.843.255 4.284a.75.75 0 0 1-.46.711 47.87 47.87 0 0 0-8.105 4.342.75.75 0 0 1-.832 0 47.87 47.87 0 0 0-8.104-4.342.75.75 0 0 1-.461-.71c.035-1.442.121-2.87.255-4.286.921.304 1.83.634 2.726.99v1.27a1.5 1.5 0 0 0-.14 2.508c-.09.38-.222.753-.397 1.11.452.213.901.434 1.346.66a6.727 6.727 0 0 0 .551-1.607 1.5 1.5 0 0 0 .14-2.67v-.645a48.549 48.549 0 0 1 3.44 1.667 2.25 2.25 0 0 0 2.12 0Z" />
                            <path d="M4.462 19.462c.42-.419.753-.89 1-1.395.453.214.902.435 1.347.662a6.742 6.742 0 0 1-1.286 1.794.75.75 0 0 1-1.06-1.06Z" />
                        </svg>

                        <template v-if="referral.doctor_user.medical_specialty">
                            <span class="italic">{{ referral.doctor_user.medical_specialty.name }}</span>
                        </template>

                        <template v-else>
                            <span class="italic">(No Medical Specialty)</span>
                        </template>
                    </div>
                </div>
            </template>

            <template v-slot:column_clinic="{ row: referral }">
                <template v-if="referral.clinic">
                    <v-link :href="route('panel.admin.clinics.show', { clinic: referral.clinic.clinic_id })" class="font-semibold">
                        {{ referral.clinic.name }}
                    </v-link>
                </template>

                <template v-else>
                    <span class="italic">No Clinic</span>
                </template>

                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4 mr-1">
                        <path fill-rule="evenodd" d="M5.75 2a.75.75 0 0 1 .75.75V4h7V2.75a.75.75 0 0 1 1.5 0V4h.25A2.75 2.75 0 0 1 18 6.75v8.5A2.75 2.75 0 0 1 15.25 18H4.75A2.75 2.75 0 0 1 2 15.25v-8.5A2.75 2.75 0 0 1 4.75 4H5V2.75A.75.75 0 0 1 5.75 2Zm-1 5.5c-.69 0-1.25.56-1.25 1.25v6.5c0 .69.56 1.25 1.25 1.25h10.5c.69 0 1.25-.56 1.25-1.25v-6.5c0-.69-.56-1.25-1.25-1.25H4.75Z" clip-rule="evenodd" />
                    </svg>

                    <template v-if="!referral.appointment || !referral.appointment.start_date">
                        <v-link :href="route('panel.admin.appointments.create', { referral_id: referral.referral_id })">
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
import Layout from "@/layouts/panel/admin/index.vue";

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
    data() {
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
                },
                doctor_user: {
                    label: "Doctor",
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
