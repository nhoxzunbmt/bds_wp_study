<?php

namespace MyHomeCore\Estates\Filters;


use MyHomeCore\Components\Listing\Form\Field;

/**
 * Class Estate_Text_Filter
 * @package MyHomeCore\Estates
 */
class Estate_Text_Filter extends Estate_Filter {

	const OPERATOR_OR = 'OR';
	const OPERATOR_AND = 'AND';
	const OPERATOR_IN = 'IN';
	const FIELD_NAME = 'name';
	const FIELD_SLUG = 'slug';

	/**
	 * @return array
	 */
	public function get_arg() {
		return array(
			'taxonomy' => $this->attribute->get_slug(),
			'operator' => $this->get_operator(),
			'field'    => $this->get_field(),
			'terms'    => $this->get_terms()
		);
	}

	/**
	 * @return string
	 */
	public function get_type() {
		return self::TAXONOMY;
	}

	private function get_field() {
		if ( $this->attribute->get_search_form_control() == Field::TEXT ) {
			return self::FIELD_NAME;
		}

		return self::FIELD_SLUG;
	}

	/**
	 * @return string
	 */
	private function get_operator() {
		if ( $this->attribute->get_search_form_control() == Field::SELECT_MULTIPLE ) {
			return self::OPERATOR_IN;
		}

		return self::OPERATOR_AND;
	}

	/**
	 * @return array
	 */
	protected function get_terms() {
		if ( $this->attribute->get_search_form_control() == Field::TEXT ) {
			return $this->values->get_names();
		}

		return $this->values->get_slugs();
	}

}