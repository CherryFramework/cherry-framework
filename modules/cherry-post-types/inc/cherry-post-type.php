<?php
/**
 * Custom post type.
 *
 * @package    Cherry_Framework
 * @subpackage Class
 * @author     Cherry Team <cherryframework@gmail.com>
 * @copyright  Copyright (c) 2012 - 2016, Cherry Team
 * @link       http://www.cherryframework.com/
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * Cherry_Post_Type class.
 *
 * @since 1.0.0
 */
class Cherry_Post_Type {

	/**
	 * Post type slug.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private $slug = '';

	/**
	 * Post type arguments.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	private $args = array();

	/**
	 * The registered custom post type.
	 *
	 * @since 1.0.0
	 * @var Object|\WP_Error
	 */
	private $post_type;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 * @param sring $slug Post type slug.
	 * @param array $args Post type arguments.
	 */
	public function __construct( $slug, $args ) {
		$this->slug = $slug;
		$this->args = $args;

		// Register post type
		add_action( 'init', array( $this, 'register' ) );
	}

	/**
	 * Triggered by the 'init' action event.
	 * Register a WordPress custom post type.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function register() {
		$this->post_type = register_post_type(
			$this->slug,
			$this->args
		);
	}
}
