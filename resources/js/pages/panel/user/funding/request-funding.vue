<template>
    <v-inertia-head title="Request Funding" />

    <div class="h-full flex flex-col">
        <v-content-body class="border-b border-gray-200">
            <v-section-heading>
                <template #title>
                    Request Funding
                </template>

                <template #description>
                    Submit your funding request to LIMITLESS for approval. Please fill out all required fields accurately.
                </template>
            </v-section-heading>
        </v-content-body>

        <div class="flex-1 p-6">
            <div class="max-w-4xl mx-auto">
                <!-- Company Header -->
                <div class="bg-gradient-to-r from-blue-600 to-blue-800 rounded-lg p-6 mb-8 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-2xl font-bold mb-2">LIMITLESS Funding Request</h2>
                            <p class="text-blue-100">Submit your funding request for medical equipment, research, or practice development</p>
                        </div>
                        <div class="text-right">
                            <div class="text-3xl font-bold text-blue-200">LIMITLESS</div>
                            <div class="text-sm text-blue-100">Funding Solutions</div>
                        </div>
                    </div>
                </div>

                <!-- Funding Request Form -->
                <form @submit.prevent="submitForm" class="space-y-8">
                    <!-- Doctor Information Section -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            Doctor Information
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Full Name *</label>
                                <input
                                    v-model="form.doctorName"
                                    type="text"
                                    required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="Dr. John Smith"
                                >
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Medical License Number *</label>
                                <input
                                    v-model="form.licenseNumber"
                                    type="text"
                                    required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="MD123456"
                                >
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Medical Specialty *</label>
                                <select
                                    v-model="form.specialty"
                                    required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                >
                                    <option value="">Select Specialty</option>
                                    <option value="cardiology">Cardiology</option>
                                    <option value="neurology">Neurology</option>
                                    <option value="orthopedics">Orthopedics</option>
                                    <option value="pediatrics">Pediatrics</option>
                                    <option value="dermatology">Dermatology</option>
                                    <option value="oncology">Oncology</option>
                                    <option value="psychiatry">Psychiatry</option>
                                    <option value="surgery">Surgery</option>
                                    <option value="internal_medicine">Internal Medicine</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Years of Practice *</label>
                                <input
                                    v-model="form.yearsOfPractice"
                                    type="number"
                                    required
                                    min="0"
                                    max="50"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="15"
                                >
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Email Address *</label>
                                <input
                                    v-model="form.email"
                                    type="email"
                                    required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="doctor@example.com"
                                >
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number *</label>
                                <input
                                    v-model="form.phone"
                                    type="tel"
                                    required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="+1 (555) 123-4567"
                                >
                            </div>
                        </div>
                    </div>

                    <!-- Practice Information Section -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            Practice Information
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Practice Name *</label>
                                <input
                                    v-model="form.practiceName"
                                    type="text"
                                    required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="Smith Medical Group"
                                >
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Practice Type *</label>
                                <select
                                    v-model="form.practiceType"
                                    required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                >
                                    <option value="">Select Practice Type</option>
                                    <option value="private_practice">Private Practice</option>
                                    <option value="hospital">Hospital</option>
                                    <option value="clinic">Clinic</option>
                                    <option value="research_institution">Research Institution</option>
                                    <option value="academic">Academic Medical Center</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Practice Address *</label>
                                <input
                                    v-model="form.practiceAddress"
                                    type="text"
                                    required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="123 Medical Center Dr, Suite 100"
                                >
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">City *</label>
                                <input
                                    v-model="form.city"
                                    type="text"
                                    required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="New York"
                                >
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">State *</label>
                                <input
                                    v-model="form.state"
                                    type="text"
                                    required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="NY"
                                >
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">ZIP Code *</label>
                                <input
                                    v-model="form.zipCode"
                                    type="text"
                                    required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="10001"
                                >
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Number of Patients Served Monthly</label>
                                <input
                                    v-model="form.patientsPerMonth"
                                    type="number"
                                    min="0"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="150"
                                >
                            </div>
                        </div>
                    </div>

                    <!-- Funding Request Details Section -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                            </svg>
                            Funding Request Details
                        </h3>

                        <!-- Funding Amount Notice -->
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                            <div class="flex items-start">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600 mr-2 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div>
                                    <h4 class="text-sm font-medium text-blue-800">Funding Amount</h4>
                                    <p class="text-sm text-blue-700 mt-1">
                                        The funding amount will be determined by LIMITLESS based on your request details, practice needs, and available funding programs.
                                        You do not need to specify an amount in this form.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Funding Purpose *</label>
                                <select
                                    v-model="form.fundingPurpose"
                                    required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                >
                                    <option value="">Select Purpose</option>
                                    <option value="medical_equipment">Medical Equipment</option>
                                    <option value="practice_expansion">Practice Expansion</option>
                                    <option value="research_project">Research Project</option>
                                    <option value="technology_upgrade">Technology Upgrade</option>
                                    <option value="staff_training">Staff Training</option>
                                    <option value="facility_renovation">Facility Renovation</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Detailed Description *</label>
                                <textarea
                                    v-model="form.description"
                                    required
                                    rows="4"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="Please provide a detailed description of your funding request, including how the funds will be used and the expected impact on your practice and patients..."
                                ></textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Timeline for Use *</label>
                                <select
                                    v-model="form.timeline"
                                    required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                >
                                    <option value="">Select Timeline</option>
                                    <option value="immediate">Immediate (0-3 months)</option>
                                    <option value="short_term">Short Term (3-6 months)</option>
                                    <option value="medium_term">Medium Term (6-12 months)</option>
                                    <option value="long_term">Long Term (1+ years)</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Expected ROI/Impact</label>
                                <input
                                    v-model="form.expectedROI"
                                    type="text"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="e.g., 25% increase in patient capacity"
                                >
                            </div>
                        </div>
                    </div>

                    <!-- Supporting Documents Section -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Supporting Documents
                        </h3>

                        <!-- File Upload Notice -->
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                            <div class="flex items-start">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-600 mr-2 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                </svg>
                                <div>
                                    <h4 class="text-sm font-medium text-yellow-800">Document Upload</h4>
                                    <p class="text-sm text-yellow-700 mt-1">
                                        After submitting this form, LIMITLESS will contact you to request any additional supporting documents needed for your funding application.
                                        You may upload documents at that time.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div class="text-center py-8">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <p class="text-gray-500 text-sm">
                                    Supporting documents will be requested by LIMITLESS after initial review of your application.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Declaration Section -->
                    <div class="bg-gray-50 rounded-lg p-6">
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input
                                    v-model="form.declaration"
                                    type="checkbox"
                                    required
                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                >
                            </div>
                            <div class="ml-3 text-sm">
                                <label class="font-medium text-gray-700">Declaration</label>
                                <p class="text-gray-500 mt-1">
                                    I hereby declare that all information provided in this funding request is true, accurate, and complete to the best of my knowledge.
                                    I understand that LIMITLESS may verify this information and that providing false information may result in the rejection of my application.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end space-x-4">
                        <button
                            type="button"
                            @click="resetForm"
                            class="px-6 py-3 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                        >
                            Reset Form
                        </button>
                        <button
                            type="submit"
                            :disabled="isSubmitting"
                            class="px-8 py-3 bg-blue-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <span v-if="isSubmitting" class="flex items-center">
                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Submitting...
                            </span>
                            <span v-else>Submit Funding Request</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import Layout from "@/layouts/panel/user/index.vue";

