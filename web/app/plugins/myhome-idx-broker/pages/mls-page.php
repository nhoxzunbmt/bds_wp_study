<h1>
	<?php esc_html_e( 'MLS', 'myhome-idx-broker' ); ?>
</h1>

<form
	action="<?php echo esc_url( admin_url( 'admin-post.php?action=myhome_idx_broker_save_mls_ids' ) ); ?>"
	method="post"
>

	<?php wp_nonce_field( 'myhome_idx_broker_update_mls', 'check_sec' ); ?>

	<p>
		<label for="mls-ids">
			<?php esc_html_e( 'MLS ID\'s (one per line)', 'myhome-idx-broker' ); ?>
		</label>
	</p>

	<p>
	<textarea name="mls_ids" id="mls-ids" cols="30" rows="10"><?php
		foreach ( \MyHomeIDXBroker\MLS::get() as $myhome_mls_id ) :
			echo esc_html( $myhome_mls_id ) . "\n";
		endforeach;
		?></textarea>
	</p>

	<p>
		<input
			type="submit"
			class="button button-primary"
			value="<?php esc_attr_e( 'Save', 'myhome-idx-broker' ); ?>"
		>
	</p>

</form>