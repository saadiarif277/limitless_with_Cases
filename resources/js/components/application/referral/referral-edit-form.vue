<template>
    <v-form class="h-full" @submit.prevent="submitForm">
        <div class="h-full divide-y divide-gray-200 space-y-12">
            <div>
                <v-content-body class=" grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6">
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
                            :options="states.data.map((state) => ({ label: state.name, value: state.state_id }))"
                            :error="form.errors.state_id"
                            :disabled="true"
                            :required="true"
                            v-model="form.state_id"
                        />
                        <v-form-error v-if="form.errors.state_id">{{ form.errors.state_id }}</v-form-error>
                    </v-form-group>

                    <template v-if="allowSourceEdit">
                        <v-form-group>
                            <v-form-label>Referral Source</v-form-label>
                            <v-form-select
                                :options="[
                                    ...[
                                        {
                                            label: $page.props.auth.user.name,
                                            value: $page.props.auth.user.user_id,
                                        },
                                    ],
                                    ...sources.data.map((user) => ({ label: user.name, value: user.user_id })),
                                ]"
                                :error="form.errors.source_user_id"
                                :disabled="form.processing"
                                v-model="form.source_user_id"
                            />
                            <v-form-error v-if="form.errors.source_user_id">{{ form.errors.source_user_id }}</v-form-error>
                        </v-form-group>
                    </template>
                </v-content-body>
            </div>

            <div class="divide-y divide-gray-200">
                <v-content-body class="">
                    <v-section-heading>
                        <template #title>
                            <div class="flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-primary-500">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 8.25 21 12m0 0-3.75 3.75M21 12H3" />
                                </svg>

                                <span class="text-primary-500">Doctor</span> Information
                            </div>
                        </template>
                    </v-section-heading>
                </v-content-body>

                <v-content-body class="grid grid-cols-1 sm:grid-cols-2 gap-2 sm:gap-4">
                    <div>
                        <v-heading :size="6" class="font-medium">
                            {{ referral.data.doctor_user.name }}

                            <template v-if="referral.data.doctor_user.medical_specialty">
                                <span class="italic">({{ referral.data.doctor_user.medical_specialty.name }})</span>
                            </template>

                            <template v-else>
                                <span class="italic">(No Medical Specialty)</span>
                            </template>
                        </v-heading>

                        <div class="flex items-center gap-2 italic">
                            <v-paragraph>{{ referral.data.doctor_user.email }}</v-paragraph>
                            <div class="w-1 h-1 rounded-full bg-gray-500"></div>
                            <v-paragraph>{{ referral.data.doctor_user.phone_number }}</v-paragraph>
                        </div>

                        <div>&nbsp;</div>
                    </div>

                    <div>
                        <v-heading :size="6" class="font-medium">{{ referral.data.clinic.name }}</v-heading>

                        <div class="flex items-center gap-2 italic">
                            <v-paragraph>{{ referral.data.clinic.email }}</v-paragraph>
                            <div class="w-1 h-1 rounded-full bg-gray-500"></div>
                            <v-paragraph>{{ referral.data.clinic.phone_number }}</v-paragraph>
                        </div>

                        <v-paragraph class="italic">
                            {{ referral.data.clinic.address_line_1 }},
                            <template v-if="referral.data.clinic.address_line_2">{{ referral.data.clinic.address_line_2 }},</template>
                            {{ referral.data.clinic.city }},
                            {{ referral.data.clinic.state.name }},
                            {{ referral.data.clinic.zip_code }}
                        </v-paragraph>
                    </div>
                </v-content-body>
            </div>

            <div class="divide-y divide-gray-200">
                <v-content-body class="">
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
                </v-content-body>

                <v-content-body class="grid grid-cols-1 sm:grid-cols-2 gap-2 sm:gap-4">
                    <div>
                        <v-heading :size="6" class="font-medium">{{ referral.data.patient_user.name }}</v-heading>

                        <div class="flex items-center gap-2 italic">
                            <v-paragraph>{{ referral.data.patient_user.email }}</v-paragraph>
                            <div class="w-1 h-1 rounded-full bg-gray-500"></div>
                            <v-paragraph>{{ referral.data.patient_user.phone_number }}</v-paragraph>
                        </div>

                        <v-paragraph class="italic">
                            {{ referral.data.patient_user.address_line_1 }},
                            <template v-if="referral.data.patient_user.address_line_2">{{ referral.data.patient_user.address_line_2 }},</template>
                            {{ referral.data.patient_user.city }},
                            {{ referral.data.patient_user.state.name }},
                            {{ referral.data.patient_user.zip_code }}
                        </v-paragraph>
                    </div>

                    <div class="flex flex-col gap-2">
                        <div>
                            <v-heading :size="6">Injury Date</v-heading>
                            <v-paragraph class="italic">{{ referral.data.injury_date || 'Missing Injury Date' }}</v-paragraph>
                        </div>

                        <div>
                            <v-heading :size="6" class="font-medium">Referral Reasons</v-heading>

                            <div class="flex items-center gap-2">
                                <v-paragraph class="italic">
                                    {{ referral.data.referral_reasons.map((referralReason) => referralReason.name) }}
                                </v-paragraph>
                            </div>
                        </div>
                    </div>
                </v-content-body>
            </div>

            <div class="divide-y divide-gray-200">
                <v-content-body class="">
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
                </v-content-body>

                <v-content-body class="grid grid-cols-1 sm:grid-cols-2 gap-2 sm:gap-4">
                    <div>
                        <v-heading :size="6" class="font-medium">{{ referral.data.attorney_user.name }}</v-heading>

                        <div class="flex items-center gap-2 italic">
                            <v-paragraph>{{ referral.data.attorney_user.email }}</v-paragraph>
                            <div class="w-1 h-1 rounded-full bg-gray-500"></div>
                            <v-paragraph>{{ referral.data.attorney_user.phone_number }}</v-paragraph>
                        </div>

                        <div>&nbsp;</div>
                    </div>

                    <div>
                        <v-heading :size="6" class="font-medium">{{ referral.data.attorney_user.law_firm.name }}</v-heading>

                        <div class="flex items-center gap-2 italic">
                            <v-paragraph>{{ referral.data.attorney_user.law_firm.email }}</v-paragraph>
                            <div class="w-1 h-1 rounded-full bg-gray-500"></div>
                            <v-paragraph>{{ referral.data.attorney_user.law_firm.phone_number }}</v-paragraph>
                        </div>

                        <v-paragraph class="italic">
                            {{ referral.data.attorney_user.law_firm.address_line_1 }},
                            <template v-if="referral.data.attorney_user.law_firm.address_line_2">{{ referral.data.attorney_user.law_firm.address_line_2 }},</template>
                            {{ referral.data.attorney_user.law_firm.city }},
                            {{ referral.data.attorney_user.law_firm.state.name }},
                            {{ referral.data.attorney_user.law_firm.zip_code }}
                        </v-paragraph>
                    </div>
                </v-content-body>
            </div>

            <div class="divide-y divide-gray-200">
                <v-content-body class="">
                    <v-section-heading>
                        <template #title>
                            <div class="flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-primary-500">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 8.25 21 12m0 0-3.75 3.75M21 12H3" />
                                </svg>

                                <span class="text-primary-500">Document</span> Information
                            </div>
                        </template>
                    </v-section-heading>
                </v-content-body>

                <v-content-body>
                    <div class="grid grid-cols-1 gap-4">
                        <template v-for="(documentCategory, documentCategoryIndex) in documentCategories.data" :key="'documentCategory_' + documentCategoryIndex">
                            <template v-if="documentCategory.document_types && documentCategory.document_types.length">
                                <div class="space-y-1">
                                    <v-section-heading>
                                        <template #title>
                                            {{ documentCategory.name }}
                                        </template>
                                    </v-section-heading>

                                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                                        <template v-for="(documentType, documentTypeIndex) in documentCategory.document_types" :key="'documentType_' + documentTypeIndex">
                                            <v-card>
                                                <v-content-body class="flex flex-col divide-y divide-gray-200">
                                                    <v-form-group class="pb-4">
                                                        <v-form-label class="flex items-center justify-between">
                                                            {{ documentType.name }}

                                                            <template v-if="formDocuments[documentType.document_type_id] && !documentType.is_generated">
                                                                <a href="#" @click.stop="destroyDocument(formDocuments[documentType.document_type_id].document_id, documentType.document_type_id)">
                                                                    <span class="text-red-500 uppercase">
                                                                        Delete
                                                                    </span>
                                                                </a>
                                                            </template>
                                                        </v-form-label>

                                                        <div class="py-2">
                                                            <template v-if="
                                                                formDocuments[documentType.document_type_id] && 
                                                                formDocuments[documentType.document_type_id].document_id
                                                            ">
                                                                <a
                                                                    class="w-full text-center inline-flex items-center justify-center border rounded-md shadow-sm focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed    px-4 py-2.5 text-sm font-medium   border-gray-200 hover:border-gray-800 text-gray-500 bg-white hover:bg-gray-800 hover:text-white"
                                                                    :href="route('panel.admin.documents.download', { 
                                                                        document: formDocuments[documentType.document_type_id].document_id, 
                                                                        _t: Date.now() 
                                                                    })"
                                                                    target="_blank"
                                                                >
                                                                    <div class="max-w-full overflow-hidden line-clamp-1">
                                                                        Download
                                                                    </div>
                                                                </a>
                                                            </template>

                                                            <template v-else>
                                                                <a
                                                                    class="w-full text-center inline-flex items-center justify-center border rounded-md shadow-sm focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed    px-4 py-2.5 text-sm font-medium   border-gray-200 text-gray-500 bg-white opacity-50 cursor-not-allowed"
                                                                    href="#"
                                                                    target="_blank"
                                                                >
                                                                    <div class="max-w-full overflow-hidden line-clamp-1">
                                                                        No File
                                                                    </div>
                                                                </a>
                                                            </template>
                                                        </div>
                                                    </v-form-group>

                                                    <v-form-group class="pt-4" v-if="!documentType.is_generated">
                                                        <v-form-label class="flex items-center justify-between">
                                                            <template v-if="documentType.is_generated">
                                                                --
                                                            </template>

                                                            <template v-else-if="
                                                                formDocuments[documentType.document_type_id] && 
                                                                formDocuments[documentType.document_type_id].document_id
                                                            ">
                                                                Replace Document
                                                            </template>

                                                            <template v-else>
                                                                Upload Document
                                                            </template>

                                                            <template v-if="form.documents[documentType.document_type_id]">
                                                                <v-link href="#" @click.stop="form.documents[documentType.document_type_id] = undefined">
                                                                    <span class="text-red-500 uppercase">
                                                                        Cancel
                                                                    </span>
                                                                </v-link>
                                                            </template>
                                                        </v-form-label>

                                                        <div class="overflow-hidden">
                                                            <v-form-file v-model="form.documents[documentType.document_type_id]" :disabled="form.processing || documentType.is_generated" />
                                                        </div>
                                                    </v-form-group>

                                                    <v-form-group class="pt-4" v-else>
                                                        <v-paragraph class="italic">
                                                            System generated documents cannot be replaced or removed.
                                                        </v-paragraph>
                                                    </v-form-group>
                                                </v-content-body>
                                            </v-card>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </template>
                    </div>
                </v-content-body>
            </div>

            <v-form-group class="col-span-full flex items-center justify-end gap-2 text-right border-t border-gray-200 bg-gray-50 px-6 py-4">
                <progress v-if="form.progress" :value="form.progress.percentage" max="100">
                    {{ form.progress.percentage }}%
                </progress>

                <v-button :href="route(this.listRoute)" color="white" :disabled="form.processing">
                    Cancel
                </v-button>

                <v-button :disabled="form.processing">
                    Save Changes
                </v-button>
            </v-form-group>
        </div>
    </v-form>
