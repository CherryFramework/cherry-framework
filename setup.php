<?php

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
