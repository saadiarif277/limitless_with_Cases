<template>
    <v-inertia-head title="Reset Password" />

    <v-section class="gap-4">
        <v-section-heading>
            <template #title>Reset Password</template>
            <template #description>Please fill-in the form below to reset your password.</template>
        </v-section-heading>

        <v-form class="grid grid-cols-1 gap-4" @submit.prevent="submitForm">
            <v-form-group>
                <v-form-label>Email</v-form-label>
                <v-form-input type="email" name="email" v-model="form.email" :disabled="form.processing" :required="true" autofocus autocomplete="username" />
                <v-form-error v-if="form.errors.email">{{ form.errors.email }}</v-form-error>
            </v-form-group>

            <v-form-group>
                <v-form-label>Password</v-form-label>
                <v-form-input type="password" name="password" v-model="form.password" :disabled="form.processing" :required="true" />
                <v-form-error v-if="form.errors.password">{{ form.errors.password }}</v-form-error>
            </v-form-group>

            <v-form-group>
                <v-form-label>Password Confirmation</v-form-label>
                <v-form-input type="password" name="password_confirmation" v-model="form.password_confirmation" :disabled="form.processing" :required="true" />
                <v-form-error v-if="form.errors.password_confirmation">{{ form.errors.password_confirmation }}</v-form-error>
            </v-form-group>

            <v-form-group>
                <v-button type="submit" color="primary" class="w-full" :disabled="form.processing">
                    Reset Password
                </v-button>
            </v-form-group>
        </v-form>
    </v-section>
</template>

<script>
import Layout from "@/layouts/auth/index.vue";
import { useForm } from "@inertiajs/vue3";

export default {
    layout: Layout,
    name: "AuthResetPassword",
    props: {
        email: {
            type: String,
            required: true,
        },
        token: {
            type: String,
            required: true,
        },
    },
    data() {
        return {
            form: useForm({
                token: this.token,
                email: this.email,
                password: "",
                password_confirmation: "",
            }),
        };
    },
    watch: {
        email: {
            handler(value) {
                this.form.email = value;
            },
            immediate: true,
        },
        token: {
            handler(value) {
                this.form.token = value;
            },
            immediate: true,
        },
    },
    methods: {
        submitForm() {
            this.form.clearErrors();

            this.form.post(route("auth.password.store"), {
                onSuccess: () => {
                    this.$toast().success("Your password has been changed!");
                },
                onError: (errors) => {
                    Object.keys(errors).forEach((key) => {
                        this.$toast().error(errors[key]);
                    });
                },
                onFinish: () => {
                    this.form.reset("password", "password_confirmation");
                },
            });
        },
    },
};
</script>
