<?php

namespace MyHomeCore\Estates\Filters;

use MyHomeCore\Attributes\Price_Attribute;
use MyHomeCore\Terms\Term_Factory;


/**
 * Class Estate_Number_Filter
 * @package MyHomeCore\Estates
 */
class Estate_Number_Filter extends Estate_Filter {

	/**
	 * @return string
	 */
	public function get_type() {
		return self::POST_META;
	}

	/**
	 * @return array
	 */
	public function get_arg() {
		$meta_query = array( 'relation' => 'AND' );
		$key        = $this->attribute->get_slug();

		foreach ( $this->values as $attribute_value ) {
			$meta_query[] = array(
				'key'     => 'estate_attr_' . $key,
				'value'   => $attribute_value->get_slug(),
				'type'    => 'numeric',
				'compare' => $this->compare
			);
		}

		return $meta_query;
	}

}