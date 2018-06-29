<?php

namespace MyHomeCore\Users;


use MyHomeCore\Users\Fields\Settings;

/**
 * Class User_Settings
 * @package MyHomeCore\Users
 */
class User_Settings {

	/**
	 * @var Settings
	 */
	private $fields_settings;

	/**
	 * User_Settings constructor.
	 */
	public function __construct() {
		add_action( 'redux/options/myhome_redux/saved', array( 'MyHomeCore\Users\User_Settings', 'update_caps' ) );
		add_action( 'redux/options/myhome_redux/reset', array( 'MyHomeCore\Users\User_Settings', 'update_caps' ) );
		add_filter( 'wp_dropdown_users_args', array( $this, 'users_dropdown' ) );

		$this->fields_settings = new Settings();
		$this->register_fields();
	}

	/**
	 * @param array $query_args
	 *
	 * @return array
	 */
	public function users_dropdown( $query_args ) {
		$query_args['who'] = '';

		return $query_args;
	}

	public function register_fields() {
		if ( ! function_exists( 'acf_add_local_field_group' ) ) {
			return;
		}

		$fields = array(
			'myhome_agent_image'     => array(
				'key'   => 'myhome_agent_image',
				'label' => esc_html__( 'Image', 'myhome-core' ),
				'name'  => 'agent_image',
				'type'  => 'image',
			),
			'myhome_agent_phone'     => array(
				'key'   => 'myhome_agent_phone',
				'label' => esc_html__( 'Phone', 'myhome-core' ),
				'name'  => 'agent_phone',
				'type'  => 'text',
			),
			'myhome_agent_facebook'  => array(
				'key'   => 'myhome_agent_facebook',
				'label' => esc_html__( 'Facebook', 'myhome-core' ),
				'name'  => 'agent_facebook',
				'type'  => 'text',
			),
			'myhome_agent_twitter'   => array(
				'key'   => 'myhome_agent_twitter',
				'label' => esc_html__( 'Twitter', 'myhome-core' ),
				'name'  => 'agent_twitter',
				'type'  => 'text',
			),
			'myhome_agent_instagram' => array(
				'key'   => 'myhome_agent_instagram',
				'label' => esc_html__( 'Instagram', 'myhome-core' ),
				'name'  => 'agent_instagram',
				'type'  => 'text',
			),
			'myhome_agent_linkedin'  => array(
				'key'   => 'myhome_agent_linkedin',
				'label' => esc_html__( 'Linkedin', 'myhome-core' ),
				'name'  => 'agent_linkedin',
				'type'  => 'text',
			),
		);

		foreach ( Settings::get_fields() as $field ) {
			$fields[] = array(
				'key'   => 'myhome_agent_' . $field['slug'],
				'label' => $field['name'],
				'name'  => 'agent_' . $field['slug'],
				'type'  => 'text'
			);
		}

		acf_add_local_field_group(
			array(
				'key'      => 'myhome_agent',
				'title'    => esc_html__( 'Agent', 'myhome-core' ),
				'location' => array(
					array(
						array(
							'param'    => 'user_role',
							'operator' => '==',
							'value'    => 'agent',
						)
					),
					array(
						array(
							'param'    => 'user_role',
							'operator' => '==',
							'value'    => 'administrator',
						)
					)
				),
				'fields'   => $fields
			)
		);
	}

	public static function create_roles() {
		remove_role( 'agent' );
		add_role(
			'agent', esc_html__( 'Agent', 'myhome-core' ), array(
				'read'          => true,
				'edit_posts'    => true,
				'delete_posts'  => true,
				'publish_posts' => true,
				'level_1'       => true,
				'level_0'       => true
			)
		);

		User_Settings::update_caps();
	}

	public static function update_caps() {
		$roles = array( 'administrator', 'agent' );
		// Loop through each role and assign capabilities
		foreach ( $roles as $role ) {
			$role = get_role( $role );
			$role->add_cap( 'read' );
			$role->add_cap( 'read_estate' );
			$role->add_cap( 'delete_estate' );
			$role->add_cap( 'edit_estates' );
			$role->add_cap( 'edit_others_estates' );
			$role->add_cap( 'publish_estates' );
			$role->add_cap( 'read_private_estates' );
			$role->add_cap( 'delete_estates' );
			$role->add_cap( 'delete_private_estates' );
			$role->add_cap( 'delete_published_estates' );
			$role->add_cap( 'delete_others_estates' );
			$role->add_cap( 'edit_private_estates' );
			$role->add_cap( 'edit_published_estates' );
			$role->add_cap( 'create_estates' );
			$role->add_cap( 'agent_cap' );
			$role->add_cap( 'upload_files' );
		}

		$agent = get_role( 'agent' );
		if ( is_null( $agent ) ) {
			return;
		}

		$agent->add_cap( 'read' );
		$agent->add_cap( 'read_estate' );
		$agent->add_cap( 'delete_estate' );
		$agent->add_cap( 'edit_estates' );
		$agent->add_cap( 'read_private_estates' );
		$agent->add_cap( 'delete_estates' );
		$agent->add_cap( 'delete_private_estates' );
		$agent->add_cap( 'delete_published_estates' );
		$agent->add_cap( 'edit_private_estates' );
		$agent->add_cap( 'edit_published_estates' );
		$agent->add_cap( 'create_estates' );
		$agent->add_cap( 'agent_cap' );
		$agent->add_cap( 'upload_files' );
	}

}