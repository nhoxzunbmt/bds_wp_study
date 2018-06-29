(function ($) {
	"use strict";

	if ($("#myhome_attributes_box").length > 0 && $("#acf-myhome_estate .inside.acf-fields").length > 0) {
		$("#acf-myhome_estate .acf-field-myhome-estate-tab-general").after($("#mh-admin-attributes").html());
		$("#myhome_attributes_box").remove();
	}

	$('.selectize').selectize({
		sortField: 'text',
		create   : function (input) {
			return {
				value: input,
				text : input
			}
		}
	});

	if ($('.mh-dismiss-yoast-notice').length) {
		var noticeInterval = setInterval(function () {
			if ($('.mh-dismiss-yoast-notice .notice-dismiss').length) {
				$('.mh-dismiss-yoast-notice .notice-dismiss').on('click', function () {
					$.post(ajaxurl, {
						action: 'myhome_yoast_dismiss_notice'
					}, function (response) {

					});
				});
				clearInterval(noticeInterval);
			}
		}, 500);
	}

	if ($(".redux-action_bar").length > 0) {
		$("#redux_save").after($("#myhome-clear-cache"));
		$("#myhome-clear-cache").show();
	}

})(jQuery);