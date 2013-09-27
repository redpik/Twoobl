<?php

// utiliser les shortcodes dans les widgets
add_filter('widget_text', 'do_shortcode');



function twoobl_widgets_init() {

	register_sidebar(array(
		'name'          => __('Sidebar', 'twoobl'),
		'id'            => 'primary',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'before_title'  => '<span class="title">',
		'after_title'   => '</span>',
		'after_widget'  => '</div>'
	));

	register_sidebar(array(
		'name'          => __('Footer', 'twoobl'),
		'id'            => 'footer',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'before_title'  => '<span class="title">',
		'after_title'   => '</span>',
		'after_widget'  => '</div>'
	));

	// Register widget, pour register des nouveaux widgets
	register_widget('TextClass');

	//on suppr les widgets par défaut qui servent à rien
	unregister_widget('WP_Widget_Meta');
	unregister_widget('WP_Widget_Tag_Cloud');
	unregister_widget('WP_Widget_Calendar');
	unregister_widget('WP_Widget_RSS');
	unregister_widget('WP_Widget_Search');
}
add_action('widgets_init', 'twoobl_widgets_init');





class TextClass extends WP_Widget {

	function __construct() {
		$widget_ops = array('classname' => 'widget_textclass', 'description' => __('Arbitrary text or HTML with custom class', 'twoobl'));
		$control_ops = array('width' => 400, 'height' => 350);
		parent::__construct('TextClass', __('Text with class', 'twoobl'), $widget_ops, $control_ops);
	}

	function widget( $args, $instance ) {
		extract($args);
		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );
		$text = apply_filters( 'widget_text', empty( $instance['text'] ) ? '' : $instance['text'], $instance );
		$class = $instance['class'];
		$before_widget = str_replace($this->widget_options['classname'], $this->widget_options['classname'].' '.$class, $before_widget);
		echo $before_widget;
		if ( !empty( $title ) ) { echo $before_title . $title . $after_title; } ?>
			<div class="textwidget"><?php echo !empty( $instance['filter'] ) ? wpautop( $text ) : $text; ?></div>
		<?php
		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		if ( current_user_can('unfiltered_html') )
			$instance['text'] =  $new_instance['text'];
		else
			$instance['text'] = stripslashes( wp_filter_post_kses( addslashes($new_instance['text']) ) ); // wp_filter_post_kses() expects slashed
		$instance['class'] = strip_tags( $new_instance['class'] );
		$instance['filter'] = isset($new_instance['filter']);
		return $instance;
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'text' => '', 'class' => '' ) );
		$title = strip_tags($instance['title']);
		$text = esc_textarea($instance['text']);
        $class = format_to_edit($instance['class']);
		
		$classes = array('facebook', 'love', 'warning', 'team', 'misc');
		?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'twoobl'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>

		<textarea class="widefat" rows="16" cols="20" id="<?php echo $this->get_field_id('text'); ?>" name="<?php echo $this->get_field_name('text'); ?>"><?php echo $text; ?></textarea>

		<p>
			<label for="<?php echo $this->get_field_id('class'); ?>"><?php _e('Custom class', 'twoobl'); ?>&nbsp;:</label>
			<select name="<?php echo $this->get_field_name('class'); ?>" id="<?php echo $this->get_field_id('class'); ?>" class="widefat">
				<?php foreach( $classes as $c ) {
					echo '<option value="'.$c.'"';
					if( $class==$c ) echo ' selected="selected"';
					echo '>'.$c.'</option>';
				}?>
			</select>
		</p>

		<p><input id="<?php echo $this->get_field_id('filter'); ?>" name="<?php echo $this->get_field_name('filter'); ?>" type="checkbox" <?php checked(isset($instance['filter']) ? $instance['filter'] : 0); ?> />&nbsp;<label for="<?php echo $this->get_field_id('filter'); ?>"><?php _e('Automatically add paragraphs', 'twoobl'); ?></label></p>
	<?php
	}
}




















/* pour tests - à supprimer */


/**
* Add custom HTML classes to individual widgets from a text field
*/

function roots_widget_classes_input($instance, $widget) {
  if (!isset($instance['classes'])) {
    $instance['classes'] = null;
  }

  $row = "<p>\n";
  $row .= "\t<label for='widget-{$widget->id_base}-{$widget->number}-classes'>HTML Classes:</label>\n";
  $row .= "\t<input type='text' name='widget-{$widget->id_base}[{$widget->number}][classes]' id='widget-{$widget->id_base}-{$widget->number}-classes' class='widefat' value='{$instance['classes']}'>\n";
  $row .= "</p>\n";

  echo $row;
  return $instance;
}
add_filter('widget_form_callback', 'roots_widget_classes_input', 10, 2);

function roots_widget_classes_save($instance, $new_instance) {
  $instance['classes'] = $new_instance['classes'];
  return $instance;
}
add_filter('widget_update_callback', 'roots_widget_classes_save', 10, 2);

function roots_widget_classes($params) {
  global $wp_registered_widgets;
  $widget_id = $params[0]['widget_id'];
  $widget_obj = $wp_registered_widgets[$widget_id];
  $widget_opt = get_option($widget_obj['callback'][0]->option_name);
  $widget_num = $widget_obj['params'][0]['number'];

  if (isset($widget_opt[$widget_num]['classes']) && !empty($widget_opt[$widget_num]['classes']))
    $params[0]['before_widget'] = preg_replace('/class="/', "class=\"{$widget_opt[$widget_num]['classes']} ", $params[0]['before_widget'], 1);

  return $params;
}
add_filter('dynamic_sidebar_params', 'roots_widget_classes');