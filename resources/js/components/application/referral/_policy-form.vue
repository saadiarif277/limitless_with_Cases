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

                            <span class="text-dark-500">Insurance</span> Information
                        </div>
                    </div>
                </template>
            </v-section-heading>
        </v-content-body>

        <v-content-body class="relative grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-6 gap-4">

            <!-- Policy Limit Input -->
            <v-form-group class="col-span-full sm:col-span-2 xl:col-span-3">
                <v-form-label><span class="text-dark-500 italic">Policy Limit</span></v-form-label>
                <v-form-input type="number" v-model="form.policy_limit" :error="form.errors['policy_limit']" @input="updateParent" class="border border-gray-300 rounded-lg" />
                <v-form-error v-if="form.errors['policy_limit']">{{ form.errors['policy_limit'] }}</v-form-error>
            </v-form-group>

            <!-- PIP Input -->
            <v-form-group class="col-span-full sm:col-span-2 xl:col-span-3">
                <v-form-label><span class="text-dark-500 italic">PIP</span></v-form-label>
                <v-form-input type="number" v-model="form.pip" :error="form.errors['pip']" @input="updateParent" class="border border-gray-300 rounded-lg" />
                <v-form-error v-if="form.errors['pip']">{{ form.errors['pip'] }}</v-form-error>
            </v-form-group>

            <!-- Defendant Insurance -->
            <v-form-group class="col-span-full sm:col-span-2 xl:col-span-3">
                <v-form-label><span class="text-dark-500 italic">Defendant Insurance</span></v-form-label>
                <v-form-input type="text" v-model="form.defendant_insurance" :error="form.errors['defendant_insurance']" @input="updateParent" class="border border-gray-300 rounded-lg" />
                <v-form-error v-if="form.errors['defendant_insurance']">{{ form.errors['defendant_insurance'] }}</v-form-error>
            </v-form-group>

            <!-- Plaintiff Insurance -->
            <v-form-group class="col-span-full sm:col-span-2 xl:col-span-3">
                <v-form-label><span class="text-dark-500 italic">Plaintiff Insurance</span></v-form-label>
                <v-form-input type="text" v-model="form.plaintiff_insurance" :error="form.errors['plaintiff_insurance']" @input="updateParent" class="border border-gray-300 rounded-lg" />
                <v-form-error v-if="form.errors['plaintiff_insurance']">{{ form.errors['plaintiff_insurance'] }}</v-form-error>
            </v-form-group>

            <!-- Commercial Case Dropdown (Yes/No) -->
            <v-form-group class="col-span-full sm:col-span-2 xl:col-span-3">
            <v-form-label>Is it a Commercial Case?</v-form-label>
<v-form-select
    :options="[
        { label: 'Yes', value: true },
        { label: 'No', value: false }
    ]"
    :error="form.errors.commercial_case"
    :disabled="form.processing"
    :required="true"
    v-model="form.commercial_case"
/>
<v-form-error v-if="form.errors.commercial_case">{{ form.errors.commercial_case }}</v-form-error>
</v-form-group>


            <!-- Type of Accident Dropdown -->
            <v-form-group class="col-span-full sm:col-span-2 xl:col-span-3">
                <v-form-label><span class="text-dark-500 italic">Type of Accident</span></v-form-label>
                <v-form-select
                    :options="typeOfAccidentOptions"
                    v-model="form.type_of_accident"
                    :error="form.errors['type_of_accident']"
                    @change="updateParent"
                    class="border border-gray-300 rounded-lg"
                />
                <v-form-error v-if="form.errors['type_of_accident']">{{ form.errors['type_of_accident'] }}</v-form-error>
            </v-form-group>
        </v-content-body>
    </div>
</template>

<script>
export default {
    props: {
        items: {
            type: Array,
            default: () => []  // Default to an empty array if no items are passed
        }
    },



    data() {
        return {
            form: {
                policy_limit: '',
                pip: '',
                defendant_insurance: '',
                plaintiff_insurance: '',
                commercial_case: false, // default value for the dropdown
                type_of_accident: '',
                errors: {},
                processing: false,
            },
            // Dropdown options for the type of accident
            typeOfAccidentOptions: [
                { label: 'Motor Vehicle Accident', value: 'motor_vehicle' },
                { label: 'Workplace Accident', value: 'workplace' },
                { label: 'Medical Malpractice', value: 'medical_malpractice' },
                { label: 'Slip and Fall', value: 'slip_and_fall' },
            ],
        };

    },
    methods: {
        filterItems() {
            // You can now safely use .filter() because items will always be an array
            const filtered = this.items.filter(item => item.someCondition);
        },
        updateParent() {
            // Emit the form data to the parent component when any field changes
            this.$emit('update:insuranceData', { ...this.form });
        },
        submitForm() {
            this.form.processing = true;

            setTimeout(() => {
                this.form.processing = false;
                alert('Form submitted!');
            }, 2000);
        },
    },
};
</script>
