<h1><?php esc_html_e( 'MyHome IDX Broker', 'myhome-idx-broker' ); ?></h1>
<h2><?php esc_html_e( 'About IDX', 'myhome-idx-broker' ) ?></h2>

<p>
	<?php esc_html_e( 'MyHome IDX Broker integration is available for:', 'myhome-idx-broker' ); ?>
	<strong><?php esc_html_e( 'United States, Canada, Bahamas, Mexico and Jamaica', 'myhome-idx-broker' ); ?></strong> <?php esc_html_E( 'only.', 'myhome-idx-broker' ); ?>
</p>

<p>
	<?php esc_html_e( 'You can find full list IDX / MLS Coverage here:', 'myhome-idx-broker' ); ?>
	<a target="_blank" href="https://idxbroker.com/idx_mls_coverage">https://idxbroker.com/idx_mls_coverage</a>
</p>

<h2><?php esc_html_e( 'Register', 'myhome-idx-broker' ); ?></h2>

<p>
	<?php esc_html_e( 'If you wish to use it you need to register first.', 'myhome-idx-broker' ); ?>
	<?php esc_html_e( 'Click here to fill the form:', 'myhome-idx-broker' ); ?>
</p>

<p>
	<a href="https://signup.idxbroker.com/d/myhome" class="button button-primary" target="_blank">
		<?php esc_html_e( 'Register', 'myhome-idx-broker' ); ?>
	</a>
</p>

<h2><?php esc_html_e( 'Basic settings', 'myhome-idx-broker' ); ?></h2>

<p>
	<?php esc_html_e( 'If you need any help with configuration please fill free to contact us', 'myhome-idx-broker' ); ?> -
	<a href="mailto:support@tangibledesign.net">support@tangibledesign.net</a>
</p>