export default {
    name: "RequestFunding",
    layout: Layout,
    data() {
        return {
            isSubmitting: false,
            form: {
                doctorName: '',
                licenseNumber: '',
                specialty: '',
                yearsOfPractice: '',
                email: '',
                phone: '',
                practiceName: '',
                practiceType: '',
                practiceAddress: '',
                city: '',
                state: '',
                zipCode: '',
                patientsPerMonth: '',
                fundingPurpose: '',
                description: '',
                timeline: '',
                expectedROI: '',
                declaration: false
            }
        };
    },
    methods: {
        async submitForm() {
            this.isSubmitting = true;

            try {
                // Send the form data to the backend
                const response = await this.$inertia.post(route('panel.user.funding.store'), this.form);

                // Show success message
                this.$inertia.visit(route('panel.user.dashboard'), {
                    method: 'get',
                    data: { message: 'Funding request submitted successfully! LIMITLESS will review your request and contact you soon.' }
                });
            } catch (error) {
                console.error('Error submitting form:', error);
                // Handle error appropriately
            } finally {
                this.isSubmitting = false;
            }
        },

        resetForm() {
            this.form = {
                doctorName: '',
                licenseNumber: '',
                specialty: '',
                yearsOfPractice: '',
                email: '',
                phone: '',
                practiceName: '',
                practiceType: '',
                practiceAddress: '',
                city: '',
                state: '',
                zipCode: '',
                patientsPerMonth: '',
                fundingPurpose: '',
                description: '',
                timeline: '',
                expectedROI: '',
                declaration: false
            };
        }
    }
};
</script>
