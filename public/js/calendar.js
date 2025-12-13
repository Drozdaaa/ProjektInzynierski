document.addEventListener('DOMContentLoaded', function () {
    let calendarEl = document.getElementById('calendar');
    let dateInput = document.getElementById('date');
    let startTimeInput = document.getElementById('start_time');
    let endTimeInput = document.getElementById('end_time');

    let initialView = window.innerWidth < 576 ? 'listWeek' : 'dayGridMonth';

    let calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: initialView,
        locale: 'pl',
        height: 550,
        firstDay: 1,

        buttonText: {
            prev: window.innerWidth < 576 ? '<' : 'Poprzedni',
            next: window.innerWidth < 576 ? '>' : 'Następny',
            today: window.innerWidth < 576 ? 'Obecny tydzień' : 'Dzisiaj',
            month: 'Miesiąc',
            week: 'Tydzień',
            list: 'Lista'
        },

        headerToolbar: {
            right: 'prevYear,prev,next,nextYear today',
        },

        customButtons: {
            prevYear: {
                text: '<<',
                click: function () {
                    calendar.incrementDate({ years: -1 });
                }
            },
            nextYear: {
                text: '>>',
                click: function () {
                    calendar.incrementDate({ years: 1 });
                }
            }
        },

        noEventsContent: 'Brak wydarzeń',
        events: window.calendarUrl,
        eventColor: '#dc3545',
        displayEventTime: false,

        eventContent: function (arg) {
            return {
                html: `<div style="font-size: 0.75rem; line-height: 1.1;">
                <strong>${arg.event.title}</strong><br>
            </div>`
            };
        },

        eventDidMount: function (info) {
            info.el.setAttribute(
                'title',
                `Sala: ${info.event.extendedProps.room}\nGodzina: ${info.event.extendedProps.time}`
            );
        },

        dateClick: function (info) {
            dateInput.value = info.dateStr;
            highlightSelectedFromInput();
            checkBusyRooms();
            if (dateInput) {
                dateInput.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
            }
        },

        datesSet: function () {
            highlightSelectedFromInput();
        }
    });

    calendar.render();

    document.querySelectorAll('.fc-button').forEach(btn => btn.removeAttribute('title'));
    document.querySelectorAll('.fc-button').forEach(btn => {
        btn.addEventListener('click', () => btn.blur());
    });

    dateInput.addEventListener('change', function () {
        let selectedDate = this.value;
        if (!selectedDate) return;

        calendar.gotoDate(selectedDate);
        highlightSelectedFromInput();
        checkBusyRooms();
    });

    startTimeInput.addEventListener('change', checkBusyRooms);
    endTimeInput.addEventListener('change', checkBusyRooms);

    function highlightSelectedFromInput() {
        let selectedDate = dateInput.value;
        if (!selectedDate) return;

        document
            .querySelectorAll('.fc-daygrid-day')
            .forEach(el => el.classList.remove('selected-from-input'));

        document
            .querySelectorAll(`.fc-daygrid-day[data-date="${selectedDate}"]`)
            .forEach(el => el.classList.add('selected-from-input'));
    }

    function checkBusyRooms() {
        if (!dateInput.value || !startTimeInput.value || !endTimeInput.value) return;

        fetch(`${window.busyRoomsUrl}?date=${dateInput.value}&start_time=${startTimeInput.value}&end_time=${endTimeInput.value}&restaurant_id=${window.restaurantId}`)
            .then(res => res.json())
            .then(busyRooms => {
                document.querySelectorAll('.room-checkbox').forEach(checkbox => {
                    const roomId = checkbox.dataset.roomId;

                    if (busyRooms.includes(Number(roomId))) {
                        checkbox.checked = false;
                        checkbox.disabled = true;
                        checkbox.closest('.card').classList.add('opacity-50');
                    } else {
                        checkbox.disabled = false;
                        checkbox.closest('.card').classList.remove('opacity-50');
                    }
                });
            });
    }
});
