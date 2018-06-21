<!-- This file is used to markup the administration form of the widget. -->


<p>
    <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', $this->get_widget_slug() ); ?></label>
    <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" />
</p>


<p>
    <label for="<?php echo $this->get_field_id( 'note' ); ?>"><?php _e( 'note:', $this->get_widget_slug() ); ?></label>
    <input class="widefat" id="<?php echo $this->get_field_id( 'note' ); ?>" name="<?php echo $this->get_field_name( 'Note' ); ?>" type="text" value="<?php echo $note; ?>" />
</p>