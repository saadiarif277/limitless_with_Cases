<template>
    <v-inertia-head title="Cases Management" />

    <div class="h-full flex flex-col">


        <!-- Section Heading -->
        <v-content-body class="border-b border-gray-200">
            <v-section-heading>
                <template #title>
                    Cases
                </template>

                <template #description>
                    Manage Cases available in the application.
                </template>

                <template #actions>
                    <v-button :href="route('panel.admin.cases.create')">
                        Create Cases
                    </v-button>
                </template>
            </v-section-heading>
        </v-content-body>
     <!-- Top Metrics Boxes -->
     <v-content-body class="border border-gray-200">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 p-4">
                <!-- Total Cases -->
                <div class="bg-white p-6 rounded-lg shadow-md text-center">
                    <div class="text-lg font-semibold text-gray-700">Total Cases</div>
                    <div class="text-2xl font-bold text-blue-600">{{ totalCases }}</div>
                </div>

                <!-- Pending Cases -->
                <div class="bg-white p-6 rounded-lg shadow-md text-center">
                    <div class="text-lg font-semibold text-gray-700">Pending Cases</div>
                    <div class="text-2xl font-bold text-yellow-600">{{ pendingCases }}</div>
                </div>

                <!-- Complete Cases -->
                <div class="bg-white p-6 rounded-lg shadow-md text-center">
                    <div class="text-lg font-semibold text-gray-700">Complete Cases</div>
                    <div class="text-2xl font-bold text-green-600">{{ completeCases }}</div>
                </div>

                <!-- Reduction Request Sent -->
                <div class="bg-white p-6 rounded-lg shadow-md text-center">
                    <div class="text-lg font-semibold text-gray-700">Reduction Sent</div>
                    <div class="text-2xl font-bold text-purple-600">{{ reductionRequestSent }}</div>
                </div>

                <!-- Won -->
                <div class="bg-white p-6 rounded-lg shadow-md text-center">
                    <div class="text-lg font-semibold text-gray-700">Won</div>
                    <div class="text-2xl font-bold text-green-600">{{ wonCases }}</div>
                </div>

                <!-- Lost -->
                <div class="bg-white p-6 rounded-lg shadow-md text-center">
                    <div class="text-lg font-semibold text-gray-700">Lost</div>
                    <div class="text-2xl font-bold text-red-600">{{ lostCases }}</div>
                </div>
            </div>
        </v-content-body>
        <!-- Status Filters -->


        <!-- Search, Filter, and View Toggles -->
        <v-content-body class="border-b border-gray-200">
            <div class="flex justify-between items-center">
                <!-- List/Grid View Toggle (Left) -->
                <div class="flex items-center space-x-2">
                    <button @click="viewMode = 'list'" :class="{'bg-blue-500 text-white': viewMode === 'list'}" class="p-2 rounded-lg hover:bg-blue-100">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                        </svg>
                    </button>
                    <button @click="viewMode = 'grid'" :class="{'bg-blue-500 text-white': viewMode === 'grid'}" class="p-2 rounded-lg hover:bg-blue-100">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                        </svg>
                    </button>
                </div>

                <!-- Search and Filter (Right) -->
                <div class="flex items-center space-x-2">
                    <div class="relative">
                        <input type="text" v-model="searchQuery" placeholder="Search cases..." class="border p-2 rounded-lg pl-10">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 absolute left-3 top-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <button @click="applyFilters" class="p-2 rounded-lg bg-blue-500 text-white hover:bg-blue-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                    </button>
                </div>
            </div>
        </v-content-body>

        <!-- Cases Cards -->
        <v-content-body>
            <div v-if="filteredCases.length > 0" :class="{'grid grid-cols-1 gap-4': viewMode === 'list', 'grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4': viewMode === 'grid'}">
                <div v-for="caseItem in filteredCases" :key="caseItem.case_id" class="border p-4 rounded-lg shadow">
                    <div class="flex justify-between items-center">
                        <span class="font-semibold">CASE#{{ caseItem.case_id }}</span>
                        <span :class="`badge ${caseItem.status ? caseItem.status.toLowerCase() : 'pending'}`">
                            {{ caseItem.status || 'Pending' }}
                        </span>
                    </div>
                    <div class="mt-2">
                        <div class="text-sm text-gray-600">Client Name: {{ caseItem.patient_name }}</div>
                        <div class="text-sm text-gray-600">Type: {{ caseItem.client_type || 'Personal Injury' }}</div>
                        <div class="text-sm text-gray-600">Next Action: {{ caseItem.next_action || '12/12/2025' }}</div>
                    </div>
                    <div class="mt-4 flex justify-between items-center">
    <v-link :href="route('panel.admin.cases.show', { case: caseItem.case_id })" class="bg-info p-2 rounded-lg text-blue-500 flex items-center gap-2">
        <span class="w-5 h-5">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h7l2 2h5a2 2 0 012 2v14a2 2 0 01-2 2z" />
            </svg>
        </span>
        View Details
    </v-link>

    <v-button @click="scheduleCase(caseItem.case_id)" class="bg-green-500 text-white flex items-center gap-2">
        <span class="w-5 h-5">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 4h10a2 2 0 012 2v10a2 2 0 01-2 2H7a2 2 0 01-2-2V9a2 2 0 012-2zm3 5h4" />
            </svg>
        </span>
        Schedule
    </v-button>
</div>



                </div>
            </div>
            <div v-else class="text-center text-gray-500 py-4">
                No cases found.
            </div>
        </v-content-body>
    </div>
</template>

<script>
import Layout from "@/layouts/panel/admin/index.vue";

export default {
    layout: Layout,
    props: {
        cases: {
            type: Object,
            required: false,
            default: () => ({ data: [] }), // Ensure default is an object with data array
        },
    },
    data() {
        return {
            searchQuery: '',
            viewMode: 'list', // 'list' or 'grid'
            totalCases: 6, // Example data
            pendingCases: 6, // Example data
            completeCases: 0, // Example data
            reductionRequestSent: 0, // Example data
            wonCases: 0, // Example data
            lostCases: 0, // Example data
        };
    },
    computed: {
        filteredCases() {
            const casesData = this.cases.data || [];
            return casesData.filter(caseItem => {
                return caseItem.patient_name.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                       caseItem.case_id.toString().includes(this.searchQuery);
            });
        }
    },
    methods: {
        selectCaseStatusId(caseStatusId = undefined) {
            this.$inertia.get(route(route().current()), {
                ...this.$page.props.query,
                caseStatusId: caseStatusId,
                page: undefined,
            }, {
                preserveState: true,
                preserveScroll: true,
            });
        },
        scheduleCase(caseId) {
            console.log('Scheduling case:', caseId);
        },
        applyFilters() {
            console.log('Applying filters...');
            // Implement filter logic here
        }
    },
};
</script>

<style scoped>
.badge {
    @apply px-2 py-1 rounded-full text-xs font-semibold;
}
.badge.pending {
    @apply bg-yellow-200 text-yellow-800;
}
.badge.active {
    @apply bg-green-200 text-green-800;
}
.badge.closed {
    @apply bg-red-200 text-red-800;
}
</style>
