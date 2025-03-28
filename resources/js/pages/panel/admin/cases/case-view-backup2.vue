<template>
    <v-inertia-head title="Case Details" />

    <div class="h-full space-y-6">
        <!-- Patient Information -->
        <div class="bg-white shadow-sm rounded-lg p-6">
            <v-content-body>
                <v-section-heading>
                    <template #title>
                        <div class="flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-primary-500">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 8.25 21 12m0 0-3.75 3.75M21 12H3" />
                            </svg>
                            <span class="text-primary-500">Patient</span> Information
                        </div>
                    </template>
                </v-section-heading>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                    <div>
                        <v-heading :size="6" class="font-medium">{{ caseDetails.patient?.name || 'N/A' }}</v-heading>
                    </div>
                    <div class="flex flex-col gap-1">
                        <v-paragraph>Email: {{ caseDetails.patient?.email || 'N/A' }}</v-paragraph>
                        <v-paragraph>Address: {{ caseDetails.patient?.address_line_1 || 'N/A' }}, {{ caseDetails.patient?.address_line_2 || 'N/A' }}</v-paragraph>
                    </div>
                </div>
            </v-content-body>
        </div>

        <!-- Attorney Information -->
        <div class="bg-white shadow-sm rounded-lg p-6">
            <v-content-body>
                <v-section-heading>
                    <template #title>
                        <div class="flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-primary-500">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 8.25 21 12m0 0-3.75 3.75M21 12H3" />
                            </svg>
                            <span class="text-primary-500">Attorney</span> Information
                        </div>
                    </template>
                </v-section-heading>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                    <div>
                        <v-heading :size="6" class="font-medium">{{ caseDetails.attorney?.name || 'N/A' }}</v-heading>
                    </div>
                    <div class="flex flex-col gap-1">
                        <v-paragraph>Email: {{ caseDetails.attorney?.email || 'N/A' }}</v-paragraph>
                    </div>
                </div>
            </v-content-body>
        </div>

        <!-- Billing Information -->
        <div class="bg-white shadow-sm rounded-lg p-6">
            <v-content-body>
                <v-section-heading>
                    <template #title>
                        <div class="flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-primary-500">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 8.25 21 12m0 0-3.75 3.75M21 12H3" />
                            </svg>
                            <span class="text-primary-500">Billing</span> Information
                        </div>
                    </template>
                </v-section-heading>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                    <div>
                        <v-heading :size="6" class="font-medium">Billing Details</v-heading>
                    </div>
                    <div class="flex flex-col gap-1">
                        <v-paragraph>Billing Type: {{ caseDetails.billing_type || 'N/A' }}</v-paragraph>
                        <v-paragraph>ICD-10 Code: {{ icdCodeDescription || 'N/A' }}</v-paragraph>
                        <v-paragraph>Service Billed: {{ caseDetails.service_billed || 'N/A' }}</v-paragraph>
                        <div v-if="cptCodeDescriptions.length">
                            <v-paragraph>CPT Codes:</v-paragraph>
                            <ul class="list-disc pl-5">
                                <li v-for="description in cptCodeDescriptions" :key="description">{{ description }}</li>
                            </ul>
                        </div>
                        <v-paragraph v-else>CPT Codes: N/A</v-paragraph>
                    </div>
                </div>
            </v-content-body>
        </div>

        <!-- Policy Limit Information -->
        <div class="bg-white shadow-sm rounded-lg p-6">
            <v-content-body>
                <v-section-heading>
                    <template #title>
                        <div class="flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-primary-500">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 8.25 21 12m0 0-3.75 3.75M21 12H3" />
                            </svg>
                            <span class="text-primary-500">Policy Limit</span> Information
                        </div>
                    </template>
                </v-section-heading>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                    <div>
                        <v-heading :size="6" class="font-medium">Policy Details</v-heading>
                    </div>
                    <div class="flex flex-col gap-1">
                        <v-paragraph>Policy Limit: {{ caseDetails.policy_limit_info?.policy_limit || 'N/A' }}</v-paragraph>
                        <v-paragraph>PIP: {{ caseDetails.policy_limit_info?.pip || 'N/A' }}</v-paragraph>
                        <v-paragraph>Defendant Insurance: {{ caseDetails.policy_limit_info?.defendant_insurance || 'N/A' }}</v-paragraph>
                        <v-paragraph>Plaintiff Insurance: {{ caseDetails.policy_limit_info?.plaintiff_insurance || 'N/A' }}</v-paragraph>
                        <v-paragraph>Commercial Case: {{ caseDetails.policy_limit_info?.commercial_case ? 'Yes' : 'No' }}</v-paragraph>
                        <v-paragraph>Type of Accident: {{ caseDetails.policy_limit_info?.type_of_accident || 'N/A' }}</v-paragraph>
                    </div>
                </div>
            </v-content-body>
        </div>

        <!-- Referrals Section -->
        <div class="bg-white shadow-sm rounded-lg p-6">
            <v-content-body>
                <v-section-heading>
                    <template #title>
                        <div class="flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-primary-500">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 8.25 21 12m0 0-3.75 3.75M21 12H3" />
                            </svg>
                            <span class="text-primary-500">Referrals</span>
                        </div>
                    </template>
                </v-section-heading>

                <!-- Referral Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mt-4">
                    <!-- Iterate over referrals -->
                    <div v-for="referral in referrals" :key="referral.referral_id" class="bg-gray-50 p-4 rounded-lg shadow-sm">
                        <div class="flex flex-col gap-2">
                            <v-heading :size="6" class="font-medium">
                                <!-- Ensure referral.referral_id is valid before generating the route -->
                                <v-link v-if="referral.referral_id" :href="route('panel.admin.referrals.show', { referral: referral.referral_id })" class="text-primary-500 hover:underline">
                                    Referral #{{ referral.referral_id }}
                                </v-link>
                                <span v-else class="text-gray-500">Referral ID Missing</span>
                            </v-heading>
                            <!-- Format created_at in a human-readable manner -->
                            <v-paragraph>Created: {{ formatDate(referral.created_at) || 'N/A' }}</v-paragraph>

                            <!-- Display Reduction Request Details -->
                            <div v-if="referral.reduction_requests && referral.reduction_requests.length > 0" class="mt-4">
                                <v-heading :size="6" class="font-medium">Reduction Requests</v-heading>
                                <div v-for="reductionRequest in referral.reduction_requests" :key="reductionRequest.id" class="mt-2">
                                    <v-paragraph>Amount: ${{ reductionRequest.amount }}</v-paragraph>
                                    <v-paragraph  class="mt-2">
                Status:
                <span :class="getStatusBadgeClass(reductionRequest.referral_status)">
                    {{ getStatusText(reductionRequest.referral_status) }}
                </span>
            </v-paragraph>
            <v-paragraph  class="mt-4">
                Doctor Decision:
                <span :class="getDoctorDecisionBadgeClass(reductionRequest.doctor_decision)">
                    {{ getDoctorDecisionText(reductionRequest.doctor_decision) }}
                </span>
            </v-paragraph>
                                    <v-paragraph v-if="reductionRequest.counter_offer">
                                        Counter Offer: ${{ reductionRequest.counter_offer }}
                                    </v-paragraph>
                                    <v-paragraph v-if="reductionRequest.file_path">
                                        <a :href="reductionRequest.file_link" target="_blank" class="text-primary-500 hover:underline">Download File</a>
                                    </v-paragraph>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex gap-2 mt-2">
                                <v-button @click="markReferralComplete(referral.referral_id)" class="bg-green-500 text-white flex-1">
                                    Complete
                                </v-button>
                                <!-- Disable the Reduction button if a reduction request already exists -->
                                <v-button
                                    @click="openReductionModal(referral.referral_id)"
                                    :disabled="referral.reduction_requests && referral.reduction_requests.length > 0"
                                    class="bg-orange-500 text-white flex-1"
                                    :class="{ 'opacity-50 cursor-not-allowed': referral.reduction_requests && referral.reduction_requests.length > 0 }"
                                >
                                    Reduction
                                </v-button>
                                <v-button @click="markReferralLost(referral.referral_id)" class="bg-red-500 text-white flex-1">
                                    Lost
                                </v-button>
                            </div>
                        </div>
                    </div>
                </div>
            </v-content-body>
        </div>

        <!-- CMS 1500 Form Download Button -->
        <div class="bg-white shadow-sm rounded-lg p-6">
            <v-content-body>
                <v-section-heading>
                    <template #title>
                        <div class="flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-primary-500">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 8.25 21 12m0 0-3.75 3.75M21 12H3" />
                            </svg>
                            <span class="text-primary-500">CMS 1500 Form</span>
                        </div>
                    </template>
                </v-section-heading>

                <div class="mt-4">
                    <v-button @click="downloadCMS1500Form" color="primary">
                        Download CMS 1500 Form
                    </v-button>
                </div>
            </v-content-body>
        </div>
    </div>

    <!-- Modal for Reduction -->
    <v-dialog v-model="isModalOpen" persistent max-width="500px">
        <v-card>
            <v-card-title>
                <span class="headline">Send for Reduction</span>
            </v-card-title>

            <v-card-text>
                <!-- Form for validation -->
                <v-form v-model="isFormValid" ref="reductionForm">
                    <!-- Amount for Reduction Field -->
                    <v-form-group class="col-span-full sm:col-span-2 xl:col-span-3">
                        <v-form-label><span class="text-dark-500 italic">Amount for Reduction</span></v-form-label>
                        <v-form-input
                            type="number"
                            v-model="reductionAmount"
                            class="border border-gray-300 rounded-lg"
                            required
                        />
                    </v-form-group>

                    <!-- File Upload Field -->
                    <v-form-group class="col-span-full sm:col-span-2 xl:col-span-3">
                        <v-form-label><span class="text-dark-500 italic">Upload Supporting File</span></v-form-label>
                        <v-form-input
                            type="file"
                            v-model="reductionFile"
                            class="border border-gray-300 rounded-lg"
                            required
                        />
                    </v-form-group>
                </v-form>
            </v-card-text>

            <v-card-actions>
                <v-btn text @click="closeReductionModal">Cancel</v-btn>
                <v-btn color="orange" :disabled="!isFormValid" @click="submitReduction">Submit</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
