<template>
    <div class="divide-y divide-gray-200">
        <v-content-body>
            <v-section-heading>
                <template #title>
                    <div class="flex items-center gap-2">
                        <div class="flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-green-500">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 8.25 21 12m0 0-3.75 3.75M21 12H3" />
                            </svg>

                            <span class="text-green-500">Attorney</span> Information
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
                    <v-form-label><span class="text-green-500 italic">Select Attorney</span></v-form-label>
                    <v-form-select
                        :options="attorneys.data.map((attorney) => ({ label: attorney.name, value: attorney.user_id }))"
                        :error="form.errors['attorney.user_id']"
                        :disabled="form.processing"
                        :required="true"
                        v-model="form.attorney.user_id"
                    />
                    <v-form-error v-if="form.errors['attorney.user_id']">{{ form.errors['attorney.user_id'] }}</v-form-error>
                </v-form-group>
            </template>

            <template v-else>
                <v-form-group class="col-span-full xl:col-span-2">
                    <v-form-label><span class="text-green-500 italic">Attorney</span> Name</v-form-label>
                    <v-form-input type="text" v-if="form.attorney" v-model="form.attorney.name" :error="form.errors['attorney.name']" :disabled="form.processing" />
                    <v-form-error v-if="form.errors['attorney.name']">{{ form.errors['attorney.name'] }}</v-form-error>
                </v-form-group>

                <v-form-group class="col-span-full xl:col-span-2">
                    <v-form-label><span class="text-green-500 italic">Attorney</span> Email</v-form-label>
                    <v-form-input type="text" v-model="form.attorney.email" :error="form.errors['attorney.email']" :disabled="form.processing" />
                    <v-form-error v-if="form.errors['attorney.email']">{{ form.errors['attorney.email'] }}</v-form-error>
                </v-form-group>

                <v-form-group class="col-span-full xl:col-span-2">
                    <v-form-label><span class="text-green-500 italic">Attorney</span> Phone Number</v-form-label>
                    <v-form-input type="text" v-model="form.attorney.phone_number" :error="form.errors['attorney.phone_number']" :disabled="form.processing" />
                    <v-form-error v-if="form.errors['attorney.phone_number']">{{ form.errors['attorney.phone_number'] }}</v-form-error>
                </v-form-group>

                <div class="col-span-full grid grid-cols-1 md:grid-cols-6 xl:grid-cols-12 gap-x-6 gap-y-4">
                    <v-form-group class="col-span-full xl:col-span-4">
                        <v-form-label><span class="text-secondary-500 italic">Law Firm</span> Name</v-form-label>
                        <v-form-input type="text" v-model="form.attorney.law_firm.name" :error="form.errors['attorney.law_firm.name']" :disabled="form.processing" />
                        <v-form-error v-if="form.errors['attorney.law_firm.name']">{{ form.errors['attorney.law_firm.name'] }}</v-form-error>
                    </v-form-group>

                    <v-form-group class="col-span-full xl:col-span-4">
                        <v-form-label><span class="text-secondary-500 italic">Law Firm</span> Email</v-form-label>
                        <v-form-input type="text" v-model="form.attorney.law_firm.email" :error="form.errors['attorney.law_firm.email']" :disabled="form.processing" />
                        <v-form-error v-if="form.errors['attorney.law_firm.email']">{{ form.errors['attorney.law_firm.email'] }}</v-form-error>
                    </v-form-group>

                    <v-form-group class="col-span-full xl:col-span-4">
                        <v-form-label><span class="text-secondary-500 italic">Law Firm</span> Phone Number</v-form-label>
                        <v-form-input type="text" v-model="form.attorney.law_firm.phone_number" :error="form.errors['attorney.law_firm.phone_number']" :disabled="form.processing" />
                        <v-form-error v-if="form.errors['attorney.law_firm.phone_number']">{{ form.errors['attorney.law_firm.phone_number'] }}</v-form-error>
                    </v-form-group>

                    <v-form-group class="col-span-full md:col-span-4">
                        <v-form-label><span class="text-secondary-500 italic">Law Firm</span> Address Line 1 </v-form-label>
                        <v-form-input type="text" v-model="form.attorney.law_firm.address_line_1" :error="form.errors['attorney.law_firm.address_line_1']" :disabled="form.processing" />
                        <v-form-error v-if="form.errors['attorney.law_firm.address_line_1']">{{ form.errors['attorney.law_firm.address_line_1'] }}</v-form-error>
                    </v-form-group>

                    <v-form-group class="col-span-full md:col-span-4">
                        <v-form-label><span class="text-secondary-500 italic">Law Firm</span> Address Line 2</v-form-label>
                        <v-form-input type="text" v-model="form.attorney.law_firm.address_line_2" :error="form.errors['attorney.law_firm.address_line_2']" :disabled="form.processing" />
                        <v-form-error v-if="form.errors['attorney.law_firm.address_line_2']">{{ form.errors['attorney.law_firm.address_line_2'] }}</v-form-error>
                    </v-form-group>

                    <v-form-group class="col-span-full md:col-span-4">
                        <v-form-label><span class="text-secondary-500 italic">Law Firm</span> City</v-form-label>
                        <v-form-input type="text" v-model="form.attorney.law_firm.city" :error="form.errors['attorney.law_firm.city']" :disabled="form.processing" />
                        <v-form-error v-if="form.errors['attorney.law_firm.city']">{{ form.errors['attorney.law_firm.city'] }}</v-form-error>
                    </v-form-group>

                    <v-form-group class="col-span-full md:col-span-4">
                        <v-form-label><span class="text-secondary-500 italic">Law Firm</span> State</v-form-label>
                        <v-form-select
                            :options="states.data.map((state) => ({ label: state.name, value: state.state_id }))"
                            :error="form.errors['attorney.law_firm.state_id']"
                            :disabled="true"
                            v-model="form.attorney.law_firm.state_id"
                        />
                        <v-form-error v-if="form.errors['attorney.law_firm.state_id']">{{ form.errors['attorney.law_firm.state_id'] }}</v-form-error>
                    </v-form-group>

                    <v-form-group class="col-span-full md:col-span-4">
                        <v-form-label><span class="text-secondary-500 italic">Law Firm</span> Zip Code</v-form-label>
                        <v-form-input type="text" v-model="form.attorney.law_firm.zip_code" :error="form.errors['attorney.law_firm.zip_code']" :disabled="form.processing" />
                        <v-form-error v-if="form.errors['attorney.law_firm.zip_code']">{{ form.errors['attorney.law_firm.zip_code'] }}</v-form-error>
                    </v-form-group>
                </div>
            </template>
        </v-content-body>
    </div>
</template>

<script>
export default {
    props: {
        attorneys: {
            type: Object,
            required: true,
            default: () => {},
        },
        attorneys: {
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
    },
    data() {
        return {
            dataStrategy: "existing",
        };
    },
    watch: {
        dataStrategy: {
            handler(value) {
                if (value === "new") {
                    this.form.attorney.user_id = null;
                }
            },
        },
    },
};
</script>