<?php

/*
 * My_Home_Scripts
 *
 * Enqueue js and css files
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Access denied.' );
}

if ( ! class_exists( 'My_Home_Scripts' ) ) :

	class My_Home_Scripts {

		public function __construct() {
			// load scripts
			add_action( 'wp_enqueue_scripts', array( $this, 'load_scripts' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'load_admin_scripts' ) );
		}

		/*
		 * load_admin_scripts
		 *
		 * Load js and css files for admin user
		 */
		public function load_admin_scripts() {
			$assets_js = get_template_directory_uri() . '/assets/js/';
			wp_enqueue_style( 'myhome-admin', get_template_directory_uri() . '/assets/css/mh-admin.css', array(), My_Home_Theme()->version );
			wp_enqueue_style( 'myhome-animate', get_template_directory_uri() . '/assets/css/animate.min.css', array(), My_Home_Theme()->version );
			wp_enqueue_style( 'myhome-backend', get_template_directory_uri() . '/assets/css/backend.css', array(), My_Home_Theme()->version );
			wp_enqueue_style( 'myhome-font-awesome', get_template_directory_uri() . '/assets/css/font-awesome.min.css', array(), My_Home_Theme()->version );
			wp_enqueue_script( 'myhome-admin', $assets_js . 'admin.js', array(
				'selectize',
				'jquery',
				'myhome-icon-picker'
			), My_Home_Theme()->version, true );
			wp_enqueue_script( 'material-admin', $assets_js . 'material.min.js', array(), My_Home_Theme()->version, true );
			wp_enqueue_script( 'lazy-sizes', get_template_directory_uri() . '/assets/js/lazysizes.min.js', array(), My_Home_Theme()->version, true );
			wp_enqueue_script( 'selectize', get_template_directory_uri() . '/assets/js/selectize.min.js', array( 'jquery' ), My_Home_Theme()->version, true );
			wp_enqueue_style( 'selectize', get_template_directory_uri() . '/assets/css/selectize.css', array(), My_Home_Theme()->version );
			wp_enqueue_style( 'myhome-icon-picker', get_template_directory_uri() . '/assets/css/icon-picker.min.css', array(), My_Home_Theme()->version );
			wp_register_script( 'myhome-icon-picker', get_template_directory_uri() . '/assets/js/icon-picker.min.js', array(), My_Home_Theme()->version );

			if ( ( strpos( $_SERVER['REQUEST_URI'], 'myhome_attributes' ) !== false ) && is_user_logged_in() ) {
				wp_enqueue_style( 'sweetalert2', get_template_directory_uri() . '/assets/css/sweetalert2.min.css', array(), My_Home_Theme()->version );

				$google_api_key = My_Home_Theme()->settings->get( 'google-api-key' );
				wp_register_script(
					'google-maps-api-admin',
					'//maps.googleapis.com/maps/api/js?libraries=places&key=' . $google_api_key,
					array( 'jquery' ),
					false,
					false
				);
				wp_enqueue_style( 'material-css', get_template_directory_uri() . '/assets/css/material.min.css', array(), My_Home_Theme()->version );
				wp_enqueue_style( 'material-icons', get_template_directory_uri() . '/assets/css/material-icon.css', array(), My_Home_Theme()->version );
				wp_enqueue_script(
					'myhome-backend', $assets_js . 'backend.js', array(
					'material-admin',
					'google-maps-api-admin',
					'myhome-icon-picker'
				), My_Home_Theme()->version, true
				);

				wp_localize_script(
					'jquery', 'MyHomePanelSettings', array(
						'translations' => \MyHomeCore\Common\Translations::get_backend(),
						'nonce'        => wp_create_nonce( 'myhome_backend_panel_' . get_current_user_id() ),
						'requestUrl'   => admin_url( 'admin-post.php' ),
						'primaryColor' => My_Home_Theme()->settings->get( 'color-primary', 'color' ),
					)
				);
			}
		}

		/*
		 * load_scripts
		 *
		 * Load all required js and css files
		 */
		public function load_scripts() {
			/*
			 * CSS Files
			 */
			wp_enqueue_style( 'normalize', get_template_directory_uri() . '/assets/css/normalize.css', array(), My_Home_Theme()->version );
			wp_enqueue_style( 'myhome-frontend', get_template_directory_uri() . '/assets/css/frontend.css', array(), My_Home_Theme()->version );
			wp_enqueue_style( 'swiper', get_template_directory_uri() . '/assets/css/swiper.min.css', array(), My_Home_Theme()->version );
			wp_enqueue_script( 'selectize', get_template_directory_uri() . '/assets/js/selectize.min.js', array( 'jquery' ), My_Home_Theme()->version, true );
			wp_enqueue_style( 'selectize', get_template_directory_uri() . '/assets/css/selectize.css', array(), My_Home_Theme()->version );
			wp_enqueue_style(
				'font-awesome', get_template_directory_uri() . '/assets/css/font-awesome.min.css', array(), My_Home_Theme()->version
			);

			$rtl_support = My_Home_Theme()->settings->get( 'typography-rtl' );
			if ( ( ! empty( $rtl_support ) && $rtl_support ) || is_rtl() ) {
				wp_enqueue_style( 'myhome-style', get_template_directory_uri() . '/style-rtl.css', array(), My_Home_Theme()->version );
				wp_enqueue_style( 'myhome-style-rtl-fix', get_template_directory_uri() . '/assets/css/rtl/fix.css', array( 'myhome-style' ), My_Home_Theme()->version );
			} else {
				wp_enqueue_style( 'myhome-style', get_stylesheet_uri(), array(), My_Home_Theme()->version );
			}
			/*
			 * JS Files
			 */


			wp_enqueue_script( 'animejs', get_template_directory_uri() . '/assets/js/anime.min.js', array(), My_Home_Theme()->version, true );
			wp_enqueue_script( 'typeahead', get_template_directory_uri() . '/assets/js/typeahead.min.js', array( 'jquery' ), My_Home_Theme()->version, true );
			wp_enqueue_script( 'swiper', get_template_directory_uri() . '/assets/js/swiper.min.js', array( 'jquery' ), My_Home_Theme()->version, true );
			wp_enqueue_script(
				'myhome-frontend',
				get_template_directory_uri() . '/assets/js/frontend.js',
				array(
					'awesomplete',
					'jquery',
					'bootstrap',
					'bootstrap-select',
					'myhome-main',
					'myhome-carousel',
					'myhome-mdl',
					'swiper',
					'typeahead'
				),
				My_Home_Theme()->version,
				true
			);
			wp_register_script(
				'bootstrap', get_template_directory_uri() . '/assets/js/bootstrap.min.js',
				array( 'jquery' ), false, true
			);
			wp_enqueue_script(
				'bootstrap-select', get_template_directory_uri() . '/assets/js/bootstrap-select.min.js',
				array( 'jquery', 'bootstrap' ), false, true
			);
			wp_enqueue_script(
				'myhome-mdl', get_template_directory_uri() . '/assets/js/material.min.js', array( 'jquery' ),
				My_Home_Theme()->version, true
			);
			wp_enqueue_script( 'lazy-sizes', get_template_directory_uri() . '/assets/js/lazysizes.min.js', array(), false, true );
			wp_register_script(
				'magnific-popup', get_template_directory_uri() . '/assets/js/jquery.magnific-popup.min.js', array( 'jquery' ),
				My_Home_Theme()->version, true
			);
			wp_register_script(
				'owl-carousel', get_template_directory_uri() . '/assets/js/owl.carousel.js',
				array( 'jquery' ), My_Home_Theme()->version, true
			);
			wp_register_script(
				'myhome-carousel', get_template_directory_uri() . '/assets/js/carousel.js', array(
				'jquery',
				'owl-carousel'
			),
				My_Home_Theme()->version, true
			);

			if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
				wp_enqueue_script( 'comment-reply' );
			}

			wp_register_script(
				'jquery-sticky', get_template_directory_uri() . '/assets/js/jquery.sticky.js',
				array( 'jquery' ), My_Home_Theme()->version, true
			);

			if ( is_singular( 'estate' ) ) {
				wp_enqueue_script(
					'myhome-main', get_template_directory_uri() . '/assets/js/main.js',
					array(
						'jquery',
						'jquery-ui-accordion',
						'magnific-popup',
						'animejs',
						'selectize'
					), My_Home_Theme()->version, true
				);
				wp_enqueue_script(
					'myhome-single-property', get_template_directory_uri() . '/assets/js/single-property.js',
					array( 'jquery', 'jquery-sticky', 'selectize' ), My_Home_Theme()->version, true
				);

				$gallery_type = My_Home_Theme()->settings->get( 'estate_slider' );
				if ( $gallery_type == 'single-estate-gallery' ) {
					wp_enqueue_script(
						'myhome-estate-gallery', get_template_directory_uri() . '/assets/js/sliders/gallery.js',
						array( 'jquery', 'wpb_composer_front_js', 'magnific-popup' ), My_Home_Theme()->version, true
					);
				} elseif ( $gallery_type == 'single-estate-slider' ) {
					wp_enqueue_script(
						'myhome-estate-slider', get_template_directory_uri() . '/assets/js/sliders/slider.js',
						array( 'jquery', 'wpb_composer_front_js' ), My_Home_Theme()->version, true
					);
				} elseif ( $gallery_type == 'single-estate-gallery-auto-height' ) {
					wp_enqueue_script(
						'myhome-estate-slider', get_template_directory_uri() . '/assets/js/sliders/gallery-auto-height.js',
						array( 'jquery', 'wpb_composer_front_js', 'magnific-popup' ), My_Home_Theme()->version, true
					);
				}
			} else {
				wp_enqueue_script(
					'myhome-main', get_template_directory_uri() . '/assets/js/main.js', array(
					'jquery',
					'magnific-popup',
					'animejs'
				),
					My_Home_Theme()->version, true
				);
			}

			wp_register_script(
				'awesomplete',
				get_template_directory_uri() . '/assets/js/awesomplete.min.js',
				array( 'jquery' ),
				My_Home_Theme()->version,
				true
			);

			if ( is_author() || is_tax() ) {
				wp_enqueue_script( 'myhome-frontend' );
			}

			$google_api_key = My_Home_Theme()->settings->get( 'google-api-key' );
			if ( ! empty( $google_api_key ) ) {
				wp_enqueue_script(
					'google-maps-api',
					'//maps.googleapis.com/maps/api/js?key=' . $google_api_key . '&libraries=places',
					array( 'jquery' ),
					null,
					false
				);
				wp_register_script(
					'google-maps-markerclusterer',
					get_template_directory_uri() . '/assets/js/markerclusterer.js',
					array( 'google-maps-api' ),
					My_Home_Theme()->version,
					true
				);
				wp_register_script(
					'infobox', get_template_directory_uri() . '/assets/js/infobox.min.js',
					array( 'jquery', 'google-maps-api' ), My_Home_Theme()->version, true
				);
				wp_register_script(
					'richmarker', get_template_directory_uri() . '/assets/js/richmarker.min.js',
					array( 'jquery', 'google-maps-api' ), My_Home_Theme()->version, true
				);

				if ( is_singular( 'estate' ) ) {
					wp_enqueue_script( 'google-maps-api' );
					wp_enqueue_script( 'google-maps-markerclusterer' );
					wp_enqueue_script( 'richmarker' );
					wp_enqueue_script( 'infobox' );
				}

				wp_register_script(
					'myhome-panel',
					get_template_directory_uri() . '/assets/js/panel.js', array(
					'jquery',
					'google-maps-api',
					'bootstrap-select',
					'recaptcha'
				),
					My_Home_Theme()->version, true
				);
			} else {
				wp_register_script(
					'myhome-panel',
					get_template_directory_uri() . '/assets/js/panel.js', array(
					'jquery',
					'bootstrap-select',
					'recaptcha'
				),
					My_Home_Theme()->version, true
				);
			}

			$options = get_option( 'myhome_redux' );

			if ( is_page_template( 'page_agents.php' ) ) {
				wp_enqueue_script( 'recaptcha', 'https://www.google.com/recaptcha/api.js', array(), true );
				wp_enqueue_style( 'dropzonejs', get_template_directory_uri() . '/assets/css/dropzone.css' );
				wp_enqueue_script( 'dropzonejs', get_template_directory_uri() . '/assets/js/dropzone.js' );
				wp_add_inline_script( 'dropzonejs', 'Dropzone.autoDiscover = false;' );
				wp_enqueue_style( 'sweetalert2', get_template_directory_uri() . '/assets/css/sweetalert2.min.css', array(), My_Home_Theme()->version );
				wp_enqueue_script( 'myhome-panel' );


				$active_tab = My_Home_Theme()->settings->get( 'agent-panel_active_tab' );
				if ( empty( $active_tab ) ) {
					$active_tab = 'login';
				}

				$register     = My_Home_Theme()->settings->get( 'agent-registration' );
				$myhome_panel = array(
					'translations'     => \MyHomeCore\Common\Translations::get_panel(),
					'nonce'            => wp_create_nonce( 'myhome_user_panel' . ( is_user_logged_in() ? '_' . get_current_user_id() : '' ) ),
					'requestUrl'       => admin_url( 'admin-ajax.php' ),
					'fields'           => \MyHomeCore\Panel\Panel_Fields::get_current(),
					'payment'          => ! empty( $options['mh-payment'] ),
					'registration'     => ! empty( $register ),
					'languages'        => ! empty( \MyHomeCore\My_Home_Core()->languages ) ? \MyHomeCore\My_Home_Core()->languages : array(),
					'current_language' => ! empty( \MyHomeCore\My_Home_Core()->current_language ) ? \MyHomeCore\My_Home_Core()->current_language : '',
					'current_url'      => get_page_link(),
					'captcha_enabled'  => ! empty( $options['mh-agent-captcha'] ),
					'captcha_site_key' => My_Home_Theme()->settings->get( 'agent_captcha_site-key' ),
					'active_tab'       => $active_tab
				);

				if ( ! empty( $options['mh-payment'] ) && ! empty( $options['mh-payment-stripe'] ) ) {
					wp_enqueue_script( 'stripe-checkout', 'https://checkout.stripe.com/checkout.js' );

					$myhome_panel['stripe'] = array(
						'name'        => get_bloginfo( 'name' ),
						'description' => esc_html__( '1 property listing', 'myhome' ),
						'key'         => $options['mh-payment-stripe-key'],
						'cost'        => empty( $options['mh-payment-stripe-cost'] ) ? '' : $options['mh-payment-stripe-cost'],
						'currency'    => $options['mh-payment-stripe-currency']
					);
				}

				if ( ! empty( $options['mh-payment'] ) && ! empty( $options['mh-payment-paypal'] ) ) {
					wp_enqueue_script( 'paypal-checkout', 'https://www.paypalobjects.com/api/checkout.js' );

					$myhome_panel['paypal'] = array(
						'name'     => get_bloginfo( 'name' ),
						'key'      => $options['mh-payment-paypal-public_key'],
						'env'      => ! empty( $options['mh-payment-paypal-sandbox'] ) ? 'sandbox' : 'production',
						'locale'   => $options['mh-payment-paypal-locale'],
						'cost'     => empty( $options['mh-payment-paypal-cost'] ) ? '' : $options['mh-payment-paypal-cost'],
						'currency' => $options['mh-payment-paypal-currency']
					);
				}

				wp_localize_script( 'myhome-panel', 'MyHomePanel', $myhome_panel );
			}

			// load default fonts if redux options are not installed
			if ( ! class_exists( 'ReduxFramework' ) ) {
				wp_enqueue_style( 'myhome-fonts', $this->fonts_url(), array(), null );
			}

			$map_style = My_Home_Theme()->settings->get( 'map-style' );
			if ( empty( $map_style ) || $map_style == 'gray' ) {
				$map_style = '[{featureType:"administrative",elementType:"labels.text.fill",stylers:[{color:"#444444"}]},{featureType:"landscape",elementType:"all",stylers:[{color:"#f2f2f2"}]},{featureType:"poi",elementType:"all",stylers:[{visibility:"off"}]},{featureType:"road",elementType:"all",stylers:[{saturation:-100},{lightness:45}]},{featureType:"road.highway",elementType:"all",stylers:[{visibility:"simplified"}]},{featureType:"road.arterial",elementType:"labels.icon",stylers:[{visibility:"off"}]},{featureType:"transit",elementType:"all",stylers:[{visibility:"off"}]},{featureType:"water",elementType:"all",stylers:[{color:"#d7e1f2"},{visibility:"on"}]}]';
			} else {
				$map_style = My_Home_Theme()->settings->get( 'map-style_custom' );
				ob_start();
				if ( ! empty( $map_style ) ) :
					?>
					window.MyHomeMapStyle = <?php echo wp_kses_post( $map_style ); ?>;
				<?php
				endif;
				$map_style_script = ob_get_clean();
				wp_add_inline_script( 'myhome-main', $map_style_script );
			}

			$map_type = My_Home_Theme()->settings->get( 'map-type' );
			if ( empty( $map_type ) ) {
				$map_type = 'roadmap';
			}

			$frontend_panel   = My_Home_Theme()->settings->get( 'agent-panel' );
			$is_register_open = My_Home_Theme()->settings->get( 'agent-registration' );
			$show_date        = My_Home_Theme()->settings->get( 'estate_show_date' );
			$panel_page       = My_Home_Theme()->settings->get( 'agent-panel_page' );

			if ( ! empty( $panel_page ) ) {
				$panel_link = get_permalink( $panel_page );
			} else {
				$panel_link = My_Home_Theme()->settings->get( 'agent-panel_link' );
			}


			$settings = array(
				'site'                  => site_url(),
				'compare'               => My_Home_Theme()->settings->get( 'compare' ),
				'api'                   => site_url() . '/wp-json/myhome/v1/estates',
				'panelUrl'              => $panel_link,
				'is_register_open'      => ! empty( $is_register_open ) && ! empty( $frontend_panel ),
				'requestUrl'            => admin_url( 'admin-ajax.php' ),
				'nonce'                 => wp_create_nonce( 'myhome_user_panel' . ( is_user_logged_in() ? '_' . get_current_user_id() : '' ) ),
				'mapStyle'              => $map_style,
				'mapType'               => $map_type,
				'contact_price_label'   => class_exists( '\MyHomeCore\Attributes\Price_Attribute_Options_Page' ) ? \MyHomeCore\Attributes\Price_Attribute_Options_Page::get_default_value() : '',
				'user_bar_label'        => apply_filters( 'wpml_translate_single_string', My_Home_Theme()->settings->get( 'agent_user-bar-text' ), 'myhome-core', 'User bar login / register text' ),
				'property_link_new_tab' => class_exists( '\MyHomeCore\Estates\Estate' ) && \MyHomeCore\Estates\Estate::is_new_tab(),
				'show_date'             => ! empty( $show_date ) ? 'true' : 'false'
			);

			if ( is_user_logged_in() && class_exists( '\MyHomeCore\Users\User' ) ) {
				$settings['user'] = \MyHomeCore\Users\User::get_user_by_id( get_current_user_id() )->get_data();
			}

			if ( class_exists( '\MyHomeCore\Common\Translations' ) ) {
				$settings['translations'] = \MyHomeCore\Common\Translations::get_frontend();
			}

			wp_localize_script( 'myhome-main', 'MyHome', $settings );

			/*
			 * Inline css
			 */
			$inline_css = '';

			$dropdown_width = My_Home_Theme()->settings->get( 'menu-drop-down-width' );

			if ( empty( $dropdown_width ) ) {
				$dropdown_width = 225;
			}

			ob_start();
			?>
			@media (min-width:1023px) {
			#mega_main_menu li.default_dropdown>.mega_dropdown {
			width:<?php echo esc_attr( $dropdown_width ); ?>px !important;
			}
			}
			<?php
			$inline_css .= ob_get_clean();

			$color_primary = My_Home_Theme()->settings->get( 'color-primary' );
			if ( ! empty( $color_primary['color'] ) ) {
				$color = $this->hex2rgb( $color_primary['color'] ) . ',0.05';
				ob_start();
				?>
				.mh-active-input-primary input[type=text]:focus,
				.mh-active-input-primary input[type=text]:active,
				.mh-active-input-primary input[type=search]:focus,
				.mh-active-input-primary input[type=search]:active,
				.mh-active-input-primary input[type=email]:focus,
				.mh-active-input-primary input[type=email]:active,
				.mh-active-input-primary input[type=password]:focus,
				.mh-active-input-primary input[type=password]:active,
				.mh-active-input-primary textarea:focus,
				.mh-active-input-primary textarea:active,
				.mh-active-input-primary .mh-active-input input,
				.mh-active-input-primary .mh-active-input input,
				.myhome-body.mh-active-input-primary .mh-active-input .bootstrap-select.btn-group > .btn {
				background: rgba(<?php echo esc_html( $color ); ?>)!important;
				}
				<?php
				$inline_css .= ob_get_clean();
			}

			$top_bar = My_Home_Theme()->settings->get( 'top-header-style' );
			ob_start();
			if ( $top_bar == 'big' ) {
				$logo_height     = My_Home_Theme()->settings->get( 'logo-top-bar_height' );
				$logo_margin_top = My_Home_Theme()->settings->get( 'logo-top-bar_margin_top' );

				if ( ! empty( $logo_height ) ) :
					?>
					@media (min-width: 1024px) {
					.mh-top-header-big__logo img {
					height: <?php echo esc_html( $logo_height ); ?>px!important;
					}
					}
				<?php
				endif;

				if ( ! empty( $logo_margin_top ) ) :
					?>
					@media (min-width: 1024px) {
					.mh-top-header-big__logo img {
					margin-top: <?php echo esc_html( $logo_margin_top ); ?>px;
					}
					}
				<?php
				endif;
			} else {
				$logo_height     = My_Home_Theme()->settings->get( 'logo-height' );
				$logo_margin_top = My_Home_Theme()->settings->get( 'logo-margin_top' );

				if ( ! empty( $logo_height ) ) :
					?>
					@media (min-width:1023px) {
					html body #mega_main_menu.mh-primary .nav_logo img {
					height: <?php echo esc_html( $logo_height ); ?>px!important;
					}
					}
				<?php
				endif;

				if ( ! empty( $logo_margin_top ) ) :
					?>
					@media (min-width:1023px) {
					html body #mega_main_menu.mh-primary .nav_logo img {
					margin-top: <?php echo esc_html( $logo_margin_top ); ?>px!important;
					}
					}
				<?php
				endif;
			}
			$inline_css .= ob_get_clean();

			wp_add_inline_style( 'myhome-style', $inline_css );
		}

		private function hex2rgb( $hex ) {
			$hex = str_replace( '#', '', $hex );

			if ( strlen( $hex ) == 3 ) {
				$r = hexdec( substr( $hex, 0, 1 ) . substr( $hex, 0, 1 ) );
				$g = hexdec( substr( $hex, 1, 1 ) . substr( $hex, 1, 1 ) );
				$b = hexdec( substr( $hex, 2, 1 ) . substr( $hex, 2, 1 ) );
			} else {
				$r = hexdec( substr( $hex, 0, 2 ) );
				$g = hexdec( substr( $hex, 2, 2 ) );
				$b = hexdec( substr( $hex, 4, 2 ) );
			}
			$rgb = array( $r, $g, $b );

			return implode( ',', $rgb );
		}

		public function fonts_url() {
			$fonts_url = '';
			$fonts     = array();
			$subsets   = 'latin,latin-ext';

			if ( 'off' !== esc_html_x( 'on', 'Lato font: on or off', 'myhome' ) ) {
				array_push( $fonts, 'Lato:400italic,300,400,700' );
			}

			if ( 'off' !== esc_html_x( 'on', 'Play font: on or off', 'myhome' ) ) {
				array_push( $fonts, 'Play:400,700' );
			}

			if ( $fonts ) {
				$fonts_url = add_query_arg(
					array(
						'family' => urlencode( implode( '|', $fonts ) ),
						'subset' => urlencode( $subsets ),
					), 'https://fonts.googleapis.com/css'
				);
			}

			return $fonts_url;
		}

	}

endif;
