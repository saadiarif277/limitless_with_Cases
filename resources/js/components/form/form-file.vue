<template>
    <input type="file" v-bind="$attrs" @change="updateValue" ref="fileInput" />
</template>

<script>
export default {
    name: "FormFileComponent",
    props: ["modelValue"],
    emits: ['update:modelValue'],
    watch: {
        modelValue(newValue) {
            if (newValue === null || newValue === '' || newValue === undefined) {
                this.resetInput();
            }
        },
    },
    methods: {
        resetInput() {
            this.$refs.fileInput.value = '';
        },
        updateValue(event) {
            this.$emit('update:modelValue', event.target.files[0]);
        },
    },
};
</script>
