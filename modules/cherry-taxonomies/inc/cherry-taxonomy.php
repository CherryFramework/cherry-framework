<?php
/**
 * Custom taxonomy
 *
 * @package    Cherry_Framework
 * @subpackage Class
 * @author     Cherry Team <cherryframework@gmail.com>
 * @copyright  Copyright (c) 2012 - 2016, Cherry Team
 * @link       http://www.cherryframework.com/
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * Cherry_Taxonomy class
 */
class Cherry_Taxonomy {

	/**
	 * Single taxonomy name.
	 *
	 * @var null
	 */
	private $single = null;

	/**
	 * Taxonomy slug
	 *
	 * @var null
	 */
	private $slug = null;

	/**
	 * Post type slug
	 *
	 * @var null
	 */
	private $post_type_slug = null;

	/**
	 * Plural taxonomy name.
	 *
	 * @var null
	 */
	private $plural = null;

	/**
	 * Taxonomy arguments
	 *
	 * @var array
	 */
	private $arguments = array();

	/**
	 * Cherry taxonomy
	 *
	 * @param [type] $single         name.
	 * @param [type] $post_type_slug post type slug.
	 * @param [type] $plural         name.
	 * @param array  $args           arguments.
	 */
	public function __construct( $single, $post_type_slug = 'post', $plural = '', $args = array() ) {
		$this->set_single( $single );
		$this->set_plural( $plural );
		$this->set_post_type_slug( $post_type_slug );
		$this->set_slug();
		$this->set_arguments( $args );
	}

	/**
	 * Init actions
	 *
	 * @return Cherry_Taxonomy object
	 */
	public function init() {
		// Register Taxonomy
		add_action( 'init', array( &$this, 'register' ), 0 );
		return $this;
	}

	/**
	 * Set single property
	 *
	 * @param [type] $single property.
	 * @return Cherry_Taxonomy object
	 */
	public function set_single( $single ) {
		if ( '' !== $single ) {
			$this->single = $single;
		}
		return $this;
	}

	/**
	 * Get single property
	 *
	 * @return string property.
	 */
	public function get_single() {
		return $this->single;
	}

	/**
	 * Set plural property
	 *
	 * @param [type] $plural property.
	 * @return Cherry_Taxonomy object
	 */
	public function set_plural( $plural = '' ) {
		if ( '' != $plural ) {
			$this->plural = $plural;
		} else {
			$this->plural = $this->get_single() . 's';
		}
		return $this;
	}

	/**
	 * Get plural property
	 *
	 * @return string plural property.
	 */
	public function get_plural() {
		return $this->plural;
	}

	/**
	 * Set slug
	 *
	 * @param type string $slug taxonomy slug.
	 * @return Cherry_Taxonomy object
	 */
	public function set_slug( $slug = '' ) {
		if ( '' != $slug ) {
			$this->slug = $slug;
		} else {
			$slug       = $this->get_single();
			$slug       = strtolower( $slug );
			$slug       = str_replace( ' ', '_', $slug );
			$this->slug = $slug;
		}
		return $this;
	}

	/**
	 * Get slug taxonomy
	 *
	 * @return string taxonomy slug.
	 */
	public function get_slug() {
		return $this->slug;
	}

	/**
	 * Set post type slug
	 *
	 * @param type string $slug post types slug.
	 * @return Cherry_Taxonomy object
	 */
	public function set_post_type_slug( $slug = '' ) {
		if ( '' != $slug ) {
			$this->post_type_slug = $slug;
		} else {
			$this->post_type_slug = 'post';
		}
		return $this;
	}

	/**
	 * Get post type slug
	 *
	 * @return string post type slug.
	 */
	public function get_post_type_slug() {
		return $this->post_type_slug;
	}

	/**
	 * Set arguments
	 *
	 * @param array $args arguments.
	 */
	public function set_arguments( $args = array() ) {
		$this->arguments = array_merge( $this->arguments, (array) $args );
		return $this;
	}

	/**
	 * Get arguments
	 *
	 * @return array taxonomy arguments.
	 */
	public function get_arguments() {
		return (array) $this->arguments;
	}

	/**
	 * Triggered by the 'init' action event.
	 * Register a WordPress custom taxonomy.
	 *
	 * @return Cherry_Taxonomy object
	 */
	public function register() {
		register_taxonomy(
			$this->slug,
			$this->post_type_slug,
			$this->arguments
		);
		return $this;
	}
}
