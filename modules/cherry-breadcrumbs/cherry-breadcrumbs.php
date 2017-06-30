<?php
/**
 * Module Name: Breadcrumb Trail
 * Description: A breadcrumb menu script for WordPress
 * Author: Cherry Team
 * Author URI: http://www.cherryframework.com/
 * License: GPLv3
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * Breadcrumb Trail - A breadcrumb menu script for WordPress.
 *
 * Breadcrumb Trail is a script for showing a breadcrumb trail for any type of page.  It tries to
 * anticipate any type of structure and display the best possible trail that matches your site's
 * permalink structure.  While not perfect, it attempts to fill in the gaps left by many other
 * breadcrumb scripts.
 *
 * @package    Cherry_Framework
 * @subpackage Modules
 * @author     Cherry Team <support@cherryframework.com>, Justin Tadlock <justin@justintadlock.com>
 * @copyright  Copyright (c) 2008 - 2014, Justin Tadlock
 * @link       http://themehybrid.com/plugins/breadcrumb-trail
 * @link       http://www.cherryframework.com
 * @license    http://www.gnu.org/licenses/gpl-3.0.html
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Cherry_Breadcrumbs' ) ) {

	/**
	 * Breadcrumbs builder class.
	 * Class is based on Breadcrumb Trail plugin by Justin Tadlock.
	 *
	 * @since 1.0.0
	 */
	class Cherry_Breadcrumbs {

		/**
		 * A reference to an instance of this class.
		 *
		 * @since 1.0.0
		 * @var   object
		 */
		private static $instance = null;

		/**
		 * Core instance.
		 *
		 * @since 1.0.0
		 * @var   object
		 */
		public $core = null;

		/**
		 * Indexed array of breadcrumb trail items.
		 *
		 * @since 1.0.0
		 * @var   array
		 */
		public $items = array();

		/**
		 * Breadcrumb arguments.
		 *
		 * @since 1.0.0
		 * @var   array
		 */
		public $args = array();

		/**
		 * Page title.
		 *
		 * @since 1.0.0
		 * @var   array
		 */
		public $page_title = null;

		/**
		 * Breadcrumbs CSS.
		 *
		 * @since 1.0.0
		 * @var   array
		 */
		public $css = array(
			'module'    => 'cherry-breadcrumbs',
			'content'   => 'cherry-breadcrumbs_content',
			'wrap'      => 'cherry-breadcrumbs_wrap',
			'browse'    => 'cherry-breadcrumbs_browse',
			'item'      => 'cherry-breadcrumbs_item',
			'separator' => 'cherry-breadcrumbs_item_sep',
			'link'      => 'cherry-breadcrumbs_item_link',
			'target'    => 'cherry-breadcrumbs_item_target',
		);

		/**
		 * Check if Breadcrumbs class was extended.
		 *
		 * @since 1.0.0
		 * @var   boolean
		 */
		public $is_extend = false;

		/**
		 * Constructor of class.
		 */
		function __construct( $core, $args = array() ) {

			$this->core = $core;

			$defaults = array(
				'separator'         => '&#47;',
				'before'            => '',
				'after'             => '',
				'show_mobile'       => true,
				'show_tablet'       => true,
				'wrapper_format'    => '<div>%1$s</div><div>%2$s</div>',
				'page_title_format' => '<h1 class="page-title">%s</h1>',
				'item_format'       => '<div class="%2$s">%1$s</div>',
				'home_format'       => '<a href="%4$s" class="%2$s is-home" rel="home" title="%3$s">%1$s</a>',
				'link_format'       => '<a href="%4$s" class="%2$s" rel="tag" title="%3$s">%1$s</a>',
				'target_format'     => '<span class="%2$s">%1$s</span>',
				'show_on_front'     => true,
				'network'           => false,
				'show_title'        => true,
				'show_items'        => true,
				'show_browse'       => true,
				'echo'              => true,
				'labels'            => array(),
				'date_labels'       => array(),
				// Cherry team editing start
				'action'            => 'cherry_breadcrumbs_render',
				'css_namespace'     => array(),
				'path_type'         => 'full',
				// Cherry team editing end
				'post_taxonomy'     => apply_filters(
					'cherry_breadcrumbs_trail_taxonomies',
					array(
						'post'      => 'category',
					)
				),
			);

			$this->args = apply_filters( 'cherry_breadcrumb_args', wp_parse_args( $args, $defaults ) );

			// Cherry team editing start
			if ( isset( $this->args['css_namespace'] ) && ! empty( $this->args['css_namespace'] ) ) {
				$this->css = wp_parse_args( $this->args['css_namespace'], $this->css );
			}
			// Cherry team editing end
			if ( ! empty( $args['labels'] ) ) {
				$this->args['labels'] = wp_parse_args( $args['labels'], $this->default_labels() );
			} else {
				$this->args['labels'] = $this->default_labels();
			}

			if ( ! empty( $args['date_labels'] ) ) {
				$this->args['date_labels'] = wp_parse_args( $args['date_labels'], $this->default_date_labels() );
			} else {
				$this->args['date_labels'] = $this->default_date_labels();
			}

			if ( is_front_page() && false === $this->args['show_on_front'] ) {
				return;
			}

			$this->build_trail();

			if ( ! $this->is_extend ) {
				add_action( $this->args['action'], array( $this, 'get_trail' ) );
			}

		}

		/**
		 * Formats and outputs the breadcrumb trail.
		 *
		 * @since 1.0.0
		 */
		public function get_trail() {

			if ( is_front_page() && false === $this->args['show_on_front'] ) {
				return;
			}

			$breadcrumb = $this->get_items();
			$title      = $this->get_title();

			$wrapper_classes = array(
				esc_attr( $this->css['module'] )
			);

			if ( false === $this->args['show_mobile'] ) {
				$wrapper_classes[] = 'hidden-xs';
			}

			if ( false === $this->args['show_tablet'] ) {
				$wrapper_classes[] = 'hidden-sm';
			}

			$wrapper_classes = apply_filters( 'cherry_breadcrumbs_wrapper_classes', $wrapper_classes );
			$wrapper_classes = array_unique( $wrapper_classes );
			$wrapper_classes = array_map( 'sanitize_html_class', $wrapper_classes );
			$wrapper_css     = implode( ' ', $wrapper_classes );

			/* Open the breadcrumb trail containers. */
			$result = "\n\t\t" . '<div class="' . esc_attr( $wrapper_css ) . '">';

			$result .= sprintf( $this->args['wrapper_format'], $title, $breadcrumb );

			/* Close the breadcrumb trail containers. */
			$result .= "\n\t\t" . '</div>';

			if ( true === $this->args['echo'] ) {
				echo $result;
			} else {
				return $result;
			}

		}

		/**
		 * Get breacrumbs list items.
		 *
		 * @since 1.0.0
		 */
		public function get_items() {

			if ( false == $this->args['show_items'] ) {
				return;
			}

			$breadcrumb = '';

			/* Connect the breadcrumb trail if there are items in the trail. */
			if ( empty( $this->items ) && ! is_array( $this->items ) ) {
				return;
			}

			if ( is_front_page() && false === $this->args['show_on_front'] ) {
				return;
			}

			/* Make sure we have a unique array of items. */
			$this->items = array_unique( $this->items );

			if ( $this->args['before'] ) {
				$breadcrumb .= $this->args['before'];
			}

			/* Open the breadcrumb trail containers. */
			$breadcrumb .= "\n\t\t" . '<div class="' . esc_attr( $this->css['content'] ) . '">';

			/* Add 'browse' label if it should be shown. */
			if ( true === $this->args['show_browse'] && ! empty( $this->args['labels']['browse'] ) ) {
				$browse      = $this->prepare_label( $this->args['labels']['browse'] );
				$breadcrumb .= "\n\t\t\t" . '<div class="' . esc_attr( $this->css['browse'] ) . '">' . $browse . '</div> ';
			}

			$breadcrumb .= "\n\t\t" . '<div class="' . esc_attr( $this->css['wrap'] ) . '">';

			/* Format the separator. */
			$separator = ! empty( $this->args['separator'] )
				? $this->args['separator']
				: '/';

			$separator = $this->prepare_label( $separator );
			$separator = '<div class="' . esc_attr( $this->css['separator'] ) . '">' . $separator . '</div>';
			$separator = sprintf( $this->args['item_format'], $separator, $this->css['item'] );

			/* Join the individual trail items into a single string. */
			$breadcrumb .= join( "\n\t\t\t {$separator} ", $this->items );

			$breadcrumb .= "\n\t\t" . '</div>';

			$breadcrumb .= "\n\t\t" . '</div>';

			if ( $this->args['after'] ) {
				$breadcrumb .= $this->args['after'];
			}

			/* Allow developers to filter the breadcrumb trail HTML and page title. */
			$breadcrumb = apply_filters( 'cherry_breadcrumbs_trail', $breadcrumb, $this->args );

			return $breadcrumb;

		}

		/**
		 * Get page title.
		 *
		 * @since 1.0.0
		 */
		public function get_title() {

			if ( false == $this->args['show_title'] || ! $this->page_title ) {
				return;
			}

			$title = apply_filters(
				'cherry_page_title',
				sprintf( $this->args['page_title_format'], $this->page_title ), $this->args
			);

			return $title;

		}

		/**
		 * Returns an array of the default labels.
		 *
		 * @since 1.0.0
		 */
		public function default_date_labels() {
			$labels = array(
				'archive_minute_hour' => 'g:i a', // minute and hour archives time format
				'archive_minute'      => 'i', // minute archives time format
				'archive_hour'        => 'g a', // hour archives time format
				'archive_year'        => 'Y', // yearly archives date format
				'archive_month'       => 'F', // monthly archives date format
				'archive_day'         => 'j', // daily archives date format
				'archive_week'        => 'W', // weekly archives date format
			);

			return $labels;
		}

		/**
		 * Returns an array of the default labels.
		 *
		 * @since 1.0.0
		 */
		public function default_labels() {

			$labels = array(
				'browse'              => esc_html__( 'Browse:', 'cherry-framework' ),
				'home'                => $this->home_title(),
				'error_404'           => esc_html__( '404 Not Found', 'cherry-framework' ),
				'archives'            => esc_html__( 'Archives', 'cherry-framework' ),
				'search'              => esc_html__( 'Search results for &#8220;%s&#8221;', 'cherry-framework' ),
				'paged'               => esc_html__( 'Page %s', 'cherry-framework' ),
				'archive_minute'      => esc_html__( 'Minute %s', 'cherry-framework' ),
				'archive_week'        => esc_html__( 'Week %s', 'cherry-framework' ),

				/* "%s" is replaced with the translated date/time format. */
				'archive_minute_hour' => '%s',
				'archive_hour'        => '%s',
				'archive_day'         => '%s',
				'archive_month'       => '%s',
				'archive_year'        => '%s',
			);

			return $labels;
		}

		/**
		 * Returns home title
		 *
		 * @return string
		 */
		public function home_title() {

			$title            = esc_html__( 'Home', 'cherry-framework' );
			$use_custom_title = apply_filters( 'cherry_breadcrumbs_custom_home_title', true );

			if ( $use_custom_title ) {

				$page_on_front_id = get_option( 'page_on_front' );
				$page_title       = false;

				if ( $page_on_front_id ) {
					$page_title = get_the_title( $page_on_front_id );
				}

				if ( ! empty( $page_title ) ) {
					$title = $page_title;
				}
			}

			return $this->prepare_label( $title );
		}

		/**
		 * Build breadcrumbs trail items array.
		 *
		 * @since 1.0.0
		 */
		public function build_trail() {

			// Break trail building, if use custom trailing for this page
			$custom_trail = apply_filters( 'cherry_breadcrumbs_custom_trail', false, $this->args );
			if ( is_array( $custom_trail ) && ! empty( $custom_trail ) ) {
				$this->items      = $custom_trail['items'];
				$this->page_title = $custom_trail['page_title'];
				return;
			}

			if ( is_front_page() ) {

				// if we on front page
				$this->add_front_page();

			} else {

				// do this for all other pages
				$this->add_network_home_link();
				$this->add_site_home_link();

				// add blog page related items
				if ( is_home() ) {

					$this->add_blog_page();

				} elseif ( is_singular() ) {

					// add single page/post items
					$this->add_singular_items();

				} elseif ( is_archive() ) {

					// is is archive page
					if ( is_post_type_archive() ) {
						$this->add_post_type_archive_items();

					} elseif ( is_category() || is_tag() || is_tax() ) {
						$this->add_term_archive_items();

					} elseif ( is_author() ) {
						$this->add_user_archive_items();

					} elseif ( get_query_var( 'minute' ) && get_query_var( 'hour' ) ) {
						$this->add_minute_hour_archive_items();

					} elseif ( get_query_var( 'minute' ) ) {
						$this->add_minute_archive_items();

					} elseif ( get_query_var( 'hour' ) ) {
						$this->add_hour_archive_items();

					} elseif ( is_day() ) {
						$this->add_day_archive_items();

					} elseif ( get_query_var( 'w' ) ) {
						$this->add_week_archive_items();

					} elseif ( is_month() ) {
						$this->add_month_archive_items();

					} elseif ( is_year() ) {
						$this->add_year_archive_items();

					} else {
						$this->add_default_archive_items();

					}
				} elseif ( is_search() ) {
					/* If viewing a search results page. */
					$this->add_search_items();
				} elseif ( is_404() ) {
					/* If viewing the 404 page. */
					$this->add_404_items();
				}
			}

			/* Add paged items if they exist. */
			$this->add_paged_items();

			/**
			 * Filter final items array
			 *
			 * @since 1.0.0
			 * @since 1.1.5 Added 3rd parameter $this.
			 *
			 * @param array $this->items Current items array.
			 * @param array $this->args  Current instance arguments array.
			 * @param array $this        Current instance.
			 */
			$this->items = apply_filters( 'cherry_breadcrumbs_items', $this->items, $this->args, $this );

		}

		/**
		 * Add trail item int array.
		 *
		 * @since 1.0.0
		 * @since 1.1.5 $prepend parameter.
		 *
		 * @param string $format Item format to add.
		 * @param string $label  Item label.
		 * @param string $url    Item URL.
		 * @param string $class  Item CSS class.
		 */
		public function _add_item( $format = 'link_format', $label, $url = '', $class = '', $prepend = false ) {

			$title = esc_attr( wp_strip_all_tags( $label ) );
			$css   = ( 'target_format' == $format ) ? 'target' : 'link';

			if ( $class ) {
				$class .= ' ' . $this->css[ $css ];
			} else {
				$class = $this->css[ $css ];
			}

			$item   = sprintf( $this->args[ $format ], $label, $class, $title, $url );
			$result = sprintf( $this->args['item_format'], $item, esc_attr( $this->css['item'] ) );

			if ( true === $prepend ) {
				array_unshift( $this->items, $result );
			} else {
				$this->items[] = $result;
			}

			if ( 'target_format' == $format ) {
				$this->page_title = $label;
			}

		}

		/**
		 * Add front items based on $wp_rewrite->front.
		 *
		 * @since 1.0.0
		 */
		public function add_rewrite_front_items() {

			global $wp_rewrite;

			if ( $wp_rewrite->front ) {
				$this->add_path_parents( $wp_rewrite->front );
			}
		}

		/**
		 * Adds the page/paged number to the items array.
		 *
		 * @since  1.0.0
		 * @return void
		 */
		public function add_paged_items() {

			/* If viewing a paged singular post. */
			if ( is_singular() && 1 < get_query_var( 'page' ) ) {
				$label = sprintf(
					$this->args['labels']['paged'],
					number_format_i18n( absint( get_query_var( 'page' ) ) )
				);
				$this->_add_item( 'target_format', $label );
			} elseif ( is_paged() ) {
				/* If viewing a paged archive-type page. */
				$label = sprintf(
					$this->args['labels']['paged'],
					number_format_i18n( absint( get_query_var( 'paged' ) ) )
				);
				$this->_add_item( 'target_format', $label );
			}

		}

		/**
		 * Get parent posts by path. Currently, this method only supports getting parents of the 'page'
		 * post type.  The goal of this function is to create a clear path back to home given what would
		 * normally be a "ghost" directory.  If any page matches the given path, it'll be added.
		 *
		 * @since 1.0.0
		 *
		 * @param string $path The path (slug) to search for posts by.
		 */
		function add_path_parents( $path ) {

			/* Trim '/' off $path in case we just got a simple '/' instead of a real path. */
			$path = trim( $path, '/' );

			/* If there's no path, return. */
			if ( empty( $path ) ) {
				return;
			}

			// process default Cherry permalinks by own way
			if ( in_array( $path, array( 'tag', 'category' ) ) ) {
				return;
			}

			/* Get parent post by the path. */
			$post = get_page_by_path( $path );

			if ( ! empty( $post ) ) {
				$this->add_post_parents( $post->ID );
			} elseif ( is_null( $post ) ) {

				/* Separate post names into separate paths by '/'. */
				$path = trim( $path, '/' );
				preg_match_all( '/\/.*?\z/', $path, $matches );

				/* If matches are found for the path. */
				if ( isset( $matches ) ) {

					/* Reverse the array of matches to search for posts in the proper order. */
					$matches = array_reverse( $matches );

					/* Loop through each of the path matches. */
					foreach ( $matches as $match ) {

						/* If a match is found. */
						if ( isset( $match[0] ) ) {

							/* Get the parent post by the given path. */
							$path = str_replace( $match[0], '', $path );
							$post = get_page_by_path( trim( $path, '/' ) );

							/* If a parent post is found, set the $post_id and break out of the loop. */
							if ( ! empty( $post ) && 0 < $post->ID ) {
								$this->add_post_parents( $post->ID );
								break;
							}
						}
					}
				}
			}
		}

		/**
		 * Build front page breadcrumb items.
		 *
		 * @since 1.0.0
		 */
		public function add_front_page() {

			// do nothing if 'show_on_front' arg not true
			if ( true !== $this->args['show_on_front'] ) {
				return;
			}

			// always add network home link (if is multisite)
			$this->add_network_home_link();

			// if is paged front page - add home link
			if ( is_paged() || ( is_singular() && 1 < get_query_var( 'page' ) ) ) {

				$this->add_site_home_link();

			} else {

				$label = ( is_multisite() && true === $this->args['network'] )
							? get_bloginfo( 'name' )
							: $this->args['labels']['home'];

				$this->_add_item( 'target_format', $label );
			}

		}

		/**
		 * Add network home link for multisite.
		 * Check if is multisite and add link to parent site to breadcrumb items.
		 *
		 * @since 1.0.0
		 */
		public function add_network_home_link() {

			// do nothing if network support diasabled in args
			if ( true !== $this->args['network'] ) {
				return;
			}

			if ( ! is_multisite() ) {
				return;
			}

			if ( ! is_main_site() ) {
				return;
			}

			$url   = esc_url( network_home_url() );
			$label = $this->args['labels']['home'];

			$this->_add_item( 'home_format', $label, $url );

		}

		/**
		 * Add site home link.
		 * Add site home link if is paged front page.
		 *
		 * @since 1.0.0
		 */
		public function add_site_home_link() {

			$format = ( is_multisite() && ! is_main_site() && true === $this->args['network'] )
						? 'link_format'
						: 'home_format';

			$url   = esc_url( home_url( '/' ) );
			$label = ( is_multisite() && ! is_main_site() && true === $this->args['network'] )
						? get_bloginfo( 'name' )
						: $this->args['labels']['home'];

			$this->_add_item( $format, $label, $url );
		}

		/**
		 * Add blog page breadcrumbs item.
		 *
		 * @since 1.0.0
		 */
		public function add_blog_page() {

			// Get the post ID and post.
			$post_id = get_queried_object_id();
			$post    = get_page( $post_id );

			// If the post has parents, add them to the trail.
			if ( 0 < $post->post_parent ) {
				$this->add_post_parents( $post->post_parent );
			}

			$url   = get_permalink( $post_id );
			$label = get_the_title( $post_id );

			if ( is_paged() ) {
				$this->_add_item( 'link_format', $label, $url );
			} elseif ( $label ) {
				$this->_add_item( 'target_format', $label );
			}

		}

		/**
		 * Adds singular post items to the items array.
		 *
		 * @since 1.0.0
		 */
		public function add_singular_items() {

			/* Get the queried post. */
			$post    = get_queried_object();
			$post_id = get_queried_object_id();

			/* If the post has a parent, follow the parent trail. */
			if ( 0 < $post->post_parent ) {
				$this->add_post_parents( $post->post_parent );
			} else {
				/* If the post doesn't have a parent, get its hierarchy based off the post type. */
				$this->add_post_hierarchy( $post_id );
			}

			/* Display terms for specific post type taxonomy if requested. */
			$this->add_post_terms( $post_id );

			$post_title = single_post_title( '', false );

			/* End with the post title. */
			if ( $post_title ) {

				if ( 1 < get_query_var( 'page' ) ) {

					$url   = get_permalink( $post_id );
					$label = $post_title;

					$this->_add_item( 'link_format', $label, $url );

				}

				$label = $post_title;
				$this->_add_item( 'target_format', $label );

			}
		}

		/**
		 * Adds a post's terms from a specific taxonomy to the items array.
		 *
		 * @since 1.0.0
		 *
		 * @param int $post_id The ID of the post to get the terms for.
		 */
		public function add_post_terms( $post_id ) {

			if ( 'minified' == $this->args['path_type'] ) {
				return;
			}

			/* Get the post type. */
			$post_type = get_post_type( $post_id );

			/* Add the terms of the taxonomy for this post. */
			if ( ! empty( $this->args['post_taxonomy'][ $post_type ] ) ) {

				$post_terms = wp_get_post_terms( $post_id, $this->args['post_taxonomy'][ $post_type ] );

				if ( is_array( $post_terms ) && isset( $post_terms[0] ) && is_object( $post_terms[0] ) ) {
					$term_id = $post_terms[0]->term_id;
					$this->add_term_parents( $term_id, $this->args['post_taxonomy'][ $post_type ] );
				}
			}
		}

		/**
		 * Gets post types by slug.  This is needed because the get_post_types() function doesn't exactly
		 * match the 'has_archive' argument when it's set as a string instead of a boolean.
		 *
		 * @since 1.0.0
		 *
		 * @param int $slug The post type archive slug to search for.
		 */
		public function get_post_types_by_slug( $slug ) {

			$return = array();

			$post_types = get_post_types( array(), 'objects' );

			foreach ( $post_types as $type ) {

				if ( $slug === $type->has_archive || ( true === $type->has_archive && $slug === $type->rewrite['slug'] ) ) {
					$return[] = $type;
				}
			}

			return $return;
		}

		/**
		 * Adds the items to the trail items array for taxonomy term archives.
		 *
		 * @since 1.0.0
		 */
		public function add_term_archive_items() {

			global $wp_rewrite;

			/* Get some taxonomy and term variables. */
			$term     = get_queried_object();
			$taxonomy = get_taxonomy( $term->taxonomy );

			/* If there are rewrite rules for the taxonomy. */
			if ( false !== $taxonomy->rewrite ) {

				$post_type_catched = false;

				/* If 'with_front' is true, dd $wp_rewrite->front to the trail. */
				if ( $taxonomy->rewrite['with_front'] && $wp_rewrite->front ) {
					$this->add_rewrite_front_items();
				}

				/* Get parent pages by path if they exist. */
				$this->add_path_parents( $taxonomy->rewrite['slug'] );

				/* Add post type archive if its 'has_archive' matches the taxonomy rewrite 'slug'. */
				if ( $taxonomy->rewrite['slug'] ) {

					$slug = trim( $taxonomy->rewrite['slug'], '/' );

					/**
					 * Deals with the situation if the slug has a '/' between multiple strings. For
					 * example, "movies/genres" where "movies" is the post type archive.
					 */
					$matches = explode( '/', $slug );

					/* If matches are found for the path. */
					if ( isset( $matches ) ) {

						/* Reverse the array of matches to search for posts in the proper order. */
						$matches = array_reverse( $matches );

						/* Loop through each of the path matches. */
						foreach ( $matches as $match ) {

							/* If a match is found. */
							$slug = $match;

							/* Get public post types that match the rewrite slug. */
							$post_types = $this->get_post_types_by_slug( $match );

							if ( ! empty( $post_types ) ) {

								$post_type_object = $post_types[0];

								$url  = get_post_type_archive_link( $post_type_object->name );
								/* Add support for a non-standard label of 'archive_title' (special use case). */
								$label = ! empty( $post_type_object->labels->archive_title )
											? $post_type_object->labels->archive_title
											: $post_type_object->labels->name;

								/* Add the post type archive link to the trail. */
								$this->_add_item( 'link_format', $label, $url );

								$post_type_catched = true;
								/* Break out of the loop. */
								break;
							}
						}
					}
				}

				/* Add the post type archive link to the trail for custom post types */
				if ( ! $post_type_catched ) {
					$post_type = isset( $taxonomy->object_type[0] ) ? $taxonomy->object_type[0] : false;

					if ( $post_type && 'post' != $post_type ) {
						$post_type_object = get_post_type_object( $post_type );

						$url  = get_post_type_archive_link( $post_type_object->name );
						/* Add support for a non-standard label of 'archive_title' (special use case). */
						$label = ! empty( $post_type_object->labels->archive_title )
									? $post_type_object->labels->archive_title
									: $post_type_object->labels->name;

						$this->_add_item( 'link_format', $label, $url );

					}
				}
			}

			/* If the taxonomy is hierarchical, list its parent terms. */
			if ( is_taxonomy_hierarchical( $term->taxonomy ) && $term->parent ) {
				$this->add_term_parents( $term->parent, $term->taxonomy );
			}

			$label = single_term_title( '', false );

			/* Add the term name to the trail end. */
			if ( is_paged() ) {

				$url = esc_url( get_term_link( $term, $term->taxonomy ) );
				$this->_add_item( 'link_format', $label, $url );

			}

			$this->_add_item( 'target_format', $label );

		}

		/**
		 * Adds the items to the trail items array for post type archives.
		 *
		 * @since 1.0.0
		 */
		public function add_post_type_archive_items() {

			/* Get the post type object. */
			$post_type_object = get_post_type_object( get_query_var( 'post_type' ) );

			if ( ! empty( $post_type_object ) && false !== $post_type_object->rewrite ) {

				/* If 'with_front' is true, add $wp_rewrite->front to the trail. */
				if ( ! empty( $post_type_object->rewrite ) && $post_type_object->rewrite['with_front'] ) {
					$this->add_rewrite_front_items();
				}
			}

			/* Add the post type [plural] name to the trail end. */
			if ( is_paged() ) {

				$url   = esc_url( get_post_type_archive_link( $post_type_object->name ) );
				$label = post_type_archive_title( '', false );

				$this->_add_item( 'link_format', $label, $url );

			}

			$label = post_type_archive_title( '', false );
			$this->_add_item( 'target_format', $label );

		}

		/**
		 * Adds the items to the trail items array for user (author) archives.
		 *
		 * @since 1.0.0
		 */
		public function add_user_archive_items() {
			global $wp_rewrite;

			/* Add $wp_rewrite->front to the trail. */
			$this->add_rewrite_front_items();

			/* Get the user ID. */
			$user_id = get_query_var( 'author' );

			/* If $author_base exists, check for parent pages. */
			if ( ! empty( $wp_rewrite->author_base ) ) {
				$this->add_path_parents( $wp_rewrite->author_base );
			}

			$label = get_the_author_meta( 'display_name', $user_id );

			/* Add the author's display name to the trail end. */
			if ( is_paged() ) {

				$url = esc_url( get_author_posts_url( $user_id ) );
				$this->_add_item( 'link_format', $label, $url );

			}

			$this->_add_item( 'target_format', $label );

		}

		/**
		 * Adds the items to the trail items array for minute + hour archives.
		 *
		 * @since 1.0.0
		 */
		public function add_minute_hour_archive_items() {

			/* Add $wp_rewrite->front to the trail. */
			$this->add_rewrite_front_items();

			/* Add the minute + hour item. */
			$label = sprintf(
				$this->args['labels']['archive_minute_hour'],
				get_the_time( $this->args['date_labels']['archive_minute_hour'] )
			);
			$this->_add_item( 'target_format', $label );

		}

		/**
		 * Adds the items to the trail items array for minute archives.
		 *
		 * @since 1.0.0
		 */
		public function add_minute_archive_items() {

			/* Add $wp_rewrite->front to the trail. */
			$this->add_rewrite_front_items();

			/* Add the minute item. */
			$label = sprintf(
				$this->args['labels']['archive_minute'],
				get_the_time( $this->args['date_labels']['archive_minute'] )
			);

			$this->_add_item( 'target_format', $label );

		}

		/**
		 * Adds the items to the trail items array for hour archives.
		 *
		 * @since 1.0.0
		 */
		public function add_hour_archive_items() {

			/* Add $wp_rewrite->front to the trail. */
			$this->add_rewrite_front_items();

			/* Add the hour item. */
			$label = sprintf(
				$this->args['labels']['archive_hour'],
				get_the_time( $this->args['date_labels']['archive_hour'] )
			);
			$this->_add_item( 'target_format', $label );

		}

		/**
		 * Adds the items to the trail items array for day archives.
		 *
		 * @since 1.0.0
		 */
		public function add_day_archive_items() {

			/* Add $wp_rewrite->front to the trail. */
			$this->add_rewrite_front_items();

			/* Get year, month, and day. */
			$year = sprintf(
				$this->args['labels']['archive_year'],
				get_the_time( $this->args['date_labels']['archive_year'] )
			);

			$month = sprintf(
				$this->args['labels']['archive_month'],
				get_the_time( $this->args['date_labels']['archive_month'] )
			);

			$day = sprintf(
				$this->args['labels']['archive_day'],
				get_the_time( $this->args['date_labels']['archive_day'] )
			);

			/* Add the year and month items. */
			$this->_add_item(
				'link_format',
				$year,
				get_year_link( get_the_time( 'Y' ) )
			);

			$this->_add_item(
				'link_format',
				$month,
				get_month_link( get_the_time( 'Y' ), get_the_time( 'm' ) )
			);

			/* Add the day item. */
			if ( is_paged() ) {

				$this->_add_item(
					'link_format',
					$day,
					get_day_link( get_the_time( 'Y' ), get_the_time( 'm' ), get_the_time( 'd' ) )
				);

			}

			$this->_add_item( 'target_format', $day );

		}

		/**
		 * Adds the items to the trail items array for week archives.
		 *
		 * @since 1.0.0
		 */
		public function add_week_archive_items() {

			/* Add $wp_rewrite->front to the trail. */
			$this->add_rewrite_front_items();

			/* Get the year and week. */
			$year = sprintf(
				$this->args['labels']['archive_year'],
				get_the_time( $this->args['date_labels']['archive_year'] )
			);
			$week = sprintf(
				$this->args['labels']['archive_week'],
				get_the_time( $this->args['date_labels']['archive_week'] )
			);

			/* Add the year item. */
			$this->_add_item(
				'link_format',
				$year,
				get_year_link( get_the_time( 'Y' ) )
			);

			/* Add the week item. */
			if ( is_paged() ) {

				$this->_add_item(
					'link_format',
					$week,
					add_query_arg(
						array(
							'm' => get_the_time( 'Y' ),
							'w' => get_the_time( 'W' ),
						),
						esc_url( home_url( '/' ) )
					)
				);

			}

			$this->_add_item( 'target_format', $week );

		}

		/**
		 * Adds the items to the trail items array for month archives.
		 *
		 * @since 1.0.0
		 */
		public function add_month_archive_items() {

			/* Add $wp_rewrite->front to the trail. */
			$this->add_rewrite_front_items();

			/* Get the year and month. */
			$year  = sprintf(
				$this->args['labels']['archive_year'],
				get_the_time( $this->args['date_labels']['archive_year'] )
			);
			$month = sprintf(
				$this->args['labels']['archive_month'],
				get_the_time( $this->args['date_labels']['archive_month'] )
			);

			/* Add the year item. */
			$this->_add_item(
				'link_format',
				$year,
				get_year_link( get_the_time( 'Y' ) )
			);

			/* Add the month item. */
			if ( is_paged() ) {
				$this->_add_item(
					'link_format',
					$month,
					get_month_link( get_the_time( 'Y' ), get_the_time( 'm' ) )
				);

			}

			$this->_add_item( 'target_format', $month );
		}

		/**
		 * Adds the items to the trail items array for year archives.
		 *
		 * @since 1.0.0
		 */
		public function add_year_archive_items() {

			/* Add $wp_rewrite->front to the trail. */
			$this->add_rewrite_front_items();

			/* Get the year. */
			$year = sprintf(
				$this->args['labels']['archive_year'],
				get_the_time( $this->args['date_labels']['archive_year'] )
			);

			/* Add the year item. */
			if ( is_paged() ) {
				$this->_add_item(
					'link_format',
					$year,
					get_year_link( get_the_time( 'Y' ) )
				);
			}

			$this->_add_item( 'target_format', $year );

		}

		/**
		 * Adds the items to the trail items array for archives that don't have a more specific method
		 * defined in this class.
		 *
		 * @since 1.0.0
		 */
		public function add_default_archive_items() {

			/* If this is a date-/time-based archive, add $wp_rewrite->front to the trail. */
			if ( is_date() || is_time() ) {
				$this->add_rewrite_front_items();
			}

			$this->_add_item( 'target_format', $this->args['labels']['archives'] );

		}

		/**
		 * Adds the items to the trail items array for search results.
		 *
		 * @since 1.0.0
		 */
		public function add_search_items() {

			$label = sprintf( $this->args['labels']['search'], get_search_query() );

			if ( is_paged() ) {
				$url   = get_search_link();
				$this->_add_item( 'link_format', $label, $url );
			} else {
				$this->_add_item( 'target_format', $label );
			}
		}

		/**
		 * Adds the items to the trail items array for 404 pages.
		 *
		 * @since  1.0.0
		 * @return void
		 */
		public function add_404_items() {

			$this->_add_item( 'target_format', $this->args['labels']['error_404'] );

		}

		/**
		 * Add post parents link to breadcrumbs items
		 *
		 * @since 1.0.0
		 *
		 * @param int $post_id first parent post ID.
		 */
		public function add_post_parents( $post_id ) {

			$parents = array();

			while ( $post_id ) {

				$url   = get_permalink( $post_id );
				$label = get_the_title( $post_id );
				$title = esc_attr( $label );

				$item = sprintf(
					$this->args['link_format'],
					$label, $this->css['link'], $title, $url
				);

				$parents[] = sprintf( $this->args['item_format'], $item, esc_attr( $this->css['item'] ) );

				$post = get_post( $post_id );
				// If there's no longer a post parent, break out of the loop.
				if ( 0 >= $post->post_parent ) {
					break;
				}

				// Change the post ID to the parent post to continue looping.
				$post_id = $post->post_parent;
			}

			// Get the post hierarchy based off the final parent post.
			$this->add_post_hierarchy( $post_id );
			$this->add_post_terms( $post_id );

			// Merge the parent items into the items array.
			$this->items = array_merge( $this->items, array_reverse( $parents ) );
		}

		/**
		 * Adds a specific post's hierarchy to the items array.
		 * The hierarchy is determined by post type's
		 * rewrite arguments and whether it has an archive page.
		 *
		 * @since 1.0.0
		 *
		 * @param  int $post_id The ID of the post to get the hierarchy for.
		 * @return void
		 */
		public function add_post_hierarchy( $post_id ) {

			if ( 'minified' == $this->args['path_type'] ) {
				return;
			}

			// Get the post type.
			$post_type        = get_post_type( $post_id );
			$post_type_object = get_post_type_object( $post_type );

			// If this is the 'post' post type, get the rewrite front items and map the rewrite tags.
			if ( 'post' === $post_type ) {
				// Get permalink specific breadcrumb items
				$this->add_rewrite_front_items();
				$this->map_rewrite_tags( $post_id, get_option( 'permalink_structure' ) );
			} elseif ( false !== $post_type_object->rewrite ) {
				// Add post type specific items
				if ( isset( $post_type_object->rewrite['with_front'] ) && $post_type_object->rewrite['with_front'] ) {
					$this->add_rewrite_front_items();
				}
			}

			/* If there's an archive page, add it to the trail. */
			if ( ! empty( $post_type_object->has_archive ) ) {

				$url = get_post_type_archive_link( $post_type );
				/* Add support for a non-standard label of 'archive_title' (special use case). */
				$label = ! empty( $post_type_object->labels->archive_title )
							? $post_type_object->labels->archive_title
							: $post_type_object->labels->name;

				$this->_add_item( 'link_format', $label, $url );
			}
		}

		/**
		 * Searches for term parents of hierarchical taxonomies.
		 * This function is similar to the WordPress function get_category_parents() but handles any type of taxonomy.
		 *
		 * @since 1.0.0
		 *
		 * @param  int    $term_id  ID of the term to get the parents of.
		 * @param  string $taxonomy Name of the taxonomy for the given term.
		 */
		function add_term_parents( $term_id, $taxonomy ) {

			if ( 'minified' == $this->args['path_type'] ) {
				return;
			}

			/* Set up some default arrays. */
			$parents = array();

			/* While there is a parent ID, add the parent term link to the $parents array. */
			while ( $term_id ) {

				/* Get the parent term. */
				$term = get_term( $term_id, $taxonomy );

				$url   = get_term_link( $term_id, $taxonomy );
				$label = $term->name;
				$title = esc_attr( $label );

				$item = sprintf(
					$this->args['link_format'],
					$label, $this->css['link'], $title, $url
				);

				$parents[] = sprintf( $this->args['item_format'], $item, esc_attr( $this->css['item'] ) );

				/* Set the parent term's parent as the parent ID. */
				$term_id = $term->parent;
			}

			/* If we have parent terms, reverse the array to put them in the proper order for the trail. */
			if ( ! empty( $parents ) ) {
				$this->items = array_merge( $this->items, array_reverse( $parents ) );
			}
		}

		/**
		 * Service function to process single tag item.
		 *
		 * @param  string $tag     Single tag.
		 * @param  int    $post_id Processed post ID.
		 */
		function _process_single_tag( $tag, $post_id ) {

			global $post;

			/* Trim any '/' from the $tag. */
			$tag = trim( $tag, '/' );

			/* If using the %year% tag, add a link to the yearly archive. */
			if ( '%year%' == $tag ) {

				$url   = get_year_link( get_the_time( 'Y', $post_id ) );
				$label = sprintf(
					$this->args['labels']['archive_year'],
					get_the_time( $this->args['date_labels']['archive_year'] )
				);

				$this->_add_item( 'link_format', $label, $url );

			/* If using the %monthnum% tag, add a link to the monthly archive. */
			} elseif ( '%monthnum%' == $tag ) {

				$url   = get_month_link( get_the_time( 'Y', $post_id ), get_the_time( 'm', $post_id ) );
				$label = sprintf(
					$this->args['labels']['archive_month'],
					get_the_time( $this->args['date_labels']['archive_month'] )
				);

				$this->_add_item( 'link_format', $label, $url );

			/* If using the %day% tag, add a link to the daily archive. */
			} elseif ( '%day%' == $tag ) {

				$url   = get_day_link(
					get_the_time( 'Y', $post_id ), get_the_time( 'm', $post_id ), get_the_time( 'd', $post_id )
				);
				$label = sprintf(
					$this->args['labels']['archive_day'],
					get_the_time( $this->args['date_labels']['archive_day'] )
				);

				$this->_add_item( 'link_format', $label, $url );

			/* If using the %author% tag, add a link to the post author archive. */
			} elseif ( '%author%' == $tag ) {

				$url   = get_author_posts_url( $post->post_author );
				$label = get_the_author_meta( 'display_name', $post->post_author );

				$this->_add_item( 'link_format', $label, $url );

			/* If using the %category% tag, add a link to the first category archive to match permalinks. */
			} elseif ( '%category%' == $tag ) {

				/* Force override terms in this post type. */
				$this->args['post_taxonomy'][ $post->post_type ] = false;

				/* Get the post categories. */
				$terms = get_the_category( $post_id );

				/* Check that categories were returned. */
				if ( $terms ) {

					/* Sort the terms by ID and get the first category. */
					if ( function_exists( 'wp_list_sort' ) ) {
						$terms = wp_list_sort( $terms, array(
							'term_id' => 'ASC',
						) );

					} else {

						// Backward compatibility with WordPress 4.6 or later.
						usort( $terms, '_usort_terms_by_ID' );
					}

					$term = get_term( $terms[0], 'category' );

					/* If the category has a parent, add the hierarchy to the trail. */
					if ( 0 < $term->parent ) {
						$this->add_term_parents( $term->parent, 'category' );
					}

					$url   = get_term_link( $term, 'category' );
					$label = $term->name;

					$this->_add_item( 'link_format', $label, $url );
				}
			}
		}


		/**
		 * Turns %tag% from permalink structures into usable links for the breadcrumb trail.
		 * This feels kind of hackish for now because we're checking for specific %tag% examples and only doing
		 * it for the 'post' post type. In the future, maybe it'll handle a wider variety of possibilities,
		 * especially for custom post types.
		 *
		 * @since 1.0.0
		 *
		 * @param  int    $post_id ID of the post whose parents we want.
		 * @param  string $path    Path of a potential parent page.
		 */
		public function map_rewrite_tags( $post_id, $path ) {

			/* Get the post based on the post ID. */
			$post = get_post( $post_id );

			/* If no post is returned, an error is returned, or the post does not have a 'post' post type, return. */
			if ( empty( $post ) || is_wp_error( $post ) || 'post' !== $post->post_type ) {
				return;
			}

			/* Trim '/' from both sides of the $path. */
			$path = trim( $path, '/' );

			/* Split the $path into an array of strings. */
			$matches = explode( '/', $path );

			/* If matches are found for the path. */
			if ( ! is_array( $matches ) ) {
				return;
			}

			/* Loop through each of the matches, adding each to the $trail array. */
			foreach ( $matches as $match ) {
				$this->_process_single_tag( $match, $post_id );
			}
		}

		/**
		 * Try to escape font icon from passed label and return it if found, or return label
		 *
		 * @since  1.0.0
		 * @param  string      $label    Passed label.
		 * @param  bool|string $fallback Optional fallback text to add inside icon tag.
		 * @return string
		 */
		public function prepare_label( $label, $fallback = false ) {

			$prefix = 'icon:';

			// Return simple text label if icon not found
			if ( false === strpos( $label, $prefix ) ) {
				return $label;
			}

			$label = str_replace( $prefix, '', $label );
			$label = str_replace( '.', '', $label );

			if ( false !== $fallback ) {
				$fallback = sprintf(
					apply_filters( 'cherry_breadcrumbs_icon_fallback', '<span class="hidden">%s</span>', $fallback ),
					esc_attr( $fallback )
				);
			}

			// Check if is Font Awesome icon
			if ( 0 === strpos( $label, 'fa-' ) ) {
				return sprintf( '<i class="fa %1$s">%2$s</i>', esc_attr( $label ), $fallback );
			}

			// Return default icon
			return sprintf(
				apply_filters( 'cherry_breadcrumbs_default_icon_format', '<span class="%1$s">%2$s</span>', $label, $fallback ),
				esc_attr( $label ), $fallback
			);

		}

		/**
		 * Returns the instance.
		 *
		 * @since  1.0.0
		 * @return object
		 */
		public static function get_instance( $core, $args ) {

			// If the single instance hasn't been set, set it now.
			if ( null == self::$instance ) {
				self::$instance = new self( $core, $args );
			}

			return self::$instance;
		}
	}
}
