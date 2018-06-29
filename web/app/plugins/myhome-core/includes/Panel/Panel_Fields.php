<?php

namespace MyHomeCore\Panel;


use MyHomeCore\Attributes\Attribute;
use MyHomeCore\Attributes\Attribute_Factory;
use MyHomeCore\Attributes\Attribute_Values;
use MyHomeCore\Attributes\Number_Attribute;
use MyHomeCore\Attributes\Price_Attribute;
use MyHomeCore\Attributes\Text_Attribute;
use MyHomeCore\Estates\Elements\Estate_Element;
use MyHomeCore\Estates\Prices\Currencies;
use MyHomeCore\Terms\Term;
use MyHomeCore\Terms\Term_Factory;

/**
 * Class Panel_Fields
 * @package MyHomeCore\Panel
 */
class Panel_Fields {

	const OPTIONS_KEY = 'myhome_panel_fields';

	private static function get_price_fields( Price_Attribute $attribute ) {
		$currencies = Currencies::get_all();

		if ( function_exists( 'icl_object_id' ) && ( \MyHomeCore\My_Home_Core()->current_language != \MyHomeCore\My_Home_Core()->default_language ) ) {
			Term_Factory::$offer_types = array();
			do_action( 'wpml_switch_language', \MyHomeCore\My_Home_Core()->default_language );
			$offer_types = Term_Factory::get_offer_types();
			do_action( 'wpml_switch_language', \MyHomeCore\My_Home_Core()->current_language );
		} else {
			$offer_types = Term_Factory::get_offer_types();
		}
		$price_fields   = array();
		$is_price_range = Price_Attribute::is_range();

		foreach ( $currencies as $currency ) {
			$currency_key = $currency->get_key();

			if ( $is_price_range ) {
				$price_keys = array( $currency_key . '_from', $currency_key . '_to' );
			} else {
				$price_keys = array( $currency_key );
			}

			$price_keys_count = count( $price_keys );

			foreach ( $price_keys as $key => $price_key ) {
				$label = $attribute->get_name();
				if ( $price_keys_count == 2 && $key == 0 ) {
					$label .= ' ' . esc_html__( 'from', 'myhome-core' );
				} elseif ( $price_keys_count == 2 && $key == 1 ) {
					$label .= ' ' . esc_html__( 'to', 'myhome-core' );
				}

				$price_fields[] = array(
					'name'         => $label . ' (' . $currency->get_sign() . ')',
					'base_slug'    => $attribute->get_base_slug(),
					'slug'         => $price_key,
					'label'        => '',
					'required'     => false,
					'width'        => '1',
					'instructions' => '',
					'multiple'     => false,
					'type'         => Panel_Field::TYPE_NUMBER,
					'id'           => $attribute->get_ID(),
					'currency_key' => $currency_key
				);
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

				$price_keys_count = count( $price_keys );

				foreach ( $price_keys as $key => $price_key ) {
					$label = $attribute->get_name();
					if ( $price_keys_count == 2 && $key == 0 ) {
						$label .= ' ' . esc_html__( 'from', 'myhome-core' );
					} elseif ( $price_keys_count == 2 && $key == 1 ) {
						$label .= ' ' . esc_html__( 'to', 'myhome-core' );
					}

					$field_key = $price_key . '_offer_' . $offer_type->get_ID();

					$price_fields[] = array(
						'name'         => $label . ' - ' . $offer_type->get_name() . ' (' . $currency->get_sign() . ')',
						'base_slug'    => $attribute->get_base_slug(),
						'slug'         => $field_key,
						'label'        => '',
						'required'     => false,
						'width'        => '1',
						'instructions' => '',
						'multiple'     => false,
						'type'         => Panel_Field::TYPE_NUMBER,
						'id'           => $attribute->get_ID(),
						'currency_key' => $currency_key,
						'offer_type'   => $offer_type->get_ID()
					);
				}
			}
		}

		return $price_fields;
	}

