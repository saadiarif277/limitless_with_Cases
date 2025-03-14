<template>
    <v-inertia-head title="Edit Profile" />
    
    <div class="h-full flex flex-col">
        <v-content-body class="border-b border-gray-200">
            <v-section-heading>
                <template #title>
                    User Profile
                </template>

                <template #description>
                    Change and/or modify your user's profile information.
                </template>
            </v-section-heading>
        </v-content-body>

        <v-content-body class="bg-yellow-100" v-if="mustVerifyEmail && this.$page.props.auth.user.email_verified_at === null">
            <div>
                <v-heading :size="6">
                    Your email address is unverified.
                    <v-button :href="route('verification.send')" method="post" as="button">
                        Click here to re-send the verification email.
                    </v-button>
                </v-heading>

                <v-heading :size="6" v-show="status === 'verification-link-sent'" class="mt-2 font-medium text-sm text-green-600">
                    A new verification link has been sent to your email address.
                </v-heading>
            </div>
        </v-content-body>

        <v-content-body>
            <v-form class="grid grid-cols-1 md:grid-cols-2 gap-6" @submit.prevent="submitForm">
                <v-form-group>
                    <v-form-label>Name</v-form-label>
                    <v-form-input type="text" v-model="form.name" :error="form.errors.name" :required="true" :disabled="form.processing" />
                    <v-form-error v-if="form.errors.name">{{ form.errors.name }}</v-form-error>
                </v-form-group>

                <v-form-group>
                    <v-form-label>Email</v-form-label>
                    <v-form-input type="email" v-model="form.email" :error="form.errors.email" :required="true" :disabled="form.processing" />
                    <v-form-error v-if="form.errors.email">{{ form.errors.email }}</v-form-error>
                </v-form-group>

                <v-form-group>
                    <v-form-label>Password</v-form-label>
                    <v-form-input type="password" v-model="form.password" :error="form.errors.password" :disabled="form.processing" />
                    <v-form-error v-if="form.errors.password">{{ form.errors.password }}</v-form-error>
                </v-form-group>
                
                <v-form-group>
                    <v-form-label>Password Confirmation</v-form-label>
                    <v-form-input type="password" v-model="form.password_confirmation" :error="form.errors.password_confirmation" :disabled="form.processing" />
                    <v-form-error v-if="form.errors.password_confirmation">{{ form.errors.password_confirmation }}</v-form-error>
                </v-form-group>

                <v-card class="col-span-full">
                    <v-content-body class="col-span-full grid grid-cols-1 md:grid-cols-6 xl:grid-cols-12 gap-x-6 gap-y-4">
                        <div class="col-span-full">
                            <v-section-heading>
                                <template #title>
                                    Location Information
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

                <v-form-group class="col-span-full flex items-center justify-end gap-2 text-right border-t border-gray-200 py-6">
                    <progress v-if="form.progress" :value="form.progress.percentage" max="100">
                        {{ form.progress.percentage }}%
                    </progress>

                    <v-button :disabled="form.processing">
                        Save Changes
                    </v-button>
                </v-form-group>
            </v-form>
        </v-content-body>
    </div>
</template>

<script>
import Layout from "@/layouts/panel/admin/index.vue";
import { useForm } from "@inertiajs/vue3";

export default {
    name: "PanelUserProfileEdit",
    layout: Layout,
    props: {
        mustVerifyEmail: {
            type: Boolean,
        },
        status: {
            type: String,
        },
        states: {
            type: Object,
            required: true,
        },
    },
    data() {
        return {
            form: useForm({
                // General Information
                name: this.$page.props.auth.user.name,
                email: this.$page.props.auth.user.email,
                // phone_number: "",
                // birthdate: "",

                // Location Information
                address_line_1: this.$page.props.auth.user.address_line_1,
                address_line_2: this.$page.props.auth.user.address_line_2,
                city: this.$page.props.auth.user.city,
                state_id: this.$page.props.auth.user.state_id,
                zip_code: this.$page.props.auth.user.zip_code,
            
                // Personal Health Information
                // gender: "",
                // height: "",
                // weight: "",

                // Access & Security
                password: "",
                password_confirmation: "",
                // role_names: [],
                // clinic_ids: [],
            }),
        };
    },
    methods: {
        submitForm() {
            this.form.clearErrors();

            this.form.patch(this.route('panel.user.profile.update'), {
                onSuccess: () => this.$toast().success("Profile updated successfully!"),
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
