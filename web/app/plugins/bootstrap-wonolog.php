<?php

/*
Plugin Name: Wonolog Bootstrap Plugin
Plugin URI:  https://github.com/inpsyde/Wonolog
Description: Enable Wonolog via their bootstrap method.
Version:     1.0
Author:      Chapman SMC

All Wonolog configurations have to be done in a MU plugin

*/
if ( function_exists( 'Inpsyde\Wonolog\bootstrap' ) ) {
	Inpsyde\Wonolog\bootstrap();
}

?>
