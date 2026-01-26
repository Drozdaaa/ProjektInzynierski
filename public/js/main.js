
$(function () {
    $('#filters-form').on('submit', function(e) {
        e.preventDefault();
        fetchRestaurants();
    });

    $(document).on('click', '#restaurant-list .pagination a', function(e) {
        e.preventDefault();
        let url = $(this).attr('href');
        fetchRestaurants(url);
    });

    $(document).on('click', '#filters-form .btn-outline-secondary', function(e) {
        e.preventDefault();
        $('#filters-form')[0].reset();
        fetchRestaurants();
    });

    function fetchRestaurants(url) {
        $.ajax({
            url: url || "/",
            type: 'GET',
            data: $('#filters-form').serialize(),
            success: function(data) {
                $('#restaurant-list').html(data);
            },
            error: function(xhr) {
                console.log("AJAX error:", xhr.responseText);
                alert('Błąd wczytywania danych.');
            }
        });
    }
});
