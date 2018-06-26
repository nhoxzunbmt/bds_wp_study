<?php

$posts = get_posts();

$collection = collect($posts)->filter(function ($post) {

	return $post->ID == 5;
});

echo '<pre>';
var_dump($collection);
die();