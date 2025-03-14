<template>
    <v-inertia-head title="Verify Email" />

    <v-section class="gap-4">
        <v-section-heading>
            <template #title>
                Email Verification
            </template>

            <template #description>
                Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn't receive the email, we will gladly send you another.

                <div class="mt-4 font-medium text-sm text-green-500" v-if="verificationLinkSent">
                    A new verification link has been sent to the email address you provided during registration.
                </div>
            </template>
        </v-section-heading>

        <v-form @submit.prevent="submitForm">
            <v-form-group>
                <v-button type="submit" color="primary" class="w-full" :disabled="form.processing">
                    Resend Verification Email
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
    props: {
        status: {
            type: String,
        },
    },
    data() {
        return {
            form: useForm({}),
        };
    },
    computed: {
        verificationLinkSent() {
            return this.props.status === "verification-link-sent";
        },
    },
    methods: {
        submitForm() {
            this.form.clearErrors();

            this.form.post(route("verification.send"), {
                onSuccess: () => {
                    this.$toast().success("A new verification link has been sent!");
                },
                onError: (errors) => {
                    Object.keys(errors).forEach((key) => {
                        this.$toast().error(errors[key]);
                    });
                },
            });
        },
    },
};
</script>
