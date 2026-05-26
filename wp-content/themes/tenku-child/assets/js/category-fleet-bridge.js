(function () {
	'use strict';

	var cards = document.querySelectorAll('[data-vip-category-filter]');
	if (!cards.length) {
		return;
	}

	var fleetRoot = document.querySelector('[data-vip-fleet]');
	var fleetSection = document.getElementById('vip-fleet');

	function setActiveCategoryCard(slug) {
		cards.forEach(function (card) {
			var isActive = card.getAttribute('data-vip-category-filter') === slug;
			card.classList.toggle('is-active', isActive);
			card.setAttribute('aria-pressed', isActive ? 'true' : 'false');
		});
	}

	function applyCategoryFilter(slug) {
		if (!fleetRoot || !slug) {
			return;
		}

		var inputs = fleetRoot.querySelectorAll('[data-vip-fleet-filter="category"]');
		var matched = false;

		inputs.forEach(function (input) {
			var isMatch = input.value === slug;
			input.checked = isMatch;
			if (isMatch) {
				matched = true;
			}
		});

		if (matched) {
			var checked = fleetRoot.querySelector('[data-vip-fleet-filter="category"]:checked');
			if (checked) {
				checked.dispatchEvent(new Event('change', { bubbles: true }));
			}
		}

		setActiveCategoryCard(slug);
	}

	cards.forEach(function (card) {
		card.addEventListener('click', function () {
			var slug = card.getAttribute('data-vip-category-filter');
			if (!slug) {
				return;
			}

			applyCategoryFilter(slug);

			if (fleetSection) {
				fleetSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
			}
		});
	});

	if (fleetRoot) {
		fleetRoot.addEventListener('change', function (e) {
			if (!e.target.matches('[data-vip-fleet-filter="category"]')) {
				return;
			}

			var checked = fleetRoot.querySelector('[data-vip-fleet-filter="category"]:checked');
			if (checked) {
				setActiveCategoryCard(checked.value);
			} else {
				setActiveCategoryCard('');
			}
		});
	}
})();