	/**
	 * @return array
	 */
	public static function get() {
		$fields = array(
			array(
				'name'         => esc_html__( 'Title', 'myhome-core' ),
				'slug'         => 'title',
				'label'        => '',
				'required'     => false,
				'width'        => '1',
				'instructions' => '',
				'multiple'     => false,
				'type'         => Panel_Field::TYPE_TITLE
			),
			array(
				'name'         => esc_html__( 'Description', 'myhome-core' ),
				'slug'         => 'description',
				'label'        => '',
				'required'     => false,
				'width'        => '1',
				'instructions' => '',
				'multiple'     => false,
				'type'         => Panel_Field::TYPE_DESCRIPTION
			),
			array(
				'name'         => esc_html__( 'Gallery', 'myhome-core' ),
				'slug'         => 'gallery',
				'label'        => '',
				'required'     => false,
				'width'        => '1',
				'instructions' => '',
				'multiple'     => false,
				'type'         => Panel_Field::TYPE_GALLERY
			),
			array(
				'name'         => esc_html__( 'Featured image', 'myhome-core' ),
				'slug'         => 'image',
				'label'        => '',
				'required'     => false,
				'width'        => '1',
				'instructions' => '',
				'multiple'     => false,
				'type'         => Panel_Field::TYPE_IMAGE
			),
			array(
				'name'         => esc_html__( 'Location', 'myhome-core' ),
				'slug'         => 'location',
				'label'        => '',
				'required'     => false,
				'width'        => '1',
				'instructions' => '',
				'multiple'     => false,
				'type'         => Panel_Field::TYPE_LOCATION
			),
			array(
				'name'         => esc_html__( 'Featured', 'myhome-core' ),
				'slug'         => 'is_featured',
				'label'        => '',
				'required'     => false,
				'width'        => '1',
				'instructions' => '',
				'multiple'     => false,
				'type'         => Panel_field::TYPE_FEATURED
			)
		);

		foreach ( Attribute_Factory::get_text() as $attribute ) {
			$fields[] = array(
				'name'         => $attribute->get_name(),
				'base_slug'    => $attribute->get_base_slug(),
				'slug'         => $attribute->get_slug(),
				'label'        => '',
				'required'     => false,
				'width'        => '2',
				'instructions' => '',
				'multiple'     => false,
				'type'         => Panel_Field::TYPE_TEXT,
				'id'           => $attribute->get_ID()
			);
		}

		foreach ( Attribute_Factory::get_number() as $attribute ) {
			if ( $attribute instanceof Price_Attribute ) {
				$fields = array_merge( $fields, self::get_price_fields( $attribute ) );
				continue;
			}

			$fields[] = array(
				'name'         => $attribute->get_name(),
				'base_slug'    => $attribute->get_base_slug(),
				'slug'         => $attribute->get_slug(),
				'label'        => '',
				'required'     => false,
				'width'        => '2',
				'instructions' => '',
				'multiple'     => false,
				'type'         => Panel_Field::TYPE_NUMBER,
				'id'           => $attribute->get_ID()
			);
		}

		foreach ( Attribute_Factory::get_text_areas() as $attribute ) {
			$fields[] = array(
				'name'         => $attribute->get_name(),
				'slug'         => $attribute->get_slug(),
				'label'        => '',
				'required'     => false,
				'width'        => '2',
				'instructions' => '',
				'multiple'     => false,
				'type'         => Panel_Field::TYPE_TEXT_AREA,
				'id'           => $attribute->get_ID(),
				'dependencies' => $attribute->get_property_type_dependency()
			);
		}

		$elements = array(
			'mh-estate_video'        => (object) array(
				'label'   => esc_html__( 'Video', 'myhome-core' ),
				'slug'    => Estate_Element::VIDEO,
				'type'    => Estate_Element::VIDEO,
				'default' => true
			),
			'mh-estate_virtual_tour' => (object) array(
				'label'   => esc_html__( 'Virtual tour', 'myhome-core' ),
				'slug'    => Estate_Element::VIRTUAL_TOUR,
				'type'    => Estate_Element::VIRTUAL_TOUR,
				'default' => false
			),
			'mh-estate_plans'        => (object) array(
				'label'   => esc_html__( 'Plans', 'myhome-core' ),
				'slug'    => Estate_Element::PLANS,
				'type'    => Estate_Element::PLANS,
				'default' => true
			),
			'mh-estate_attachments'  => (object) array(
				'label'   => esc_html__( 'Attachments', 'myhome-core' ),
				'slug'    => Estate_Element::ATTACHMENTS,
				'type'    => Estate_Element::ATTACHMENTS,
				'default' => false
			)
		);

		foreach ( $elements as $el_key => $el ) {
			if ( ! isset( \MyHomeCore\My_Home_Core()->settings->props[ $el_key ] ) && ! $el->default ) {
				$delete = true;
			} elseif ( ! isset( \MyHomeCore\My_Home_Core()->settings->props[ $el_key ] ) && $el->default ) {
				$delete = false;
			} elseif ( empty( \MyHomeCore\My_Home_Core()->settings->props[ $el_key ] ) ) {
				$delete = true;
			} else {
				$delete = false;
			}

			if ( $delete ) {
				unset( $elements[ $el_key ] );
			}
		}

		foreach ( $elements as $element ) {
			$fields[] = array(
				'name'         => $element->label,
				'slug'         => $element->slug,
				'label'        => '',
				'required'     => false,
				'width'        => '1',
				'instructions' => '',
				'multiple'     => false,
				'type'         => $element->type,
				'id'           => '',
				'dependencies' => array()
			);
		}

		//		print_r($fields);

		return $fields;
	}

