<?php

namespace MyHomeCore\Estates;

use MyHomeCore\Attributes\Offer_Type_Attribute;
use MyHomeCore\Attributes\Price_Attribute;
use MyHomeCore\Estates\Filters\Estate_Filter;
use MyHomeCore\Estates\Filters\Estate_ID_Filter;
use MyHomeCore\Estates\Filters\Estate_Keyword_Filter;
use MyHomeCore\Estates\Filters\Estate_Offer_Type_Filter;
use MyHomeCore\Estates\Filters\Estate_Price_Filter;
use MyHomeCore\Estates\Prices\Currencies;
use MyHomeCore\Terms\Term_Factory;

/**
 * Class Estate_Factory
 * @package MyHomeCore\Estates
 */
class Estate_Factory {

	const NO_LIMIT = - 1;
	const ORDER_BY_TITLE_ASC = 'titleASC';
	const ORDER_BY_TITLE_DESC = 'titleDESC';
	const ORDER_BY_NEWEST = 'newest';
	const ORDER_BY_PRICE_HIGH_TO_LOW = 'priceHighToLow';
	const ORDER_BY_PRICE_LOW_TO_HIGH = 'priceLowToHigh';
	const ORDER_BY_POPULAR = 'popular';
	const ORDER_BY_RANDOM = 'random';

	/**
	 * @var int
	 */
	private $found_number = 0;

	/**
	 * @var string
	 */
	private $currency = 'any';

	/**
	 * @var array
	 */
	private $args = array(
		'post_type'           => 'estate',
		'ignore_sticky_posts' => 1,
		'posts_per_page'      => 12,
		'page'                => 1,
		'tax_query'           => array(
			'relation' => 'AND'
		),
		'meta_query'          => array(
			'relation' => 'AND'
		)
	);

	/**
	 * @var Estate_Filter[]
	 */
	private $filters = array();

	/**
	 * Estate_Factory constructor.
	 *
	 * @param array $args
	 */
	public function __construct( $args = array() ) {
		$this->args = array_merge( $this->args, $args );
	}

	/**
	 * @param Estate_Filter $estate_filter
	 */
	public function add_filter( Estate_Filter $estate_filter ) {
		$this->filters[] = $estate_filter;
	}

	/**
	 * @param mixed $page
	 */
	public function set_page( $page ) {
		$this->args['page'] = intval( $page );
	}

	/**
	 * @param array $status
	 */
	public function set_status( array $status ) {
		$this->args['post_status'] = $status;
	}

	/**
	 * @param string $sort_by
	 */
	public function set_sort_by( $sort_by ) {
		if ( $sort_by == Estate_Factory::ORDER_BY_PRICE_HIGH_TO_LOW || $sort_by == Estate_Factory::ORDER_BY_PRICE_LOW_TO_HIGH ) {
			if ( ! empty( \MyHomeCore\My_Home_Core()->currency ) && \MyHomeCore\My_Home_Core()->currency != 'any' ) {
				$key = \MyHomeCore\My_Home_Core()->currency;
			} else {
				$currency = Currencies::get_current();
				$key      = ! empty( $currency ) ? $currency->get_key() : 'price';
			}
		}

		if ( $sort_by == Estate_Factory::ORDER_BY_PRICE_HIGH_TO_LOW ) {
			if ( Price_Attribute::is_range() ) {
				$key .= '_from';
			}
			$this->args['orderby']  = 'meta_value_num';
			$this->args['meta_key'] = 'estate_attr_' . $key;
			$this->args['order']    = 'DESC';
		} elseif ( $sort_by == Estate_Factory::ORDER_BY_PRICE_LOW_TO_HIGH ) {
			if ( Price_Attribute::is_range() ) {
				$key .= '_from';
			}
			$this->args['orderby']  = 'meta_value_num';
			$this->args['meta_key'] = 'estate_attr_' . $key;
			$this->args['order']    = 'ASC';
		} elseif ( $sort_by == Estate_Factory::ORDER_BY_POPULAR ) {
			$this->args['orderby']      = 'meta_value_num';
			$this->args['meta_key']     = 'estate_views';
			$this->args['order']        = 'DESC';
			$this->args['meta_query'][] = array(
				'key'     => 'myhome_estate_views',
				'value'   => '',
				'compare' => 'NOT EXISTS'
			);
		} elseif ( $sort_by == Estate_Factory::ORDER_BY_TITLE_ASC ) {
			$this->args['order']   = 'ASC';
			$this->args['orderby'] = 'title';
		} elseif ( $sort_by == Estate_Factory::ORDER_BY_TITLE_DESC ) {
			$this->args['order']   = 'DESC';
			$this->args['orderby'] = 'title';
		} elseif ( $sort_by == Estate_Factory::ORDER_BY_RANDOM ) {
			$this->args['orderby'] = 'rand';
		} else {
			$this->args['orderby'] = 'date';
			$this->args['order']   = 'DESC';
		}
	}

