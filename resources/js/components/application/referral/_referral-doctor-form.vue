<template>
    <div class="divide-y divide-gray-200">
        <v-content-body>
            <v-section-heading>
                <template #title>
                    <div class="flex items-center gap-2">
                        <div class="flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-primary-500">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 8.25 21 12m0 0-3.75 3.75M21 12H3" />
                            </svg>

                            <span class="text-primary-500">Doctor</span> Information
                        </div>

                        <div class="text-red-500 font-medium italic" v-if="!form.state_id">
                            Please select a referral state.
                        </div>
                    </div>
                </template>
            </v-section-heading>
        </v-content-body>

        <v-content-body class="relative grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-6 gap-4">
            <div class="w-full h-full absolute bg-white opacity-50" v-if="!form.state_id"></div>
            <v-form-group class="col-span-full flex">
                <v-tabs :tabs="[
                    {
                        title: 'Existing',
                        slug: 'existing',
                    },
                    {
                        title: 'New',
                        slug: 'new',
                    },
                ]" v-model="dataStrategy" />
            </v-form-group>

            <template v-if="dataStrategy === 'existing'">
                <v-form-group class="col-span-full md:col-span-2">
                    <v-form-label><span class="text-primary-500 italic">Select Doctor</span></v-form-label>
                    <v-form-select
                        :options="doctors.data.map((doctor) => ({ label: doctor.name, value: doctor.user_id }))"
                        :error="form.errors['doctor.user_id']"
                        :disabled="form.processing"
                        :required="true"
                        v-model="form.doctor.user_id"
                    />
                    <v-form-error v-if="form.errors['doctor.user_id']">{{ form.errors['doctor.user_id'] }}</v-form-error>
                </v-form-group>

                <v-form-group class="col-span-full md:col-span-2" v-if="selectedDoctor">
                    <v-form-label><span class="text-primary-500 italic">Select Clinic</span></v-form-label>
                    <v-form-select
                        :options="selectedDoctor.clinics.map((clinic) => ({ label: clinic.name, value: clinic.clinic_id }))"
                        :error="form.errors['doctor.clinic.clinic_id']"
                        :disabled="form.processing"
                        :required="true"
                        v-model="form.doctor.clinic.clinic_id"
                    />
                    <v-form-error v-if="form.errors['doctor.clinic.clinic_id']">{{ form.errors['doctor.clinic.clinic_id'] }}</v-form-error>
                </v-form-group>
            </template>

            <template v-else>
                <div class="col-span-full grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-x-6 gap-y-4">
                    <v-form-group>
                        <v-form-label><span class="text-primary-500 italic">Doctor</span> Name</v-form-label>
                        <v-form-input type="text" v-if="form.doctor" v-model="form.doctor.name" :error="form.errors['doctor.name']" :disabled="form.processing" />
                        <v-form-error v-if="form.errors['doctor.name']">{{ form.errors['doctor.name'] }}</v-form-error>
                    </v-form-group>

                    <v-form-group>
                        <v-form-label><span class="text-primary-500 italic">Doctor</span> Email</v-form-label>
                        <v-form-input type="text" v-model="form.doctor.email" :error="form.errors['doctor.email']" :disabled="form.processing" />
                        <v-form-error v-if="form.errors['doctor.email']">{{ form.errors['doctor.email'] }}</v-form-error>
                    </v-form-group>

                    <v-form-group>
                        <v-form-label><span class="text-primary-500 italic">Doctor</span> Phone Number</v-form-label>
                        <v-form-input type="text" v-model="form.doctor.phone_number" :error="form.errors['doctor.phone_number']" :disabled="form.processing" />
                        <v-form-error v-if="form.errors['doctor.phone_number']">{{ form.errors['doctor.phone_number'] }}</v-form-error>
                    </v-form-group>

                    <v-form-group>
                        <v-form-label><span class="text-primary-500 italic">Doctor</span> Medical Specialty</v-form-label>
                        <v-form-select
                            :options="[
                                { label: 'Select Medical Specialty', value: null },
                                ...medicalSpecialties.data.map((medicalSpecialty) => ({ label: medicalSpecialty.name, value: medicalSpecialty.medical_specialty_id }))
                            ]"
                            :error="form.errors['doctor.medical_specialty_id']"
                            v-model="form.doctor.medical_specialty_id"
                        />
                        <v-form-error v-if="form.errors['doctor.medical_specialty_id']">{{ form.errors['doctor.medical_specialty_id'] }}</v-form-error>
                    </v-form-group>
                </div>

                <div class="col-span-full grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-x-6 gap-y-4">
                    <v-form-group>
                        <v-form-label><span class="text-secondary-500 italic">Clinic</span> Name</v-form-label>
                        <v-form-input type="text" v-model="form.doctor.clinic.name" :error="form.errors['doctor.clinic.name']" :disabled="form.processing" />
                        <v-form-error v-if="form.errors['doctor.clinic.name']">{{ form.errors['doctor.clinic.name'] }}</v-form-error>
                    </v-form-group>

                    <v-form-group>
                        <v-form-label><span class="text-secondary-500 italic">Clinic</span> Email</v-form-label>
                        <v-form-input type="text" v-model="form.doctor.clinic.email" :error="form.errors['doctor.clinic.email']" :disabled="form.processing" />
                        <v-form-error v-if="form.errors['doctor.clinic.email']">{{ form.errors['doctor.clinic.email'] }}</v-form-error>
                    </v-form-group>

                    <v-form-group>
                        <v-form-label><span class="text-secondary-500 italic">Clinic</span> Phone Number</v-form-label>
                        <v-form-input type="text" v-model="form.doctor.clinic.phone_number" :error="form.errors['doctor.clinic.phone_number']" :disabled="form.processing" />
                        <v-form-error v-if="form.errors['doctor.clinic.phone_number']">{{ form.errors['doctor.clinic.phone_number'] }}</v-form-error>
                    </v-form-group>

                    <v-form-group>
                        <v-form-label><span class="text-secondary-500 italic">Clinic</span> Address Line 1 </v-form-label>
                        <v-form-input type="text" v-model="form.doctor.clinic.address_line_1" :error="form.errors['doctor.clinic.address_line_1']" :disabled="form.processing" />
                        <v-form-error v-if="form.errors['doctor.clinic.address_line_1']">{{ form.errors['doctor.clinic.address_line_1'] }}</v-form-error>
                    </v-form-group>

                    <v-form-group>
                        <v-form-label><span class="text-secondary-500 italic">Clinic</span> Address Line 2</v-form-label>
                        <v-form-input type="text" v-model="form.doctor.clinic.address_line_2" :error="form.errors['doctor.clinic.address_line_2']" :disabled="form.processing" />
                        <v-form-error v-if="form.errors['doctor.clinic.address_line_2']">{{ form.errors['doctor.clinic.address_line_2'] }}</v-form-error>
                    </v-form-group>

                    <v-form-group>
                        <v-form-label><span class="text-secondary-500 italic">Clinic</span> City</v-form-label>
                        <v-form-input type="text" v-model="form.doctor.clinic.city" :error="form.errors['doctor.clinic.city']" :disabled="form.processing" />
                        <v-form-error v-if="form.errors['doctor.clinic.city']">{{ form.errors['doctor.clinic.city'] }}</v-form-error>
                    </v-form-group>

                    <v-form-group>
                        <v-form-label><span class="text-secondary-500 italic">Clinic</span> State</v-form-label>
                        <v-form-select
                            :options="states.data.map((state) => ({ label: state.name, value: state.state_id }))"
                            :error="form.errors['doctor.clinic.state_id']"
                            :disabled="true"
                            v-model="form.doctor.clinic.state_id"
                        />
                        <v-form-error v-if="form.errors['doctor.clinic.state_id']">{{ form.errors['doctor.clinic.state_id'] }}</v-form-error>
                    </v-form-group>

                    <v-form-group>
                        <v-form-label><span class="text-secondary-500 italic">Clinic</span> Zip Code</v-form-label>
                        <v-form-input type="text" v-model="form.doctor.clinic.zip_code" :error="form.errors['doctor.clinic.zip_code']" :disabled="form.processing" />
                        <v-form-error v-if="form.errors['doctor.clinic.zip_code']">{{ form.errors['doctor.clinic.zip_code'] }}</v-form-error>
                    </v-form-group>
                </div>
            </template>

            <v-form-group class="col-span-full sm:col-span-2 xl:col-span-3">
                <v-form-label><span class="text-primary-500 italic">Doctor</span> Additional Notes</v-form-label>
                <v-form-textarea v-model="form.doctor.notes" :error="form.errors['doctor.notes']" :disabled="form.processing" />
                <v-form-error v-if="form.errors['doctor.notes']">{{ form.errors['doctor.notes'] }}</v-form-error>
            </v-form-group>
        </v-content-body>
    </div>
