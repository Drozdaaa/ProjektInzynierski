document.addEventListener('DOMContentLoaded', function () {
    var editModal = document.getElementById('editDishModal');

    editModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;

        var id = button.getAttribute('data-id');
        var name = button.getAttribute('data-name');
        var price = button.getAttribute('data-price');
        var description = button.getAttribute('data-description');
        var typeId = button.getAttribute('data-type');

        var dietIds = JSON.parse(button.getAttribute('data-diets'));
        var allergyIds = JSON.parse(button.getAttribute('data-allergies'));

        var form = document.getElementById('editDishForm');
        form.action = '/dishes/' + id;

        document.getElementById('edit_name').value = name;
        document.getElementById('edit_price').value = price;
        document.getElementById('edit_description').value = description;
        document.getElementById('edit_dish_type').value = typeId;

        document.querySelectorAll('.edit-diet-checkbox').forEach(cb => cb.checked = false);
        document.querySelectorAll('.edit-allergy-checkbox').forEach(cb => cb.checked = false);

        dietIds.forEach(dietId => {
            var checkbox = document.getElementById('diet_edit_' + dietId);
            if (checkbox) checkbox.checked = true;
        });

        allergyIds.forEach(allergyId => {
            var checkbox = document.getElementById('allergy_edit_' + allergyId);
            if (checkbox) checkbox.checked = true;
        });
    });
});

