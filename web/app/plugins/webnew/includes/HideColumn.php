<?php

function my_columns_filter( $columns ) {
	unset($columns['author']);
	unset($columns['categories']);
	unset($columns['tags']);
	unset($columns['comments']);
	return $columns;
}

// Filter pages
add_filter( 'manage_edit-page_columns', 'my_columns_filter',10, 1 );