<template>
    <!-- Original select for comparison
    <div class="mb-4">
        <select v-model="value" v-bind="attrs" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
            <template v-for="(option, optionIndex) in options" :key="'option_' + optionIndex">
                <option :value="option.value">
                    {{ option.label }}
                </option>
            </template>
        </select>
    </div>
     -->

    <Combobox as="div" v-model="value" :multiple="$attrs.multiple">
        <div class="relative">
            <template v-if="$attrs.multiple">
                <ComboboxInput class="w-full rounded-md border border-gray-200 bg-white py-2.5 pl-3 pr-10 text-gray-900 ring-0 ring-inset ring-gray-300 focus:ring-1 focus:ring-inset focus:ring-primary-500 text-sm" @change="query = $event.target.value" placeholder="Search ..." />
            </template>

            <template v-else>
                <ComboboxInput class="w-full rounded-md border border-gray-200 bg-white py-2.5 pl-3 pr-10 text-gray-900 ring-0 ring-inset ring-gray-300 focus:ring-1 focus:ring-inset focus:ring-primary-500 text-sm" @change="query = $event.target.value" :display-value="(optionValue) => options.find((option) => (optionValue == option.value))?.label" placeholder="Search ..." />
            </template>

            <ComboboxButton class="absolute inset-y-0 right-0 flex items-center rounded-r-md px-2 focus:outline-none">
                <!-- HeroIcons: chevron-up-down -->
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5 text-gray-400">
                    <path fill-rule="evenodd" d="M11.47 4.72a.75.75 0 0 1 1.06 0l3.75 3.75a.75.75 0 0 1-1.06 1.06L12 6.31 8.78 9.53a.75.75 0 0 1-1.06-1.06l3.75-3.75Zm-3.75 9.75a.75.75 0 0 1 1.06 0L12 17.69l3.22-3.22a.75.75 0 1 1 1.06 1.06l-3.75 3.75a.75.75 0 0 1-1.06 0l-3.75-3.75a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                </svg>
            </ComboboxButton>

            <ComboboxOptions v-if="filteredOptions.length > 0" class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm">
                <ComboboxOption v-for="(option, optionIndex) in filteredOptions" :key="'option_' + optionIndex" :value="option.value" as="template" v-slot="{ active, selected }">
                    <li :class="['relative cursor-default select-none py-2 pl-3 pr-9', active ? 'bg-primary-500 text-white' : 'text-gray-900']">
                        <span :class="['block truncate', selected && 'font-semibold']">
                            {{ option.label }}
                        </span>

                        <span v-if="selected" :class="['absolute inset-y-0 right-0 flex items-center pr-4', active ? 'text-white' : 'text-primary-500']">
                            <!-- HeroIcons: check-circle -->
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                                <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm13.36-1.814a.75.75 0 1 0-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.14-.094l3.75-5.25Z" clip-rule="evenodd" />
                            </svg>
                        </span>
                    </li>
                </ComboboxOption>
            </ComboboxOptions>
        </div>

        <template v-if="$attrs.multiple && selectedOptions">
            <div class="flex flex-wrap gap-2 mt-4">
                <template v-for="(selectedOption, selectedOptionIndex) in selectedOptions" :key="'selectedOption_' + selectedOptionIndex">
                    <a href="#"  @click.stop="value.splice(value.indexOf(selectedOption.value), 1)">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 hover:bg-gray-300 cursor-pointer">
                            {{ selectedOption.label }}

                            <span class="text-red-500 ml-1 text-base">
                                &times;
                            </span>
                        </span>
                    </a>
                </template>
            </div>
        </template>

    </Combobox>
</template>

<script>
import {
  Combobox,
  ComboboxButton,
  ComboboxInput,
  ComboboxLabel,
  ComboboxOption,
  ComboboxOptions,
} from "@headlessui/vue";

export default {
    name: "FormSelectComponent",
    props: [
        "modelValue",
        "options",
    ],
    emits: ["update:modelValue"],
    components: {
        Combobox,
        ComboboxButton,
        ComboboxInput,
        ComboboxLabel,
        ComboboxOption,
        ComboboxOptions,
    },
    data() {
        return {
            query: "",
        };
    },
    computed: {
        attrs() {
            return {
                ...this.$attrs,
                ...{
                    class: "bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5",
                },
            };
        },
        value: {
            get() {
                return this.modelValue;
            },
            set(value) {
                this.$emit("update:modelValue", value);
            }
        },
        filteredOptions() {
            return this.options
                .filter((option) => (option.label.toLowerCase()).includes(this.query.toLowerCase()));
        },
        selectedOption() {
            if (Array.isArray(this.value)) {
                return null;
            }

            return this.options
                .filter((option) => (option.value == this.value));
        },
        selectedOptions() {
            if (!Array.isArray(this.value)) {
                return [];
            }

            return this.options
                .filter((option) => (this.value).includes(option.value));
        },
    },
    watch: {
        value: {
            handler() {
                if (this.$attrs.multiple) {
                    this.query = "";
                }
            },
            immediate: true,
            deep: true,
        },
    },
    methods: {
        setValue(inputValue) {
            console.log(inputValue);
        },
    },
}
</script>
