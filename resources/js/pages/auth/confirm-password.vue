<template>
    <v-inertia-head title="Confirm Password" />

    <v-section class="gap-4">
        <v-section-heading>
            <template #title>Confirm Password</template>
            <template #description>This is a secure area of the application. Please confirm your password before continuing.</template>
        </v-section-heading>

        <v-form class="grid grid-cols-1 gap-4" @submit.prevent="submitForm">
            <v-form-group>
                <v-form-label>Password</v-form-label>
                <v-form-input type="password" name="password" v-model="form.password" :disabled="form.processing" :required="true" autofocus />
                <v-form-error v-if="form.errors.password">{{ form.errors.password }}</v-form-error>
            </v-form-group>

            <v-form-group>
                <v-button type="submit" color="primary" class="w-full" :disabled="form.processing">
                    Confirm Password
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
    name: "AuthConfirmPassword",
    data() {
        return {
            form: useForm({
                password: "",
            }),
        };
    },
    methods: {
        submitForm() {
            this.form.clearErrors();

            this.form.post(route("password.confirm"), {
                onSuccess: () => {
                    this.$toast().success("Thank you for confirming your password!");
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
