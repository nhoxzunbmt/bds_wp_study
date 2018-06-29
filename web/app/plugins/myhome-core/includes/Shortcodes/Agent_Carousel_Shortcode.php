<?php

namespace MyHomeCore\Shortcodes;

use MyHomeCore\Users\Users_Factory;


/**
 * Class Agent_Carousel_Shortcode
 * @package MyHomeCore\Shortcodes
 */
class Agent_Carousel_Shortcode extends Shortcode {

	/**
	 * @param array       $args
	 * @param string|null $content
	 *
	 * @return string
	 */
	public function display( $args = array(), $content = null ) {
		wp_enqueue_script( 'myhome-carousel' );

		if ( function_exists( 'vc_map_get_attributes' ) ) {
			$args = array_merge( $args, vc_map_get_attributes( 'mh_carousel_agent', $args ) );
		}

		$exclude_admin = $args['exclude_admin'] == 'true';
		global $myhome_carousel_agents;
		$myhome_carousel_agents = Users_Factory::get_agents( $args['limit'], $exclude_admin );
		global $myhome_carousel_settings;
		$myhome_carousel_settings = array(
			'class'                  => $args['owl_visible'] . ' ' . $args['owl_dots'],
			'style'                  => $args['agent_style'],
			'email_show'             => $args['email_show'],
			'phone_show'             => $args['phone_show'],
			'button_show'            => $args['button_show'],
			'description_show'       => $args['description_show'],
			'social_icons_show'      => $args['social_icons_show'],
			'additional_fields_show' => ! isset( $args['additional_fields_show'] ) ? 1 : $args['additional_fields_show']
		);

		if ( $args['owl_auto_play'] != 'true' ) {
			$myhome_carousel_settings['class'] .= ' owl-carousel--no-auto-play';
		}

		return $this->get_template();
	}

	/**
	 * @return array
	 */
	public function get_vc_params() {
		return array(
			// Visible
			array(
				'type'       => 'dropdown',
				'heading'    => esc_html__( 'Visible', 'myhome-core' ),
				'param_name' => 'owl_visible',
				'value'      => array(
					esc_html__( 'Default - 3', 'myhome-core' ) => 'owl-carousel--visible-3',
					esc_html__( '1 ', 'myhome-core' )          => 'owl-carousel--visible-1',
					esc_html__( '2 ', 'myhome-core' )          => 'owl-carousel--visible-2',
					esc_html__( '3 ', 'myhome-core' )          => 'owl-carousel--visible-3',
					esc_html__( '4 ', 'myhome-core' )          => 'owl-carousel--visible-4',
				)
			),
			// Agents limit
			array(
				'type'       => 'textfield',
				'heading'    => esc_html__( 'Agents limit', 'myhome-core' ),
				'param_name' => 'limit',
				'value'      => 5,
			),
			// Exclude admins (only agent role)
			array(
				'type'       => 'checkbox',
				'heading'    => esc_html__( 'Exclude admins', 'myhome-core' ),
				'param_name' => 'exclude_admin',
				'value'      => 'true',
				'std'        => 'true',
			),
			// Style
			array(
				'type'       => 'dropdown',
				'heading'    => esc_html__( 'Style', 'myhome-core' ),
				'param_name' => 'agent_style',
				'value'      => array(
					esc_html__( 'Default', 'myhome-core' )          => '',
					esc_html__( 'White Background', 'myhome-core' ) => 'mh-agent--white',
					esc_html__( 'Dark Background', 'myhome-core' )  => 'mh-agent--dark',
				),
			),
			// Show description
			array(
				'type'       => 'dropdown',
				'heading'    => esc_html__( 'Show description', 'myhome-core' ),
				'param_name' => 'description_show',
				'value'      => array(
					esc_html__( 'Yes', 'myhome-core' ) => 1,
					esc_html__( 'No', 'myhome-core' )  => 0,
				),
			),
			// Show additional_fields
			array(
				'type'       => 'dropdown',
				'heading'    => esc_html__( 'Show additional fields', 'myhome-core' ),
				'param_name' => 'additional_fields_show',
				'value'      => array(
					esc_html__( 'Yes', 'myhome-core' ) => 1,
					esc_html__( 'No', 'myhome-core' )  => 0,
				),
			),
			// Show Social Icons
			array(
				'type'       => 'dropdown',
				'heading'    => esc_html__( 'Show social icons', 'myhome-core' ),
				'param_name' => 'social_icons_show',
				'value'      => array(
					esc_html__( 'Yes', 'myhome-core' ) => 1,
					esc_html__( 'No', 'myhome-core' )  => 0,
				),
			),
			// Show email
			array(
				'type'       => 'dropdown',
				'heading'    => esc_html__( 'Show Email', 'myhome-core' ),
				'param_name' => 'email_show',
				'value'      => array(
					esc_html__( 'Yes', 'myhome-core' ) => 1,
					esc_html__( 'No', 'myhome-core' )  => 0,
				),
			),
			// Show phone
			array(
				'type'       => 'dropdown',
				'heading'    => esc_html__( 'Show phone', 'myhome-core' ),
				'param_name' => 'phone_show',
				'value'      => array(
					esc_html__( 'Yes', 'myhome-core' ) => 1,
					esc_html__( 'No', 'myhome-core' )  => 0,
				),
			),
			// Show button
			array(
				'type'       => 'dropdown',
				'heading'    => esc_html__( 'Show button', 'myhome-core' ),
				'param_name' => 'button_show',
				'value'      => array(
					esc_html__( 'Yes', 'myhome-core' ) => 1,
					esc_html__( 'No', 'myhome-core' )  => 0,
				),
			),
			// Dots
			array(
				'type'       => 'dropdown',
				'heading'    => esc_html__( 'Dots', 'myhome-core' ),
				'param_name' => 'owl_dots',
				'value'      => array(
					esc_html__( 'Yes', 'myhome-core' ) => '',
					esc_html__( 'No', 'myhome-core' )  => 'owl-carousel--no-dots',
				),
			),
			// Auto play
			array(
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