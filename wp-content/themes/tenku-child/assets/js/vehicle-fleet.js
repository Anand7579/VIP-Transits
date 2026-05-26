(function () {
	'use strict';

	var roots = document.querySelectorAll('[data-vip-fleet]');
	if (!roots.length) {
		return;
	}

	roots.forEach(function (root) {
		var grid = root.querySelector('[data-vip-fleet-grid]');
		var resultsWrap = root.querySelector('[data-vip-fleet-results]');
		var loaderEl = root.querySelector('[data-vip-fleet-loader]');
		var countEl = root.querySelector('[data-vip-fleet-count] span');
		var sortEl = root.querySelector('[data-vip-fleet-sort]');
		var sortWrap = root.querySelector('[data-vip-fleet-sort-wrap]');
		var sortTrigger = root.querySelector('[data-vip-fleet-sort-trigger]');
		var sortLabel = root.querySelector('[data-vip-fleet-sort-label]');
		var sortMenu = root.querySelector('[data-vip-fleet-sort-menu]');
		var loadBtn = root.querySelector('[data-vip-fleet-load-more]');
		var filterToggle = root.querySelector('[data-vip-fleet-filter-toggle]');
		var filtersPanel = root.querySelector('.vip-fleet__filters');
		var mobileFiltersMq = window.matchMedia('(max-width: 768px)');

		var sortOptionsMap = {
			'title-asc': 'Car Type',
			'price-asc': 'Price Low to high',
			'price-desc': 'Price High to low',
		};

		function getSortValue() {
			return sortEl ? sortEl.value : 'title-asc';
		}
		var cards = function () {
			return Array.prototype.slice.call(grid.querySelectorAll('.vip-fleet-card'));
		};

		function updateCount(visible) {
			if (countEl) {
				countEl.textContent = String(visible);
			}
		}

		function updatePriceRange() {
			var minR = root.querySelector('[data-vip-fleet-price-min]');
			var maxR = root.querySelector('[data-vip-fleet-price-max]');
			var fill = root.querySelector('[data-vip-fleet-range-fill]');
			if (!minR || !maxR || !fill) {
				return;
			}

			var floor = parseInt(minR.min, 10);
			var ceil = parseInt(minR.max, 10);
			var minVal = parseInt(minR.value, 10);
			var maxVal = parseInt(maxR.value, 10);

			if (minVal > maxVal) {
				if (minR === document.activeElement) {
					maxVal = minVal;
					maxR.value = String(maxVal);
				} else {
					minVal = maxVal;
					minR.value = String(minVal);
				}
			}

			var span = ceil - floor;
			var leftPct = ((minVal - floor) / span) * 100;
			var rightPct = 100 - ((maxVal - floor) / span) * 100;

			fill.style.setProperty('--vip-range-left', leftPct + '%');
			fill.style.setProperty('--vip-range-right', rightPct + '%');

			var minLabel = root.querySelector('[data-vip-fleet-price-min-label]');
			var maxLabel = root.querySelector('[data-vip-fleet-price-max-label]');
			if (minLabel) {
				minLabel.textContent = minVal.toLocaleString();
			}
			if (maxLabel) {
				maxLabel.textContent = maxVal.toLocaleString();
			}
		}

		function cardMatches(card) {
			var categories = root.querySelectorAll('[data-vip-fleet-filter="category"]:checked');
			var brands = root.querySelectorAll('[data-vip-fleet-filter="brand"]:checked');
			var seats = root.querySelectorAll('[data-vip-fleet-filter="seat"]:checked');
			var delivery = root.querySelector('[data-vip-fleet-filter="delivery"]');
			var minR = root.querySelector('[data-vip-fleet-price-min]');
			var maxR = root.querySelector('[data-vip-fleet-price-max]');

			var categorySlugs = (card.getAttribute('data-categories') || '').split(/\s+/).filter(Boolean);
			var brandSlugs = (card.getAttribute('data-brands') || '').split(/\s+/).filter(Boolean);
			var seatSlugs = (card.getAttribute('data-seats') || '').split(/\s+/).filter(Boolean);
			var price = parseInt(card.getAttribute('data-price') || '0', 10);
			var hasDelivery = card.getAttribute('data-delivery') === '1';

			var i;
			if (categories.length) {
				var categoryOk = false;
				for (i = 0; i < categories.length; i++) {
					if (categorySlugs.indexOf(categories[i].value) !== -1) {
						categoryOk = true;
						break;
					}
				}
				if (!categoryOk) {
					return false;
				}
			}

			if (brands.length) {
				var brandOk = false;
				for (i = 0; i < brands.length; i++) {
					if (brandSlugs.indexOf(brands[i].value) !== -1) {
						brandOk = true;
						break;
					}
				}
				if (!brandOk) {
					return false;
				}
			}

			if (seats.length) {
				var seatOk = false;
				for (i = 0; i < seats.length; i++) {
					if (seatSlugs.indexOf(seats[i].value) !== -1) {
						seatOk = true;
						break;
					}
				}
				if (!seatOk) {
					return false;
				}
			}

			/* Delivery toggle: ON = hotel/home delivery only; OFF = vehicles without that option. */
			if (delivery) {
				if (delivery.checked) {
					if (!hasDelivery) {
						return false;
					}
				} else if (hasDelivery) {
					return false;
				}
			}

			if (minR && maxR && price > 0) {
				var min = parseInt(minR.value, 10);
				var max = parseInt(maxR.value, 10);
				if (price < min || price > max) {
					return false;
				}
			}

			return true;
		}

		var filterDebounceTimer = null;
		var loaderMinMs = 280;
		var loaderStartedAt = 0;

		function applyFilters() {
			var visible = 0;
			cards().forEach(function (card) {
				var show = cardMatches(card);
				if (show) {
					card.hidden = false;
					card.removeAttribute('hidden');
					visible++;
				} else {
					card.hidden = true;
				}
			});
			updateCount(visible);
		}

		function setResultsLoading(loading) {
			if (!resultsWrap) {
				return;
			}
			resultsWrap.classList.toggle('is-loading', loading);
			if (loaderEl) {
				loaderEl.setAttribute('aria-hidden', loading ? 'false' : 'true');
			}
		}

		function runResultsUpdate() {
			if (!resultsWrap) {
				applyFilters();
				sortCards();
				return;
			}

			setResultsLoading(true);
			loaderStartedAt = Date.now();

			requestAnimationFrame(function () {
				requestAnimationFrame(function () {
					applyFilters();
					sortCards();
					var remaining = Math.max(0, loaderMinMs - (Date.now() - loaderStartedAt));
					window.setTimeout(function () {
						setResultsLoading(false);
					}, remaining);
				});
			});
		}

		function scheduleResultsUpdate() {
			if (filterDebounceTimer) {
				window.clearTimeout(filterDebounceTimer);
			}
			filterDebounceTimer = window.setTimeout(function () {
				filterDebounceTimer = null;
				runResultsUpdate();
			}, 180);
		}

		function sortCards() {
			var value = getSortValue();
			var list = cards().filter(function (c) {
				return !c.hidden;
			});

			list.sort(function (a, b) {
				if (value === 'price-asc' || value === 'price-desc') {
					var pa = parseInt(a.getAttribute('data-price') || '0', 10);
					var pb = parseInt(b.getAttribute('data-price') || '0', 10);
					return value === 'price-asc' ? pa - pb : pb - pa;
				}
				var ta = a.querySelector('.vip-fleet-card__title');
				var tb = b.querySelector('.vip-fleet-card__title');
				var na = ta ? ta.textContent.trim() : '';
				var nb = tb ? tb.textContent.trim() : '';
				return na.localeCompare(nb);
			});

			list.forEach(function (card) {
				grid.appendChild(card);
			});
		}

		root.addEventListener('change', function (e) {
			if (
				e.target.matches('[data-vip-fleet-filter]') ||
				e.target.matches('[data-vip-fleet-price-min]') ||
				e.target.matches('[data-vip-fleet-price-max]')
			) {
				if (e.target.matches('[data-vip-fleet-price-min]') || e.target.matches('[data-vip-fleet-price-max]')) {
					updatePriceRange();
				}
				runResultsUpdate();
			}
		});

		root.addEventListener('input', function (e) {
			if (e.target.matches('[data-vip-fleet-price-min]') || e.target.matches('[data-vip-fleet-price-max]')) {
				updatePriceRange();
				scheduleResultsUpdate();
			}
		});

		function populateSortMenu() {
			if (!sortMenu) {
				return;
			}
			var current = getSortValue();
			sortMenu.innerHTML = '';
			Object.keys(sortOptionsMap).forEach(function (key) {
				if (key === current) {
					return;
				}
				var li = document.createElement('li');
				var btn = document.createElement('button');
				btn.type = 'button';
				btn.setAttribute('data-vip-fleet-sort-option', key);
				btn.textContent = sortOptionsMap[key];
				btn.addEventListener('click', function () {
					sortEl.value = key;
					if (sortLabel) {
						sortLabel.textContent = sortOptionsMap[key];
					}
					closeSortMenu();
					runResultsUpdate();
				});
				li.appendChild(btn);
				sortMenu.appendChild(li);
			});
		}

		function closeSortMenu() {
			if (!sortWrap) {
				return;
			}
			sortWrap.classList.remove('is-open');
			if (sortTrigger) {
				sortTrigger.setAttribute('aria-expanded', 'false');
			}
			if (sortMenu) {
				sortMenu.hidden = true;
			}
		}

		function isMobileFilters() {
			return mobileFiltersMq.matches;
		}

		function setFiltersOpen(open) {
			if (!filtersPanel || !isMobileFilters()) {
				return;
			}
			root.classList.toggle('vip-fleet__layout--filters-open', open);
			filtersPanel.setAttribute('aria-hidden', open ? 'false' : 'true');
			if (filterToggle) {
				filterToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
			}
		}

		function syncFiltersPanelState() {
			if (!filtersPanel) {
				return;
			}
			if (isMobileFilters()) {
				var open = root.classList.contains('vip-fleet__layout--filters-open');
				filtersPanel.setAttribute('aria-hidden', open ? 'false' : 'true');
				if (filterToggle) {
					filterToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
				}
			} else {
				root.classList.remove('vip-fleet__layout--filters-open');
				filtersPanel.setAttribute('aria-hidden', 'false');
				if (filterToggle) {
					filterToggle.setAttribute('aria-expanded', 'false');
				}
			}
		}

		if (filtersPanel) {
			if (filterToggle) {
				filterToggle.addEventListener('click', function () {
					if (!isMobileFilters()) {
						return;
					}
					setFiltersOpen(!root.classList.contains('vip-fleet__layout--filters-open'));
				});
			}

			if (typeof mobileFiltersMq.addEventListener === 'function') {
				mobileFiltersMq.addEventListener('change', syncFiltersPanelState);
			} else if (typeof mobileFiltersMq.addListener === 'function') {
				mobileFiltersMq.addListener(syncFiltersPanelState);
			}
			syncFiltersPanelState();
		}

		if (sortWrap && sortTrigger && sortMenu) {
			sortTrigger.addEventListener('click', function (e) {
				e.stopPropagation();
				var opening = !sortWrap.classList.contains('is-open');
				if (opening) {
					populateSortMenu();
				}
				sortWrap.classList.toggle('is-open', opening);
				sortTrigger.setAttribute('aria-expanded', opening ? 'true' : 'false');
				sortMenu.hidden = !opening;
			});

			document.addEventListener('click', function (e) {
				if (!sortWrap.contains(e.target)) {
					closeSortMenu();
				}
			});
		}

		if (loadBtn && window.vipFleet) {
			loadBtn.addEventListener('click', function () {
				var page = parseInt(loadBtn.getAttribute('data-page') || '1', 10) + 1;
				var perPage = parseInt(root.getAttribute('data-per-page') || '9', 10);
				var maxPages = parseInt(root.getAttribute('data-max-pages') || '1', 10);

				if (page > maxPages) {
					loadBtn.hidden = true;
					return;
				}

				loadBtn.disabled = true;
				setResultsLoading(true);
				var body = new FormData();
				body.append('action', 'vip_fleet_load_more');
				body.append('nonce', vipFleet.nonce);
				body.append('page', String(page));
				body.append('per_page', String(perPage));

				fetch(vipFleet.ajaxUrl, { method: 'POST', body: body, credentials: 'same-origin' })
					.then(function (r) {
						return r.json();
					})
					.then(function (res) {
						if (!res.success || !res.data.html) {
							return;
						}
						grid.insertAdjacentHTML('beforeend', res.data.html);
						loadBtn.setAttribute('data-page', String(page));
						if (page >= res.data.maxPages) {
							loadBtn.hidden = true;
						}
						applyFilters();
					})
					.finally(function () {
						loadBtn.disabled = false;
						setResultsLoading(false);
					});
			});
		}

		updatePriceRange();
		applyFilters();
	});
})();
