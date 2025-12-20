$(document).ready(function () {
    $('#res_user_id').select2({
        theme: 'bootstrap-5',
        dropdownParent: $('#editRestaurantModal'),
        width: '100%',
        placeholder: "Wybierz managera...",
        allowClear: true,
        language: { noResults: () => "Brak wyników" }
    });

    $('#filter_restaurant_id').select2({
        theme: 'bootstrap-5',
        width: '100%',
        placeholder: "Wpisz nazwę restauracji...",
        allowClear: true,
        language: { noResults: () => "Nie znaleziono restauracji" }
    });

    const activeTab = localStorage.getItem('adminTab') || 'users';
    const btnUsers = document.getElementById('btn-users');
    const btnRestaurants = document.getElementById('btn-restaurants');
    const btnEvents = document.getElementById('btn-events');

    if (btnUsers && btnRestaurants && btnEvents) {
        if (activeTab === 'restaurants') {
            btnRestaurants.click();
        } else if (activeTab === 'events') {
            btnEvents.click();
        } else {
            btnUsers.click();
        }
    }

    if (window.adminConfig && window.adminConfig.formErrorType) {
        if (window.adminConfig.formErrorType === 'admin_user_edit') {
            new bootstrap.Modal(document.getElementById('editUserModal')).show();
        } else if (window.adminConfig.formErrorType === 'admin_restaurant_edit') {
            new bootstrap.Modal(document.getElementById('editRestaurantModal')).show();
        }
    }
});

function showTable(type) {
    const tables = {
        users: document.getElementById('table-users'),
        restaurants: document.getElementById('table-restaurants'),
        events: document.getElementById('table-events')
    };

    Object.values(tables).forEach(el => {
        if (el) el.classList.add('d-none');
    });

    if (tables[type]) {
        tables[type].classList.remove('d-none');
        localStorage.setItem('adminTab', type);
    }
}

function openUserModal(id, firstName, lastName, email, phone, roleId, isActive) {
    const form = document.getElementById('userForm');
    // Używamy base URL przekazanego z widoku lub domyślnej ścieżki
    const baseUrl = (window.adminConfig && window.adminConfig.urls.users) ? window.adminConfig.urls.users : '/admin/users';
    form.action = baseUrl + "/" + id;

    document.getElementById('user_first_name').value = firstName;
    document.getElementById('user_last_name').value = lastName;
    document.getElementById('user_email').value = email;
    document.getElementById('user_phone').value = phone;
    document.getElementById('user_role_id').value = roleId;
    document.getElementById('user_is_active').checked = isActive == 1;

    new bootstrap.Modal(document.getElementById('editUserModal')).show();
}

function openRestaurantModal(id, name, desc, street, bNumber, city, pCode, userId) {
    const form = document.getElementById('restaurantForm');
    const baseUrl = (window.adminConfig && window.adminConfig.urls.restaurants) ? window.adminConfig.urls.restaurants : '/restaurants';
    form.action = baseUrl + "/" + id;

    document.getElementById('res_name').value = name;
    document.getElementById('res_description').value = desc || '';
    document.getElementById('res_street').value = street;
    document.getElementById('res_building_number').value = bNumber;
    document.getElementById('res_city').value = city;
    document.getElementById('postal_code').value = pCode;

    if (userId) {
        $('#res_user_id').val(userId).trigger('change');
    } else {
        $('#res_user_id').val(null).trigger('change');
    }

    new bootstrap.Modal(document.getElementById('editRestaurantModal')).show();
}
