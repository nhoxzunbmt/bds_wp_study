<?php

namespace MyHomeIDXBroker;

/**
 * Class Api
 * @package MyHomeIDXBroker
 */
class Api {

	const CITIES = 'https://api.idxbroker.com/mls/cities';
	const COUNTIES = 'https://api.idxbroker.com/mls/counties';
	const POSTAL_CODES = 'https://api.idxbroker.com/mls/postalcodes';
	const PROPERTY_TYPES = 'https://api.idxbroker.com/mls/propertytypes/a';
	const SEARCH_FIELDS = 'https://api.idxbroker.com/mls/searchfields';
	const SEARCH_FIELD_VALUES = 'https://api.idxbroker.com/mls/searchfieldvalues';
	const AGENTS = 'https://api.idxbroker.com/clients/agents';
	const PROPERTIES_ACTIVE = 'https://api.idxbroker.com/clients/featured';
	const PROPERTIES_SOLD_PENDING = 'https://api.idxbroker.com/clients/soldpending';
	const PROPERTIES_SUPPLEMENTAL = 'https://api.idxbroker.com/clients/supplemental';

	const OPTION_API_KEY = 'api_key';

	/**
	 * @var string
	 */
	private $key;

	/**
	 * Api constructor.
	 */
	public function __construct() {
		$this->key = My_Home_IDX_Broker()->options->get( Api::OPTION_API_KEY );
	}

	/**
	 * @param $query
	 *
	 * @return object|bool
	 */
	public function request( $query ) {
		// headers (required and optional)
		$headers = array(
			'Content-Type: application/x-www-form-urlencoded', // required
			'accesskey: ' . $this->key, // required - replace with your own
			'outputtype: json' // optional - overrides the preferences in our API control page
		);

		$handle = curl_init();
		curl_setopt( $handle, CURLOPT_URL, $query );
		curl_setopt( $handle, CURLOPT_HTTPHEADER, $headers );
		curl_setopt( $handle, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $handle, CURLOPT_SSL_VERIFYHOST, false );
		curl_setopt( $handle, CURLOPT_SSL_VERIFYPEER, false );
		$response = curl_exec( $handle );
		$code     = curl_getinfo( $handle, CURLINFO_HTTP_CODE );

		if ( $code >= 200 || $code < 300 ) {
			return json_decode( $response );
		}

		return false;
	}

	public function get_mls() {

	}

	/**
	 * @param $mls_ID
	 *
	 * @return bool|object
	 */
	public function get_search_fields( $mls_ID ) {
		$query = Api::SEARCH_FIELDS . '/' . $mls_ID;

		return $this->request( $query );
	}

	/**
	 * @param $mls_ID
	 * @param $mls_pt_ID
	 * @param $mls_name
	 */
	public function get_search_field_values( $mls_ID, $mls_pt_ID, $mls_name ) {
		$query = Api::SEARCH_FIELD_VALUES . '/' . $mls_ID . '?mlsPtID=' . $mls_pt_ID . '&name=' . $mls_name;

		$this->request( $query );
	}

	/**
	 * @return array
	 */
	public function get_agents() {
		$query = Api::AGENTS;

		$response = $this->request( $query );

		if ( isset( $response->agent ) ) {
			return $response->agent;
		}

		return array();
	}

	/**
	 * @param string $last_check
	 *
	 * @return array
	 */
	public function get_new_active_properties( $last_check = '' ) {
		$query = Api::PROPERTIES_ACTIVE;

		$response   = $this->request( $query );
		$properties = json_decode( json_encode( $response ), true );

		if ( ! is_array( $properties ) ) {
			return array();
		}

		return $properties;
	}

	/**
	 * @param string $last_check
	 *
	 * @return array
	 */
	public function get_sold_pending_properties( $last_check = '' ) {
		$query = Api::PROPERTIES_SOLD_PENDING;

		$response   = $this->request( $query );
		$properties = json_decode( json_encode( $response ), true );

		if ( ! is_array( $properties ) ) {
			return array();
		}

		return $properties;
	}

	public function get_supplemental_properties() {
		$query = Api::PROPERTIES_SUPPLEMENTAL;

		$this->request( $query );
	}

}