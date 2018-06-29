<?php

namespace MyHomeCore\Users;


/**
 * Class Agents_Manager
 * @package MyHomeCore\Users
 */
class Agents_Manager {

	/**
	 * Agents_Manager constructor.
	 */
	public function __construct() {
		add_action( 'myhome_agent_created', array( $this, 'send_register_confirmation_email' ) );
		add_action( 'myhome_agent_welcome', array( $this, 'send_welcome_message_email' ) );
		add_action( 'wp_ajax_nopriv_mh_agent_activate', array( $this, 'activate' ) );
		add_action( 'wp_ajax_nopriv_mh_agent_send_link', array( $this, 'send_link' ) );
		add_action( 'wp_mail_failed', array( $this, 'action_wp_mail_failed' ), 10, 1 );
	}

	public function action_wp_mail_failed( $wp_error ) {
		print_r( $wp_error );
	}

	// add the action
	public function activate() {
		if ( empty( $_GET['uid'] ) || empty( $_GET['hash'] ) ) {
			wp_die();
		}

		$user_id = intval( $_GET['uid'] );
		$hash    = get_user_meta( $user_id, 'myhome_user_confirm', true );
		$expired = get_user_meta( $user_id, 'myhome_user_confirm_expire', true );

		if ( empty( $hash ) || $hash != $_GET['hash'] || empty( $expired ) || time() > strtotime( $expired ) ) {
			$notice = '#notice-expired';
		} else {
			$notice = '#notice-confirmed';

			update_user_meta( $user_id, 'myhome_agent_confirmed', true );
			delete_user_meta( $user_id, 'myhome_user_confirm' );

			do_action( 'myhome_agent_welcome', $user_id );
		}

		$panel_url = '';
		if ( ! empty( \MyHomeCore\My_Home_Core()->settings->props['mh-agent-panel_page'] ) ) {
			$page_id   = \MyHomeCore\My_Home_Core()->settings->props['mh-agent-panel_page'];
			$page      = get_post( $page_id );
			$panel_url = get_page_link( $page );
		} else if ( ! empty( \MyHomeCore\My_Home_Core()->settings->props['mh-agent-panel_link'] ) ) {
			$panel_url = \MyHomeCore\My_Home_Core()->settings->props['mh-agent-panel_link'];
		}
		$panel_url .= $notice;

		?>
		<script>
			redirect();

			function redirect() {
				window.location = "<?php echo esc_url( $panel_url ); ?>";
			}
		</script>
		<?php
		wp_die();
	}

	public function send_link() {
		if ( ! isset( $_GET['uid'] ) ) {
			return;
		}

		$user_id = intval( $_GET['uid'] );
		$user    = User::get_instance( $user_id );

		if ( $user->is_confirmed() ) {
			return;
		}

		$this->send_register_confirmation_email( $user_id );

		echo json_encode( array( 'success' => true ) );
		wp_die();
	}

	public function send_register_confirmation_email( $user_id ) {
		if ( empty( \MyHomeCore\My_Home_Core()->settings->props['mh-agent-email_confirmation'] ) ) {
			do_action( 'myhome_agent_welcome', $user_id );

			return;
		}

		$user = User::get_user_by_id( $user_id );

		if ( ! empty( \MyHomeCore\My_Home_Core()->settings->props['mh-agents-msg_confirm-title'] ) ) {
			$subject = apply_filters(
				'wpml_translate_single_string',
				\MyHomeCore\My_Home_Core()->settings->props['mh-agents-msg_confirm-title'],
				'myhome-core',
				'Confirmation mail - title'
			);
		} else {
			$subject = esc_html__( 'Welcome to MyHome', 'myhome' );
		}

		if ( ! empty( \MyHomeCore\My_Home_Core()->settings->props['mh-agents-msg_confirm-msg'] ) ) {
			$message = apply_filters(
				'wpml_translate_single_string',
				\MyHomeCore\My_Home_Core()->settings->props['mh-agents-msg_confirm-msg'],
				'myhome-core',
				'Confirmation mail - text'
			);
		} else {
			$message = esc_html__( 'Last step - click link', 'myhome' );
		}

		$hash = md5( $user_id . '-' . time() );
		update_user_meta( $user_id, 'myhome_user_confirm', $hash );

		if ( ! empty( \MyHomeCore\My_Home_Core()->settings->props['mh-agent_email_confirmation-expire'] ) ) {
			$hours = \MyHomeCore\My_Home_Core()->settings->props['mh-agent_email_confirmation-expire'];
		} else {
			$hours = 48;
		}

		$expire = date( "Y-m-d H:i:s", strtotime( '+' . $hours . ' hours' ) );
		update_user_meta( $user_id, 'myhome_user_confirm_expire', $expire );

		$subject = str_replace(
			array( '{{username}}' ),
			array( $user->get_name() ),
			$subject
		);

		$link    = admin_url( 'admin-ajax.php?action=mh_agent_activate&uid=' . $user_id . '&hash=' . $hash );
		$message = str_replace(
			array( '{{username}}', '{{confirmation_link}}' ),
			array( $user->get_name(), $link ),
			$message
		);

		$headers = array( 'Content-Type: text/html; charset=UTF-8' );
		wp_mail( $user->get_email(), $subject, $message, $headers );
	}

	public function send_welcome_message_email( $user_id ) {
		if ( empty( \MyHomeCore\My_Home_Core()->settings->props['mh-agent-email_welcome-message'] ) ) {
			return;
		}

		$user = User::get_instance( $user_id );

		if ( ! empty( \MyHomeCore\My_Home_Core()->settings->props['mh-agents-msg_welcome-title'] ) ) {
			$subject = apply_filters(
				'wpml_translate_single_string',
				\MyHomeCore\My_Home_Core()->settings->props['mh-agents-msg_welcome-title'],
				'myhome-core',
				'Welcome mail - title'
			);
		} else {
			$subject = esc_html__( 'Welcome mail - title', 'myhome' );
		}

		if ( ! empty( \MyHomeCore\My_Home_Core()->settings->props['mh-agents-msg_welcome-msg'] ) ) {
			$message = apply_filters(
				'wpml_translate_single_string',
				\MyHomeCore\My_Home_Core()->settings->props['mh-agents-msg_welcome-msg'],
				'myhome-core',
				'Welcome mail - message'
			);
		} else {
			$message = esc_html__( 'Welcome mail - message', 'myhome' );
		}
		$subject = str_replace(
			array( '{{username}}' ),
			array( $user->get_name() ),
			$subject
		);

		$message = str_replace(
			array( '{{username}}' ),
			array( $user->get_name() ),
			$message
		);

		$headers = array( 'Content-Type: text/html; charset=UTF-8' );

		wp_mail( $user->get_email(), $subject, $message, $headers );
	}

}