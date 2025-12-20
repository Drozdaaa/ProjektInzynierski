document.addEventListener('DOMContentLoaded', function () {
    const dateInput = document.getElementById('date');
    const startTimeInput = document.getElementById('start_time');
    const endTimeInput = document.getElementById('end_time');

    if (!dateInput || !startTimeInput || !endTimeInput) return;

    const busyRoomsUrl = window.busyRoomsUrl;
    const restaurantId = window.restaurantId;
    const eventId = window.eventId ?? null;

    const roomCheckboxes = document.querySelectorAll(
        '.form-check-input[name="rooms[]"]'
    );

    [dateInput, startTimeInput, endTimeInput].forEach(input => {
        input.addEventListener('change', checkAvailability);
    });

    checkAvailability();

    async function checkAvailability() {
        const date = dateInput.value;
        const start = startTimeInput.value;
        const end = endTimeInput.value;

        resetRooms();

        if (!date || !start || !end) return;

        let url = `${busyRoomsUrl}?date=${date}&start_time=${start}&end_time=${end}&restaurant_id=${restaurantId}`;
        if (eventId) {
            url += `&exclude_event_id=${eventId}`;
        }

        try {
            const response = await fetch(url);
            const busyRoomIds = await response.json();

            busyRoomIds.forEach(id => {
                const checkbox = document.querySelector(
                    `.form-check-input[name="rooms[]"][value="${id}"]`
                );

                if (!checkbox) return;

                checkbox.checked = false;
                checkbox.disabled = true;

                const card = checkbox.closest('.card');
                if (card) {
                    card.classList.add('border-danger', 'opacity-50');
                }
            });
        } catch (error) {
            console.error('Błąd sprawdzania dostępności sal:', error);
        }
    }

    function resetRooms() {
        roomCheckboxes.forEach(cb => {
            cb.disabled = false;

            const card = cb.closest('.card');
            if (card) {
                card.classList.remove('border-danger', 'opacity-50');
            }
        });
    }
});
