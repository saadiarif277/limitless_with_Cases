<template>
    <v-inertia-head title="Case Details" />

    <div class="h-full space-y-6">
        <!-- Tab Navigation -->
        <div class="bg-dark shadow-sm rounded-lg p-6 pb-8">
            <!-- Case View Heading -->
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Case View</h2>

            <!-- Tab Navigation -->
            <nav class="flex space-x-4 border-b pb-3">
                <button
                    v-for="tab in tabs"
                    :key="tab.name"
                    @click="activeTab = tab.name"
                    :class="[
                        'px-4 py-2 rounded-md text-sm font-medium focus:outline-none border',
                        activeTab === tab.name
                            ? 'bg-primary-500 text-white border-primary-500 shadow-md'
                            : 'text-gray-600 border-gray-300 hover:bg-gray-100',
                    ]"
                >
                    {{ tab.label }}
                </button>
            </nav>
        </div>

        <!-- Tab Content -->
        <div class="bg-white shadow-sm rounded-lg p-6">
            <!-- Patient Information Tab -->
            <div v-if="activeTab === 'patient'">
                <v-card class="p-6 shadow-lg rounded-lg border border-gray-200">
            <v-content-body>
                <v-section-heading>
                    <template #title>
                                <div class="flex items-center gap-2 text-xl font-semibold text-gray-700">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke-width="1.5"
                                        stroke="currentColor"
                                        class="w-6 h-6 text-primary-500"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M17.25 8.25 21 12m0 0-3.75 3.75M21 12H3"
                                        />
                            </svg>
                                    <span class="text-primary-500">Patient Information</span>
                        </div>
                    </template>
                </v-section-heading>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mt-6">
                            <!-- Patient Name -->
                            <div class="flex items-center gap-3">
                                <v-avatar size="40" class="bg-primary-100 text-primary-600">
                                    {{ caseDetails.patient?.name?.charAt(0) || "N" }}
                                </v-avatar>
                                <div>
                                    <v-heading :size="5" class="font-semibold text-gray-800">
                                        {{ caseDetails.patient?.name || "N/A" }}
                                    </v-heading>
                                    <v-paragraph class="text-gray-500 text-sm">
                                        Patient Name
                                    </v-paragraph>
                                </div>
                            </div>

                            <!-- Email -->
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M16 12H8m8 0a4 4 0 00-8 0m8 0a4 4 0 01-8 0m8 0V9a3 3 0 00-6 0v3m6 0a3 3 0 11-6 0" />
                                </svg>
                                <div>
                                    <v-paragraph class="text-gray-800 font-medium">
                                        {{ caseDetails.patient?.email || "N/A" }}
                                    </v-paragraph>
                                    <v-paragraph class="text-gray-500 text-sm">
                                        Email
                                    </v-paragraph>
                                </div>
                            </div>

                            <!-- Address -->
                            <div class="flex items-start gap-3 col-span-2">
                                <svg class="w-5 h-5 text-gray-600 mt-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M12 2C8.134 2 5 5.134 5 9c0 4.656 7 11 7 11s7-6.344 7-11c0-3.866-3.134-7-7-7zM12 11a2 2 0 110-4 2 2 0 010 4z" />
                                </svg>
                                <div>
                                    <v-paragraph class="text-gray-800 font-medium">
                                        {{ caseDetails.patient?.address_line_1 || "N/A" }},
                                        {{ caseDetails.patient?.address_line_2 || "N/A" }}
                                    </v-paragraph>
                                    <v-paragraph class="text-gray-500 text-sm">
                                        Address
                                    </v-paragraph>
                                </div>
                            </div>
                        </div>
                    </v-content-body>
                </v-card>
            </div>

            <!-- Attorney Information Tab -->
            <div v-if="activeTab === 'attorney'" class="bg-white shadow-md rounded-lg p-6">
                <!-- Attorney Section Heading -->
                <div class="flex items-center gap-2 border-b pb-3 mb-4">
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
                            d="M17.25 8.25 21 12m0 0-3.75 3.75M21 12H3"
                        />
                    </svg>
                    <h2 class="text-lg font-semibold text-gray-700">Attorney Information</h2>
                </div>

                <!-- Attorney Details -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Attorney Name -->
                    <div>
                        <h3 class="text-base font-medium text-gray-800">
                            {{ caseDetails.attorney?.name || "N/A" }}
                        </h3>
                    </div>

                    <!-- Contact Info -->
                    <div class="flex flex-col gap-2 text-gray-600">
                        <p class="text-sm">
                            <span class="font-medium text-gray-700">Email:</span>
                            {{ caseDetails.attorney?.email || "N/A" }}
                        </p>
                    </div>
                </div>
        </div>

            <!-- Policy Limit Information Tab -->
            <div v-if="activeTab === 'policy'">
            <v-content-body>
                    <!-- Card Wrapper -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <!-- Card Header -->
                <v-section-heading>
                    <template #title>
                        <div class="flex items-center gap-2">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke-width="1.5"
                                        stroke="currentColor"
                                        class="w-4 h-4 text-primary-500"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M17.25 8.25 21 12m0 0-3.75 3.75M21 12H3"
                                        />
                            </svg>
                                    <span class="text-primary-500">Policy Limit</span>
                                    Information
                        </div>
                    </template>
                </v-section-heading>

                        <!-- Card Content -->
                        <div class="mt-6">
                            <!-- Policy Details Section -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <!-- Left Column -->
                                <div class="flex flex-col gap-3">
                                    <!-- Policy Limit -->
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <v-paragraph class="text-sm text-gray-600">
                                            Policy Limit:
                                        </v-paragraph>
                                        <v-paragraph class="font-medium text-gray-800">
                                            {{ caseDetails.policy_limit_info?.policy_limit || "N/A" }}
                                        </v-paragraph>
                                    </div>

                                    <!-- PIP -->
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <v-paragraph class="text-sm text-gray-600">
                                            PIP:
                                        </v-paragraph>
                                        <v-paragraph class="font-medium text-gray-800">
                                            {{ caseDetails.policy_limit_info?.pip || "N/A" }}
                                        </v-paragraph>
                                    </div>

                                    <!-- Defendant Insurance -->
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <v-paragraph class="text-sm text-gray-600">
                                            Defendant Insurance:
                                        </v-paragraph>
                                        <v-paragraph class="font-medium text-gray-800">
                                            {{ caseDetails.policy_limit_info?.defendant_insurance || "N/A" }}
                                        </v-paragraph>
                                    </div>
                                </div>

                                <!-- Right Column -->
                                <div class="flex flex-col gap-3">
                                    <!-- Plaintiff Insurance -->
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <v-paragraph class="text-sm text-gray-600">
                                            Plaintiff Insurance:
                                        </v-paragraph>
                                        <v-paragraph class="font-medium text-gray-800">
                                            {{ caseDetails.policy_limit_info?.plaintiff_insurance || "N/A" }}
                                        </v-paragraph>
                                    </div>

                                    <!-- Commercial Case -->
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <v-paragraph class="text-sm text-gray-600">
                                            Commercial Case:
                                        </v-paragraph>
                                        <v-paragraph class="font-medium text-gray-800">
                                            {{ caseDetails.policy_limit_info?.commercial_case ? "Yes" : "No" }}
                                        </v-paragraph>
                                    </div>

                                    <!-- Type of Accident -->
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <v-paragraph class="text-sm text-gray-600">
                                            Type of Accident:
                                        </v-paragraph>
                                        <v-paragraph class="font-medium text-gray-800">
                                            {{ caseDetails.policy_limit_info?.type_of_accident || "N/A" }}
                                        </v-paragraph>
                                    </div>
                                </div>
                            </div>
                    </div>
                </div>
            </v-content-body>
        </div>

            <!-- Referrals Tab -->
            <div v-if="activeTab === 'referrals'">
            <v-content-body>
                <v-section-heading>
                    <template #title>
                        <div class="flex items-center gap-2">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="1.5"
                                    stroke="currentColor"
                                    class="w-4 h-4 text-primary-500"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M17.25 8.25 21 12m0 0-3.75 3.75M21 12H3"
                                    />
                            </svg>
                                <span class="text-primary-500">Referrals</span>
                        </div>
                    </template>
                </v-section-heading>

                    <!-- Referral Cards -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mt-4">
                        <!-- Iterate over referrals -->
                        <div
                            v-for="referral in referrals"
                            :key="referral.referral_id"
                            class="bg-gray-50 p-4 rounded-lg shadow-sm"
                        >
                            <div class="flex flex-col gap-2">
                                <v-heading :size="6" class="font-medium">
                                    <v-link
                                        v-if="referral.referral_id"
                                        :href="route('panel.user.referrals.show', {
                                            referral: referral.referral_id,
                                        })"
                                        class="text-primary-500 hover:underline"
                                    >
                                        Referral #{{ referral.referral_id }}
                                    </v-link>
                                    <span v-else class="text-gray-500">Referral ID Missing</span>
                                </v-heading>

                                <!-- Format created_at in a human-readable manner -->
                                <v-paragraph>Created: {{ formatDate(referral.created_at) || "N/A" }}</v-paragraph>

                                <!-- Display Reduction Request Details -->
                                <div v-if="referral.reduction_requests && referral.reduction_requests.length > 0" class="mt-4">
                                    <v-heading :size="6" class="font-medium">Reduction Requests</v-heading>
                                    <div v-for="reductionRequest in referral.reduction_requests" :key="reductionRequest.id" class="mt-3 p-4 bg-gray-50 rounded-lg border">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <div class="flex items-center gap-2 mb-2">
                                                    <span class="text-sm font-medium text-gray-700">Request #{{ reductionRequest.id }}</span>
                                                    <span :class="getStatusBadgeClass(reductionRequest.referral_status)" class="text-xs px-2 py-1 rounded-full">
                                                        {{ getStatusText(reductionRequest.referral_status) }}
                                                    </span>
                                                </div>
                                                <div class="space-y-1 text-sm text-gray-600">
                                                    <div>
                                                        <span class="font-medium">Requested Amount:</span>
                                                        <span class="ml-2 text-lg font-semibold text-red-600">${{ reductionRequest.amount }}</span>
                                                    </div>
                                                    <div>
                                                        <span class="font-medium">Requested On:</span>
                                                        <span class="ml-2">{{ formatDate(reductionRequest.created_at) }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div>
                                                <div class="flex items-center gap-2 mb-2">
                                                    <span class="text-sm font-medium text-gray-700">Doctor Decision:</span>
                                                    <span :class="getDoctorDecisionBadgeClass(reductionRequest.doctor_decision)" class="text-xs px-2 py-1 rounded-full">
                                                        {{ getDoctorDecisionText(reductionRequest.doctor_decision) }}
                                                    </span>
                                                </div>
                                                <div class="space-y-1 text-sm text-gray-600">
                                                    <div v-if="reductionRequest.counter_offer">
                                                        <span class="font-medium">Counter Offer:</span>
                                                        <span class="ml-2 text-lg font-semibold text-blue-600">${{ reductionRequest.counter_offer }}</span>
                                                    </div>
                                                    <div v-if="reductionRequest.notes">
                                                        <span class="font-medium">Notes:</span>
                                                        <span class="ml-2">{{ reductionRequest.notes }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Supporting Document -->
                                        <div v-if="reductionRequest.file_path" class="mt-3 pt-3 border-t border-gray-200">
                                            <div class="flex items-center space-x-2">
                                                <svg class="h-4 w-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
                                                </svg>
                                                <a :href="reductionRequest.file_link" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm">
                                                    View Supporting Document
                                                </a>
                                            </div>
                                        </div>

                                        <!-- Doctor Response Form (if pending and user is doctor) -->
                                        <div v-if="userRole === 'Doctor' && reductionRequest.doctor_decision === 'pending'" class="mt-4 pt-3 border-t border-gray-200">
                                            <h5 class="text-sm font-medium text-gray-700 mb-3">Your Response</h5>
                                            <form @submit.prevent="submitDecision(reductionRequest)" class="space-y-3">
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-2">Decision *</label>
                                                    <div class="space-y-2">
                                                        <label class="flex items-center">
                                                            <input
                                                                type="radio"
                                                                v-model="reductionRequest.decision"
                                                                value="accepted"
                                                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                                                            >
                                                            <span class="ml-2 text-sm text-gray-700">Accept Reduction</span>
                                                        </label>
                                                        <label class="flex items-center">
                                                            <input
                                                                type="radio"
                                                                v-model="reductionRequest.decision"
                                                                value="rejected"
                                                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                                                            >
                                                            <span class="ml-2 text-sm text-gray-700">Reject Reduction</span>
                                                        </label>
                                                    </div>
                                                </div>

                                                <div v-if="reductionRequest.decision === 'rejected'">
                                                    <label class="block text-sm font-medium text-gray-700 mb-2">Counter Offer (Optional)</label>
                                                    <div class="relative">
                                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                            <span class="text-gray-500 sm:text-sm">$</span>
                                                        </div>
                                                        <input
                                                            type="number"
                                                            v-model="reductionRequest.counter_offer"
                                                            step="0.01"
                                                            min="0"
                                                            class="pl-7 block w-full border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                                            placeholder="0.00"
                                                        />
                                                    </div>
                                                </div>

                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-2">Notes (Optional)</label>
                                                    <textarea
                                                        v-model="reductionRequest.notes"
                                                        rows="2"
                                                        class="block w-full border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                                        placeholder="Add any notes or comments..."
                                                    ></textarea>
                                                </div>

                                                <div class="flex justify-end space-x-3">
                                                    <button
                                                        type="submit"
                                                        :disabled="!reductionRequest.decision || reductionRequest.processing"
                                                        class="px-3 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                                                    >
                                                        <span v-if="reductionRequest.processing">Processing...</span>
                                                        <span v-else>Submit Decision</span>
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex gap-2 mt-2">
                                    <v-button
                                        @click="markReferralComplete(referral.referral_id)"
                                        class="bg-green-500 text-white flex-1"
                                    >
                                        Complete
                                    </v-button>
                                    <v-button
                                        v-if="userRole === 'Attorney' || userRole === 'Case_manager'"
                                        @click="openReductionModal(referral.referral_id)"
                                        :disabled="referral.reduction_requests && referral.reduction_requests.length > 0"
                                        class="bg-orange-500 text-white flex-1"
                                        :class="{
                                            'opacity-50 cursor-not-allowed': referral.reduction_requests && referral.reduction_requests.length > 0,
                                        }"
                                    >
                                        Reduction
                                    </v-button>
                                    <v-button
                                        @click="markReferralLost(referral.referral_id)"
                                        class="bg-red-500 text-white flex-1"
                                    >
                                        Lost
                                    </v-button>
                                </div>
                        </div>
                    </div>
                </div>
            </v-content-body>
        </div>

            <!-- CMS 1500 Form Tab -->
            <div v-if="activeTab === 'cms'">
            <v-content-body>
                <v-section-heading>
                    <template #title>
                        <div class="flex items-center gap-2">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="1.5"
                                    stroke="currentColor"
                                    class="w-4 h-4 text-primary-500"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M17.25 8.25 21 12m0 0-3.75 3.75M21 12H3"
                                    />
                            </svg>
                                <span class="text-primary-500">CMS 1500 Form</span>
                        </div>
                    </template>
                </v-section-heading>

                    <!-- Embedded DocuSeal Form -->
                    <div class="mt-4">
                        <DocusealForm
                            :src="'https://docuseal.com/d/Dwz83PcPdAY9Ry'"
                            @submit="handleFormSubmit"
                            @save="handleFormSave"
                        />
                    </div>

                    <!-- Form Action Buttons -->
                    <div class="mt-6 flex gap-4">
                        <v-button
                            @click="saveFormForLater"
                            color="secondary"
                            class="flex-1"
                        >
                            Save for Later
                        </v-button>
                        <v-button
                            @click="downloadFormPDF"
                            color="primary"
                            class="flex-1"
                        >
                            Download PDF
                        </v-button>
                </div>
            </v-content-body>
        </div>

            <!-- Reduction Requests Tab -->
            <div v-if="activeTab === 'reduction-requests'">
                <v-content-body>
                    <v-section-heading>
                        <template #title>
                            <div class="flex items-center gap-2">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="1.5"
                                    stroke="currentColor"
                                    class="w-4 h-4 text-primary-500"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M17.25 8.25 21 12m0 0-3.75 3.75M21 12H3"
                                    />
                                </svg>
                                <span class="text-primary-500">Reduction Requests</span>
                            </div>
                        </template>
                    </v-section-heading>

                    <!-- Reduction Requests List -->
                    <div class="mt-4">
                        <div v-if="reductionRequests.length === 0" class="text-center text-gray-500 py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No reduction requests</h3>
                            <p class="mt-1 text-sm text-gray-500">No reduction requests have been sent yet.</p>
                        </div>

                        <div v-else class="space-y-4">
                            <div v-for="request in reductionRequests" :key="request.id" class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm">
                                <div class="flex justify-between items-start mb-4">
                                    <div>
                                        <h4 class="text-lg font-medium text-gray-900">
                                            Reduction Request #{{ request.id }}
                                        </h4>
                                        <p class="text-sm text-gray-500">
                                            Referral #{{ request.referral?.referral_id }} -
                                            {{ request.referral?.patientUser?.name }}
                                        </p>
                                    </div>
                                    <div class="flex gap-2">
                                        <span :class="getStatusBadgeClass(request.referral_status)">
                                            {{ getStatusText(request.referral_status) }}
                                        </span>
                                        <span :class="getDoctorDecisionBadgeClass(request.doctor_decision)">
                                            {{ getDoctorDecisionText(request.doctor_decision) }}
                                        </span>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Amount Requested</label>
                                        <p class="mt-1 text-lg font-semibold text-gray-900">${{ request.amount }}</p>
                                    </div>
                                    <div v-if="request.counter_offer">
                                        <label class="block text-sm font-medium text-gray-700">Counter Offer</label>
                                        <p class="mt-1 text-lg font-semibold text-green-600">${{ request.counter_offer }}</p>
                                    </div>
                                </div>

                                <div v-if="request.notes" class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700">Notes</label>
                                    <p class="mt-1 text-sm text-gray-600">{{ request.notes }}</p>
                                </div>

                                <div v-if="request.file_path" class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700">Attached File</label>
                                    <a :href="`/storage/${request.file_path}`" target="_blank" class="mt-1 inline-flex items-center text-primary-600 hover:text-primary-500">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        Download File
                                    </a>
                                </div>

                                <!-- Doctor Decision Form (Only for Doctors) -->
                                <div v-if="userRole === 'Doctor' && request.doctor_decision === 'pending'" class="border-t pt-4">
                                    <h5 class="text-md font-medium text-gray-900 mb-3">Respond to Reduction Request</h5>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Decision</label>
                                            <select v-model="request.decision" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500">
                                                <option value="">Select decision</option>
                                                <option value="accepted">Accept</option>
                                                <option value="rejected">Reject</option>
                                            </select>
                                        </div>
                                        <div v-if="request.decision === 'accepted'">
                                            <label class="block text-sm font-medium text-gray-700">Counter Offer (Optional)</label>
                                            <input type="number" v-model="request.counter_offer" step="0.01" min="0" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500" placeholder="Enter counter offer amount">
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <label class="block text-sm font-medium text-gray-700">Notes (Optional)</label>
                                        <textarea v-model="request.notes" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500" placeholder="Add any notes about your decision"></textarea>
                                    </div>
                                    <div class="mt-4 flex gap-2">
                                        <button @click="submitDecision(request)" :disabled="!request.decision" class="bg-primary-600 text-white px-4 py-2 rounded-md hover:bg-primary-700 disabled:opacity-50 disabled:cursor-not-allowed">
                                            Submit Decision
                                        </button>
                                        <button @click="resetDecisionForm(request)" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400">
                                            Reset
                                        </button>
                                    </div>
                                </div>

                                <!-- Decision Status (For Attorneys) -->
                                <div v-if="(userRole === 'Attorney' || userRole === 'Case_manager') && request.doctor_decision !== 'pending'" class="border-t pt-4">
                                    <h5 class="text-md font-medium text-gray-900 mb-2">Doctor's Decision</h5>
                                    <div class="bg-gray-50 p-3 rounded-md">
                                        <p class="text-sm text-gray-600">
                                            <strong>Decision:</strong>
                                            <span :class="getDoctorDecisionBadgeClass(request.doctor_decision)">
                                                {{ getDoctorDecisionText(request.doctor_decision) }}
                                            </span>
                                        </p>
                                        <p v-if="request.counter_offer" class="text-sm text-gray-600 mt-1">
                                            <strong>Counter Offer:</strong> ${{ request.counter_offer }}
                                        </p>
                                        <p v-if="request.notes" class="text-sm text-gray-600 mt-1">
                                            <strong>Notes:</strong> {{ request.notes }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </v-content-body>
            </div>

            <!-- Billing Information Tab -->
            <div v-if="activeTab === 'billing'">
            <v-content-body>
                    <!-- Card Wrapper -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <!-- Card Header -->
                <v-section-heading>
                    <template #title>
                        <div class="flex items-center gap-2">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke-width="1.5"
                                        stroke="currentColor"
                                        class="w-4 h-4 text-primary-500"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M17.25 8.25 21 12m0 0-3.75 3.75M21 12H3"
                                        />
                            </svg>
                                    <span class="text-primary-500">Billing Information</span>
                        </div>
                    </template>
                </v-section-heading>

                        <!-- Card Content -->
                        <div class="mt-4">
                            <!-- Billing Type Selection (Only for Doctors) -->
                            <div v-if="userRole === 'Doctor'" class="mb-6">
                                <v-form-group>
                                    <v-form-label>Billing Type</v-form-label>
                                    <v-form-select
                                        v-model="billingType"
                                        :options="[
                                            { label: 'Insurance', value: 'Insurance' },
                                            { label: 'LOP (Letter of Protection)', value: 'LOP' },
                                        ]"
                                        :error="form.errors.billing_type"
                                        :disabled="form.processing"
                                        class="w-full"
                                    />
                                    <p class="mt-2 text-sm text-gray-600">
                                        <span v-if="billingType === 'Insurance'">
                                            Insurance billing will not deduct from policy limit.
                                        </span>
                                        <span v-else-if="billingType === 'LOP'">
                                            LOP billing will deduct from policy limit.
                                        </span>
                                    </p>
                                </v-form-group>
                            </div>

                            <!-- CPT Codes Section (Only for Doctors) -->
                            <div v-if="userRole === 'Doctor'" class="mt-6">
                                <v-form-group>
                                    <v-form-label class="mb-2">CPT Codes</v-form-label>
                                    <div v-if="form.cptCodes.length === 0" class="text-gray-500 mb-4">
                                        No CPT codes added yet.
                                    </div>
                                    <div v-else class="space-y-4">
                                        <div v-for="(cpt, index) in form.cptCodes" :key="index" class="flex gap-4 items-start">
                                            <v-form-select
                                                :options="allCptCodes.map((cptCode) => ({
                                                    label: `${cptCode.code} - ${cptCode.description}`,
                                                    value: cptCode.id,
                                                }))"
                                                v-model="cpt.code"
                                                :error="form.errors[`cptCodes.${index}.code`]"
                                                :disabled="form.processing"
                                                class="flex-1"
                                            />
                                            <v-form-input
                                                type="number"
                                                v-model="cpt.value"
                                                :error="form.errors[`cptCodes.${index}.value`]"
                                                :disabled="form.processing"
                                                placeholder="Amount"
                                                class="flex-1"
                                            />
                                            <v-button
                                                @click="removeCptCode(index)"
                                                color="red"
                                                class="self-start"
                                            >
                                                Remove
