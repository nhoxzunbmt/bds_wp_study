<?php

namespace MyHomeCore\Shortcodes;


use MyHomeCore\Users\Users_Factory;

/**
 * Class List_Agent_shortcode
 * @package MyHomeCore\Shortcodes
 */
class List_Agent_shortcode extends Shortcode {

	/**
	 * @param array $args
	 * @param null  $content
	 *
	 * @return string
	 */
	public function display( $args = array(), $content = null ) {
		$atts = array(
			'limit'                  => 5,
			'agent_style'            => '',
			'description_show'       => 1,
			'social_icons_show'      => 1,
			'email_show'             => 1,
			'phone_show'             => 1,
			'button_show'            => 1,
			'exclude_admin'          => 'true',
			'additional_fields_show' => 1
		);

		if ( function_exists( 'vc_map_get_attributes' ) ) {
			$atts = array_merge( $atts, vc_map_get_attributes( 'mh_list_agent', $args ) );
		}

		$exclude_admin = $atts['exclude_admin'] == 'true';

		global $myhome_agents_list;
		$myhome_agents_list           = $atts;
		$myhome_agents_list['agents'] = Users_Factory::get_agents( $atts['limit'], $exclude_admin );

		return $this->get_template();
	}

	/**
	 * @return array
	 */
	public function get_vc_params() {
		return array(
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
			// Show additional fields
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
			)
		);
	}

}