	/**
	 * @param int $limit
	 */
	public function set_limit( $limit ) {
		$this->args['posts_per_page'] = intval( $limit );
	}

	public function set_featured_only() {
		$this->args['meta_query'][] = array(
			'key'     => 'estate_featured',
			'value'   => '1',
			'compare' => '=='
		);
	}

	/**
	 * @param array $ids
	 */
	public function set_estates__in( $ids = array() ) {
		if ( empty( $this->args['post__in'] ) ) {
			$this->args['post__in'] = $ids;

			return;
		}

		$this->args['post__in'] = array_filter( $this->args['post__in'], function ( $id ) use ( $ids ) {
			return in_array( $id, $ids );
		} );
	}

	/**
	 * @param array $ids
	 */
	public function set_estates__not_in( $ids = array() ) {
		$this->args['post__not_in'] = $ids;
	}

	/**
	 * @param string $currency
	 */
	public function set_currency( $currency ) {
		$this->currency = $currency;
	}

	/**
	 * @param int $user_id
	 */
	public function set_user_id( $user_id ) {
		$this->args['author'] = intval( $user_id );
	}

	public function set_keyword( $keyword ) {
		$this->args['s'] = $keyword;
	}

	/**
	 * @return int
	 */
	private function get_offset() {
		return $this->args['page'] * $this->args['posts_per_page'] - $this->args['posts_per_page'];
	}

	/**
	 * @return Estates
	 */
	public function get_results() {
		$this->args['offset']      = $this->get_offset();
		$this->args['tax_query'][] = Offer_Type_Attribute::get_exclude();

		if ( ! empty( \MyHomeCore\My_Home_Core()->currency ) && \MyHomeCore\My_Home_Core()->currency != 'any' && \MyHomeCore\My_Home_Core()->currency != 'undefined' ) {
			$currency = Currencies::get_current();
			$this->set_estates__in( $currency->get_estate_ids() );
		}

		$selected_offer_types = array();

		foreach ( $this->filters as $filter ) {
			if ( $filter instanceof Estate_Offer_Type_Filter ) {
				$selected_offer_types = $filter->get_selected_offer_types();
			}
		}

		foreach ( $this->filters as $filter ) {
			if ( $filter instanceof Estate_Price_Filter ) {
				$filter->set_selected_offer_types( $selected_offer_types );
				$this->set_estates__in( $filter->get_arg() );
			} else if ( $filter instanceof Estate_Keyword_Filter || $filter instanceof Estate_ID_Filter ) {
				$this->args[ $filter->get_type() ] = $filter->get_arg();
			} else if ( is_array( $this->args[ $filter->get_type() ] ) ) {
				$this->args[ $filter->get_type() ][] = $filter->get_arg();
			} else {
				$this->args[ $filter->get_type() ] = $filter->get_arg();
			}
		}

		if ( isset( $this->args['post__in'] ) && empty( $this->args['post__in'] ) ) {
			$this->found_number = 0;

			return new Estates();
		}

		$estates = new Estates();
		$query   = new \WP_Query( $this->args );

		$posts = $query->posts;
		foreach ( $posts as $post ) {
			$estates->add( new Estate( $post ) );
		}
		$this->found_number = $query->found_posts;

		return $estates;
	}

	/**
	 * @return int
	 */
	public function get_found_number() {
		return intval( $this->found_number );
	}

	/**
	 * @param array $args
	 *
	 * @return Estates
	 */
	public static function get( $args = array() ) {
		$args = array_merge(
			array(
				'post_type' => 'estate'
			), $args
		);

		$estates = new Estates();
		$query   = new \WP_Query( $args );
		$posts   = $query->posts;
		foreach ( $posts as $post ) {
			$estates->add( new Estate( $post ) );
		}

		return $estates;
	}

	/**
	 * @param int   $user_id
	 * @param array $status
	 *
	 * @return Estates
	 */
	public static function get_from_user( $user_id, $status = array() ) {
		$estate_factory = new Estate_Factory();
		$estate_factory->set_user_id( $user_id );
		$estate_factory->set_status( $status );
		$estate_factory->set_limit( Estate_Factory::NO_LIMIT );

		return $estate_factory->get_results();
	}

}