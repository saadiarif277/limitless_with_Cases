<template>
    <v-inertia-head title="Create CPT Code" />

    <div class="h-full flex flex-col">
        <!-- Heading Section -->
        <v-content-body class="border-b border-gray-200">
            <v-section-heading>
                <template #title>Create Cpt Code</template>
                <template #description>Add a new CPT code to the system.</template>
            </v-section-heading>
        </v-content-body>

        <!-- Form Section -->
        <div class="p-4">
            <form @submit.prevent="createCptCode" class="space-y-4">
                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700">CPT Code</label>
                    <input
                        type="text"
                        id="code"
                        v-model="form.code"
                        required
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                    />
                </div>
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea
                        id="description"
                        v-model="form.description"
                        rows="3"
                        required
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                    ></textarea>
                </div>

                <div>
                    <label for="default_value" class="block text-sm font-medium text-gray-700">Cost ( Price )</label>
                    <input
                        type="text"
                        id="default_value"
                        v-model="form.default_value"
                        required
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                    />
                </div>
                <div class="flex justify-end">
                    <v-button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">
                        Save CPT Code
                    </v-button>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
import Layout from "@/layouts/panel/admin/index.vue";
import axios from "axios";

export default {
    layout: Layout,
    data() {
        return {
            form: {
                code: "",
                description: "",
                default_value:"",
            },
        };
    },
    methods: {
        async createCptCode() {
            try {
                const response = await axios.post(route("panel.admin.cptcodes.store"), this.form);

                // Handle success
                this.$toast().success(response.data.message); // Show success toast
                this.$inertia.visit(route("panel.admin.cptcodes.list")); // Redirect to the listing page
            } catch (error) {
                // Handle error
                if (error.response && error.response.data && error.response.data.errors) {
                    const errors = error.response.data.errors;
                    Object.keys(errors).forEach((key) => {
                        this.$toast().error(errors[key][0]); // Show error toast for each field
                    });
                } else {
                    this.$toast().error("Failed to create CPT Code. Please try again."); // General error
                }
            }
        },
    },
};
</script>