</template>

<script>
import ReferralAttorneyForm from "./_referral-attorney-form.vue";
import ReferralDoctorForm from "./_referral-doctor-form.vue";
import ReferralPatientForm from "./_referral-patient-form.vue";
import { useForm } from "@inertiajs/vue3";

export default {
    name: "ReferralsCreateForm",
    components: {
        "x-referral-attorney-form": ReferralAttorneyForm,
        "x-referral-doctor-form": ReferralDoctorForm,
        "x-referral-patient-form": ReferralPatientForm,
    },
    props: {
        allowSourceEdit: {
            type: Boolean,
            required: false,
            default: () => true,
        },
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
        referral: {
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
        sources:{
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
        updateRoute: {
            type: String,
            required: false,
            default: () => "panel.admin.referrals.update",
        },
        destroyDocumentRoute: {
            type: String,
            required: false,
            default: () => "panel.admin.documents.destroy",
        },
    },
    data() {
        return {
            formDocuments: {},
            form: useForm({
                _method: "patch",
                referral_date: "",
                referral_status_id: "",
                state_id: "",
                source_user_id: this.$page.props.auth.user.user_id,
                documents: {},
            }),
        };
    },
    watch: {
        referral: {
            handler(value) {
                if (!value || !value.data) {
                    return;
                }

                this.form.referral_date = value.data.referral_date;
                this.form.referral_status_id = value.data.referral_status_id;
                this.form.state_id = value.data.state_id;
                this.form.documents = {};

                this.populateFormDocuments();
            },
            immediate: true,
            deep: true,
        },
    },
    methods: {
        populateFormDocuments() {
            if (!this.referral.data || !this.referral.data.documents) {
                return;
            }

            const documents = {};

            Object.keys(this.referral.data.documents).forEach((key) => {
                const document = JSON.parse(JSON.stringify(this.referral.data.documents[key]));
                documents[document.document_type_id] = document;
                
                this.form.documents[document.document_type_id] = "";
            });

            this.formDocuments = documents;

        },
        submitForm() {
            this.form.clearErrors();

            
            Object.keys(this.form.documents).forEach((key) => {
                const document = this.form.documents[key];
                
                if (!document) {
                    this.form.documents[key] = undefined;
                }
            });

            this.form.post(this.route(this.updateRoute, { referral: this.referral.data.referral_id }), {
                onSuccess: () => {
                    this.$toast().success("Referral updated successfully!");
                    this.$inertia.reload();
                },
                onError: (errors) => {
                    Object.keys(errors).forEach((key) => {
                        const error = errors[key];
                        this.$toast().error(error);
                    });
                },
            });
        },
        destroyDocument(documentId, documentTypeId) {
            this.form.delete(this.route(this.destroyDocumentRoute, { document: documentId }), {
                onSuccess: () => {
                    this.$toast().success("Document deleted successfully!");
                    this.$inertia.reload();
                },
                onError: (errors) => {
                    Object.keys(errors).forEach((key) => {
                        const error = errors[key];
                        this.$toast().error(error);
                    });
                },
            });
        },
    },
};
</script>
