<?php

namespace MyHomeIDXBroker;


/**
 * Class IDX
 * @package MyHomeIDXBroker
 */
class IDX {

	/**
	 * @var Options
	 */
	public $options;

	private static $instance = false;

	/**
	 * @return IDX
	 */
	public static function get_instance() {
		if ( ! self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}


	public function init() {
		$this->options = new Options();

		add_action( 'admin_menu', array( $this, 'add_menu' ), 99 );
		add_action( 'init', array( $this, 'load_text_domain' ) );

		add_action( 'admin_post_myhome_idx_broker_import_agents', array( $this, 'import_agents' ) );
		add_action( 'admin_post_myhome_idx_broker_import_fields', array( $this, 'import_fields' ) );
		add_action( 'admin_post_myhome_idx_broker_save_fields', array( $this, 'save_fields' ) );
		add_action( 'admin_post_myhome_idx_broker_save_options', array( $this, 'save_options' ) );
		add_action( 'admin_post_myhome_idx_broker_save_mls_ids', array( $this, 'save_mls_ids' ) );

		add_action( 'wp_ajax_myhome_idx_broker_import_init', array( $this, 'import_init' ) );
		add_action( 'wp_ajax_myhome_idx_broker_import_job', array( $this, 'import_job' ) );

		if ( is_admin() ) {
			add_action( 'admin_enqueue_scripts', array( $this, 'scripts' ) );
		}

		$this->register_fields();
	}

	public function scripts() {
		if ( isset( $_GET['page'] ) && strpos( $_GET['page'], 'myhome_idx_broker_properties' ) !== false ) {
			wp_enqueue_script( 'myhome-idx-broker', plugins_url( MY_HOME_IDX_PATH . '/assets/js/build.js' ), array(), false, true );
		}

		if ( isset( $_GET['page'] ) && strpos( $_GET['page'], 'myhome_idx_broker' ) !== false ) {
			wp_enqueue_style( 'myhome-idx-broker', plugins_url( MY_HOME_IDX_PATH . '/assets/css/style.css' ) );
		}
	}

	public function load_text_domain() {
		load_plugin_textdomain( 'myhome-idx-broker', false, MY_HOME_IDX_PATH . '/languages' );
	}

	public function add_menu() {
		add_menu_page(
			esc_html__( 'MyHome IDX Broker', 'myhome-idx-broker' ),
			esc_html__( 'MyHome IDX Broker', 'myhome-idx-broker' ),
			'administrator',
			'myhome_idx_broker',
			array( $this, 'admin_page' ),
			'',
			3
		);
		$pages = array(
			array(
				'title' => esc_html__( 'MLS ID\'s', 'myhome-idx-broker' ),
				'slug'  => 'mls'
			),
			array(
				'title' => esc_html__( 'Agents', 'myhome-idx-broker' ),
				'slug'  => 'agents'
			),
			array(
				'title' => esc_html__( 'Property Fields', 'myhome-idx-broker' ),
				'slug'  => 'fields'
			),
			array(
				'title' => esc_html__( 'Synchronize Properties', 'myhome-idx-broker' ),
				'slug'  => 'properties'
			)
		);

		foreach ( $pages as $page ) {
			add_submenu_page(
				'myhome_idx_broker',
				$page['title'],
				$page['title'],
				'administrator',
				'myhome_idx_broker_' . $page['slug'],
				array( $this, $page['slug'] . '_page' )
			);
		}
	}

	public function admin_page() {
		require MY_HOME_IDX_VIEWS . 'admin-page.php';
	}

	public function properties_page() {
		require MY_HOME_IDX_VIEWS . 'properties-page.php';
	}

	public function agents_page() {
		require MY_HOME_IDX_VIEWS . 'agents-page.php';
	}

	public function fields_page() {
		require MY_HOME_IDX_VIEWS . 'fields-page.php';
	}

	public function mls_page() {
		require MY_HOME_IDX_VIEWS . 'mls-page.php';
	}

	public function import_agents() {
		$agents = new Agents();
		$agents->import();

		wp_redirect( admin_url( 'admin.php?page=myhome_idx_broker_agents' ) );
		exit;
	}

	public function import_init() {
		$importer = new Importer();
		$importer->init();
		wp_die();
	}

	public function import_job() {
		$importer = new Importer();
		$importer->job();
		wp_die();
	}

	public function import_fields() {
		$fields = new Fields();
		$fields->import();

		wp_redirect( admin_url( 'admin.php?page=myhome_idx_broker_fields' ) );
		exit;
	}

	public function save_mls_ids() {
		$this->check_if_allowed( 'myhome_idx_broker_update_mls' );

		if ( ! isset( $_POST['mls_ids'] ) ) {
			return;
		}

		MLS::save();
		wp_redirect( admin_url( 'admin.php?page=myhome_idx_broker_mls' ) );
		exit;
	}

	public function save_fields() {
		$fields = new Fields();
		$fields->save();

		wp_redirect( admin_url( 'admin.php?page=myhome_idx_broker_fields' ) );
		exit;
	}

	public function save_options() {
		$this->check_if_allowed( 'myhome_idx_broker_update_options' );

		if ( isset( $_POST['options'] ) && ! empty( $_POST['options'] ) ) {
			My_Home_IDX_Broker()->options->save( $_POST['options'] );
		}

		wp_redirect( admin_url( 'admin.php?page=myhome_idx_broker' ) );
		exit;
	}

	private function check_if_allowed( $action ) {
		check_admin_referer( $action, 'check_sec' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You don\'t have right permissions to manage options.', 'myhome-idx-broker' ) );
		}
	}

	public function register_fields() {
		if ( ! function_exists( 'acf_add_local_field_group' ) ) {
			return;
		}

		/**
		 * Agent fields
		 */
		acf_add_local_field_group( array(
			'key'        => 'myhome_idx_broker_user_fields',
			'title'      => esc_html__( 'MyHome IDX Broker', 'myhome-idx-broker' ),
			'fields'     => array(
				array(
					'key'   => 'myhome_idx_broker_user_id',
					'label' => esc_html__( 'IDX Agent ID', 'myhome-idx-broker' ),
					'name'  => 'idx_broker_user_id',
					'type'  => 'text'
				),
				array(
					'key'   => 'myhome_idx_broker_user_listing_id',
					'label' => esc_html__( 'IDX Agent Listing ID', 'myhome-idx-broker' ),
					'name'  => 'idx_broker_user_listing_id',
					'type'  => 'text'
				)
			),
			'menu_order' => 11,
			'location'   => array(
				array(
					array(
						'param'    => 'user_role',
						'operator' => '==',
						'value'    => 'all',
					),
				),
			),
		) );

		/**
		 * Property fields
		 */
		acf_add_local_field_group( array(
			'key'        => 'myhome_idx_broker_property_fields',
			'title'      => esc_html__( 'MyHome IDX Broker', 'myhome-idx-broker' ),
			'fields'     => array(
				array(
					'key'   => 'myhome_idx_broker_property_id',
					'label' => esc_html__( 'Property IDX ID', 'myhome-idx-broker' ),
					'name'  => 'idx_broker_property_id',
					'type'  => 'text'
				)
			),
			'menu_order' => 11,
			'location'   => array(
				array(
					array(
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => 'estate',
					),
				),
			),
		) );
	}

}