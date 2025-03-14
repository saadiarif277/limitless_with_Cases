<template>
    <label :class="`flex items-center gap-2 py-1 border border-transparent cursor-pointer ${attrs.disabled ? 'opacity-50 cursor-not-allowed' : ''}`">
        <input type="checkbox" v-bind="attrs" v-model="value">
        <div class="text-sm text-gray-500">
            <slot />
        </div>
    </label>
</template>

<script>
export default {
    name: "FormCheckboxComponent",
    props: ["modelValue", "error"],
    emits: ["update:modelValue"],
    computed: {
        attrs() {
            return {
                ...this.$attrs,
                ...{
                    class: "v-form-checkbox focus:ring-blue-500 h-4 w-4 text-primary-500 border-gray-300 rounded",
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
