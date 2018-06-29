jQuery(document).ready(function () {
	var offsetTop = 24;

	if (jQuery('.mh-sticky-menu-placeholder').length) {
		offsetTop = jQuery('.mh-sticky-menu-placeholder').height() + 24;
	}

	jQuery('.mh-layout__sidebar--sticky').stick_in_parent({
		offset_top: offsetTop
	});
});