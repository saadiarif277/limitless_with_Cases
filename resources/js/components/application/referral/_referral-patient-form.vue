<template>
    <div class="divide-y divide-gray-200">
        <v-content-body>
            <v-section-heading>
                <template #title>
                    <div class="flex items-center gap-2">
                        <div class="flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-orange-500">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 8.25 21 12m0 0-3.75 3.75M21 12H3" />
                            </svg>

                            <span class="text-orange-500">Patient</span> Information
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
                    <v-form-label><span class="text-orange-500 italic">Select Patient</span></v-form-label>
                    <v-form-select
                        :options="patients.data.map((patient) => ({ label: patient.name, value: patient.user_id }))"
                        :error="form.errors['patient.user_id']"
                        :disabled="form.processing"
                        :required="true"
                        v-model="form.patient.user_id"
                    />
                    <v-form-error v-if="form.errors.state_id">{{ form.errors.state_id }}</v-form-error>
                </v-form-group>
            </template>

            <template v-else>
                <v-form-group class="col-span-full xl:col-span-2">
                    <v-form-label><span class="text-orange-500 italic">Patient</span> Name</v-form-label>
                    <v-form-input type="text" v-if="form.doctor" v-model="form.patient.name" :error="form.errors['patient.name']" :disabled="form.processing" />
                    <v-form-error v-if="form.errors['patient.name']">{{ form.errors['patient.name'] }}</v-form-error>
                </v-form-group>

                <v-form-group class="col-span-full xl:col-span-2">
                    <v-form-label><span class="text-orange-500 italic">Patient</span> Email</v-form-label>
                    <v-form-input type="text" v-model="form.patient.email" :error="form.errors['patient.email']" :disabled="form.processing" />
                    <v-form-error v-if="form.errors['patient.email']">{{ form.errors['patient.email'] }}</v-form-error>
                </v-form-group>

                <v-form-group class="col-span-full xl:col-span-2">
                    <v-form-label><span class="text-orange-500 italic">Patient</span> Phone Number</v-form-label>
                    <v-form-input type="text" v-model="form.patient.phone_number" :error="form.errors['patient.phone_number']" :disabled="form.processing" />
                    <v-form-error v-if="form.errors['patient.phone_number']">{{ form.errors['patient.phone_number'] }}</v-form-error>
                </v-form-group>

                <div class="col-span-full grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-6 gap-4">
                    <v-form-group class="col-span-full xl:col-span-1">
                        <v-form-label><span class="text-orange-500 italic">Patient</span> Height</v-form-label>
                        <v-form-input type="text" v-model="form.patient.height" :error="form.errors['patient.height']" :disabled="form.processing" />
                        <v-form-error v-if="form.errors['patient.height']">{{ form.errors['patient.height'] }}</v-form-error>
                    </v-form-group>

                    <v-form-group class="col-span-full xl:col-span-1">
                        <v-form-label><span class="text-orange-500 italic">Patient</span> Weight</v-form-label>
                        <v-form-input type="text" v-model="form.patient.weight" :error="form.errors['patient.weight']" :disabled="form.processing" />
                        <v-form-error v-if="form.errors['patient.weight']">{{ form.errors['patient.weight'] }}</v-form-error>
                    </v-form-group>

                    <v-form-group class="col-span-full xl:col-span-2">
                        <v-form-label><span class="text-orange-500 italic">Patient</span> Gender</v-form-label>
                        <v-form-select
                            :options="[
                                { label: 'Male', value: 'male' },
                                { label: 'Female', value: 'female' },
                                { label: 'Other', value: 'other' },
                            ]"
                            :error="form.errors['patient.gender']"
                            :disabled="form.processing"
                            v-model="form.patient.gender"
                        />
                        <v-form-error v-if="form.errors['patient.gender']">{{ form.errors['patient.gender'] }}</v-form-error>
                    </v-form-group>

                    <v-form-group class="col-span-full sm:col-span-1 xl:col-span-2">
                        <v-form-label><span class="text-orange-500 italic">Patient</span> Birthdate</v-form-label>
                        <v-form-input type="date" v-model="form.patient.birthdate" :error="form.errors['patient.birthdate']" :disabled="form.processing" />
                        <v-form-error v-if="form.errors['patient.birthdate']">{{ form.errors['patient.birthdate'] }}</v-form-error>
                    </v-form-group>
                </div>

                <div class="col-span-full grid grid-cols-1 md:grid-cols-6 xl:grid-cols-12 gap-x-6 gap-y-4">
                    <v-form-group class="col-span-full md:col-span-4">
                        <v-form-label><span class="text-orange-500 italic">Patient</span> Address Line 1 </v-form-label>
                        <v-form-input type="text" v-model="form.patient.address_line_1" :error="form.errors['patient.address_line_1']" :disabled="form.processing" />
                        <v-form-error v-if="form.errors['patient.address_line_1']">{{ form.errors['patient.address_line_1'] }}</v-form-error>
                    </v-form-group>

                    <v-form-group class="col-span-full md:col-span-4">
                        <v-form-label><span class="text-orange-500 italic">Patient</span> Address Line 2</v-form-label>
                        <v-form-input type="text" v-model="form.patient.address_line_2" :error="form.errors['patient.address_line_2']" :disabled="form.processing" />
                        <v-form-error v-if="form.errors['patient.address_line_2']">{{ form.errors['patient.address_line_2'] }}</v-form-error>
                    </v-form-group>

                    <v-form-group class="col-span-full md:col-span-4">
                        <v-form-label><span class="text-orange-500 italic">Patient</span> City</v-form-label>
                        <v-form-input type="text" v-model="form.patient.city" :error="form.errors['patient.city']" :disabled="form.processing" />
                        <v-form-error v-if="form.errors['patient.city']">{{ form.errors['patient.city'] }}</v-form-error>
                    </v-form-group>

                    <v-form-group class="col-span-full md:col-span-4 xl:col-span-2">
                        <v-form-label><span class="text-orange-500 italic">Patient</span> State</v-form-label>
                        <v-form-select
                            :options="states.data.map((state) => ({ label: state.name, value: state.state_id }))"
                            :error="form.errors['patient.state_id']"
                            :disabled="form.processing"
                            v-model="form.patient.state_id"
                        />
                        <v-form-error v-if="form.errors['patient.state_id']">{{ form.errors['patient.state_id'] }}</v-form-error>
                    </v-form-group>

                    <v-form-group class="col-span-full md:col-span-4 xl:col-span-2">
                        <v-form-label><span class="text-orange-500 italic">Patient</span> Zip Code</v-form-label>
                        <v-form-input type="text" v-model="form.patient.zip_code" :error="form.errors['patient.zip_code']" :disabled="form.processing" />
                        <v-form-error v-if="form.errors['patient.zip_code']">{{ form.errors['patient.zip_code'] }}</v-form-error>
                    </v-form-group>
                </div>
            </template>



            <v-form-group class="col-span-full sm:col-span-1 xl:col-span-2">
                <v-form-label><span class="text-orange-500 italic">Patient</span> Injury Date</v-form-label>
                <v-form-input type="date" v-model="form.patient.injury_date" :error="form.errors['patient.injury_date']" :disabled="form.processing" />
                <v-form-error v-if="form.errors['patient.injury_date']">{{ form.errors['patient.injury_date'] }}</v-form-error>
            </v-form-group>
            <v-form-group>
    <v-form-label>ICD Codes</v-form-label>
    <v-form-select
        :options="(icdCodes?.data ?? []).map((code) => ({
            label: code.code + ' - ' + code.description,
            value: code.id
        }))"
        :error="form.errors.ict_code_id"
        :disabled="form.processing"
        :required="true"
        multiple
        v-model="form.ict_code_id"
        @change="updateIcdCodes"
    />

   <!-- Display selected ICD codes as badges -->
