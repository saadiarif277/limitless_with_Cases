<template>
    <v-form class="h-full" @submit.prevent="submitForm">
        <div class="h-full divide-y divide-gray-200 space-y-12">
            <!-- Case Details Section -->
            <div>
                <v-content-body class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6">
                    <!-- Case State -->
                    <v-form-group>
                        <v-form-label>Case State</v-form-label>
                        <v-form-select
                            :options="stateOptions"
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
                            :options="patientOptions"
                            :error="form.errors.patient_id"
                            :disabled="form.processing"
                            :required="true"
                            v-model="form.patient_id"
                        />
                        <v-form-error v-if="form.errors.patient_id">{{ form.errors.patient_id }}</v-form-error>
                    </v-form-group>

                    <!-- Case Type -->
                    <v-form-group>
                        <v-form-label>Case Type</v-form-label>
                        <v-form-select
                            :options="[
                                { label: 'Auto Accident', value: 'auto_accident' },
                                { label: 'Slip and Fall', value: 'slip_and_fall' },
                                { label: 'Workers Compensation', value: 'workers_comp' },
                                { label: 'Personal Injury', value: 'personal_injury' }
                            ]"
                            :error="form.errors.type_of_accident"
                            :disabled="form.processing"
                            :required="true"
                            v-model="form.type_of_accident"
                        />
                        <v-form-error v-if="form.errors.type_of_accident">{{ form.errors.type_of_accident }}</v-form-error>
                    </v-form-group>
                </v-content-body>
            </div>

            <!-- Role-specific Section -->
            <div>
                <v-content-body class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6">
                    <!-- Doctor-specific Fields -->
                    <template v-if="userRole === 'Doctor'">
                        <!-- Attorney Selection -->
                        <v-form-group>
                            <v-form-label>Select Attorney</v-form-label>
                            <v-form-select
                                :options="attorneyOptions"
                                :error="form.errors.attorney_id"
                                :disabled="form.processing"
                                :required="true"
                                v-model="form.attorney_id"
                            />
                            <v-form-error v-if="form.errors.attorney_id">{{ form.errors.attorney_id }}</v-form-error>
                        </v-form-group>

                        <!-- Billing Information -->
                        <v-form-group>
                            <v-form-label>Billing Type</v-form-label>
                            <v-form-select
                                :options="[
                                    { label: 'Insurance', value: 'Insurance' },
                                    { label: 'LOP', value: 'LOP' }
                                ]"
                                :error="form.errors.billing_type"
                                :disabled="form.processing"
                                :required="true"
                                v-model="form.billing_type"
                            />
                            <v-form-error v-if="form.errors.billing_type">{{ form.errors.billing_type }}</v-form-error>
                        </v-form-group>

                        <!-- Service Billed -->
                        <v-form-group>
                            <v-form-label>Service Billed</v-form-label>
                            <v-form-input
                                type="number"
                                :error="form.errors.service_billed"
                                :disabled="form.processing"
                                :required="true"
                                v-model="form.service_billed"
                            />
                            <v-form-error v-if="form.errors.service_billed">{{ form.errors.service_billed }}</v-form-error>
                        </v-form-group>

                        <!-- CPT Codes -->
                        <v-form-group class="col-span-full">
                            <v-form-label>CPT Codes</v-form-label>
                            <div v-for="(cpt, index) in selectedCptCodes" :key="index" class="flex gap-2 mb-2">
                                <v-form-select
                                    :options="cptCodeOptions"
                                    :error="form.errors[`cpt_codes.${index}.code`]"
                                    :disabled="form.processing"
                                    class="flex-1"
                                    v-model="cpt.code"
                                />
                                <v-form-input
                                    type="number"
                                    :error="form.errors[`cpt_codes.${index}.value`]"
                                    :disabled="form.processing"
                                    placeholder="Value"
                                    class="flex-1"
                                    v-model="cpt.value"
                                />
                                <v-button
                                    @click="removeCptCode(index)"
                                    color="red"
                                    class="self-end"
                                >
                                    Remove
                                </v-button>
                            </div>
                            <v-button
                                @click="addCptCode"
                                color="primary"
                                class="mt-4"
                            >
                                Add CPT Code
                            </v-button>
                        </v-form-group>
                    </template>

                    <!-- Attorney-specific Fields -->
                    <template v-if="userRole === 'Attorney'">
                        <!-- Doctor Selection -->
                        <v-form-group>
                            <v-form-label>Select Doctor</v-form-label>
                            <v-form-select
                                :options="doctorOptions"
                                :error="form.errors.piloting_physician_id"
                                :disabled="form.processing"
                                :required="true"
                                v-model="form.piloting_physician_id"
                            />
                            <v-form-error v-if="form.errors.piloting_physician_id">{{ form.errors.piloting_physician_id }}</v-form-error>
                        </v-form-group>

                        <!-- Policy Limit -->
                        <v-form-group>
                            <v-form-label>Policy Limit</v-form-label>
                            <v-form-input
                                type="number"
                                :error="form.errors.policy_limit"
                                :disabled="form.processing"
                                :required="true"
                                v-model="form.policy_limit"
                            />
                            <v-form-error v-if="form.errors.policy_limit">{{ form.errors.policy_limit }}</v-form-error>
                        </v-form-group>

                        <!-- PIP Coverage -->
                        <v-form-group>
                            <v-form-label>PIP Coverage</v-form-label>
                            <v-form-input
                                type="number"
                                :error="form.errors.pip_coverage"
                                :disabled="form.processing"
                                :required="true"
                                v-model="form.pip_coverage"
                            />
                            <v-form-error v-if="form.errors.pip_coverage">{{ form.errors.pip_coverage }}</v-form-error>
                        </v-form-group>

                        <!-- Commercial Case -->
                        <v-form-group>
                            <v-form-label>Commercial Case</v-form-label>
                            <v-form-checkbox
                                :error="form.errors.commercial_case"
                                :disabled="form.processing"
                                v-model="form.commercial_case"
                            />
                            <v-form-error v-if="form.errors.commercial_case">{{ form.errors.commercial_case }}</v-form-error>
                        </v-form-group>
                    </template>
                </v-content-body>
            </div>

            <!-- Referrals Section -->
            <div>
                <v-content-body class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6">
                    <!-- Referrals -->
                    <v-form-group>
                        <v-form-label>Referrals</v-form-label>
                        <v-form-select
                            :options="referralOptions"
                            :error="form.errors.referral_ids"
                            :disabled="form.processing"
                            :multiple="true"
                            v-model="selectedReferrals"
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
    name: "CaseCreateForm",
    props: {
        attorneys: { type: Object, required: true },
        doctors: { type: Object, required: true },
        patients: { type: Object, required: true },
        states: { type: Object, required: true },
        allCptCodes: { type: Object, required: true },
        referrals: { type: Object, required: true },
        userRole: { type: String, required: true },
        userId: { type: Number, required: true },
        listRoute: { type: String, default: "panel.user.cases.index" },
        storeRoute: { type: String, default: "panel.user.cases.store" },
    },
    data() {
        return {
            form: useForm({
                state_id: this.$page.props.auth.user.state_id || null,
                patient_id: null,
                attorney_id: this.userRole === 'Doctor' ? null : (this.userId || 0),
                piloting_physician_id: this.userRole === 'Attorney' ? null : (this.userId || 0),
                policy_limit_info: JSON.stringify({
                    policy_limit: "",
                    pip_coverage: "",
                    commercial_case: false
                }),
                type_of_accident: "",
                billing_type: this.userRole === 'Doctor' ? "Insurance" : null,
                service_billed: this.userRole === 'Doctor' ? "" : null,
                cpt_codes: this.userRole === 'Doctor' ? JSON.stringify([]) : null,
                referral_ids: [],
                primary_referral_id: null,
                is_cms1500_generated: false,
                case_won: false,
                outcome: "",
                reduction_accepted: false,
                is_closed: false,
                closed_at: null,
                // Attorney-specific fields
                policy_limit: this.userRole === 'Attorney' ? "" : null,
                pip_coverage: this.userRole === 'Attorney' ? "" : null,
                commercial_case: this.userRole === 'Attorney' ? false : null
            }),
            selectedCptCodes: this.userRole === 'Doctor' ? [] : null,
            selectedReferrals: []
        };
    },
    computed: {
        stateOptions() {
            return (this.states.data || []).map((state) => ({
                label: state.name,
                value: state.id
            }));
        },
        patientOptions() {
            return (this.patients.data || []).map((patient) => ({
                label: patient.name,
                value: patient.user_id
            }));
        },
        attorneyOptions() {
            return (this.attorneys.data || []).map((attorney) => ({
                label: attorney.name,
                value: attorney.user_id
            }));
        },
        doctorOptions() {
            return (this.doctors.data || []).map((doctor) => ({
                label: doctor.name,
                value: doctor.user_id
            }));
        },
        cptCodeOptions() {
            return (this.allCptCodes.data || []).map(c => ({
                label: `${c.code} - ${c.description}`,
                value: c.code
            }));
        },
        referralOptions() {
            return (this.referrals.data || []).map((referral) => ({
                label: `Referral #${referral.id} - ${referral.patient_user?.name || 'Unknown Patient'}`,
                value: referral.id
            }));
        }
    },
    methods: {
        addCptCode() {
            if (this.userRole === 'Doctor') {
                this.selectedCptCodes.push({
                    code: "",
                    value: ""
                });
                this.updateCptCodes();
            }
        },
        removeCptCode(index) {
            if (this.userRole === 'Doctor') {
                this.selectedCptCodes.splice(index, 1);
                this.updateCptCodes();
            }
        },
        updateCptCodes() {
            if (this.userRole === 'Doctor') {
                this.form.cpt_codes = JSON.stringify(this.selectedCptCodes);
            }
        },
        submitForm() {
            // Set the primary referral if referrals are selected
            if (this.selectedReferrals && this.selectedReferrals.length > 0) {
                this.form.primary_referral_id = this.selectedReferrals[0];
                this.form.referral_ids = this.selectedReferrals;
            }

            // Format policy limit info based on role
            if (this.userRole === 'Attorney') {
                this.form.policy_limit_info = JSON.stringify({
                    policy_limit: this.form.policy_limit,
                    pip_coverage: this.form.pip_coverage,
                    commercial_case: this.form.commercial_case
                });
            }

            // Remove null fields based on role
            if (this.userRole === 'Doctor') {
                delete this.form.policy_limit;
                delete this.form.pip_coverage;
                delete this.form.commercial_case;
            } else if (this.userRole === 'Attorney') {
                delete this.form.billing_type;
                delete this.form.service_billed;
                delete this.form.cpt_codes;
            }

            this.form.post(route(this.storeRoute), {
                onSuccess: () => {
                    this.$inertia.visit(route(this.listRoute));
                }
            });
        }
    },
    watch: {
        selectedCptCodes: {
            deep: true,
            handler() {
                if (this.userRole === 'Doctor') {
                    this.updateCptCodes();
                }
            }
        },
        selectedReferrals: {
            handler(newValue) {
                if (newValue && newValue.length > 0) {
                    this.form.primary_referral_id = newValue[0];
                    this.form.referral_ids = newValue;
                } else {
                    this.form.primary_referral_id = null;
                    this.form.referral_ids = [];
                }
            }
        }
    }
};
</script>
