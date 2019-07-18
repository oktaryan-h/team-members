<?php

/*
Plugin Name: Team Members
Plugin URI: https://oktaryan.com/wptm
Description: Declares a plugin that will create a custom post type displaying team members.
Version: 1.0
Author: Oktaryan Nh
Author URI: https://oktaryan.com/
License: GPLv2
*/

class Team_members {

	function create_team_member() {

		register_post_type( 'team_members',
			array(
				'labels' => array(
					'name' => 'Team Members',
					'singular_name' => 'Team Member',
					'add_new' => 'Add New',
					'add_new_item' => 'Add New Team Member',
					'edit' => 'Edit',
					'edit_item' => 'Edit Team Member',
					'new_item' => 'New Team Member',
					'view' => 'View',
					'view_item' => 'View Team Member',
					'search_items' => 'Search Team Member',
					'not_found' => 'No Team Member found',
					'not_found_in_trash' => 'No Team Member found in Trash',
					'parent' => 'Parent Movie Review'
				),

				'public' => true,
				'menu_position' => 15,
				'supports' => array( 'title', 'editor', 'comments', 'thumbnail', 'custom-fields' ),
				'taxonomies' => array( '' ),
				'menu_icon' => 'dashicons-universal-access', //plugins_url( 'images/image.png', __FILE__ ),
				'has_archive' => true
			)
		);
	}

	function native_metabox() {
		add_meta_box(
            'wporg_box_id',           // Unique ID
            'Custom Meta Box Title',  // Box title
            array($this,'native_metabox_html'),  // Content callback, must be of type callable
            'team_members'                   // Post type
        );
	}

	function native_metabox_html($post) {

		$a = $post->ID;

		?>
		<p>
			Position : 
			<input name="mb-position" type="text" value="<?php echo get_post_meta( $a, 'mb-position', true ) ?>">
		</p>
		<p>
			Email :
			<input name="mb-email" type="text" value="<?php echo get_post_meta( $a, 'mb-email', true ) ?>">
		</p>
		<p>
			Phone :
			<input name="mb-phone" type="text" value="<?php echo get_post_meta( $a, 'mb-phone', true ) ?>">
		</p>
		<p> Website :
			<input name="mb-website" type="text" value="<?php echo get_post_meta( $a, 'mb-website', true ) ?>">
		</p>
		<p> Profile Picture :
			<input id="mb-image" name="mb-image" type="file">
		</p>
		<?php
	}

	function save_metabox($post_id) {

		if (isset($_POST['mb-position'])) {
			update_post_meta($post_id,'mb-position',$_POST['mb-position']);
		}
		if (isset($_POST['mb-email'])) {
			update_post_meta($post_id,'mb-email',$_POST['mb-email']);
		}
		if (isset($_POST['mb-phone'])) {
			update_post_meta($post_id,'mb-phone',$_POST['mb-phone']);
		}

		if ( isset ( $_FILES['mb-image'] ) ) {
			$uploaded = media_handle_upload( 'mb-image', $post_id );
			if ( is_wp_error( $uploaded ) ) {
				echo 'Error uploading file: ' . $uploaded->get_error_message();
			} else {
				update_post_meta( $post_id, 'mb-image', $uploaded );
				echo 'File upload successful!';
			}
		}
		//return ob_get_clean();
	}

	function show_team_members($attr) {

		$a = shortcode_atts( array(
			'email' => 'true',
			'phone' => 'true',
			'website' => 'true',
			'image' => 'true',
		),
		$attr );

		$args = array(
			'post_type'         => 'team_members',   /* the names of you custom post types */
			'posts_per_page'    => -1                       /* get all posts */
		);

		$query = new WP_Query($args);

		ob_start();

		if ( $query->have_posts() ) {

			echo '<ul>';

			while ( $query->have_posts() ) {
				$query->the_post();

				echo '<li>'.get_post_meta(get_the_ID(),'mb-image',true).'</li>';
				echo '<li>'.get_the_title().'</li>';
				echo '<li><strong>'.get_post_meta(get_the_ID(),'mb-position',true).'</strong></li>';

				if (isset($a['email']) && $a['email'] != 'false') {
					echo '<li>'.get_post_meta(get_the_ID(),'mb-email',true).'</li>' ;
				}
				if (isset($a['phone']) && $a['phone'] != 'false') {
					echo '<li>'.get_post_meta(get_the_ID(),'mb-phone',true).'</li>';
				}
				if (isset($a['website']) && $a['website'] != 'false') {
					echo '<li>'.get_post_meta(get_the_ID(),'mb-website',true).'</li>' ;
				}
				if (isset($a['image']) && $a['image'] != 'false') {
					$image_attachment_id = get_post_meta( get_the_ID(), 'mb-image', true);
					echo '<li><img src="' . wp_get_attachment_url( $image_attachment_id ) . '"></li>' ;
				}
			}

			echo '</ul>';

		}
		/* Restore original Post Data */
		wp_reset_postdata();

		return ob_get_clean();

	}
}

$team_members = new Team_members;

add_action('init', array($team_members,'create_team_member'));

add_action('add_meta_boxes', array($team_members,'native_metabox'));
add_action('save_post_team_members', array($team_members,'save_metabox'));

add_action(
	'post_edit_form_tag',
	function() {
		echo ' enctype="multipart/form-data"';
	}
);

//add_filter('rwmb_meta_boxes', array($team_members,'wp_team_members_metabox'));
add_shortcode('team-members', array($team_members, 'show_team_members'));