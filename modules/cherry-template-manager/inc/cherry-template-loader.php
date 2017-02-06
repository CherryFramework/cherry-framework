<?php
/**
 * Class for load templates.
 *
 * @package    Template_Manager
 * @author     Cherry Team <cherryframework@gmail.com>
 * @copyright  Copyright (c) 2012 - 2016, Cherry Team
 * @link       http://www.cherryframework.com/
 * @license    http://www.gnu.org/licenses/gpl-3.0.html
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Cherry_Template_Loader' ) ) {

	/**
	 * Class Cherry Template Loader.
	 *
	 * @since 1.0.0
	 */
	class Cherry_Template_Loader {

		/**
		 * A reference to an instance of this Cherry_Template_Manager class.
		 *
		 * @since 1.0.0
		 * @access private
		 * @var   object
		 */
		private $cherry_template_manager_class = null;

		/**
		 * Module arguments.
		 *
		 * @since  1.0.0
		 * @access private
		 * @var    array
		 */
		private $args = array(
			'template_dir' => 'templates/%1$s/%2$s.tmpl',
			'slug'         => null,
			'upload_dir'   => null,
		);

		/**
		 * Cherry_Template_Loader constructor.
		 *
		 * @since 1.0.0
		 * @access public
		 * @return void
		 */
		public function __construct( $args = array(), $main_class = null ) {

			$this->args = wp_parse_args(
				$args,
				$this->args
			);

			$this->cherry_template_manager_class = $main_class;

			$this->set_default_variable();
			$this->includes_file_system();

		}

		/**
		 * Set the default variables.
		 *
		 * @since  1.0.0
		 * @access private
		 * @return void
		 */
		private function set_default_variable() {
			if ( ! $this->args['slug'] ) {
				$this->args['slug'] = $this->get_slug();
			}

			if ( ! $this->args['upload_dir'] ) {
				$get_upload_dir = wp_upload_dir();
				$this->args['upload_dir'] = trailingslashit( $get_upload_dir['basedir'] );
			}
		}

		/**
		 * Function Include file with class WP_Filesystem.
		 *
		 * @since  1.0.0
		 * @access private
		 * @return void
		 */
		private function includes_file_system() {
			if ( ! function_exists( 'WP_Filesystem' ) ) {
				include_once( ABSPATH . '/wp-admin/includes/file.php' );
			}

			WP_Filesystem();
		}

		/**
		 * Return product slug.
		 *
		 * @since  1.0.0
		 * @access private
		 * @return string
		 */
		private function get_slug() {
			$file_dir    = wp_normalize_path( dirname( $this->cherry_template_manager_class->core->settings['extra_base_dir'] ) );
			$product_dir = $this->get_project_root();

			$slug = str_replace( $product_dir, '', $file_dir );
			preg_match( '/^[a-zA-Z-]*/' , $slug, $slug );

			return $slug[0];
		}

		/**
		 * Function return the project root dir, themes or plugins.
		 *
		 * @since  1.0.0
		 * @access private
		 * @return string
		 */
		private function get_project_root() {
			$themes_dir   = wp_normalize_path( get_theme_root() );
			$plugin_dir   = wp_normalize_path( WP_PLUGIN_DIR );
			$file_dir     = wp_normalize_path( dirname( $this->cherry_template_manager_class->core->settings['extra_base_dir'] ) );
			$project_root = ( false === strpos( $file_dir, $themes_dir ) ) ? $plugin_dir : $themes_dir;

			return trailingslashit( $project_root );
		}

		/**
		 * Retrieve a *.tmpl file content.
		 *
		 * @since  1.0.0
		 * @param  string $name  File name.
		 * @access private
		 * @return string|bool
		 */
		public function get_template_by_name( $name ) {
			$file         = '';
			$template_dir = sprintf( $this->args['template_dir'], $this->args['slug'], $name );
			$in_uploads   = $this->args['upload_dir'] . $template_dir ;
			$in_project   = trailingslashit( $this->get_project_root() . $this->args['slug'] ) . $template_dir;

			if ( file_exists( $in_uploads ) ) {
				$file = $in_uploads;
			} elseif ( $theme_template = locate_template( $template_dir ) ) {
				$file = $theme_template;
			} else {
				$file = $in_project;
			}

			if ( ! empty( $file ) ) {
				return $this->get_contents( $file );
			} else {
				return false;
			}
		}

		/**
		 * Read template (static).
		 *
		 * @since  1.0.0
		 * @since  1.0.1 - Use Cherry_Toolkit::get_file instead of WP Filesystem.
		 * @param  string $file Correct file path.
		 * @access public
		 * @return string|bool
		 */
		public function get_contents( $file ) {
			return Cherry_Toolkit::get_file( $file );
		}

		/**
		 * Returns argument.
		 *
		 * @since  1.0.0
		 * @param  string $argument_name Argument name.
		 * @access public
		 * @return object
		 */
		public function get_argument( $argument_name ) {
			if ( isset( $this->args[ $argument_name ] ) ) {
				return $this->args[ $argument_name ];
			} else {
				return;
			}
		}
	}
}
