<?php
class My_WP_REST_Contact_Controller extends WP_REST_Posts_Controller {
	public function create_item_permissions_check( $request ) {
		if ( ! empty( $request['id'] ) ) {
			return new WP_Error(
				'rest_post_exists',
				__( 'Cannot create existing post.' ),
				array( 'status' => 400 )
			);
		}

		return true;
	}

	public function handle_status_param( $post_status, $post_type ) {
		if ( in_array( $post_status, array( 'draft', 'publish' ) ) ) {
			return $post_status;
		}

		return parent::handle_status_param( $post_status, $post_type );
	}
}
