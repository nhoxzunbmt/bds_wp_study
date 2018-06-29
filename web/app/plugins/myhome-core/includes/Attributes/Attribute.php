<?php

namespace MyHomeCore\Attributes;


use MyHomeCore\Components\Listing\Form\Field;
use MyHomeCore\Terms\Term_Factory;

/**
 * Class Attribute
 * @package MyHomeCore\Attributes
 */
abstract class Attribute {

	/**
	 * @var \stdClass
	 */
	protected $attribute;

	/**
	 * @var array
	 */
	protected $attribute_data = array();

	const TEXT = 'taxonomy';
	const NUMBER = 'field';
	const KEYWORD = 'keyword';
	const CORE = 'core';
	const PRICE = 'price';
	const PROPERTY_ID = 'estate_id';
	const PROPERTY_TYPE = 'property_type';
	const OFFER_TYPE = 'offer_type';
	const TEXTAREA = 'textarea';
	const WIDGETS = 'Widgets';
	const ID = 'id';

	/**
	 * @return Attribute_Options_Page|false
	 */
	abstract public function get_options_page();

	/**
	 * @return string
	 */
	abstract public function get_type();

	/**
	 * @return string
	 */
	abstract public function get_type_name();

	/**
	 * @return bool
	 */
	abstract public function has_archive();

	/**
	 * @param int $estate_id
	 *
	 * @return Attribute_Values
	 */
	abstract public function get_estate_values( $estate_id );

	/**
	 * @param bool $all_values
	 *
	 * @return Attribute_Values[]
	 */
	abstract public function get_values( $all_values = false );

	/**
	 * @param array $attribute_data
	 */
	abstract public function update_options( $attribute_data );

	/**
	 * @param int   $estate_id
	 * @param array $values
	 */
	abstract public function update_estate_values( $estate_id, $values );

	/**
	 * @param Attribute_Values $attribute_values
	 * @param string           $compare
	 *
	 * @return mixed
	 */
	abstract public function get_estate_filter( Attribute_Values $attribute_values, $compare = '=' );

	/**
	 * Attribute constructor.
	 *
	 * @param $attribute
	 */
	public function __construct( $attribute ) {
		$this->attribute = $attribute;
		$this->register_options_page();
		$this->set_attribute_data();
	}

	/**
	 * @param $attribute
	 */
	public function set_attribute( $attribute ) {
		$this->attribute = $attribute;
	}

	public function set_attribute_data() {
		if ( ! function_exists( 'get_field' ) ) {
			return;
		}

		$show                                  = get_field( $this->get_slug() . '_show_property', 'option' );
		$this->attribute_data['show_property'] = is_null( $show ) || ! empty( $show );

		$card_show                         = get_field( $this->get_slug() . '_show_card', 'option' );
		$this->attribute_data['show_card'] = ! empty( $card_show );

		$checkbox_full_width                         = get_field( $this->get_slug() . '_checkbox_full_width', 'option' );
		$this->attribute_data['checkbox_full_width'] = ! empty( $checkbox_full_width );

		$display_after                         = get_field( $this->get_slug() . '_display_after', 'option' );
		$this->attribute_data['display_after'] = empty( $display_after ) ? '' : apply_filters( 'wpml_translate_single_string', $display_after, 'myhome-core', $this->attribute->attribute_name . ' display after - ' . $display_after );

		$placeholder                         = get_field( $this->get_slug() . '_placeholder', 'option' );
		$this->attribute_data['placeholder'] = empty( $placeholder ) ? '' : apply_filters( 'wpml_translate_single_string', $placeholder, 'myhome-core', $this->attribute->attribute_name . ' placeholder - ' . $placeholder );

		$placeholder                              = get_field( $this->get_slug() . '_placeholder_from', 'option' );
		$this->attribute_data['placeholder_from'] = empty( $placeholder ) ? '' : apply_filters( 'wpml_translate_single_string', $placeholder, 'myhome-core', $this->attribute->attribute_name . ' placeholder from - ' . $placeholder );

		$placeholder                            = get_field( $this->get_slug() . '_placeholder_to', 'option' );
		$this->attribute_data['placeholder_to'] = empty( $placeholder ) ? '' : apply_filters( 'wpml_translate_single_string', $placeholder, 'myhome-core', $this->attribute->attribute_name . ' placeholder to - ' . $placeholder );

		$form_control                                = get_field( $this->get_slug() . '_search_form_control', 'option' );
		$this->attribute_data['search_form_control'] = empty( $form_control ) ? Field::SELECT : $form_control;

		$icon                         = get_field( $this->get_slug() . '_icon', 'option' );
		$this->attribute_data['icon'] = empty( $icon ) ? '' : $icon;

		$static_values_field = get_field( 'myhome_' . $this->get_slug() . '_static_values', 'option' );
		$static_values       = array();
		if ( ! empty( $static_values_field ) && is_array( $static_values_field ) ) {
			foreach ( $static_values_field as $field ) {
				$static_values[] = (object) array(
					'name'  => apply_filters( 'wpml_translate_single_string', $field['name'], 'myhome-core', $this->attribute->attribute_name . ' (static value) name - ' . $field['name'] ),
					'value' => apply_filters( 'wpml_translate_single_string', $field['value'], 'myhome-core', $this->attribute->attribute_name . ' (static value) value - ' . $field['value'] )
				);
			}
		}
		$this->attribute_data['static_values'] = $static_values;
	}

