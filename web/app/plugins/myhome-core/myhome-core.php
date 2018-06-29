<?php
/*
Plugin Name: MyHome Core
Description: This plugin is a part of MyHome Theme. The minimum PHP version required is 5.6.0.
Version: 2.1.1
Plugin URI: https://myhometheme.net
*/

namespace MyHomeCore {

	define( 'MYHOME_CORE_PATH', basename( dirname( __FILE__ ) ) );
	define( 'MYHOME_CORE_DIR', __DIR__ );
	define( 'MYHOME_CORE_VIEWS', __DIR__ . '/views/' );

	spl_autoload_register(
		function ( $class_name ) {
			$path       = plugin_dir_path( __FILE__ ) . 'includes/';
			$class_name = str_replace( 'MyHomeCore\\', '', $class_name );
			$class_name = str_replace( '\\', '/', $class_name );
			$class      = $path . $class_name . '.php';

			if ( file_exists( $class ) ) {
				return require_once( $class );
			}
		}
	);

	function My_Home_Core() {
		return Core::get_instance();
	}

	$my_home_core = My_Home_Core();

	add_action( 'plugins_loaded', array( $my_home_core, 'init' ) );

	// Plugin activation
	register_activation_hook( __FILE__, array( $my_home_core, 'activation' ) );
}

namespace {
	if ( ! function_exists( 'wp_new_user_notification' ) ) :
		function wp_new_user_notification( $user_id, $deprecated = null, $notify = '' ) {
			return;
		}
	endif;
}
