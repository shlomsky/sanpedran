<?php
/*
Plugin Name: Simple Flickr Photos
Plugin URI: http://www.iwasinturkey.com/photos?utm_source=wp&utm_medium=plgn&utm_campaign=SimpleFlickr
Description: Displays Flickr photos based on your settings.
Author: Onur Kocatas
Version: 2.9.9.5
Author URI: http://www.iwasinturkey.com?utm_source=wp&utm_medium=plgn&utm_campaign=SimpleFlickr
*/

/* Add our function to the widgets_init hook. */
add_action( 'widgets_init', 'simple_flickr_photos_load_widgets' );

/* Function that registers our widget. */
function simple_flickr_photos_load_widgets() {
	register_widget( 'Simple_Flickr_Photos_Widget' );
}


class Simple_Flickr_Photos_Widget extends WP_Widget {
function Simple_Flickr_Photos_Widget() {
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'Simple_Flickr_Photos', 'description' => 'Displays photos from Flickr based on your tags.' );

		/* Widget control settings. */
		$control_ops = array( 'width' => 500,  'id_base' => 'simple-flickr-photos-widget' );

		/* Create the widget. */
		$this->WP_Widget( 'simple-flickr-photos-widget', 'Simple Flickr Photos', $widget_ops, $control_ops );
	}
	
	
	function widget( $args, $instance ) {
		extract( $args );

		/* User-selected settings. */
$title = apply_filters('widget_title', $instance['title'] );
$flickr_gallery_source = $instance['flickr_gallery_source'];
$flickr_gallery_user_id = $instance['flickr_gallery_user_id'];
$flickr_gallery_group_id = $instance['flickr_gallery_group_id'];
$flickr_gallery_group_name = $instance['flickr_gallery_group_name'];
$flickr_gallery_count = $instance['flickr_gallery_count'];
$flickr_gallery_display = $instance['flickr_gallery_display'];
$flickr_gallery_size = $instance['flickr_gallery_size'];
$flickr_gallery_use_tag = $instance['flickr_gallery_use_tag'];
$flickr_gallery_tag = $instance['flickr_gallery_tag'];

		/* Before widget (defined by themes). */
		echo $before_widget;

		/* Title of widget (before and after defined by themes). */
		if ( $title )
			echo $before_title . $title . $after_title;
if ($flickr_gallery_size=="0"){
	$flickr_gallery_size='s';
	} else
if ($flickr_gallery_size=="1"){
	$flickr_gallery_size='t';
	} else
if ($flickr_gallery_size=="2"){
	$flickr_gallery_size='m';
	} 
if ($flickr_gallery_source=="0"){
	$router='source=user_tag&user='.$flickr_gallery_user_id.'';
}
else
if ($flickr_gallery_source=="2"){
	$router='source=all_tag';
}
else
if ($flickr_gallery_source=="1"){
	$router='context=in%2F'.$flickr_gallery_group_name.'%2F&source=group_tag&group='.$flickr_gallery_group_id.'';
}
if ($flickr_gallery_display=='0'){
$display='latest';
}
else
if ($flickr_gallery_display=='1'){
$display='random';
}
else
if ($flickr_gallery_display=='2'){
$display='popular';
}


if ($flickr_gallery_use_tag=='Yes'){
$tagged='&tag='.$flickr_gallery_tag.'';
}
$url = 'http://www.flickr.com/badge_code_v2.gne?count='.$flickr_gallery_count.'&display='.$display.'&size='.$flickr_gallery_size.'&layout=x&'.$router.''.$tagged.'';





echo '<!--simple flickr photos-->
<style type="text/css">
#flickr {text-align:center;}
#flickr img {
display:inline;
margin:3px;
padding:1px;
border:1px solid #ccc;}
</style>
<div id="flickr">';
					$html = file_get_contents($url);
					preg_match_all("/<div.*div>/", $html, $matches); 
						foreach($matches[0] as $div) { 
					  	echo str_replace("></a>", "/></a>", $div);
						}
echo '</div><!--simple flickr photos ends-->';
		/* After widget (defined by themes). */
		echo $after_widget;
	}
	
	
		function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		/* Strip tags (if needed) and update the widget settings. */
$instance['title'] = strip_tags( $new_instance['title'] );
$instance['flickr_gallery_source'] = strip_tags( $new_instance['flickr_gallery_source'] );
$instance['flickr_gallery_user_id'] = strip_tags( $new_instance['flickr_gallery_user_id'] );
$instance['flickr_gallery_group_id'] = strip_tags( $new_instance['flickr_gallery_group_id'] );
$instance['flickr_gallery_group_name'] = strip_tags( $new_instance['flickr_gallery_group_name'] );
$instance['flickr_gallery_count'] = strip_tags( $new_instance['flickr_gallery_count'] );
$instance['flickr_gallery_display'] = strip_tags( $new_instance['flickr_gallery_display'] );
$instance['flickr_gallery_size'] = strip_tags( $new_instance['flickr_gallery_size'] );
$instance['flickr_gallery_use_tag'] = strip_tags( $new_instance['flickr_gallery_use_tag'] );
$instance['flickr_gallery_tag'] = strip_tags($new_instance['flickr_gallery_tag']);
		return $instance;
	}
	
	function form( $instance ) {

		/* Set up some default widget settings. */
		$defaults = array( 'title' => 'Simple Flickr Photos', 'flickr_gallery_source' => '2', 'flickr_gallery_tag' => 'nature', 'flickr_gallery_count' => '6','flickr_gallery_display'=>'2','flickr_gallery_size'=>'0','flickr_gallery_use_tag'=>'Yes' );
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

<p>
<label for="<?php echo $this->get_field_id( 'title' ); ?>">Title:</label>
<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:100%;" />
</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'flickr_gallery_source' ); ?>">Whose photos to show:</label>
			<select name="<?php echo $this->get_field_name( 'flickr_gallery_source' ); ?>" id="<?php echo $this->get_field_id( 'flickr_gallery_source' ); ?>" class="widefat">
				<option value="0" <?php if ($instance ['flickr_gallery_source'] == '0') echo 'selected'; ?>>User</option>
				<option value="1" <?php if ($instance ['flickr_gallery_source'] == '1') echo 'selected'; ?>>Group</option>
				<option value="2" <?php if ($instance ['flickr_gallery_source'] == '2') echo 'selected'; ?>>Public</option>
			</select>
		</p>
