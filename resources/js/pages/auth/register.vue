<template>
    <v-inertia-head title="Create Account" />

    <v-section class="gap-4">
        <v-section-heading>
            <template #title>Create Account</template>
            <template #description>Fill the below form to create a new account.</template>
        </v-section-heading>

        <v-form class="grid grid-cols-2 gap-4" @submit.prevent="submitForm">
            <v-form-group class="col-span-full">
                <v-form-label>Name</v-form-label>
                <v-form-input type="text" name="name" v-model="form.name" :disabled="form.processing" :required="true" autofocus />
                <v-form-error v-if="form.errors.name">{{ form.errors.name }}</v-form-error>
            </v-form-group>

            <v-form-group class="col-span-full">
                <v-form-label>Email</v-form-label>
                <v-form-input type="email" name="email" v-model="form.email" :disabled="form.processing" :required="true" />
                <v-form-error v-if="form.errors.email">{{ form.errors.email }}</v-form-error>
            </v-form-group>

            <v-form-group class="col-span-full">
                <v-form-label>Password</v-form-label>
                <v-form-input type="password" name="password" v-model="form.password" :disabled="form.processing" :required="true" />
                <v-form-error v-if="form.errors.password">{{ form.errors.password }}</v-form-error>
            </v-form-group>

            <v-form-group class="col-span-full">
                <v-form-label>Password Confirmation</v-form-label>
                <v-form-input type="password" name="password_confirmation" v-model="form.password_confirmation" :disabled="form.processing" :required="true" />
                <v-form-error v-if="form.errors.password_confirmation">{{ form.errors.password_confirmation }}</v-form-error>
            </v-form-group>

            <v-form-group class="col-span-full sm:col-span-1">
                <v-button type="submit" color="primary" class="w-full" :disabled="form.processing">
                    Create Account
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
    name: "AuthRegister",
    data() {
        return {
            form: useForm({
                name: "",
                email: "",
                password: "",
                password_confirmation: "",
            }),
        };
    },
    methods: {
        submitForm() {
            this.form.clearErrors();

            this.form.post(route("auth.login"), {
                onSuccess: () => {
                    this.$toast().success("Your new account has been created!");
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
