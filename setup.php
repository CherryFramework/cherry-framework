<?php
/**
 * Setup function that used for checking latest version of the core.
 * It creates `$__tm_version` global variable and writes the latest core version
 * and it's path into it.
 *
 * @package    Cherry_Framework
 * @author     Cherry Team <cherryframework@gmail.com>
 * @copyright  Copyright (c) 2012 - 2016, Cherry Team
 * @link       http://www.cherryframework.com/
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

return create_function( '', '
global $__tm_version;

$path = trailingslashit( __DIR__ ) . \'cherry-core.php\';

$data = get_file_data( $path, array(
  \'version\' => \'Version\'
) );

if ( isset( $data[\'version\'] ) ) {
  $version = $data[\'version\'];
}

$old_versions = null;

if ( null !== $__tm_version ) {
  $old_versions = array_keys( $__tm_version );
}

if ( is_array( $old_versions ) && isset( $old_versions[0] ) ) {
  $compare = version_compare( $old_versions[0], $version, \'<\' );

  if ( $compare ) {
    $__tm_version = array();
    $__tm_version[ $version ] = $path;
  }
} else {
  $__tm_version = array();
  $__tm_version[ $version ] = $path;
}
' );
