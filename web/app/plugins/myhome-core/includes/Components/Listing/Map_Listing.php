<?php

namespace MyHomeCore\Components\Listing;


/**
 * Class Map_Listing
 * @package MyHomeCore\Components\Listing
 */
class Map_Listing {

	/**
	 * @var Listing_Map_Settings
	 */
	protected $settings;

	/**
	 * Map_Listing constructor.
	 *
	 * @param array $args
	 */
	public function __construct( $args ) {
		$this->settings = new Listing_Map_Settings( $args );
	}

	public function display() {
		ob_start();
		?>
        <listing-map
                id="myhome-listing-map"
                config-key="<?php echo esc_attr( $this->settings->get_config() ); ?>"
        >
        </listing-map>
		<?php
		echo ob_get_clean();
	}

}