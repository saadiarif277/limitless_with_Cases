<template>
    <v-inertia-head :title="(!user) ? 'Create Patient' : `Editing '${user.data.name}' patient.`" />

    <div class="h-full flex flex-col">
        <v-content-body class="border-b border-gray-200">
            <v-section-heading>
                <template #title>
                    <template v-if="!user">
                        Create Patient
                    </template>

                    <template v-else>
                        Editing '{{ user.data.name }}' patient.
                    </template>
                </template>

                <template #description>
                    <template v-if="!user">
                        Fill up the form below to create a new user for the application.
                    </template>

                    <template v-else>
                        Change and/or modify the details for the user.
                    </template>
                </template>
            </v-section-heading>
        </v-content-body>

        <v-content-body>
            <v-form class="grid grid-cols-1 md:grid-cols-2 gap-6" @submit.prevent="submitForm">
                <v-card class="col-span-full">
                    <v-content-body class="col-span-full grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-x-6 gap-y-4">
                        <div class="col-span-full">
                            <v-section-heading>
                                <template #title>
                                    General Information
                                </template>

                                <template #description>
                                    Basic information about the user.
                                </template>
                            </v-section-heading>
                        </div>

                        <v-form-group>
                            <v-form-label>Name</v-form-label>
                            <v-form-input type="text" v-model="form.name" :error="form.errors.name" :disabled="form.processing" />
                            <v-form-error v-if="form.errors.name">{{ form.errors.name }}</v-form-error>
                        </v-form-group>

                        <v-form-group>
                            <v-form-label>Email</v-form-label>
                            <v-form-input type="text" v-model="form.email" :error="form.errors.email" :disabled="form.processing" />
                            <v-form-error v-if="form.errors.email">{{ form.errors.email }}</v-form-error>
                        </v-form-group>

                        <v-form-group>
                            <v-form-label>Phone Number</v-form-label>
                            <v-form-input type="text" v-model="form.phone_number" :error="form.errors.phone_number" :disabled="form.processing" />
                            <v-form-error v-if="form.errors.phone_number">{{ form.errors.phone_number }}</v-form-error>
                        </v-form-group>

                        <v-form-group>
                            <v-form-label>Birthdate</v-form-label>
                            <v-form-input type="date" v-model="form.birthdate" :error="form.errors.birthdate" :disabled="form.processing" />
                            <v-form-error v-if="form.errors.birthdate">{{ form.errors.birthdate }}</v-form-error>
                        </v-form-group>
                    </v-content-body>
                </v-card>

                <v-card class="col-span-full">
                    <v-content-body class="col-span-full grid grid-cols-1 md:grid-cols-6 xl:grid-cols-12 gap-x-6 gap-y-4">
                        <div class="col-span-full">
                            <v-section-heading>
                                <template #title>
                                    Location Information
                                </template>

                                <template #description>
                                    Address data for the user.
                                </template>
                            </v-section-heading>
                        </div>

                        <v-form-group class="col-span-full md:col-span-3">
                            <v-form-label>Address Line 1 </v-form-label>
                            <v-form-input type="text" v-model="form.address_line_1" :error="form.errors.address_line_1" :disabled="form.processing" />
                            <v-form-error v-if="form.errors.address_line_1">{{ form.errors.address_line_1 }}</v-form-error>
                        </v-form-group>

                        <v-form-group class="col-span-full md:col-span-3">
                            <v-form-label>Address Line 2</v-form-label>
                            <v-form-input type="text" v-model="form.address_line_2" :error="form.errors.address_line_2" :disabled="form.processing" />
                            <v-form-error v-if="form.errors.address_line_2">{{ form.errors.address_line_2 }}</v-form-error>
                        </v-form-group>

                        <v-form-group class="col-span-full md:col-span-2">
                            <v-form-label>City</v-form-label>
                            <v-form-input type="text" v-model="form.city" :error="form.errors.city" :disabled="form.processing" />
                            <v-form-error v-if="form.errors.city">{{ form.errors.city }}</v-form-error>
                        </v-form-group>

                        <v-form-group class="col-span-full md:col-span-2">
                            <v-form-label>State</v-form-label>
                            <v-form-select
                                :options="states.data.map((state) => ({ label: state.name, value: state.state_id }))"
                                :error="form.errors.state_id"
                                :disabled="form.processing"
                                v-model="form.state_id"
                            />
                            <v-form-error v-if="form.errors.state_id">{{ form.errors.state_id }}</v-form-error>
                        </v-form-group>

                        <v-form-group class="col-span-full md:col-span-2">
                            <v-form-label>Zip Code</v-form-label>
                            <v-form-input type="text" v-model="form.zip_code" :error="form.errors.zip_code" :disabled="form.processing" />
                            <v-form-error v-if="form.errors.zip_code">{{ form.errors.zip_code }}</v-form-error>
                        </v-form-group>
                    </v-content-body>
                </v-card>

                <v-card class="col-span-full">
                    <v-content-body class="col-span-full grid grid-cols-1 md:grid-cols-3 xl:grid-cols-6 gap-x-6 gap-y-4">
                        <div class="col-span-full">
                            <v-section-heading>
                                <template #title>
                                    Personal Health Information
                                </template>

                                <template #description>
                                    Data regarding the personal status of the user.
                                </template>
                            </v-section-heading>
                        </div>

                        <v-form-group>
                            <v-form-label>Gender</v-form-label>
                            <v-form-input type="text" v-model="form.gender" :error="form.errors.gender" :disabled="form.processing" />
                            <v-form-error v-if="form.errors.gender">{{ form.errors.gender }}</v-form-error>
                        </v-form-group>

                        <v-form-group>
                            <v-form-label>Height</v-form-label>
                            <v-form-input type="text" v-model="form.height" :error="form.errors.height" :disabled="form.processing" />
                            <v-form-error v-if="form.errors.height">{{ form.errors.height }}</v-form-error>
                        </v-form-group>

                        <v-form-group>
                            <v-form-label>Weight</v-form-label>
                            <v-form-input type="text" v-model="form.weight" :error="form.errors.weight" :disabled="form.processing" />
                            <v-form-error v-if="form.errors.weight">{{ form.errors.weight }}</v-form-error>
                        </v-form-group>
                    </v-content-body>
                </v-card>

                <v-form-group class="col-span-full flex items-center justify-end gap-2 text-right border-t border-gray-200 py-6">
                    <progress v-if="form.progress" :value="form.progress.percentage" max="100">
                        {{ form.progress.percentage }}%
                    </progress>

                    <v-button :href="route('panel.user.patients.index')" color="white" :disabled="form.processing">
                        Cancel
                    </v-button>

                    <template v-if="!user">
                        <v-button :disabled="form.processing">
                            Create User
                        </v-button>
                    </template>

                    <template v-else>
                        <v-button :disabled="form.processing">
                            Save Changes
                        </v-button>
                    </template>
                </v-form-group>
            </v-form>
        </v-content-body>
    </div>
