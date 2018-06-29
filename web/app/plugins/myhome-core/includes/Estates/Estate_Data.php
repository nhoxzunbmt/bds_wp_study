<?php

namespace MyHomeCore\Estates;


use MyHomeCore\Attributes\Attribute_Factory;
use MyHomeCore\Attributes\Price_Attribute_Options_Page;

/**
 * Class Estate_Data
 * @package MyHomeCore\Estates
 */
class Estate_Data {

	/**
	 * @param Estate $estate
	 *
	 * @return array
	 */
	public static function get_data( $estate ) {
		$data = array(
			'id'                => $estate->get_ID(),
			'name'              => $estate->get_name(),
			'slug'              => $estate->get_slug(),
			'excerpt'           => $estate->get_short_excerpt(),
			'link'              => $estate->get_link(),
			'has_price'         => $estate->has_price(),
			'image_srcset'      => wp_get_attachment_image_srcset( get_post_thumbnail_id( $estate->get_ID() ), 'myhome-standard-xs' ),
			'attributes'        => $estate->get_attributes_data(),
			'address'           => $estate->get_address(),
			'days_ago'          => $estate->get_days_ago(),
			'is_featured'       => $estate->is_featured(),
			'offer_type'        => $estate->get_offer_type()->get_data(),
			'status'            => $estate->get_status(),
			'payment_status'    => $estate->get_payment_state(),
			'attribute_classes' => $estate->get_attribute_classes(),
			'gallery'           => $estate->get_gallery_data(),
			'date'              => get_the_date( '', $estate->get_post() )
		);

		if ( $data['has_price'] ) {
			$data['price'] = $estate->get_current_currency_prices_data();
		}

		return $data;
	}

	/**
	 * @param Estate $estate
	 *
	 * @return array
	 */
	public static function get_full_data( $estate ) {
		$plans = array();
		foreach ( $estate->get_plans() as $plan ) {
			$plans[] = $plan->get_data();
		}

		return array(
			'id'             => $estate->get_ID(),
			'post_title'     => $estate->get_name(),
			'post_content'   => $estate->get_description(),
			'slug'           => $estate->get_slug(),
			'excerpt'        => $estate->get_short_excerpt(),
			'link'           => $estate->get_link(),
			'prices'         => $estate->get_all_prices_data(),
			'image_id'       => intval( $estate->get_image_id() ),
			'image_srcset'   => wp_get_attachment_image_srcset( get_post_thumbnail_id( $estate->get_ID() ), 'myhome-standard-xs' ),
			'gallery'        => $estate->get_gallery(),
			'attributes'     => $estate->get_all_attributes_data(),
			'address'        => $estate->get_address(),
			'location'       => $estate->get_position(),
			'days_ago'       => $estate->get_days_ago(),
			'is_featured'    => $estate->is_featured(),
			'offer_type'     => $estate->get_offer_type()->get_data(),
			'status'         => $estate->get_status(),
			'plans'          => $plans,
			'video'          => $estate->get_video(),
			'virtual_tour'   => $estate->get_virtual_tour(),
			'payment_status' => $estate->get_payment_state()
		);
	}

	/**
	 * @param Estate $estate
	 *
	 * @return array
	 */
	public static function get_marker_data( $estate ) {
		return array(
			'id'       => $estate->get_ID(),
			'name'     => $estate->get_name(),
			'slug'     => $estate->get_slug(),
			'link'     => $estate->get_link(),
			'image'    => $estate->get_marker_image(),
			'position' => $estate->get_position(),
			'price'    => $estate->get_current_currency_prices_data()
		);
	}

}