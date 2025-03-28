<template>
    <v-inertia-head title="Cases Management" />

    <div class="h-full space-y-6">
        <!-- Metrics Section -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Cards</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-4">
                <div class="bg-white rounded-lg shadow-sm p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Total Cases</p>
                            <p class="text-2xl font-semibold text-gray-800">{{ totalCases }}</p>
                        </div>
                        <div class="bg-primary-100 p-2 rounded-full">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Pending Cases</p>
                            <p class="text-2xl font-semibold text-gray-800">{{ pendingCases }}</p>
                        </div>
                        <div class="bg-yellow-100 p-2 rounded-full">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Complete Cases</p>
                            <p class="text-2xl font-semibold text-gray-800">{{ completeCases }}</p>
                        </div>
                        <div class="bg-green-100 p-2 rounded-full">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Reduction Requests Sent</p>
                            <p class="text-2xl font-semibold text-gray-800">{{ reductionRequestsSent }}</p>
                        </div>
                        <div class="bg-blue-100 p-2 rounded-full">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Won Cases</p>
                            <p class="text-2xl font-semibold text-gray-800">{{ wonCases }}</p>
                        </div>
                        <div class="bg-green-100 p-2 rounded-full">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Lost Cases</p>
                            <p class="text-2xl font-semibold text-gray-800">{{ lostCases }}</p>
                        </div>
                        <div class="bg-red-100 p-2 rounded-full">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cases List Section -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                <div class="flex items-center gap-4">
                    <h2 class="text-lg font-semibold text-gray-800">Cases List</h2>
                    <!-- Create Case Button -->
                    <v-button
                        v-if="canCreateCase"
                        @click="createCase"
                        color="primary"
                        class="flex items-center gap-2"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Create Case
                    </v-button>
                </div>
                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-2">
                        <button
                            @click="viewMode = 'list'"
                            :class="[
                                'p-2 rounded-md',
                                viewMode === 'list'
                                    ? 'bg-primary-100 text-primary-600'
                                    : 'text-gray-600 hover:bg-gray-100'
                            ]"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                        <button
                            @click="viewMode = 'grid'"
                            :class="[
                                'p-2 rounded-md',
                                viewMode === 'grid'
                                    ? 'bg-primary-100 text-primary-600'
                                    : 'text-gray-600 hover:bg-gray-100'
                            ]"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                        </svg>
                    </button>
                </div>
                    <div class="relative">
                        <input
                            type="text"
                            v-model="searchQuery"
                            placeholder="Search cases..."
                            class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                        />
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 absolute left-3 top-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <v-button
                        @click="openFilterModal"
                        color="secondary"
                        class="flex items-center gap-2"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        Filter
                    </v-button>
                </div>
            </div>

            <!-- Cases Grid/List -->
            <div :class="[
                viewMode === 'grid' ? 'grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4' : 'space-y-4'
            ]">
                <div
                    v-for="caseItem in filteredCases"
                    :key="caseItem.case_id"
                    :class="[
                        viewMode === 'grid'
                            ? 'bg-white rounded-lg shadow-sm p-4 border border-gray-200'
                            : 'bg-white rounded-lg shadow-sm p-4 border border-gray-200 flex items-center justify-between'
                    ]"
                >
                    <!-- Case ID and Status -->
                    <div :class="[viewMode === 'grid' ? 'mb-4' : 'flex items-center gap-4']">
                        <div>
                            <p class="text-sm text-gray-600">Case ID</p>
                            <p class="font-medium text-gray-800">#{{ caseItem.case_id }}</p>
                        </div>
                        <div class="mt-2 flex gap-2">
                            <span :class="getStatusBadgeClass(caseItem.status)">
                                {{ getStatusText(caseItem.status) }}
                            </span>
                        </div>
                    </div>

                    <!-- Client Name -->
                    <div :class="[viewMode === 'grid' ? 'mb-4' : 'flex-1']">
                        <p class="text-sm text-gray-600">Client Name</p>
                        <p class="font-medium text-gray-800">{{ caseItem.patient?.name || 'N/A' }}</p>
                    </div>

                    <!-- Case Type -->
                    <div :class="[viewMode === 'grid' ? 'mb-4' : 'flex-1']">
                        <p class="text-sm text-gray-600">Case Type</p>
                        <span :class="getBillingTypeBadgeClass(caseItem.billing_type)">
                            {{ caseItem.billing_type || 'N/A' }}
                        </span>
                    </div>

                    <!-- Last Update -->
                    <div :class="[viewMode === 'grid' ? 'mb-4' : 'flex-1']">
                        <p class="text-sm text-gray-600">Last Update</p>
                        <p class="font-medium text-gray-800">{{ formatDate(caseItem.updated_at) || 'N/A' }}</p>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center gap-2">
                        <v-button
                            @click="viewCase(caseItem.case_id)"
                            color="primary"
                            class="flex items-center gap-2"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
                            View
                        </v-button>
                        <v-button
                            @click="scheduleCase(caseItem.case_id)"
                            color="secondary"
                            class="flex items-center gap-2"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
        Schedule
    </v-button>
