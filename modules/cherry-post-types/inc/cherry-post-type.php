<?php
/**
 * Custom post type
 *
 * @package    Cherry_Framework
 * @subpackage Class
 * @author     Cherry Team <cherryframework@gmail.com>
 * @copyright  Copyright (c) 2012 - 2016, Cherry Team
 * @link       http://www.cherryframework.com/
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * Cherry_Post_Type class
 */
class Cherry_Post_Type {

	/**
	 * Post type slug
	 *
	 * @var null
	 */
	private $slug = null;

	/**
	 * Post type arguments
	 *
	 * @var null
	 */
	private $args = null;

	/**
	 * The registered custom post type.
	 *
	 * @var Object|\WP_Error
	 */
	private $post_type;

	/**
	 * Font awesome icon name.
	 *
	 * @var null
	 */
	private $icon = null;

	/**
	 * Cherry Post Type Builder class constructor
	 *
	 * @param [type] $slug post type slug.
	 * @param [type] $args post type arguments.
	 */
	public function __construct( $slug, $args ) {

		$this->slug = $slug;
		$this->args = $args;

		// Register post type
		add_action( 'init', array( &$this, 'register' ) );
	}

	/**
	 * Triggered by the 'init' action event.
	 * Register a WordPress custom post type.
	 *
	 * @return void
	 */
	public function register() {
		$this->post_type = register_post_type(
			$this->slug,
			$this->args
		);
	}

	/**
	 * Add font awesome icon to menu
	 *
	 * @param  [type] $icon font awesome icon code.
	 * @return boolen true if succes | false if not.
	 */
	public function font_awesome_icon( $icon = '' ) {
		if ( '' === $icon ) {
			return false;
		}

		$this->icon = $icon;

		add_action( 'admin_enqueue_scripts', array( &$this, 'load_font_awesome' ) );

		return true;
	}

	/**
	 * Load font awesome fonts to admin menu.
	 *
	 * @return void
	 */
	public function load_font_awesome() {
		wp_enqueue_style(
			'font-awesome',
			'//maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css'
		);

		?>
		<style type='text/css' media='screen'>
			#adminmenu .menu-icon-<?php echo $this->slug; ?> div.wp-menu-image:before {
				font-family: Fontawesome !important;
				content: '\<?php echo $this->icon; ?>';
			}
		</style>
		<?php
	}
}
