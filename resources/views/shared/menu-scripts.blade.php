<script>
document.addEventListener('DOMContentLoaded', function() {

    document.querySelectorAll('.menu-editor-form').forEach(form => {

        const cards = form.querySelectorAll('.dish-card');
        const priceInput = form.querySelector('input[name="price"]');
        let selectedDishes = new Set();

        // inicjalizacja zaznaczonych dań
        form.querySelectorAll('.dish-checkbox:checked').forEach(checkbox => {
            const card = checkbox.closest('.dish-card');
            selectedDishes.add(card.dataset.dishId);
            card.classList.add('selected');
        });

        function updateTotalPrice() {
            let total = 0;

            selectedDishes.forEach(id => {
                const dishElement = form.querySelector(`.dish-card[data-dish-id="${id}"]`);
                if (dishElement) {
                    total += parseFloat(dishElement.dataset.price);
                }
            });

            priceInput.value = total.toFixed(2);
        }

        updateTotalPrice();

        // obsługa kliknięcia na kartę
        cards.forEach(card => {
            card.addEventListener('click', function() {
                const dishId = card.dataset.dishId;
                const checkbox = card.querySelector('.dish-checkbox');

                if (selectedDishes.has(dishId)) {
                    selectedDishes.delete(dishId);
                    card.classList.remove('selected');
                    checkbox.checked = false;
                } else {
                    selectedDishes.add(dishId);
                    card.classList.add('selected');
                    checkbox.checked = true;
                }

                updateTotalPrice();
            });
        });

        form.addEventListener('submit', function(e) {
            if (selectedDishes.size === 0) {
                e.preventDefault();
                alert('Wybierz przynajmniej jedno danie do menu.');
            }
        });
    });

});
</script>
