<?php
/**
 * Plugin Name: WPSE 385769
 * Plugin URI: https://wordpress.stackexchange.com/a/386018
 * Description: Just testing a custom REST controller class for <a href="/wp-json/wp/v2/contact">/wp-json/wp/v2/contact</a> (creating a Contact CPT post <strong>without authentication</strong>). :)
 * Author: Sally CJ
 * Author URI: https://wordpress.stackexchange.com/users/137402/sally-cj
 * Version: 20210408.1
 */

add_action( 'init', 'wpse_385769_init' );
function wpse_385769_init() {
	// 1. Load the custom REST controller class for /wp-json/wp/v2/contact
	require_once __DIR__ . '/class-my-wp-rest-contact-controller.php';

	// 2. Then register the custom post type.
	register_post_type( 'contact', array(
		'supports'              => array( 'title' ),
		'has_archive'           => false,
		'show_in_rest'          => true,
		// Make sure the class name is correct.
		'rest_controller_class' => 'My_WP_REST_Contact_Controller',
		'public'                => true,
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
	$hook = add_management_page( $title, $title, 'manage_options',
		sanitize_title( $title ), 'wpse_385769_admin_page' );
}

function wpse_385769_admin_page() {
	$title = 'Contact CPT API Endpoint Tester';
	$api_url = rest_url( 'wp/v2/contact' );
	?>
		<div class="wrap">
			<h1><?php echo esc_html( $title ); ?></h1>
			<p style="max-width: 50%">
				Click the button to create a Contact CPT <strong>without authentication</strong>:
				(the JS uses ESNext or modern JS, so be sure your browser is the latest one...)
				<button type="button" class="button" onclick="createContact( this )">Create Post</button>
			</p>
			<div id="ajax-res"></div>
		</div>

		<!-- Just for testing, so I put this script here. -->
		<script>
			function createContact( btn ) {
				const _btnText = btn.text;

				btn.disabled = true;
				btn.text = 'Loading...';

				fetch( '<?php echo esc_url( $api_url ); ?>', {
					method: 'POST',
					body: JSON.stringify( {
						title: `test via JS fetch() at ${ ( new Date() ).toLocaleString() } :)`,
					} ),
					headers: { 'Content-Type': 'application/json' },
				} )
					.then( res => res.json() )
					.then( post => {
						const res = document.querySelector( '#ajax-res' );

						if ( post.id ) {
							const link = `<a href="${ post.link }" target="_blank">${ post.id }</a>`;
							res.innerHTML = `Success! ID: ${ link }; Title: <i>${ post.title.rendered }</i>`;
						} else if ( post.code ) {
							res.innerHTML = `Failed! :( Message: <i>${ post.message } (${ post.code })</i>`;
						} else {
							res.innerHTML = 'Unknown error - check the console log. Try again later.';
						}
						console.log( post );

						btn.text = _btnText;
						btn.disabled = false;
					} );
			}
		</script>
	<?php
}