<?php
/**
 * Blog / article helpers (listing + single).
 *
 * @package Tenku_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Default query args for article listings.
 *
 * @param array $overrides WP_Query overrides.
 * @return array
 */
function vip_transits_article_query_args( $overrides = array() ) {
	$defaults = array(
		'post_type'           => 'post',
		'post_status'         => 'publish',
		'posts_per_page'      => vip_transits_article_listing_posts_per_page(),
		'ignore_sticky_posts' => true,
		'orderby'             => 'date',
		'order'               => 'DESC',
	);

	return wp_parse_args( $overrides, $defaults );
}

/**
 * LATEST block: 5 posts per page (paginated).
 *
 * @return int
 */
function vip_transits_article_listing_posts_per_page() {
	return 5;
}

/**
 * TRENDING block: 4 most recent posts (shown on blog page 1 only).
 *
 * @return int
 */
function vip_transits_article_trending_count() {
	return 4;
}

/**
 * True on blog listing views only (never vehicle fleet).
 *
 * @return bool
 */
function vip_transits_is_blog_listing_request() {
	if ( function_exists( 'vip_transits_is_fleet_listing_view' ) && vip_transits_is_fleet_listing_view() ) {
		return false;
	}

	return is_home() || is_category() || is_tag() || is_author() || is_date() || is_post_type_archive( 'post' );
}

/**
 * Magazine layout for LATEST on all blog listing pages.
 *
 * @param int $paged Unused; kept for backwards compatibility.
 * @return bool
 */
function vip_transits_article_use_magazine_layout( $paged = 0 ) {
	unset( $paged );

	return true;
}

/**
 * Primary category for magazine cards.
 *
 * @param int $post_id Post ID.
 * @return string
 */
function vip_transits_get_article_primary_category( $post_id = 0 ) {
	$post_id = $post_id ? (int) $post_id : (int) get_the_ID();
	$terms   = get_the_category( $post_id );

	if ( empty( $terms ) || is_wp_error( $terms ) ) {
		return '';
	}

	return (string) $terms[0]->name;
}

/**
 * TRENDING row: the latest N blog posts (newest first, sitewide on main blog).
 *
 * @param array<int> $exclude_ids Unused; kept for template compatibility.
 * @param int        $limit       Max posts.
 * @param array      $context     Extra query args (category/tag archives).
 * @return WP_Post[]
 */
function vip_transits_get_trending_articles( $exclude_ids = array(), $limit = 0, $context = array() ) {
	unset( $exclude_ids );

	$limit = $limit > 0 ? (int) $limit : vip_transits_article_trending_count();

	$query = new WP_Query(
		vip_transits_article_query_args(
			array_merge(
				array(
					'posts_per_page'         => $limit,
					'orderby'                => 'date',
					'order'                  => 'DESC',
					'no_found_rows'          => true,
					'update_post_meta_cache' => false,
				),
				$context
			)
		)
	);

	if ( ! $query->have_posts() ) {
		return array();
	}

	$posts = array();
	$seen  = array();

	foreach ( $query->posts as $post_obj ) {
		if ( ! $post_obj instanceof WP_Post ) {
			continue;
		}
		$id = (int) $post_obj->ID;
		if ( isset( $seen[ $id ] ) ) {
			continue;
		}
		$seen[ $id ]  = true;
		$posts[] = $post_obj;
	}

	return $posts;
}

/**
 * Build blog listing query for LATEST pagination.
 *
 * @param int   $paged   Page number.
 * @param array $context cat, tag_id, author, etc.
 * @return WP_Query
 */
function vip_transits_get_blog_listing_query( $paged = 1, $context = array() ) {
	$paged = max( 1, (int) $paged );

	$args = vip_transits_article_query_args(
		array_merge(
			array( 'paged' => $paged ),
			$context
		)
	);

	return new WP_Query( $args );
}

/**
 * HTML for LATEST block (featured + 2x2 grid).
 *
 * @param array<WP_Post> $posts Up to 5 posts.
 * @return string
 */
