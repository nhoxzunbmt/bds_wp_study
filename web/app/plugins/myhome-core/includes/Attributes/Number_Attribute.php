<?php

namespace MyHomeCore\Attributes;


use MyHomeCore\Components\Listing\Form\Field;
use MyHomeCore\Estates\Filters\Estate_Number_Filter;

/**
 * Class Number_Attribute
 * @package MyHomeCore\Attributes
 */
class Number_Attribute extends Attribute {

	/**
	 * Number_Attribute constructor.
	 *
	 * @param $attribute
	 */
	public function __construct( $attribute ) {
		parent::__construct( $attribute );

		$this->set_number_attribute_data();
	}

	public function set_number_attribute_data() {
		if ( ! function_exists( 'get_field' ) ) {
			return;
		}

		$suggestions                         = get_field( $this->get_slug() . '_suggestions', 'option' );
		$this->attribute_data['suggestions'] = ! empty( $suggestions );

		$number_operator                         = get_field( $this->get_slug() . '_number_operator', 'option' );
		$this->attribute_data['number_operator'] = empty( $number_operator ) ? 'equal' : $number_operator;

		$checkbox_move                         = get_field( $this->get_slug() . '_checkbox_move', 'option' );
		$this->attribute_data['checkbox_move'] = is_null( $checkbox_move ) || ! empty( $checkbox_move );
	}

	/**
	 * @return \bool
	 */
	public function get_checkbox_move() {
		return $this->attribute_data['checkbox_move'];
	}

	/**
	 * @return bool
	 */
	public function has_archive() {
		return false;
	}

	/**
	 * @return string
	 */
	public function get_type() {
		return self::NUMBER;
	}

	/**
	 * @return string
	 */
	public function get_type_name() {
		return esc_html__( 'Number field', 'myhome-core' );
	}

	/**
	 * @return Number_Attribute_Options_Page
	 */
	public function get_options_page() {
		return new Number_Attribute_Options_Page( $this );
	}

	/**
	 * @param int $estate_id
	 *
	 * @return Attribute_Values
	 */
	public function get_estate_values( $estate_id ) {
		$value = get_field( 'estate_attr_' . $this->get_slug(), $estate_id );
		if ( empty( $value ) ) {
			$values = array();
		} else {
			if ( ( $display_after = $this->get_display_after() ) !== '' ) {
				$name = $value . ' ' . $display_after;
			} else {
				$name = $value;
			}

			$values = array( new Attribute_Value( $name, $value, '', $this->get_slug() ) );
		}

		return new Attribute_Values( $values );
	}

	/**
	 * @return bool
	 */
	public function is_estate_attribute() {
		return true;
	}

	/**
	 * @param bool $all_values
	 *
	 * @return Attribute_Values[]
	 */
	public function get_values( $all_values = false ) {
		$form_control = $this->get_search_form_control();

		if ( ( $form_control == Field::TEXT || $form_control == Field::TEXT_RANGE ) && ! $this->get_suggestions() ) {
			return array( 'any' => new Attribute_Values() );
		}

		$values        = array();
		$static_values = $this->get_static_values();
		foreach ( $static_values as $static_value ) {
			$values[] = new Attribute_Value( $static_value->name, $static_value->name, '', $static_value->value );
		}

		return array( 'any' => new Attribute_Values( $values ) );
	}

	/**
	 * @return bool
	 */
	public function get_suggestions() {
		return $this->attribute_data['suggestions'];
	}

	/**
	 * @return string
	 */
	public function get_number_operator() {
		return $this->attribute_data['number_operator'];
	}

	/**
	 * @return array
	 */
	public function get_data() {
		return array(
			'id'                  => $this->get_ID(),
			'name'                => $this->get_name(),
			'full_name'           => $this->get_full_name(),
			'base_slug'           => $this->get_base_slug(),
			'slug'                => $this->get_slug(),
			'type'                => $this->get_type(),
			'type_name'           => $this->get_type_name(),
			'form_order'          => $this->get_form_order(),
			'display_after'       => $this->get_display_after(),
			'placeholder'         => $this->get_placeholder(),
			'placeholder_from'    => $this->get_placeholder_from(),
			'placeholder_to'      => $this->get_placeholder_to(),
			'search_form_control' => $this->get_search_form_control(),
			'static_values'       => $this->get_static_values(),
			'full_width'          => $this->get_full_width(),
			'card_show'           => $this->get_card_show(),
			'property_show'       => $this->get_property_show(),
			'number_operator'     => $this->get_number_operator(),
			'suggestions'         => $this->get_suggestions(),
			'icon'                => $this->get_icon(),
			'icon_class'          => $this->get_icon_class(),
			'checkbox_move'       => $this->get_checkbox_move()
		);
	}

	/**
	 * @param array $attribute_data
	 */
	public function update_options( $attribute_data ) {
		$options_page = $this->get_options_page();
		$options_page->update_options( $attribute_data );
	}

	/**
	 * @param Attribute_Values $attribute_values
	 * @param string           $compare
	 *
	 * @return Estate_Number_Filter
	 */
	public function get_estate_filter( Attribute_Values $attribute_values, $compare = '=' ) {
		return new Estate_Number_Filter( $this, $attribute_values, $compare );
	}

	/**
	 * @param array $fields
	 *
	 * @return array
	 */
	public function get_vc_control( $fields ) {
		$form_control = $this->get_search_form_control();
		if ( $form_control == Field::SELECT_RANGE || $form_control == Field::TEXT_RANGE ) {
			$fields[] = array(
				'type'        => 'textfield',
				'heading'     => sprintf( esc_html__( '%s from', 'myhome-core' ), $this->get_name() ),
				'param_name'  => $this->get_slug() . '_from',
				'group'       => esc_html__( 'Default values', 'myhome-core' ),
				'save_always' => true
			);
			$fields[] = array(
				'type'        => 'textfield',
				'heading'     => sprintf( esc_html__( '%s to', 'myhome-core' ), $this->get_name() ),
				'param_name'  => $this->get_slug() . '_to',
				'group'       => esc_html__( 'Default values', 'myhome-core' ),
				'save_always' => true
			);
		} else {
			$fields[] = array(
				'type'        => 'textfield',
				'heading'     => $this->get_name(),
				'param_name'  => $this->get_slug(),
				'group'       => esc_html__( 'Default values', 'myhome-core' ),
				'save_always' => true
			);
		}

		return $fields;
	}

	/**
	 * @param int   $estate_id
	 * @param array $values
	 */
	public function update_estate_values( $estate_id, $values ) {
		$value = count( $values ) > 0 ? $values[0] : '';

		update_post_meta( $estate_id, 'estate_attr_' . $this->get_slug(), $value );
	}

	protected function set_defaults() {
		update_field( $this->get_slug() . '_search_form_control', Field::TEXT_RANGE, 'option' );
	}

}