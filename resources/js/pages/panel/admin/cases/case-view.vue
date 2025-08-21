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
            v-show="!tab.show || tab.show()"
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
                                <v-avatar size="lg" class="bg-primary-100 text-primary-600">
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
                                    <span class="text-primary-500">Billing</span>
                                    Information
                                </div>
                            </template>
                        </v-section-heading>

                        <!-- Card Content -->
                        <div class="mt-4">
                            <!-- Billing Details Section -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <v-heading :size="6" class="font-medium"
                                        >Billing Details</v-heading
                                    >
                                </div>
                                <div class="flex flex-col gap-1">
                                    <v-paragraph
                                        >Billing Type:
                                        {{
                                            caseDetails.billing_type || "N/A"
                                        }}</v-paragraph
                                    >
                                    <v-paragraph
                                        >ICD-10 Code:
                                        {{
                                            icdCodeDescription || "N/A"
                                        }}</v-paragraph
                                    >
                                    <v-paragraph
                                        >Service Billed:
                                        {{
                                            caseDetails.service_billed || "N/A"
                                        }}</v-paragraph
                                    >
                                    <div v-if="cptCodeDescriptions.length">
                                        <v-paragraph>CPT Codes:</v-paragraph>
                                        <ul class="list-disc pl-5">
                                            <li
                                                v-for="description in cptCodeDescriptions"
                                                :key="description"
                                            >
                                                {{ description }}
                                            </li>
                                        </ul>
                                    </div>
                                    <v-paragraph v-else>CPT Codes: N/A</v-paragraph>
                                </div>
                            </div>

                            <!-- Billing Type Selection -->
                            <div class="mt-6">
                                <v-form-group>
                                    <v-form-label>Billing Type</v-form-label>
                                    <v-form-select
                                        v-model="billingType"
                                        :options="[
                                            { label: 'LOP', value: 'LOP' },
                                            { label: 'Insurance', value: 'Insurance' },
                                        ]"
                                        :error="form.errors.billing_type"
                                        :disabled="form.processing"
                                        class="w-full"
                                    />
                                </v-form-group>
                            </div>

                            <!-- CPT Codes Section (Visible only if Insurance is selected) -->
                            <div v-if="billingType === 'Insurance'" class="mt-6">
                                <v-form-group>
                                    <v-form-label class="mb-2">CPT Codes</v-form-label>
                                    <!-- Display a message if no CPT codes are added -->
                                    <div
                                        v-if="form.cptCodes.length === 0"
                                        class="text-gray-500 mb-4"
                                    >
                                        No CPT codes added yet.
                                    </div>
                                    <!-- Dynamic CPT Code Inputs -->
                                    <div
                                        v-for="(cpt, index) in form.cptCodes"
                                        :key="index"
                                        class="flex gap-2 mb-2"
                                    >
                                        <!-- CPT Code Selection -->
                                        <v-form-select
                                            :options="
                                                allCptCodes.map((cptCode) => ({
                                                    label: `${cptCode.code} - ${cptCode.description}`,
                                                    value: cptCode.id,
                                                }))
                                            "
                                            v-model="cpt.code"
                                            :error="form.errors[`cptCodes.${index}.code`]"
                                            :disabled="form.processing"
                                            class="flex-1"
                                        />
                                        <!-- CPT Code Value Input -->
                                        <v-form-input
                                            type="number"
                                            v-model="cpt.value"
                                            :error="form.errors[`cptCodes.${index}.value`]"
                                            :disabled="form.processing"
                                            placeholder="Value"
                                            class="flex-1"
                                        />
                                        <!-- Remove Button -->
                                        <v-button
                                            @click="removeCptCode(index)"
                                            color="red"
                                            class="self-end"
                                        >
                                            Remove
                                        </v-button>
                                    </div>
                                    <!-- Add CPT Code Button -->
                                    <v-button
                                        @click="addCptCode"
                                        color="primary"
                                        class="mt-4"
                                    >
                                        Add CPT Code
                                    </v-button>
                                </v-form-group>
                            </div>

                            <!-- Save Button -->
                            <div class="mt-6">
                                <v-button
                                    @click="saveCase"
                                    color="primary"
                                    :disabled="form.processing"
                                    class="w-full sm:w-auto"
                                >
                                    Save Billing Information
                                </v-button>
                            </div>
                        </div>
                    </div>
                </v-content-body>
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
                                            {{
                                                caseDetails.policy_limit_info?.policy_limit ||
                                                "N/A"
                                            }}
                                        </v-paragraph>
                                    </div>

                                    <!-- PIP -->
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <v-paragraph class="text-sm text-gray-600">
                                            PIP:
                                        </v-paragraph>
                                        <v-paragraph class="font-medium text-gray-800">
                                            {{
                                                caseDetails.policy_limit_info?.pip || "N/A"
                                            }}
                                        </v-paragraph>
                                    </div>

                                    <!-- Defendant Insurance -->
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <v-paragraph class="text-sm text-gray-600">
                                            Defendant Insurance:
                                        </v-paragraph>
                                        <v-paragraph class="font-medium text-gray-800">
                                            {{
                                                caseDetails.policy_limit_info
                                                    ?.defendant_insurance || "N/A"
                                            }}
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
                                            {{
                                                caseDetails.policy_limit_info
                                                    ?.plaintiff_insurance || "N/A"
                                            }}
                                        </v-paragraph>
                                    </div>

                                    <!-- Commercial Case -->
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <v-paragraph class="text-sm text-gray-600">
                                            Commercial Case:
                                        </v-paragraph>
                                        <v-paragraph class="font-medium text-gray-800">
                                            {{
                                                caseDetails.policy_limit_info
                                                    ?.commercial_case
                                                    ? "Yes"
                                                    : "No"
                                            }}
                                        </v-paragraph>
                                    </div>

                                    <!-- Type of Accident -->
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <v-paragraph class="text-sm text-gray-600">
                                            Type of Accident:
                                        </v-paragraph>
                                        <v-paragraph class="font-medium text-gray-800">
                                            {{
                                                caseDetails.policy_limit_info
                                                    ?.type_of_accident || "N/A"
                                            }}
                                        </v-paragraph>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </v-content-body>
            </div>

            <!-- LOP Management Tab -->
            <div v-if="activeTab === 'lop'">
                <v-content-body>
                    <!-- LOP Details Section -->
                    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
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
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                        />
                                    </svg>
                                    <span class="text-primary-500">LOP Details</span>
                                </div>
                            </template>
                        </v-section-heading>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mt-4">
                            <!-- LOP Date -->
                            <div>
                                <v-form-group>
                                    <v-form-label>LOP Date</v-form-label>
                                    <v-form-input
                                        type="date"
                                        v-model="lopForm.lop_date"
                                        :error="form.errors.lop_date"
                                        :disabled="form.processing"
                                    />
                                </v-form-group>
                            </div>

                            <!-- LOP Expiration Date -->
                            <div>
                                <v-form-group>
                                    <v-form-label>LOP Expiration Date</v-form-label>
                                    <v-form-input
                                        type="date"
                                        v-model="lopForm.lop_expiration_date"
                                        :error="form.errors.lop_expiration_date"
                                        :disabled="form.processing"
                                    />
                                </v-form-group>
                            </div>

                            <!-- LOP Status -->
                            <div>
                                <v-form-group>
                                    <v-form-label>LOP Status</v-form-label>
                                    <v-form-select
                                        v-model="lopForm.lop_status"
                                        :options="[
                                            { label: 'Active', value: 'active' },
                                            { label: 'Expired', value: 'expired' },
                                            { label: 'Revoked', value: 'revoked' },
                                        ]"
                                        :error="form.errors.lop_status"
                                        :disabled="form.processing"
                                    />
                                </v-form-group>
                            </div>

                            <!-- LOP Acknowledgment Received -->
                            <div>
                                <v-form-group>
                                    <v-form-label>LOP Acknowledgment Received</v-form-label>
                                    <v-form-select
                                        v-model="lopForm.lop_acknowledgment_received"
                                        :options="[
                                            { label: 'Yes', value: 'yes' },
                                            { label: 'No', value: 'no' },
                                        ]"
                                        :error="form.errors.lop_acknowledgment_received"
                                        :disabled="form.processing"
                                    />
                                </v-form-group>
                            </div>

                            <!-- LOP Acknowledgment Date -->
                            <div>
                                <v-form-group>
                                    <v-form-label>LOP Acknowledgment Date</v-form-label>
                                    <v-form-input
                                        type="date"
                                        v-model="lopForm.lop_acknowledgment_date"
                                        :error="form.errors.lop_acknowledgment_date"
                                        :disabled="form.processing"
                                    />
                                </v-form-group>
                            </div>

                            <!-- LOP Verification Status -->
                            <div>
                                <v-form-group>
                                    <v-form-label>LOP Verification Status</v-form-label>
                                    <v-form-select
                                        v-model="lopForm.lop_verification_status"
                                        :options="[
                                            { label: 'Verified', value: 'verified' },
                                            { label: 'Unverified', value: 'unverified' },
                                        ]"
                                        :error="form.errors.lop_verification_status"
                                        :disabled="form.processing"
                                    />
                                </v-form-group>
                            </div>

                            <!-- LOP Verification Date -->
                            <div>
                                <v-form-group>
                                    <v-form-label>LOP Verification Date</v-form-label>
                                    <v-form-input
                                        type="date"
                                        v-model="lopForm.lop_verification_date"
                                        :error="form.errors.lop_verification_date"
                                        :disabled="form.processing"
                                    />
                                </v-form-group>
                            </div>
                        </div>

                        <!-- LOP Document Upload -->
                        <div class="mt-6">
                            <v-form-group>
                                <v-form-label>LOP Document Upload</v-form-label>
                                <v-form-input
                                    type="file"
                                    v-model="lopForm.lop_document"
                                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                                    :error="form.errors.lop_document"
                                    :disabled="form.processing"
                                />
                                <small class="text-gray-500">Accepted formats: PDF, DOC, DOCX, JPG, JPEG, PNG</small>
                            </v-form-group>
                        </div>
                    </div>

                    <!-- Attorney/Law Firm Information Section -->
                    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
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
                                            d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"
                                        />
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M9.879 16.121A3 3 0 1012.015 11L11 14H9l1.878 2.121z"
                                        />
                                    </svg>
                                    <span class="text-primary-500">Attorney/Law Firm Information</span>
                                </div>
                            </template>
                        </v-section-heading>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mt-4">
                            <!-- Law Firm Name -->
                            <div class="sm:col-span-2">
                                <v-form-group>
                                    <v-form-label>Law Firm Name</v-form-label>
                                    <v-form-input
                                        v-model="lopForm.law_firm_name"
                                        :error="form.errors.law_firm_name"
                                        :disabled="form.processing"
                                        placeholder="Enter law firm name"
                                    />
                                </v-form-group>
                            </div>

                            <!-- Attorney Contact Person -->
                            <div>
                                <v-form-group>
                                    <v-form-label>Attorney Contact Person</v-form-label>
                                    <v-form-input
                                        v-model="lopForm.attorney_contact_person"
                                        :error="form.errors.attorney_contact_person"
                                        :disabled="form.processing"
                                        placeholder="Enter attorney name"
                                    />
                                </v-form-group>
                            </div>

                            <!-- Attorney Phone -->
                            <div>
                                <v-form-group>
                                    <v-form-label>Attorney Phone</v-form-label>
                                    <v-form-input
                                        v-model="lopForm.attorney_phone"
                                        :error="form.errors.attorney_phone"
                                        :disabled="form.processing"
                                        placeholder="Enter phone number"
                                    />
                                </v-form-group>
                            </div>

                            <!-- Attorney Fax -->
                            <div>
                                <v-form-group>
                                    <v-form-label>Attorney Fax</v-form-label>
                                    <v-form-input
                                        v-model="lopForm.attorney_fax"
                                        :error="form.errors.attorney_fax"
                                        :disabled="form.processing"
                                        placeholder="Enter fax number"
                                    />
                                </v-form-group>
                            </div>

                            <!-- Attorney Bar Number -->
                            <div>
                                <v-form-group>
                                    <v-form-label>Attorney Bar Number</v-form-label>
                                    <v-form-input
                                        v-model="lopForm.attorney_bar_number"
                                        :error="form.errors.attorney_bar_number"
                                        :disabled="form.processing"
                                        placeholder="Enter bar number"
                                    />
                                </v-form-group>
                            </div>

                            <!-- Attorney File/Claim Number -->
                            <div>
                                <v-form-group>
                                    <v-form-label>Attorney File/Claim Number</v-form-label>
                                    <v-form-input
                                        v-model="lopForm.attorney_file_number"
                                        :error="form.errors.attorney_file_number"
                                        :disabled="form.processing"
                                        placeholder="Enter file/claim number"
                                    />
                                </v-form-group>
                            </div>
                        </div>
                    </div>

                    <!-- Case/Litigation Information Section -->
                    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
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
                                            d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"
                                        />
                                    </svg>
                                    <span class="text-primary-500">Case/Litigation Information</span>
                                </div>
                            </template>
                        </v-section-heading>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mt-4">
                            <!-- Case Number -->
                            <div>
                                <v-form-group>
                                    <v-form-label>Case Number</v-form-label>
                                    <v-form-input
                                        v-model="lopForm.case_number"
                                        :error="form.errors.case_number"
                                        :disabled="form.processing"
                                        placeholder="Enter case number"
                                    />
                                </v-form-group>
                            </div>

                            <!-- Court/Jurisdiction -->
                            <div>
                                <v-form-group>
                                    <v-form-label>Court/Jurisdiction</v-form-label>
                                    <v-form-input
                                        v-model="lopForm.court_jurisdiction"
                                        :error="form.errors.court_jurisdiction"
                                        :disabled="form.processing"
                                        placeholder="Enter court/jurisdiction"
                                    />
                                </v-form-group>
                            </div>

                            <!-- Insurance Company Name -->
                            <div>
                                <v-form-group>
                                    <v-form-label>Insurance Company Name</v-form-label>
                                    <v-form-input
                                        v-model="lopForm.insurance_company_name"
                                        :error="form.errors.insurance_company_name"
                                        :disabled="form.processing"
                                        placeholder="Enter insurance company"
                                    />
                                </v-form-group>
                            </div>

                            <!-- Insurance Claim Number -->
                            <div>
                                <v-form-group>
                                    <v-form-label>Insurance Claim Number</v-form-label>
                                    <v-form-input
                                        v-model="lopForm.insurance_claim_number"
                                        :error="form.errors.insurance_claim_number"
                                        :disabled="form.processing"
                                        placeholder="Enter claim number"
                                    />
                                </v-form-group>
                            </div>

                            <!-- Date of Accident/Incident -->
                            <div>
                                <v-form-group>
                                    <v-form-label>Date of Accident/Incident</v-form-label>
                                    <v-form-input
                                        type="date"
                                        v-model="lopForm.accident_date"
                                        :error="form.errors.accident_date"
                                        :disabled="form.processing"
                                    />
                                </v-form-group>
                            </div>

                            <!-- Accident/Incident Description -->
                            <div class="sm:col-span-2">
                                <v-form-group>
                                    <v-form-label>Accident/Incident Description</v-form-label>
                                    <v-form-textarea
                                        v-model="lopForm.accident_description"
                                        :error="form.errors.accident_description"
                                        :disabled="form.processing"
                                        placeholder="Describe the accident/incident"
                                        rows="3"
                                    />
                                </v-form-group>
                            </div>
                        </div>
                    </div>

                    <!-- Financial Information Section -->
                    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
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
                                            d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.414M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                        />
                                    </svg>
                                    <span class="text-primary-500">Financial Information</span>
                                </div>
                            </template>
                        </v-section-heading>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mt-4">
                            <!-- Estimated Case Value -->
                            <div>
                                <v-form-group>
                                    <v-form-label>Estimated Case Value</v-form-label>
                                    <v-form-input
                                        type="number"
                                        v-model="lopForm.estimated_case_value"
                                        :error="form.errors.estimated_case_value"
                                        :disabled="form.processing"
                                        placeholder="0.00"
                                        step="0.01"
                                    />
                                </v-form-group>
                            </div>

                            <!-- Contingency Percentage -->
                            <div>
                                <v-form-group>
                                    <v-form-label>Contingency Percentage</v-form-label>
                                    <v-form-input
                                        type="number"
                                        v-model="lopForm.contingency_percentage"
                                        :error="form.errors.contingency_percentage"
                                        :disabled="form.processing"
                                        placeholder="0"
                                        min="0"
                                        max="100"
                                    />
                                </v-form-group>
                            </div>

                            <!-- Current Medical Specials -->
                            <div>
                                <v-form-group>
                                    <v-form-label>Current Medical Specials</v-form-label>
                                    <v-form-input
                                        type="number"
                                        v-model="lopForm.current_medical_specials"
                                        :error="form.errors.current_medical_specials"
                                        :disabled="form.processing"
                                        placeholder="0.00"
                                        step="0.01"
                                    />
                                </v-form-group>
                            </div>

                            <!-- Outstanding Balance -->
                            <div>
                                <v-form-group>
                                    <v-form-label>Outstanding Balance</v-form-label>
                                    <v-form-input
                                        type="number"
                                        v-model="lopForm.outstanding_balance"
                                        :error="form.errors.outstanding_balance"
                                        :disabled="form.processing"
                                        placeholder="0.00"
                                        step="0.01"
                                    />
                                </v-form-group>
                            </div>
                        </div>
                    </div>

                    <!-- Treatment Authorization Section -->
                    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
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
                                            d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                        />
                                    </svg>
                                    <span class="text-primary-500">Treatment Authorization</span>
                                </div>
                            </template>
                        </v-section-heading>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                            <!-- Authorized Treatment Types -->
                            <div>
                                <v-form-group>
                                    <v-form-label>Authorized Treatment Types</v-form-label>
                                    <v-form-textarea
                                        v-model="lopForm.authorized_treatment_types"
                                        :error="form.errors.authorized_treatment_types"
                                        :disabled="form.processing"
                                        placeholder="List authorized treatment types"
                                        rows="3"
                                    />
                                </v-form-group>
                            </div>

                            <!-- Treatment Limitations -->
                            <div>
                                <v-form-group>
                                    <v-form-label>Treatment Limitations</v-form-label>
                                    <v-form-textarea
                                        v-model="lopForm.treatment_limitations"
                                        :error="form.errors.treatment_limitations"
                                        :disabled="form.processing"
                                        placeholder="List any treatment limitations"
                                        rows="3"
                                    />
                                </v-form-group>
                            </div>

                            <!-- Authorization Expiration Date -->
                            <div>
                                <v-form-group>
                                    <v-form-label>Authorization Expiration Date</v-form-label>
                                    <v-form-input
                                        type="date"
                                        v-model="lopForm.authorization_expiration_date"
                                        :error="form.errors.authorization_expiration_date"
                                        :disabled="form.processing"
                                    />
                                </v-form-group>
                            </div>
                        </div>
                    </div>

                    <!-- Save All LOP Information Button -->
                    <div class="flex justify-center mt-8">
                        <v-button
                            @click="saveAllLopInformation"
                            color="primary"
                            :disabled="form.processing"
                            class="px-8 py-3 text-lg"
                        >
                            Save All LOP Information
                        </v-button>
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
                    <div
                        class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mt-4"
                    >
                        <!-- Iterate over referrals -->
                        <div
                            v-for="referral in referrals"
                            :key="referral.referral_id"
                            class="bg-gray-50 p-4 rounded-lg shadow-sm"
                        >
                            <div class="flex flex-col gap-2">
                                <v-heading :size="6" class="font-medium">
                                    <!-- Ensure referral.referral_id is valid before generating the route -->
                                    <v-link
                                        v-if="referral.referral_id"
                                        :href="
                                            route('panel.admin.referrals.show', {
                                                referral: referral.referral_id,
                                            })
                                        "
                                        class="text-primary-500 hover:underline"
                                    >
                                        Referral #{{ referral.referral_id }}
                                    </v-link>
                                    <span v-else class="text-gray-500"
                                        >Referral ID Missing</span
                                    >
                                </v-heading>
                                <!-- Format created_at in a human-readable manner -->
                                <v-paragraph
                                    >Created:
                                    {{
                                        formatDate(referral.created_at) || "N/A"
                                    }}</v-paragraph
                                >

                                <!-- Display Reduction Request Details -->
                                <div
                                    v-if="
                                        referral.reduction_requests &&
                                        referral.reduction_requests.length > 0
                                    "
                                    class="mt-4"
                                >
                                    <v-heading :size="6" class="font-medium"
                                        >Reduction Requests</v-heading
                                    >
                                    <div
                                        v-for="reductionRequest in referral.reduction_requests"
                                        :key="reductionRequest.id"
                                        class="mt-2"
                                    >
                                        <v-paragraph
                                            >Amount: ${{
                                                reductionRequest.amount
                                            }}</v-paragraph
                                        >
                                        <v-paragraph class="mt-2">
                                            Status:
                                            <span
                                                :class="
                                                    getStatusBadgeClass(
                                                        reductionRequest.referral_status
                                                    )
                                                "
                                            >
                                                {{
                                                    getStatusText(
                                                        reductionRequest.referral_status
                                                    )
                                                }}
                                            </span>
                                        </v-paragraph>
                                        <v-paragraph class="mt-4">
                                            Doctor Decision:
                                            <span
                                                :class="
                                                    getDoctorDecisionBadgeClass(
                                                        reductionRequest.doctor_decision
                                                    )
                                                "
                                            >
                                                {{
                                                    getDoctorDecisionText(
                                                        reductionRequest.doctor_decision
                                                    )
                                                }}
                                            </span>
                                        </v-paragraph>
                                        <v-paragraph
                                            v-if="reductionRequest.counter_offer"
                                        >
                                            Counter Offer: ${{
                                                reductionRequest.counter_offer
                                            }}
                                        </v-paragraph>
                                        <v-paragraph
                                            v-if="reductionRequest.file_path"
                                        >
                                            <a
                                                :href="reductionRequest.file_link"
                                                target="_blank"
                                                class="text-primary-500 hover:underline"
                                                >Download File</a
                                            >
                                        </v-paragraph>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex gap-2 mt-2">
                                    <v-button
                                        @click="
                                            markReferralComplete(
                                                referral.referral_id
                                            )
                                        "
                                        class="bg-green-500 text-white flex-1"
                                    >
                                        Complete
                                    </v-button>
                                    <!-- Disable the Reduction button if a reduction request already exists -->
                                    <v-button
                                        @click="
                                            openReductionModal(
                                                referral.referral_id
                                            )
                                        "
                                        :disabled="
                                            referral.reduction_requests &&
                                            referral.reduction_requests.length >
                                                0
                                        "
                                        class="bg-orange-500 text-white flex-1"
                                        :class="{
                                            'opacity-50 cursor-not-allowed':
                                                referral.reduction_requests &&
                                                referral.reduction_requests
                                                    .length > 0,
                                        }"
                                    >
                                        Reduction
                                    </v-button>
                                    <v-button
                                        @click="
                                            markReferralLost(referral.referral_id)
                                        "
                                        class="bg-red-500 text-white flex-1"
                                    >
                                        Lost
                                    </v-button>
                                </div>
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
        </div>
    </div>
