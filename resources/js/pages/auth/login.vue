<template>
    <v-inertia-head title="Login" />

    <v-section class="gap-4">
        <v-section-heading>
            <template #title>Welcome to the Referral System</template>
            <template #description>Please sign-in to your account and start the adventure.</template>
        </v-section-heading>

        <v-form class="grid grid-cols-2 gap-4" @submit.prevent="submitForm">
            <v-form-group class="col-span-full">
                <v-form-label>Email</v-form-label>
                <v-form-input type="email" name="email" v-model="form.email" :disabled="form.processing" :required="true" autofocus autocomplete="username" />
                <v-form-error v-if="form.errors.email">{{ form.errors.email }}</v-form-error>
            </v-form-group>

            <v-form-group class="col-span-full">
                <v-form-label>Password</v-form-label>
                <v-form-input type="password" name="password" v-model="form.password" :disabled="form.processing" :required="true" />
                <v-form-error v-if="form.errors.password">{{ form.errors.password }}</v-form-error>
            </v-form-group>

            <v-form-group class="col-span-full">
                <v-form-checkbox v-model:checked="form.remember" :disabled="form.processing">
                    Remember Me
                </v-form-checkbox>
            </v-form-group>

            <v-form-group class="col-span-full sm:col-span-1">
                <v-button type="submit" color="primary" class="w-full" :disabled="form.processing">
                    Login
                </v-button>
            </v-form-group>

            <v-form-group class="col-span-full sm:col-span-1">
                <v-button :href="route('auth.password.request')" color="dark" class="w-full" :disabled="form.processing">
                    Forgot Password
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
    name: "AuthLogin",
    data() {
        return {
            form: useForm({
                email: "",
                password: "",
                remember: false,
            }),
        };
    },
    methods: {
        submitForm() {
            this.form.clearErrors();

            this.form.post(route("auth.login"), {
                onSuccess: () => {
                    this.$toast().success("Welcome Back!");
                },
                onError: (errors) => {
                    Object.keys(errors).forEach((key) => {
                        this.$toast().error(errors[key]);
                    });
                },
                onFinish: () => {
                    this.form.reset("password");
                },
            });
        },
    },
};
</script>
