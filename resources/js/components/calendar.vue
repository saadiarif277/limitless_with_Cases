<template>
    <x-full-calendar :options="calendarOptions" />
</template>

<script>
import FullCalendar from '@fullcalendar/vue3'
import dayGridPlugin from '@fullcalendar/daygrid'
import interactionPlugin from '@fullcalendar/interaction'

export default {
    name: "CalendarComponent",
    components: {
        "x-full-calendar": FullCalendar,
    },
    props: {
        events: {
            type: Array,
            required: false,
            default: () => [],
        },
    },
    emits: [
        "onEventClick"
    ],
    computed: {
        calendarOptions() {
            return {
                height: 650,
                plugins: [dayGridPlugin, interactionPlugin],
                initialView: 'dayGridMonth',
                events: this.events,
                eventClick: ({ event }) => {
                    this.$emit("onEventClick", event);
                },
            };
        },
    },
};
</script>
