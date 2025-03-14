<template>
    <v-inertia-head :title="(!state) ? 'Create State' : `Editing '${state.data.name}' state.`" />

    <div class="h-full flex flex-col">
        <v-content-body class="border-b border-gray-200">
            <v-section-heading>
                <template #title>
                    <template v-if="!state">
                        Create State
                    </template>

                    <template v-else>
                        Editing '{{ state.data.name }}' state.
                    </template>
                </template>

                <template #description>
                    <template v-if="!state">
                        Fill up the form below to create a new state for the application.
                    </template>

                    <template v-else>
                        Change and/or modify the details for the state.
                    </template>
                </template>
            </v-section-heading>
        </v-content-body>

        <v-content-body>
            <v-form class="space-y-6" @submit.prevent="submitForm">
                <v-form-group>
                    <v-form-label>Name</v-form-label>
                    <v-form-input type="text" v-model="form.name" :error="form.errors.name" :disabled="form.processing" />
                    <v-form-error v-if="form.errors.name">{{ form.errors.name }}</v-form-error>
                </v-form-group>

                <v-form-group>
                    <v-form-label>Document Types</v-form-label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6">
                        <template v-for="(documentType, documentTypeIndex) in documentTypes.data" :key="'documentType_' + documentTypeIndex">
                            <v-form-checkbox v-model="form.document_type_ids" :value="documentType.document_type_id" :disabled="form.processing">
                                {{ documentType.name }}
                            </v-form-checkbox>
                        </template>
                    </div>
                    <v-form-error v-if="form.errors.documentType_ids">{{ form.errors.documentType_ids }}</v-form-error>
                </v-form-group>

                <v-form-group class="flex items-center justify-end gap-2 text-right border-t border-gray-200 py-6">
                    <progress v-if="form.progress" :value="form.progress.percentage" max="100">
                        {{ form.progress.percentage }}%
                    </progress>

                    <v-button :href="route('panel.admin.states.index')" color="white" :disabled="form.processing">
                        Cancel
                    </v-button>

                    <template v-if="!state">
                        <v-button :disabled="form.processing">
                            Create State
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
    name: "StatesCreateEdit",
    layout: Layout,
    props: {
        documentTypes: {
            type: Object,
            required: false,
            defualt: () => {},
        },
        state: {
            type: Object,
            required: false,
            default: () => {},
        },
    },
    data() {
        return {
            form: useForm({
                name: "",
                document_type_ids: [],
            }),
        };
    },
    watch: {
        state: {
            handler(value) {
                if (!value || !value.data) {
                    return;
                }

                this.form.name = value.data.name;
                this.form.document_type_ids = value.data.document_types.map((documentType) => documentType.document_type_id);
            },
            immediate: true,
        },
    },
    methods: {
        submitForm() {
            this.form.clearErrors();
            
            if (this.state && this.state.data && this.state.data.state_id) {
                this.updateRequest();
            } else {
                this.storeRequest();
            }
        },
        storeRequest() {
            this.form.post(this.route('panel.admin.states.store'), {
                onSuccess: () => this.$toast().success("State created successfully!"),
                onError: (errors) => {
                    Object.keys(errors).forEach((key) => {
                        const error = errors[key];
                        this.$toast().error(error);
                    });
                },
            });
        },
        updateRequest() {
            this.form.patch(this.route('panel.admin.states.update', { state: this.state.data.state_id }), {
                onSuccess: () => this.$toast().success("State updated successfully!"),
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
                    this.form.delete(this.route('panel.admin.states.destroy', { state: this.state.data.id }), {
                        onSuccess: () => {
                            this.$toast().success("State deleted successfully!");
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
