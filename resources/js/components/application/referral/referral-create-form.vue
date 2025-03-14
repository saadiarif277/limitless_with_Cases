<template>
    <v-form class="h-full" @submit.prevent="submitForm">
        <div class="h-full divide-y divide-gray-200 space-y-12">
            <!-- Referral Information Section -->
            <div>
                <v-content-body class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
                    <!-- Referral Date -->
                    <v-form-group>
                        <v-form-label>Referral Date</v-form-label>
                        <v-form-input type="date" v-model="form.referral_date" :error="form.errors.referral_date"
                            :disabled="form.processing" />
                        <v-form-error v-if="form.errors.referral_date">{{ form.errors.referral_date }}</v-form-error>
                    </v-form-group>

                    <!-- Referral Status -->
                    <v-form-group>
                        <v-form-label>Referral Status</v-form-label>
                        <v-form-select
                            :options="referralStatuses.data.map((referralStatus) => ({ label: referralStatus.name, value: referralStatus.referral_status_id }))"
                            :error="form.errors.referral_status_id" :disabled="form.processing" :required="true"
                            v-model="form.referral_status_id" />
                        <v-form-error v-if="form.errors.referral_status_id">{{ form.errors.referral_status_id }}</v-form-error>
                    </v-form-group>

                    <!-- Referral State -->
                    <v-form-group>
                        <v-form-label>Referral State</v-form-label>
                        <v-form-select
                            :options="(referralStates || states).data.map((state) => ({ label: state.name, value: state.state_id }))"
                            :error="form.errors.state_id" :disabled="form.processing" :required="true"
                            v-model="form.state_id" />
                        <v-form-error v-if="form.errors.state_id">{{ form.errors.state_id }}</v-form-error>
                    </v-form-group>

                    <!-- CPT Codes -->
                    <v-form-group>
                        <v-form-label>CPT Codes</v-form-label>
                        <v-form-select
                            :options="(CptCodes?.data ?? []).map((code) => ({ label: code.code + ' - ' + code.description, value: code.id }))"
                            :error="form.errors.cpt_code_id" :disabled="form.processing" :required="true" multiple
                            v-model="form.selectedCptCodes" />
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
                            :options="(physicians || physicians).data.map((physician) => ({ label: physician.name, value: physician.user_id }))"
                            :error="form.errors.doctor_user_id" :disabled="form.processing" :required="true"
                            v-model="form.doctor_user_id" />
                        <v-form-error v-if="form.errors.doctor_user_id">{{ form.errors.doctor_user_id }}</v-form-error>
                    </v-form-group>



                    <!-- Billing Type -->
                    <v-form-group>
                        <v-form-label>Billing Type</v-form-label>
                        <v-form-select
                            :options="['LOP', 'Insurance'].map((type) => ({ label: type, value: type }))"
                            :error="form.errors.billing_type" :disabled="form.processing" :required="true"
                            v-model="form.billing_type" />
                        <v-form-error v-if="form.errors.billing_type">{{ form.errors.billing_type }}</v-form-error>
                    </v-form-group>







                </v-content-body>
            </div>

            <!-- Other Sections -->
            <x-policy-form @update:insuranceData="handleInsuranceData" />
            <x-referral-doctor-form :attorneys="attorneys" :doctors="doctors" :document-categories="documentCategories"
                :form="form" :medical-specialties="medicalSpecialties" :patients="patients"
                :referral-reasons="referralReasons" :referral-statuses="referralStatuses" :states="states" />

            <x-referral-patient-form :attorneys="attorneys" :doctors="doctors" :document-categories="documentCategories"
                :form="form" :medical-specialties="medicalSpecialties" :patients="patients"
                :referral-reasons="referralReasons" :referral-statuses="referralStatuses" :states="states" :icdCodes="icdCodes" />

            <x-referral-attorney-form :attorneys="attorneys" :doctors="doctors"
                :document-categories="documentCategories" :form="form" :medical-specialties="medicalSpecialties"
                :patients="patients" :referral-reasons="referralReasons" :referral-statuses="referralStatuses"
                :states="states" />

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

                <v-button :disabled="form.processing">
                    Create Referral
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
    },
    data() {
        return {
            form: useForm({
                referral_date: new Date().toISOString().split('T')[0],
                referral_status_id: 1,
                state_id: this.$page.props.auth.user.state_id || "",
                attorney: {
                    name: "",
                    email: "",
                    phone_number: "",
                    law_firm: {
                        name: "",
                        email: "",
                        phone_number: "",
                        address_line_1: "",
                        address_line_2: "",
                        city: "",
                        state_id: "",
                        zip_code: "",
                    },
                },
                doctor: {
                    user_id: "",
                    name: "",
                    email: "",
                    phone_number: "",
                    clinic: {
                        clinic_id: "",
                        name: "",
                        email: "",
                        phone_number: "",
                        address_line_1: "",
                        address_line_2: "",
                        city: "",
                        state_id: "",
                        zip_code: "",
                    },
                    notes: "",
                },
                patient: {
                    name: "",
                    email: "",
                    phone_number: "",
                    height: "",
                    weight: "",
                    gender: "",
                    birthdate: "",
                    injury_date: "",
                    address_line_1: "",
                    address_line_2: "",
                    city: "",
                    state_id: "",
                    zip_code: "",
                    referral_reason_ids: [],
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
        };
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
        submitForm() {
            this.form.post(this.route(this.storeRoute), {
                onSuccess: () => this.$toast().success("Referral created successfully!"),
                onError: (errors) => {
                    Object.keys(errors).forEach((key) => {
                        this.$toast().error(errors[key]);
                    });
                },
            });
        },
        getCptLabel(codeId) {
            const code = (this.CptCodes?.data ?? []).find(c => c.id === codeId);
            return code ? `${code.code} - ${code.description}` : 'Unknown';
        },
        removeCptCode(codeId) {
            this.form.selectedCptCodes = this.form.selectedCptCodes.filter(id => id !== codeId);
        },
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
