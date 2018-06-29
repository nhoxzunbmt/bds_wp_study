<?php

namespace MyHomeCore\Shortcodes;

use MyHomeCore\Attributes\Attribute;
use MyHomeCore\Attributes\Attribute_Factory;
use MyHomeCore\Attributes\Text_Attribute;
use MyHomeCore\Terms\Term_Factory;


/**
 * Class Attribute_Carousel_Shortcode
 * @package MyHomeCore\Shortcodes
 */
class Attribute_Carousel_Shortcode extends Shortcode {

	/**
	 * @param array $args
	 * @param null  $content
	 *
	 * @return string
	 */
	public function display( $args = array(), $content = null ) {
		wp_enqueue_script( 'myhome-carousel' );

		if ( function_exists( 'vc_map_get_attributes' ) ) {
			$args = array_merge( $args, vc_map_get_attributes( 'mh_carousel_attribute', $args ) );
		}
		$class = $args['dots'] . ' ' . $args['visible_number'];
		if ( $args['owl_auto_play'] != 'true' ) {
			$class .= ' owl-carousel--no-auto-play';
		}

		/* @var Text_Attribute $attribute */
		$attribute = Attribute::get_by_id( $args['attribute'] );

		if ( $attribute == false || ! $attribute instanceof Text_Attribute ) {
			return '';
		}

		global $myhome_attribute_carousel;
		$myhome_attribute_carousel          = $args;
		$myhome_attribute_carousel['class'] = $class;
		global $myhome_terms;
		$myhome_terms = Term_Factory::get( $attribute, $args['total_number'] );

		return $this->get_template();
	}

	/**
	 * @return array
	 */
	public function get_vc_params() {
		$attributes_list = array();
		$attributes      = Attribute_Factory::get_text();
		foreach ( $attributes as $attribute ) {
			$attributes_list[ $attribute->get_name() ] = $attribute->get_ID();
		}

		return array(
			// Attribute
			array(
				'type'        => 'dropdown',
				'heading'     => esc_html__( 'Attribute', 'myhome-core' ),
				'description' => esc_html__( 'It shows options with at least one property assigned e.g. it will not show Washington if it has not at least one property assigned', 'myhome-core' ),
				'param_name'  => 'attribute',
				'value'       => $attributes_list,
				'group'       => esc_html__( 'General', 'myhome-core' ),
				'save_always' => true
			),
			// Visible number
			array(
				'group'       => esc_html__( 'General', 'myhome-core' ),
				'type'        => 'dropdown',
				'heading'     => esc_html__( 'Visible number', 'myhome-core' ),
				'param_name'  => 'visible_number',
				'value'       => array(
					esc_html__( 'Default - 3', 'myhome-core' ) => 'owl-carousel--visible-3',
					esc_html__( '1 ', 'myhome-core' )          => 'owl-carousel--visible-1',
					esc_html__( '2 ', 'myhome-core' )          => 'owl-carousel--visible-2',
					esc_html__( '3 ', 'myhome-core' )          => 'owl-carousel--visible-3',
					esc_html__( '4 ', 'myhome-core' )          => 'owl-carousel--visible-4',
					esc_html__( '5 ', 'myhome-core' )          => 'owl-carousel--visible-5',
				),
				'save_always' => true
			),
			// Total number
			array(
				'group'       => esc_html__( 'General', 'myhome-core' ),
				'type'        => 'textfield',
				'heading'     => esc_html__( 'Total elements number', 'myhome-core' ),
				'param_name'  => 'total_number',
				'value'       => 5,
				'description' => esc_html__( '0 or empty = all elements', 'myhome-core' ),
				'save_always' => true
			),
			// Dots
			array(
				'group'       => esc_html__( 'General', 'myhome-core' ),
				'type'        => 'dropdown',
				'heading'     => esc_html__( 'Dots', 'myhome-core' ),
				'param_name'  => 'dots',
				'value'       => array(
					esc_html__( 'Yes', 'myhome-core' ) => '',
					esc_html__( 'No', 'myhome-core' )  => 'owl-carousel--no-dots',
				),
				'save_always' => true
			),
			// Auto play
			array(
				'group'       => esc_html__( 'General', 'myhome-core' ),
				'type'        => 'checkbox',
				'heading'     => esc_html__( 'Auto play', 'myhome-core' ),
				'param_name'  => 'owl_auto_play',
				'value'       => 'true',
				'std'         => 'true',
				'save_always' => true
			)
		);
	}

}