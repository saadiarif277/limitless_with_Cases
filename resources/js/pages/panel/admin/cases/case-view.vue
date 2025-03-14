<template>
    <v-inertia-head title="Case Details" />

    <div class="h-full divide-y divide-gray-200 space-y-12">

        <!-- Action Buttons -->
        <div class="divide-y divide-gray-200">
            <v-content-body>

        <div class="flex gap-4 mt-6 mr-2">


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









        </div>
            </v-content-body>
        </div>
        <!-- Patient Information -->
        <div class="divide-y divide-gray-200">
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

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 sm:gap-4 mt-4">
                    <v-heading :size="6" class="font-medium">{{ caseDetails.patient.name }}</v-heading>
                    <div class="flex flex-col gap-1">
                        <v-paragraph>Email: {{ caseDetails.patient.email }}</v-paragraph>
                        <v-paragraph>Address: {{ caseDetails.patient.address_line_1 }}, {{ caseDetails.patient.address_line_2 }}</v-paragraph>
                    </div>
                </div>
            </v-content-body>
        </div>

        <!-- Attorney Information -->
        <div class="divide-y divide-gray-200">
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

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 sm:gap-4 mt-4">
                    <v-heading :size="6" class="font-medium">{{ caseDetails.attorney.name }}</v-heading>
                    <div class="flex flex-col gap-1">
                        <v-paragraph>Email: {{ caseDetails.attorney.email }}</v-paragraph>
                    </div>
                </div>
            </v-content-body>
        </div>





        <!-- Billing Information -->
        <div class="divide-y divide-gray-200">
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

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 sm:gap-4 mt-4">
                    <v-heading :size="6" class="font-medium">Billing Details</v-heading>
                    <div class="flex flex-col gap-1">
                        <v-paragraph>Billing Type: {{ caseDetails.billing_type }}</v-paragraph>
                        <v-paragraph>ICD-10 Code: {{ icdCodeDescription }}</v-paragraph>
                        <v-paragraph>Service Billed: {{ caseDetails.service_billed }}</v-paragraph>
                        <div v-if="cptCodeDescriptions.length">
                            <v-paragraph>CPT Codes:</v-paragraph>
                            <ul class="list-disc pl-5">
                                <li v-for="description in cptCodeDescriptions" :key="description">{{ description }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </v-content-body>
        </div>

        <!-- Policy Limit Information -->
        <div class="divide-y divide-gray-200">
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

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 sm:gap-4 mt-4">
                    <v-heading :size="6" class="font-medium">Policy Details</v-heading>
                    <div class="flex flex-col gap-1">
                        <v-paragraph>Policy Limit: {{ caseDetails.policy_limit_info.policy_limit }}</v-paragraph>
                        <v-paragraph>PIP: {{ caseDetails.policy_limit_info.pip }}</v-paragraph>
                        <v-paragraph>Defendant Insurance: {{ caseDetails.policy_limit_info.defendant_insurance }}</v-paragraph>
                        <v-paragraph>Plaintiff Insurance: {{ caseDetails.policy_limit_info.plaintiff_insurance }}</v-paragraph>
                        <v-paragraph>Commercial Case: {{ caseDetails.policy_limit_info.commercial_case ? 'Yes' : 'No' }}</v-paragraph>
                        <v-paragraph>Type of Accident: {{ caseDetails.policy_limit_info.type_of_accident }}</v-paragraph>
                    </div>
                </div>
            </v-content-body>
        </div>

        <!-- Referrals -->
        <div class="divide-y divide-gray-200">
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

                <table class="table-auto w-full mt-4">
                    <thead>
                        <tr>
                            <th class="px-4 py-2 text-left">Referral</th>
                            <th class="px-4 py-2 text-left">Patient Name</th>
                            <th class="px-4 py-2 text-left">Created At</th>
                            <th class="px-4 py-2 text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="px-4 py-2">
                                <v-link :href="route('panel.admin.referrals.show', { referral: caseDetails.primary_referral_id })" class="font-semibold">
                                    Referral #{{ caseDetails.primary_referral_id }}
                                </v-link>
                            </td>
                            <td class="px-4 py-2">{{ caseDetails.patient.name }}</td>
                            <td class="px-4 py-2">{{ caseDetails.created_at }}</td>
                            <td class="px-4">   <v-button @click="markCaseComplete" class="bg-success w-30" color="green">
    Mark it Complete
</v-button>
<v-button @click="openReductionModal" class="bg-warning w-30" color="orange">
    Send for Reduction
</v-button>
<v-button @click="markLostCase" class="bg-danger w-30" color="red">
    Mark Lost Case
</v-button></td>
                        </tr>
                    </tbody>
                </table>

            </v-content-body>
        </div>


        <!-- CMS 1500 Form Download Button -->
        <div class="divide-y divide-gray-200">
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
    },
    data() {
        return {
            isModalOpen: false, // Controls modal visibility
            reductionAmount: null, // Holds the entered amount for reduction
            reductionFile: null, // Holds the uploaded file for reduction
            successSnackbar: false, // Controls the visibility of the success message
        };
    },
    methods: {
        downloadCMS1500Form() {
            // Logic to download the CMS 1500 form
        },
        updateCaseState() {
            // Logic to update case state (Won or In Reduction)
        }, markCaseComplete() {
      this.$toast().success("Case marked as complete!");
      // Your other logic here
    },
    sendForReduction() {
      this.$toast().info("Case sent for reduction.");
      // Your other logic here
    },
    markLostCase() {
      this.$toast().info("Case marked as lost.");
      // Your other logic here
    },
        updateCaseState(status) {
            // Example of updating the case state, this could involve an API request or state management
            this.caseDetails.status = status;
            // You can implement additional logic like saving this status or sending it to the backend
            console.log(`Case marked as ${status}`);
        },

        openReductionModal() {
            this.isModalOpen = true;
        },
        closeReductionModal() {
            this.isModalOpen = false;
            this.reductionAmount = null; // Reset the form data when closing
            this.reductionFile = null;
        },
        submitReduction() {
            // Show success message
            this.successSnackbar = true;
            // Close modal after submission
            this.closeReductionModal();
        },
    },

};
</script>

<style scoped>
.mt-4 {
    margin-top: 1rem;
}
</style>