</div>
                </div>
            </div>
            </div>
    </div>
</template>

<script>
import Layout from "@/layouts/panel/user/index.vue";

export default {
    layout: Layout,
    props: {
        cases: {
            type: Array,
            required: true,
        },
        userRole: {
            type: String,
            required: true,
        },
        userId: {
            type: Number,
            required: true,
        },
        canCreateCase: {
            type: Boolean,
            required: true,
        },
    },
    data() {
        return {
            viewMode: 'list',
            searchQuery: '',
        };
    },
    computed: {
        totalCases() {
            return this.cases.length;
        },
        pendingCases() {
            return this.cases.filter(caseItem => caseItem.status === 'pending').length;
        },
        completeCases() {
            return this.cases.filter(caseItem => caseItem.status === 'complete').length;
        },
        reductionRequestsSent() {
            return this.cases.filter(caseItem => caseItem.status === 'reduction_request_sent').length;
        },
        wonCases() {
            return this.cases.filter(caseItem => caseItem.status === 'won').length;
        },
        lostCases() {
            return this.cases.filter(caseItem => caseItem.status === 'lost').length;
        },
        filteredCases() {
            return this.cases.filter(caseItem => {
                const searchLower = this.searchQuery.toLowerCase();
                return (
                    caseItem.case_id.toString().includes(searchLower) ||
                    caseItem.patient?.name?.toLowerCase().includes(searchLower) ||
                    caseItem.case_type?.toLowerCase().includes(searchLower)
                );
            });
        },
    },
    methods: {
        getStatusBadgeClass(status) {
            switch (status) {
                case 'pending':
                    return 'bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full text-sm';
                case 'complete':
                    return 'bg-green-100 text-green-800 px-2 py-1 rounded-full text-sm';
                case 'reduction_request_sent':
                    return 'bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-sm';
                case 'won':
                    return 'bg-green-100 text-green-800 px-2 py-1 rounded-full text-sm';
                case 'lost':
                    return 'bg-red-100 text-red-800 px-2 py-1 rounded-full text-sm';
                default:
                    return 'bg-gray-100 text-gray-800 px-2 py-1 rounded-full text-sm';
            }
        },
        getStatusText(status) {
            switch (status) {
                case 'pending':
                    return 'Pending';
                case 'complete':
                    return 'Complete';
                case 'reduction_request_sent':
                    return 'Reduction Request Sent';
                case 'won':
                    return 'Won';
                case 'lost':
                    return 'Lost';
                default:
                    return 'Unknown';
            }
        },
        viewCase(caseId) {
            this.$inertia.visit(route('panel.user.cases.show', { case: caseId }));
        },
        scheduleCase(caseId) {
            // Implement scheduling functionality
            this.$toast().info('Scheduling functionality coming soon!');
        },
        openFilterModal() {
            // Implement filter modal functionality
            this.$toast().info('Filter functionality coming soon!');
        },
        getBillingTypeBadgeClass(billingType) {
            switch (billingType) {
                case 'Insurance':
                    return 'bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-sm';
                case 'LOP':
                    return 'bg-purple-100 text-purple-800 px-2 py-1 rounded-full text-sm';
                default:
                    return 'bg-gray-100 text-gray-800 px-2 py-1 rounded-full text-sm';
            }
        },
        formatDate(dateString) {
            if (!dateString) return 'N/A';
            const date = new Date(dateString);
            if (isNaN(date)) return 'Invalid Date';
            return new Intl.DateTimeFormat('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            }).format(date);
        },
        createCase() {
            this.$inertia.visit(route('panel.user.cases.create'));
        },
    },
};
</script>

<style scoped>
.bg-white {
    background-color: white;
}
.shadow-sm {
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
}
.rounded-lg {
    border-radius: 0.5rem;
}
.p-4 {
    padding: 1rem;
}
.mb-4 {
    margin-bottom: 1rem;
}
.gap-4 {
    gap: 1rem;
}
.bg-gray-50 {
    background-color: #f9fafb;
}
</style>
