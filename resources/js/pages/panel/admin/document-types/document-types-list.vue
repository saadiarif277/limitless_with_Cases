<template>
    <v-inertia-head title="Document Type Management" />

    <div class="h-full flex flex-col">
        <v-content-body class="border-b border-gray-200">
            <v-section-heading>
                <template #title>
                    Document Types
                </template>

                <template #description>
                    Manage document types available in the application.
                </template>

                <template #actions>
                    <v-button :href="route('panel.admin.document-types.create')">
                        Create Document Type
                    </v-button>
                </template>
            </v-section-heading>
        </v-content-body>

        <v-app-model-table :columns="columns" :data="documentTypes.data" :links="documentTypes.links" :meta="documentTypes.meta">
            <template v-slot:column_document_type_id="{ row: documentType }">
                <v-link :href="`/admin/document-types/${documentType.document_type_id}`" class="font-semibold">
                    {{ documentType.name }}
                </v-link>
            </template>
        </v-app-model-table>
    </div>
</template>

<script>
import Layout from "@/layouts/panel/admin/index.vue";

export default {
    layout: Layout,
    props: {
        documentTypes: {
            type: Object,
            required: false,
            default: () => {},
        },
        links: {
            type: Object,
            required: false,
            default: () => {},
        },
    },
    data(){
        return {
            columns: {
                document_type_id: {
                    label: "Name",
                    formatter: (row) => row.name,
                },
                document_category_id: {
                    label: "Document Category",
                    formatter: (row) => row.document_category.name,
                },
            },
        };
    },
};
</script>
