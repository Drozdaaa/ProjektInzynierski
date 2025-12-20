document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('calendar');
    if (!calendarEl) return;

    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');
    const singleDateInput = document.getElementById('date');

    const isRangeMode = !!(startDateInput && endDateInput);
    const isSingleMode = !!singleDateInput;

    let initialView = window.innerWidth < 576 ? 'listWeek' : 'dayGridMonth';

    let initialDate = new Date();
    if (isSingleMode && singleDateInput.value) {
        initialDate = singleDateInput.value;
    } else if (isRangeMode && startDateInput.value) {
        initialDate = startDateInput.value;
    }

    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: initialView,
        initialDate: initialDate,

        locale: 'pl',
        firstDay: 1,

        height: 'auto',
        expandRows: true,

        headerToolbar: {
            left: 'title',
            right: 'prevYear,prev,next,nextYear today resetDates',
        },

        buttonText: {
            today: 'Dziś',
            month: 'Miesiąc',
            week: 'Tydzień',
            list: 'Lista',
        },

        customButtons: {
            prevYear: {
                text: '<<',
                click: () => calendar.incrementDate({ years: -1 }),
            },
            nextYear: {
                text: '>>',
                click: () => calendar.incrementDate({ years: 1 }),
            },
            resetDates: {
                text: 'Wyczyść daty',
                click: function () {
                    if (isRangeMode) {
                        startDateInput.value = '';
                        endDateInput.value = '';
                        triggerChange(startDateInput);
                        triggerChange(endDateInput);
                    }
                    if (isSingleMode) {
                        singleDateInput.value = '';
                        triggerChange(singleDateInput);
                    }
                    highlightSelectedRange();
                },
            },
        },

        events: window.calendarUrl || [],

        eventColor: '#dc3545',
        displayEventTime: false,

        eventContent: function (arg) {
            return {
                html: `<div style="font-size:0.75rem"><strong>${arg.event.title}</strong></div>`,
            };
        },

        dateClick: function (info) {
            if (isRangeMode) {
                handleRangeSelection(info.dateStr);
            } else if (isSingleMode) {
                handleSingleSelection(info.dateStr);
            }
        },

        datesSet: function () {
            highlightSelectedRange();
        },
    });

    calendar.render();
    requestAnimationFrame(() => {
        calendar.updateSize();
    });


    function triggerChange(el) {
        if (el) el.dispatchEvent(new Event('change'));
    }

    function handleSingleSelection(dateStr) {
        singleDateInput.value = dateStr;
        triggerChange(singleDateInput);
        highlightSelectedRange();
    }

    function handleRangeSelection(dateStr) {
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

        triggerChange(startDateInput);
        triggerChange(endDateInput);
        highlightSelectedRange();
    }

    if (isRangeMode) {
        startDateInput.addEventListener('change', function () {
            if (endDateInput.value && endDateInput.value < this.value) {
                endDateInput.value = this.value;
                triggerChange(endDateInput);
            }
            if (this.value) calendar.gotoDate(this.value);
            highlightSelectedRange();
        });

        endDateInput.addEventListener('change', function () {
            if (startDateInput.value && startDateInput.value > this.value) {
                startDateInput.value = this.value;
                triggerChange(startDateInput);
            }
            highlightSelectedRange();
        });
    }

    if (isSingleMode) {
        singleDateInput.addEventListener('change', function () {
            if (this.value) calendar.gotoDate(this.value);
            highlightSelectedRange();
        });
    }

    function highlightSelectedRange() {
        document.querySelectorAll('.fc-daygrid-day').forEach(el =>
            el.classList.remove('selected-range')
        );

        let start, end;

        if (isRangeMode) {
            if (!startDateInput.value || !endDateInput.value) return;
            start = new Date(startDateInput.value);
            end = new Date(endDateInput.value);
        } else if (isSingleMode) {
            if (!singleDateInput.value) return;
            start = new Date(singleDateInput.value);
            end = new Date(singleDateInput.value);
        } else {
            return;
        }

        start.setHours(0, 0, 0, 0);
        end.setHours(0, 0, 0, 0);

        document.querySelectorAll('.fc-daygrid-day').forEach(el => {
            const cellDate = new Date(el.dataset.date);
            cellDate.setHours(0, 0, 0, 0);
            if (cellDate >= start && cellDate <= end) {
                el.classList.add('selected-range');
            }
        });
    }

    highlightSelectedRange();
});
