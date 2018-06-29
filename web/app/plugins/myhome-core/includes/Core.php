<?php

namespace MyHomeCore;


use MyHomeCore\Api\Estates_Api;
use MyHomeCore\Attributes\Attribute_Admin_Ajax;
use MyHomeCore\Attributes\Attribute_Factory;
use MyHomeCore\Attributes\Attributes_Settings;
use MyHomeCore\Cache\Cache;
use MyHomeCore\Components\Listing\Search_Forms\Search_Forms_Admin_Ajax;
use MyHomeCore\Integrations\ESSB\ESSB_Init;
use MyHomeCore\Estates\Elements\Estate_Elements_Settings;
use MyHomeCore\Estates\Estate_Settings;
use MyHomeCore\Integrations\Yoast\Yoast_Init;
use MyHomeCore\Panel\Panel;
use MyHomeCore\Shortcodes\Shortcodes;
use MyHomeCore\Users\User_Settings;

class Core {

	const VERSION = '2.1.1';

	private static $instance = false;

	/**
	 * @var Settings
	 */
	public $settings;

	/**
	 * @var Attribute_Factory
	 */
	public $attributes;

	/**
	 * @var User_Settings
	 */
	public $user_settings;

	/**
	 * @var Attributes_Settings
	 */
	public $attributes_settings;

	/**
	 * @var Attribute_Admin_Ajax
	 */
	public $attribute_admin_ajax;

	/**
	 * @var Estate_Settings
	 */
	public $estates_settings;

	/**
	 * @var Estate_Elements_Settings
	 */
	public $estate_elements_settings;

	/**
	 * @var Search_Forms_Admin_Ajax
	 */
	public $search_form_settings;

	/**
	 * @var Shortcodes
	 */
	public $shortcodes;

	/**
	 * @var Rewrite
	 */
	public $rewrite;

	/**
	 * @var Estates_Api
	 */
	public $api;

	/**
	 * @var Panel
	 */
	public $panel;

	/**
	 * @var string
	 */
	public $currency = 'any';

	/**
	 * @var string
	 */
	public $current_language;

	/**
	 * @var \string
	 */
	public $default_language;

	/**
	 * @var array
	 */
	public $languages;

	/**
	 * @var Post_Types
	 */
	public $post_types;

	/**
	 * @var Yoast_Init
	 */
	public $yoast;

	/**
	 * @var Essb_Init()
	 */
	public $essb;

	/**
	 * @var Cache
	 */
	public $cache;

	/**
	 * @var bool
	 */
	public $development_mode;

	/**
	 * @return Core
	 */
	public static function get_instance() {
		if ( ! self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	public function init() {
		$this->settings            = new Settings();
		$this->cache               = new Cache();
		$this->development_mode    = ! empty( $this->settings->props['mh-development'] ) || current_user_can( 'administrator' );
		$this->languages           = apply_filters( 'wpml_active_languages', $this->languages );
		$this->current_language    = apply_filters( 'wpml_current_language', $this->current_language );
		$this->default_language    = apply_filters( 'wpml_default_language', $this->default_language );
		$this->attributes_settings = new Attributes_Settings();
		$this->post_types          = new Post_Types();
		$this->estates_settings    = new Estate_Settings();
		$this->rewrite             = new Rewrite();
		$this->user_settings       = new User_Settings();
		$this->shortcodes          = new Shortcodes();
		$this->api                 = new Estates_Api();
		$this->panel               = new Panel();

		if ( function_exists( 'is_plugin_active' ) && is_plugin_active( 'wordpress-seo/wp-seo.php' ) ) {
			$this->yoast = new Yoast_Init();
		}

		$this->essb = new ESSB_Init();

		if ( isset( $_COOKIE['myhome_currency'] ) && ! empty( $_COOKIE['myhome_currency'] ) && $_COOKIE['myhome_currency'] !== 'undefined' ) {
			$this->currency = $_COOKIE['myhome_currency'];
		} elseif ( ! empty( $this->settings->props['mh-currency_switcher-default'] ) ) {
			$this->currency = $this->settings->props['mh-currency_switcher-default'];
		}

		if ( is_user_logged_in() && is_admin() ) {
			$this->attribute_admin_ajax     = new Attribute_Admin_Ajax();
			$this->estate_elements_settings = new Estate_Elements_Settings();
			$this->search_form_settings     = new Search_Forms_Admin_Ajax();

			$current_version = get_option( 'myhome-core-version' );
			if ( empty( $current_version ) || $current_version !== Core::VERSION ) {
				do_action( 'myhome_reload_cache' );
				update_option( 'myhome-core-version', Core::VERSION );
			}
		}

		add_action(
			'wp_ajax_nopriv_myhome_contact_form_send',
			array( 'MyHomeCore\Components\Contact_Form\Contact_Form_Single_Property', 'mail' )
		);
		add_action(
			'wp_ajax_myhome_contact_form_send',
			array( 'MyHomeCore\Components\Contact_Form\Contact_Form_Single_Property', 'mail' )
		);

		add_action( 'widgets_init', array( $this, 'register_widgets' ) );
		add_action( 'init', array( $this, 'load_text_domain' ) );
	}

	public function load_text_domain() {
		load_plugin_textdomain( 'myhome-core', false, MYHOME_CORE_PATH . '/languages' );
	}

	public function register_widgets() {
		register_widget( 'MyHomeCore\Widgets\Facebook_Widget' );
		register_widget( 'MyHomeCore\Widgets\Twitter_Widget' );
		register_widget( 'MyHomeCore\Widgets\Social_Icons_Widget' );
		register_widget( 'MyHomeCore\Widgets\Infobox_Widget' );
	}

	public function activation() {
		User_Settings::create_roles();
		Attributes_Settings::create_table();
		Estate_Settings::create_table();
	}

}