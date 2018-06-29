<?php

namespace MyHomeCore\Panel;


use MyHomeCore\Users\Agents_Manager;
use MyHomeCore\Users\Users_Ajax;

/**
 * Class Panel
 * @package MyHomeCore\Panel
 */
class Panel {

	/**
	 * @var Users_Ajax
	 */
	private $users;

	/**
	 * @var Panel_Settings
	 */
	private $panel_settings;

	/**
	 * @var Panel_Notifications
	 */
	private $notifications;

	/**
	 * @var Agents_Manager
	 */
	private $agents_manager;

	/**
	 * Panel constructor.
	 */
	public function __construct() {
		$this->panel_settings = new Panel_Settings();

		if ( ! empty( \MyHomeCore\My_Home_Core()->settings->props['mh-agent-panel'] ) ) {
			$this->agents_manager = new Agents_Manager();
			$this->notifications  = new Panel_Notifications();
			$this->users          = new Users_Ajax();
		}
	}

}