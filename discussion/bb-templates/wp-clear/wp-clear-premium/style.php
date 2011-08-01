<?php
require_once( dirname(__FILE__) . '../../../../wp-config.php');
require_once( dirname(__FILE__) . '/functions.php');
header("Content-type: text/css");
global $options;
foreach ($options as $value) { if (get_settings( $value['id'] ) === FALSE) { $$value['id'] = $value['std']; } else { $$value['id'] = get_settings( $value['id'] ); } } ?>

/* --------------[ User-Defined Adjustments from Stylesheet #2 ]-------------- */

@import 'style-2.css';

/* --------------[ User-Defined Adjustments ]-------------- */

body {
	<?php if ( $wp_clear_body_backgroundcolor ) { ?>
	background-color: <?php echo $wp_clear_body_backgroundcolor; ?>;
	background-image:none;
	<?php } ?>
	font-size: <?php echo $wp_clear_body_font_size; ?>;
	font-family: <?php echo $wp_clear_body_font_family; ?>;
	<?php if ( $wp_clear_body_font_color ) { ?>
	color: <?php echo $wp_clear_body_font_color; ?>;
	<?php } ?>
	}

<?php if ( $wp_clear_body_backgroundimage ) { ?>
body {
	background-image: url(<?php echo $wp_clear_body_backgroundimage; ?>);
	background-repeat: <?php echo $wp_clear_body_backgroundimage_repeat; ?>;
	background-position: <?php echo $wp_clear_body_backgroundimage_position; ?>;
	background-attachment: fixed;
	}
<?php } ?>

h1, h2, h3, h4, h5, h6, h7, .sitetitle {
	font-family: <?php echo $wp_clear_post_title_font; ?>;
	font-weight: <?php echo $wp_clear_post_title_weight; ?>;
	}

/* -------------------[ Site Title Adjustments ]------------------- */

#sitetitle h1, #sitetitle .title {
	font-size: <?php echo $wp_clear_site_title_size; ?>;
	color: <?php echo $wp_clear_site_title_color; ?>;
	text-align: <?php echo $wp_clear_site_title_alignment; ?>;
	font-weight: <?php echo $wp_clear_site_title_weight; ?>;
	font-family: <?php echo $wp_clear_site_title_font_family; ?>;
	}

#sitetitle .description { 
	color:<?php echo $wp_clear_site_title_color; ?>;
	text-align:<?php echo $wp_clear_site_title_alignment; ?>;
	}

<?php if ( $wp_clear_site_title_option == 'Basic Text-Type Title' ) { ?>
#head-content {
	background-image: none;
	}
<?php } ?>

<?php if ( $wp_clear_site_title_option == 'Image/Logo-Type Title' ) { ?>
#sitetitle {
	float:none;
	text-indent:-10000em;
	position:absolute;
	display:none;
	}
<?php } ?>

<?php if ( $wp_clear_ad468head == 'no' ) { ?>
#sitetitle {
	width:960px;
	}
<?php } ?>

<?php if ( $wp_clear_site_title_option == 'Image/Logo-Type Title' && $wp_clear_site_logo_url ) { ?>
#head-content {
	background: transparent;
	background-image: url(<?php echo $wp_clear_site_logo_url; ?>);
	background-position: <?php echo $wp_clear_site_logo_position; ?>;
	background-repeat: no-repeat;
	}
<?php } ?>

/* ----------[ Header Background Color Adjustments ]---------- */

<?php if ( $wp_clear_header_bg_color ) { ?>
#head-content {
	background-color: <?php echo $wp_clear_header_bg_color; ?>;
	}
<?php } ?>

/* ----------[ Left Sidebar Float Adjustments ]----------- */

<?php if ( $wp_clear_side_left_loc == 'Left of Content' ) { ?>
#content .col-3 { float:right; }
#sidebarleft { float:left; }
<?php } else { ?>
#content .col-3 { float:left; }
#sidebarleft { float:right; }
<?php } ?>

/* --------------[ Top Navigation Adjustments ]-------------- */

#topnav {
	<?php if ( $wp_clear_topnav_bg_color ) { ?>
	background: <?php echo $wp_clear_topnav_bg_color; ?>;
	<?php } ?>
	font-size: <?php echo $wp_clear_topnav_size; ?>; 
	font-weight: <?php echo $wp_clear_topnav_weight; ?>;	
	}

#topnav li a, #topnav li a:link, #topnav li a:visited {
	<?php if ( $wp_clear_topnav_link_color ) { ?>
	color: <?php echo $wp_clear_topnav_link_color; ?>;
	<?php } ?>
	}

#topnav li a:hover, #topnav li a:active  {
	<?php if ( $wp_clear_topnav_link_hover_color ) { ?>
	color: <?php echo $wp_clear_topnav_link_hover_color; ?>;
	<?php } ?>
	<?php if ( $wp_clear_topnav_link_hover_bg_color ) { ?>
	background: <?php echo $wp_clear_topnav_link_hover_bg_color; ?>;
	<?php } ?>
	}

<?php if ( $wp_clear_topnav_bg_color ) { ?>
#topnav li ul {
	background: <?php echo $wp_clear_topnav_bg_color; ?>;
	}
<?php } ?>

/* --------------[ Category Navigation Adjustments ]-------------- */

#nav {
	<?php if ( $wp_clear_catnav_bg_color ) { ?>
	background: <?php echo $wp_clear_catnav_bg_color; ?>;
	<?php } ?>
	font-size: <?php echo $wp_clear_catnav_size; ?>; 
	font-weight: <?php echo $wp_clear_catnav_weight; ?>;	
	}

