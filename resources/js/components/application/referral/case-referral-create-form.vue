<template>
    <v-form class="h-full" @submit.prevent="submitForm">
        <div class="h-full divide-y divide-gray-200 space-y-12">
            <!-- Case Details Section -->
            <div>
                <v-content-body class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6">
                    <!-- Case Status -->
                    <v-form-group>
                        <v-form-label>Case Status</v-form-label>
                        <v-form-select
                            :options="referralStatuses.data.map((referralStatus) => ({ label: referralStatus.name, value: referralStatus.referral_status_id }))"
                            :error="form.errors.referral_status_id"
                            :disabled="form.processing"
                            :required="true"
                            v-model="form.referral_status_id"
                        />
                        <v-form-error v-if="form.errors.referral_status_id">{{ form.errors.referral_status_id }}</v-form-error>
                    </v-form-group>

                    <!-- Case State -->
                    <v-form-group>
                        <v-form-label>Case State</v-form-label>
                        <v-form-select
                            :options="(states).data.map((state) => ({ label: state.name, value: state.state_id }))"
                            :error="form.errors.state_id"
                            :disabled="form.processing"
                            :required="true"
                            v-model="form.state_id"
                        />
                        <v-form-error v-if="form.errors.state_id">{{ form.errors.state_id }}</v-form-error>
                    </v-form-group>

                    <!-- Patient Selection -->
                    <v-form-group>
                        <v-form-label>Patient</v-form-label>
                        <v-form-select
                            :options="patients.data.map((patient) => ({ label: patient.name, value: patient.user_id }))"
                            :error="form.errors.patient_id"
                            :disabled="form.processing"
                            :required="true"
                            v-model="form.patient_id"
                        />
                        <v-form-error v-if="form.errors.patient_id">{{ form.errors.patient_id }}</v-form-error>
                    </v-form-group>
                </v-content-body>
            </div>

            <!-- Physician and Attorney Section -->
            <div>
                <v-content-body class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6">
                    <!-- Doctor Selection -->
                    <v-form-group>
                        <v-form-label>Doctor</v-form-label>
                        <v-form-select
                            :options="doctors.data.map((doctor) => ({ label: doctor.name, value: doctor.user_id }))"
                            :error="form.errors.doctor_id"
                            :disabled="form.processing"
                            :required="true"
                            v-model="form.doctor_id"
                        />
                        <v-form-error v-if="form.errors.doctor_id">{{ form.errors.doctor_id }}</v-form-error>
                    </v-form-group>

                    <!-- Piloting Physician -->
                    <v-form-group>
                        <v-form-label>Piloting Physician</v-form-label>
                        <v-form-select
                            :options="doctors.data.map((doctor) => ({ label: doctor.name, value: doctor.user_id }))"
                            :error="form.errors.piloting_physician_id"
                            :disabled="form.processing"
                            :required="true"
                            v-model="form.piloting_physician_id"
                        />
                        <v-form-error v-if="form.errors.piloting_physician_id">{{ form.errors.piloting_physician_id }}</v-form-error>
                    </v-form-group>

                    <!-- Attorney Selection -->
                    <v-form-group>
                        <v-form-label>Attorney</v-form-label>
                        <v-form-select
                            :options="attorneys.data.map((attorney) => ({ label: attorney.name, value: attorney.user_id }))"
                            :error="form.errors.attorney_id"
                            :disabled="form.processing"
                            :required="true"
                            v-model="form.attorney_id"
                        />
                        <v-form-error v-if="form.errors.attorney_id">{{ form.errors.attorney_id }}</v-form-error>
                    </v-form-group>
                </v-content-body>
            </div>

            <!-- Policy and Insurance Details Section -->
            <div>
                <v-content-body class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6">
                    <!-- Policy Limit Info -->
                    <v-form-group>
                        <v-form-label>Policy Limit Info</v-form-label>
                        <v-form-input
                            type="text"
                            v-model="form.policy_limit_info"
                            :error="form.errors.policy_limit_info"
                            :disabled="form.processing"
                        />
                        <v-form-error v-if="form.errors.policy_limit_info">{{ form.errors.policy_limit_info }}</v-form-error>
                    </v-form-group>

                    <!-- PIP (Personal Injury Protection) -->
                    <v-form-group>
                        <v-form-label>PIP (Personal Injury Protection)</v-form-label>
                        <v-form-checkbox
                            v-model="form.pip"
                            :error="form.errors.pip"
                            :disabled="form.processing"
                        />
                        <v-form-error v-if="form.errors.pip">{{ form.errors.pip }}</v-form-error>
                    </v-form-group>

                    <!-- Defendant Insurance -->
                    <v-form-group>
                        <v-form-label>Defendant Insurance</v-form-label>
                        <v-form-input
                            type="text"
                            v-model="form.defendant_insurance"
                            :error="form.errors.defendant_insurance"
                            :disabled="form.processing"
                        />
                        <v-form-error v-if="form.errors.defendant_insurance">{{ form.errors.defendant_insurance }}</v-form-error>
                    </v-form-group>

                    <!-- Plaintiff Insurance -->
                    <v-form-group>
                        <v-form-label>Plaintiff Insurance</v-form-label>
                        <v-form-input
                            type="text"
                            v-model="form.plaintiff_insurance"
                            :error="form.errors.plaintiff_insurance"
                            :disabled="form.processing"
                        />
                        <v-form-error v-if="form.errors.plaintiff_insurance">{{ form.errors.plaintiff_insurance }}</v-form-error>
                    </v-form-group>

                    <!-- Commercial Case -->
                    <v-form-group>
                        <v-form-label>Commercial Case</v-form-label>
                        <v-form-checkbox
                            v-model="form.commercial_case"
                            :error="form.errors.commercial_case"
                            :disabled="form.processing"
                        />
                        <v-form-error v-if="form.errors.commercial_case">{{ form.errors.commercial_case }}</v-form-error>
                    </v-form-group>

                    <!-- Type of Accident -->
                    <v-form-group>
                        <v-form-label>Type of Accident</v-form-label>
                        <v-form-select
                            :options="[
                                { label: 'Motor Vehicle', value: 'motor_vehicle' },
                                { label: 'Slip and Fall', value: 'slip_and_fall' },
                                { label: 'Other', value: 'other' }
                            ]"
                            :error="form.errors.type_of_accident"
                            :disabled="form.processing"
                            v-model="form.type_of_accident"
                        />
                        <v-form-error v-if="form.errors.type_of_accident">{{ form.errors.type_of_accident }}</v-form-error>
                    </v-form-group>
                </v-content-body>
            </div>

            <!-- ICD Codes Section -->
            <div>
                <v-content-body class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6">
                    <!-- ICD Codes -->
                    <v-form-group>
                        <v-form-label>ICD Codes</v-form-label>
                        <v-form-select
                            :options="icdCodes.data.map((icdCode) => ({ label: `${icdCode.code} - ${icdCode.description}`, value: icdCode.code }))"
                            :error="form.errors.icd_codes"
                            :disabled="form.processing"
                            :multiple="true"
                            v-model="form.icd_codes"
                        />
                        <v-form-error v-if="form.errors.icd_codes">{{ form.errors.icd_codes }}</v-form-error>
                    </v-form-group>
                </v-content-body>
            </div>

            <!-- Referrals Section -->
            <div>
                <v-content-body class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6">
                    <!-- Referrals -->
                    <v-form-group>
                        <v-form-label>Referrals</v-form-label>
                        <v-form-select
                            :options="referrals.data.map((referral) => ({ label: `Referral #${referral.referral_id} - ${referral.patient_user.name}`, value: referral.referral_id }))"
                            :error="form.errors.referral_ids"
                            :disabled="form.processing"
                            :multiple="true"
                            v-model="form.referral_ids"
                        />
                        <v-form-error v-if="form.errors.referral_ids">{{ form.errors.referral_ids }}</v-form-error>
                    </v-form-group>
                </v-content-body>
            </div>

            <!-- Submit Button -->
            <div class="text-right mt-4">
                <v-button color="primary" dark type="submit">Create Case</v-button>
            </div>
        </div>
    </v-form>
