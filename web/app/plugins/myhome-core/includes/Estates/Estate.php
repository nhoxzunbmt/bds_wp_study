<?php

namespace MyHomeCore\Estates;


use MyHomeCore\Attributes\Attribute_Factory;
use MyHomeCore\Attributes\Number_Attribute;
use MyHomeCore\Attributes\Offer_Type_Attribute;
use MyHomeCore\Attributes\Price_Attribute;
use MyHomeCore\Attributes\Price_Attribute_Options_Page;
use MyHomeCore\Attributes\Property_Type_Attribute;
use MyHomeCore\Attributes\Text_Area_Attribute;
use MyHomeCore\Attributes\Text_Attribute;
use MyHomeCore\Common\Attachment;
use MyHomeCore\Common\Plan;
use MyHomeCore\Estates\Prices\Currencies;
use MyHomeCore\Estates\Prices\Currency_Prices;
use MyHomeCore\Estates\Prices\Price;
use MyHomeCore\Estates\Prices\Prices;
use MyHomeCore\Terms\Term_Factory;
use MyHomeCore\Users\User;

/**
 * Class Estate
 * @package MyHomeCore\Estates
 */
class Estate {

	const SHORT_EXCERPT_LIMIT = 125;
	const LONG_EXCERPT_LIMIT = 160;

	/**
	 * @var \WP_Post
	 */
	private $post;

	/**
	 * @var Estate_Attribute[]
	 */
	private $attributes = array();

	/**
	 * @var Prices
	 */
	private $prices;

	/**
	 * Estate constructor.
	 *
	 * @param \WP_Post $post
	 */
	public function __construct( \WP_Post $post ) {
		$this->post   = $post;
		$this->prices = new Prices( $this );
	}

	/**
	 * @return \WP_Post
	 */
	public function get_post() {
		return $this->post;
	}

	/**
	 * @param int $post_id
	 *
	 * @return Estate
	 * @throws \ErrorException
	 */
	public static function get_instance( $post_id ) {
		$post = get_post( $post_id );
		if ( ! ( $post instanceof \WP_Post ) ) {
			throw new \ErrorException( die( 'Post with id ' . $post_id . ' not found' ) );
		}

		return new Estate( $post );
	}

	/**
	 * @return array
	 */
	public function get_data() {
		return Estate_Data::get_data( $this );
	}

	/**
	 * @return array
	 */
	public function get_full_data() {
		return Estate_Data::get_full_data( $this );
	}

	/**
	 * @return array
	 */
	public function get_marker_data() {
		return Estate_Data::get_marker_data( $this );
	}

	/**
	 * @return Estate_Attribute[]
	 */
	public function get_attributes() {
		if ( ! empty( $this->attributes ) ) {
			return $this->attributes;
		}

		foreach ( Attribute_Factory::get() as $attribute ) {
			if ( $attribute->is_estate_attribute() ) {
				$this->attributes[] = new Estate_Attribute( $attribute, $this->post->ID );
			}
		}

		return $this->attributes;
	}

	/**
	 * @return array
	 */
	public function get_all_attributes_data() {
		$attributes = array();

		foreach ( Attribute_Factory::get() as $attribute ) {
			if ( $attribute instanceof Text_Attribute || $attribute instanceof Number_Attribute || $attribute instanceof Text_Area_Attribute || $attribute instanceof Offer_Type_Attribute ) {
				$attr         = new Estate_Attribute( $attribute, $this->post->ID );
				$attributes[] = $attr->get_data();
			}
		}

		return $attributes;
	}

	/**
	 * @return array
	 */
	public function get_attributes_data() {
		$attributes = array();

		foreach ( $this->get_attributes() as $estate_Attribute ) {
			$attributes[] = $estate_Attribute->get_data();
		}

		return $attributes;
	}

	/**
	 * @return int
	 */
	public function get_ID() {
		return $this->post->ID;
	}

	/**
	 * @return string
	 */
	public function get_name() {
		return $this->post->post_title;
	}

	/**
	 * @return string
	 */
	public function get_description() {
		return $this->post->post_content;
	}

	/**
	 * @return string
	 */
	public function get_slug() {
		return $this->post->post_name;
	}

	/**
	 * @return false|string
	 */
	public function get_link() {
		return get_the_permalink( $this->post->ID );
	}

	/**
	 * @return int
	 */
	public function has_price() {
		if ( ! Price_Attribute_Options_Page::show_price() ) {
			return false;
		}

		return $this->prices->has_price();
	}

	/**
	 * @return string
	 */
	public function get_contact_for_price_text() {
		return Price_Attribute_Options_Page::get_default_value();
	}

