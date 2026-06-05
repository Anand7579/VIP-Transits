(function () {
	'use strict';

	if (typeof vipWhatsAppTrack === 'undefined' || !vipWhatsAppTrack.enabled) {
		return;
	}

	function isWhatsAppLink(anchor) {
		if (!anchor || !anchor.href) {
			return false;
		}
		return /wa\.me|api\.whatsapp\.com/i.test(anchor.href);
	}

	function extractMessageFromWaUrl(url) {
		if (!url) {
			return '';
		}
		var match = String(url).match(/[?&]text=([^&]+)/i);
		if (!match) {
			return '';
		}
		try {
			return decodeURIComponent(match[1].replace(/\+/g, ' '));
		} catch (e) {
			return match[1].replace(/\+/g, ' ');
		}
	}

	function getVehicleContext(anchor) {
		var card = anchor.closest('.vip-fleet-card, .vip-vdetail, article.vip-vdetail');
		var vehicleId = 0;
		var vehicleName = '';
		var vehicleUrl = '';
		var source = 'whatsapp';

		if (anchor.classList.contains('vip-fleet-card__book') || anchor.classList.contains('vip-fleet-card__icon--wa')) {
			source = 'fleet-card';
		} else if (anchor.classList.contains('vip-vdetail__pricing-wa') || anchor.classList.contains('vip-vdetail__masthead-wa')) {
			source = 'vehicle-detail';
		} else if (anchor.classList.contains('vip-wa-sticky')) {
			source = 'sticky-widget';
		} else if (anchor.classList.contains('vip-page__btn--whatsapp')) {
			source = 'contact-page';
		} else if (anchor.classList.contains('vip-cta__wa')) {
			source = 'cta-band';
		}

		if (card) {
			vehicleId = parseInt(card.getAttribute('data-vehicle-id') || '0', 10);
			if (!vehicleId && card.id) {
				var match = String(card.id).match(/post-(\d+)/);
				if (match) {
					vehicleId = parseInt(match[1], 10);
				}
			}
		}

		var titleEl = document.querySelector('.vip-vdetail__masthead-title, h1.vip-vdetail__masthead-title');
		if (titleEl) {
			vehicleName = titleEl.textContent.trim();
		}

		if (card && card.querySelector('.vip-fleet-card__title a')) {
			vehicleName = card.querySelector('.vip-fleet-card__title a').textContent.trim();
			vehicleUrl = card.querySelector('.vip-fleet-card__title a').href || '';
		}

		if (!vehicleUrl && card && card.classList.contains('vip-vdetail')) {
			vehicleUrl = window.location.href;
		}

		return {
			vehicle_id: vehicleId || 0,
			vehicle_name: vehicleName,
			vehicle_url: vehicleUrl,
			source: source,
			page_url: window.location.href,
			wa_url: anchor.href,
			message: extractMessageFromWaUrl(anchor.href),
		};
	}

	function openWhatsApp(url) {
		window.open(url, '_blank', 'noopener,noreferrer');
	}

	document.addEventListener(
		'click',
		function (event) {
			var anchor = event.target.closest('a');
			if (!isWhatsAppLink(anchor)) {
				return;
			}

			event.preventDefault();

			var payload = getVehicleContext(anchor);
			var formData = new FormData();
			formData.append('action', 'vip_track_whatsapp_enquiry');
			formData.append('nonce', vipWhatsAppTrack.nonce);
			formData.append('vehicle_id', String(payload.vehicle_id));
			formData.append('vehicle_name', payload.vehicle_name);
			formData.append('vehicle_url', payload.vehicle_url);
			formData.append('source', payload.source);
			formData.append('page_url', payload.page_url);
			formData.append('wa_url', payload.wa_url);
			formData.append('message', payload.message);

			fetch(vipWhatsAppTrack.ajaxUrl, {
				method: 'POST',
				body: formData,
				credentials: 'same-origin',
			})
				.then(function (response) {
					return response.json();
				})
				.then(function (json) {
					var targetUrl = payload.wa_url;
					if (json && json.success && json.data) {
						if (json.data.wa_url) {
							targetUrl = json.data.wa_url;
						}
					}
					openWhatsApp(targetUrl);
				})
				.catch(function () {
					openWhatsApp(payload.wa_url);
				});
		},
		true
	);
})();
