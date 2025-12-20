function openRoomModal(mode, id = null, name = '', capacity = '', price = '', description = '', cleaningDuration = 0) {
    const form = document.getElementById('roomForm');
    const title = document.getElementById('roomModalLabel');
    const methodInput = document.getElementById('roomFormMethod');
    const config = window.restaurantConfig;

    let hours = Math.floor(cleaningDuration / 60);
    let minutes = cleaningDuration % 60;

    if (mode === 'add') {
        title.textContent = 'Dodaj salę';
        form.action = config.routes.store;
        methodInput.value = 'POST';

        document.getElementById('room_name').value = config.oldInput.name || '';
        document.getElementById('room_capacity').value = config.oldInput.capacity || '';
        document.getElementById('room_price').value = config.oldInput.price || '';
        document.getElementById('room_description').value = config.oldInput.description || '';
        document.getElementById('room_cleaning_hours').value = config.oldInput.cleaning_hours || 0;
        document.getElementById('room_cleaning_minutes').value = config.oldInput.cleaning_minutes || 0;

    } else if (mode === 'edit') {
        title.textContent = 'Edytuj salę';
        form.action = config.routes.updateBase + '/' + id;
        methodInput.value = 'PUT';

        document.getElementById('room_name').value = config.oldInput.name || name;
        document.getElementById('room_capacity').value = config.oldInput.capacity || capacity;
        document.getElementById('room_price').value = config.oldInput.price || price;
        document.getElementById('room_description').value = config.oldInput.description || description;

        const oldHours = config.oldInput.cleaning_hours;
        const oldMinutes = config.oldInput.cleaning_minutes;

        document.getElementById('room_cleaning_hours').value = (oldHours !== null && oldHours !== "") ? oldHours : hours;
        document.getElementById('room_cleaning_minutes').value = (oldMinutes !== null && oldMinutes !== "") ? oldMinutes : minutes;
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const config = window.restaurantConfig;

    if (config.errors.any) {
        if (config.errors.formType === 'restaurant_edit') {
            new bootstrap.Modal(document.getElementById('editRestaurantModal')).show();
        } else if (config.errors.formType === 'room_action') {
            new bootstrap.Modal(document.getElementById('roomModal')).show();
        }
    }
});
