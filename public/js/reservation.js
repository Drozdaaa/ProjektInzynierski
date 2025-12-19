document.addEventListener('DOMContentLoaded', function () {
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');
    const dailyHoursContainer = document.getElementById('daily-hours-container');
    const dailySelectionsContainer = document.getElementById('daily-selections-container');
    const selectionTemplate = document.getElementById('daily-selection-template');
    const mainPeopleInput = document.getElementById('number_of_people');

    if (!startDateInput || !endDateInput) return;

    if (startDateInput.value && endDateInput.value) {
        buildReservationDays();
    } else {
        renderPreviewMode();
    }

    startDateInput.addEventListener('change', syncDates);
    endDateInput.addEventListener('change', syncDates);

    if (mainPeopleInput) {
        mainPeopleInput.addEventListener('input', syncPeopleCount);
    }

    function syncDates() {
        if (!startDateInput.value || !endDateInput.value) {
            renderPreviewMode();
            return;
        }
        buildReservationDays();
    }

    function buildReservationDays() {
        renderHoursPerDay();
        renderSelectionsPerDay();
        checkBusyRoomsMultiDay();
    }

    function renderPreviewMode() {
        if (!dailySelectionsContainer || !selectionTemplate) return;

        dailySelectionsContainer.innerHTML = '';
        dailyHoursContainer.innerHTML =
            '<div class="text-center text-muted fst-italic p-3">Wybierz daty, aby skonfigurować rezerwację.</div>';

        const clone = selectionTemplate.content.cloneNode(true);
        const header = clone.querySelector('.card-header');

        if (header) {
            header.classList.remove('bg-light');
            header.classList.add('bg-secondary', 'text-white');
            header.innerHTML =
                '<h5 class="mb-0"><i class="bi bi-info-circle me-2"></i> Podgląd oferty</h5>';
        }

        clone.querySelectorAll('input, select, button').forEach(el => {
            el.disabled = true;
            if (el.type === 'checkbox') el.checked = false;
        });

        dailySelectionsContainer.appendChild(clone);
    }

    function renderHoursPerDay() {
        if (!dailyHoursContainer) return;
        dailyHoursContainer.innerHTML = '';

        iterateDays((dateStr, dateObj) => {
            const dayName = dateObj.toLocaleDateString('pl-PL', {
                weekday: 'long',
                day: 'numeric',
                month: 'long'
            });

            const row = document.createElement('div');
            row.className = 'row mb-2 align-items-center border-bottom pb-2';
            row.innerHTML = `
                <div class="col-md-4 fw-bold text-capitalize">${dayName}</div>
                <div class="col-md-4">
                    <label class="small text-muted">Od:</label>
                    <input type="time" name="hours[${dateStr}][start]" class="form-control form-control-sm time-input" value="12:00" required>
                </div>
                <div class="col-md-4">
                    <label class="small text-muted">Do:</label>
                    <input type="time" name="hours[${dateStr}][end]" class="form-control form-control-sm time-input" value="20:00" required>
                </div>
            `;
            dailyHoursContainer.appendChild(row);
        });

        document.querySelectorAll('.time-input').forEach(input => {
            input.addEventListener('change', checkBusyRoomsMultiDay);
        });
    }

    function renderSelectionsPerDay() {
        if (!dailySelectionsContainer || !selectionTemplate) return;
        dailySelectionsContainer.innerHTML = '';

        const defaultPeople = mainPeopleInput ? mainPeopleInput.value : '';

        iterateDays((dateStr, dateObj) => {
            const clone = selectionTemplate.content.cloneNode(true);
            const wrapper = clone.querySelector('.daily-block');
            wrapper.dataset.date = dateStr;

            clone.querySelector('.date-label').textContent =
                dateObj.toLocaleDateString('pl-PL', {
                    day: 'numeric',
                    month: 'long',
                    year: 'numeric'
                });

            const peopleInput = clone.querySelector('.daily-people-input');
            if (peopleInput) {
                peopleInput.name = `people[${dateStr}]`;
                peopleInput.value = defaultPeople;
            }

            clone.querySelectorAll('.room-checkbox').forEach(input => {
                input.name = `rooms[${dateStr}][]`;
                input.id = `room_${input.value}_${dateStr}`;
                const label = input.closest('.form-check')?.querySelector('label');
                if (label) label.setAttribute('for', input.id);
            });

            clone.querySelectorAll('.menu-wrapper input[type="checkbox"]').forEach(input => {
                input.name = `menus[${dateStr}][]`;
                input.id = `${input.id}_${dateStr}`;
                const label = input.closest('.form-check')?.querySelector('label');
                if (label) label.setAttribute('for', input.id);
            });

            dailySelectionsContainer.appendChild(clone);
        });
    }

    function iterateDays(callback) {
        let current = new Date(startDateInput.value);
        const end = new Date(endDateInput.value);

        while (current <= end) {
            const dateStr = current.toISOString().split('T')[0];
            callback(dateStr, new Date(current));
            current.setDate(current.getDate() + 1);
        }
    }

    async function checkBusyRoomsMultiDay() {
        const timeInputs = document.querySelectorAll('.time-input');
        if (!timeInputs.length) return;

        const days = {};
        timeInputs.forEach(input => {
            const match = input.name.match(/hours\[(.*?)\]\[(.*?)\]/);
            if (!match) return;
            days[match[1]] ??= {};
            days[match[1]][match[2]] = input.value;
        });

        const requests = Object.entries(days).map(([date, times]) => {
            if (!times.start || !times.end) return;

            const url =
                `${window.busyRoomsUrl}?date=${date}` +
                `&start_time=${times.start}` +
                `&end_time=${times.end}` +
                `&restaurant_id=${window.restaurantId}`;

            return fetch(url)
                .then(res => res.json())
                .then(busyIds => disableBusyRooms(date, busyIds));
        });

        await Promise.all(requests);
    }

    function disableBusyRooms(date, busyIds) {
        const block = document.querySelector(`.daily-block[data-date="${date}"]`);
        if (!block) return;

        busyIds.forEach(id => {
            const checkbox = block.querySelector(`.room-checkbox[value="${id}"]`);
            if (!checkbox) return;

            checkbox.checked = false;
            checkbox.disabled = true;

            const card = checkbox.closest('.card');
            if (card) card.classList.add('opacity-50', 'border-danger');

            const label = block.querySelector(`label[for="${checkbox.id}"]`);
            if (label) label.innerText = 'Zajęta';
        });
    }

    function syncPeopleCount() {
        const value = mainPeopleInput.value;
        document.querySelectorAll('.daily-people-input').forEach(input => {
            input.value = value;
        });
    }
});
