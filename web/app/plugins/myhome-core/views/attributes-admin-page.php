<div>

	<backend-panel
		id="myhome-backend-panel"
		:atts="<?php echo esc_attr( \MyHomeCore\Attributes\Attribute_Factory::get_all_unfiltered_data() ); ?>"
		:selected-elements="<?php echo esc_attr( json_encode( \MyHomeCore\Estates\Elements\Estate_Elements_Settings::get() ) ); ?>"
		:available-elements="<?php echo esc_attr( json_encode( \MyHomeCore\Estates\Elements\Estate_Elements_Settings::get_available() ) ); ?>"
		:element-types="<?php echo esc_attr( json_encode( \MyHomeCore\Estates\Elements\Estate_Elements_Settings::get_types() ) ); ?>"
		:selected-fields='<?php echo esc_attr( json_encode( \MyHomeCore\Panel\Panel_Fields::get_selected_backend() ) ); ?>'
		:available-fields='<?php echo esc_attr( json_encode( \MyHomeCore\Panel\Panel_Fields::get_available() ) ); ?>'
		:agent-fields='<?php echo esc_attr( json_encode( \MyHomeCore\Users\Fields\Settings::get_fields() ) ); ?>'
		:search-forms='<?php echo esc_attr( json_encode( \MyHomeCore\Components\Listing\Search_Forms\Search_Form::get_all_search_forms_data() ) ); ?>'
	>
	</backend-panel>

</div>