</v-button>
                                        </div>
                                    </div>
                                    <v-button
                                        @click="addCptCode"
                                        color="primary"
                                        class="mt-4"
                                    >
                                        Add CPT Code
</v-button>
                                </v-form-group>
                            </div>

                            <!-- Save Button (Only for Doctors) -->
                            <div v-if="userRole === 'Doctor'" class="mt-6">
                                <v-button
                                    @click="saveCase"
                                    color="primary"
                                    :disabled="form.processing"
                                    class="w-full sm:w-auto"
                                >
                                    Save Billing Information
                                </v-button>
        </div>

                            <!-- Read-only view for Attorneys and Case Managers -->
                            <div v-else class="space-y-4">
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <p class="text-sm text-gray-600">Billing Type</p>
                                    <p class="font-medium text-gray-800">{{ caseDetails.billing_type || 'N/A' }}</p>
                                </div>
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <p class="text-sm text-gray-600">CPT Codes</p>
                                    <div v-if="form.cptCodes.length === 0" class="text-gray-500">
                                        No CPT codes added yet.
                                    </div>
                                    <div v-else class="space-y-2">
                                        <div v-for="(cpt, index) in form.cptCodes" :key="index" class="flex justify-between items-center">
                                            <span class="font-medium text-gray-800">
                                                {{ getCptCodeDescription(cpt.code) }}
                                            </span>
                                            <span class="text-gray-600">${{ cpt.value }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                </div>
            </v-content-body>
            </div>
        </div>
    </div>

    <!-- Reduction Modal -->
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
                            step="0.01"
                            min="0"
                            class="border border-gray-300 rounded-lg"
                            required
                        />
                        <v-form-error v-if="!reductionAmount">Amount is required</v-form-error>
                    </v-form-group>

                    <!-- File Upload Field -->
                    <v-form-group class="col-span-full sm:col-span-2 xl:col-span-3">
                        <v-form-label><span class="text-dark-500 italic">Upload Supporting File</span></v-form-label>
                        <v-form-input
                            type="file"
                            @change="handleFileChange"
                            accept=".pdf,.doc,.docx"
                            class="border border-gray-300 rounded-lg"
                            required
                        />
                        <div v-if="reductionFile" class="mt-2 p-2 bg-green-50 border border-green-200 rounded text-sm text-green-700">
                            Selected: {{ selectedFileName }}
                        </div>
                        <v-form-error v-if="!reductionFile">File is required</v-form-error>
                        <p class="mt-1 text-xs text-gray-500">Accepted formats: PDF, DOC, DOCX (max 2MB)</p>
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
import Layout from "@/layouts/panel/user/index.vue";
import axios from "axios";
import { DocusealForm } from '@docuseal/vue';

export default {
    layout: Layout,
    components: {
        DocusealForm,
    },
    props: {
        caseDetails: {
            type: Object,
            required: true,
        },
        referrals: {
            type: Array,
            required: true,
        },
        userRole: {
            type: String,
            required: true,
        },
        userId: {
            type: Number,
            required: true,
        },
        allCptCodes: {
            type: Array,
            required: true,
        },
        reductionRequests: {
            type: Array,
            required: false,
            default: () => [],
        },
    },
    data() {
        return {
            // Tabs configuration
            tabs: [
                { name: "patient", label: "Patient Information" },
                { name: "attorney", label: "Attorney Information" },
                { name: "policy", label: "Policy Limit Information" },
                { name: "referrals", label: "Referrals" },
                { name: "reduction-requests", label: "Reduction Requests", show: () => ['Doctor', 'Attorney', 'Case_manager'].includes(this.userRole) },
                { name: "cms", label: "CMS 1500 Form" },
                { name: "billing", label: "Billing Information" },
            ],
            activeTab: "patient", // Default active tab
            formData: null,
            savedFormId: null,
            billingType: this.caseDetails.billing_type || "Insurance",
            form: {
                cptCodes: [],
                processing: false,
            },
            // Reduction modal state
            isModalOpen: false,
            reductionAmount: null,
            reductionFile: null,
            isFormValid: true,
            selectedReferralId: null,
        };
    },
    created() {
        // Initialize CPT codes after component is created
        if (this.caseDetails.cpt_codes) {
            try {
                this.form.cptCodes = JSON.parse(this.caseDetails.cpt_codes);
            } catch (error) {
                console.error('Error parsing CPT codes:', error);
                this.form.cptCodes = [];
            }
        }
    },
    watch: {
        reductionAmount(newVal) {
            this.validateForm();
        },
        reductionFile(newVal) {
            this.validateForm();
        }
    },
    computed: {
        calculateRemainingPolicyLimit() {
            const totalLimit = parseFloat(this.caseDetails.policy_limit_info?.policy_limit || 0);
            const totalBilled = this.calculateTotalBilledAmount;
            return (totalLimit - totalBilled).toFixed(2);
        },
        calculateTotalBilledAmount() {
            if (!this.form.cptCodes) return '0.00';
            return this.form.cptCodes.reduce((total, cpt) => {
                return total + (parseFloat(cpt.value) || 0);
            }, 0).toFixed(2);
        },
        selectedFileName() {
            return this.reductionFile ? this.reductionFile.name : 'No file selected';
        }
    },
    methods: {
        // Format date in a human-readable manner
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
        // Get status badge class
        getStatusBadgeClass(status) {
            switch (status) {
                case 'pending':
                    return 'bg-yellow-100 text-yellow-800';
                case 'reduction_request_sent':
                    return 'bg-blue-100 text-blue-800';
                case 'accepted':
                    return 'bg-green-100 text-green-800';
                case 'rejected':
                    return 'bg-red-100 text-red-800';
                default:
                    return 'bg-gray-100 text-gray-800';
            }
        },

        // Get status text
        getStatusText(status) {
            switch (status) {
                case 'pending':
                    return 'Pending';
                case 'reduction_request_sent':
                    return 'Request Sent';
                case 'accepted':
                    return 'Accepted';
                case 'rejected':
                    return 'Rejected';
                default:
                    return 'Unknown Status';
            }
        },

        // Get doctor decision badge class
        getDoctorDecisionBadgeClass(decision) {
            switch (decision) {
                case 'pending':
                    return 'bg-yellow-100 text-yellow-800';
                case 'accepted':
                    return 'bg-green-100 text-green-800';
                case 'rejected':
                    return 'bg-red-100 text-red-800';
                default:
                    return 'bg-gray-100 text-gray-800';
            }
        },

        // Get doctor decision text
        getDoctorDecisionText(decision) {
            switch (decision) {
                case 'pending':
                    return 'Pending';
                case 'accepted':
                    return 'Accepted';
                case 'rejected':
                    return 'Rejected';
                default:
                    return 'Unknown Decision';
            }
        },
        // Mark referral as complete
        markReferralComplete(referralId) {
            this.$toast().success(`Referral #${referralId} marked as complete!`);
        },
        // Mark referral as lost
        markReferralLost(referralId) {
            this.$toast().info(`Referral #${referralId} marked as lost.`);
        },
        // Open reduction modal
        openReductionModal(referralId) {
            this.selectedReferralId = referralId;
            this.isModalOpen = true;
        },
        // Close reduction modal
        closeReductionModal() {
            this.isModalOpen = false;
            this.reductionAmount = null;
            this.reductionFile = null;
            this.selectedReferralId = null;
            this.isFormValid = true;
        },
        // Validate reduction form
        validateForm() {
            this.isFormValid = !!(this.reductionAmount && this.reductionFile && this.selectedReferralId);
        },
        // Handle file change
        handleFileChange(event) {
            const file = event.target.files[0];
            if (file) {
                // Check file size (2MB limit)
                if (file.size > 2 * 1024 * 1024) {
                    this.$toast().error('File size must be less than 2MB');
                    event.target.value = '';
                    this.reductionFile = null;
                    return;
                }

                // Check file type
                const allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
                if (!allowedTypes.includes(file.type)) {
                    this.$toast().error('Please select a valid file type (PDF, DOC, or DOCX)');
                    event.target.value = '';
                    this.reductionFile = null;
                    return;
                }

                this.reductionFile = file;
            }
        },
        // Submit doctor decision on reduction request
        async submitDecision(reductionRequest) {
            if (!reductionRequest.decision) {
                this.$toast().error('Please select a decision');
                return;
            }

            // Set processing state
            reductionRequest.processing = true;

            try {
                const response = await this.$inertia.put(
                    route('panel.user.cases.reduction-requests.update-decision', {
                        case: this.caseDetails.case_id,
                        reductionRequest: reductionRequest.id
                    }),
                    {
                        doctor_decision: reductionRequest.decision,
                        counter_offer: reductionRequest.counter_offer || null,
                        notes: reductionRequest.notes || null,
                    }
                );

                if (response.status === 200) {
                    this.$toast().success('Decision submitted successfully');
                    // Update the local state
                    reductionRequest.doctor_decision = reductionRequest.decision;
                    reductionRequest.processing = false;
                    // Remove the form data
                    reductionRequest.decision = null;
                    reductionRequest.counter_offer = null;
                    reductionRequest.notes = '';
                }
            } catch (error) {
                this.$toast().error('Error submitting decision. Please try again.');
                console.error('Error:', error);
            } finally {
                reductionRequest.processing = false;
            }
        },

        // Reset decision form
        resetDecisionForm(request) {
            request.decision = null;
            request.counter_offer = null;
            request.notes = '';
        },

        // Submit reduction request
        async submitReduction() {
            // Validate form
            if (!this.reductionAmount || !this.reductionFile || !this.selectedReferralId) {
                this.$toast().error('Please fill in all required fields');
                return;
            }

            if (this.reductionAmount <= 0) {
                this.$toast().error('Reduction amount must be greater than 0');
                return;
            }

            if (this.isFormValid && this.selectedReferralId) {
                const formData = new FormData();
                formData.append("case_id", this.caseDetails.case_id);
                formData.append("referral_id", this.selectedReferralId);
                formData.append("amount", this.reductionAmount);
                formData.append("referral_status", "reduction_request_sent");
                if (this.reductionFile) {
                    formData.append("file", this.reductionFile);
                }

                try {
                    const response = await axios.post(
                        route('panel.user.cases.reduction-requests.create', {
                            case: this.caseDetails.case_id
                        }),
                        formData,
                        {
                            headers: { "Content-Type": "multipart/form-data" },
                        }
                    );
                    this.$toast().info("Reduction request sent successfully.");
                    this.closeReductionModal();
                    this.$inertia.reload(); // Refresh the case data
                } catch (error) {
                    this.$toast().error("Error sending reduction request.");
                    console.error("Error:", error.response.data);
                }
            }
        },
        // Handle form submit
        async handleFormSubmit(data) {
            this.formData = data;
            await this.saveFormToDatabase(data, 'completed');
        },
        // Handle form save
        async handleFormSave(data) {
            this.formData = data;
            await this.saveFormToDatabase(data, 'draft');
        },
        // Add CPT code
        addCptCode() {
            this.form.cptCodes.push({
                code: '',
                value: ''
            });
        },

        // Remove CPT code
        removeCptCode(index) {
            this.form.cptCodes.splice(index, 1);
        },

        // Get CPT code description
        getCptCodeDescription(codeId) {
            const cptCode = this.allCptCodes.find(cpt => cpt.id === codeId);
            return cptCode ? `${cptCode.code} - ${cptCode.description}` : 'Unknown CPT Code';
        },

        // Save case billing information
        async saveCase() {
            this.form.processing = true;
            try {
                const response = await this.$inertia.put(
                    route('panel.user.cases.updateBilling', {
                        case: this.caseDetails.case_id
                    }),
                    {
                        cpt_codes: JSON.stringify(this.form.cptCodes),
                        billing_type: this.billingType
                    }
                );
                this.$toast().success('Billing information saved successfully');
            } catch (error) {
                this.$toast().error('Error saving billing information');
                console.error('Error:', error);
            } finally {
                this.form.processing = false;
            }
        },

        // Save form to database
        async saveFormToDatabase(data, status) {
            try {
                const response = await axios.post(
                    route('panel.user.cases.saveForm', {
                        case: this.caseDetails.case_id
                    }),
                    {
                        form_data: data,
                        status: status
                    }
                );

                if (response.data.success) {
                    this.savedFormId = response.data.form_id;
                    this.$toast().success('Form saved successfully!');
                }
            } catch (error) {
                this.$toast().error('Error saving form');
                console.error('Error saving form:', error);
            }
        },
        // Save form for later
        async saveFormForLater() {
            if (this.formData) {
                await this.saveFormToDatabase(this.formData, 'draft');
            } else {
                this.$toast().warning('No form data to save');
            }
        },
        // Download form PDF
        async downloadFormPDF() {
            if (!this.savedFormId) {
                this.$toast().warning('Please save the form first');
                return;
            }

            try {
                const response = await axios.get(
                    route('panel.user.cases.downloadForm', {
                        case: this.caseDetails.case_id,
                        form: this.savedFormId
                    }),
                    {
                        responseType: 'blob'
                    }
                );

                // Create a blob from the PDF data
                const blob = new Blob([response.data], { type: 'application/pdf' });
                const url = window.URL.createObjectURL(blob);
                const link = document.createElement('a');
                link.href = url;
                link.download = `CMS-1500-Form-${this.caseDetails.case_id}.pdf`;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                window.URL.revokeObjectURL(url);

                this.$toast().success('Form downloaded successfully!');
            } catch (error) {
                this.$toast().error('Error downloading form');
                console.error('Error downloading form:', error);
            }
        },
        // Add a new CPT code
        addCptCode() {
            this.form.cptCodes.push({
                code: '',
                value: ''
            });
        },

        // Remove a CPT code
        removeCptCode(index) {
            this.form.cptCodes.splice(index, 1);
        },

        // Get CPT code description
        getCptCodeDescription(codeId) {
            const cptCode = this.allCptCodes.find(c => c.id === codeId);
            return cptCode ? `${cptCode.code} - ${cptCode.description}` : 'Unknown CPT Code';
        },

        // Save case with billing information
        async saveCase() {
            if (this.userRole !== 'Doctor') {
                this.$toast().error('Only doctors can update billing information.');
                return;
            }

            const data = {
                billing_type: this.billingType,
                cpt_codes: JSON.stringify(this.form.cptCodes),
                is_cms1500_generated: true,
            };

            try {
                const response = await axios.post(
                    route('panel.user.cases.updateBilling', {
                        case: this.caseDetails.case_id,
                    }),
                    data,
                    {
                        headers: {
                            'Content-Type': 'application/json',
                        },
                    }
                );
                this.$toast().success('Billing information updated successfully.');
                this.$inertia.reload(); // Reload the page to reflect changes
            } catch (error) {
                this.$toast().error('Error updating billing information.');
                console.error('Error:', error.response ? error.response.data : error.message);
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
.docuseal-form-container {
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 16px;
    background-color: #f7fafc;
}
</style>
