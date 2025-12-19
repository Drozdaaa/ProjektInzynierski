<script>
document.addEventListener('DOMContentLoaded', function() {
    const daySections = document.querySelectorAll('.day-section');

    daySections.forEach(section => {
        const cards = section.querySelectorAll('.dish-card');
        const priceInput = section.querySelector('.day-price-input');

        function updateTotalPrice() {
            let total = 0;
            section.querySelectorAll('.dish-checkbox:checked').forEach(checkbox => {
                const card = checkbox.closest('.dish-card');
                if (card && card.dataset.price) {
                    total += parseFloat(card.dataset.price);
                }
            });

            if (priceInput) {
                priceInput.value = total.toFixed(2);
            }
        }

        updateTotalPrice();

        cards.forEach(card => {
            card.addEventListener('click', function(e) {
                const checkbox = card.querySelector('.dish-checkbox');

                if (e.target.type === 'checkbox') {
                    if (checkbox.checked) {
                        card.classList.add('selected');
                    } else {
                        card.classList.remove('selected');
                    }
                    updateTotalPrice();
                    return;
                }

                if (e.target.tagName !== 'LABEL') {
                    checkbox.checked = !checkbox.checked;

                    if (checkbox.checked) {
                        card.classList.add('selected');
                    } else {
                        card.classList.remove('selected');
                    }
                    updateTotalPrice();
                }
            });
        });
    });
});
</script>
