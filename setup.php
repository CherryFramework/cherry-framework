<?php
/**
 * Setup function that used for checking latest version of the core.
 * It creates `$chery_core_version` global variable and writes the latest core version
 * and it's path into it.
 *
 * @package    Cherry_Framework
 * @author     Cherry Team <cherryframework@gmail.com>
 * @copyright  Copyright (c) 2012 - 2017, Cherry Team
 * @link       http://www.cherryframework.com/
 * @license    http://www.gnu.org/licenses/gpl-3.0.en.html
 */

return function () {
	global $chery_core_version;

	$path = trailingslashit( dirname( __FILE__ ) ) . 'cherry-core.php';

	$data = get_file_data( $path, array(
		'version' => 'Version'
	) );

	if ( isset( $data['version'] ) ) {
		$version = $data['version'];
	}

	$old_versions = null;

	if ( null !== $chery_core_version ) {
		$old_versions = array_keys( $chery_core_version );
	}

	if ( is_array( $old_versions ) && isset( $old_versions[0] ) ) {
		$compare = version_compare( $old_versions[0], $version, '<' );

		if ( $compare ) {
			$chery_core_version = array();
			$chery_core_version[ $version ] = $path;
		}
	} else {
		$chery_core_version = array();
		$chery_core_version[ $version ] = $path;
	}
};
