<?php

namespace MyHomeCore\Terms;


use MyHomeCore\Attributes\Attribute;
use MyHomeCore\Attributes\Attribute_Factory;
use MyHomeCore\Attributes\Attribute_Value;
use MyHomeCore\Attributes\Attribute_Values;
use MyHomeCore\Common\Image;
use MyHomeCore\Components\Listing\Listing;
use MyHomeCore\Estates\Filters\Estate_Text_Filter;

/**
 * Class Term
 * @package MyHomeCore\Terms
 */
class Term {

	/**
	 * @var \WP_Term
	 */
	protected $term;

	/**
	 * Term constructor.
	 *
	 * @param \WP_Term $term
	 */
	public function __construct( \WP_Term $term ) {
		$this->term = $term;
	}

	/**
	 * @return int
	 */
	public function get_ID() {
		return $this->term->term_id;
	}

	/**
	 * @return string
	 */
	public function get_name() {
		return $this->term->name;
	}

	/**
	 * @return string
	 */
	public function get_slug() {
		return $this->term->slug;
	}

	/**
	 * @return int
	 */
	public function get_count() {
		return $this->term->count;
	}

	/**
	 * @return string|\WP_Error
	 */
	public function get_link() {
		return get_term_link( $this->term );
	}

	/**
	 * @return bool
	 */
	public function has_image() {
		$image = get_field( 'myhome_term_image', $this->term );

		return ! empty( $image );
	}

	/**
	 * @return array
	 */
	public function get_image() {
		return get_field( 'myhome_term_image', $this->term );
	}

	/**
	 * @return bool
	 */
	public function is_excluded_from_search() {
		$is_excluded = get_field( 'myhome_offer_type_exclude', $this->term );

		return ! empty( $is_excluded );
	}

	/**
	 * @return bool|int
	 */
	public function get_image_id() {
		$image = get_field( 'myhome_term_image', $this->term );
		if ( isset( $image['id'] ) ) {
			return intval( $image['id'] );
		}

		return false;
	}

	/**
	 * @param string $thumbnail
	 * @param string $class
	 */
	public function image( $thumbnail = 'square', $class = '' ) {
		$image = $this->get_image();
		Image::the_image( $image['id'], $thumbnail, $this->get_name(), $class );
	}

	/**
	 * @return bool
	 */
	public function has_image_wide() {
		$image_wide = get_field( 'myhome_term_image_wide', $this->term );

		return ! empty( $image_wide );
	}

	/*
	 * @return string
	 */
	public function get_image_wide() {
		$image_wide = get_field( 'myhome_term_image_wide', $this->term );
		if ( isset( $image_wide['url'] ) ) {
			return $image_wide['url'];
		}

		return '';
	}

	/**
	 * @return mixed
	 */
	public function get_after_price() {
		$after_price = get_field( 'myhome_offer_type_after_price', $this->term );

		return apply_filters( 'wpml_translate_single_string', $after_price, 'myhome-core', 'After price' );
	}

	/**
	 * @return mixed
	 */
	public function get_before_price() {
		$before_price = get_field( 'myhome_offer_type_before_price', $this->term );

		return apply_filters( 'wpml_translate_single_string', $before_price, 'myhome-core', 'Before price' );
	}

	/**
	 * @return bool
	 */
	public function specify_price() {
		$specify_price = get_field( 'myhome_offer_type_specify_price', $this->term );

		return is_null( $specify_price ) ? false : ! empty( $specify_price );
	}

	/**
	 * @return bool
	 */
	public function is_price_range() {
		$is_price_range = get_field( 'myhome_offer_type_is_price_range', $this->term );

		return ! empty( $is_price_range );
	}

	/**
	 * @return bool
	 */
	public function has_label() {
		$label = get_field( 'myhome_offer_type_label', $this->term );

		return is_null( $label ) || ! empty( $label );
	}

	/**
	 * @return mixed
	 */
	public function get_bg_color() {
		$bg_color = get_field( 'myhome_offer_type_label_bg', $this->term );

		if ( empty( $bg_color ) ) {
			$bg_color = \MyHomeCore\My_Home_Core()->settings->props['mh-color-primary']['color'];
		}

		return $bg_color;
	}

	/**
	 * @return mixed
	 */
	public function get_color() {
		$color = get_field( 'myhome_offer_type_label_color', $this->term );

		if ( empty( $color ) ) {
			$color = '#fff';
		}

		return $color;
	}

