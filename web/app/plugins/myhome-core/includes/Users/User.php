<?php

namespace MyHomeCore\Users;


use MyHomeCore\Attributes\Attribute_Factory;
use MyHomeCore\Common\Image;
use MyHomeCore\Common\Social_Icon;
use MyHomeCore\Components\Listing\Listing;
use MyHomeCore\Estates\Estate_Factory;

class User {

	/**
	 * @var \WP_User
	 */
	protected $user;

	/**
	 * @var array
	 */
	protected $social_icons = array(
		'facebook'  => 'fa-facebook',
		'twitter'   => 'fa-twitter',
		'instagram' => 'fa-instagram',
		'linkedin'  => 'fa-linkedin',
	);

	/**
	 * User constructor.
	 *
	 * @param \WP_User $user
	 */
	public function __construct( \WP_User $user ) {
		$this->user = $user;
	}

	/**
	 * @param \WP_User|int $user
	 *
	 * @return User
	 * @throws \ErrorException
	 */
	public static function get_instance( $user ) {
		if ( is_int( $user ) ) {
			$user = get_user_by( 'id', $user );
		} elseif ( is_string( $user ) ) {
			$user = get_user_by( 'email', $user );
		}

		if ( $user instanceof \WP_User ) {
			if ( in_array( 'agent', $user->roles ) ) {
				return new Agent( $user );
			}

			return new User( $user );
		}

		throw new \ErrorException( die( 'Error during user creation.' ) );
	}

	/**
	 * @return int
	 */
	public function get_ID() {
		return $this->user->ID;
	}

	/**
	 * @return string
	 */
	public function get_name() {
		return $this->user->display_name;
	}

	/**
	 * @return string
	 */
	public function get_login() {
		return $this->user->user_login;
	}

	/**
	 * @return bool
	 */
	public function has_phone() {
		$phone = $this->get_phone();

		return ! empty( $phone );
	}

	/**
	 * @return string
	 */
	public function get_phone() {
		return get_field( 'agent_phone', 'user_' . $this->user->ID );
	}

	/**
	 * @return string
	 */
	public function get_phone_href() {
		return str_replace( array( ' ', '-', '(', ')' ), '', $this->get_phone() );
	}

	/**
	 * @return string
	 */
	public function has_email() {
		return ! empty( $this->user->user_email );
	}

	/**
	 * @return string
	 */
	public function get_email() {
		return $this->user->user_email;
	}

