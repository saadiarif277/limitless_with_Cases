<template>
    <v-inertia-head :title="(!role) ? 'Create Role' : `Editing '${role.data.name}' role.`" />

    <div class="h-full flex flex-col">
        <v-content-body class="border-b border-gray-200">
            <v-section-heading>
                <template #title>
                    <template v-if="!role">
                        Create Role
                    </template>

                    <template v-else>
                        Editing '{{ role.data.name }}' role.
                    </template>
                </template>

                <template #description>
                    <template v-if="!role">
                        Fill up the form below to create a new role for the application.
                    </template>

                    <template v-else>
                        Change and/or modify the details for the role.
                    </template>
                </template>

                <template #actions>
                    <template v-if="role">
                        <div class="flex gap-2">
                            <v-button color="danger" :disabled="form.processing" @click.stop="deleteRequest">
                                Delete Role
                            </v-button>
                        </div>
                    </template>
                </template>
            </v-section-heading>
        </v-content-body>

        <v-content-body>
            <v-form class="space-y-6" @submit.prevent="submitForm">
                <v-form-group>
                    <v-form-label>Name</v-form-label>
                    <v-form-input type="text" v-model="form.name" :error="form.errors.name" :disabled="form.processing || role" />
                    <v-form-error v-if="form.errors.name">{{ form.errors.name }}</v-form-error>
                </v-form-group>

                <v-form-group>
                    <v-form-label>Document Categories</v-form-label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6">
                        <template v-for="(documentCategory, documentCategoryIndex) in documentCategories.data" :key="'documentCategory_' + documentCategoryIndex">
                            <v-form-checkbox v-model="form.document_category_ids" :value="documentCategory.document_category_id" :disabled="form.processing">
                                {{ documentCategory.name }}
                            </v-form-checkbox>
                        </template>
                    </div>
                    <v-form-error v-if="form.errors.document_category_ids">{{ form.errors.document_category_ids }}</v-form-error>
                </v-form-group>

                <v-form-group>
                    <v-form-label>Permissions</v-form-label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6">
                        <template v-for="(permission, permissionIndex) in permissions.data" :key="'permission_' + permissionIndex">
                            <v-form-checkbox v-model="form.permission_ids" :value="permission.id" :disabled="form.processing">
                                {{ permission.name }}
                            </v-form-checkbox>
                        </template>
                    </div>
                    <v-form-error v-if="form.errors.permission_ids">{{ form.errors.permission_ids }}</v-form-error>
                </v-form-group>

                <v-form-group class="flex items-center justify-end gap-2 text-right border-t border-gray-200 py-6">
                    <progress v-if="form.progress" :value="form.progress.percentage" max="100">
                        {{ form.progress.percentage }}%
                    </progress>

                    <v-button :href="route('panel.admin.roles.index')" color="white" :disabled="form.processing">
                        Cancel
                    </v-button>

                    <template v-if="!role">
                        <v-button :disabled="form.processing">
                            Create Role
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
    name: "RolesCreateEdit",
    layout: Layout,
    props: {
        documentCategories: {
            type: Object,
            required: false,
            defualt: () => {},
        },
        permissions: {
            type: Object,
            required: false,
            defualt: () => {},
        },
        role: {
            type: Object,
            required: false,
            default: () => {},
        },
    },
    data() {
        return {
            form: useForm({
                name: "",
                document_category_ids: [],
                permission_ids: [],
            }),
        };
    },
    watch: {
        role: {
            handler(value) {
                if (!value || !value.data) {
                    return;
                }

                this.form.name = value.data.name;
                this.form.permission_ids = value.data.permissions.map((permission) => permission.id);
                this.form.document_category_ids = JSON.parse(JSON.stringify(value.data.document_categories))
                    .map((documentCategory) => documentCategory.document_category_id)
                    .filter((documentCategory) => documentCategory != null);

                console.log();
            },
            immediate: true,
        },
    },
    methods: {
        submitForm() {
            this.form.clearErrors();
            
            if (this.role && this.role.data && this.role.data.id) {
                this.updateRequest();
            } else {
                this.storeRequest();
            }
        },
        storeRequest() {
            this.form.post(this.route('panel.admin.roles.store'), {
                onSuccess: () => this.$toast().success("Role created successfully!"),
                onError: (errors) => {
                    Object.keys(errors).forEach((key) => {
                        const error = errors[key];
                        this.$toast().error(error);
                    });
                },
            });
        },
        updateRequest() {
            this.form.patch(this.route('panel.admin.roles.update', { role: this.role.data.id }), {
                onSuccess: () => this.$toast().success("Role updated successfully!"),
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
                    this.form.delete(this.route('panel.admin.roles.destroy', { role: this.role.data.id }), {
                        onSuccess: () => {
                            this.$toast().success("Role deleted successfully!");
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
