<template>
    <input v-bind="attrs" v-model="value">
</template>

<script>
export default {
    name: "FormInputComponent",
    props: ["modelValue", "error"],
    emits: ["update:modelValue"],
    computed: {
        attrs() {
            return {
                ...this.$attrs,
                ...{
                    class: `v-form-input block w-full py-2.5 text-sm ${this.error ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : 'focus:ring-primary-500 focus:border-primary-500'} border-gray-200 rounded-md disabled:opacity-50 disabled:cursor-not-allowed`,
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
        }
    },
};
</script>