import Layout from "@/layouts/panel/admin/index.vue";

export default {
    layout: Layout,
    props: {
        caseDetails: {
            type: Object,
            required: true,
        },
        icdCodeDescription: {
            type: String,
            required: true,
        },
        cptCodeDescriptions: {
            type: Array,
            required: true,
        },
        referrals: {
            type: Array, // Ensure referrals is an array
            required: true,
        },
    },
    data() {
        return {
            isModalOpen: false,
            reductionAmount: null,
            reductionFile: null,
            isFormValid: true,
            selectedReferralId: null, // Track the selected referral for reduction
        };
    },
    methods: {
        downloadCMS1500Form() {
            // Logic to download the CMS 1500 form
        },
        markReferralComplete(referralId) {
            this.$toast().success(`Referral #${referralId} marked as complete!`);
        },
        markReferralLost(referralId) {
            this.$toast().info(`Referral #${referralId} marked as lost.`);
        },
        openReductionModal(referralId) {
            this.selectedReferralId = referralId; // Set the selected referral ID
            this.isModalOpen = true; // Open the modal
        },
        closeReductionModal() {
            this.isModalOpen = false;
            this.reductionAmount = null;
            this.reductionFile = null;
            this.selectedReferralId = null; // Reset the selected referral ID
        },
        async submitReduction() {
            if (this.isFormValid && this.selectedReferralId) {
                const formData = new FormData();
                formData.append('case_id', this.caseDetails.case_id);
                formData.append('referral_id', this.selectedReferralId);
                formData.append('amount', this.reductionAmount);
                formData.append('referral_status', 'reduction_request_sent'); // Set referral status
                if (this.reductionFile) {
                    formData.append('file', this.reductionFile);
                }

                try {
                    const response = await axios.post('/reduction-requests', formData, {
                        headers: { 'Content-Type': 'multipart/form-data' },
                    });
                    this.$toast().info("Reduction request sent successfully.");
                    this.closeReductionModal();
                    // Refresh the referrals data
                    this.$inertia.reload();
                } catch (error) {
                    this.$toast().error("Error sending reduction request.");
                    console.error("Error:", error.response.data);
                }
            }
        },
        formatDate(dateString) {
            if (!dateString) return 'N/A';

            const date = new Date(dateString);
            if (isNaN(date)) return 'Invalid Date';

            // Use Intl.DateTimeFormat for human-readable formatting
            return new Intl.DateTimeFormat('en-US', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
            }).format(date);
        },
        getStatusBadgeClass(status) {
            switch (status) {
                case 'pending':
                    return 'bg-yellow-500 text-white px-2 py-1 rounded text-sm';
                case 'reduction_request_sent':
                    return 'bg-blue-500 text-white px-2 py-1 rounded text-sm';
                case 'accepted':
                    return 'bg-green-500 text-white px-2 py-1 rounded text-sm';
                case 'rejected':
                    return 'bg-red-500 text-white px-2 py-1 rounded text-sm';
                default:
                    return 'bg-gray-500 text-white px-2 py-1 rounded text-sm';
            }
        },

        // Helper method to get status text
        getStatusText(status) {
            switch (status) {
                case 'pending':
                    return 'Pending';
                case 'reduction_request_sent':
                    return 'Reduction Request Sent';
                case 'accepted':
                    return 'Accepted';
                case 'rejected':
                    return 'Rejected';
                default:
                    return 'Unknown';
            }
        },

        // Helper method to get doctor decision badge class
        getDoctorDecisionBadgeClass(decision) {
            switch (decision) {
                case 'pending':
                    return 'bg-yellow-500 text-white px-2 py-1 mb-2 rounded text-sm';
                case 'accepted':
                    return 'bg-green-500 text-white px-2 py-1  mb-2  rounded text-sm';
                case 'rejected':
                    return 'bg-red-500 text-white px-2 py-1  mb-2  rounded text-sm';
                default:
                    return 'bg-gray-500 text-white px-2 py-1  mb-2  rounded text-sm';
            }
        },

        // Helper method to get doctor decision text
        getDoctorDecisionText(decision) {
            switch (decision) {
                case 'pending':
                    return 'Pending';
                case 'accepted':
                    return 'Accepted';
                case 'rejected':
                    return 'Rejected';
                default:
                    return 'Unknown';
            }
        },

    },
};
</script>

<style scoped>
.bg-white {
    background-color: white;
}
.shadow-sm {
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
}
.rounded-lg {
    border-radius: 0.5rem;
}
.p-6 {
    padding: 1.5rem;
}
.mt-4 {
    margin-top: 1rem;
}
.gap-4 {
    gap: 1rem;
}
.bg-gray-50 {
    background-color: #f9fafb;
}
</style>
