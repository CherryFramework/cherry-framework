<?php
/**
 * Class for the building ui-collection element.
 *
 * @package    Cherry_Framework
 * @subpackage Class
 * @author     Cherry Team <support@cherryframework.com>
 * @copyright  Copyright (c) 2012 - 2015, Cherry Team
 * @link       http://www.cherryframework.com/
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'UI_Collection' ) ) {

	/**
	 * UI_Collection class
	 * Example usage:
	 *
	 * New UI_Collection(
	 *  	array(
	 *  		'type'	  => 'collection',
	 *  		'id'      => 'gallery',
	 *  		'name'    => 'gallery',
	 *  		'left_label' => __( 'Gallery', 'cherry' ),
	 *  		'controls' => array(
	 *  			'UI_Text' => array(
	 *  				'type'    => 'text',
	 *  				'id'      => 'title',
	 *  				'class'   => 'large_text',
	 *  				'name'    => 'title',
	 *  				'value'   => '',
	 *  				'left_label' => __( 'Title', 'cherry' )
	 *  			),
	 *  			'UI_Media' => array(
	 *  				'id'      => 'image',
	 *  				'name'    => 'image',
	 *  				'value'   => '',
	 *  				'left_label' => __( 'Image', 'cherry' )
	 *  			),
	 *  		),
	 *  	)
	 *  );
	 */
	class UI_Collection extends UI_Element implements I_UI {
		/**
		 * Default settings
		 *
		 * @var array
		 */
		private $defaults_settings = array(
			'id'			=> 'collection',
			'name'			=> 'collection',
			'button_label'	=> 'Add',
			'controls'      => array(),
		);
		/**
		 * Constructor method for the UI_Text class.
		 *
		 * @since  4.0.0
		 */
		function __construct( $args = array() ) {
			$this->settings = wp_parse_args( $args, $this->defaults_settings );
		}

		/**
		 * Render html UI_Text.
		 *
		 * @since  4.0.0
		 */
		public function render() {
			$count = max( 1, $this->get_rows_count() );
			$rendered_controls = array();
			if ( is_array( $this->settings['controls'] ) && count( $this->settings['controls'] ) ) {
				for ( $i = 0; $i < $count; $i++ ) {
					foreach ( $this->settings['controls'] as $class => $args ) {
						if ( in_array( 'I_UI', class_implements( $class ) ) ) {
							$control      = new $class( $args );
							$control_old_name = $control->get_name();
							$control_name = sprintf(
								'%s[%s][]',
								$this->get_name(),
								$control->get_name()
							);
							$control->set_name( $control_name );
							$control_value = $this->get_control_value( $control_old_name, $i );
							$control->set_value( $control_value );
							$rendered_controls[ $i ][] = array(
								'args' => $args,
								'html' => $control->render(),
							);
						}
					}
				}
			}
			return Cherry_Toolkit::render_view(
				dirname( __FILE__ ) . '/views/collection.php',
				array(
					'rendered_controls' => $rendered_controls,
				)
			);
		}

		/**
		 * Get rows count
		 *
		 * @return [type] rows count.
		 */
		public function get_rows_count() {
			$count = 0;
			$value = $this->get_value();

			if ( is_array( $value ) && count( $value ) ) {
				foreach ( $value as $key => $v ) {
					if ( is_array( $v ) ) {
						$count = max( $count, count( $v ) );
					}
				}
			}
			return $count;
		}

		/**
		 * Get control value
		 *
		 * @param  [type] $control_name name.
		 * @param  [type] $row          row index.
		 * @return current value.
		 */
		public function get_control_value( $control_name, $row ) {
			$values = $this->get_value();
			if ( is_array( $values ) ) {
				if ( array_key_exists( $control_name, $values ) ) {
					$control_values = $values[ $control_name ];
					if ( array_key_exists( $row, $control_values ) ) {
						return $control_values[ $row ];
					}
				}
			}
			return '';
		}

		/**
		 * Enqueue javascript and stylesheet UI_Colection
		 *
		 * @since  4.0.0
		 */
		public static function enqueue_assets() {
			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'jquery-ui-sortable' );
			wp_enqueue_script(
				'collection',
				self::get_current_file_url( __FILE__ ) . '/assets/js/min/jquery.collection.min.js',
				array( 'jquery', 'jquery-ui-sortable' )
			);
			wp_enqueue_script(
				'ui-collection',
				self::get_current_file_url( __FILE__ ) . '/assets/js/min/ui-collection.min.js',
				array( 'jquery', 'jquery-ui-sortable', 'collection' )
			);
			wp_enqueue_style(
				'ui-collection',
				self::get_current_file_url( __FILE__ ) . '/assets/css/ui-collection.css',
				array(),
				'1.0.0',
				'all'
			);
		}
	}
}