	/**
	 * @return array
	 */
	public static function get_selected() {
		$fields = array();
		foreach ( self::get() as $field ) {
			$fields[] = $field['slug'];
		}

		$selected_filtered = array();
		$selected          = get_option( self::OPTIONS_KEY, self::get() );

		foreach ( $selected as $key => $field ) {
			if ( ! in_array( $field['slug'], $fields ) ) {
				continue;
			}

			$field['required'] = filter_var( $field['required'], FILTER_VALIDATE_BOOLEAN );
			$field['multiple'] = filter_var( $field['multiple'], FILTER_VALIDATE_BOOLEAN );

			if ( ! empty( $field['id'] ) ) {

				if ( false === ( $attribute = Attribute::get_by_id( $field['id'] ) ) ) {
					continue;
				}

				$field['name'] = $attribute->get_name();

				if ( $attribute instanceof Text_Attribute ) {
					$order = Term_Factory::ORDER_DESC;
					if ( isset( \MyHomeCore\My_Home_Core()->settings->props['mh-agent-panel-order_by'] ) && ! empty( \MyHomeCore\My_Home_Core()->settings->props['mh-agent-panel-order_by'] ) ) {
						$order_by = \MyHomeCore\My_Home_Core()->settings->props['mh-agent-panel-order_by'];
						if ( $order_by == Term_Factory::ORDER_BY_NAME ) {
							$order = Term_Factory::ORDER_ASC;
						}
					} else {
						$order_by = Term_Factory::ORDER_BY_COUNT;
					}

					$field['parent_id']   = $attribute->get_parent_id();
					$field['parent_type'] = $attribute->get_parent_type();
					$field['values']      = array_map(
						function ( $attribute_values ) {
							/* @var Attribute_Values $attribute_values */
							return $attribute_values->get_data();
						}, $attribute->get_values( true, $order_by, $order )
					);
					$field['tags']        = $attribute->get_tags();
				}

				if ( $attribute instanceof Price_Attribute ) {
					if ( ! empty( $field['currency_key'] ) ) {
						$currency_key = $field['currency_key'];
					} else {
						$currency_key = str_replace( array( '_to', '_from' ), '', $field['slug'] );
					}
					$currency      = Currencies::get_by_key( $currency_key );
					$currency_sign = $currency != false ? $currency->get_sign() : '';

					if ( strpos( $field['slug'], '_from' ) !== false ) {
						$label = ' ' . esc_html__( 'from', 'myhome-core' );
					} elseif ( strpos( $field['slug'], '_to' ) !== false ) {
						$label = ' ' . esc_html__( 'to', 'myhome-core' );
					} else {
						$label = '';
					}

					if ( ! empty( $field['offer_type'] ) ) {
						if ( function_exists( 'icl_object_id' ) ) {
							do_action( 'wpml_switch_language', \MyHomeCore\My_Home_Core()->default_language );
							$offer_type = Term::get_term( $field['offer_type'] );
							do_action( 'wpml_switch_language', \MyHomeCore\My_Home_Core()->current_language );
						} else {
							$offer_type = Term::get_term( $field['offer_type'] );
						}

						$display_after = ! empty( $offer_type ) ? $label . ' - ' . $offer_type->get_name() . ' (' . $currency_sign . ')' : $label . ' (' . $currency_sign . ')';
					} else {
						$display_after = $label . ' (' . $currency_sign . ')';
					}

					$field['display_after'] = $display_after;
				} else if ( $attribute instanceof Number_Attribute ) {
					$field['display_after'] = $attribute->get_display_after();
				}
				$field['dependencies'] = $attribute->get_property_type_dependency();

				if ( empty( $field['control'] ) ) {
					$field['control'] = 'select';
				}
			}

			$selected_filtered[] = $field;
		}

		if ( count( $selected_filtered ) != count( $selected ) ) {
			update_option( self::OPTIONS_KEY, $selected_filtered );
		}

		return $selected_filtered;
	}

	/**
	 * @return array
	 */
	public static function get_selected_backend() {
		$selected_fields = self::get_selected();
		foreach ( $selected_fields as $key => $field ) {
			if ( isset( $selected_fields[ $key ]['values'] ) ) {
				unset( $selected_fields[ $key ]['values'] );
			}

			if ( isset( $selected_fields[ $key ]['dependencies'] ) ) {
				unset( $selected_fields[ $key ]['dependencies'] );
			}
		}

		return $selected_fields;
	}

	/**
	 * @return array
	 */
	public static function get_current() {
		$fields = array();
		foreach ( self::get_selected() as $field ) {
			$field['name']         = apply_filters( 'wpml_translate_single_string', $field['name'], 'myhome-core', 'Submit property field (label) - ' . $field['name'] );
			$field['instructions'] = apply_filters( 'wpml_translate_single_string', $field['instructions'], 'myhome-core', 'Submit property (instructions) - ' . $field['instructions'] );

			$fields[] = $field;
		}

		return $fields;
	}

	/**
	 * @return array
	 */
	public static function get_available() {
		$available_fields = array();
		$selected_fields  = array();

		foreach ( self::get_selected() as $field ) {
			$selected_fields[] = $field['slug'];
		}

		foreach ( self::get() as $field ) {
			if ( ! in_array( $field['slug'], $selected_fields ) ) {
				$field['required']  = filter_var( $field['required'], FILTER_VALIDATE_BOOLEAN );
				$field['multiple']  = filter_var( $field['multiple'], FILTER_VALIDATE_BOOLEAN );
				$available_fields[] = $field;
			}
		}

		return $available_fields;
	}

}