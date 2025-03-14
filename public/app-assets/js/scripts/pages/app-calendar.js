'use strict';

let direction = 'ltr';

let date = new Date();
let nextDay = new Date(new Date().getTime() + 24 * 60 * 60 * 1000);
// prettier-ignore
let nextMonth = date.getMonth() === 11 ? new Date(date.getFullYear() + 1, 0, 1) : new Date(date.getFullYear(), date.getMonth() + 1, 1);
// prettier-ignore
let prevMonth = date.getMonth() === 11 ? new Date(date.getFullYear() - 1, 0, 1) : new Date(date.getFullYear(), date.getMonth() - 1, 1);
var events = [];
document.addEventListener('DOMContentLoaded', function () {
    (function () {
        const calendarEl = document.getElementById('calendar'),
            appCalendarSidebar = document.querySelector('.app-calendar-sidebar'),
            addEventSidebar = document.getElementById('addEventSidebar'),
            appOverlay = document.querySelector('.app-overlay'),
            calendarsColor = {
                Business: 'primary',
                Holiday: 'success',
                Personal: 'danger',
                Family: 'warning',
                ETC: 'info'
            },
            offcanvasTitle = document.querySelector('.offcanvas-title'),
            btnToggleSidebar = document.querySelector('.btn-toggle-sidebar'),
            btnSubmit = document.querySelector('button[type="submit"]'),
            btnDeleteEvent = document.querySelector('.btn-delete-event'),
            btnCancel = document.querySelector('.btn-cancel'),
            title = $('#title'),
            start_date = document.querySelector('#start_date'),
            end_date = document.querySelector('#end_date'),
            clinic_id = $('#clinic_id'), // ! Using jquery vars due to select2 jQuery dependency
            patient_transaction_ids = $('#patient_transaction_ids'), // ! Using jquery vars due to select2 jQuery dependency
            //   eventLocation = document.querySelector('#eventLocation'),
            description = document.querySelector('#description'),
            //   allDaySwitch = document.querySelector('.allDay-switch'),
            selectAll = document.querySelector('.select-all'),
            filterInput = [].slice.call(document.querySelectorAll('.input-filter')),
            inlineCalendar = document.querySelector('.inline-calendar');

        let eventToUpdate,
            currentEvents = events,
            // Assign app-calendar-events.js file events (assume events from API) to currentEvents (browser store/object) to manage and update calender events
            isFormValid = false,
            inlineCalInstance;
        // Init event Offcanvas
        const bsAddEventSidebar = new bootstrap.Offcanvas(addEventSidebar);


        // Event Title (select2)
        if (title.length) {
            function renderBadges(option) {
                if (!option.id) {
                    return option.text;
                }
                var $badge = "<span class='badge badge-dot bg-" + $(option.element).data('label') + " me-2'> " + '</span>' + option.text;
                return $badge;
            }
            title.wrap('<div class="position-relative"></div>').select2({
                placeholder: 'Select value',
                dropdownParent: title.parent(),
                templateResult: renderBadges,
                templateSelection: renderBadges,
                // minimumResultsForSearch: -1,
                closeOnSelect: true,
                escapeMarkup: function (es) {
                    return es;
                }
            });
        }

        //! TODO: Update Event label and guest code to JS once select removes jQuery dependency
        // Event Label (select2)
        if (clinic_id.length) {
            function renderBadges(option) {
                if (!option.id) {
                    return option.text;
                }
                var $badge =
                    "<span class='badge badge-dot bg-" + $(option.element).data('label') + " me-2'> " + '</span>' + option.text;

                return $badge;
            }
            clinic_id.wrap('<div class="position-relative"></div>').select2({
                placeholder: 'Select value',
                dropdownParent: clinic_id.parent(),
                templateResult: renderBadges,
                templateSelection: renderBadges,
                // minimumResultsForSearch: -1,
                closeOnSelect: true,
                escapeMarkup: function (es) {
                    return es;
                }
            });
        }

        // Event Guests (select2)
        if (patient_transaction_ids.length) {
            function renderGuestAvatar(option) {
                if (!option.id) {
                    return option.text;
                }
                var $avatar = "<span class='badge badge-dot bg-warning me-2'> " + '</span>' + option.text;
                return $avatar;
            }
            patient_transaction_ids.wrap('<div class="position-relative"></div>').select2({
                placeholder: 'Select value',
                dropdownParent: patient_transaction_ids.parent(),
                closeOnSelect: true,
                templateResult: renderGuestAvatar,
                templateSelection: renderGuestAvatar,
                escapeMarkup: function (es) {
                    return es;
                }
            });
        }

        // Event start (flatpicker)
        if (start_date) {
            var start = start_date.flatpickr({
                enableTime: true,
                altFormat: 'Y-m-dTH:i:S',
                onReady: function (selectedDates, dateStr, instance) {
                    if (instance.isMobile) {
                        instance.mobileInput.setAttribute('step', null);
                    }
                }
            });
        }

        // Event end (flatpicker)
        if (end_date) {
            var end = end_date.flatpickr({
                enableTime: true,
                altFormat: 'Y-m-dTH:i:S',
                onReady: function (selectedDates, dateStr, instance) {
                    if (instance.isMobile) {
                        instance.mobileInput.setAttribute('step', null);
                    }
                }
            });
        }

        // Inline sidebar calendar (flatpicker)
        if (inlineCalendar) {
            inlineCalInstance = inlineCalendar.flatpickr({
                monthSelectorType: 'static',
                inline: true
            });
        }

        // Event click function
        function eventClick(info) {
            eventToUpdate = info.event;
            bsAddEventSidebar.show();
            // For update event set offcanvas title text: Update Event
            if (offcanvasTitle) {
                offcanvasTitle.innerHTML = 'Update Schedule';
                $('#action_type').val('update');
                $('#id').val(eventToUpdate.id).trigger('change');
            }
            if (btnSubmit) {
                btnSubmit.innerHTML = 'Update';
                btnSubmit.classList.add('btn-update-event');
                btnSubmit.classList.remove('btn-add-event');
                btnDeleteEvent.classList.remove('d-none');
            }


            title.val(eventToUpdate.title).trigger('change');
            start.setDate(eventToUpdate.start, true, 'Y-m-d');
            eventToUpdate.end !== null
                ? end.setDate(eventToUpdate.end, true, 'Y-m-d')
                : end.setDate(eventToUpdate.start, true, 'Y-m-d');

            clinic_id.val(eventToUpdate.extendedProps.clinic_id).trigger('change');
            //   eventToUpdate.extendedProps.location !== undefined? (eventLocation.value = eventToUpdate.extendedProps.location): null;
            eventToUpdate.extendedProps.patient_transaction_ids !== undefined
                ? patient_transaction_ids.val(eventToUpdate.extendedProps.patient_transaction_ids).trigger('change')
                : null;
            eventToUpdate.extendedProps.description !== undefined
                ? (description.value = eventToUpdate.extendedProps.description)
                : null;
        }

        // Modify sidebar toggler
        function modifyToggler() {
            const fcSidebarToggleButton = document.querySelector('.fc-sidebarToggle-button');
            fcSidebarToggleButton.classList.remove('fc-button-primary');
            fcSidebarToggleButton.classList.add('d-lg-none', 'd-inline-block', 'ps-0');
            while (fcSidebarToggleButton.firstChild) {
                fcSidebarToggleButton.firstChild.remove();
            }
            fcSidebarToggleButton.setAttribute('data-bs-toggle', 'sidebar');
            fcSidebarToggleButton.setAttribute('data-overlay', '');
            fcSidebarToggleButton.setAttribute('data-target', '#app-calendar-sidebar');
            fcSidebarToggleButton.insertAdjacentHTML('beforeend', '<i class="ti ti-menu-2 ti-sm text-heading"></i>');
        }

        // Filter events by calender
        function selectedCalendars() {
            let selected = [],
                filterInputChecked = [].slice.call(document.querySelectorAll('.input-filter:checked'));

            filterInputChecked.forEach(item => {
                selected.push(item.getAttribute('data-value'));
            });

            return selected;
        }

        // --------------------------------------------------------------------------------------------------
        // AXIOS: fetchEvents
        // * This will be called by fullCalendar to fetch events. Also this can be used to refetch events.
        // --------------------------------------------------------------------------------------------------
        function fetchEvents(info, successCallback) {
            // Fetch Events from API endpoint reference
            /* $.ajax(
              {
                url: '../../../app-assets/data/app-calendar-events.js',
                type: 'GET',
                success: function (result) {
                  // Get requested calendars as Array
                  var calendars = selectedCalendars();

                  return [result.events.filter(event => calendars.includes(event.extendedProps.calendar))];
                },
                error: function (error) {
                  console.log(error);
                }
              }
            ); */

            let calendars = selectedCalendars();
            // We are reading event object from app-calendar-events.js file directly by including that file above app-calendar file.
            // You should make an API call, look into above commented API call for reference
            let selectedEvents = currentEvents.filter(function (event) {
                // console.log(event.extendedProps.calendar.toLowerCase());
                return calendars.includes(event.clinic_id + '');
            });
            console.log('***** selectedEvents ******', selectedEvents);
            let new_events = [];
            selectedEvents.map(em => {
                let eventData = {
                    id: em.id,
                    title: em.title,
                    start: em.start_date,
                    end: em.end_date,
                    extendedProps: {
                        patient_transaction_ids: em.patient_id + '_' + em.patient_transaction_id,
                        clinic_id: em.clinic_id,
                        description: em.description
                    },
                    display: 'block',
                };
                new_events.push(eventData);
            });

            // schedule_data.map((item) => {
            //     events.push({
            //             id: item.id,
            //             title: item.title,
            //             start: new Date(item.start_date),
            //             end: new Date(item.end_date),
            //             extendedProps: {
            //                 description:item.description,
            //                 guests:item.patient_id + '_' + item.patient_transaction_id,
            //                 clinic_id: em.clinic_id,
            //                 patient_id: em.patient_id,
            //             }
            //         });
            // });

            console.log('***** new_events ******', new_events);
            // if (selectedEvents.length > 0) {
            successCallback(new_events);
            // }
        }

        // Init FullCalendar
        // ------------------------------------------------
        let calendar = new Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            events: fetchEvents,
            plugins: [dayGridPlugin, interactionPlugin, listPlugin, timegridPlugin],
            editable: true,
            dragScroll: true,
            dayMaxEvents: 2,
            eventResizableFromStart: true,
            customButtons: {
                sidebarToggle: {
                    text: 'Sidebar'
                }
            },
            headerToolbar: {
                start: 'sidebarToggle, prev,next, title',
                end: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth'
            },
            direction: direction,
            initialDate: new Date(),
            navLinks: true, // can click day/week names to navigate views
            eventClassNames: function ({ event: calendarEvent }) {
                // Background Color
                return ['fc-event-danger'];
            },
            dateClick: function (info) {
                let date = moment(info.date).format('YYYY-MM-DD');
                resetValues();
                bsAddEventSidebar.show();

                // For new event set offcanvas title text: Add Event
                if (offcanvasTitle) {
                    offcanvasTitle.innerHTML = 'Add Schedule';
                    $('#action_type').val('create');
                    $('#id').val('0').trigger('change');
                }
                if (btnSubmit) {
                    btnSubmit.innerHTML = 'Add';
                    btnSubmit.classList.remove('btn-update-event');
                    btnSubmit.classList.add('btn-add-event');
                    btnDeleteEvent.classList.add('d-none');
                }
                start_date.value = date;
                end_date.value = date;
            },
            eventClick: function (info) {
                eventClick(info);
            },
            datesSet: function () {
                modifyToggler();
            },
            viewDidMount: function () {
                modifyToggler();
            },
            validRange: function (nowDate) {
                return {
                    start: '1970-01-01',
                    end: '9999-12-31'
                }
            },
            // eventDidMount: function (arg) {
            //     var patient_key = arg.event.extendedProps.guests;
            //     var patient_element = '';
            //     if (!isNaN(parseInt(patient_key)) && patient_object[patient_key] != undefined) {
            //         patient_element = '<div><li class="event-patient">' + patient_object[patient_key] + '</li></div>';
            //     }
            //     if ($(arg.el).hasClass('fc-daygrid-dot-event')) {
            //         $(arg.el).css('display', 'block');
            //         var new_sub_parent = $('<div style="display:flex">');
            //         $(arg.el).children().appendTo(new_sub_parent);
            //         new_sub_parent.appendTo($(arg.el));
            //         $(arg.el).append(patient_element);
            //     } else {
            //         $(arg.el).append(patient_element);
            //     }

            // }
        });

        // Render calendar
        calendar.render();
        // Modify sidebar toggler
        modifyToggler();

        const eventForm = document.getElementById('eventForm');
        const fv = FormValidation.formValidation(eventForm, {
            fields: {
                title: {
                    validators: {
                        notEmpty: {
                            message: 'Please enter event title '
                        }
                    }
                },
                start_date: {
                    validators: {
                        notEmpty: {
                            message: 'Please enter start date '
                        }
                    }
                },
                end_date: {
                    validators: {
                        notEmpty: {
                            message: 'Please enter end date '
                        }
                    }
                }
            },
            plugins: {
                trigger: new FormValidation.plugins.Trigger(),
                bootstrap5: new FormValidation.plugins.Bootstrap5({
                    // Use this for enabling/changing valid/invalid class
                    eleValidClass: '',
                    rowSelector: function (field, ele) {
                        // field is the field name & ele is the field element
                        return '.mb-3';
                    }
                }),
                submitButton: new FormValidation.plugins.SubmitButton(),
                // Submit the form when all fields are valid
                // defaultSubmit: new FormValidation.plugins.DefaultSubmit(),
                autoFocus: new FormValidation.plugins.AutoFocus()
            }
        })
            .on('core.form.valid', function () {
                // Jump to the next step when all fields in the current step are valid
                isFormValid = true;
            })
            .on('core.form.invalid', function () {
                // if fields are invalid
                isFormValid = false;
            });

        // Sidebar Toggle Btn
        if (btnToggleSidebar) {
            btnToggleSidebar.addEventListener('click', e => {
                btnCancel.classList.remove('d-none');
            });
        }

        // Add Event
        // ------------------------------------------------
        function addEvent() {
            // ? Add new event data to current events object and refetch it to display on calender
            // ? You can write below code to AJAX call success response
            calendar.refetchEvents();

            // ? To add event directly to calender (won't update currentEvents object)
            // calendar.addEvent(eventData);
        }

        // Update Event
        // ------------------------------------------------
        function updateEvent(eventData) {
            // ? Update existing event data to current events object and refetch it to display on calender
            // ? You can write below code to AJAX call success response
            eventData.id = parseInt(eventData.id);
            currentEvents[currentEvents.findIndex(el => el.id === eventData.id)] = eventData; // Update event by id
            calendar.refetchEvents();

            // ? To update event directly to calender (won't update currentEvents object)
            // let propsToUpdate = ['id', 'title', 'url'];
            // let extendedPropsToUpdate = ['calendar', 'guests', 'location', 'description'];

            // updateEventInCalendar(eventData, propsToUpdate, extendedPropsToUpdate);
        }

        // Remove Event
        // ------------------------------------------------

        function removeEvent(eventId) {
            // ? Delete existing event data to current events object and refetch it to display on calender
            // ? You can write below code to AJAX call success response
            currentEvents = currentEvents.filter(function (event) {
                return event.id != eventId;
            });
            calendar.refetchEvents();

            // ? To delete event directly to calender (won't update currentEvents object)
            // removeEventInCalendar(eventId);
        }

        // (Update Event In Calendar (UI Only)
        // ------------------------------------------------
        const updateEventInCalendar = (updatedEventData, propsToUpdate, extendedPropsToUpdate) => {
            const existingEvent = calendar.getEventById(updatedEventData.id);

            // --- Set event properties except date related ----- //
            // ? Docs: https://fullcalendar.io/docs/Event-setProp
            // dateRelatedProps => ['start', 'end', 'allDay']
            // eslint-disable-next-line no-plusplus
            for (var index = 0; index < propsToUpdate.length; index++) {
                var propName = propsToUpdate[index];
                existingEvent.setProp(propName, updatedEventData[propName]);
            }

            // --- Set date related props ----- //
            // ? Docs: https://fullcalendar.io/docs/Event-setDates
            existingEvent.setDates(updatedEventData.start, updatedEventData.end, {
                allDay: updatedEventData.allDay
            });

            // --- Set event's extendedProps ----- //
            // ? Docs: https://fullcalendar.io/docs/Event-setExtendedProp
            // eslint-disable-next-line no-plusplus
            for (var index = 0; index < extendedPropsToUpdate.length; index++) {
                var propName = extendedPropsToUpdate[index];
                existingEvent.setExtendedProp(propName, updatedEventData.extendedProps[propName]);
            }
        };

        // Remove Event In Calendar (UI Only)
        // ------------------------------------------------
        function removeEventInCalendar(eventId) {
            calendar.getEventById(eventId).remove();
        }

        // Add new event
        // ------------------------------------------------
        if (btnSubmit) {
            btnSubmit.addEventListener('click', e => {
                if (btnSubmit.classList.contains('btn-add-event')) {
                    if (isFormValid) {
                        addEvent();
                        bsAddEventSidebar.hide();
                    }
                }
            });
        }

        if (btnDeleteEvent) {
            btnDeleteEvent.addEventListener('click', e => {
                $('#action_type').val('delete').trigger('change');
                bsAddEventSidebar.hide();
            });

        }

        // Reset event form inputs values
        // ------------------------------------------------
        function resetValues() {
            end_date.value = '';
            start_date.value = '';

            title.val('').trigger('change');
            clinic_id.val('').trigger('change');
            patient_transaction_ids.val('').trigger('change');

            description.value = '';
        }

        // When modal hides reset input values
        addEventSidebar.addEventListener('hidden.bs.offcanvas', function () {
            resetValues();
        });

        // Hide left sidebar if the right sidebar is open
        btnToggleSidebar.addEventListener('click', e => {
            if (offcanvasTitle) {
                offcanvasTitle.innerHTML = 'Add Schedule';
            }
            if (btnSubmit) {
                btnSubmit.innerHTML = 'Add';
                btnSubmit.classList.remove('btn-update-event');
                btnSubmit.classList.add('btn-add-event');
            }
            if (btnDeleteEvent) {
                btnDeleteEvent.classList.add('d-none');
            }
            appCalendarSidebar.classList.remove('show');
            appOverlay.classList.remove('show');
        });

        // Calender filter functionality
        // ------------------------------------------------
        if (selectAll) {
            selectAll.addEventListener('click', e => {
                if (e.currentTarget.checked) {
                    document.querySelectorAll('.input-filter').forEach(c => (c.checked = 1));
                } else {
                    document.querySelectorAll('.input-filter').forEach(c => (c.checked = 0));
                }
                calendar.refetchEvents();
            });
        }

        if (filterInput) {
            filterInput.forEach(item => {
                item.addEventListener('click', () => {
                    document.querySelectorAll('.input-filter:checked').length < document.querySelectorAll('.input-filter').length
                        ? (selectAll.checked = false)
                        : (selectAll.checked = true);
                    calendar.refetchEvents();
                });
            });
        }

        // Jump to date on sidebar(inline) calendar change
        inlineCalInstance.config.onChange.push(function (date) {
            calendar.changeView(calendar.view.type, moment(date[0]).format('YYYY-MM-DD'));
            modifyToggler();
            appCalendarSidebar.classList.remove('show');
            appOverlay.classList.remove('show');
        });
    })();
});
