/**
 * VIP Transits — lazy sections + scroll animations.
 */
( function () {
	'use strict';

	var REDUCED =
		window.matchMedia &&
		window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;

	var CARD_SELECTORS = [
		'.vip-fleet-card',
		'.vip-occasions__card',
		'.vip-occasions__col--left',
		'.vip-why-rent__card',
		'.vip-delivery__card',
		'.vip-vehicles__card',
		'.vip-vdetail__related-link',
		'.vip-features__col',
	];

	function onReady( fn ) {
		if ( document.readyState === 'loading' ) {
			document.addEventListener( 'DOMContentLoaded', fn );
		} else {
			fn();
		}
	}

	function markVisible( node, className ) {
		if ( node && className ) {
			node.classList.add( className );
		}
	}

	function observeOnce( elements, options, callback ) {
		if ( ! elements.length || typeof IntersectionObserver === 'undefined' ) {
			elements.forEach( function ( el ) {
				callback( el );
			} );
			return;
		}

		var observer = new IntersectionObserver( function ( entries, obs ) {
			entries.forEach( function ( entry ) {
				if ( ! entry.isIntersecting ) {
					return;
				}
				callback( entry.target );
				obs.unobserve( entry.target );
			} );
		}, options );

		elements.forEach( function ( el ) {
			observer.observe( el );
		} );
	}

	function initLazyImages() {
		document.querySelectorAll( '[data-vip-section] img:not([loading])' ).forEach( function ( img ) {
			img.loading = 'lazy';
			img.decoding = 'async';
		} );
	}

	function initCards() {
		var cards = [];

		CARD_SELECTORS.forEach( function ( selector ) {
			document.querySelectorAll( selector ).forEach( function ( el ) {
				el.classList.add( 'vip-animate-card' );
				cards.push( el );
			} );
		} );

		return cards;
	}

	function initHero() {
		var heroes = Array.prototype.slice.call( document.querySelectorAll( '.vip-hero' ) );

		heroes.forEach( function ( hero ) {
			hero.querySelectorAll( '.vip-hero__prop' ).forEach( function ( prop, index ) {
				prop.style.setProperty(
					'--vip-hero-prop-delay',
					( 0.65 + index * 0.08 ).toFixed( 2 ) + 's'
				);
			} );
		} );

		return heroes;
	}

	function runHeroAnimation( hero ) {
		markVisible( hero, 'is-hero-animated' );
		markVisible( hero, 'is-vip-section-ready' );
	}

	function initCategories() {
		var items = Array.prototype.slice.call(
			document.querySelectorAll( '.vip-categories__item' )
		);

		items.forEach( function ( item, index ) {
			item.style.setProperty( '--vip-cat-delay', ( index * 0.12 ).toFixed( 2 ) + 's' );
		} );

		return items;
	}

	function initReducedMotion( sections, heroes, categories, cards, faqVisuals ) {
		document.documentElement.classList.add( 'vip-motion-reduced' );

		sections.forEach( function ( el ) {
			markVisible( el, 'is-vip-section-ready' );
		} );
		heroes.forEach( function ( el ) {
			runHeroAnimation( el );
		} );
		categories.forEach( function ( el ) {
			markVisible( el, 'is-cat-animated' );
		} );
		cards.forEach( function ( el ) {
			markVisible( el, 'is-card-animated' );
		} );
		faqVisuals.forEach( function ( el ) {
			markVisible( el, 'is-faq-car-animated' );
		} );
	}

	onReady( function () {
		initLazyImages();

		var sections = Array.prototype.slice.call(
			document.querySelectorAll( '[data-vip-section]' )
		);
		var heroes = initHero();
		var categories = initCategories();
		var cards = initCards();
		var faqVisuals = Array.prototype.slice.call(
			document.querySelectorAll( '.vip-faq__visual' )
		);

		if ( REDUCED ) {
			initReducedMotion( sections, heroes, categories, cards, faqVisuals );
			return;
		}

		heroes.forEach( function ( hero ) {
			var rect = hero.getBoundingClientRect();
			var inView = rect.top < window.innerHeight && rect.bottom > 0;

			if ( inView ) {
				window.requestAnimationFrame( function () {
					window.requestAnimationFrame( function () {
						runHeroAnimation( hero );
					} );
				} );
			} else {
				observeOnce(
					[ hero ],
					{ root: null, rootMargin: '0px', threshold: 0.1 },
					runHeroAnimation
				);
			}
		} );

		observeOnce(
			sections.filter( function ( el ) {
				return ! el.classList.contains( 'vip-hero' );
			} ),
			{ root: null, rootMargin: '0px 0px -6% 0px', threshold: 0.05 },
			function ( el ) {
				markVisible( el, 'is-vip-section-ready' );
			}
		);

		observeOnce(
			categories,
			{ root: null, rootMargin: '0px 0px -8% 0px', threshold: 0.15 },
			function ( el ) {
				markVisible( el, 'is-cat-animated' );
			}
		);

		observeOnce(
			cards,
			{ root: null, rootMargin: '0px 0px -6% 0px', threshold: 0.08 },
			function ( el ) {
				var root =
					el.closest( '[data-vip-section]' ) ||
					el.closest( '.vip-fleet__grid' ) ||
					el.closest( '.vip-home' ) ||
					el.parentElement;
				var group = root
					? Array.prototype.slice.call( root.querySelectorAll( '.vip-animate-card' ) )
					: [ el ];
				var index = Math.max( 0, group.indexOf( el ) );
				el.style.transitionDelay = ( index * 0.08 ).toFixed( 2 ) + 's';
				markVisible( el, 'is-card-animated' );
			}
		);

		observeOnce(
			faqVisuals,
			{ root: null, rootMargin: '0px 0px -10% 0px', threshold: 0.2 },
			function ( el ) {
				markVisible( el, 'is-faq-car-animated' );
			}
		);
	} );
} )();