</template>

<script>
import { useForm } from "@inertiajs/vue3";

export default {
    name: "CaseReferralCreateForm",
    props: {
        attorneys: { type: Object, required: true },
        doctors: { type: Object, required: true },
        documentCategories: { type: Object, required: false },
        medicalSpecialties: { type: Object, required: true },
        patients: { type: Object, required: true },
        referralReasons: { type: Object, required: false },
        referralStatuses: { type: Object, required: false },
        referralStates: { type: Object, required: false },
        states: { type: Object, required: false },
        icdCodes: { type: Object, required: true },
        referrals: { type: Object, required: true },
        listRoute: { type: String, default: "panel.admin.referrals.index" },
        storeRoute: { type: String, default: "panel.admin.referrals.store" },
    },
    created() {
        console.log("Props received:", this.$props);
    },
    data() {
        return {
            form: useForm({
                referral_status_id: 1,
                state_id: this.$page.props.auth.user.state_id || "",
                patient_id: null,
                doctor_id: null,
                piloting_physician_id: null,
                attorney_id: null,
                policy_limit_info: "",
                pip: false,
                defendant_insurance: "",
                plaintiff_insurance: "",
                commercial_case: false,
                type_of_accident: "motor_vehicle",
                icd_codes: [],
                referral_ids: [],
                cpt_codes: "",
                service_billed: 0.0,
                is_cms1500_generated: false,
                billing_type: "",
                case_won: false,
                outcome: "",
                reduction_accepted: false,
                is_closed: false,
                closed_at: null,
            }),
        };
    },
    methods: {
        submitForm() {
            this.form.post(this.route(this.storeRoute), {
                onSuccess: () => this.$toast().success("Case created successfully!"),
                onError: (errors) => {
                    Object.keys(errors).forEach((key) => {
                        this.$toast().error(errors[key]);
                    });
                },
            });
        },
    },
};
</script>
