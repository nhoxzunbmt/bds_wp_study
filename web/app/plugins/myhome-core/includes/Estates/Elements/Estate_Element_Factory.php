<?php

namespace MyHomeCore\Estates\Elements;

use MyHomeCore\Estates\Estate;

/**
 * Class Estate_Element_Factory
 * @package MyHomeCore\Estates\Elements
 */
class Estate_Element_Factory {

	/**
	 * @param Estate $estate
	 *
	 * @return Estate_Element[]
	 */
	public static function get( Estate $estate ) {
		$elements      = array();
		$elements_list = get_option( Estate_Elements_Settings::OPTION_KEY );
		$elements_list = apply_filters( 'myhome_single_property_elements', $elements_list );

		if ( ! is_array( $elements_list ) ) {
			$elements_list = array();
		}

		foreach ( $elements_list as $element ) {
			$elements[] = Estate_Element::get_instance( $element, $estate );
		}

		return $elements;
	}

}