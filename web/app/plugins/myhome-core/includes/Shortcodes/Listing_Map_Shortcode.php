<?php

namespace MyHomeCore\Shortcodes;


use MyHomeCore\Components\Listing\Listing_Map_Settings;
use MyHomeCore\Components\Listing\Map_Listing;

/**
 * Class Listing_Map_Shortcode
 * @package MyHomeCore\Shortcodes
 */
class Listing_Map_Shortcode extends Shortcode {

	/**
	 * @param array $args
	 * @param string|null $content
	 *
	 * @return string
	 */
	public function display( $args = array(), $content = null ) {
		wp_enqueue_script( 'myhome-frontend' );
		wp_enqueue_script( 'google-maps-api' );
		wp_enqueue_script( 'google-maps-markerclusterer' );
		wp_enqueue_script( 'richmarker' );
		wp_enqueue_script( 'infobox' );

		$listing_map = new Map_Listing( $args );
		ob_start();
		$listing_map->display();

		return ob_get_clean();
	}

	/**
	 * @return array
	 */
	public function get_vc_params() {
		return Listing_Map_Settings::get_vc_settings();
	}

}