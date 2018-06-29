<?php

namespace MyHomeIDXBroker;


/**
 * Class Importer
 * @package MyHomeIDXBroker
 */
class Importer {

	const LAST_CHECK = 'myhome_idx_broker_last_check';
	const CURRENT_STATUS = 'myhome_idx_broker_current_status';
	const STATUS_STOP = 'stop';
	const STATUS_WORK = 'work';
	const JOBS = 'myhome_idx_broker_jobs';

	public function import() {
		$status = get_option( Importer::CURRENT_STATUS, Importer::STATUS_STOP );
		if ( $status == Importer::STATUS_WORK ) {
			$this->job();
		}
	}

	public function init() {
		$last_check              = get_option( Importer::LAST_CHECK );
		$api                     = new Api();
		$properties_active       = $api->get_new_active_properties( $last_check );
		$properties_sold_pending = $api->get_sold_pending_properties( $last_check );
		$properties              = array_merge( $properties_active, $properties_sold_pending );

		update_option( Importer::JOBS, $properties );
		update_option( Importer::LAST_CHECK, date( "Y-m-d h:i:s" ) );

		if ( ! empty( $properties ) ) {
			update_option( Importer::CURRENT_STATUS, Importer::STATUS_WORK );
			echo json_encode( array(
				'start' => true,
				'found' => count( $properties ),
				'msg'   => esc_html__( 'Please wait synchronizing data', 'myhome-idx-broker' )
			) );
		} else {
			update_option( Importer::CURRENT_STATUS, Importer::STATUS_STOP );
			echo json_encode( array(
				'start' => false,
				'msg'   => esc_html__( 'Nothing new', 'myhome-idx-broker' )
			) );
		}
	}

	public function job() {
		$properties = get_option( Importer::JOBS );
		if ( empty( $properties ) || ! is_array( $properties ) ) {
			update_option( Importer::CURRENT_STATUS, Importer::STATUS_STOP );

			return false;
		}

		$property           = array_shift( $properties );
		$properties_manager = new Properties();
		if ( ! $properties_manager->exists( $property['listingID'] ) ) {
			$properties_manager->create( $property );
		} else {
			$properties_manager->update( $property );
		}

		update_option( Importer::JOBS, $properties );

		if ( empty( $properties ) ) {
			update_option( Importer::CURRENT_STATUS, Importer::STATUS_STOP );
		}

		return true;
	}

}