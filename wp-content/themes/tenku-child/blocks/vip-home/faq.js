/**
 * FAQ accordion: one item open at a time + smooth max-height open/close.
 */
( function () {
	'use strict';

	var DURATION_MS = 450;

	function initFaqAccordions() {
		var reduceMotion =
			window.matchMedia && window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;

		function getAnswer( item ) {
			return item.querySelector( '.vip-faq__answer' );
		}

		function getTrigger( item ) {
			return item.querySelector( '[data-vip-faq-trigger]' );
		}

		function closeItemInstant( item ) {
			var answer = getAnswer( item );
			var trigger = getTrigger( item );
			item.classList.remove( 'is-open' );
			if ( trigger ) {
				trigger.setAttribute( 'aria-expanded', 'false' );
			}
			if ( answer ) {
				answer.setAttribute( 'inert', '' );
				answer.style.maxHeight = '0px';
			}
		}

		function openItemInstant( item ) {
			var answer = getAnswer( item );
			var trigger = getTrigger( item );
			item.classList.add( 'is-open' );
			if ( trigger ) {
				trigger.setAttribute( 'aria-expanded', 'true' );
			}
			if ( answer ) {
				answer.removeAttribute( 'inert' );
				answer.style.maxHeight = 'none';
			}
		}

		function animateOpen( item ) {
			var answer = getAnswer( item );
			var trigger = getTrigger( item );
			if ( ! answer || reduceMotion ) {
				openItemInstant( item );
				return;
			}

			item.classList.add( 'is-open' );
			if ( trigger ) {
				trigger.setAttribute( 'aria-expanded', 'true' );
			}
			answer.removeAttribute( 'inert' );

			answer.style.maxHeight = '0px';
			void answer.offsetHeight;
			requestAnimationFrame( function () {
				requestAnimationFrame( function () {
					answer.style.maxHeight = answer.scrollHeight + 'px';
				} );
			} );

			var settled = false;
			var tid;
			var done = function ( e ) {
				if ( e && e.propertyName && e.propertyName !== 'max-height' ) {
					return;
				}
				if ( settled ) {
					return;
				}
				settled = true;
				window.clearTimeout( tid );
				answer.removeEventListener( 'transitionend', done );
				if ( item.classList.contains( 'is-open' ) ) {
					answer.style.maxHeight = 'none';
				}
			};
			answer.addEventListener( 'transitionend', done );
			tid = window.setTimeout( done, DURATION_MS + 80 );
		}

		function animateClose( item ) {
			var answer = getAnswer( item );
			var trigger = getTrigger( item );
			if ( ! answer || reduceMotion ) {
				closeItemInstant( item );
				return;
			}

			var h = answer.scrollHeight;
			if ( answer.style.maxHeight === 'none' || ! answer.style.maxHeight ) {
				answer.style.maxHeight = h + 'px';
			}
			void answer.offsetHeight;

			item.classList.remove( 'is-open' );
			if ( trigger ) {
				trigger.setAttribute( 'aria-expanded', 'false' );
			}
			answer.setAttribute( 'inert', '' );

			requestAnimationFrame( function () {
				requestAnimationFrame( function () {
					answer.style.maxHeight = '0px';
				} );
			} );

			var settled = false;
			var tid;
			var done = function ( e ) {
				if ( e && e.propertyName && e.propertyName !== 'max-height' ) {
					return;
				}
				if ( settled ) {
					return;
				}
				settled = true;
				window.clearTimeout( tid );
				answer.removeEventListener( 'transitionend', done );
				answer.style.maxHeight = '';
			};
			answer.addEventListener( 'transitionend', done );
			tid = window.setTimeout( done, DURATION_MS + 80 );
		}

		document.querySelectorAll( '[data-vip-faq-accordion]' ).forEach( function ( accordion ) {
			var openItem = accordion.querySelector( '.vip-faq__item.is-open' );
			if ( openItem ) {
				var openAnswer = getAnswer( openItem );
				if ( openAnswer ) {
					openAnswer.style.maxHeight = 'none';
				}
			}

			accordion.querySelectorAll( '[data-vip-faq-trigger]' ).forEach( function ( btn ) {
				btn.addEventListener( 'click', function () {
					var item = btn.closest( '[data-vip-faq-item]' );
					if ( ! item ) {
						return;
					}

					if ( item.classList.contains( 'is-open' ) ) {
						animateClose( item );
						return;
					}

					accordion.querySelectorAll( '.vip-faq__item.is-open' ).forEach( function ( other ) {
						if ( other !== item ) {
							animateClose( other );
						}
					} );

					animateOpen( item );
				} );
			} );

			window.addEventListener(
				'resize',
				function () {
					accordion.querySelectorAll( '.vip-faq__item.is-open .vip-faq__answer' ).forEach( function ( answer ) {
						if ( answer.style.maxHeight && answer.style.maxHeight !== 'none' ) {
							answer.style.maxHeight = answer.scrollHeight + 'px';
						}
					} );
				},
				{ passive: true }
			);
		} );
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', initFaqAccordions );
	} else {
		initFaqAccordions();
	}
} )();
