<?php
//manage_{post_type}_posts_columns
//manage_{post_type}_posts_custom_column


// Add the custom columns to the book post type:
add_filter( 'manage_post_posts_columns', 'set_custom_edit_book_columns' );
function set_custom_edit_book_columns( $columns ) {
	//do_action('wonolog.log',$columns);

	$columns['book_author'] = __( 'Author HAHA', 'your_text_domain' );
	$columns['publisher']   = __( 'Publisher', 'your_text_domain' );

	return $columns;
}

// Add the data to the custom columns for the book post type:
add_action( 'manage_post_posts_custom_column', 'custom_book_column', 10, 2 );
function custom_book_column( $column, $post_id ) {
	switch ( $column ) {
		case 'book_author' :
			$terms = get_the_term_list( $post_id, 'book_author', '', ',', '' );
			if ( is_string( $terms ) ) {
				echo $terms;
			} else {
				_e( 'Unable to get author(s)', 'your_text_domain' );
			}
			break;

		case 'publisher' :
			echo get_post_meta( $post_id, 'publisher', true );
			break;
		case 'title':
			do_action('wonolog.log',$post_id);
			break;
	}
}

add_action( 'manage_posts_custom_column', 'manage_posts_custom_column', 10, 2 );
function manage_posts_custom_column( $column, $post_id ) {
	dump(func_get_args());
	switch ( $column ) {

		case 'book_author' :
			$terms = get_the_term_list( $post_id, 'book_author', '', ',', '' );
			if ( is_string( $terms ) ) {
				echo $terms;
			} else {
				_e( 'Unable to get author(s)', 'your_text_domain' );
			}
			break;

		case 'publisher' :
			echo get_post_meta( $post_id, 'publisher', true );
			break;
		case 'title':
			do_action('wonolog.log',$post_id);
			break;
	}
}





$post_columns = new CPT_columns('post');
$post_columns->add_column('title_na',
	array(
		'label'    => __('Day Title Ne'),
		'type'     => 'custom_value',
		'sortable' => true,
		'prefix' => '<b>',
		'suffix' => '</b>',
		'def' => 'Not defined',
		'order' => '2',
		'callback' => function ($value){
			$post = get_post($value);
			return strtoupper($post->post_title);
		}
	)
);


$post_columns->add_column('title',
	array(
		'label'    => __('Tieu De'),
		'type'     => 'custom_value',

		'callback' => function ($value){
			echo '<pre>';
			var_dump($value);
			die();
		}
	)
);


add_filter('post_row_actions','my_action_row', 10, 2);

function my_action_row($actions, $post){
	//check for your post type
	if ($post->post_type =="post"){
		$actions['in_google'] = '<a href="http://www.google.com/?q='.$post->post_title.'" target="_blank">Xem thu</a>';
	}
	return $actions;
}