function vip_transits_render_blog_latest_html( array $posts ) {
	$posts = array_values(
		array_filter(
			$posts,
			static function ( $post_obj ) {
				return $post_obj instanceof WP_Post;
			}
		)
	);

	if ( ! $posts ) {
		return '<p class="vip-articles__empty">' . esc_html__( 'No articles found.', 'tenku-child' ) . '</p>';
	}

	$featured   = $posts[0];
	$side_posts = array_slice( $posts, 1, 4 );

	ob_start();
	?>
	<div class="vip-articles__latest">
		<div class="vip-articles__latest-feature">
			<?php
			setup_postdata( $featured );
			get_template_part(
				'template-parts/article/card',
				null,
				array(
					'data'   => vip_transits_get_article_card_data( (int) $featured->ID ),
					'layout' => 'magazine',
					'size'   => 'featured',
				)
			);
			wp_reset_postdata();
			?>
		</div>
		<?php if ( $side_posts ) : ?>
			<ul class="vip-articles__latest-grid" role="list">
				<?php foreach ( $side_posts as $post_obj ) : ?>
					<li class="vip-articles__latest-grid-item" role="listitem">
						<?php
						setup_postdata( $post_obj );
						get_template_part(
							'template-parts/article/card',
							null,
							array(
								'data'   => vip_transits_get_article_card_data( (int) $post_obj->ID ),
								'layout' => 'magazine',
								'size'   => 'compact',
							)
						);
						wp_reset_postdata();
						?>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</div>
	<?php
	return (string) ob_get_clean();
}

/**
 * HTML for TRENDING row (4 newest posts).
 *
 * @param array $context Query context.
 * @return string
 */
function vip_transits_render_blog_trending_html( $context = array() ) {
	$trending_posts = vip_transits_get_trending_articles( array(), vip_transits_article_trending_count(), $context );

	if ( ! $trending_posts ) {
		return '<p class="vip-articles__empty vip-articles__empty--trending">' . esc_html__( 'No articles found.', 'tenku-child' ) . '</p>';
	}

	ob_start();
	?>
	<ul class="vip-articles__trending-grid" role="list">
		<?php foreach ( $trending_posts as $post_obj ) : ?>
			<li class="vip-articles__trending-grid-item" role="listitem">
				<?php
				setup_postdata( $post_obj );
				get_template_part(
					'template-parts/article/card',
					null,
					array(
						'data'   => vip_transits_get_article_card_data( (int) $post_obj->ID ),
						'layout' => 'magazine',
						'size'   => 'trending',
					)
				);
				wp_reset_postdata();
				?>
			</li>
		<?php endforeach; ?>
	</ul>
	<?php
	return (string) ob_get_clean();
}

/**
 * HTML for centered pagination below LATEST.
 *
 * @param WP_Query $query   Query.
 * @param int      $paged   Current page.
 * @param bool     $visible Show links.
 * @return string
 */
function vip_transits_render_blog_pagination_html( WP_Query $query, $paged = 1, $visible = true ) {
	if ( ! $visible || (int) $query->max_num_pages <= 1 && (int) $query->found_posts <= 5 ) {
		return '';
	}

	$links = paginate_links(
		array(
			'total'     => (int) $query->max_num_pages,
			'current'   => max( 1, (int) $paged ),
			'type'      => 'list',
			'prev_text' => '&laquo;',
			'next_text' => '&raquo; ' . __( 'Next', 'tenku-child' ),
		)
	);

	if ( ! $links ) {
		return '';
	}

	return '<nav class="vip-articles__pagination vip-articles__pagination--latest" aria-label="' . esc_attr__( 'Articles pagination', 'tenku-child' ) . '" data-vip-blog-pagination>' . wp_kses_post( $links ) . '</nav>';
}

/**
 * AJAX: LATEST page + pagination + TRENDING.
 */