	/**
	 * @return string
	 */
	public function get_days_ago() {
		$now          = time();
		$publish_date = strtotime( $this->post->post_date );

		return sprintf( esc_html__( '%s ago', 'myhome-core' ), human_time_diff( $publish_date, $now ) );
	}

	/**
	 * @return string
	 */
	public function get_short_excerpt() {
		$patterns     = "/\[[\/]?vc_[^\]]*\]/";
		$replacements = "";
		$content      = wp_kses( strip_shortcodes( preg_replace( $patterns, $replacements, $this->post->post_content ) ), array() );

		if ( ! empty( \MyHomeCore\My_Home_Core()->settings->props['mh-estate_short-description'] ) ) {
			$length = intval( \MyHomeCore\My_Home_Core()->settings->props['mh-estate_short-description'] );
		} else {
			$length = self::SHORT_EXCERPT_LIMIT;
		}

		if ( mb_strlen( $content, 'UTF-8' ) > $length ) {
			$content = mb_strimwidth( $content, 0, $length, '...', 'UTF-8' );
		}

		return preg_replace( $patterns, $replacements, $content );
	}

	/**
	 * @return string
	 */
	public function get_excerpt() {
		$patterns     = "/\[[\/]?vc_[^\]]*\]/";
		$replacements = "";
		$content      = wp_kses( strip_shortcodes( preg_replace( $patterns, $replacements, $this->post->post_content ) ), array() );

		if ( mb_strlen( $content, 'UTF-8' ) > self::LONG_EXCERPT_LIMIT ) {
			$content = mb_strimwidth( $content, 0, self::LONG_EXCERPT_LIMIT, '...', 'UTF-8' );
		}

		return preg_replace( $patterns, $replacements, $content );
	}

	/**
	 * @return array
	 */
	public function get_gallery() {
		$gallery = get_field( 'myhome_estate_gallery', $this->post->ID );
		if ( ! is_array( $gallery ) ) {
			return array();
		}

		foreach ( $gallery as $key => $image ) {
			$gallery[ $key ]['srcset'] = wp_get_attachment_image_srcset( $image['ID'], 'myhome-standard' );
		}

		return $gallery;
	}

	public function get_gallery_data() {
		$gallery_data = array();
		$gallery      = get_field( 'myhome_estate_gallery', $this->post->ID );
		if ( empty( $gallery ) ) {
			return array();
		}

		foreach ( $gallery as $key => $image ) {
			//			$gallery_data[] = wp_get_attachment_image_srcset( $image['ID'], 'myhome-standard' );
			$gallery_data[] = wp_get_attachment_image_url( $image['ID'], 'myhome-standard-m' );
		}

		return $gallery_data;
	}

	/**
	 * @return bool
	 */
	public function has_plans() {
		$plans_field = get_field( 'estate_plans', $this->post->ID );

		return is_array( $plans_field ) && ! empty( $plans_field );
	}

	/**
	 * @return Plan[]
	 */
	public function get_plans() {
		$plans_field = get_field( 'estate_plans', $this->post->ID );
		$plans       = array();

		if ( ! is_array( $plans_field ) ) {
			return $plans;
		}

		foreach ( $plans_field as $plan ) {
			$plan = Plan::get_from_acf_data( $plan );
			if ( $plan !== false ) {
				$plans[] = $plan;
			}
		}

		return $plans;
	}

	/**
	 * @return array
	 */
	public function get_location() {
		return get_field( 'estate_location', $this->post->ID );
	}

	/**
	 * @return array
	 */
	public function get_position() {
		$location = $this->get_location();

		if ( empty( $location ) ) {
			return array( 'lat' => 0, 'lng' => 0 );
		}

		return array(
			'lat' => doubleval( $location['lat'] ),
			'lng' => doubleval( $location['lng'] )
		);
	}

	/**
	 * @return string
	 */
	public function get_address() {
		if ( ! empty( \MyHomeCore\My_Home_Core()->settings->props['mh-estate_hide-address'] ) ) {
			return '';
		}

		$location = $this->get_location();

		return empty( $location['address'] ) ? '' : $location['address'];
	}

	/**
	 * @return \MyHomeCore\Attributes\Attribute_Values
	 */
	public function get_offer_type() {
		return Attribute_Factory::get_offer_type()->get_estate_values( $this->get_ID() );
	}

	/**
	 * @return bool
	 */
	public function has_additional_features() {
		$additional_features = get_field( 'estate_additional_features', $this->get_ID() );

		return ! empty( $additional_features );
	}