</template>
<script>
import Layout from "@/layouts/panel/admin/index.vue";
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
        icdCodeDescription: {
            type: String,
            required: false,
            default: 'N/A'
        },
        cptCodeDescriptions: {
            type: Array,
            required: false,
            default: () => []
        },
        referrals: {
            type: Array,
            required: false,
            default: () => []
        },
        allCptCodes: {
            type: Array,
            required: false,
            default: () => []
        },
    },
    data() {
        return {
            // Tabs configuration
            tabs: [
                { name: "patient", label: "Patient Information" },
                { name: "attorney", label: "Attorney Information" },
                { name: "billing", label: "Billing Information" },
                { name: "policy", label: "Policy Limit Information", show: () => this.caseDetails?.billing_type !== 'LOP' },
                { name: "lop", label: "LOP Management" },
                { name: "referrals", label: "Referrals" },
                { name: "cms", label: "CMS 1500 Form" },
            ],
            activeTab: "lop", // Default active tab to LOP
            billingType: this.caseDetails?.billing_type || "LOP", // Default billing type
            form: {
                cptCodes: (() => {
                    try {
                        if (this.caseDetails?.cpt_codes) {
                            const parsed = JSON.parse(this.caseDetails.cpt_codes);
                            return Array.isArray(parsed) ? parsed : [];
                        }
                        return [];
                    } catch (error) {
                        console.warn('Error parsing CPT codes:', error);
                        return [];
                    }
                })(),
                processing: false,
                errors: {},
            },
            isModalOpen: false, // Reduction modal state
            reductionAmount: null, // Reduction amount input
            reductionFile: null, // Reduction file input
            isFormValid: true, // Form validation state
            selectedReferralId: null, // Selected referral ID for reduction

            formData: null,
            savedFormId: null,

            // LOP Form Data
            lopForm: {
                // LOP Details
                lop_date: this.caseDetails.lop_date || null,
                lop_expiration_date: this.caseDetails.lop_expiration_date || null,
                lop_status: this.caseDetails.lop_status || 'active',
                lop_acknowledgment_received: this.caseDetails.lop_acknowledgment_received || 'no',
                lop_acknowledgment_date: this.caseDetails.lop_acknowledgment_date || null,
                lop_verification_status: this.caseDetails.lop_verification_status || 'unverified',
                lop_verification_date: this.caseDetails.lop_verification_date || null,
                lop_document: null,

                // Attorney/Law Firm Information
                law_firm_name: this.caseDetails.law_firm_name || '',
                attorney_contact_person: this.caseDetails.attorney_contact_person || '',
                attorney_phone: this.caseDetails.attorney_phone || '',
                attorney_fax: this.caseDetails.attorney_fax || '',
                attorney_bar_number: this.caseDetails.attorney_bar_number || '',
                attorney_file_number: this.caseDetails.attorney_file_number || '',

                // Case/Litigation Information
                case_number: this.caseDetails.case_number || '',
                court_jurisdiction: this.caseDetails.court_jurisdiction || '',
                insurance_company_name: this.caseDetails.insurance_company_name || '',
                insurance_claim_number: this.caseDetails.insurance_claim_number || '',
                accident_date: this.caseDetails.accident_date || null,
                accident_description: this.caseDetails.accident_description || '',

                // Financial Information
                estimated_case_value: this.caseDetails.estimated_case_value || '',
                contingency_percentage: this.caseDetails.contingency_percentage || '',
                current_medical_specials: this.caseDetails.current_medical_specials || '',
                outstanding_balance: this.caseDetails.outstanding_balance || '',

                // Treatment Authorization
                authorized_treatment_types: this.caseDetails.authorized_treatment_types || '',
                treatment_limitations: this.caseDetails.treatment_limitations || '',
                authorization_expiration_date: this.caseDetails.authorization_expiration_date || null,
            },
        };
    },
    methods: {
        // Add a new CPT code
        addCptCode() {
            this.form.cptCodes.push({ code: null, value: 0 });
        },
        // Remove a CPT code
        removeCptCode(index) {
            this.form.cptCodes.splice(index, 1);
        },
        // Save billing information
        async saveCase() {
            const data = {
                billing_type: this.billingType,
                cpt_codes: JSON.stringify(this.form.cptCodes),
                is_cms1500_generated: this.billingType === "LOP" ? 1 : 0,
            };

            try {
                const response = await axios.post(
                    route("panel.admin.newcase.updateBilling", {
                        case: this.caseDetails.case_id,
                    }),
                    data,
                    {
                        headers: {
                            "Content-Type": "application/json",
                        },
                    }
                );
                this.$toast().success("Billing information updated successfully.");
                this.$inertia.reload(); // Reload the page to reflect changes
            } catch (error) {
                this.$toast().error("Error updating billing information.");
                console.error("Error:", error.response ? error.response.data : error.message);
            }
        },
        // Download CMS 1500 Form
        downloadCMS1500Form() {
            // Logic to download the CMS 1500 form
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
        },
        // Submit reduction request
        async submitReduction() {
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
                        "/reduction-requests",
                        formData,
                        {
                            headers: { "Content-Type": "multipart/form-data" },
                        }
                    );
                    this.$toast().info("Reduction request sent successfully.");
                    this.closeReductionModal();
                    this.$inertia.reload(); // Refresh the referrals data
                } catch (error) {
                    this.$toast().error("Error sending reduction request.");
                    console.error("Error:", error.response.data);
                }
            }
        },
        // Format date in a human-readable manner
        formatDate(dateString) {
            if (!dateString) return "N/A";

            const date = new Date(dateString);
            if (isNaN(date)) return "Invalid Date";

            return new Intl.DateTimeFormat("en-US", {
                year: "numeric",
                month: "long",
                day: "numeric",
                hour: "2-digit",
                minute: "2-digit",
                second: "2-digit",
            }).format(date);
        },
        // Get status badge class
        getStatusBadgeClass(status) {
            switch (status) {
                case "pending":
                    return "bg-yellow-500 text-white px-2 py-1 rounded text-sm";
                case "reduction_request_sent":
                    return "bg-blue-500 text-white px-2 py-1 rounded text-sm";
                case "accepted":
                    return "bg-green-500 text-white px-2 py-1 rounded text-sm";
                case "rejected":
                    return "bg-red-500 text-white px-2 py-1 rounded text-sm";
                default:
                    return "bg-gray-500 text-white px-2 py-1 rounded text-sm";
            }
        },
        // Get status text
        getStatusText(status) {
            switch (status) {
                case "pending":
                    return "Pending";
                case "reduction_request_sent":
                    return "Reduction Request Sent";
                case "accepted":
                    return "Accepted";
                case "rejected":
                    return "Rejected";
                default:
                    return "Unknown";
            }
        },
        // Get doctor decision badge class
        getDoctorDecisionBadgeClass(decision) {
            switch (decision) {
                case "pending":
                    return "bg-yellow-500 text-white px-2 py-1 mb-2 rounded text-sm";
                case "accepted":
                    return "bg-green-500 text-white px-2 py-1 mb-2 rounded text-sm";
                case "rejected":
                    return "bg-red-500 text-white px-2 py-1 mb-2 rounded text-sm";
                default:
                    return "bg-gray-500 text-white px-2 py-1 mb-2 rounded text-sm";
            }
        },
        // Get doctor decision text
        getDoctorDecisionText(decision) {
            switch (decision) {
                case "pending":
                    return "Pending";
                case "accepted":
                    return "Accepted";
                case "rejected":
                    return "Rejected";
                default:
                    return "Unknown";
            }
        },

        async handleFormSubmit(data) {
            this.formData = data;
            await this.saveFormToDatabase(data, 'completed');
        },

        async handleFormSave(data) {
            this.formData = data;
            await this.saveFormToDatabase(data, 'draft');
        },

        async saveFormToDatabase(data, status) {
            try {
                const response = await axios.post(
                    route('panel.admin.cases.saveForm', {
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

        async saveFormForLater() {
            if (this.formData) {
                await this.saveFormToDatabase(this.formData, 'draft');
            } else {
                this.$toast().warning('No form data to save');
            }
        },

        async downloadFormPDF() {
            if (!this.savedFormId) {
                this.$toast().warning('Please save the form first');
                return;
            }

            try {
                const response = await axios.get(
                    route('panel.admin.cases.downloadForm', {
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

        // Save all LOP information
        async saveAllLopInformation() {
            const formData = new FormData();
            formData.append("case_id", this.caseDetails.case_id);

            // Append LOP details
            formData.append("lop_date", this.lopForm.lop_date);
            formData.append("lop_expiration_date", this.lopForm.lop_expiration_date);
            formData.append("lop_status", this.lopForm.lop_status);
            formData.append("lop_acknowledgment_received", this.lopForm.lop_acknowledgment_received);
            formData.append("lop_acknowledgment_date", this.lopForm.lop_acknowledgment_date);
            formData.append("lop_verification_status", this.lopForm.lop_verification_status);
            formData.append("lop_verification_date", this.lopForm.lop_verification_date);
            if (this.lopForm.lop_document) {
                formData.append("lop_document", this.lopForm.lop_document);
            }

            // Append Attorney/Law Firm Information
            formData.append("law_firm_name", this.lopForm.law_firm_name);
            formData.append("attorney_contact_person", this.lopForm.attorney_contact_person);
            formData.append("attorney_phone", this.lopForm.attorney_phone);
            formData.append("attorney_fax", this.lopForm.attorney_fax);
            formData.append("attorney_bar_number", this.lopForm.attorney_bar_number);
            formData.append("attorney_file_number", this.lopForm.attorney_file_number);

            // Append Case/Litigation Information
            formData.append("case_number", this.lopForm.case_number);
            formData.append("court_jurisdiction", this.lopForm.court_jurisdiction);
            formData.append("insurance_company_name", this.lopForm.insurance_company_name);
            formData.append("insurance_claim_number", this.lopForm.insurance_claim_number);
            formData.append("accident_date", this.lopForm.accident_date);
            formData.append("accident_description", this.lopForm.accident_description);

            // Append Financial Information
            formData.append("estimated_case_value", this.lopForm.estimated_case_value);
            formData.append("contingency_percentage", this.lopForm.contingency_percentage);
            formData.append("current_medical_specials", this.lopForm.current_medical_specials);
            formData.append("outstanding_balance", this.lopForm.outstanding_balance);

            // Append Treatment Authorization
            formData.append("authorized_treatment_types", this.lopForm.authorized_treatment_types);
            formData.append("treatment_limitations", this.lopForm.treatment_limitations);
            formData.append("authorization_expiration_date", this.lopForm.authorization_expiration_date);

            try {
                const response = await axios.post(
                    route("panel.admin.cases.saveLopInformation", {
                        case: this.caseDetails.case_id,
                    }),
                    formData,
                    {
                        headers: { "Content-Type": "multipart/form-data" },
                    }
                );
                this.$toast().success("LOP information updated successfully.");
                this.$inertia.reload(); // Reload the page to reflect changes
            } catch (error) {
                this.$toast().error("Error updating LOP information.");
                console.error("Error:", error.response ? error.response.data : error.message);
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
