document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('calendar');
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');

    let initialView = window.innerWidth < 576 ? 'listWeek' : 'dayGridMonth';

    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: initialView,
        locale: 'pl',
        height: 550,
        firstDay: 1,
        headerToolbar: {
            left: 'title',
            right: 'prevYear,prev,next,nextYear today resetDates',
        },
        customButtons: {
            prevYear: { text: '<<', click: () => calendar.incrementDate({ years: -1 }) },
            nextYear: { text: '>>', click: () => calendar.incrementDate({ years: 1 }) },
            resetDates: {
                text: 'Wyczyść daty',
                click: function () {
                    if (startDateInput) startDateInput.value = '';
                    if (endDateInput) endDateInput.value = '';

                    triggerChangeEvent(startDateInput);
                    triggerChangeEvent(endDateInput);

                    highlightSelectedRange();
                }
            }
        },
        events: window.calendarUrl,
        eventColor: '#dc3545',
        displayEventTime: false,
        eventContent: function (arg) {
            return {
                html: `<div style="font-size:0.75rem"><strong>${arg.event.title}</strong></div>`
            };
        },
        eventDidMount: function (info) {
            if (info.event.extendedProps?.room) {
                info.el.setAttribute(
                    'title',
                    `Sala: ${info.event.extendedProps.room}\nGodzina: ${info.event.extendedProps.time}`
                );
            }
        },
        dateClick: function (info) {
            handleDateSelection(info.dateStr);
        },
        datesSet: function () {
            highlightSelectedRange();
        }
    });

    calendar.render();

    function triggerChangeEvent(element) {
        if (element) {
            element.dispatchEvent(new Event('change'));
        }
    }

    if (startDateInput) {
        startDateInput.addEventListener('change', function () {
            if (endDateInput.value && endDateInput.value < this.value) {
                endDateInput.value = this.value;
                triggerChangeEvent(endDateInput);
            }
            if (this.value) calendar.gotoDate(this.value);
            highlightSelectedRange();
        });
    }

    if (endDateInput) {
        endDateInput.addEventListener('change', function () {
            if (startDateInput.value && startDateInput.value > this.value) {
                startDateInput.value = this.value;
                triggerChangeEvent(startDateInput);
            }
            highlightSelectedRange();
        });
    }

    function handleDateSelection(dateStr) {
        if (!startDateInput.value) {
            startDateInput.value = dateStr;
            endDateInput.value = dateStr;
        } else if (dateStr < startDateInput.value) {
            startDateInput.value = dateStr;
            endDateInput.value = dateStr;
        } else if (dateStr > endDateInput.value) {
            endDateInput.value = dateStr;
        } else {
            startDateInput.value = dateStr;
            endDateInput.value = dateStr;
        }

        triggerChangeEvent(startDateInput);
        triggerChangeEvent(endDateInput);

        highlightSelectedRange();
    }

    function highlightSelectedRange() {
        document.querySelectorAll('.fc-daygrid-day').forEach(el => {
            el.classList.remove('selected-range');
        });

        if (!startDateInput.value || !endDateInput.value) return;

        let start = new Date(startDateInput.value);
        let end = new Date(endDateInput.value);
        start.setHours(0,0,0,0);
        end.setHours(0,0,0,0);

        document.querySelectorAll('.fc-daygrid-day').forEach(el => {
            let cellDate = new Date(el.dataset.date);
            cellDate.setHours(0,0,0,0);
            if (cellDate >= start && cellDate <= end) {
                el.classList.add('selected-range');
            }
        });
    }
});