#nav li a, #nav li a:link, #nav li a:visited {
	<?php if ( $wp_clear_catnav_link_color ) { ?>
	color: <?php echo $wp_clear_catnav_link_color; ?>;
	<?php } ?>
	}

#nav li a:hover, #nav li a:active  {
	<?php if ( $wp_clear_catnav_link_hover_color ) { ?>
	color: <?php echo $wp_clear_catnav_link_hover_color; ?>;
	<?php } ?>
	<?php if ( $wp_clear_catnav_link_hover_bg_color ) { ?>
	background: <?php echo $wp_clear_catnav_link_hover_bg_color; ?>;
	<?php } ?>
	}

<?php if ( $wp_clear_catnav_bg_color ) { ?>
#nav li ul {
	background: <?php echo $wp_clear_catnav_bg_color; ?>;
	}
<?php } ?>

/* --------------[ Features Adjustments ]-------------- */

#my-glider {
<?php if ( $wp_clear_featured_content_bg_color ) { ?>
	background-color: <?php echo $wp_clear_featured_content_bg_color; ?>;
	background-image: none;
	background-repeat: no-repeat;
	background-position: 0 0;
<?php } ?>
	color: <?php echo $wp_clear_featured_font_color; ?>;
	font-size: <?php echo $wp_clear_featured_size; ?>;
	}

<?php if ( $wp_clear_featured_controls_bg_color ) { ?>
#my-glider .controls ul {
	background-color: <?php echo $wp_clear_featured_controls_bg_color; ?>;
	}
<?php } ?>

<?php if ( $wp_clear_featured_link_color ) { ?>
#my-glider a, #my-glider a:link, #my-glider a:visited, #my-glider .controls li.feat-head {
	color: <?php echo $wp_clear_featured_link_color; ?>;
	}
<?php } ?>

<?php if ( $wp_clear_featured_link_color ) { ?>
#my-glider a:hover, #my-glider a:active, #my-glider .controls a.active {
	color:<?php echo $wp_clear_featured_link_hover_color; ?> !important;
	}
<?php } ?>

<?php if ( $wp_clear_featured_controls_border_color ) { ?>
#my-glider .controls li {
	border-color:<?php echo $wp_clear_featured_controls_border_color; ?>;
	}
<?php } ?>

/* --------------[ Main Content Adjustments ]-------------- */

.maincontent {
	font-size: <?php echo $wp_clear_content_size; ?>;
	}

.maincontent a, .maincontent a:link, .maincontent a:visited { 
	<?php if ( $wp_clear_content_link_color ) { ?>
	color: <?php echo $wp_clear_content_link_color; ?>;
	<?php } ?>
	}

.maincontent a:hover, .maincontent a:active, .post h1 a:active, .post h1 a:hover, .post h2 a:active, .post h2 a:hover { 
	<?php if ( $wp_clear_content_link_hover_color ) { ?>
	color: <?php echo $wp_clear_content_link_hover_color; ?>;
	<?php } ?>
	}

/* --------------[ Sidebar-Left Adjustments ]-------------- */

#sidebarleft {
	font-size: <?php echo $wp_clear_left_sidebar_size; ?>;
	}

#sidebarleft a, #sidebar-left a:link, #sidebar-left a:visited { 
	<?php if ( $wp_clear_left_sidebar_link_color ) { ?>
	color: <?php echo $wp_clear_left_sidebar_link_color; ?>;
	<?php } ?>
	}

#sidebarleft a:hover, #sidebar-left a:active { 
	<?php if ( $wp_clear_left_sidebar_link_hover_color ) { ?>
	color: <?php echo $wp_clear_left_sidebar_link_hover_color; ?>;
	<?php } ?>
	}

/* --------------[ Sidebar-Right Adjustments ]-------------- */

#sidebar {
	font-size: <?php echo $wp_clear_right_sidebar_size; ?>;
	}

<?php if ( $wp_clear_right_sidebar_link_color ) { ?>
#sidebar a, #sidebar a:link, #sidebar a:visited { 
	color: <?php echo $wp_clear_right_sidebar_link_color; ?>;
	}
	<?php } ?>

<?php if ( $wp_clear_right_sidebar_hover_link_color ) { ?>
#sidebar a:hover, #sidebar a:active { 
	color: <?php echo $wp_clear_right_sidebar_hover_link_color; ?>;
	}
<?php } ?>

/* --------------[ Footer Adjustments ]-------------- */

#footer {
	font-size:<?php echo $wp_clear_footer_font_size; ?>;
	color:<?php echo $wp_clear_footer_font_color; ?>;
	}

#footer a, #footer a:link, #footer a:visited { 
	<?php if ( $wp_clear_footer_link_color ) { ?>
	color: <?php echo $wp_clear_footer_link_color; ?>;
	<?php } ?>
	}

#footer a:hover, #footer a:active { 
	<?php if ( $wp_clear_footer_hover_link_color ) { ?>
	color: <?php echo $wp_clear_footer_hover_link_color; ?>;
	<?php } ?>
	}

<?php if ( $wp_clear_home_posts_stack == 'yes' ) { ?>
/* --------------[ Adjustments for Stacked Categories ]-------------- */

ul.home-left {
	width:628px;
	float:none;
	clear:both;
	}

ul.home-right {
	width:628px;
	float:none;
	clear:both;
	}
<?php } ?>