	/**
	 * @return bool|int
	 */
	public function get_image_wide_id() {
		$image_wide = get_field( 'myhome_term_image_wide', $this->term );
		if ( isset( $image_wide['id'] ) ) {
			return intval( $image_wide['id'] );
		}

		return false;
	}

	public function listing() {
		$settings = array(
			'show_advanced',
			'show_clear',
			'show_sort_by',
			'show_view_types',
			'search_form_advanced_number'
		);

		$atts = array(
			'lazy_loading'         => \MyHomeCore\My_Home_Core()->settings->props['mh-listing-lazy_loading'] ? 'true' : 'false',
			'lazy_loading_limit'   => \MyHomeCore\My_Home_Core()->settings->props['mh-listing-load_more_button_number'],
			'load_more_button'     => \MyHomeCore\My_Home_Core()->settings->props['mh-listing-load_more_button_label'],
			'listing_default_view' => \MyHomeCore\My_Home_Core()->settings->props['mh-listing-default_view'],
			'estates_per_page'     => \MyHomeCore\My_Home_Core()->settings->props['mh-listing-estates_limit'],
			'search_form_position' => \MyHomeCore\My_Home_Core()->settings->props['mh-listing-search_form_position'],
			'label'                => \MyHomeCore\My_Home_Core()->settings->props['mh-listing-label'],
			$this->term->taxonomy  => $this->get_slug()
		);

		foreach ( $settings as $opt ) {
			if ( isset( \MyHomeCore\My_Home_Core()->settings->props[ 'mh-listing-' . $opt ] ) ) {
				$atts[ $opt ] = intval( \MyHomeCore\My_Home_Core()->settings->props[ 'mh-listing-' . $opt ] );
			}
		}

		foreach ( Attribute_Factory::get_search() as $attribute ) {
			if ( isset( \MyHomeCore\My_Home_Core()->settings->props[ 'mh-listing-' . $attribute->get_slug() . '_show' ] ) ) {
				$atts[ $attribute->get_slug() . '_show' ] = ! empty( \MyHomeCore\My_Home_Core()->settings->props[ 'mh-listing-' . $attribute->get_slug() . '_show' ] );
			} else {
				$atts[ $attribute->get_slug() . '_show' ] = true;
			}
		}

		$atts[ $this->term->taxonomy . '_show' ] = false;

		$listing = new Listing( $atts );

		?>
		<div class="mh-listing--full-width mh-listing--horizontal-boxed">
			<?php $listing->display(); ?>
		</div>
		<?php
	}

	/**
	 * @return Estate_Text_Filter
	 */
	public function get_estate_filter() {
		$attribute        = Attribute_Factory::get_by_slug( $this->term->taxonomy );
		$attribute_values = new Attribute_Values( array( new Attribute_Value( $this->get_name(), $this->get_name(), $this->get_link(), $this->get_slug() ) ) );

		return new Estate_Text_Filter( $attribute, $attribute_values );
	}

	/**
	 * @return string
	 */
	public function get_description() {
		return $this->term->description;
	}

	/**
	 * @param int|null $term_id
	 *
	 * @return bool|Term
	 */
	public static function get_term( $term_id = null ) {
		if ( is_null( $term_id ) ) {
			$term_id = get_queried_object()->term_id;
		}
		$term = get_term( $term_id );

		if ( ! $term instanceof \WP_Term ) {
			return false;
		}

		return new Term( $term );
	}

	/**
	 * @param           $slug
	 * @param Attribute $attribute
	 *
	 * @return bool|Term
	 */
	public static function get_by_slug( $slug, $attribute ) {
		$wp_term = get_term_by( 'slug', $slug, $attribute->get_slug() );

		if ( ! $wp_term instanceof \WP_Term ) {
			return false;
		}

		return new Term( $wp_term );
	}

	/**
	 * @return \WP_Term
	 */
	public function get_wp_term() {
		return $this->term;
	}

	/**
	 * @return bool
	 */
	public function has_parent_term() {
		$attribute = Attribute_Factory::get_by_slug( $this->term->taxonomy );

		if ( $attribute == false ) {
			return false;
		}

		$parent_term_id = get_field( 'myhome_term_parent_' . $attribute->get_ID(), $this->term );

		return ! empty( $parent_term_id );
	}

	/**
	 * @return \WP_Term|\bool
	 */
	public function get_parent_term() {
		$attribute   = Attribute_Factory::get_by_slug( $this->term->taxonomy );
		$parent_term = get_field( 'myhome_term_parent_' . $attribute->get_ID(), $this->term );

		if ( ! $parent_term instanceof \WP_Term ) {
			return false;
		}

		return $parent_term;
	}

}