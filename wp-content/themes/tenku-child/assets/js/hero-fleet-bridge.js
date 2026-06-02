(function () {
	'use strict';

	var form = document.querySelector('[data-vip-hero-fleet-search]');
	if (!form) {
		return;
	}

	var fleetRoot = document.querySelector('[data-vip-fleet]');
	var fleetSection = document.getElementById('vip-fleet');
	var brandSelect = form.querySelector('[data-vip-hero-brand]');

	if (!fleetRoot || !brandSelect) {
		return;
	}

	function applyBrandFilter(slug) {
		var inputs = fleetRoot.querySelectorAll('[data-vip-fleet-filter="brand"]');

		inputs.forEach(function (input) {
			input.checked = slug && input.value === slug;
		});

		fleetRoot.removeAttribute('data-vip-hero-search');

		var trigger = fleetRoot.querySelector('[data-vip-fleet-filter="brand"]');
		if (trigger) {
			trigger.dispatchEvent(new Event('change', { bubbles: true }));
		}
	}

	form.addEventListener('submit', function (e) {
		e.preventDefault();

		if (!brandSelect.value) {
			brandSelect.focus();
			return;
		}

		applyBrandFilter(brandSelect.value);

		if (fleetSection) {
			fleetSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
		}
	});

	fleetRoot.addEventListener('change', function (e) {
		if (!e.target.matches('[data-vip-fleet-filter="brand"]')) {
			return;
		}

		var checked = fleetRoot.querySelector('[data-vip-fleet-filter="brand"]:checked');
		brandSelect.value = checked ? checked.value : '';
	});

	document.addEventListener('vip-hero-search-reset', function () {
		brandSelect.value = '';
		brandSelect.selectedIndex = 0;
	});
})();
