<?php
get_header();
get_template_part( 'templates/top-title' );
get_template_part( 'templates/breadcrumbs' );

?>
	<div class="mh-layout mh-top-title-offset">
		<?php
		$myhome_listing = new \MyHomeCore\Components\Listing\Archive_Listing();
		$myhome_listing->display();
		?>
	</div>
<?php

get_footer();