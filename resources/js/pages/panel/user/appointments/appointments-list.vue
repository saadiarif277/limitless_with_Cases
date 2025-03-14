<template>
    <v-inertia-head title="Appointments" />

    <div class="h-full flex flex-col">
        <v-content-body class="border-b border-gray-200">
            <v-section-heading>
                <template #title>
                    Appointments
                </template>

                <template #description>
                    Manage patient appointments across the application.
                </template>
            </v-section-heading>
        </v-content-body>

        <v-content-body>

            <div class="w-full grid grid-cols-1 gap-6">
                <v-form class="w-full">
                    <v-form-group>
                        <v-form-label>Clinics</v-form-label>
                        <div class="grid grid-cols-4">
                            <template v-for="(clinic, clinicIndex) in clinics.data" :key="'clinic_' + clinicIndex">
                                <v-form-checkbox :value="clinic.clinic_id" v-model="selected_clinic_ids" @change="updateClinicFilter">
                                    <div class="line-clamp-1">
                                        {{ clinic.name }}
                                    </div>
                                </v-form-checkbox>
                            </template>
                        </div>
                    </v-form-group>
                </v-form>
                
                <v-calendar :events="parsedEvents" @on-event-click="onEventClick" />
            </div>
        </v-content-body>

        <br />
    </div>
</template>

<script>
import Layout from "@/layouts/panel/user/index.vue";
import Calendar from "@/components/calendar.vue";

export default {
    layout: Layout,
    components: {
        "v-calendar": Calendar,
    },
    props: {
        appointments: {
            type: Object,
            required: false,
            default: () => {},
        },
        clinics: {
            type: Object,
            required: false,
            default: () => {},
        },
    },
    computed: {
        parsedEvents() {
            return this.appointments.data.map((appointment) => ({
                title: appointment.appointment_type.name,
                start: appointment.start_date,
                end: appointment.end_date,
                borderColor: appointment.appointment_type.color,
                extendedProps: {
                    appointmentId: appointment.appointment_id,
                },
            }));
        },
    },
    data() {
        return {
            selected_clinic_ids: this.$page.props.query.selected_clinic_ids || [],
        };
    },
    methods: {
        onEventClick(event) {
            this.$inertia.visit(route('panel.user.appointments.show', { appointment: event.extendedProps.appointmentId }));
        },
        updateClinicFilter() {
            this.$inertia.get(route(route().current()), {
                ...this.$page.props.query,
                selected_clinic_ids: this.selected_clinic_ids,
            }, {
                preserveState: true,
                preserveScroll: true,
                only: ['appointments']
            });
        },
    },
};
</script>
