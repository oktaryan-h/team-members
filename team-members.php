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
			'post_types' => array('post', 'page' ),
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

	function show_team_members() {

		?>

		<ul>
			<li><strong><?php echo rwmb_meta('position') ?></strong></li>
			<li><?php echo rwmb_meta('email') ?></li>
			<li><?php echo rwmb_meta('phone') ?></li>
			<li><?php echo rwmb_meta('website') ?></li>
			<li><?php echo rwmb_meta('image') ?></li>
		</ul>

		<?php

	}
}

$team_members = new Team_members;
add_action('init', array($team_members,'create_movie_review'));
add_filter('rwmb_meta_boxes', array($team_members,'wp_team_members_metabox'));
add_shortcode('team-members', array($team_members, 'show_team_members'));

//add_action('init', [new Team_members, 'create_movie_review']);