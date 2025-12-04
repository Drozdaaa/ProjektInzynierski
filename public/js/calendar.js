document.addEventListener('DOMContentLoaded', function () {
    let calendarEl = document.getElementById('calendar');
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
        noEventsContent: 'Brak wydarzeń',
        events: window.calendarUrl,
        eventColor: '#dc3545',
        eventTimeFormat: { hour: '2-digit', minute: '2-digit', hour12: false },
        eventDidMount: function (info) {
            info.el.setAttribute('title',
                `Godzina: ${info.event.extendedProps.time}\nSala: ${info.event.extendedProps.rooms}`);
        },
        dateClick: function (info) {
            document.getElementById('date').value = info.dateStr;
        }
    });

    calendar.render();
    document.querySelectorAll('.fc-button').forEach(btn => btn.removeAttribute('title'));
    document.querySelectorAll('.fc-button').forEach(btn => {
        btn.addEventListener('click', () => btn.blur());
    });
});

const dateInput = document.getElementById('date');
const startTimeInput = document.getElementById('start_time');
const endTimeInput = document.getElementById('end_time');

function checkBusyRooms() {
    if (!dateInput.value || !startTimeInput.value || !endTimeInput.value) return;

    fetch(`${window.busyRoomsUrl}?date=${dateInput.value}&start_time=${startTimeInput.value}
        &end_time=${endTimeInput.value}&restaurant_id=${window.restaurantId}`)
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

dateInput.addEventListener('change', checkBusyRooms);
startTimeInput.addEventListener('change', checkBusyRooms);
endTimeInput.addEventListener('change', checkBusyRooms);