<form action="<?php echo esc_url( admin_url( 'admin-post.php?action=myhome_idx_broker_save_options' ) ); ?>" method="post">

	<?php wp_nonce_field( 'myhome_idx_broker_update_options', 'check_sec' ); ?>

	<table class="form-table">
		<tr>
			<th>
				<label for="api-key">
					<?php esc_html_e( 'API Key', 'myhome-idx-broker' ); ?>
				</label>
			</th>
			<td>
				<input
					id="api-key"
					type="text"
					name="options[api_key]"
					value="<?php echo esc_attr( \MyHomeIDXBroker\My_Home_IDX_Broker()->options->get( 'api_key' ) ); ?>">
			</td>
		</tr>
		<tr>
			<th>
				<label for="init-status">
					<?php esc_html_e( 'Initial property status', 'myhome-idx-broker' ); ?>
				</label>
			</th>
			<td>
				<select name="options[init_status]" id="init-status">
					<option
						value="publish"
						<?php if ( \MyHomeIDXBroker\My_Home_IDX_Broker()->options->get( 'init_status' ) == 'publish' ) : ?>
							selected="selected"
						<?php endif; ?>
					>
						<?php esc_html_e( 'Publish', 'myhome-idx-broker' ); ?>
					</option>
					<option
						value="pending"
						<?php if ( \MyHomeIDXBroker\My_Home_IDX_Broker()->options->get( 'init_status' ) == 'pending' ) : ?>
							selected="selected"
						<?php endif; ?>
					>
						<?php esc_html_e( 'Pending', 'myhome-idx-broker' ); ?>
					</option>
					<option
						value="draft"
						<?php if ( \MyHomeIDXBroker\My_Home_IDX_Broker()->options->get( 'init_status' ) == 'draft' ) : ?>
							selected="selected"
						<?php endif; ?>
					>
						<?php esc_html_e( 'Draft', 'myhome-idx-broker' ); ?>
					</option>
				</select>
			</td>
		</tr>
		<tr>
			<th>
				<label for="offer-type">
					<?php esc_html_e( 'Default offer type', 'myhome-idx-broker' ); ?>
				</label>
			</th>
			<td>
				<?php $myhome_idx_broker_offer_type = intval( \MyHomeIDXBroker\My_Home_IDX_Broker()->options->get( 'offer_type' ) ); ?>
				<select name="options[offer_type]" id="offer-type">

					<?php foreach ( \MyHomeCore\Terms\Term_Factory::get_offer_types() as $offer_type ) : ?>
						<option
							<?php if ( $myhome_idx_broker_offer_type == $offer_type->get_ID() ) : ?>
								selected="selected"
							<?php endif; ?>
							value="<?php echo esc_attr( $offer_type->get_ID() ); ?>"
						>
							<?php echo esc_html( $offer_type->get_name() ); ?>
						</option>
					<?php endforeach; ?>

				</select>
			</td>
		</tr>
		<tr>
			<th>
				<label for="offer-type-sold">
					<?php esc_html_e( 'Offer type for "Sold" properties:', 'myhome-idx-broker' ); ?>
				</label>
			</th>
			<td>
				<?php $myhome_idx_broker_offer_type_sold = intval( \MyHomeIDXBroker\My_Home_IDX_Broker()->options->get( 'offer_type_sold' ) ); ?>
				<select name="options[offer_type_sold]" id="offer-type-sold">

					<?php foreach ( \MyHomeCore\Terms\Term_Factory::get_offer_types() as $offer_type ) : ?>
						<option
							<?php if ( $myhome_idx_broker_offer_type_sold == $offer_type->get_ID() ) : ?>
								selected="selected"
							<?php endif; ?>
							value="<?php echo esc_attr( $offer_type->get_ID() ); ?>"
						>
							<?php echo esc_html( $offer_type->get_name() ); ?>
						</option>
					<?php endforeach; ?>

				</select>
			</td>
		</tr>
		<tr>
			<th>
				<label for="offer-type-pending">
					<?php esc_html_e( 'Offer type for "Pending" properties:', 'myhome-idx-broker' ); ?>
				</label>
			</th>
			<td>
				<?php $myhome_idx_broker_offer_type_pending = intval( \MyHomeIDXBroker\My_Home_IDX_Broker()->options->get( 'offer_type_pending' ) ); ?>
				<select name="options[offer_type_pending]" id="offer-type-pending">

					<?php foreach ( \MyHomeCore\Terms\Term_Factory::get_offer_types() as $offer_type ) : ?>
						<option
							<?php if ( $myhome_idx_broker_offer_type_pending == $offer_type->get_ID() ) : ?>
								selected="selected"
							<?php endif; ?>
							value="<?php echo esc_attr( $offer_type->get_ID() ); ?>"
						>
							<?php echo esc_html( $offer_type->get_name() ); ?>
						</option>
					<?php endforeach; ?>

				</select>
			</td>
		</tr>
		<tr>
			<th>
				<label for="images-limit">
					<?php esc_html_e( 'Limit number of imported images', 'myhome-idx-broker' ); ?>
				</label>
			</th>
			<td>
				<?php
				$myhome_idx_broker_images_limit = \MyHomeIDXBroker\My_Home_IDX_Broker()->options->get( 'images_limit' );

				if ( $myhome_idx_broker_images_limit == '' ) {
					$myhome_idx_broker_images_limit = 25;
				} else {
					$myhome_idx_broker_images_limit = intval( $myhome_idx_broker_images_limit );
				}
				?>
				<input name="options[images_limit]" id="images-limit" type="text" value="<?php echo esc_attr( $myhome_idx_broker_images_limit ); ?>">
			</td>
		</tr>

		<?php
		$myhome_breadcrumb_attributes = \MyHomeCore\Common\Breadcrumbs\Breadcrumbs::get_attributes();

		if ( count( $myhome_breadcrumb_attributes ) ) :
			?>
			<tr>
				<th>
					<?php esc_html_e( 'Breadcrumbs default values', 'myhome-idx-broker' ); ?>
				</th>
				<td>
					<p>
						<?php
						echo wp_kses_post( __( 'Fields below are required by breadcrumbs (<a href="admin.php?page=MyHome&tab=5" target="_blank">click here to change it</a>).
							<br>Choose default values below in case imported property does not have it set:', 'myhome-idx-broker' ) );
						?>
					</p>
				</td>
			</tr>
		<?php
		endif;
		?>

		<?php foreach ( $myhome_breadcrumb_attributes as $myhome_attribute ) : ?>
			<tr>
				<th>
					<label for="attr-<?php echo esc_attr( $myhome_attribute->get_slug() ); ?>">
						<?php echo esc_html( $myhome_attribute->get_name() ); ?>
					</label>
				</th>
				<td>
					<select
						name="options[attributes][<?php echo esc_attr( $myhome_attribute->get_ID() ); ?>]"
						id="attr-<?php echo esc_attr( $myhome_attribute->get_slug() ); ?>"
					>
						<?php foreach ( $myhome_attribute->get_terms() as $myhome_term ) : ?>
							<option
								value="<?php echo esc_attr( $myhome_term->get_ID() ); ?>"
								<?php
								$myhome_current_term_id = intval( \MyHomeIDXBroker\My_Home_IDX_Broker()->options->get( 'attributes', $myhome_attribute->get_ID() ) );
								if ( $myhome_term->get_ID() == $myhome_current_term_id ) : ?>
									selected="selected"
								<?php endif; ?>
							>
								<?php echo esc_html( $myhome_term->get_name() ); ?>
							</option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
		<?php endforeach; ?>
	</table>

	<button class="button button-primary">
		<?php esc_html_e( 'Save options', 'myhome-idx-broker' ); ?>
	</button>

</form>
