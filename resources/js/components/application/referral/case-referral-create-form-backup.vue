<template>
    <v-form class="h-full" >
        <div class="h-full divide-y divide-gray-200 space-y-12">
            <div>
                <v-content-body class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6">
                    <v-form-group>
                        <v-form-label>Referral Date</v-form-label>
                        <v-form-input type="date" v-model="form.referral_date" :error="form.errors.referral_date" :disabled="form.processing" />
                        <v-form-error v-if="form.errors.referral_date">{{ form.errors.referral_date }}</v-form-error>
                    </v-form-group>

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
                        <v-form-group>

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
             </v-form-group>





              <!-- Piloting Physician Selection -->
                 <!-- Attorney Selection -->
                  <v-form-group>
                 <v-form-label>
                Piloting Physician
               </v-form-label>
              <v-form-select

                :items="doctors"
                item-value="id"
                item-text="name"
                label="Piloting Physician (Optional)"
                dense outlined
              />
            </v-form-group>
               <v-form-group>

              <v-form-label>
               CPT Codes
               </v-form-label>
              <!-- Service Billed -->
              <v-form-textarea

                type="number"
                label="Service Billed"
                dense outlined required
              />

            </v-form-group>
              <!-- CMS1500 Generated -->
              <v-form-group>
              <v-form-label>
               Want to Generate CMS1500 Invoice ?
               </v-form-label>
              <v-form-checkbox

                label="CMS1500 Generated"
              />

              <v-form-label>
               Case Won
               </v-form-label>
              <!-- Case Won -->
              <v-form-checkbox

                label="Case Won"
              />
              </v-form-group>
            <v-form-group>
              <v-form-label>
                Notes
               </v-form-label>
              <!-- Outcome -->
              <v-form-textarea

                label="Outcome"
                dense outlined
              />
              <v-form-label>
                Is Reduction Accepted ?
               </v-form-label>
              <!-- Reduction Accepted -->
              <v-form-checkbox

                label="Reduction Accepted"
              />

              <v-form-label>
                Is Case Closed ?
               </v-form-label>
               <v-form-checkbox

label="Is Closed"
@change="handleCloseChange"
/>
            </v-form-group>
              <!-- Is Closed -->

            <v-form-group>
              <!-- Closed At -->
              <v-form-label>
                Closed At ?
               </v-form-label>
              <v-form-textarea

                type="datetime-local"
                label="Closed At"
                dense outlined
              />
            </v-form-group>
              <!-- Submit Button -->
              <div class="text-right mt-4">
                <v-button color="primary" dark type="submit">Create Case</v-button>
              </div>


            <v-form-group class="col-span-full flex items-center justify-end gap-2 text-right border-t border-gray-200 bg-gray-50 px-6 py-4">
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
                </v-content-body>
            </div>

            <x-referral-doctor-form
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

            <x-referral-attorney-form
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

            <x-referral-documents-form
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

            <!-- Integrated Form -->

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
        attorneys: {
            type: Object,
            required: true,
            default: () => {},
        },
        doctors: {
            type: Object,
            required: true,
            default: () => {},
        },
        documentCategories: {
            type: Object,
            required: false,
            default: () => {},
        },
        medicalSpecialties: {
            type: Object,
            required: true,
            default: () => {},
        },
        patients: {
            type: Object,
            required: true,
            default: () => {},
        },
        referralReasons: {
            type: Object,
            required: false,
            default: () => {},
        },
        referralStatuses: {
            type: Object,
            required: false,
            default: () => {},
        },
        referralStates: {
            type: Object,
            required: false,
            default: () => {},
        },
        states: {
            type: Object,
            required: false,
            default: () => {},
        },
        listRoute: {
            type: String,
            required: false,
            default: () => "panel.admin.referrals.index",
        },
        storeRoute: {
            type: String,
            required: false,
            default: () => "panel.admin.referrals.store",
        },

    },
    // data() {
    //     return {
    //         form: useForm({
    //             referral_date: (new Date().toISOString().split('T')[0]),
    //             referral_status_id: 1,
    //             state_id: this.$page.props.auth.user.state_id || "",
    //             attorney: {
    //                 name: "",
    //                 email: "",
    //                 phone_number: "",
    //                 law_firm: {
    //                     name: "",
    //                     email: "",
    //                     phone_number: "",
    //                     address_line_1: "",
    //                     address_line_2: "",
    //                     city: "",
    //                     state_id: "",
    //                     zip_code: "",
    //                 },
    //             },
    //             doctor: {
    //                 user_id: "",
    //                 name: "",
    //                 email: "",
    //                 phone_number: "",
    //                 clinic: {
    //                     clinic_id: "",
    //                     name: "",
    //                     email: "",
    //                     phone_number: "",
    //                     address_line_1: "",
    //                     address_line_2: "",
    //                     city: "",
    //                     state_id: "",
    //                     zip_code: "",
    //                 },
    //                 notes: "",
    //             },
    //             patient: {
    //                 name: "",
    //                 email: "",
    //                 phone_number: "",
    //                 height: "",
    //                 weight: "",
    //                 gender: "",
    //                 birthdate: "",
    //                 injury_date: "",
    //                 address_line_1: "",
    //                 address_line_2: "",
    //                 city: "",
    //                 state_id: "",
    //                 zip_code: "",
    //                 referral_reason_ids: [],
    //             },
    //             documents: {},
    //         }),
    //     };
    // },

    data() {
        return {
            form: useForm({
                referral_date: (new Date().toISOString().split('T')[0]),
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
            }),
        };
    },
    watch: {
        "form.state_id": {
            handler(value) {
                if (!value) {
                    return;
                }

                this.$inertia.get(route(route().current()), {
                    ...this.$page.props.query,
                    state_id: this.form.state_id || undefined,
                }, {
                    preserveState: true,
                    preserveScroll: true,
                });

                this.form.doctor.clinic.state_id = value;
                this.form.attorney.law_firm.state_id = value;
                this.form.patient.state_id = value;
            },
            immediate: true,
        },
    },
    methods: {
        submitForm() {
            this.form.clearErrors();

            this.form.post(this.route(this.storeRoute || "panel.admin.referrals.store"), {
                onSuccess: () => this.$toast().success("Referral created successfully!"),
                onError: (errors) => {
                    Object.keys(errors).forEach((key) => {
                        const error = errors[key];
                        this.$toast().error(error);
                    });
                },
            });
        },
    },
    setup() {
        const form = useForm({
            referral_date: '',
            referral_status_id: null,
            state_id: null,
            // Add all other fields that your form uses
        });

        const submitForm = () => {
            form.post('/your-api-endpoint', {
                onSuccess: () => {
                    // Handle success
                },
                onError: (errors) => {
                    // Handle errors
                },
            });
        };

        return {
            form,
            submitForm,
        };
    },
}


</script>
