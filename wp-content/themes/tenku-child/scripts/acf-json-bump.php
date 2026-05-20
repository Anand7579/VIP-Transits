<?php
/**
 * Bump ACF local JSON modified timestamps (run: php scripts/acf-json-bump.php)
 *
 * @package Tenku_Child
 */

$dir  = dirname( __DIR__ ) . '/acf-json';
$time = time();

foreach ( glob( $dir . '/*.json' ) as $file ) {
	$data = json_decode( (string) file_get_contents( $file ), true );
	if ( ! is_array( $data ) ) {
		echo basename( $file ) . ": INVALID JSON\n";
		continue;
	}
	$data['modified'] = $time;
	file_put_contents( $file, json_encode( $data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE ) . "\n" );
	echo basename( $file ) . ": modified set to {$time}\n";
}
