<h1>
	<?php esc_html_e( 'Agents', 'myhome-idx-broker' ); ?>
</h1>

<p>
	<a
		class="button button-primary"
		href="<?php echo esc_url( admin_url( 'admin-post.php?action=myhome_idx_broker_import_agents' ) ); ?>"
	>
		<?php esc_html_e( 'Import', 'myhome-idx-broker' ); ?>
	</a>
</p>

<table class="wp-list-table widefat fixed striped posts">

	<tr>
		<th>
			<?php esc_html_e( 'IDX Broker ID', 'myhome-idx-broker' ); ?>
		</th>
		<th class="manage-column">
			<?php esc_html_e( 'Name', 'myhome-idx-broker' ); ?>
		</th>
		<th>
			<?php esc_html_e( 'E-mail', 'myhome-idx-broker' ); ?>
		</th>
		<th></th>
	</tr>

	<?php foreach ( \MyHomeIDXBroker\Agents::get() as $myhome_agent ) : ?>
		<tr>
			<td>
				#<?php echo esc_html( $myhome_agent->get_idx_broker_id() ); ?>
			</td>
			<td>
				<a href="<?php echo esc_url( $myhome_agent->get_link() ); ?>">
					<?php echo esc_html( $myhome_agent->get_name() ); ?>
				</a>
			</td>
			<td>
				<?php if ( $myhome_agent->has_email() ) : ?>
					<a href="mailto:<?php echo esc_attr( $myhome_agent->get_email() ); ?>">
						<?php echo esc_html( $myhome_agent->get_email() ); ?>
					</a>
				<?php endif; ?>
			</td>
			<td>
				<a href="<?php echo esc_url( admin_url( 'user-edit.php?user_id=' . $myhome_agent->get_ID() ) ); ?>">
					<?php esc_html_e( 'Edit', 'myhome-idx-broker' ); ?>
				</a>
			</td>
		</tr>
	<?php endforeach; ?>
</table>