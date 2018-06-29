<?php

namespace MyHomeCore\Components\Listing;


use MyHomeCore\Attributes\Attribute_Factory;
use MyHomeCore\Attributes\Text_Attribute;
use MyHomeCore\Common\Breadcrumbs\Breadcrumbs;

/**
 * Class Archive_Listing
 * @package MyHomeCore\Components\Listing
 */
class Archive_Listing {

	public function display() {
		$options                        = get_option( 'myhome_redux' );
		$show_advanced                  = is_null( $options['mh-listing-show_advanced'] ) ? true : intval( $options['mh-listing-show_advanced'] );
		$show_clear                     = is_null( $options['mh-listing-show_clear'] ) ? true : intval( $options['mh-listing-show_clear'] );
		$show_gallery                   = is_null( $options['mh-listing-show_gallery'] ) ? true : intval( $options['mh-listing-show_gallery'] );
		$show_sort_by                   = is_null( $options['mh-listing-show_sort_by'] ) ? true : intval( $options['mh-listing-show_sort_by'] );
		$show_view_types                = is_null( $options['mh-listing-show_view_types'] ) ? true : intval( $options['mh-listing-show_view_types'] );
		$advanced_number                = is_null( $options['mh-listing-search_form_advanced_number'] ) ? 3 : intval( $options['mh-listing-search_form_advanced_number'] );
		$show_results_number            = is_null( $options['mh-listing_show-results-number'] ) ? true : ! empty( $options['mh-listing_show-results-number'] );
		$listing_type                   = is_null( $options['mh-listing-type'] ) ? 'load_more' : $options['mh-listing-type'];
		$show_sort_by_newest            = ! isset( $options['mh-listing-show_sort_by_newest'] ) ? true : ! empty( $options['mh-listing-show_sort_by_newest'] );
		$show_sort_by_popular           = ! isset( $options['mh-listing-show_sort_by_popular'] ) ? true : ! empty( $options['mh-listing-show_sort_by_popular'] );
		$show_sort_by_price_high_to_low = ! isset( $options['mh-listing-show_sort_by_price_high_to_low'] ) ? true : ! empty( $options['mh-listing-show_sort_by_price_high_to_low'] );
		$show_sort_by_price_low_to_high = ! isset( $options['mh-listing-show_sort_by_price_low_to_high'] ) ? true : ! empty( $options['mh-listing-show_sort_by_price_low_to_high'] );
		$show_sort_by_alphabetically    = ! isset( $options['mh-listing-show_sort_by_alphabetically'] ) ? false : ! empty( $options['mh-listing-show_sort_by_alphabetically'] );
		$search_form                    = ! isset( $options['mh-listing-search_form'] ) ? 'default' : $options['mh-listing-search_form'];

		$atts = array(
			'lazy_loading'                   => $options['mh-listing-lazy_loading'] ? 'true' : 'false',
			'lazy_loading_limit'             => intval( $options['mh-listing-load_more_button_number'] ),
			'load_more_button'               => $options['mh-listing-load_more_button_label'],
			'listing_default_view'           => $options['mh-listing-default_view'],
			'estates_per_page'               => $options['mh-listing-estates_limit'],
			'search_form_position'           => $options['mh-listing-search_form_position'],
			'label'                          => $options['mh-listing-label'],
			'search_form_advanced_number'    => $advanced_number,
			'show_advanced'                  => $show_advanced ? 'true' : 'false',
			'show_clear'                     => $show_clear ? 'true' : 'false',
			'show_sort_by'                   => $show_sort_by ? 'true' : 'false',
			'show_view_types'                => $show_view_types ? 'true' : 'false',
			'show_gallery'                   => $show_gallery ? 'true' : 'false',
			'show_results_number'            => $show_results_number ? 'true' : 'false',
			'listing_type'                   => $listing_type,
			'show_sort_by_newest'            => $show_sort_by_newest ? 'true' : 'false',
			'show_sort_by_popular'           => $show_sort_by_popular ? 'true' : 'false',
			'show_sort_by_price_high_to_low' => $show_sort_by_price_high_to_low ? 'true' : 'false',
			'show_sort_by_price_low_to_high' => $show_sort_by_price_low_to_high ? 'true' : 'false',
			'show_sort_by_alphabetically'    => $show_sort_by_alphabetically ? 'true' : 'false',
			'search_form'                    => $search_form
		);

		foreach ( Attribute_Factory::get_search() as $attribute ) {
			if ( isset( \MyHomeCore\My_Home_Core()->settings->props[ 'mh-listing-' . $attribute->get_slug() . '_show' ] ) ) {
				$atts[ $attribute->get_slug() . '_show' ] = ! empty( \MyHomeCore\My_Home_Core()->settings->props[ 'mh-listing-' . $attribute->get_slug() . '_show' ] );
			} else {
				$atts[ $attribute->get_slug() . '_show' ] = true;
			}

			if ( is_tax( $attribute->get_slug() ) ) {
				$atts[ $attribute->get_slug() . '_show' ] = false;
			}

			if ( $attribute instanceof Text_Attribute ) {
				$breadcrumb_attributes = Breadcrumbs::get_attributes();
				$value                 = get_query_var( $attribute->get_slug(), '' );

				if ( empty( $value ) ) {
					continue;
				}

				foreach ( $breadcrumb_attributes as $attr ) {
					$attr_value = get_query_var( $attr->get_slug() );

					if ( empty( $attr_value ) ) {
						break;
					}

					if ( $attr->get_ID() == $attribute->get_ID() ) {
						$atts[ $attribute->get_slug() . '_show' ] = false;
					}
				}

				$atts[ $attribute->get_slug() ] = $value;
			}
		}

		$listing = new Listing( $atts );
		$listing->display();
	}

}