<div v-if="form.ict_code_id && form.ict_code_id.length > 0" class="selected-codes">
    <span v-for="(code, index) in form.ict_code_id" :key="index" class="badge">
        {{ getIcdLabel(code) }}
        <button type="button" @click="removeIcdCode(code)" class="remove-btn">×</button>
    </span>
</div>

    <v-form-error v-if="form.errors.ict_code_id">{{ form.errors.ict_code_id }}</v-form-error>
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
        states: {
            type: Object,
            required: false,
            default: () => {},
        },
        icdCodes: {
            type: Object,
            required: false,
            default: () => {},
        }
    },
    data() {
        return {
            dataStrategy: "new",
        };
    },
    computed: {
        // Debug computed properties
        patientsCount() {
            return this.patients?.data?.length || 0;
        },
        patientsData() {
            return this.patients?.data || [];
        }
    },
    mounted() {
        // Component is ready
    },
    methods: {
        getIcdLabel(codeId) {
            const code = (this.icdCodes?.data ?? []).find(c => c.id === codeId);
            return code ? `${code.code} - ${code.description}` : 'Unknown';
        },
        removeIcdCode(codeId) {
            this.form.ict_code_id = this.form.ict_code_id.filter(id => id !== codeId);
        },
        updateIcdCodes() {
            // This method is called when ICD codes selection changes
            // You can add any additional logic here if needed
        },
    },
    watch: {
        dataStrategy: {
            handler(value) {
                if (value === "new") {
                    this.form.patient.user_id = null;
                }
            },
        },
    }
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
