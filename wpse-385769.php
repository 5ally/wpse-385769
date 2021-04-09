<?php
/**
 * Plugin Name: WPSE 385769
 * Plugin URI: https://wordpress.stackexchange.com/a/386018
 * Description: Just testing a custom REST controller class for <a href="/wp-json/wp/v2/contact">/wp-json/wp/v2/contact</a> (creating a Contact CPT post <strong>without authentication</strong>). :)
 * Author: Sally CJ
 * Author URI: https://wordpress.stackexchange.com/users/137402/sally-cj
 * Version: 20210409.1
 */

define( 'WPSE_385769_VERSION', '20210409.1' );

add_action( 'init', 'wpse_385769_init' );
function wpse_385769_init() {
	// 1. Load the custom REST controller class for /wp-json/wp/v2/contact
	require_once __DIR__ . '/class-my-wp-rest-contact-controller.php';

	// 2. Then register the custom post type.
	register_post_type( 'contact', array(
		'supports'              => array( 'title' ),
		'has_archive'           => false,

		// Make this CPT available in the REST API.
		'show_in_rest'          => true,

		// Make sure the class name is correct.
		'rest_controller_class' => 'My_WP_REST_Contact_Controller',

		// Contact data are private, so you might want to make this CPT a
		// private one:
		'public'                => false,
		'publicly_queryable'    => true,
		'show_ui'               => true,
		'exclude_from_search'   => true,

		'labels'                => array(
			'name'         => 'Contact DB',
			'add_new_item' => 'Add New Contact',
			'edit_item'    => 'Edit Contact',
			'all_items'    => 'All Contacts',
		),
		'menu_icon'             => 'dashicons-format-chat',
	) );
}

// The following are used for testing the Contact CPT API endpoint.

add_action( 'admin_menu', 'wpse_385769_admin_menu' );
function wpse_385769_admin_menu() {
	$title = 'Contact CPT API Endpoint Tester';

	add_management_page( $title, $title, 'manage_options',
		'wpse-385769', 'wpse_385769_admin_page' );
}

function wpse_385769_admin_page() {
	$title = 'Contact CPT API Endpoint Tester';
	?>
		<div class="wrap">
			<h1><?php echo esc_html( $title ); ?></h1>

			<p>
				Click the button to create a Contact post <strong>without authentication</strong>:<br>
				(the JS script uses ESNext or modern JS, so be sure your browser is the latest one...)
			</p>
			<p>
				<button type="button" class="button" onclick="createContact( this, 'draft' )">Create Contact (status = draft)</button> or
				<button type="button" class="button" onclick="createContact( this, 'publish' )">Create Contact (status = publish)</button>
			</p>

			<div id="ajax-res"></div>
		</div>
	<?php
}

add_action( 'admin_enqueue_scripts', 'wpse_385769_admin_enqueue_scripts' );
function wpse_385769_admin_enqueue_scripts( $hook_suffix ) {
	if ( get_plugin_page_hook( 'wpse-385769', 'tools.php' ) === $hook_suffix ) {
		wp_enqueue_script( 'admin-create-contact', plugins_url( 'create-contact.js', __FILE__ ), array(), WPSE_385769_VERSION, true );
		wp_localize_script( 'admin-create-contact', 'wpse_385769', array(
			'apiRoot' => esc_url_raw( get_rest_url() ),
		) );
	}
}
