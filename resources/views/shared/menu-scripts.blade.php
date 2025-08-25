<script>
    document.addEventListener('DOMContentLoaded', function() {
        const cards = document.querySelectorAll('.dish-card');
        const priceInput = document.getElementById('price');
        let selectedDishes = new Set();

        document.querySelectorAll('.dish-checkbox:checked').forEach(checkbox => {
            const card = checkbox.closest('.dish-card');
            selectedDishes.add(card.dataset.dishId);
            card.classList.add('selected');
        });
        updateTotalPrice();

        function updateTotalPrice() {
            let total = 0;
            selectedDishes.forEach(id => {
                const dishElement = document.querySelector(`.dish-card[data-dish-id="${id}"]`);
                total += parseFloat(dishElement.dataset.price);
            });
            priceInput.value = total.toFixed(2);
        }

        cards.forEach(card => {
            card.addEventListener('click', function(e) {
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

        const form = document.getElementById('menu-form');
        form.addEventListener('submit', function(e) {
            const anyChecked = selectedDishes.size > 0;
            if (!anyChecked) {
                e.preventDefault();
                alert('Wybierz przynajmniej jedno danie do menu.');
            }
        });
    });
</script>