	/**
	 * @return bool
	 */
	public function has_social_icons() {
		foreach ( $this->social_icons as $icon_key => $icon ) {
			$i = get_field( 'agent_' . $icon_key, 'user_' . $this->user->ID );
			if ( ! empty( $i ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * @return Social_Icon[]
	 */
	public function get_social_icons() {
		$icons = array();
		foreach ( $this->social_icons as $icon_key => $icon ) {
			$i = get_field( 'agent_' . $icon_key, 'user_' . $this->user->ID );
			if ( ! empty( $i ) ) {
				$icons[] = new Social_Icon( $icon, $i );
			}
		}

		return $icons;
	}

	/**
	 * @return bool
	 */
	public function has_image() {
		$image = $this->get_image();

		return ! empty( $image );
	}

	/**
	 * @return array
	 */
	public function get_image() {
		return get_field( 'agent_image', 'user_' . $this->user->ID );
	}

	/**
	 * @return string
	 */
	public function get_image_url() {
		$image = $this->get_image();

		return empty( $image['url'] ) ? '' : $image['url'];
	}

	/**
	 * @return int
	 */
	public function get_image_id() {
		$image = $this->get_image();


		return empty( $image['id'] ) ? 0 : intval( $image['id'] );
	}

	/**
	 * @param string $thumbnail
	 * @param string $class
	 */
	public function image( $thumbnail = 'square', $class = '' ) {
		$image = $this->get_image();
		if ( ! empty( $image['id'] ) ) {
			Image::the_image( $image['id'], $thumbnail, $this->get_name(), $class );
		}
	}

	/**
	 * @return string
	 */
	public function get_link() {
		return get_author_posts_url( $this->user->ID );
	}

	/**
	 * @return bool
	 */
	public function has_description() {
		$description = $this->get_description();

		return ! empty( $description );
	}

	/**
	 * @return string
	 */
	public function get_description() {
		return apply_filters(
			'wpml_translate_single_string',
			get_the_author_meta( 'description', $this->user->ID ),
			'Agents',
			'agent-description-' . $this->get_ID()
		);
	}

	/**
	 * @return string
	 */
	public function get_short_description() {
		return wp_trim_words(
			$this->get_description(),
			35,
			'...'
		);
	}

	/**
	 * @param string $status
	 *
	 * @return \MyHomeCore\Estates\Estates
	 */
	public function get_estates( $status = 'publish' ) {
		return Estate_Factory::get_from_user( $this->get_ID(), array( $status ) );
	}

	/**
	 * @return array
	 */
	public function get_data() {
		$social_icons = array();

		foreach ( $this->social_icons as $icon_key => $icon_class ) {
			$social_icons[ $icon_key ] = array(
				'key'   => $icon_key,
				'class' => $icon_class,
				'value' => get_field( 'agent_' . $icon_key, 'user_' . $this->user->ID )
			);
		}

		return array(
			'id'      => $this->get_ID(),
			'login'   => $this->get_login(),
			'name'    => $this->get_name(),
			'link'    => $this->get_link(),
			'email'   => $this->get_email(),
			'phone'   => $this->get_phone(),
			'fields'  => $this->get_fields()->get_data(),
			'image'   => array(
				'url' => $this->get_image_url(),
				'id'  => $this->get_image_id()
			),
			'social'  => $social_icons,
			'estates' => $this->get_estates( 'any' )->get_data()
		);
	}

	/**
	 * @return Fields\Fields
	 */
	public function get_fields() {
		return new Fields\Fields( $this->get_ID() );
	}

	/**
	 * @return string
	 */
	public function get_hash() {
		return $this->user->data->user_pass;
	}

	/**
	 * @param int|null $user_id
	 *
	 * @return User
	 */
	public static function get_user_by_id( $user_id = null ) {
		if ( is_null( $user_id ) ) {
			$author_name = get_query_var( 'author_name' );
			$user        = $author_name ? get_user_by( 'slug', $author_name ) : get_userdata( get_query_var( 'author' ) );
		} else {
			$user = get_user_by( 'ID', $user_id );
		}

		return self::get_instance( $user );
	}

	public function listing() {
		$options                        = get_option( 'myhome_redux' );
		$show_advanced                  = is_null( $options['mh-listing-show_advanced'] ) ? true : intval( $options['mh-listing-show_advanced'] );
		$show_clear                     = is_null( $options['mh-listing-show_clear'] ) ? true : intval( $options['mh-listing-show_clear'] );
		$show_sort_by                   = is_null( $options['mh-listing-show_sort_by'] ) ? true : intval( $options['mh-listing-show_sort_by'] );
		$show_view_types                = is_null( $options['mh-listing-show_view_types'] ) ? true : intval( $options['mh-listing-show_view_types'] );
		$show_gallery                   = is_null( $options['mh-listing-show_gallery'] ) ? true : intval( $options['mh-listing-show_gallery'] );
		$advanced_number                = is_null( $options['mh-listing-search_form_advanced_number'] ) ? 3 : intval( $options['mh-listing-search_form_advanced_number'] );
		$show_results_number            = is_null( $options['mh-listing_show-results-number'] ) ? true : ! empty( $options['mh-listing_show-results-number'] );
		$listing_type                   = is_null( $options['mh-listing-type'] ) ? 'load_more' : $options['mh-listing-type'];
		$show_sort_by_newest            = ! isset( $options['mh-listing-show_sort_by_newest'] ) ? true : ! empty( $options['mh-listing-show_sort_by_newest'] );
		$show_sort_by_popular           = ! isset( $options['mh-listing-show_sort_by_popular'] ) ? true : ! empty( $options['mh-listing-show_sort_by_popular'] );
		$show_sort_by_price_high_to_low = ! isset( $options['mh-listing-show_sort_by_price_high_to_low'] ) ? true : ! empty( $options['mh-listing-show_sort_by_price_high_to_low'] );
		$show_sort_by_price_low_to_high = ! isset( $options['mh-listing-show_sort_by_price_low_to_high'] ) ? true : ! empty( $options['mh-listing-show_sort_by_price_low_to_high'] );
		$show_sort_by_alphabetically    = ! isset( $options['mh-listing-show_sort_by_alphabetically'] ) ? false : ! empty( $options['mh-listing-show_sort_by_alphabetically'] );
		$search_form                    = ! isset( $options['mh-listing-search_form'] ) ? 'default' : $options['mh-listing-search_form'];

		$atts = array(
			'lazy_loading'                   => $options['mh-listing-lazy_loading'] ? 'true' : 'false',
			'lazy_loading_limit'             => intval( $options['mh-listing-load_more_button_number'] ),
			'load_more_button'               => $options['mh-listing-load_more_button_label'],
			'listing_default_view'           => $options['mh-listing-default_view'],
			'estates_per_page'               => $options['mh-listing-estates_limit'],
			'search_form_position'           => $options['mh-listing-search_form_position'],
			'label'                          => $options['mh-listing-label'],
			'search_form_advanced_number'    => $advanced_number,
			'show_advanced'                  => $show_advanced ? 'true' : 'false',
			'show_clear'                     => $show_clear ? 'true' : 'false',
			'show_sort_by'                   => $show_sort_by ? 'true' : 'false',
			'show_view_types'                => $show_view_types ? 'true' : 'false',
			'agent_id'                       => $this->user->ID,
			'show_gallery'                   => $show_gallery ? 'true' : 'false',
			'show_results_number'            => $show_results_number ? 'true' : 'false',
			'listing_type'                   => $listing_type,
			'show_sort_by_newest'            => $show_sort_by_newest ? 'true' : 'false',
			'show_sort_by_popular'           => $show_sort_by_popular ? 'true' : 'false',
			'show_sort_by_price_high_to_low' => $show_sort_by_price_high_to_low ? 'true' : 'false',
			'show_sort_by_price_low_to_high' => $show_sort_by_price_low_to_high ? 'true' : 'false',
			'show_sort_by_alphabetically'    => $show_sort_by_alphabetically ? 'true' : 'false',
			'search_form'                    => $search_form
		);

		foreach ( Attribute_Factory::get_search() as $attribute ) {
			if ( isset( \MyHomeCore\My_Home_Core()->settings->props[ 'mh-listing-' . $attribute->get_slug() . '_show' ] ) ) {
				$atts[ $attribute->get_slug() . '_show' ] = ! empty( \MyHomeCore\My_Home_Core()->settings->props[ 'mh-listing-' . $attribute->get_slug() . '_show' ] );
			} else {
				$atts[ $attribute->get_slug() . '_show' ] = true;
			}
		}

		$listing = new Listing( $atts );
		?>
		<div class="mh-listing--full-width mh-listing--horizontal-boxed">
			<?php $listing->display(); ?>
		</div>
		<?php
	}

	/**
	 * @return bool
	 */
	public function is_confirmed() {
		$is_confirmed = get_user_meta( $this->get_ID(), 'myhome_agent_confirmed', true );

		return ! empty( $is_confirmed );
	}

}