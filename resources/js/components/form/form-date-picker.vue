<template>
    <x-flat-pickr :model-value="modelValue" @update:model-value="updateValue" />
</template>

<script>
import FlatPickr from "vue-flatpickr-component";
import "flatpickr/dist/flatpickr.css";

export default {
    name: "FormDatePickerComponent",
    components: {
        "x-flat-pickr": FlatPickr,
    },
    props: {
        modelValue: String
    },
    emits: [
        "update:modelValue"
    ],
    methods: {
        updateValue(value) {
            this.$emit("update:modelValue", value);
        }
    },
};
</script>
