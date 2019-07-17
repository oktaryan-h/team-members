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

	function create_movie_review() {

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

	function wp_team_members_metabox( $meta_boxes ) {

		$prefix = 'mb-';

		$meta_boxes[] = array(
			'id' => 'untitled',
			'title' => esc_html__( 'Untitled Metabox', 'metabox-online-generator' ),
			'post_types' => array('team_members'),
			'context' => 'advanced',
			'priority' => 'default',
			'autosave' => 'false',
			'fields' => array(
				array(
					'id' => $prefix . 'position',
					'name' => esc_html__( 'Position', 'metabox-online-generator' ),
					'type' => 'select',
					'placeholder' => esc_html__( 'Select an Item', 'metabox-online-generator' ),
					'options' => array(
						'owner' => 'Owner',
						'manager' => 'Manager',
						'design' => 'Design Dept.',
					),
				),
				array(
					'id' => $prefix . 'email',
					'name' => esc_html__( 'Email', 'metabox-online-generator' ),
					'type' => 'email',
				),
				array(
					'id' => $prefix . 'phone',
					'type' => 'text',
					'name' => esc_html__( 'Phone', 'metabox-online-generator' ),
				),
				array(
					'id' => $prefix . 'website',
					'type' => 'url',
					'name' => esc_html__( 'Website', 'metabox-online-generator' ),
				),
				array(
					'id' => $prefix . 'image',
					'type' => 'image_select',
					'name' => esc_html__( 'Image Select', 'metabox-online-generator' ),
					'force_delete' => 'false',
					'max_file_uploads' => '4',
				),
			),
		);

		return $meta_boxes;
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
		$a = get_post_meta($post->ID);
		//var_dump($a);
		?>
		<input name="mb-position" type="text" value="<?php echo $a['mb-position'][0] ?>">
		<input name="mb-email" type="text" value="<?php echo $a['mb-email'][0] ?>">
		<input name="mb-phone" type="text" value="<?php echo $a['mb-phone'][0] ?>">
		<?php
	}

	function save_metabox($post_id) {
    //if (array_key_exists('wporg_field', $_POST)) {
        if (isset($_POST['mb-position'])) update_post_meta($post_id,'mb-position',$_POST['mb-position']);
        if (isset($_POST['mb-email'])) update_post_meta($post_id,'mb-email',$_POST['mb-email']);
        if (isset($_POST['mb-phone'])) update_post_meta($post_id,'mb-phone',$_POST['mb-phone']);
    }

	function show_team_members($attr) {

		$a = shortcode_atts(array(
			'email' => 'true',
			'phone' => 'true',
			'website' => 'true'),$attr);

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

				//var_dump(get_post_meta(get_the_ID(),'mb-email',true));

				echo '<li>'.get_post_meta(get_the_ID(),'mb-image',true).'</li>';
				echo '<li>'.get_the_title().'</li>';
				echo '<li><strong>'.get_post_meta(get_the_ID(),'mb-position',true).'</strong></li>';
				if ($a['email'] != 'false') echo '<li>'.get_post_meta(get_the_ID(),'mb-email',true).'</li>';
				if ($a['phone'] != 'false') echo '<li>'.get_post_meta(get_the_ID(),'mb-phone',true).'</li>';
				if ($a['website'] != 'false') echo '<li>'.get_post_meta(get_the_ID(),'mb-website',true).'</li>';

			}

			echo '</ul>';

		} else {
    // no posts found
		}
		/* Restore original Post Data */
		wp_reset_postdata();

		//var_dump($query);

		return ob_get_clean();

	}
}

$team_members = new Team_members;

add_action('init', array($team_members,'create_movie_review'));

add_action('add_meta_boxes', array($team_members,'native_metabox'));
add_action('save_post', array($team_members,'save_metabox'));

add_filter('rwmb_meta_boxes', array($team_members,'wp_team_members_metabox'));
add_shortcode('team-members', array($team_members, 'show_team_members'));

//add_action('init', [new Team_members, 'create_movie_review']);