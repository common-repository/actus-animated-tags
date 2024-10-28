<?php
/**
 * Actions that run during plugin activation
 *
 * Sets the default options
 *
 * @package    Actus_Animated_Tags
 */
 
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

update_option( 'ACTUS_ANIT_VERSION',    ACTUS_ANIT_VERSION );
add_option( 'ACTUS_ANIT_height',        250);
add_option( 'ACTUS_ANIT_density',       7);
add_option( 'ACTUS_ANIT_min_font_size', 30);
add_option( 'ACTUS_ANIT_max_font_size', 300);
add_option( 'ACTUS_ANIT_background',    'random' );