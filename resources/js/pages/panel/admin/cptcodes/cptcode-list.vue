<template>
    <v-inertia-head title="Cpt Codes List" />

    <div class="h-full flex flex-col">
        <v-content-body class="border-b border-gray-200">
            <v-section-heading>
                <template #title>
                    Cpt Codes Listing
                </template>

                <template #description>
                    Manage CPT Codes available in the application.
                </template>
                <template #actions>
                    <v-button :href="route('panel.admin.cptcodes.create')" class="mr-2">
                        Create Cpt Code
                    </v-button>
                    <v-button @click="openModal" class="ml-1">
                        Upload in Bulk
                    </v-button>
                </template>
            </v-section-heading>
        </v-content-body>

        <!-- Table for Cpt Codes -->
        <v-app-model-table
            :columns="columns"
            :data="CptCodes.data || CptCodes"
            :links="CptCodes.links || []"
            :meta="CptCodes.meta || []"
        >
            <!-- Custom template for the 'code' column -->
            <template v-slot:column_id="{ row: CptCode }">
                <!-- <v-row class="font-semibold"> -->
                    {{ CptCode.code }}
                <!-- </v-row> -->
            </template>
        </v-app-model-table>
             <!-- Modal for Bulk Upload -->
             <v-dialog v-model="showModal" max-width="450">
            <v-card class="pa-4">
                <v-card-title class="text-h5 text-center">Upload CPT Codes in Bulk</v-card-title>
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

        CptCodes: {
            type: [Object, Array], // Allow CptCodes to be an array or an object
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
                    label: "Cpt Code",
                },
                description: {
                    label: "Description",
                },
                default_value: {
                    label: "Cost (Pricing)"
                },
                created_at: {
                    label: "Created At",
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

            this.$inertia.post(route("panel.admin.cptcodes.bulk-upload"), formData, {
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
