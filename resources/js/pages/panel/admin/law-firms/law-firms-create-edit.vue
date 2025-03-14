<template>
    <v-inertia-head :title="(!lawFirm) ? 'Create Law Firm' : `Editing '${lawFirm.data.name}' lawFirm.`" />

    <div class="h-full flex flex-col">
        <v-content-body class="border-b border-gray-200">
            <v-section-heading>
                <template #title>
                    <template v-if="!lawFirm">
                        Create Law Firm
                    </template>

                    <template v-else>
                        Editing '{{ lawFirm.data.name }}' lawFirm.
                    </template>
                </template>

                <template #description>
                    <template v-if="!lawFirm">
                        Fill up the form below to create a new law firm for the application.
                    </template>

                    <template v-else>
                        Change and/or modify the details for the lawFirm.
                    </template>
                </template>

                <template #actions>
                    <template v-if="lawFirm">
                        <div class="flex gap-2">
                            <v-button color="danger" :disabled="form.processing" @click.stop="deleteRequest">
                                Delete Law Firm
                            </v-button>
                        </div>
                    </template>
                </template>
            </v-section-heading>
        </v-content-body>

        <v-content-body>
            <v-form class="grid grid-cols-1 sm:grid-cols-3 gap-6" @submit.prevent="submitForm">
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

                <div class="col-span-full grid grid-cols-1 md:grid-cols-6 xl:grid-cols-12 gap-x-6 gap-y-4">
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
                </div>

                <v-form-group class="col-span-full flex items-center justify-end gap-2 text-right border-t border-gray-200 py-6">
                    <progress v-if="form.progress" :value="form.progress.percentage" max="100">
                        {{ form.progress.percentage }}%
                    </progress>

                    <v-button :href="route('panel.admin.law-firms.index')" color="white" :disabled="form.processing">
                        Cancel
                    </v-button>

                    <template v-if="!lawFirm">
                        <v-button :disabled="form.processing">
                            Create Law Firm
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
import Layout from "@/layouts/panel/admin/index.vue";
import { useForm } from "@inertiajs/vue3";

export default {
    name: "LawFirmsCreateEdit",
    layout: Layout,
    props: {
        lawFirm: {
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
            form: useForm({
                name: "",
                email: "",
                phone_number: "",
                address_line_1: "",
                address_line_2: "",
                city: "",
                state_id: "",
                zip_code: "",
                price_read: "",
                price_scan: "",
            }),
        };
    },
    watch: {
        lawFirm: {
            handler(value) {
                if (!value || !value.data) {
                    return;
                }

                this.form.name = value.data.name;
                this.form.email = value.data.email;
                this.form.phone_number = value.data.phone_number;
                this.form.address_line_1 = value.data.address_line_1;
                this.form.address_line_2 = value.data.address_line_2;
                this.form.city = value.data.city;
                this.form.state_id = value.data.state_id;
                this.form.zip_code = value.data.zip_code;
                this.form.price_read = value.data.price_read;
                this.form.price_scan = value.data.price_scan;
            },
            immediate: true,
        },
    },
    methods: {
        submitForm() {
            this.form.clearErrors();
            
            if (this.lawFirm && this.lawFirm.data && this.lawFirm.data.law_firm_id) {
                this.updateRequest();
            } else {
                this.storeRequest();
            }
        },
        storeRequest() {
            this.form.post(this.route('panel.admin.law-firms.store'), {
                onSuccess: () => this.$toast().success("Law firm created successfully!"),
                onError: (errors) => {
                    Object.keys(errors).forEach((key) => {
                        const error = errors[key];
                        this.$toast().error(error);
                    });
                },
            });
        },
        updateRequest() {
            this.form.patch(this.route('panel.admin.law-firms.update', { law_firm: this.lawFirm.data.law_firm_id }), {
                onSuccess: () => this.$toast().success("Law firm updated successfully!"),
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
                    this.form.delete(this.route('panel.admin.law-firms.destroy', { lawFirm: this.lawFirm.data.law_firm_id }), {
                        onSuccess: () => {
                            this.$toast().success("Law firm deleted successfully!");
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
