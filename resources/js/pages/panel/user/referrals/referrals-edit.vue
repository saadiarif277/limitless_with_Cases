<template>
    <v-inertia-head :title="`Editing 'REF#${referral.data.referral_id}' referral.`" />

    <div class="h-full flex flex-col">
        <v-content-body class="border-b border-gray-200">
            <v-section-heading>
                <template #title>
                    Editing 'REF#{{ referral.data.referral_id }}' referral.
                </template>

                <template #description>
                    Change and/or modify the details for the referral.
                </template>

                <template #actions>
                    <template v-if="referral">
                        <div class="flex gap-2">
                            <v-button color="white" :href="route('panel.user.appointments.create', { referral_id: referral.data.referral_id })">
                                Create Appointment
                            </v-button>

                            <v-button color="danger" @click.stop="deleteRequest" :disabled="deleteForm.processing">
                                Delete Referral
                            </v-button>
                        </div>
                    </template>
                </template>
            </v-section-heading>
        </v-content-body>

        <x-referral-edit-form
            :allow-source-edit="false"
            :attorneys="attorneys"
            :doctors="doctors"
            :document-categories="documentCategories"
            :patients="patients"
            :referral="referral"
            :referral-reasons="referralReasons"
            :referral-statuses="referralStatuses"
            :sources="sources"
            :states="states"
            :list-route="'panel.user.referrals.index'"
            :update-route="'panel.user.referrals.update'"
            :destroy-document-route="'panel.user.documents.destroy'"
        />
    </div>
</template>

<script>
import Layout from "@/layouts/panel/user/index.vue";
import ReferralEditForm from "@/components/application/referral/referral-edit-form.vue";
import { useForm } from "@inertiajs/vue3";

export default {
    name: "ReferralsEdit",
    layout: Layout,
    components: {
        "x-referral-edit-form": ReferralEditForm,
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
        referral: {
            type: Object,
            required: false,
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
        sources: {
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
    data() {
        return {
            deleteForm: useForm({
                // ...
            }),
        };
    },
    methods: {
        deleteRequest() {
            this.$alert().confirm(() => {
                return new Promise((resolve, reject) => {
                    this.deleteForm.delete(this.route('panel.user.referrals.destroy', { referral: this.referral.data.referral_id }), {
                        onSuccess: () => {
                            this.$toast().success("Referral deleted successfully!");
                            resolve();
                        },
                        onError: (errors) => {
                            Object.keys(errors).forEach((key) => {
                                const error = errors[key];
                                this.$toast().error(error);
                            });

                            reject();
                        },
                    });
                });
            });
        },
    },
};
</script>
