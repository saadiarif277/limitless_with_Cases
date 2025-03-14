<template>
    <v-inertia-head title="Create Referral" />

    <div class="h-full flex flex-col">
        <v-content-body class="border-b border-gray-200">
            <v-section-heading>
                <template #title>
                    Create Cases
                </template>

                <template #description>
                    Fill up the form below to create a new Case for the application.
                </template>
            </v-section-heading>
        </v-content-body>

        <x-case-referral-create-form
            :attorneys="attorneys"
            :doctors="doctors"
            :document-categories="documentCategories"
            :medical-specialties="medicalSpecialties"
            :patients="patients"
            :referral-reasons="referralReasons"
            :referral-statuses="referralStatuses"
            :states="states"
            :list-route="'panel.admin.referrals.index'"
            :store-route="'panel.admin.referrals.store'"
        />
    </div>
</template>

<script>
import Layout from "@/layouts/panel/admin/index.vue";
import CaseReferralCreateForm from "@/components/application/referral/case-referral-create-form.vue";

export default {
    name: "ReferralsCreate",
    layout: Layout,
    components: {
        "x-case-referral-create-form": CaseReferralCreateForm,
    },
    props: {
        attorneys: {
            type: Object,
            required: true,
            default: () => {},
        },
        doctors: {
            type: Object,
            required: true,
            default: () => {},
        },
        documentCategories: {
            type: Object,
            required: false,
            default: () => {},
        },
        medicalSpecialties: {
            type: Object,
            required: false,
            default: () => {},
        },
        patients: {
            type: Object,
            required: true,
            default: () => {},
        },
        referralReasons: {
            type: Object,
            required: false,
            default: () => {},
        },
        referralStatuses: {
            type: Object,
            required: false,
            default: () => {},
        },
        states: {
            type: Object,
            required: false,
            default: () => {},
        },
    },
};
</script>
