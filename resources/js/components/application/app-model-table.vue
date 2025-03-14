<template>
    <div class="v-app-model-table h-full flex flex-col justify-between">
        <div>
            <v-content-body>
                <div class="grid grid-cols-2">
                    <div class="flex items-center">
                        <v-paragraph>
                            <template v-if="$page.props.query.searchQuery">
                                Searching for "{{ $page.props.query.searchQuery }}". 

                                <v-link href="#" class="font-medium" @click.prevent="clearFilters">
                                    Clear Filters
                                </v-link>
                            </template>

                            <template v-else>
                                All Results
                            </template>
                        </v-paragraph>
                    </div>

                    <div class="flex items-center justify-end">
                        <v-form class="flex items-center justify-between gap-2" @submit.prevent="submitFilterForm">
                            <v-form-group>
                                <v-form-input type="text" v-model="form.searchQuery" placeholder="Search ..." />
                            </v-form-group>

                            <v-form-group>
                                <v-button color="dark">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                                        <path fill-rule="evenodd" d="M10.5 3.75a6.75 6.75 0 1 0 0 13.5 6.75 6.75 0 0 0 0-13.5ZM2.25 10.5a8.25 8.25 0 1 1 14.59 5.28l4.69 4.69a.75.75 0 1 1-1.06 1.06l-4.69-4.69A8.25 8.25 0 0 1 2.25 10.5Z" clip-rule="evenodd" />
                                    </svg>
                                </v-button>
                            </v-form-group>
                        </v-form>
                    </div>
                </div>
            </v-content-body>

            <v-table class="border-t border-gray-200">
                <v-table-head>
                    <template v-for="(column, columnIndex) in columns" :key="'column_' + columnIndex">
                        <v-table-header :class="tableHeaderAlignmentClasses[column.align || 'left']">
                            {{ column.label }}
                        </v-table-header>
                    </template>
                </v-table-head>

                <v-table-body>
                    <template v-for="(row, rowIndex) in data" :key="'row_' + rowIndex">
                        <v-table-row>
                            <template v-for="(column, columnIndex) in columns" :key="'rowColumn_' + columnIndex">
                                <v-table-data :align="column.align || 'left'">
                                    <slot :name="'column_' + columnIndex" :row="row" :column="column">
                                        <template v-if="column.formatter">
                                            {{ column.formatter(row, column) }}
                                        </template>

                                        <template v-else>
                                            {{ row[columnIndex] }}
                                        </template>
                                    </slot>
                                </v-table-data>
                            </template>
                        </v-table-row>
                    </template>

                    <template v-if="!data || !data.length">
                        <v-table-row>
                            <v-table-data colspan="100%">
                                <v-paragraph>No results found.</v-paragraph>
                            </v-table-data>
                        </v-table-row>
                    </template>
                </v-table-body>
            </v-table>
        </div>

        <div class="bg-gray-50 border-t border-gray-200" v-if="links">
            <v-content-body class="flex items-center gap-2 justify-end">
                <v-paragraph>
                    Page {{ meta.current_page }} of {{ meta.last_page }}
                </v-paragraph>

                <div class="grid grid-cols-2 sm:inline-flex items-center space-x-2">
                    <v-button :href="links.prev" :color="'white'" :disabled="!links.prev" :data="{ searchQuery: form.searchQuery || undefined }">
                        Previous
                    </v-button>

                    <v-button :href="links.next" :color="'white'" :disabled="!links.next" :data="{ searchQuery: form.searchQuery || undefined }">
                        Next
                    </v-button>
                </div>
            </v-content-body>
        </div>
    </div>
</template>

<script>
export default {
    name: "AppModelTableComponent",
    props: {
        columns: {
            type: Object,
            required: true,
        },
        data: {
            type: Array,
            required: false,
            default: () => [],
        },
        links: {
            type: Object,
            required: false,
            default: () => {},
        },
        meta: {
            type: Object,
            required: false,
            default: () => {},
        },
    },
    data() {
        return {
            tableHeaderAlignmentClasses: {
                left: "text-left",
                center: "text-center",
                right: "text-right"
            },
            form: {
                searchQuery: this.$page.props.query.searchQuery || "",
            },
        };
    },
    methods: {
        submitFilterForm() {
            this.$inertia.get(route(route().current()), {
                ...this.$page.props.query,
                searchQuery: this.form.searchQuery,
                page: undefined,
            }, {
                preserveState: true,
                preserveScroll: true,
            });
        },
        clearFilters() {
            this.form.searchQuery = "";
            this.$inertia.get(route(route().current()), {
                ...this.$page.props.query,
                searchQuery: undefined,
                page: undefined,
            }, {
                preserveState: true,
                preserveScroll: true,
            });
        },
    },
};
</script>
