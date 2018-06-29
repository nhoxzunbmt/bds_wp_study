<?php

namespace MyHomeCore\Estates\Filters;


use MyHomeCore\Estates\Prices\Currencies;
use MyHomeCore\Terms\Term;

/**
 * Class Estate_Price_Filter
 * @package MyHomeCore\Estates
 */
class Estate_Price_Filter extends Estate_Filter {

	/**
	 * @var Term[]
	 */
	private $selected_offer_types = array();

	/**
	 * @param $offer_types Term[]
	 */
	public function set_selected_offer_types( $offer_types ) {
		$this->selected_offer_types = $offer_types;
	}

	/**
	 * @return array
	 */
	public function get_arg() {
		$estate_ids = array();

		$currency = Currencies::get_current();

		foreach ( $this->values as $attribute_value ) {
			foreach ( $currency->get_price_keys( $this->selected_offer_types ) as $price_key ) {
				if ( $this->compare == '>=' && strpos( '_to', $price_key ) !== false ) {
					continue;
				} else if ( $this->compare == '<=' && strpos( '_from', $price_key ) !== false ) {
					continue;
				} else {
					$compare = $this->compare;
				}

				$args = array(
					'post_type'      => 'estate',
					'fields'         => 'ids',
					'posts_per_page' => - 1,
					'meta_query'     => array(
						'relation' => 'AND',
						array(
							'key'     => 'estate_attr_' . $price_key,
							'value'   => array( '', '0' ),
							'compare' => 'NOT IN'
						),
						array(
							'key'     => 'estate_attr_' . $price_key,
							'value'   => $attribute_value->get_slug(),
							'type'    => 'numeric',
							'compare' => $compare
						)
					)
				);

				$wp_query   = new \WP_Query( $args );
				$estate_ids = array_merge( $estate_ids, array_values( $wp_query->posts ) );
			}
		}

		return $estate_ids;
	}

	/**
	 * @return string
	 */
	public function get_type() {
		return self::PRICE;
	}

}