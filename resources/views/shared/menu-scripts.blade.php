<script>
    document.addEventListener('DOMContentLoaded', function() {
        function updateSection(container) {
            let total = 0;
            let count = 0;

            const checkboxes = container.querySelectorAll('.dish-checkbox:checked');
            const priceInput = container.querySelector('.day-price-input') || document.getElementById('price');
            const countBadge = document.getElementById('selected-count');

            checkboxes.forEach(checkbox => {
                const card = checkbox.closest('.dish-card');
                if (card && card.dataset.price) {
                    total += parseFloat(card.dataset.price);
                    count++;
                }
            });

            if (priceInput) {
                priceInput.value = total.toFixed(2);
            }

            if (countBadge && !container.classList.contains('day-section')) {
                countBadge.textContent = count;
            }
        }

        const dishCards = document.querySelectorAll('.dish-card');
        dishCards.forEach(card => {
            card.addEventListener('click', function(e) {
                const checkbox = this.querySelector('.dish-checkbox');
                const target = e.target;

                if (target !== checkbox && !target.closest('.form-check-label')) {
                    checkbox.checked = !checkbox.checked;
                }

                if (checkbox.checked) {
                    this.classList.add('selected');
                } else {
                    this.classList.remove('selected');
                }

                const container = this.closest('.day-section') || this.closest('#menu-form');
                if (container) {
                    updateSection(container);
                }
            });
        });

        const sections = document.querySelectorAll('.day-section');
        if (sections.length > 0) {
            sections.forEach(s => updateSection(s));
        } else {
            const form = document.getElementById('menu-form');
            if (form) updateSection(form);
        }
    });
</script>