<p>Fill the blanks below according to whose photos you want to show.Use <a href="http://idgettr.com/" target="_blank">idGettr</a> to find the Flickr IDs.</p>
<p>
<label for="<?php echo $this->get_field_id( 'flickr_gallery_user_id' ); ?>">Flickr User ID:</label>
<input id="<?php echo $this->get_field_id( 'flickr_gallery_user_id' ); ?>" name="<?php echo $this->get_field_name( 'flickr_gallery_user_id' ); ?>" value="<?php echo $instance['flickr_gallery_user_id']; ?>" style="width:100%;" />
</p>
<p>Group ID and group name has to be filled for group photos.Get the group name from the group URL - e.g. "nature" for "http://www.flickr.com/groups/nature"</p>
<p>
<label for="<?php echo $this->get_field_id( 'flickr_gallery_group_name' ); ?>">Flickr Group Name:</label>
<input id="<?php echo $this->get_field_id( 'flickr_gallery_group_name' ); ?>" name="<?php echo $this->get_field_name( 'flickr_gallery_group_name' ); ?>" value="<?php echo $instance['flickr_gallery_group_name']; ?>" style="width:100%;" />
</p>
<p>
<label for="<?php echo $this->get_field_id( 'flickr_gallery_group_id' ); ?>">Flickr Group ID:</label>
<input id="<?php echo $this->get_field_id( 'flickr_gallery_group_id' ); ?>" name="<?php echo $this->get_field_name( 'flickr_gallery_group_id' ); ?>" value="<?php echo $instance['flickr_gallery_group_id']; ?>" style="width:100%;" />
</p>
<p>
<label for="<?php echo $this->get_field_id( 'flickr_gallery_count' ); ?>">How many photos (max 10):</label>
<input id="<?php echo $this->get_field_id( 'flickr_gallery_count' ); ?>" name="<?php echo $this->get_field_name( 'flickr_gallery_count' ); ?>" value="<?php echo $instance['flickr_gallery_count']; ?>" style="width:100%;" />
</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'flickr_gallery_display' ); ?>">Which photos to show:</label>
			<select name="<?php echo $this->get_field_name( 'flickr_gallery_display' ); ?>" id="<?php echo $this->get_field_id( 'flickr_gallery_display' ); ?>" class="widefat">
				<option value="0" <?php if ($instance ['flickr_gallery_display'] == '0') echo 'selected'; ?> >Latest</option>
				<option value="1" <?php if ($instance ['flickr_gallery_display'] == '1') echo 'selected'; ?> >Random</option>
				<option value="2" <?php if ($instance ['flickr_gallery_display'] == '2') echo 'selected'; ?> >Popular</option>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'flickr_gallery_size' ); ?>">Photo size:</label>
			<select name="<?php echo $this->get_field_name( 'flickr_gallery_size' ); ?>" id="<?php echo $this->get_field_id( 'flickr_gallery_size' ); ?>" class="widefat">
				<option value="0" <?php if ($instance ['flickr_gallery_size'] == '0') echo 'selected'; ?> >Square</option>
				<option value="1" <?php if ($instance ['flickr_gallery_size'] == '1') echo 'selected'; ?>>Thumbnail</option>
				<option value="2" <?php if ($instance ['flickr_gallery_size'] == '2') echo 'selected'; ?>>Medium</option>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'flickr_gallery_use_tag' ); ?>">Do you want to use tag function:</label>
			<select name="<?php echo $this->get_field_name( 'flickr_gallery_use_tag' ); ?>" id="<?php echo $this->get_field_id( 'flickr_gallery_use_tag' ); ?>" class="widefat">
				<option value="Yes" <?php if ($instance ['flickr_gallery_use_tag'] == 'Yes') echo 'selected'; ?> >Yes</option>
				<option value="No" <?php if ($instance ['flickr_gallery_use_tag'] == 'No') echo 'selected'; ?>>No</option>
			</select>
			</p>
<p>
<label for="<?php echo $this->get_field_id( 'flickr_gallery_tag' ); ?>">Tag:</label>
<input id="<?php echo $this->get_field_id( 'flickr_gallery_tag' ); ?>" name="<?php echo $this->get_field_name( 'flickr_gallery_tag' ); ?>" value="<?php echo $instance['flickr_gallery_tag']; ?>" style="width:100%;" />
</p>
<?php
	}
}
?>