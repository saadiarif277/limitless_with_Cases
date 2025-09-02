<template>
    <v-form class="h-full" @submit.prevent="submitForm">
        <!-- Error Display -->
        <div v-if="Object.keys(form.errors).length > 0" class="mb-6 p-4 bg-red-50 border border-red-200 rounded-md">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">Please fix the following errors:</h3>
                    <div class="mt-2 text-sm text-red-700">
                        <ul class="list-disc pl-5 space-y-1">
                            <li v-for="(error, field) in form.errors" :key="field">
                                {{ error }}
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Success Message -->
        <div v-if="showSuccessMessage" class="mb-6 p-4 bg-green-50 border border-green-200 rounded-md">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM5.293 9.293a1 1 0 011.414 0L9 11.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-green-800">Referral created successfully!</h3>
                    <div class="mt-2 text-sm text-green-700">
                        Your referral has been submitted and is now being processed.
                    </div>
                </div>
            </div>
        </div>

        <div class="h-full divide-y divide-gray-200 space-y-12">
            <!-- Referral Information Section -->
            <div>
                <v-content-body class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
                    <!-- Referral Date -->
                    <v-form-group>
                        <v-form-label>Referral Date</v-form-label>
                        <v-form-input type="date" v-model="form.referral_date" :error="form.errors.referral_date"
                            :disabled="form.processing" @change="onFormFieldChange" />
                        <v-form-error v-if="form.errors.referral_date">{{ form.errors.referral_date }}</v-form-error>
                    </v-form-group>

                    <!-- Referral Status -->
                    <v-form-group>
                        <v-form-label>Referral Status</v-form-label>
                        <v-form-select
                            :options="(referralStatuses?.data || []).map((referralStatus) => ({ label: referralStatus.name, value: referralStatus.referral_status_id }))"
                            :error="form.errors.referral_status_id" :disabled="form.processing" :required="true"
                            v-model="form.referral_status_id" @change="onFormFieldChange" />
                        <v-form-error v-if="form.errors.referral_status_id">{{ form.errors.referral_status_id }}</v-form-error>
                    </v-form-group>

                    <!-- Injury Date -->
                    <v-form-group>
                        <v-form-label>Injury Date</v-form-label>
                        <v-form-input type="date" v-model="form.patient.injury_date" :error="form.errors['patient.injury_date']"
                            :disabled="form.processing" :required="true" @change="onFormFieldChange" />
                        <v-form-error v-if="form.errors['patient.injury_date']">{{ form.errors['patient.injury_date'] }}</v-form-error>
                    </v-form-group>

                    <!-- Referral State -->
                    <v-form-group class="col-span-full md:col-span-2">
                        <v-form-label><span class="text-blue-500 italic">Referral State</span></v-form-label>
                        <div class="flex items-center gap-2">
                            <v-form-select
                                :options="availableStates"
                                :error="form.errors.state_id"
                                :disabled="form.processing || isAttorney"
                                :required="true"
                                v-model="form.state_id"
                                @change="onFormFieldChange"
                                class="flex-1"
                            />
                            <div v-if="isUpdatingData" class="flex items-center gap-2 text-sm text-gray-500">
                                <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-blue-500"></div>
                                Updating...
                            </div>
                            <div v-if="isAttorney" class="text-sm text-gray-500 italic">
                                (Attorney's state)
                            </div>
                        </div>
                        <div v-if="isAttorney" class="mt-2 text-sm text-blue-600 bg-blue-50 p-2 rounded">
                            <strong>Note:</strong> As an attorney, you can only create referrals within your assigned state.
                            Only doctors and patients from your state will be available for selection.
                        </div>
                        <v-form-error v-if="form.errors.state_id">{{ form.errors.state_id }}</v-form-error>
                        <div v-if="form.state_id" class="text-sm text-gray-600 mt-1">
                            Selected: {{ currentStateName }}
                        </div>
                    </v-form-group>

                    <!-- CPT Codes -->
                    <v-form-group>
                        <v-form-label>CPT Codes</v-form-label>
                        <v-form-select
                            :options="getCptCodeOptions()"
                            :error="form.errors.cpt_code_id" :disabled="form.processing" :required="true" multiple
                            v-model="form.selectedCptCodes" @change="onFormFieldChange" />
                        <!-- Display selected CPT codes as badges -->
                        <div v-if="form.selectedCptCodes.length" class="selected-codes">
                            <span v-for="(code, index) in form.selectedCptCodes" :key="index" class="badge">
                                {{ getCptLabel(code) }}
                                <button type="button" @click="removeCptCode(code)" class="remove-btn">×</button>
                            </span>
                        </div>
                        <v-form-error v-if="form.errors.cpt_code_id">{{ form.errors.cpt_code_id }}</v-form-error>
                    </v-form-group>

                    <!-- Piloting Physician -->
                    <v-form-group>
                        <v-form-label>Piloting Physician</v-form-label>
                        <v-form-select
                            :options="(physicians?.data || []).map((physician) => ({ label: physician.name, value: physician.user_id }))"
                            :error="form.errors.doctor_user_id" :disabled="form.processing" :required="true"
                            v-model="form.doctor_user_id" @change="onFormFieldChange" />
                        <v-form-error v-if="form.errors.doctor_user_id">{{ form.errors.doctor_user_id }}</v-form-error>
                    </v-form-group>



                    <!-- Billing Type -->
                    <v-form-group>
                        <v-form-label>Billing Type</v-form-label>
                        <v-form-select
                            :options="['LOP', 'Insurance'].map((type) => ({ label: type, value: type }))"
                            :error="form.errors.billing_type" :disabled="form.processing" :required="true"
                            v-model="form.billing_type" @change="onFormFieldChange" />
                        <v-form-error v-if="form.errors.billing_type">{{ form.errors.billing_type }}</v-form-error>
                    </v-form-group>







                </v-content-body>
            </div>

            <!-- Other Sections -->
            <x-policy-form @update:insuranceData="handleInsuranceData" />

            <x-referral-doctor-form :attorneys="doctorsData" :doctors="doctorsData" :document-categories="documentCategories"
                :form="form" :medical-specialties="medicalSpecialties" :patients="patientsData"
                :referral-reasons="referralReasons" :referral-statuses="referralStatuses" :states="states" />

            <x-referral-patient-form :attorneys="attorneysData" :doctors="doctorsData" :document-categories="documentCategories"
                :form="form" :medical-specialties="medicalSpecialties" :patients="patientsData"
                :referral-reasons="referralReasons" :referral-statuses="referralStatuses" :states="states" :icdCodes="icdCodes" />

            <!-- Referral Reasons Section -->
            <div class="col-span-full bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Referral Reasons</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div v-for="reason in (referralReasons?.data || [])" :key="reason.referral_reason_id" class="flex items-center">
                        <input
                            type="checkbox"
                            :id="'reason_' + reason.referral_reason_id"
                            :value="reason.referral_reason_id"
                            v-model="form.patient.referral_reason_ids"
                            @change="onFormFieldChange"
                            class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                        />
                        <label :for="'reason_' + reason.referral_reason_id" class="ml-2 text-sm text-gray-700">
                            {{ reason.name }}
                        </label>
                    </div>
                </div>
                <v-form-error v-if="form.errors['patient.referral_reason_ids']">{{ form.errors['patient.referral_reason_ids'] }}</v-form-error>
            </div>

            <x-referral-attorney-form
                :attorneys="attorneysData"
                :form="form"
                :user-role="userRole"
                :patients="patientsData"
                :referral-reasons="referralReasons"
                :states="states"
            />

            <x-referral-documents-form :attorneys="attorneys" :doctors="doctors"
                :document-categories="documentCategories" :form="form" :medical-specialties="medicalSpecialties"
                :patients="patients" :referral-reasons="referralReasons" :referral-statuses="referralStatuses"
                :states="states" />

        <!-- Submit Button -->
            <v-form-group
                class="col-span-full flex items-center justify-end gap-2 text-right border-t border-gray-200 bg-gray-50 px-6 py-4">
                <progress v-if="form.progress" :value="form.progress.percentage" max="100">
                    {{ form.progress.percentage }}%
                </progress>

                <v-button :href="route(listRoute)" color="white" :disabled="form.processing">
                    Cancel
                </v-button>

                <v-button @click="resetForm" color="gray" :disabled="form.processing">
                    Reset
                </v-button>

                <v-button
                    :disabled="form.processing || !isFormReady()"
                    :class="{ 'opacity-50': !isFormReady() }"
                >
                    {{ isFormReady() ? 'Create Referral' : 'Please fill required fields' }}
                </v-button>
            </v-form-group>
        </div>
    </v-form>
</template>

<script>
import ReferralAttorneyForm from "./_referral-attorney-form.vue";
import ReferralDoctorForm from "./_referral-doctor-form.vue";
import ReferralDocumentsForm from "./_referral-documents-form.vue";
import ReferralPatientForm from "./_referral-patient-form.vue";
import Policyform from "./_policy-form.vue";
import { useForm } from "@inertiajs/vue3";

export default {
    name: "ReferralsCreateForm",
    components: {
        "x-referral-attorney-form": ReferralAttorneyForm,
        "x-referral-doctor-form": ReferralDoctorForm,
        "x-referral-documents-form": ReferralDocumentsForm,
        "x-referral-patient-form": ReferralPatientForm,
        "x-policy-form": Policyform,
    },
    props: {
        attorneys: { type: Object, required: true },
        doctors: { type: Object, required: true },
        physicians: { type: Object, required: true },
        documentCategories: { type: Object, required: false },
        medicalSpecialties: { type: Object, required: true },
        patients: { type: Object, required: true },
        referralReasons: { type: Object, required: false },
        referralStatuses: { type: Object, required: false },
        referralStates: { type: Object, required: false },
        states: { type: Object, required: false },
        listRoute: { type: String, default: "panel.admin.referrals.index" },
        storeRoute: { type: String, default: "panel.admin.referrals.store" },
        icdCodes: { type: Object, required: false },
        CptCodes: { type: Object, required: false },
        selectedStateId: { type: [String, Number], required: false, default: null },
    },
    data() {
        const currentUser = this.$page.props.auth.user;
        const userRole = currentUser?.roles?.[0]?.name;

        // Auto-select attorney or doctor based on user role
        let autoSelectedAttorney = null;
        let autoSelectedDoctor = null;

        if (userRole === 'Attorney') {
            // Find the current user in attorneys list and auto-select
            const attorneyData = (this.attorneys?.data || []).find(attorney => attorney.user_id === currentUser.user_id);
            if (attorneyData) {
                autoSelectedAttorney = {
                    name: attorneyData.name,
                    email: attorneyData.email,
                    phone_number: attorneyData.phone_number || "",
                    user_id: attorneyData.user_id, // Add user_id field
                    law_firm: {
                        name: attorneyData.law_firm?.name || "",
                        email: attorneyData.law_firm?.email || "",
                        phone_number: attorneyData.law_firm?.phone_number || "",
                        address_line_1: attorneyData.law_firm?.address_line_1 || "",
                        address_line_2: attorneyData.law_firm?.address_line_2 || "",
                        city: attorneyData.law_firm?.city || "",
                        state_id: attorneyData.law_firm?.state_id || currentUser.state_id || "",
                        zip_code: attorneyData.law_firm?.zip_code || "",
                    },
                };
            } else {
                // If attorney data not found in list, create from current user data
                console.warn('Attorney data not found in attorneys list, using current user data');
                autoSelectedAttorney = {
                    name: currentUser.name,
                    email: currentUser.email,
                    phone_number: currentUser.phone_number || "",
                    user_id: currentUser.user_id,
                    law_firm: {
                        name: currentUser.lawFirm?.name || "",
                        email: currentUser.lawFirm?.email || "",
                        phone_number: currentUser.lawFirm?.phone_number || "",
                        address_line_1: currentUser.lawFirm?.address_line_1 || "",
                        address_line_2: currentUser.lawFirm?.address_line_2 || "",
                        city: currentUser.lawFirm?.city || "",
                        state_id: currentUser.lawFirm?.state_id || currentUser.state_id || "",
                        zip_code: currentUser.lawFirm?.zip_code || "",
                    },
                };
            }
        } else if (userRole === 'Doctor') {
            // Find the current user in doctors list and auto-select
            const doctorData = (this.doctors?.data || []).find(doctor => doctor.user_id === currentUser.user_id);
            if (doctorData) {
                autoSelectedDoctor = {
                    user_id: doctorData.user_id,
                    name: doctorData.name,
                    email: doctorData.email,
                    phone_number: doctorData.phone_number || "",
                    medical_specialty_id: doctorData.medical_specialty_id || null, // Add required medical_specialty_id
                    clinic: {
                        clinic_id: doctorData.clinics?.[0]?.clinic_id || "",
                        name: doctorData.clinics?.[0]?.name || "",
                        email: doctorData.clinics?.[0]?.email || "",
                        phone_number: doctorData.clinics?.[0]?.phone_number || "",
                        address_line_1: doctorData.clinics?.[0]?.address_line_1 || "",
                        address_line_2: doctorData.clinics?.[0]?.address_line_2 || "",
                        city: doctorData.clinics?.[0]?.city || "",
                        state_id: doctorData.clinics?.[0]?.state_id || currentUser.state_id || "",
                        zip_code: doctorData.clinics?.[0]?.zip_code || "",
                    },
                    notes: "",
                };
            }
        }

        return {
            isInitializing: true, // Flag to prevent redirects during initialization
            form: useForm({
                referral_date: new Date().toISOString().split('T')[0],
                referral_status_id: 1,
                state_id: currentUser?.state_id || "",

                // Top-level user IDs that the repository expects
                attorney_user_id: autoSelectedAttorney?.user_id || "",
                doctor_user_id: autoSelectedDoctor?.user_id || "",
                patient_user_id: "",
                clinic_id: autoSelectedDoctor?.clinic?.clinic_id || "",

                attorney: autoSelectedAttorney || {
                    name: "",
                    email: "",
                    phone_number: "",
                    user_id: "", // Add user_id field
                    law_firm: {
                        name: "",
                        email: "",
                        phone_number: "",
                        address_line_1: "",
                        address_line_2: "",
                        city: "",
                        state_id: currentUser?.state_id || "",
                        zip_code: "",
                    },
                },
                doctor: autoSelectedDoctor || {
                    user_id: "",
                    name: "",
                    email: "",
                    phone_number: "",
                    medical_specialty_id: null,
                    clinic: {
                        clinic_id: "",
                        name: "",
                        email: "",
                        phone_number: "",
                        address_line_1: "",
                        address_line_2: "",
                        city: "",
                        state_id: currentUser?.state_id || "",
                        zip_code: "",
                    },
                    notes: "",
                },
                patient: {
                    name: "",
                    email: "",
                    phone_number: "",
                    user_id: "",
                    height: "",
                    weight: "",
                    gender: "",
                    birthdate: "",
                    injury_date: new Date().toISOString().split('T')[0],
                    referral_reason_ids: [],
                    address_line_1: "",
                    address_line_2: "",
                    city: "",
                    state_id: currentUser?.state_id || "",
                    zip_code: "",
                },
                documents: {},
                selectedCptCodes: [],
                insuranceData: {},
                billing_type: "",
                is_cms1500_generated: false,
                case_won: false,
                outcome: "",
                reduction_accepted: false,
                is_closed: false,
                closed_at: null,
                created_at: null,
                updated_at: null,
            }),
            // Local copies of data that can be updated
            localAttorneys: this.attorneys || { data: [] },
            localDoctors: this.doctors || { data: [] },
            localPatients: this.patients || { data: [] },
            isUpdatingData: false,
            showSuccessMessage: false,
        };
    },
    computed: {
        currentUser() {
            return this.$page.props.auth.user;
        },
        userRole() {
            return this.currentUser?.roles?.[0]?.name;
        },
        isAttorney() {
            return this.userRole === 'Attorney';
        },
        isDoctor() {
            return this.userRole === 'Doctor';
        },
        isCaseManager() {
            return this.userRole === 'Case_manager';
        },
        isAdmin() {
            return this.userRole === 'Administrator' || this.userRole === 'Admin';
        },
        // Get available states for the dropdown
        availableStates() {
            // For attorneys, only show their own state
            if (this.isAttorney && this.currentUser?.state_id) {
                const userState = this.referralStates?.data?.find(state => state.state_id == this.currentUser.state_id);
                if (userState) {
                    return [{
                        label: userState.name,
                        value: userState.state_id
                    }];
                }
            }

            // For admin users and other roles, show all states
            const states = this.referralStates?.data || this.states?.data || [];
            return states.map(state => ({
                label: state.name,
                value: state.state_id
            }));
        },
        // Get current state name for display
        currentStateName() {
            const state = this.availableStates.find(s => s.value == this.form.state_id);
            return state ? state.label : 'Not selected';
        },
                // Use local data for attorneys
        attorneysData() {
            // For attorneys, only show attorneys from their own state
            if (this.isAttorney && this.currentUser?.state_id) {
                const filteredAttorneys = this.localAttorneys?.data?.filter(attorney =>
                    attorney.law_firm?.state_id == this.currentUser.state_id
                ) || [];
                return { data: filteredAttorneys };
            }
            // For admin users, return the data as-is (already filtered by backend)
            if (this.isAdmin) {
                return this.localAttorneys;
            }
            return this.localAttorneys;
        },
                // Use local data for doctors
        doctorsData() {
            // For attorneys, only show doctors from their state
            if (this.isAttorney && this.currentUser?.state_id) {
                const filteredDoctors = this.localDoctors?.data?.filter(doctor =>
                    doctor.clinics?.some(clinic => clinic.state_id == this.currentUser.state_id)
                ) || [];
                return { data: filteredDoctors };
            }
            // For admin users, return the data as-is (already filtered by backend)
            if (this.isAdmin) {
                return this.localDoctors;
            }
            return this.localDoctors;
        },
                // Use local data for patients
        patientsData() {
            // For attorneys, only show patients from their state
            if (this.isAttorney && this.currentUser?.state_id) {
                const filteredPatients = this.localPatients?.data?.filter(patient =>
                    patient.state_id == this.currentUser.state_id
                ) || [];
                return { data: filteredPatients };
            }
            // For admin users, return the data as-is (already filtered by backend)
            if (this.isAdmin) {
                return this.localPatients;
            }
            return this.localPatients;
        },
    },
    watch: {
        // Keep top-level fields in sync with nested data
        'form.attorney.user_id'(newValue) {
            this.form.attorney_user_id = newValue;
        },
        'form.doctor.user_id'(newValue) {
            this.form.doctor_user_id = newValue;
        },
        'form.patient.user_id'(newValue) {
            this.form.patient_user_id = newValue;
        },
        'form.doctor.clinic.clinic_id'(newValue) {
            this.form.clinic_id = newValue;
        },
                // Watch for state changes to update available doctors and patients
        'form.state_id'(newStateId, oldStateId) {
            // Skip redirects during initialization
            if (this.isInitializing) {
                return;
            }

            if (newStateId && newStateId !== oldStateId) {
                // For attorneys, prevent changing to a different state
                if (this.isAttorney && this.currentUser?.state_id && newStateId != this.currentUser.state_id) {
                    this.form.state_id = this.currentUser.state_id; // Revert
                    return;
                }
                // For admin users, redirect to the same page with new state_id
                // Only redirect if the current URL doesn't already have the correct state_id
                if (this.isAdmin || !this.isAttorney) {
                    const currentUrl = new URL(window.location.href);
                    const currentStateId = currentUrl.searchParams.get('state_id');

                    // Only redirect if the URL state_id is different from the form state_id
                    if (currentStateId != newStateId) {
                        this.redirectWithState(newStateId);
                    }
                }
            }
        },
        // Watch for doctor selection to ensure medical_specialty_id is set
        'form.doctor.user_id'(newDoctorId) {
            if (newDoctorId) {
                const selectedDoctor = this.doctorsData?.data?.find(doctor => doctor.user_id == newDoctorId);
                if (selectedDoctor) {
                    this.form.doctor.medical_specialty_id = selectedDoctor.medical_specialty_id || 1;
                }
            }
        },
        // Note: Local data watchers removed since we're using URL-based state changes
        // Watch for any form changes to hide success message
        'form.data()'() {
            if (this.showSuccessMessage) {
                this.showSuccessMessage = false;
            }
        }
    },
    methods: {
        handleInsuranceData(data) {
            this.form.insuranceData = data;
        },
        handleCloseChange() {
            if (this.form.is_closed) {
                this.form.closed_at = new Date().toISOString();
            } else {
                this.form.closed_at = null;
            }
        },
        // Method to submit the form
        submitForm() {
            // Validate form before submission
            const errors = this.validateForm();
            if (errors.length > 0) {
                errors.forEach(error => {
                    this.$toast().error(error);
                });
                return;
            }

            // Hide success message if it was showing
            this.showSuccessMessage = false;

            // Submit the form
            this.form.post(this.route(this.storeRoute), {
                onSuccess: () => {
                    this.$toast().success('Referral created successfully!');
                    this.showSuccessMessage = true; // Show success message
                    this.form.reset(); // Reset form fields
                    this.form.clearErrors(); // Clear errors
                },
                onError: (errors) => {
                    this.$toast().error('Failed to create referral. Please check the form and try again.');
                }
            });
        },
        validateForm() {
            const errors = [];

            // Check required fields
            if (!this.form.referral_date) {
                errors.push("Referral date is required");
            }

            if (!this.form.referral_status_id) {
                errors.push("Referral status is required");
            }

            if (!this.form.state_id) {
                errors.push("Referral state is required");
            }

            if (!this.form.patient.injury_date) {
                errors.push("Injury date is required");
            }

            if (!this.form.patient.referral_reason_ids || this.form.patient.referral_reason_ids.length === 0) {
                errors.push("At least one referral reason is required");
            }

            // Check role-specific requirements
            if (this.isAttorney) {
                if (!this.form.doctor_user_id && !this.form.doctor.name) {
                    errors.push("Doctor information is required for attorneys");
                }

                // Ensure attorney can only select doctor from their state
                if (this.form.doctor.user_id) {
                    const selectedDoctor = this.localDoctors?.data?.find(d => d.user_id == this.form.doctor.user_id);
                    if (selectedDoctor && this.currentUser?.state_id) {
                        const doctorInAttorneyState = selectedDoctor.clinics?.some(clinic =>
                            clinic.state_id == this.currentUser.state_id
                        );
                        if (!doctorInAttorneyState) {
                            errors.push("Selected doctor must be from your assigned state");
                        }
                    }
                }
            }

            // Check patient information
            if (!this.form.patient_user_id && !this.form.patient.name) {
                errors.push("Patient information is required");
            }

            // Ensure attorney can only select patient from their state
            if (this.isAttorney && this.form.patient.user_id && this.currentUser?.state_id) {
                const selectedPatient = this.localPatients?.data?.find(p => p.user_id == this.form.patient.user_id);
                if (selectedPatient && selectedPatient.state_id != this.currentUser.state_id) {
                    errors.push("Selected patient must be from your assigned state");
                }
            }

            return errors;
        },
        isFormReady() {
            // Debug logging
            console.log('isFormReady check:', {
                referral_date: this.form.referral_date,
                referral_status_id: this.form.referral_status_id,
                state_id: this.form.state_id,
                patient_injury_date: this.form.patient?.injury_date,
                patient_referral_reason_ids: this.form.patient?.referral_reason_ids,
                patient_name: this.form.patient?.name,
                patient_user_id: this.form.patient_user_id,
                doctor_user_id: this.form.doctor_user_id,
                doctor_name: this.form.doctor?.name,
                attorney_user_id: this.form.attorney_user_id,
                attorney_name: this.form.attorney?.name,
                localDoctors_length: this.localDoctors?.data?.length,
                localPatients_length: this.localPatients?.data?.length
            });

            // Check if all required fields are filled
            const requiredFields = [
                'referral_date',
                'referral_status_id',
                'state_id'
            ];

            for (const field of requiredFields) {
                if (!this.form[field]) {
                    console.log(`Field ${field} is missing:`, this.form[field]);
                    return false;
                }
            }

            // Check nested patient fields
            if (!this.form.patient?.injury_date) {
                console.log('Patient injury_date is missing');
                return false;
            }

            if (!this.form.patient?.referral_reason_ids || this.form.patient.referral_reason_ids.length === 0) {
                console.log('Patient referral_reason_ids is missing or empty:', this.form.patient?.referral_reason_ids);
                return false;
            }

            // Check if state is selected and data is available
            if (this.form.state_id) {
                if (this.localDoctors?.data?.length === 0) {
                    console.log('No doctors available for selected state');
                    return false;
                }
                if (this.localPatients?.data?.length === 0) {
                    console.log('No patients available for selected state');
                    return false;
                }
            }

            // Check role-specific requirements
            if (this.isDoctor && !this.form.attorney_user_id && !this.form.attorney.name) {
                console.log('Doctor role: attorney information is missing');
                return false;
            }

            if (this.isAttorney && !this.form.doctor_user_id && !this.form.doctor.name) {
                console.log('Attorney role: doctor information is missing');
                return false;
            }

            if (!this.form.patient_user_id && !this.form.patient.name) {
                console.log('Patient information is missing');
                return false;
            }

            console.log('Form is ready!');
            return true;
        },
        getCptLabel(codeId) {
            // CptCodes should now be an array from the backend
            const codes = Array.isArray(this.CptCodes) ? this.CptCodes : [];
            const code = codes.find(c => c.id === codeId);
            return code ? `${code.code} - ${code.description}` : 'Unknown';
        },
        removeCptCode(codeId) {
            this.form.selectedCptCodes = this.form.selectedCptCodes.filter(id => id !== codeId);
            this.onFormFieldChange();
        },
        // Method to manually trigger auto-selection if needed
        triggerAutoSelection() {
            if (this.isAttorney) {
                const attorneyData = (this.attorneys?.data || []).find(attorney => attorney.user_id === this.currentUser.user_id);
                if (attorneyData) {
                    this.form.attorney_user_id = attorneyData.user_id;
                    this.form.attorney = {
                        name: attorneyData.name,
                        email: attorneyData.email,
                        phone_number: attorneyData.phone_number || "",
                        user_id: attorneyData.user_id, // Add user_id field
                        law_firm: {
                            name: attorneyData.law_firm?.name || "",
                            email: attorneyData.law_firm?.email || "",
                            phone_number: attorneyData.law_firm?.phone_number || "",
                            address_line_1: attorneyData.law_firm?.address_line_1 || "",
                            address_line_2: attorneyData.law_firm?.address_line_2 || "",
                            city: attorneyData.law_firm?.city || "",
                            state_id: attorneyData.law_firm?.state_id || this.currentUser.state_id || "",
                            zip_code: attorneyData.law_firm?.zip_code || "",
                        },
                    };
                    this.onFormFieldChange(); // Hide success message when auto-selection happens
                } else {
                    // Use current user data if not found in attorneys list
                    console.warn('Attorney data not found in attorneys list, using current user data');
                    this.form.attorney_user_id = this.currentUser.user_id;
                    this.form.attorney = {
                        name: this.currentUser.name,
                        email: this.currentUser.email,
                        phone_number: this.currentUser.phone_number || "",
                        user_id: this.currentUser.user_id,
                        law_firm: {
                            name: this.currentUser.lawFirm?.name || "",
                            email: this.currentUser.lawFirm?.email || "",
                            phone_number: this.currentUser.lawFirm?.phone_number || "",
                            address_line_1: this.currentUser.lawFirm?.address_line_1 || "",
                            address_line_2: this.currentUser.lawFirm?.address_line_2 || "",
                            city: this.currentUser.lawFirm?.city || "",
                            state_id: this.currentUser.lawFirm?.state_id || this.currentUser.state_id || "",
                            zip_code: this.currentUser.lawFirm?.zip_code || "",
                        },
                    };
                    this.onFormFieldChange();
                }
            } else if (this.isDoctor) {
                const doctorData = (this.doctors?.data || []).find(doctor => doctor.user_id === this.currentUser.user_id);
                if (doctorData) {
                    this.form.doctor_user_id = doctorData.user_id;
                    this.form.clinic_id = doctorData.clinics?.[0]?.clinic_id || "";
                    this.form.doctor = {
                        user_id: doctorData.user_id,
                        name: doctorData.name,
                        email: doctorData.email,
                        phone_number: doctorData.phone_number || "",
                        medical_specialty_id: doctorData.medical_specialty_id || 1, // Default to first medical specialty if not set
                        clinic: {
                            clinic_id: doctorData.clinics?.[0]?.clinic_id || "",
                            name: doctorData.clinics?.[0]?.name || "",
                            email: doctorData.clinics?.[0]?.email || "",
                            phone_number: doctorData.clinics?.[0]?.phone_number || "",
                            address_line_1: doctorData.clinics?.[0]?.address_line_1 || "",
                            address_line_2: doctorData.clinics?.[0]?.address_line_2 || "",
                            city: doctorData.clinics?.[0]?.city || "",
                            state_id: doctorData.clinics?.[0]?.state_id || this.currentUser.state_id || "",
                            zip_code: doctorData.clinics?.[0]?.zip_code || "",
                        },
                        notes: "",
                    };
                    this.onFormFieldChange(); // Hide success message when auto-selection happens
                } else {
                    this.$toast().warning("Doctor data not found. Please contact administrator.");
                }
            }
        },
        // Method to clear success message
        clearSuccessMessage() {
            this.showSuccessMessage = false;
        },
        // Method to reset the form
        resetForm() {
            this.form.reset();
            this.form.clearErrors();
            this.showSuccessMessage = false;
            this.initializeFormState();
        },
        // Method to handle form field changes
        onFormFieldChange() {
            if (this.showSuccessMessage) {
                this.showSuccessMessage = false;
            }
        },
        redirectWithState(stateId) {
            if (!stateId) return;

            // Redirect to the same page with the new state_id parameter
            const currentUrl = new URL(window.location.href);
            currentUrl.searchParams.set('state_id', stateId);

            console.log('Redirecting to:', currentUrl.toString());

            // Use Inertia to navigate to the new URL
            this.$inertia.visit(currentUrl.toString(), {
                method: 'get',
                preserveState: false,
                preserveScroll: false,
                replace: true
            });
        },
        // Note: AJAX-based state updates have been replaced with URL-based redirects
        // This ensures proper data loading and avoids reactivity issues
        // Initialize the form state based on user's default state
        initializeFormState() {
            // Use selectedStateId prop if provided, otherwise fall back to user's state
            if (this.selectedStateId && !this.form.state_id) {
                this.form.state_id = this.selectedStateId;
            } else if (this.currentUser?.state_id && !this.form.state_id) {
                this.form.state_id = this.currentUser.state_id;
            }

            // For attorneys, ensure they can only use their state
            if (this.isAttorney && this.currentUser?.state_id) {
                this.form.state_id = this.currentUser.state_id;
            }

            // Mark initialization as complete
            this.isInitializing = false;
        },
        getCptCodeOptions() {
            // CptCodes should now be an array from the backend
            const codes = Array.isArray(this.CptCodes) ? this.CptCodes : [];

            // Safely map the codes
            return codes.map(code => ({
                label: `${code.code} - ${code.description}`,
                value: code.id
            }));
        }
    },
    mounted() {
        // Initialize local data with props data
        this.localAttorneys = this.attorneys || { data: [] };
        this.localDoctors = this.doctors || { data: [] };
        this.localPatients = this.patients || { data: [] };

        // Ensure auto-selection happens after component is mounted
        this.$nextTick(() => {
            this.triggerAutoSelection();
            this.initializeFormState();
        });
    },
};
</script>

<style scoped>
.selected-codes {
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
    margin-top: 10px;
}

.badge {
    background: #3498db;
    color: white;
    padding: 5px 10px;
    border-radius: 15px;
    display: flex;
    align-items: center;
}

.remove-btn {
    background: none;
    border: none;
    color: white;
    font-size: 14px;
    margin-left: 5px;
    cursor: pointer;
}

</style>
