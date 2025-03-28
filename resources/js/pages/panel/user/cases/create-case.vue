<template>
    <v-inertia-head title="Create Case" />

    <div class="h-full space-y-6">
        <!-- Page Header -->
        <div class="bg-dark shadow-sm rounded-lg p-6 pb-8">
            <v-section-heading>
                <template #title>
                    <div class="flex items-center gap-2">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            class="w-5 h-5 text-primary-500"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M12 4.5v15m7.5-7.5h-15"
                            />
                        </svg>
                        <span class="text-primary-500">Create Case</span>
                    </div>
                </template>
                <template #description>
                    Create a new case and manage its details
                </template>
            </v-section-heading>
        </div>

        <!-- Error Message -->
        <div v-if="error" class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ error }}</span>
        </div>

        <!-- Case Creation Form -->
        <div class="bg-white shadow-sm rounded-lg p-6">
            <case-create-form
                :attorneys="formattedAttorneys"
                :doctors="formattedDoctors"
                :patients="formattedPatients"
                :states="formattedStates"
                :all-cpt-codes="formattedCptCodes"
                :referrals="formattedReferrals"
                :user-role="userRole"
                :user-id="userId"
                :list-route="listRoute"
                :store-route="storeRoute"
            />
        </div>
    </div>
</template>

<script>
import Layout from "@/layouts/panel/user/index.vue";
import CaseCreateForm from "@/components/application/case-create-form.vue";

export default {
    name: "CreateCase",
    layout: Layout,
    components: {
        CaseCreateForm,
    },
    props: {
        attorneys: {
            type: Object,
            required: true,
            default: () => ({ data: [] }),
        },
        doctors: {
            type: Object,
            required: true,
            default: () => ({ data: [] }),
        },
        patients: {
            type: Object,
            required: true,
            default: () => ({ data: [] }),
        },
        states: {
            type: Object,
            required: true,
            default: () => ({ data: [] }),
        },
        allCptCodes: {
            type: Object,
            required: true,
            default: () => ({ data: [] }),
        },
        referrals: {
            type: Object,
            required: true,
            default: () => ({ data: [] }),
        },
        userRole: {
            type: String,
            required: true,
            default: "",
        },
        userId: {
            type: Number,
            required: true,
            default: 0,
            validator: (value) => value !== null,
        },
        listRoute: {
            type: String,
            default: "panel.user.cases.index",
        },
        storeRoute: {
            type: String,
            default: "panel.user.cases.store",
        },
        error: {
            type: String,
            default: null,
        },
    },
    computed: {
        formattedAttorneys() {
            return {
                data: Array.isArray(this.attorneys.data) ? this.attorneys.data : []
            };
        },
        formattedDoctors() {
            return {
                data: Array.isArray(this.doctors.data) ? this.doctors.data : []
            };
        },
        formattedPatients() {
            return {
                data: Array.isArray(this.patients.data) ? this.patients.data : []
            };
        },
        formattedStates() {
            return {
                data: Array.isArray(this.states.data) ? this.states.data : []
            };
        },
        formattedCptCodes() {
            return {
                data: Array.isArray(this.allCptCodes.data) ? this.allCptCodes.data : []
            };
        },
        formattedReferrals() {
            return {
                data: Array.isArray(this.referrals.data) ? this.referrals.data : []
            };
        }
    }
};
</script>
