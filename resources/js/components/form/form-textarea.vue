<template>
    <textarea v-bind="attrs" v-model="value" rows="5"></textarea>
</template>

<script>
export default {
    name: "FormTextareaComponent",
    props: ["modelValue"],
    emits: ["update:modelValue"],
    computed: {
        attrs() {
            return {
                ...this.$attrs,
                ...{
                    class: "v-form-textarea block w-full focus:ring-primary-500 focus:border-primary-500 border-gray-200 rounded-md disabled:opacity-50 disabled:cursor-not-allowed",
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