<?php
/**
 * Creator term
 *
 * @package    Cherry_Framework
 * @subpackage Class
 * @author     Cherry Team <cherryframework@gmail.com>
 * @copyright  Copyright (c) 2012 - 2016, Cherry Team
 * @link       http://www.cherryframework.com/
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * Cherry_Creator_Term class
 */
class Cherry_Creator_Term {

	/**
	 * Term title
	 *
	 * @var string
	 */
	private $title;

	/**
	 * Term taxonomy
	 *
	 * @var string
	 */
	private $taxonomy = 'category';

	/**
	 * Term arguments
	 *
	 * @var array
	 */
	private $arguments = array();

	/**
	 * Inserted term
	 *
	 * @var null
	 */
	private $inserted = null;

	/**
	 * Cherry_Creator_Term
	 *
	 * @param [type]   $title term title.
	 * @param [string] $tax   taxonomy.
	 * @param array    $args  arguments.
	 */
	public function __construct( $title, $tax = 'category', $args = array() ) {
		$this->title     = $title;
		$this->taxonomy  = $tax;
		$this->arguments = $args;
	}

	/**
	 * Insert term
	 *
	 * @return Cherry_Creator_Term
	 */
	public function insert( $unique = false ) {
		if ( ! is_array( $this->inserted ) ) {
			if ( $unique ) {
				if ( ! term_exists( $this->get_term_slug(), $this->taxonomy ) ) {
					$this->_insert();
				}
			} else {
				$this->_insert();
			}
		}
		return $this;
	}

	/**
	 * Insert term without checking
	 *
	 * @return Cherry_Creator_Term
	 */
	private function _insert() {
		$this->inserted = wp_insert_term(
			$this->title,
			$this->taxonomy,
			$this->arguments
		);
		return $this;
	}

	/**
	 * Set parent by slug
	 *
	 * @param [type] $parent_slug parent.
	 */
	public function set_parent_by_slug( $parent_slug = null ) {
		if ( null !== $parent_slug ) {
			$term = get_term_by( 'slug', $parent_slug, $this->taxonomy );
			if ( $term ) {
				$this->arguments['parent'] = $term->term_id;
			}
		}
		return $this;
	}

	/**
	 * Get inserted object
	 *
	 * @return mixed.
	 */
	public function get_inserted() {
		return $this->inserted;
	}

	/**
	 * Get term slug
	 *
	 * @return [type] term slug.
	 */
	public function get_term_slug() {
		if ( array_key_exists( 'slug', $this->arguments ) ) {
			return $this->arguments['slug'];
		}
		return sanitize_title( $this->title );
	}
}
