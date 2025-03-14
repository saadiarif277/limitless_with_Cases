<template>
    <v-inertia-head title="Forgot Password" />

    <v-section class="gap-4">
        <v-section-heading>
            <template #title>Forgot Password</template>
            <template #description>Enter your email and we'll send you instructions to reset your password if an account is found.</template>
        </v-section-heading>

        <v-form class="grid grid-cols-2 gap-4" @submit.prevent="submitForm">
            <v-form-group class="col-span-full">
                <v-form-label>Email</v-form-label>
                <v-form-input type="email" name="email" v-model="form.email" :disabled="form.processing" :required="true" autofocus autocomplete="username" />
                <v-form-error v-if="form.errors.email">{{ form.errors.email }}</v-form-error>
            </v-form-group>

            <v-form-group class="col-span-full sm:col-span-1">
                <v-button type="submit" color="primary" class="w-full" :disabled="form.processing">
                    Email Instructions
                </v-button>
            </v-form-group>

            <v-form-group class="col-span-full sm:col-span-1">
                <v-button :href="route('auth.login')" color="dark" class="w-full" :disabled="form.processing">
                    Login Instead
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
    name: "AuthForgotPassword",
    data() {
        return {
            form: useForm({
                email: "",
            }),
        };
    },
    methods: {
        submitForm() {
            this.form.clearErrors();

            this.form.post(route("auth.password.email"), {
                onSuccess: () => {
                    this.$toast().success("Please check your email including the spam folder!");
                },
                onError: (errors) => {
                    Object.keys(errors).forEach((key) => {
                        this.$toast().error(errors[key]);
                    });
                },
                onFinish: () => {
                    this.form.reset();
                },
            });
        },
    },
};
</script>