function vip_transits_ajax_blog_listing() {
	check_ajax_referer( 'vip_blog_listing', 'nonce' );

	$paged   = max( 1, (int) ( $_POST['paged'] ?? 1 ) );
	$context = array();

	if ( ! empty( $_POST['cat'] ) ) {
		$context['cat'] = (int) $_POST['cat'];
	}
	if ( ! empty( $_POST['tag_id'] ) ) {
		$context['tag_id'] = (int) $_POST['tag_id'];
	}
	if ( ! empty( $_POST['author'] ) ) {
		$context['author'] = (int) $_POST['author'];
	}

	$query = vip_transits_get_blog_listing_query( $paged, $context );

	wp_send_json_success(
		array(
			'paged'      => $paged,
			'latest'     => vip_transits_render_blog_latest_html( $query->posts ),
			'pagination' => vip_transits_render_blog_pagination_html( $query, $paged, true ),
			'trending'   => vip_transits_render_blog_trending_html( $context ),
		)
	);
}
add_action( 'wp_ajax_vip_blog_listing', 'vip_transits_ajax_blog_listing' );
add_action( 'wp_ajax_nopriv_vip_blog_listing', 'vip_transits_ajax_blog_listing' );

/**
 * Inline AJAX blog pagination (no separate .js file).
 */
function vip_transits_print_blog_listing_script() {
	static $printed = false;

	if ( $printed || ! function_exists( 'vip_transits_is_blog_listing_request' ) || ! vip_transits_is_blog_listing_request() ) {
		return;
	}

	$printed = true;

	$context = array();
	if ( is_category() ) {
		$context['cat'] = (int) get_queried_object_id();
	} elseif ( is_tag() ) {
		$context['tag_id'] = (int) get_queried_object_id();
	} elseif ( is_author() ) {
		$context['author'] = (int) get_queried_object_id();
	}

	$config = array(
		'ajaxUrl' => admin_url( 'admin-ajax.php' ),
		'nonce'   => wp_create_nonce( 'vip_blog_listing' ),
		'context' => $context,
	);
	?>
	<script>
	window.vipBlogListing = <?php echo wp_json_encode( $config ); ?>;
	(function () {
		'use strict';
		var root = document.querySelector('[data-vip-blog-listing]');
		if (!root || typeof vipBlogListing === 'undefined') {
			return;
		}
		var latestEl = root.querySelector('[data-vip-blog-latest]');
		var trendingEl = root.querySelector('[data-vip-blog-trending]');
		var paginationEl = root.querySelector('[data-vip-blog-pagination]');
		function parseContext() {
			var raw = root.getAttribute('data-vip-blog-context') || '{}';
			try {
				return JSON.parse(raw);
			} catch (e) {
				return {};
			}
		}
		function getPageFromLink(link) {
			var href = link.getAttribute('href') || '';
			var match = href.match(/[?&]paged=(\d+)/i);
			if (match) {
				return parseInt(match[1], 10);
			}
			match = href.match(/\/page\/(\d+)\/?/i);
			if (match) {
				return parseInt(match[1], 10);
			}
			var num = parseInt(link.textContent.trim(), 10);
			if (!isNaN(num) && link.classList.contains('page-numbers')) {
				return num;
			}
			if (link.classList.contains('next')) {
				return parseInt(root.getAttribute('data-vip-blog-paged') || '1', 10) + 1;
			}
			if (link.classList.contains('prev')) {
				return Math.max(1, parseInt(root.getAttribute('data-vip-blog-paged') || '1', 10) - 1);
			}
			return 1;
		}
		function setLoading(on) {
			root.classList.toggle('is-loading', on);
			if (latestEl) {
				latestEl.setAttribute('aria-busy', on ? 'true' : 'false');
			}
			if (trendingEl) {
				trendingEl.setAttribute('aria-busy', on ? 'true' : 'false');
			}
		}
		function loadPage(paged) {
			paged = Math.max(1, parseInt(paged, 10) || 1);
			setLoading(true);
			var body = new FormData();
			body.append('action', 'vip_blog_listing');
			body.append('nonce', vipBlogListing.nonce);
			body.append('paged', String(paged));
			var ctx = vipBlogListing.context || parseContext();
			if (ctx.cat) {
				body.append('cat', String(ctx.cat));
			}
			if (ctx.tag_id) {
				body.append('tag_id', String(ctx.tag_id));
			}
			if (ctx.author) {
				body.append('author', String(ctx.author));
			}
			fetch(vipBlogListing.ajaxUrl, { method: 'POST', body: body, credentials: 'same-origin' })
				.then(function (res) { return res.json(); })
				.then(function (json) {
					if (!json.success || !json.data) {
						return;
					}
					if (latestEl && json.data.latest) {
						latestEl.innerHTML = json.data.latest;
					}
					if (paginationEl && json.data.pagination !== undefined) {
						if (json.data.pagination) {
							paginationEl.outerHTML = json.data.pagination;
							paginationEl = root.querySelector('[data-vip-blog-pagination]');
						} else if (paginationEl.parentNode) {
							paginationEl.parentNode.removeChild(paginationEl);
							paginationEl = null;
						}
					}
					if (trendingEl && json.data.trending) {
						trendingEl.innerHTML = json.data.trending;
						trendingEl.setAttribute('data-vip-blog-trending-ajax', '1');
					}
					root.setAttribute('data-vip-blog-paged', String(json.data.paged || paged));
					var latestSection = root.querySelector('.vip-articles__section--latest');
					if (latestSection) {
						latestSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
					}
				})
				.finally(function () {
					setLoading(false);
				});
		}
		root.addEventListener('click', function (e) {
			var link = e.target.closest('a.page-numbers');
			if (!link || !root.contains(link)) {
				return;
			}
			e.preventDefault();
			loadPage(getPageFromLink(link));
		});
		if (trendingEl && trendingEl.getAttribute('data-vip-blog-trending-ajax') === '1') {
			loadPage(parseInt(root.getAttribute('data-vip-blog-paged') || '2', 10));
		}
	})();
	</script>
	<?php
}
add_action( 'wp_footer', 'vip_transits_print_blog_listing_script', 20 );

