<?php

add_filter( 'manage_posts_extra_tablenav', 'my_add_category_dropdown' );
function my_add_category_dropdown($which) {
	if($which == 'bottom') return;
	$args = array(
		'show_option_all'   => '',
		'show_option_none'  => '',
		'option_none_value' => '-1',
		'orderby'           => 'ID',
		'order'             => 'ASC',
		'show_count'        => 0,
		'hide_empty'        => 1,
		'child_of'          => 0,
		'exclude'           => '',
		'echo'              => 1,
		'selected'          => 0,
		'hierarchical'      => 0,
		'name'              => 'cat',
		'id'                => '',
		'class'             => 'postform',
		'depth'             => 0,
		'tab_index'         => 0,
		'taxonomy'          => 'category',
		'hide_if_empty'     => false,
		'value_field'       => 'term_id',
	);
	wp_dropdown_categories( $args );
}


add_action( 'manage_posts_extra_tablenav', 'btn_export' );

function btn_export($which) {
	if($which == 'bottom') return;
	echo '<div class="alignleft actions">
        <a href="' . admin_url( 'admin-post.php?action=export' ) . '" class="button">Export</a>
    </div>';
}

add_action( 'admin_post_export', 'export_posts' );


function export_posts() {
	header( 'Content-Type: application/csv' );
	header( 'Content-Disposition: attachment; filename=export.csv' );
	header( 'Pragma: no-cache' );

	$output = fopen( 'php://output', 'w' );
	fputcsv( $output, array(
		'Name',
		'ID',
	) );

	return $output;
}



