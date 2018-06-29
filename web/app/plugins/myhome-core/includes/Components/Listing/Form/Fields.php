<?php

namespace MyHomeCore\Components\Listing\Form;

use MyHomeCore\Attributes\Attribute_Factory;
use MyHomeCore\Components\Listing\Search_Forms\Search_Form;


/**
 * Class Fields
 * @package MyHomeCore\Components\Listing\Form
 */
class Fields {

	/**
	 * @var Field[]
	 */
	private $fields;

	/**
	 * Fields constructor.
	 *
	 * @param Field[] $fields
	 */
	public function __construct( $fields ) {
		$this->fields = $fields;
	}

	/**
	 * @return array
	 */
	public function get_data() {
		$fields = array();
		foreach ( $this->fields as $field ) {
			$fields[] = $field->get_data();
		}

		return $fields;
	}

	/**
	 * @param array $args
	 *
	 * @return Fields
	 */
	public static function get( $args = array() ) {
		if ( empty( $args['search_form'] ) || $args['search_form'] == 'default' || ! Search_Form::exists( $args['search_form'] ) ) {
			$attributes = Attribute_Factory::get_search();
		} else {
			$search_form = new Search_Form( $args['search_form'] );
			$attributes  = $search_form->get_attributes();
		}

		$fields = array();
		foreach ( $attributes as $attribute ) {
			if ( isset( $args[$attribute->get_slug() . '_show'] ) && empty( $args[$attribute->get_slug() . '_show'] ) ) {
				continue;
			}
			$fields[] = new Field( $attribute );
		}

		return new Fields( $fields );
	}

}