/**
 * Blog archives: 5 posts per page (LATEST pagination).
 *
 * @param WP_Query $query Query.
 */
function vip_transits_article_archive_posts_per_page( $query ) {
	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}

	if ( $query->is_home() || $query->is_category() || $query->is_tag() || $query->is_author() || $query->is_date() ) {
		$query->set( 'posts_per_page', vip_transits_article_listing_posts_per_page() );
	}
}
add_action( 'pre_get_posts', 'vip_transits_article_archive_posts_per_page' );

/**
 * Card data for article grid items.
 *
 * @param int $post_id Post ID.
 * @return array<string, mixed>
 */
function vip_transits_get_article_card_data( $post_id = 0 ) {
	$post_id = $post_id ? (int) $post_id : (int) get_the_ID();
	$post    = get_post( $post_id );

	if ( ! $post || 'post' !== $post->post_type ) {
		return array();
	}

	$categories = get_the_category( $post_id );
	$cat_names  = array();

	if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) {
		foreach ( $categories as $term ) {
			$cat_names[] = $term->name;
		}
	}

	$excerpt = has_excerpt( $post_id ) ? get_the_excerpt( $post_id ) : wp_trim_words( wp_strip_all_tags( $post->post_content ), 22 );

	return array(
		'id'         => $post_id,
		'title'      => get_the_title( $post_id ),
		'permalink'  => get_permalink( $post_id ),
		'excerpt'    => $excerpt,
		'date'       => get_the_date( '', $post_id ),
		'date_iso'   => get_the_date( 'c', $post_id ),
		'thumbnail'  => get_the_post_thumbnail_url( $post_id, 'medium_large' ),
		'categories' => $cat_names,
		'category'   => vip_transits_get_article_primary_category( $post_id ),
		'author'     => get_the_author_meta( 'display_name', (int) $post->post_author ),
	);
}

/**
 * Masthead title on article archives (posts page title, e.g. "Blogs").
 *
 * @return string
 */
function vip_transits_get_article_masthead_title() {
	$posts_page_id = (int) get_option( 'page_for_posts' );
	if ( $posts_page_id ) {
		return get_the_title( $posts_page_id );
	}

	return __( 'Articles', 'tenku-child' );
}

