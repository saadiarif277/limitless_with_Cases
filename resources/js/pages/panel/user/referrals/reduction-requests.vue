<template>
    <div class="h-full space-y-6">
        <!-- Header -->
        <div class="bg-white shadow-sm rounded-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900">Reduction Requests</h1>
                    <p class="mt-1 text-sm text-gray-600">
                        Review and respond to reduction requests from attorneys
                    </p>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="bg-white shadow-sm rounded-lg p-6">
            <!-- Success/Error Messages -->
            <div v-if="$page?.props?.flash?.success" class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ $page.props.flash.success }}</span>
            </div>

            <div v-if="$page?.props?.flash?.error" class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ $page.props.flash.error }}</span>
            </div>

            <!-- No Reduction Requests -->
            <div v-if="reductionRequests.length === 0" class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No pending reduction requests</h3>
                <p class="mt-1 text-sm text-gray-500">
                    You don't have any pending reduction requests at the moment.
                </p>
            </div>

            <!-- Reduction Requests List -->
            <div v-else class="space-y-6">
                <div v-for="request in reductionRequests" :key="request.id" class="bg-white shadow rounded-lg border border-gray-200">
                    <div class="px-6 py-4">
                        <!-- Header -->
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">
                                    Reduction Request #{{ request.id }}
                                </h3>
                                <p class="text-sm text-gray-500">
                                    Requested on {{ formatDate(request.created_at) }}
                                </p>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                Pending Response
                            </span>
                        </div>

                        <!-- Request Details -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <h4 class="text-sm font-medium text-gray-700 mb-2">Case Information</h4>
                                <div class="space-y-2 text-sm text-gray-600">
                                    <div>
                                        <span class="font-medium">Case ID:</span>
                                        <span class="ml-2">{{ request.case?.case_id || 'N/A' }}</span>
                                    </div>
                                    <div>
                                        <span class="font-medium">Patient:</span>
                                        <span class="ml-2">{{ request.referral?.patientUser?.name || 'N/A' }}</span>
                                    </div>
                                    <div>
                                        <span class="font-medium">Attorney:</span>
                                        <span class="ml-2">{{ request.referral?.attorneyUser?.name || 'N/A' }}</span>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-700 mb-2">Reduction Details</h4>
                                <div class="space-y-2 text-sm text-gray-600">
                                    <div>
                                        <span class="font-medium">Requested Amount:</span>
                                        <span class="ml-2 text-lg font-semibold text-red-600">${{ request.amount }}</span>
                                    </div>
                                    <div>
                                        <span class="font-medium">Referral Status:</span>
                                        <span class="ml-2 capitalize">{{ request.referral_status }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- File Attachment -->
                        <div v-if="request.file_path" class="mb-6">
                            <h4 class="text-sm font-medium text-gray-700 mb-2">Supporting Documents</h4>
                            <div class="flex items-center space-x-2">
                                <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
                                </svg>
                                <a :href="`/storage/${request.file_path}`" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm">
                                    View Document
                                </a>
                            </div>
                        </div>

                        <!-- Response Form -->
                        <div class="border-t border-gray-200 pt-6">
                            <h4 class="text-sm font-medium text-gray-700 mb-4">Your Response</h4>

                            <form @submit.prevent="submitDecision(request)" class="space-y-4">
                                <!-- Decision -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Decision *
                                    </label>
                                    <div class="space-y-2">
                                        <label class="flex items-center">
                                            <input
                                                type="radio"
                                                v-model="request.decision"
                                                value="accepted"
                                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                                            >
                                            <span class="ml-2 text-sm text-gray-700">Accept Reduction</span>
                                        </label>
                                        <label class="flex items-center">
                                            <input
                                                type="radio"
                                                v-model="request.decision"
                                                value="rejected"
                                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                                            >
                                            <span class="ml-2 text-sm text-gray-700">Reject Reduction</span>
                                        </label>
                                    </div>
                                </div>

                                <!-- Counter Offer (if rejected) -->
                                <div v-if="request.decision === 'rejected'">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Counter Offer (Optional)
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 sm:text-sm">$</span>
                                        </div>
                                        <input
                                            type="number"
                                            v-model="request.counter_offer"
                                            step="0.01"
                                            min="0"
                                            class="pl-7 block w-full border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                            placeholder="0.00"
                                        >
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500">
                                        Suggest an alternative amount if you'd like to negotiate
                                    </p>
                                </div>

                                <!-- Notes -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Additional Notes (Optional)
                                    </label>
                                    <textarea
                                        v-model="request.notes"
                                        rows="3"
                                        class="block w-full border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                        placeholder="Add any additional comments or explanations..."
                                    ></textarea>
                                </div>

                                <!-- Submit Button -->
                                <div class="flex justify-end space-x-3">
                                    <button
                                        type="button"
                                        @click="resetForm(request)"
                                        class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                    >
                                        Reset
                                    </button>
                                    <button
                                        type="submit"
                                        :disabled="!request.decision || request.processing"
                                        class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                                    >
                                        <span v-if="request.processing">Processing...</span>
                                        <span v-else>Submit Decision</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Layout from "@/layouts/panel/user/index.vue";
import { useForm } from "@inertiajs/vue3";

export default {
    name: "ReductionRequests",
    layout: Layout,
    props: {
        reductionRequests: {
            type: Array,
            required: true,
            default: () => [],
        },
    },
    data() {
        return {
            // Each request will have its own form state
        };
    },
    methods: {
        formatDate(dateString) {
            if (!dateString) return "N/A";
            return new Date(dateString).toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        },

        async submitDecision(request) {
            if (!request.decision) {
                this.$toast().error('Please select a decision');
                return;
            }

            // Set processing state
            request.processing = true;

            try {
                const response = await this.$inertia.put(
                    route('panel.user.referrals.update-reduction-decision', request.id),
                    {
                        doctor_decision: request.decision,
                        counter_offer: request.counter_offer || null,
                        notes: request.notes || null,
                    }
                );

                if (response.status === 200) {
                    this.$toast().success('Decision submitted successfully');
                    // Remove the request from the list since it's no longer pending
                    const index = this.reductionRequests.findIndex(r => r.id === request.id);
                    if (index > -1) {
                        this.reductionRequests.splice(index, 1);
                    }
                }
            } catch (error) {
                this.$toast().error('Error submitting decision. Please try again.');
                console.error('Error:', error);
            } finally {
                request.processing = false;
            }
        },

        resetForm(request) {
            request.decision = null;
            request.counter_offer = null;
            request.notes = '';
        },
    },

    mounted() {
        // Initialize form state for each request
        this.reductionRequests.forEach(request => {
            request.decision = null;
            request.counter_offer = null;
            request.notes = '';
            request.processing = false;
        });
    },
};
</script>

<style scoped>
/* Add any custom styles here */
</style>
