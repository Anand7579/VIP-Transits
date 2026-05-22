/**
 * Single vehicle gallery: thumb click updates main image; slider shows max 3 thumbs.
 */
( function () {
	'use strict';

	var MAX_VISIBLE = 3;

	function onReady( fn ) {
		if ( document.readyState === 'loading' ) {
			document.addEventListener( 'DOMContentLoaded', fn );
		} else {
			fn();
		}
	}

	function initGallery( root ) {
		var mainImg = root.querySelector( '[data-vip-gallery-main]' );
		var track = root.querySelector( '[data-vip-gallery-track]' );
		var viewport = root.querySelector( '[data-vip-gallery-viewport]' );
		var thumbs = Array.prototype.slice.call( root.querySelectorAll( '[data-vip-gallery-thumb]' ) );
		var prevBtn = root.querySelector( '[data-vip-gallery-prev]' );
		var nextBtn = root.querySelector( '[data-vip-gallery-next]' );

		if ( ! mainImg || ! thumbs.length ) {
			return;
		}

		var slideIndex = 0;
		var maxSlide = Math.max( 0, thumbs.length - MAX_VISIBLE );

		function setActiveThumb( button ) {
			thumbs.forEach( function ( btn ) {
				var active = btn === button;
				btn.classList.toggle( 'is-active', active );
				btn.setAttribute( 'aria-pressed', active ? 'true' : 'false' );
			} );
		}

		function updateMainFromThumb( button ) {
			var full = button.getAttribute( 'data-full' );
			if ( ! full ) {
				return;
			}
			mainImg.src = full;
			var img = button.querySelector( 'img' );
			if ( img && img.alt ) {
				mainImg.alt = img.alt;
			}
			setActiveThumb( button );
		}

		function updateTrackPosition() {
			if ( ! track || ! viewport || thumbs.length <= MAX_VISIBLE ) {
				return;
			}
			var first = thumbs[ 0 ];
			var gap = 8;
			var thumbWidth = first.getBoundingClientRect().width + gap;
			track.style.transform = 'translate3d(' + -slideIndex * thumbWidth + 'px, 0, 0)';
		}

		function updateNavState() {
			if ( ! prevBtn || ! nextBtn ) {
				return;
			}
			var showNav = thumbs.length > MAX_VISIBLE;
			prevBtn.hidden = ! showNav;
			nextBtn.hidden = ! showNav;
			if ( showNav ) {
				prevBtn.disabled = slideIndex <= 0;
				nextBtn.disabled = slideIndex >= maxSlide;
			}
		}

		thumbs.forEach( function ( button ) {
			button.addEventListener( 'click', function () {
				updateMainFromThumb( button );
			} );
		} );

		if ( prevBtn ) {
			prevBtn.addEventListener( 'click', function () {
				slideIndex = Math.max( 0, slideIndex - 1 );
				updateTrackPosition();
				updateNavState();
			} );
		}

		if ( nextBtn ) {
			nextBtn.addEventListener( 'click', function () {
				slideIndex = Math.min( maxSlide, slideIndex + 1 );
				updateTrackPosition();
				updateNavState();
			} );
		}

		window.addEventListener( 'resize', updateTrackPosition );
		updateTrackPosition();
		updateNavState();
	}

	onReady( function () {
		document.querySelectorAll( '[data-vip-gallery]' ).forEach( initGallery );
	} );
} )();