</template>

<script>
import Layout from "@/layouts/panel/user/index.vue";
import { useForm } from "@inertiajs/vue3";

export default {
    name: "UsersCreateEdit",
    layout: Layout,
    props: {
        clinics: {
            type: Object,
            required: false,
            default: () => {},
        },
        roles: {
            type: Object,
            required: false,
            default: () => {},
        },
        states: {
            type: Object,
            required: false,
            default: () => {},
        },
        user: {
            type: Object,
            required: false,
            default: () => {},
        },
    },
    data() {
        return {
            form: useForm({
                // General Information
                name: "",
                email: "",
                phone_number: "",
                birthdate: "",

                // Location Information
                address_line_1: "",
                address_line_2: "",
                city: "",
                state_id: "",
                zip_code: "",
            
                // Personal Health Information
                gender: "",
                height: "",
                weight: "",

                // Access & Security
                password: "",
                password_confirmation: "",
                role_names: [],
                clinic_ids: [],
            }),
        };
    },
    watch: {
        user: {
            handler(value) {
                if (!value || !value.data) {
                    return;
                }

                // General Information
                this.form.name = value.data.name;
                this.form.email = value.data.email;
                this.form.phone_number = value.data.phone_number;
                this.form.birthdate = value.data.birthdate;

                // Location Information
                this.form.address_line_1 = value.data.address_line_1;
                this.form.address_line_2 = value.data.address_line_2;
                this.form.city = value.data.city;
                this.form.state_id = value.data.state_id;
                this.form.zip_code = value.data.zip_code;
            
                // Personal Health Information
                this.form.gender = value.data.gender;
                this.form.height = value.data.height;
                this.form.weight = value.data.weight;

                // Access & Security
                this.form.role_names = value.data.roles;
                this.form.clinic_ids = value.data.clinic_ids;
            },
            immediate: true,
        },
    },
    methods: {
        submitForm() {
            this.form.clearErrors();
            
            if (this.user && this.user.data && this.user.data.user_id) {
                this.updateRequest();
            } else {
                this.storeRequest();
            }
        },
        storeRequest() {
            this.form.post(this.route('panel.user.patients.store'), {
                onSuccess: () => this.$toast().success("Patient created successfully!"),
                onError: (errors) => {
                    Object.keys(errors).forEach((key) => {
                        const error = errors[key];
                        this.$toast().error(error);
                    });
                },
            });
        },
        updateRequest() {
            this.form.patch(this.route('panel.user.patients.update', { user: this.user.data.user_id }), {
                onSuccess: () => this.$toast().success("Patient updated successfully!"),
                onError: (errors) => {
                    Object.keys(errors).forEach((key) => {
                        const error = errors[key];
                        this.$toast().error(error);
                    });
                },
            });
        },
        deleteRequest() {
            this.$alert().confirm(() => {
                return new Promise((resolve, reject) => {
                    this.form.delete(this.route('panel.user.patients.destroy', { user: this.user.data.user_id }), {
                        onSuccess: () => {
                            this.$toast().success("Patient deleted successfully!");
                            resolve();
                        },
                        onError: (errors) => {
                            Object.keys(errors).forEach((key) => {
                                const error = errors[key];
                                this.$toast().error(error);
                            });

                            reject();
                        },
                    });
                });
            });
        },
    },
};
</script>
