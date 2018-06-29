<?php

namespace MyHomeIDXBroker;

use MyHomeCore\Attributes\Attribute;
use MyHomeCore\Attributes\Attribute_Factory;
use MyHomeCore\Attributes\Number_Attribute;
use MyHomeCore\Attributes\Text_Attribute;
use MyHomeCore\Common\Breadcrumbs\Breadcrumbs;
use MyHomeCore\Terms\Term;
use MyHomeCore\Terms\Term_Factory;


/**
 * Class Properties
 * @package MyHomeIDXBroker
 */
class Properties {

	const IDX_LISTING_ID = 'idx_broker_property_id';
	const IDX_STATUS_PENDING = 'Pending';
	const IDX_STATUS_ACTIVE = 'Active';
	const IDX_STATUS_SOLD = 'SOld';

	/**
	 * @param array $properties
	 */
	public function import( $properties ) {
		if ( empty( $properties ) ) {
			return;
		}

		foreach ( $properties as $property ) {
			if ( ! $this->exists( $property['listingID'] ) ) {
				$this->create( $property );
			}
		}
	}

	/**
	 * @param array $property
	 *
	 * @return int|\WP_Error
	 */
	public function create( $property ) {
		if ( $this->exists( $property['listingID'] ) ) {
			return false;
		}

		$property_name = '';
		if ( isset( $property['address'] ) ) {
			$property_name = $property['address'];
		} elseif ( isset( $property['listingID'] ) ) {
			$property_name = $property['listingID'];
		}

		$status = My_Home_IDX_Broker()->options->get( 'init_status' );
		if ( empty( $status ) ) {
			$status = 'draft';
		}

		$property_data = array(
			'post_title'  => $property_name,
			'post_type'   => 'estate',
			'post_status' => $status
		);

		if ( isset( $property['userAgentID'] ) ) {
			$agent = Idx_Broker_Agent::get_by_idx_broker_id( $property['userAgentID'] );
			if ( $agent instanceof Idx_Broker_Agent ) {
				$property_data['post_author'] = $agent->get_ID();
			}
		}

		$property_id = wp_insert_post( $property_data );
		$this->update_data( $property_id, $property );

		if ( isset( $property['image'] ) ) {
			$gallery = array();
			if ( isset( $property['image']['totalCount'] ) ) {
				unset( $property['image']['totalCount'] );
			}

			$images_limit_number = intval( \MyHomeIDXBroker\My_Home_IDX_Broker()->options->get( 'images_limit' ) );
			$images_limit        = $images_limit_number != - 1;

			foreach ( $property['image'] as $key => $image ) {
				if ( $images_limit && $images_limit_number == $key ) {
					break;
				}

				$image         = $image['url'];
				$get           = wp_remote_get( $image );
				$type          = wp_remote_retrieve_header( $get, 'content-type' );
				$mirror        = wp_upload_bits( basename( $image ), '', wp_remote_retrieve_body( $get ) );
				$attachment    = array(
					'post_title'     => basename( $image ),
					'post_mime_type' => $type
				);
				$attachment_id = wp_insert_attachment( $attachment, $mirror['file'] );
				$attach_data   = wp_generate_attachment_metadata( $attachment_id, $mirror['file'] );
				wp_update_attachment_metadata( $attachment_id, $attach_data );

				if ( ! is_wp_error( $attachment_id ) ) {
					$gallery[] = $attachment_id;

					if ( ! $key ) {
						set_post_thumbnail( $property_id, $attachment_id );
					}
				}
			}

			update_post_meta( $property_id, 'estate_gallery', $gallery );
		}

		return $property_id;
	}

	/**
	 * @param $property_data
	 *
	 * @return bool
	 */
	public function update( $property_data ) {
		$property = $this->get_property( $property_data['listingID'] );

		if ( ! $property instanceof \WP_Post ) {
			return false;
		}

		if ( $property_data['propStatus'] == Properties::IDX_STATUS_PENDING ) {
			$offer_type = Term::get_term( My_Home_IDX_Broker()->options->get( 'offer_type_pending' ) );
		} elseif ( $property_data['propStatus'] == Properties::IDX_STATUS_SOLD ) {
			$offer_type = Term::get_term( My_Home_IDX_Broker()->options->get( 'offer_type_sold' ) );
		} else {
			$offer_type = Term::get_term( My_Home_IDX_Broker()->options->get( 'offer_type' ) );
		}

		if ( $offer_type instanceof Term ) {
			wp_set_post_terms( $property->ID, array( $offer_type->get_name() ), $offer_type->get_wp_term()->taxonomy );
		}

		return true;
	}

