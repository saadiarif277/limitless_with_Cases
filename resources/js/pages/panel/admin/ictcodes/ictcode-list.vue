<template>
    <div class="h-full flex flex-col">
        <!-- Page Header -->
        <v-content-body class="border-b border-gray-200 py-4 px-6">
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
        <v-app-model-table class="table-container" :columns="columns" :data="ictCodes.data || ictCodes"
            :links="ictCodes.links || []" :meta="ictCodes.meta || []">

            <template v-slot:column_id="{ row: ictCode }">
                <div class="text-center">{{ ictCode.code }}</div>
            </template>

            <template v-slot:column_description="{ row: ictCode }">
                <div class="text-center">{{ ictCode.description }}</div>
            </template>

            <template v-slot:column_created_at="{ row: ictCode }">
                <!-- Format the timestamp -->
                <div class="text-center">{{ formatDate(ictCode.created_at) }}</div>
            </template>

        </v-app-model-table>

        <!-- Modal for Bulk Upload -->
        <v-dialog v-model="showModal" max-width="450">
            <v-card class="pa-4">
                <v-card-title class="text-h5 text-center">Upload ICT Codes in Bulk</v-card-title>
                <v-card-text class="mt-2">
                    <v-file-input v-model="bulkFile" label="Select CSV File" accept=".csv" required outlined dense />
                </v-card-text>
                <v-card-actions class="justify-end">
                    <v-btn @click="submitBulkUpload" color="primary">Upload</v-btn>
                    <v-btn @click="closeModal" color="grey darken-1">Cancel</v-btn>
                </v-card-actions>
            </v-card>
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
            showModal: false,
            bulkFile: null,
            columns: {
                code: {
                    label: "ICT Code",

                    align: "right",
                },
                description: {
                    label: "Description",

                    align: "right",
                },
                created_at: {
                    label: "Created At",

                    align: "right",
                },
            },
        };
    },
    methods: {
        formatDate(timestamp) {
            try {
                return format(new Date(timestamp), "yyyy-MM-dd HH:mm");
            } catch (error) {
                return timestamp; // Fallback in case of parsing error
            }
        },
        openModal() {
            this.showModal = true;
        },
        closeModal() {
            this.showModal = false;
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
                    this.$toast.success("Data Imported successfully!");
                },
                onError: (errors) => {
                    this.closeModal();
                    if (errors.file) {
                        this.$toast.error(errors.file[0]);
                    } else {
                        this.$toast.error("Failed to upload file.");
                    }
                },
            });
        },
    },
};
</script>

<style scoped>

</style>
