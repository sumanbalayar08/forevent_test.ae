s<?php
// error_reporting(E_ERROR);  // Show all errors
// 	ini_set('display_errors', true);
// 	ini_set('display_startup_errors', true);
	
// phpcs:disable WordPress.Security.NonceVerification.Recommended
$nonce = isset($_GET['_wpnonce']) ? sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ) : wp_create_nonce('my_nonce_action');
// phpcs:enable WordPress.Security.NonceVerification.Recommended
if (wp_verify_nonce($nonce, 'my_nonce_action')) {
	if(isset($_POST))
	{
		$tk = isset($_POST['key']) ? sanitize_text_field( wp_unslash( $_POST['key'] ) ) : '';
		$data  = array();
		$item = new stdClass();
		$cnt = isset($_POST['cnt']) ? sanitize_text_field( wp_unslash( $_POST['cnt'] ) ) : '';
		for($i=0;$i<$cnt; $i++)
		{
			$pid_key = 'prdt_' . ($i + 1);
			$qty_key = 'qty_' . ($i + 1);

			// Check if the keys exist in the $_POST array
			if (isset($_POST[$pid_key], $_POST[$qty_key])) {
				// Unslash and sanitize the inputs
				$pid = sanitize_text_field( wp_unslash( $_POST[$pid_key] ) );
				$item->$pid = intval( wp_unslash( $_POST[$qty_key] ) );
			}

		}
	}
}
	$data['items'] = wp_json_encode($item);
	$data['job'] = 0;
	if ( isset( $_POST['name'] ) ) {
		$data['name'] = sanitize_text_field( wp_unslash( $_POST['name'] ) );
	}

	if ( isset( $_POST['company'] ) ) {
		$data['company'] = sanitize_text_field( wp_unslash( $_POST['company'] ) );
	}

	if ( isset( $_POST['address'] ) ) {
		$data['address'] = sanitize_text_field( wp_unslash( $_POST['address'] ) );
	}

	if ( isset( $_POST['telephone'] ) ) {
		$data['telephone'] = sanitize_text_field( wp_unslash( $_POST['telephone'] ) );
	}

	if ( isset( $_POST['email'] ) ) {
		$data['email'] = sanitize_email( wp_unslash( $_POST['email'] ) );
	}

	if ( isset( $_POST['mobile'] ) ) {
		$data['mobile'] = sanitize_text_field( wp_unslash( $_POST['mobile'] ) );
	}

	if ( isset( $_POST['start_date'] ) ) {
		$data['start'] = sanitize_text_field( wp_unslash( $_POST['start_date'] ) );
		$data['out'] = sanitize_text_field( wp_unslash( $_POST['start_date'] ) );
	}

	if ( isset( $_POST['end_date'] ) ) {
		$data['end'] = sanitize_text_field( wp_unslash( $_POST['end_date'] ) );
		$data['to'] = sanitize_text_field( wp_unslash( $_POST['end_date'] ) );
	}

	$data['user'] = '';
	$data['depot'] = 2;
	$data['key'] = $tk;
	$data_a = wp_json_encode($data);

	
	$request_url = 'https://hirehop.rent/wp_save_job.php?key=' . urlencode($tk);
	$request_args = array(
		'headers'     => array(
			'Content-Type' => 'application/json',
		),
		'body'        => $data_a,
		'method'      => 'POST',
		'data_format' => 'body',
	);

	$response = wp_remote_post($request_url, $request_args);

	if (is_wp_error($response)) {
		$error_message = $response->get_error_message();
		echo esc_html("Something went wrong: $error_message");
	} else {
		$body = wp_remote_retrieve_body($response);
		$http_code = wp_remote_retrieve_response_code($response);
		$code = wp_remote_retrieve_response_message($response);

		// Make sure $result is an array
		$response = is_string($body) ? json_decode($body, true) : $body;

		if ($http_code != 200) {
			$response = array('error' => $error_message . ' ' . $http_code, $code);
		}

		// if response is empty
		if (empty($response)) {
			$response = array('success' => true, $code);
		}
	}

   if(empty($resonse['error']))
	{
		sendmail();
	}
	
	
?>