/**
 * Title for the current article archive context.
 *
 * @return string
 */
function vip_transits_get_article_archive_title() {
	if ( is_category() ) {
		return single_cat_title( '', false );
	}

	if ( is_tag() ) {
		return single_tag_title( '', false );
	}

	if ( is_author() ) {
		return get_the_author();
	}

	if ( is_date() ) {
		if ( is_year() ) {
			return get_the_date( 'Y' );
		}
		if ( is_month() ) {
			return get_the_date( 'F Y' );
		}
		return get_the_date();
	}

	$posts_page_id = (int) get_option( 'page_for_posts' );
	if ( $posts_page_id ) {
		return get_the_title( $posts_page_id );
	}

	return __( 'Articles', 'tenku-child' );
}

/**
 * Subtitle / description for article archive header.
 *
 * @return string
 */
function vip_transits_get_article_archive_subtitle() {
	if ( is_category() || is_tag() ) {
		$description = term_description();
		if ( $description ) {
			return wp_strip_all_tags( $description );
		}
	}

	return __( 'Insights, guides, and news from VIP Transits.', 'tenku-child' );
}

/**
 * Build a unique HTML id for an article heading.
 *
 * @param string               $title    Heading text.
 * @param array<string, true>  $used_ids Already used ids (by reference).
 * @return string
 */
function vip_transits_article_heading_id( $title, array &$used_ids ) {
	$base = sanitize_title( $title );
	if ( $base === '' ) {
		$base = 'section';
	}

	$id = $base;
	$n  = 2;
	while ( isset( $used_ids[ $id ] ) ) {
		$id = $base . '-' . $n;
		++$n;
	}

	$used_ids[ $id ] = true;

	return $id;
}

/**
 * Add ids to h2 headings and build table of contents data.
 *
 * @param string $content Post content HTML (after the_content filters).
 * @return array{content:string,toc:array<int,array{id:string,title:string}>}
 */
function vip_transits_prepare_article_content( $content ) {
	$content = (string) $content;
	$toc     = array();

	if ( $content === '' || stripos( $content, '<h2' ) === false ) {
		return array(
			'content' => $content,
			'toc'     => $toc,
		);
	}

	$used_ids = array();

	// Match Gutenberg / classic h2 (may include nested markup inside the heading).
	$content = preg_replace_callback(
		'/<h2\b([^>]*)>([\s\S]*?)<\/h2>/i',
		static function ( $matches ) use ( &$toc, &$used_ids ) {
			$attrs = isset( $matches[1] ) ? (string) $matches[1] : '';
			$title = trim( wp_strip_all_tags( $matches[2] ) );

			if ( $title === '' ) {
				return $matches[0];
			}

			$id = '';
			if ( preg_match( '/\bid\s*=\s*["\']([^"\']+)["\']/i', $attrs, $id_match ) ) {
				$id = sanitize_title( $id_match[1] );
			}
			if ( $id === '' ) {
				$id = vip_transits_article_heading_id( $title, $used_ids );
			} else {
				$used_ids[ $id ] = true;
			}

			$toc[] = array(
				'id'    => $id,
				'title' => $title,
			);

			if ( preg_match( '/\bid\s*=/i', $attrs ) ) {
				return sprintf( '<h2%s>%s</h2>', $attrs, $matches[2] );
			}

			return sprintf( '<h2 id="%s"%s>%s</h2>', esc_attr( $id ), $attrs, $matches[2] );
		},
		$content
	);

	if ( ! is_string( $content ) ) {
		$content = '';
	}

	return array(
		'content' => $content,
		'toc'     => $toc,
	);
}

/**
 * Single-article view data.
 *
 * @param int $post_id Post ID.
 * @return array<string, mixed>
 */