	/**
	 * @param $attribute
	 *
	 * @return Attribute
	 * @throws \ErrorException
	 */
	public static function get_instance( $attribute ) {
		if ( $attribute->base_slug == self::KEYWORD ) {
			return new Keyword_Attribute( $attribute );
		} elseif ( $attribute->base_slug == self::PRICE ) {
			return new Price_Attribute( $attribute );
		} elseif ( $attribute->base_slug == self::PROPERTY_ID ) {
			return new Property_ID_Attribute( $attribute );
		} elseif ( $attribute->base_slug == self::PROPERTY_TYPE ) {
			return new Property_Type_Attribute( $attribute );
		} elseif ( $attribute->base_slug == self::OFFER_TYPE ) {
			return new Offer_Type_Attribute( $attribute );
		} elseif ( $attribute->attribute_type == self::TEXT ) {
			return new Text_Attribute( $attribute );
		} elseif ( $attribute->attribute_type == self::NUMBER ) {
			return new Number_Attribute( $attribute );
		} elseif ( $attribute->attribute_type == self::TEXTAREA ) {
			return new Text_Area_Attribute( $attribute );
		} elseif ( $attribute->attribute_type == self::WIDGETS ) {
			return new Widgets_Attribute( $attribute );
		} elseif ( $attribute->base_slug == Property_ID_Attribute::BASE_SLUG ) {
			return new Property_ID_Attribute( $attribute );
		}

		throw new \ErrorException( die( 'Unknown attribute type ' . $attribute->attribute_type ) );
	}

	/**
	 * @return bool
	 */
	public function is_estate_attribute() {
		return false;
	}

	/**
	 * @param int $attribute_id
	 *
	 * @return Attribute|\bool
	 */
	public static function get_by_id( $attribute_id ) {
		return Attribute_Factory::get_by_ID( $attribute_id );
	}

	public function register_options_page() {
		$options_page = $this->get_options_page();
		if ( $options_page !== false ) {
			$options_page->register();
		}
	}

	/**
	 * @return int
	 */
	public function get_ID() {
		return intval( $this->attribute->ID );
	}

	/**
	 * @return string
	 */
	public function get_name() {
		return apply_filters( 'wpml_translate_single_string', $this->attribute->attribute_name, 'myhome-core', 'Attribute name - ' . $this->attribute->attribute_name );
	}

	/**
	 * @return string
	 */
	public function get_slug() {
		return $this->attribute->attribute_slug;
	}

	/**
	 * @return int
	 */
	public function get_form_order() {
		return intval( $this->attribute->form_order );
	}

	/**
	 * @return string
	 */
	public function get_base_slug() {
		return $this->attribute->base_slug;
	}

	/**
	 * @since 1.0.5
	 *
	 * @return boolean
	 */
	public function get_property_show() {
		return $this->attribute_data['show_property'];
	}

	/**
	 * @since 1.0.5
	 *
	 * @return boolean
	 */
	public function get_card_show() {
		return $this->attribute_data['show_card'];
	}

	/**
	 * @return string
	 */
	public function get_number_operator() {
		return '=';
	}

	/**
	 * @return int
	 */
	public function get_parent_id() {
		return 0;
	}

	/**
	 * @return bool
	 */
	public function get_tags() {
		return false;
	}

