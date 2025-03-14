<template>
    <v-form class="h-full" @submit.prevent="submitForm">
        <div class="h-full divide-y divide-gray-200 space-y-12">
            <!-- Referral Information Section -->
            <div>
                <v-content-body class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6">
                    <!-- Referral Date -->
                    <v-form-group>
                        <v-form-label>Referral Date</v-form-label>
                        <v-form-input type="date" v-model="form.referral_date" :error="form.errors.referral_date" :disabled="form.processing" />
                        <v-form-error v-if="form.errors.referral_date">{{ form.errors.referral_date }}</v-form-error>
                    </v-form-group>

                    <!-- Referral Status -->
                    <v-form-group>
                        <v-form-label>Referral Status</v-form-label>
                        <v-form-select
                            :options="referralStatuses.data.map((referralStatus) => ({ label: referralStatus.name, value: referralStatus.referral_status_id }))"
                            :error="form.errors.referral_status_id"
                            :disabled="form.processing"
                            :required="true"
                            v-model="form.referral_status_id"
                        />
                        <v-form-error v-if="form.errors.referral_status_id">{{ form.errors.referral_status_id }}</v-form-error>
                    </v-form-group>

                    <!-- Referral State -->
                    <v-form-group>
                        <v-form-label>Referral State</v-form-label>
                        <v-form-select
                            :options="(referralStates || states).data.map((state) => ({ label: state.name, value: state.state_id }))"
                            :error="form.errors.state_id"
                            :disabled="form.processing"
                            :required="true"
                            v-model="form.state_id"
                        />
                        <v-form-error v-if="form.errors.state_id">{{ form.errors.state_id }}</v-form-error>
                    </v-form-group>

                    <!-- Patient Selection -->
                    <x-referral-patient-form
                        :attorneys="attorneys"
                        :doctors="doctors"
                        :document-categories="documentCategories"
                        :form="form"
                        :medical-specialties="medicalSpecialties"
                        :patients="patients"
                        :referral-reasons="referralReasons"
                        :referral-statuses="referralStatuses"
                        :states="states"
                    />
                </v-content-body>
            </div>

            <!-- Physician and Attorney Section -->
            <div>
                <v-content-body class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6">
                    <!-- Piloting Physician -->
                    <v-form-group>
                        <v-form-label>Piloting Physician</v-form-label>
                        <v-form-select
                            :items="doctors"
                            item-value="id"
                            item-text="name"
                            label="Piloting Physician (Optional)"
                            dense outlined
                            v-model="form.piloting_physician_id"
                        />
                    </v-form-group>

                    <!-- Attorney Selection -->
                    <v-form-group>
                        <v-form-label>Attorney</v-form-label>
                        <v-form-select
                            :items="attorneys"
                            item-value="id"
                            item-text="name"
                            label="Attorney"
                            dense outlined
                            v-model="form.attorney_id"
                        />
                    </v-form-group>
                </v-content-body>
            </div>

            <!-- Billing and CPT Codes Section -->
            <div>
                <v-content-body class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6">
                    <!-- CPT Codes -->
                    <v-form-group>
                        <v-form-label>CPT Codes</v-form-label>
                        <v-form-textarea
                            label="CPT Codes"
                            dense outlined
                            v-model="form.cpt_codes"
                        />
                    </v-form-group>

                    <!-- Service Billed -->
                    <v-form-group>
                        <v-form-label>Service Billed</v-form-label>
                        <v-form-input
                            type="number"
                            label="Service Billed"
                            dense outlined
                            v-model="form.service_billed"
                        />
                    </v-form-group>

                    <!-- CMS1500 Generated -->
                    <v-form-group>
                        <v-form-label>Generate CMS1500 Invoice?</v-form-label>
                        <v-form-checkbox
                            label="CMS1500 Generated"
                            v-model="form.is_cms1500_generated"
                        />
                    </v-form-group>

                    <!-- LOP or Insurance -->
                    <v-form-group>
                        <v-form-label>Billing Type</v-form-label>
                        <v-form-select
                            :items="['LOP', 'Insurance']"
                            label="Billing Type"
                            dense outlined
                            v-model="form.billing_type"
                        />
                    </v-form-group>
                </v-content-body>
            </div>

            <!-- Case Outcome Section -->
            <div>
                <v-content-body class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6">
                    <!-- Case Won -->
                    <v-form-group>
                        <v-form-label>Case Won</v-form-label>
                        <v-form-checkbox
                            label="Case Won"
                            v-model="form.case_won"
                        />
                    </v-form-group>

                    <!-- Outcome -->
                    <v-form-group>
                        <v-form-label>Outcome</v-form-label>
                        <v-form-textarea
                            label="Outcome"
                            dense outlined
                            v-model="form.outcome"
                        />
                    </v-form-group>

                    <!-- Reduction Accepted -->
                    <v-form-group>
                        <v-form-label>Reduction Accepted</v-form-label>
                        <v-form-checkbox
                            label="Reduction Accepted"
                            v-model="form.reduction_accepted"
                        />
                    </v-form-group>

                    <!-- Is Closed -->
                    <v-form-group>
                        <v-form-label>Is Case Closed?</v-form-label>
                        <v-form-checkbox
                            label="Is Closed"
                            v-model="form.is_closed"
                            @change="handleCloseChange"
                        />
                    </v-form-group>

                    <!-- Closed At -->
                    <v-form-group v-if="form.is_closed">
                        <v-form-label>Closed At</v-form-label>
                        <v-form-input
                            type="datetime-local"
                            label="Closed At"
                            dense outlined
                            v-model="form.closed_at"
                        />
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
import ReferralAttorneyForm from "./_referral-attorney-form.vue";
import ReferralDoctorForm from "./_referral-doctor-form.vue";
import ReferralDocumentsForm from "./_referral-documents-form.vue";
import ReferralPatientForm from "./_referral-patient-form.vue";
import { useForm } from "@inertiajs/vue3";

export default {
    name: "CaseReferralCreateForm",
    components: {
        "x-referral-attorney-form": ReferralAttorneyForm,
        "x-referral-doctor-form": ReferralDoctorForm,
        "x-referral-documents-form": ReferralDocumentsForm,
        "x-referral-patient-form": ReferralPatientForm,
    },
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
        listRoute: { type: String, default: "panel.admin.referrals.index" },
        storeRoute: { type: String, default: "panel.admin.referrals.store" },
    },
    data() {
        return {
            form: useForm({
                referral_date: new Date().toISOString().split('T')[0],
                referral_status_id: 1,
                state_id: this.$page.props.auth.user.state_id || "",
                attorney_id: null,
                piloting_physician_id: null,
                cpt_codes: "",
                service_billed: 0.0,
                is_cms1500_generated: false,
                billing_type: "",
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
        handleCloseChange() {
            if (this.form.is_closed) {
                this.form.closed_at = new Date().toISOString();
            } else {
                this.form.closed_at = null;
            }
        },
    },
};
</script>
