<template>
    <div class="v-section-heading w-full flex flex-col sm:flex-row gap-2 items-center justify-between">
        <div :class="`w-full ${$slots.description ? 'py-0.5' : '' }`">
            <v-heading :size="6" v-if="$slots.title" class="font-medium">
                <slot name="title" />
            </v-heading>

            <v-paragraph v-if="$slots.description">
                <slot name="description" />
            </v-paragraph>
        </div>

        <template v-if="$slots.actions">
            <div class="w-full sm:w-auto flex-shrink-0">
                <slot name="actions" />
            </div>
        </template>
    </div>
</template>

<script>
export default {
    name: "SectionHeadingComponent",
};
</script>