	/**
	 * @return bool
	 */
	public function get_full_width() {
		return $this->attribute_data['checkbox_full_width'];
	}

	/**
	 * @since 1.1
	 *
	 * @return string
	 */
	public function get_full_name() {
		$full_name     = $this->get_name();
		$display_after = $this->get_display_after();
		if ( ! empty( $display_after ) ) {
			$full_name .= ' (' . $display_after . ')';
		}

		return $full_name;
	}

	/**
	 * @return bool
	 */
	public function get_suggestions() {
		return false;
	}

	/**
	 * @return string
	 */
	public function get_display_after() {
		return $this->attribute_data['display_after'];
	}

	/**
	 * @return string
	 */
	public function get_placeholder() {
		return $this->attribute_data['placeholder'];
	}

	/**
	 * @return string
	 */
	public function get_placeholder_from() {
		return $this->attribute_data['placeholder_from'];
	}

	/**
	 * @return string
	 */
	public function get_placeholder_to() {
		return $this->attribute_data['placeholder_to'];
	}

	/**
	 * @return array
	 */
	public function get_static_values() {
		return $this->attribute_data['static_values'];
	}

	/**
	 * @return string
	 */
	public function get_search_form_control() {
		return $this->attribute_data['search_form_control'];
	}

	/**
	 * @param array $attribute_data
	 *
	 * @return Attribute
	 * @throws \ErrorException
	 */
	public static function create( $attribute_data ) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'myhome_attributes';

		$wpdb->insert(
			$table_name,
			array(
				'attribute_name' => $attribute_data['name'],
				'attribute_slug' => self::set_slug( $attribute_data['slug'] ),
				'attribute_type' => $attribute_data['type'],
				'form_order'     => $attribute_data['form_order']
			),
			array( '%s', '%s', '%s', '%d' )
		);

		if ( ! is_int( $wpdb->insert_id ) ) {
			throw new \ErrorException( die( 'Attribute creation failed.' ) );
		}

		$attribute_data = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE ID = %d ", $wpdb->insert_id ) );

		$attribute = Attribute::get_instance( $attribute_data );
		$attribute->set_defaults();