	private function update_data( $property_id, $property ) {
		if ( is_wp_error( $property_id ) ) {
			return false;
		}

		$fields = Fields::get();

		if ( isset( $property['listingID'] ) ) {
			update_post_meta( $property_id, Properties::IDX_LISTING_ID, $property['listingID'] );
		}

		$map = array( 'zoom' => 5 );
		if ( isset( $property['address'] ) ) {
			$map['address'] = $property['address'];
		}

		if ( isset( $property['latitude'] ) ) {
			$map['lat'] = $property['latitude'];
		}

		if ( isset( $property['longitude'] ) ) {
			$map['lng'] = $property['longitude'];
		}
		update_field( 'myhome_estate_location', $map, $property_id );

		$taxonomies          = array();
		$additional_features = array();

		foreach ( $property as $key => $value ) {
			if ( ! isset( $fields[ $key ] ) ) {
				continue;
			}

			$attribute = $fields[ $key ]->get_value();
			echo $attribute . '<br>';
			if ( $attribute == 'ignore' ) {
				continue;
			}

			if ( strpos( $attribute, 'myhome_attribute' ) !== false ) {
				$attribute_id = str_replace( 'myhome_attribute_', '', $attribute );
				$attribute    = Attribute::get_by_id( $attribute_id );

				if ( $attribute instanceof Text_Attribute ) {
					if ( isset( $taxonomies[ $attribute->get_slug() ] ) ) {
						$taxonomies[ $attribute->get_slug() ][] = $value;
					} else {
						$taxonomies[ $attribute->get_slug() ] = array( $value );
					}
				} elseif ( $attribute instanceof Number_Attribute ) {
					$value = floatval( str_replace( ',', '', preg_replace( "/[^0-9,.]/", "", $value ) ) );
					update_field( 'myhome_estate_attr_' . $attribute->get_slug(), $value, $property_id );
				}
			} elseif ( strpos( $attribute, 'myhome_price' ) !== false ) {
				$value     = floatval( str_replace( ',', '', preg_replace( "/[^0-9,.]/", "", $value ) ) );
				$price_key = str_replace( 'myhome_', '', $attribute );
				update_field( 'myhome_estate_attr_' . $price_key, $value, $property_id );
			} elseif ( $attribute == 'myhome_name' ) {
				wp_update_post( array(
					'post_title' => $value,
					'ID'         => $property_id
				) );
			} elseif ( $attribute == 'myhome_description' ) {
				wp_update_post( array(
					'post_content' => $value,
					'ID'           => $property_id
				) );
			} elseif ( $attribute == 'myhome_additional_features' ) {
				$field = Fields::get_by_key( $key );
				if ( $field != false ) {
					$additional_features[] = array(
						'estate_additional_feature_name'  => $field->get_display_name(),
						'estate_additional_feature_value' => $value
					);
				}
			}
		}

		update_field( 'estate_additional_features', $additional_features, $property_id );

		foreach ( Breadcrumbs::get_attributes() as $attribute ) {
			if ( isset( $taxonomies[ $attribute->get_slug() ] ) && ! empty( $taxonomies[ $attribute->get_slug() ] ) ) {
				continue;
			}

			$default_term_id = intval( My_Home_IDX_Broker()->options->get( 'attributes', $attribute->get_slug() ) );
			if ( $default_term_id == 0 ) {
				$wp_terms = get_terms( array(
					'taxonomy' => $attribute->get_slug()
				) );

				if ( count( $wp_terms ) == 0 ) {
					continue;
				}

				$default_term = new Term( $wp_terms[0] );
			} else {
				$default_term = Term::get_term( $default_term_id );
			}

			$taxonomies[ $attribute->get_slug() ] = $default_term->get_name();
		}

		foreach ( $taxonomies as $taxonomy => $terms ) {
			wp_set_post_terms( $property_id, $terms, $taxonomy );
		}
	}

	/**
	 * @param $idx_listing_ID
	 *
	 * @return bool|\WP_Post
	 */
	public function get_property( $idx_listing_ID ) {
		$args     = array(
			'post_type'  => 'estate',
			'meta_key'   => Properties::IDX_LISTING_ID,
			'meta_value' => $idx_listing_ID
		);
		$wp_query = new \WP_Query( $args );

		return isset( $wp_query->posts[0] ) ? $wp_query->posts[0] : false;
	}

	/**
	 * @param string $idx_listing_ID
	 *
	 * @return bool
	 */
	public function exists( $idx_listing_ID ) {
		$args     = array(
			'post_type'  => 'estate',
			'meta_key'   => Properties::IDX_LISTING_ID,
			'meta_value' => $idx_listing_ID
		);
		$wp_query = new \WP_Query( $args );

		return $wp_query->found_posts > 0;
	}

}