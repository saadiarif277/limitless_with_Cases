<template>
    <div class="grid grid-cols-2 rounded-md overflow-hidden">
        <template v-for="(tab, tabIndex) in tabs" :key="'tab_' + tabIndex">
            <div 
                @click.stop="selectTab(tab.slug)"
                :class="`${baseClasses} ${(tab.slug !== selectedTab)
                    ? 'text-gray-500 bg-gray-100 hover:bg-gray-200 border-gray-100 hover:border-gray-200'
                    : 'text-gray-100 bg-secondary-500 hover:bg-secondary-600 border-secondary-500 hover:border-secondary-600'}`">
                {{ tab.title }}
            </div>
        </template>
    </div>
</template>

<script>
export default {
    name: "TabsComponent",
    props: {
        tabs: {
            type: Array,
            default: () => [],
        },
        modelValue: { // Using modelValue as per Vue 3 convention
            type: String,
            default: ''
        },
    },
    emits: ['update:modelValue'], // Explicitly declaring the event
    data() {
        return {
            baseClasses: "text-center px-6 py-2.5 font-medium text-sm uppercase cursor-pointer border-2",
            selectedTab: this.modelValue || (this.tabs[0] ? this.tabs[0].slug : null),
        };
    },
    watch: {
        modelValue(value) {
            this.selectedTab = value;
        },
        selectedTab(value) {
            this.$emit('update:modelValue', value); // Emitting the update:modelValue event
        }
    },
    methods: {
        selectTab(tab) {
            this.selectedTab = tab;
        },
    },
};
</script>
