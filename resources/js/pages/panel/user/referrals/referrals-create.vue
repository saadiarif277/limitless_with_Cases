<template>
    <v-inertia-head title="Create Referral" />

    <div class="h-full flex flex-col">
        <v-content-body class="border-b border-gray-200">
            <v-section-heading>
                <template #title>
                    Create Referral
                </template>

                <template #description>
                    Fill up the form below to create a new referral for the application.
                </template>
            </v-section-heading>
        </v-content-body>

        <x-referral-create-form
            :attorneys="attorneys"
            :doctors="doctors"
            :document-categories="documentCategories"
            :patients="patients"
            :referral-reasons="referralReasons"
            :referral-statuses="referralStatuses"
            :referral-states="referralStates"
            :states="states"
            :list-route="'panel.user.referrals.index'"
            :store-route="'panel.user.referrals.store'"
        />
    </div>
</template>

<script>
import Layout from "@/layouts/panel/user/index.vue";
import ReferralCreateForm from "@/components/application/referral/referral-create-form.vue";

export default {
    name: "ReferralsCreate",
    layout: Layout,
    components: {
        "x-referral-create-form": ReferralCreateForm,
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
        referralStates: {
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
