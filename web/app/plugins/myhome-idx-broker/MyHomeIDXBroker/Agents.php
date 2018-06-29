<?php

namespace MyHomeIDXBroker;


/**
 * Class Agents
 * @package MyHomeIDXBroker
 */
class Agents {

	const FIELD_ID = 'idx_broker_user_id';
	const FIELD_LISTING_ID = 'idx_broker_user_listing_id';

	public function import() {
		$api    = new Api();
		$agents = $api->get_agents();

		if ( empty( $agents ) ) {
			return;
		}

		foreach ( $agents as $agent ) {
			if ( $this->exists( $agent->agentID ) ) {
				continue;
			}

			$this->create( $agent );
		}
	}

	/**
	 * @param $agent_idx_id
	 *
	 * @return bool
	 */
	public function exists( $agent_idx_id ) {
		$users = get_users(
			array(
				'meta_key'   => Agents::FIELD_ID,
				'meta_value' => $agent_idx_id
			)
		);

		return count( $users );
	}

	/**
	 * @param $agent_data
	 *
	 * @return bool|int
	 */
	public function create( $agent_data ) {
		if ( ! isset( $agent_data->agentDisplayName ) || empty( $agent_data->agentDisplayName ) ) {
			return false;
		}

		$user_id = wp_create_user(
			$agent_data->agentDisplayName,
			wp_generate_password( $length = 12, $include_standard_special_chars = false )
		);

		if ( is_wp_error( $user_id ) ) {
			return false;
		}

		$user_data = array(
			'ID'   => $user_id,
			'role' => 'agent'
		);

		if ( isset( $agent_data->agentFirstName ) && ! empty( $agent_data->agentFirstName ) ) {
			$user_data['first_name'] = $agent_data->agentFirstName;
		}

		if ( isset( $agent_data->agentLastName ) && ! empty( $agent_data->agentLastName ) ) {
			$user_data['last_name'] = $agent_data->agentLastName;
		}

		if ( isset( $agent_data->agentEmail ) && ! empty( $agent_data->agentEmail ) ) {
			$user_data['user_email'] = $agent_data->agentEmail;
		}

		wp_update_user( $user_data );
		update_user_meta( $user_id, Agents::FIELD_ID, $agent_data->agentID );
		update_user_meta( $user_id, Agents::FIELD_LISTING_ID, $agent_data->listingAgentID );

		if ( isset( $agent_data->agentCellPhone ) && ! empty( $agent_data->agentCellPhone ) ) {
			update_user_meta( $user_id, 'agent_phone', $agent_data->agentCellPhone );
		}

		return $user_id;
	}

	/**
	 * @return Idx_Broker_Agent[]
	 */
	public static function get() {
		$agents = array();
		$users  = get_users(
			array(
				'meta_key' => Agents::FIELD_ID,
				'compare'  => 'EXISTS'
			)
		);

		foreach ( $users as $user ) {
			$agents[] = new Idx_Broker_Agent( $user );
		}

		return $agents;
	}

}