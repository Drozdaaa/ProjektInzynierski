<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

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