function vip_transits_get_article_single_data( $post_id = 0 ) {
	$post_id = $post_id ? (int) $post_id : (int) get_the_ID();
	$post    = get_post( $post_id );

	if ( ! $post || 'post' !== $post->post_type ) {
		return array();
	}

	$card           = vip_transits_get_article_card_data( $post_id );
	$content_html   = apply_filters( 'the_content', $post->post_content );
	$content_parsed = vip_transits_prepare_article_content( $content_html );

	return array_merge(
		$card,
		array(
			'content'       => $content_parsed['content'],
			'toc'           => $content_parsed['toc'],
			'featured'      => get_the_post_thumbnail_url( $post_id, 'large' ),
			'blog_url'      => vip_transits_get_articles_page_url(),
			'category_list' => get_the_category_list( ', ', '', $post_id ),
		)
	);
}

/**
 * URL for the main articles / blog listing.
 *
 * @return string
 */
function vip_transits_get_articles_page_url() {
	$posts_page_id = (int) get_option( 'page_for_posts' );
	if ( $posts_page_id ) {
		return (string) get_permalink( $posts_page_id );
	}

	return (string) get_post_type_archive_link( 'post' );
}

/**
 * Render article listing grid + pagination.
 *
 * @param array $args {
 *     @type WP_Query|null $query          Query instance.
 *     @type bool          $show_pagination Show pagination. Default true.
 * }
 */
function vip_transits_render_article_listing( $args = array() ) {
	$args = wp_parse_args(
		$args,
		array(
			'query'            => null,
			'show_pagination'  => true,
			'layout'           => 'magazine',
			'paged'            => 0,
			'trending_context' => array(),
		)
	);

	$query = $args['query'];
	if ( ! $query instanceof WP_Query ) {
		global $wp_query;
		$query = $wp_query;
	}

	$paged = (int) $args['paged'];
	if ( $paged < 1 ) {
		$paged = max( 1, (int) $query->get( 'paged' ), (int) get_query_var( 'paged' ), (int) get_query_var( 'page' ) );
	}

	$use_magazine = 'magazine' === $args['layout'] && vip_transits_article_use_magazine_layout( $paged );

	get_template_part(
		'template-parts/article/listing',
		null,
		array(
			'query'            => $query,
			'show_pagination'  => (bool) $args['show_pagination'],
			'layout'           => $use_magazine ? 'magazine' : 'grid',
			'paged'            => $paged,
			'trending_context' => is_array( $args['trending_context'] ) ? $args['trending_context'] : array(),
		)
	);
}

/**
 * Inline TOC script (scroll spy, smooth scroll, active state). No separate .js file required.
 */