</template>

<script>
export default {
    props: {
        doctors: {
            type: Object,
            required: true,
            default: () => {},
        },
        form: {
            type: Object,
            required: true,
            default: () => {},
        },
        medicalSpecialties: {
            type: Object,
            required: true,
            default: () => {},
        },
        patients: {
            type: Object,
            required: false,
            default: () => {},
        },
        referral: {
            type: Object,
            required: false,
            default: () => {},
        },
        referralReasons: {
            type: Object,
            required: false,
            default: () => {},
        },
        states: {
            type: Object,
            required: false,
            default: () => {},
        },
    },
    data() {
        return {
            dataStrategy: "existing",
        };
    },
    computed: {
        selectedDoctor() {
            if (!this.form.doctor || !this.form.doctor.user_id) {
                return null;
            }

            return this.doctors.data.find((doctor) => ((doctor.user_id) == (this.form.doctor.user_id)));
        },
        // Debug computed properties
        doctorsCount() {
            return this.doctors?.data?.length || 0;
        },
        doctorsData() {
            return this.doctors?.data || [];
        }
    },
    mounted() {
        // Component is ready
    },
    watch: {
        dataStrategy: {
            handler(value) {
                if (value === "new") {
                    this.form.doctor.user_id = null;
                    this.form.doctor.clinic_id = null;
                    this.form.doctor.medical_specialty_id = null;
                }
            },
        },
        'form.doctor.user_id': {
            handler(newValue) {
                if (newValue && this.dataStrategy === 'existing') {
                    // When an existing doctor is selected, populate the form with their data
                    const selectedDoctor = this.doctors.data.find(doctor => doctor.user_id == newValue);
                    if (selectedDoctor) {
                        this.form.doctor.name = selectedDoctor.name;
                        this.form.doctor.email = selectedDoctor.email;
                        this.form.doctor.phone_number = selectedDoctor.phone_number || "";
                        this.form.doctor.medical_specialty_id = selectedDoctor.medical_specialty_id || null;

                        // Populate clinic information if available
                        if (selectedDoctor.clinics && selectedDoctor.clinics.length > 0) {
                            const clinic = selectedDoctor.clinics[0];
                            this.form.doctor.clinic.clinic_id = clinic.clinic_id || "";
                            this.form.doctor.clinic.name = clinic.name || "";
                            this.form.doctor.clinic.email = clinic.email || "";
                            this.form.doctor.clinic.phone_number = clinic.phone_number || "";
                            this.form.doctor.clinic.address_line_1 = clinic.address_line_1 || "";
                            this.form.doctor.clinic.address_line_2 = clinic.address_line_2 || "";
                            this.form.doctor.clinic.city = clinic.city || "";
                            this.form.doctor.clinic.state_id = clinic.state_id || "";
                            this.form.doctor.clinic.zip_code = clinic.zip_code || "";
                        }
                    }
                }
            },
            immediate: true
        }
    },
};
</script>
