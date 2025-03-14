<template>
    <div class="h-full flex flex-col">
        <!-- Page Header -->
        <v-content-body class="border-b border-gray-200">
            <v-section-heading>
                <template #title>
                    ICT Codes Listing
                </template>
                <template #description>
                    Manage ICT Codes available in the application.
                </template>
                <template #actions>
                    <v-button :href="route('panel.admin.ictcodes.create')" class="mr-2">
                        Create ICT Code
                    </v-button>
                    <v-button @click="openModal" class="ml-1">
                        Upload in Bulk
                    </v-button>
                </template>
            </v-section-heading>
        </v-content-body>

        <!-- Table for ICT Codes -->
        <v-app-model-table :columns="columns" :data="ictCodes.data || ictCodes" :links="ictCodes.links || []"
            :meta="ictCodes.meta || []">
            <template v-slot:column_id="{ row: ictCode }">
                {{ ictCode.code }}
            </template>
        </v-app-model-table>

        <!-- Modal for Bulk Upload -->
        <v-dialog v-model="showModal" max-width="500">


            <template #default>
                <v-card>
                    <v-card-title>Upload ICT Codes in Bulk</v-card-title>
                    <v-card-text>
                        <v-file-input v-model="bulkFile" label="Select CSV File" accept=".csv" required />
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-button @click="submitBulkUpload">Upload</v-button>
                        <v-button @click="closeModal">Cancel</v-button>
                    </v-card-actions>
                </v-card>
            </template>
        </v-dialog>
    </div>
</template>

<script>
import Layout from "@/layouts/panel/admin/index.vue";

export default {
    layout: Layout,
    props: {
        ictCodes: {
            type: [Object, Array],
            required: true,
            default: () => [],
        },
    },
    data() {
        return {
            showModal: false,   // Modal visibility state
            bulkFile: null,     // Holds the selected file for upload
            columns: {
                code: {
                    label: "ICT Code",
                },
                description: {
                    label: "Description",
                },
                created_at: {
                    label: "Created At",
                },
            },
        };
    },
    methods: {
        openModal() {
            this.showModal = true; // Open modal when "Upload in Bulk" is clicked
        },
        closeModal() {
            this.showModal = false; // Close modal when "Cancel" is clicked
        },
        submitBulkUpload() {
            if (!this.bulkFile) {
                this.$toast.error("Please select a file.");
                return;
            }

            const formData = new FormData();
            formData.append("file", this.bulkFile);

            this.$inertia.post(route("panel.admin.ictcodes.bulk-upload"), formData, {
                onSuccess: () => {

                    this.closeModal();
                    this.$toast().success("Date Imported successfully!");
                },
                onError: () => {
                    this.closeModal();
                    this.$toast().error("Failed to upload file.");
                },
            });
        },
    },
};
</script>

<style scoped>
/* Custom styles for modal, if necessary */
</style>
