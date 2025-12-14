document.addEventListener('DOMContentLoaded', function () {
    const postalInput = document.getElementById('postal_code');
    postalInput.addEventListener('input', function (e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length > 2) {
            value = value.substring(0, 2) + '-' + value.substring(2, 5);
        }
        e.target.value = value;
    });
});
