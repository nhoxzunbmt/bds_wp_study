<?php

namespace MyHomeCore\Users;


/**
 * Class Users_Factory
 * @package MyHomeCore\Users
 */
class Users_Factory {

	/**
	 * @param int $limit
	 * @param bool $exclude_admins
	 *
	 * @return User[]
	 */
	public static function get_agents( $limit = - 1, $exclude_admins = true ) {
		$roles = array(
			'agent'
		);
		if ( ! $exclude_admins ) {
			$roles[] = 'administrator';
		}

		$users = get_users( array(
			'role__in' => $roles,
			'number'   => $limit
		) );

		$agents = array();
		foreach ( $users as $user ) {
			$agents[] = User::get_instance( $user );
		}

		return $agents;
	}

	/**
	 * @return User
	 */
	public static function get_current() {
		$user_id = get_current_user_id();
		return User::get_instance( $user_id );
	}

}