<?php

namespace MyHomeIDXBroker;


use MyHomeCore\Users\Agent;

/**
 * Class Idx_Broker_Agent
 * @package MyHomeIDXBroker
 */
class Idx_Broker_Agent extends Agent {

	/**
	 * @return string
	 */
	public function get_idx_broker_id() {
		return get_user_meta( $this->get_ID(), Agents::FIELD_ID, true );
	}

	/**
	 * @return string
	 */
	public function get_idx_broker_listing_id() {
		return get_user_meta( $this->get_ID(), Agents::FIELD_LISTING_ID, true );
	}

	/**
	 * @param $idx_broker_id
	 *
	 * @return bool|Idx_Broker_Agent
	 */
	public static function get_by_idx_broker_id( $idx_broker_id ) {
		$users = get_users( array(
			'meta_key'   => Agents::FIELD_ID,
			'meta_value' => $idx_broker_id
		) );

		if ( count( $users ) ) {
			return new Idx_Broker_Agent( $users[0] );
		}

		return false;
	}

}