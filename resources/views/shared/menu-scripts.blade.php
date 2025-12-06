<script>
document.addEventListener('DOMContentLoaded', function() {

    const forms = document.querySelectorAll('#menu-form, .menu-editor-form');

    forms.forEach(form => {

        const cards = form.querySelectorAll('.dish-card');
        const priceInput = form.querySelector('input[name="price"]');
        let selectedDishes = new Set();

        form.querySelectorAll('.dish-checkbox:checked').forEach(checkbox => {
            const card = checkbox.closest('.dish-card');
            selectedDishes.add(card.dataset.dishId);
            card.classList.add('selected');
        });

        function updateTotalPrice() {
            let total = 0;
            selectedDishes.forEach(id => {
                const card = form.querySelector(`.dish-card[data-dish-id="${id}"]`);
                if (card) total += parseFloat(card.dataset.price);
            });
            if (priceInput) priceInput.value = total.toFixed(2);
        }

        updateTotalPrice();

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

    });

});
</script>