function vip_transits_print_article_toc_script() {
	static $printed = false;

	if ( $printed ) {
		return;
	}

	$printed = true;
	?>
	<script>
	(function () {
		'use strict';

		function initVipArticleToc() {
			var article = document.querySelector('.vip-article');
			if (!article) {
				return;
			}

			var tocRoot = article.querySelector('.vip-article__toc');
			if (!tocRoot) {
				return;
			}

			var accordion = tocRoot.querySelector('.vip-article__toc-accordion');
			var desktopMq = window.matchMedia('(min-width: 901px)');

			var mobileStickyOffset = 500;

			function syncTocAccordion() {
				if (!accordion) {
					return;
				}
				if (desktopMq.matches) {
					accordion.setAttribute('open', '');
					tocRoot.classList.remove('is-mobile-sticky-visible');
				} else {
					accordion.removeAttribute('open');
				}
			}

			function updateMobileStickyToc() {
				if (desktopMq.matches) {
					tocRoot.classList.remove('is-mobile-sticky-visible');
					return;
				}

				if (window.scrollY > mobileStickyOffset) {
					tocRoot.classList.add('is-mobile-sticky-visible');
				} else {
					tocRoot.classList.remove('is-mobile-sticky-visible');
					if (accordion) {
						accordion.removeAttribute('open');
					}
				}
			}

			syncTocAccordion();
			updateMobileStickyToc();
			if (desktopMq.addEventListener) {
				desktopMq.addEventListener('change', function () {
					syncTocAccordion();
					updateMobileStickyToc();
				});
			} else if (desktopMq.addListener) {
				desktopMq.addListener(function () {
					syncTocAccordion();
					updateMobileStickyToc();
				});
			}

			var links = tocRoot.querySelectorAll('a[href^="#"]');
			var sections = [];

			links.forEach(function (link) {
				var raw = (link.getAttribute('href') || '').replace(/^#/, '');
				if (!raw) {
					return;
				}
				var id = decodeURIComponent(raw);
				var target = document.getElementById(id);
				if (!target) {
					target = article.querySelector('#' + (window.CSS && CSS.escape ? CSS.escape(id) : id.replace(/[^\w-]/g, '\\$&')));
				}
				var item = link.closest('.vip-article__toc-item, li');
				if (!target || !item) {
					return;
				}
				sections.push({ id: id, target: target, item: item, link: link });
			});

			if (!sections.length) {
				return;
			}

			function setActive(id) {
				sections.forEach(function (section) {
					section.item.classList.toggle('is-active', section.id === id);
				});
			}

			function scrollOffset() {
				if (!desktopMq.matches && tocRoot.classList.contains('is-mobile-sticky-visible')) {
					return tocRoot.offsetHeight + 16;
				}

				var toc = article.querySelector('.vip-article__toc');
				var top = toc ? parseFloat(getComputedStyle(toc).top) || 24 : 24;
				return top + 24;
			}

			function prefersReducedMotion() {
				return window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
			}

			function scrollToTarget(target, smooth) {
				var y = target.getBoundingClientRect().top + window.pageYOffset - scrollOffset();
				window.scrollTo({
					top: y,
					behavior: smooth && !prefersReducedMotion() ? 'smooth' : 'auto'
				});
			}

			var clickLock = false;

			sections.forEach(function (section) {
				section.link.addEventListener('click', function (event) {
					event.preventDefault();
					clickLock = true;
					setActive(section.id);
					scrollToTarget(section.target, true);
					if (accordion && !desktopMq.matches) {
						accordion.removeAttribute('open');
					}
					if (history.replaceState) {
						history.replaceState(null, '', '#' + encodeURIComponent(section.id));
					}
					window.setTimeout(function () {
						clickLock = false;
					}, 700);
				});
			});

			function updateActiveFromScroll() {
				if (clickLock) {
					return;
				}
				var offset = scrollOffset();
				var current = sections[0].id;

				sections.forEach(function (section) {
					if (section.target.getBoundingClientRect().top <= offset) {
						current = section.id;
					}
				});

				setActive(current);
			}

			if ('IntersectionObserver' in window) {
				var visible = new Map();
				var observer = new IntersectionObserver(
					function (entries) {
						if (clickLock) {
							return;
						}
						entries.forEach(function (entry) {
							if (entry.isIntersecting) {
								visible.set(entry.target.id, entry.boundingClientRect.top);
							} else {
								visible.delete(entry.target.id);
							}
						});
						if (!visible.size) {
							updateActiveFromScroll();
							return;
						}
						var bestId = sections[0].id;
						var bestTop = Infinity;
						visible.forEach(function (top, id) {
							if (top < bestTop) {
								bestTop = top;
								bestId = id;
							}
						});
						setActive(bestId);
					},
					{ root: null, rootMargin: '-15% 0px -55% 0px', threshold: [0, 0.1, 0.5, 1] }
				);
				sections.forEach(function (section) {
					observer.observe(section.target);
				});
			}

			var ticking = false;
			window.addEventListener(
				'scroll',
				function () {
					if (!ticking) {
						window.requestAnimationFrame(function () {
							updateMobileStickyToc();
							updateActiveFromScroll();
							ticking = false;
						});
						ticking = true;
					}
				},
				{ passive: true }
			);

			var initialHash = decodeURIComponent((location.hash || '').replace(/^#/, ''));
			var hashSection = sections.find(function (s) { return s.id === initialHash; });
			if (hashSection) {
				setActive(hashSection.id);
				scrollToTarget(hashSection.target, false);
			} else {
				setActive(sections[0].id);
				updateActiveFromScroll();
			}
		}

		if (document.readyState === 'loading') {
			document.addEventListener('DOMContentLoaded', initVipArticleToc);
		} else {
			initVipArticleToc();
		}
	})();
	</script>
	<?php
}