	/**
	 * @return array
	 */
	public function get_additional_features() {
		$additional_features       = array();
		$additional_features_field = get_field( 'estate_additional_features', $this->get_ID() );

		if ( empty( $additional_features_field ) ) {
			return array();
		}

		foreach ( $additional_features_field as $feature ) {
			$additional_features[] = array(
				'name'  => $feature['estate_additional_feature_name'],
				'value' => $feature['estate_additional_feature_value']
			);
		}

		return $additional_features;
	}

	public function get_current_currency_prices() {
		return $this->prices->get_current_currency();
	}

	/**
	 * @return Currency_Prices[]
	 */
	public function get_currency_prices() {
		return $this->prices->get_all_currencies();
	}

	/**
	 * @return Price[]
	 */
	public function get_prices() {
		$current_currency_prices = $this->prices->get_current_currency();
		$prices                  = array();

		if ( $current_currency_prices == false ) {
			return $prices;
		}

		foreach ( $current_currency_prices->get_prices() as $price ) {
			$prices[] = $price;
		}

		return $prices;
	}

	/**
	 * @return bool|Price
	 */
	public function get_price() {
		$prices = $this->get_prices();

		if ( ! isset( $prices[0] ) ) {
			return false;
		}

		return $prices[0];
	}

	/**
	 * @return array
	 */
	public function get_all_prices_data() {
		$currencies = Currencies::get_all();

		if ( function_exists( 'icl_object_id' ) && ( \MyHomeCore\My_Home_Core()->current_language != \MyHomeCore\My_Home_Core()->default_language ) ) {
			Term_Factory::$offer_types = array();
			do_action( 'wpml_switch_language', \MyHomeCore\My_Home_Core()->default_language );
			$offer_types = Term_Factory::get_offer_types();
			do_action( 'wpml_switch_language', \MyHomeCore\My_Home_Core()->current_language );
		} else {
			$offer_types = Term_Factory::get_offer_types();
		}

		$prices = array();

		foreach ( $currencies as $currency ) {
			$currency_key = $currency->get_key();

			if ( Price_Attribute::is_range() ) {
				$price_keys = array( $currency_key . '_from', $currency_key . '_to' );
			} else {
				$price_keys = array( $currency_key );
			}

			foreach ( $price_keys as $price_key ) {
				$price = floatval( get_field( 'estate_attr_' . $price_key, $this->get_ID() ) );

				$prices[ $price_key ] = $price == 0 ? '' : $prices[ $price_key ];
			}

			foreach ( $offer_types as $offer_type ) {
				if ( ! $offer_type->specify_price() ) {
					continue;
				}

				if ( $offer_type->is_price_range() ) {
					$price_keys = array( $currency_key . '_from', $currency_key . '_to' );
				} else {
					$price_keys = array( $currency_key );
				}

				foreach ( $price_keys as $price_key ) {
					$field_key = $price_key . '_offer_' . $offer_type->get_ID();

					$price                = floatval( get_field( 'estate_attr_' . $field_key, $this->get_ID() ) );
					$prices[ $field_key ] = $price == 0 ? '' : $price;
				}

			}
		}

		return $prices;
	}

	/**
	 * @return array
	 */
	public function get_current_currency_prices_data() {
		$currency_prices = $this->prices->get_current_currency();

		if ( $currency_prices == false ) {
			return array();
		}

		return $currency_prices->get_data();
	}

	/**
	 * @return string
	 */
	public function get_payment_state() {
		$state = get_post_meta( $this->get_ID(), 'myhome_state', true );

		if ( empty( $state ) ) {
			return 'pre_payment';
		}

		return $state;
	}

	/**
	 * @return bool
	 */
	public function is_featured() {
		$featured = get_field( 'myhome_estate_featured', $this->post->ID );

		return ! empty( $featured );
	}

	/**
	 * @return User
	 */
	public function get_user() {
		$user_id = intval( $this->post->post_author );

		return User::get_instance( $user_id );
	}

	/**
	 * @return bool
	 */
	public function has_attachments() {
		$attachments = get_field( 'estate_attachments', $this->post->ID );

		return is_array( $attachments ) && ! empty( $attachments );
	}

	/**
	 * @return Attachment[]
	 */
	public function get_attachments() {
		$attachments_field = get_field( 'estate_attachments', $this->post->ID );
		$attachments       = array();

		if ( ! is_array( $attachments_field ) ) {
			return $attachments;
		}

		foreach ( $attachments_field as $attachment ) {
			$attachment = Attachment::get_from_acf_data( $attachment );
			if ( $attachment !== false ) {
				$attachments[] = $attachment;
			}
		}

		return $attachments;
	}

