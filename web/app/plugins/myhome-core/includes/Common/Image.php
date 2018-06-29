<?php

namespace MyHomeCore\Common;


/**
 * Class Image
 * @package MyHomeCore\Common
 */
class Image {

	const STANDARD = 'standard';
	const ADDITIONAL = 'additional';
	const SQUARE = 'square';

	/**
	 * @param int|null $thumbnail_id
	 * @param string   $size
	 * @param string   $alt
	 * @param string   $class
	 */
	public static function the_image( $thumbnail_id = null, $size = self::STANDARD, $alt = '', $class = '' ) {
		$thumbnail_id = is_null( $thumbnail_id ) ? get_post_thumbnail_id() : $thumbnail_id;

		$image_meta = wp_get_attachment_metadata( $thumbnail_id );

		$prefix       = 'myhome-';

		if ( $size == self::STANDARD
			|| $size == self::ADDITIONAL
			|| $size == self::SQUARE
		) {
			$thumbnail_size = $prefix . $size . '-xs';
		} else {
			$thumbnail_size = $prefix . $size;
		}


		ob_start();
		?>
		<img
			data-srcset="<?php echo esc_attr( wp_get_attachment_image_srcset( $thumbnail_id, $thumbnail_size ) ); ?>"
			class="lazyload <?php echo esc_attr( $class ); ?>"
			alt="<?php echo esc_attr( $alt ); ?>"
			data-sizes="auto"
		>
		<?php
		echo ob_get_clean();
	}

}