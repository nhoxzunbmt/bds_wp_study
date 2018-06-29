<?php
global $myhome_mosaic;
/* @var \MyHomeCore\Terms\Term[] $myhome_mosaic */
?>

<div class="mh-mosaic-grid">
	<?php foreach ( $myhome_mosaic as $myhome_term ) : ?>
		<div class="mh-mosaic-grid__item">
			<div class="mh-box__content">
				<a href="<?php echo esc_url( $myhome_term->get_link() ); ?>"
				   title="<?php echo esc_attr( $myhome_term->get_name() ); ?>"
				   class="mh-box">
					<span class="mh-box__img-wrapper">
						<?php
						if ( $myhome_term->has_image() ) :
							$myhome_term->image();
						endif;
						?>
					</span>
					<div class="mh-box__middle">
						<h3 class="mh-box__title mh-heading mh-heading--style-3">
							<?php echo esc_attr( $myhome_term->get_name() ); ?>
						</h3>
					</div>
				</a>
			</div>
		</div>
	<?php endforeach ?>
</div>