		return $attribute;
	}

	/**
	 * @param array $attribute
	 *
	 * @throws \ErrorException
	 */
	public function update( $attribute ) {
		$this->update_options( $attribute );

		$new_slug = self::set_slug( $attribute['slug'], $attribute['id'] );
		if ( $this->get_type() == self::TEXT && $new_slug != $this->get_slug() ) {
			$this->change_slug( $new_slug, $this->get_slug() );
			$attribute['slug']      = $new_slug;
			$attr                   = json_decode( json_encode( $this->attribute ), true );
			$attr['attribute_slug'] = $attribute['slug'];
			$this->attribute        = (object) $attr;
		}

		global $wpdb;
		$table_name = $wpdb->prefix . 'myhome_attributes';
		$result     = $wpdb->update(
			$table_name,
			array(
				'attribute_name' => $attribute['name'],
				'attribute_slug' => $attribute['slug'],
				'form_order'     => $attribute['form_order'],
			),
			array( 'ID' => intval( $attribute['id'] ) ),
			array( '%s', '%s', '%d' )
		);


		if ( $result === false ) {
			// @todo sprawdzic jak dokladnie wyswietlac errory
			throw new \ErrorException(
				esc_html__( 'Something went wrong during fields order update.', 'myhome-core' ),
				400
			);
		}
	}

	public function change_slug( $new_slug, $old_slug ) {
		$property_type = Attribute_Factory::get_property_type();

		global $wpdb;
		$wpdb->query(
			$wpdb->prepare(
				"
                        UPDATE {$wpdb->options}
                        SET option_name = REPLACE(option_name, %s, %s ), option_value = REPLACE(option_value, %s, %s )
                        WHERE ( option_name LIKE %s OR option_name LIKE %s OR option_name LIKE %s OR option_name LIKE %s ) AND option_name NOT LIKE %s
                        ",
				$old_slug, $new_slug, $old_slug, $new_slug,
				'%options_' . $old_slug . '_%',
				'%' . $property_type->get_slug() . '_%_property_type_' . $old_slug,
				'%' . $old_slug . '_%_term_image%',
				'%' . $old_slug . '_%_property_type_%',
				'%options_' . $old_slug . '_icon%'
			)
		);
		$wpdb->query(
			$wpdb->prepare(
				"
                        UPDATE {$wpdb->options}
                        SET option_name = REPLACE(option_name, %s, %s )
                        WHERE option_name LIKE %s
                        ",
				$old_slug, $new_slug, '%options_' . $old_slug . '_icon%'
			)
		);
		// update taxonomy slug
		$wpdb->update(
			$wpdb->term_taxonomy,
			array( 'taxonomy' => $new_slug ),
			array( 'taxonomy' => $old_slug ),
			array( '%s' ),
			array( '%s' )
		);
	}

	/**
	 * @param int $attribute_id
	 */
	public static function delete( $attribute_id ) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'myhome_attributes';
		$wpdb->delete(
			$table_name,
			array( 'ID' => $attribute_id ),
			'%d'
		);
	}

	/**
	 * @param string $slug
	 * @param int    $id
	 *
	 * @return string
	 */
	public static function set_slug( $slug, $id = 0 ) {
		$slug         = mb_strtolower( $slug, 'UTF-8' );
		$slug_counter = 1;
		$flag         = true;
		global $wpdb;
		$table_name = $wpdb->prefix . 'myhome_attributes';

		while ( $flag ) {
			$temp_slug = $slug_counter == 1 ? $slug : $slug . '_' . $slug_counter;
			$results   = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table_name WHERE attribute_slug = %s AND ID != %d", $temp_slug, $id ) );
			if ( count( $results ) == 0 ) {
				return $temp_slug;
			}
			$slug_counter ++;
		}
	}

	/**
	 * @return \string
	 */
	public function get_icon() {
		return $this->attribute_data['icon'];
	}

	/**
	 * @return mixed
	 */
	public function get_icon_class() {
		if ( ! empty( $this->attribute_data['icon'] ) && strpos( $this->attribute_data['icon'], 'fa-' ) !== false ) {
			return 'fa ' . $this->attribute_data['icon'];
		}

		if ( ! empty( $this->attribute_data['icon'] ) && strpos( $this->attribute_data['icon'], 'flatfront' ) !== false ) {
			return str_replace( 'flatfront', 'flaticon', $this->attribute_data['icon'] );
		}

		return $this->attribute_data['icon'];
	}

	/**
	 * @return bool
	 */
	public function has_icon() {
		return ! empty( $this->attribute_data['icon'] );
	}

	/**
	 * @return array
	 */
	public function get_data() {
		return array(
			'id'         => $this->get_ID(),
			'name'       => $this->get_name(),
			'full_name'  => $this->get_full_name(),
			'base_slug'  => $this->get_base_slug(),
			'slug'       => $this->get_slug(),
			'type'       => $this->get_type(),
			'type_name'  => $this->get_type_name(),
			'form_order' => $this->get_form_order()
		);
	}

	/**
	 * @return bool
	 */
	public function exclude_from_search_form() {
		return false;
	}

	/**
	 * @return array
	 */
	public function get_property_type_dependency() {
		$cache_key = 'myhome_cache_attribute_' . $this->get_ID() . '_property_type_dependency';

		if ( ! \MyHomeCore\My_Home_Core()->development_mode && false !== ( $dependencies = get_transient( $cache_key ) ) ) {
			return $dependencies;
		}

		$dependencies  = array( 'all' => true );
		$property_type = Attribute_Factory::get_property_type();

		if ( $property_type == false ) {
			return $dependencies;
		}

		foreach ( Term_Factory::get( $property_type ) as $term ) {
			$dependency                        = get_field( 'property_type_' . $this->get_slug(), 'term_' . $term->get_ID() );
			$dependencies[ $term->get_slug() ] = is_null( $dependency ) || ! empty( $dependency );
		}

		foreach ( $dependencies as $key => $value ) {
			if ( ! $value ) {
				$dependencies['all'] = false;
				break;
			}
		}

		set_transient( 'myhome_cache_attribute_' . $this->get_ID() . '_property_type_dependency', $dependencies, DAY_IN_SECONDS );

		return $dependencies;
	}


	/**
	 * @param array $fields
	 *
	 * @return array
	 */
	public function get_vc_control( $fields ) {
		return $fields;
	}

	protected function set_defaults() {
	}

	/**
	 * @return bool
	 */
	public function is_in_breadcrumbs() {
		return false;
	}

}