	/**
	 * @return int
	 */
	public function get_views() {
		return intval( get_field( 'estate_views', $this->post->ID ) );
	}

	public function increment_views() {
		$views = intval( get_field( 'estate_views', $this->post->ID ) );
		$views ++;
		update_field( 'estate_views', $views, $this->post->ID );
	}

	/**
	 * @return bool
	 */
	public function has_image() {
		return has_post_thumbnail( $this->post->ID );
	}

	/**
	 * @return int|string
	 */
	public function get_image_id() {
		return get_post_thumbnail_id( $this->post->ID );
	}

	/**
	 * @return false|string
	 */
	public function get_marker_image() {
		return wp_get_attachment_image_url( $this->get_image_id(), 'myhome-additional-m' );
	}

	/**
	 * @return array
	 */
	public function get_video() {
		$video_url   = get_post_meta( $this->get_ID(), 'estate_video', true );
		$video_embed = get_field( 'estate_video', $this->get_ID() );

		return array(
			'url'   => empty( $video_url ) ? '' : $video_url,
			'embed' => empty( $video_embed ) ? '' : $video_embed
		);
	}

	/**
	 * @return string
	 */
	public function get_virtual_tour() {
		$virtual_tour = get_field( 'myhome_estate_virtual_tour', $this->get_ID() );

		return empty( $virtual_tour ) ? '' : $virtual_tour;
	}

	/**
	 * @return string
	 */
	public function get_status() {
		return $this->post->post_status;
	}

	/**
	 * @return string
	 */
	public function get_publish_date() {
		return $this->post->post_date;
	}

	/**
	 * @return string
	 */
	public function get_modified_date() {
		return $this->post->post_modified;
	}

	/**
	 * @return  Estate_Attribute[]
	 */
	public function get_property_type() {
		$property_type = array();

		foreach ( $this->get_attributes() as $attribute ) {
			if ( ! $attribute->attribute instanceof Property_Type_Attribute ) {
				continue;
			}

			$property_type[] = $attribute;
		}

		return $property_type;
	}

	/**
	 * @return array
	 */
	public function get_near_estates_ids() {
		$location      = $this->get_location();
		$distance_unit = \MyHomeCore\My_Home_Core()->settings->props['mh-estate-distance_unit'];
		$range         = \MyHomeCore\My_Home_Core()->settings->props['mh-estate-near_estates_range'];

		if ( $distance_unit == 'miles' ) {
			$unit = 3959;
		} else {
			$unit = 6371;
		}

		global $wpdb;
		$args  = array(
			$unit,
			$location['lat'],
			$location['lng'],
			$location['lat'],
			$this->get_ID(),
		);
		$query = "
			SELECT key1.ID,
				( %d * acos( cos( radians(%f) ) * cos( radians( lat ) ) * cos( radians( lng ) - radians(%f) ) + sin( radians(%f) ) * sin( radians( lat ) ) ) ) AS distance
			FROM {$wpdb->posts} key1
				INNER JOIN {$wpdb->prefix}myhome_locations key2
				ON key1.ID = key2.post_id
			WHERE key1.post_status = 'publish'
				AND key1.post_type = 'estate'
				AND key1.ID != %d
			HAVING distance < $range
		";

		$results = $wpdb->get_col( $wpdb->prepare( $query, $args ) );
		if ( ! $results ) {
			return array();
		}

		return $results;
	}

	/**
	 * @param int $limit
	 *
	 * @return Estates
	 */
	public function get_related( $limit = 4 ) {
		$estate_factory = new Estate_Factory();
		$estate_factory->set_limit( $limit );
		$estate_factory->set_estates__in( $this->get_near_estates_ids() );

		foreach ( $this->get_property_type() as $property_type ) {
			$estate_factory->add_filter( $property_type->get_filter() );
		}

		return $estate_factory->get_results();
	}

	/**
	 * @return string
	 */
	public function get_attribute_classes() {
		$classes = '';
		foreach ( $this->get_attributes() as $attribute ) {
			foreach ( $attribute->get_values() as $value ) {
				$value_slug = $value->get_slug();
				if ( empty( $value_slug ) ) {
					continue;
				}
				$classes .= ' mh-attribute-' . $attribute->get_slug() . '__' . $value_slug;
			}
		}

		return $classes;
	}

	/**
	 * @return bool
	 */
	public function is_awaiting_moderation() {
		return $this->get_status() == 'pending';
	}

	/**
	 * @return bool
	 */
	public static function is_new_tab() {
		return ! empty( \MyHomeCore\My_Home_Core()->settings->props['mh-estate_link-blank'] );
	}

}