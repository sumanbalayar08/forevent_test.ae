<?php



/**
 * @package  HireHop
 */
/*
Plugin Name: HireHop webshop
Plugin URI: https://hirehop.com/plugin
Description: This is HireHop rental software webshop plugin.
Version: 1.0.0
Author: hirehop
Author URI: https://hirehop.com
License: GPLv2 or later
Text Domain: hirehop_webshop
*/

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.

Copyright 2005-2015 Automattic, Inc.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Activation and deactivation hooks
register_activation_hook(__FILE__, 'hh_webshop_api_data_plugin_activate');
register_deactivation_hook(__FILE__, 'hh_webshop_api_data_plugin_deactivate');

// Include the file containing activation function
require_once(plugin_dir_path(__FILE__) . 'functions.php');

function hh_webshop_plugin_textdomain() {
    load_plugin_textdomain( 'HireHop_webshop', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}
add_action( 'init', 'hh_webshop_plugin_textdomain' );

// Enqueue styles and scripts
function hh_webshop_assets_enqueue() 
{
    // Enqueue CSS file for the front page
    if (!is_admin()) {
        wp_enqueue_style('custom-style', plugin_dir_url(__FILE__) . 'style/style.css', array(), '1.0.0');
        wp_enqueue_style('custom1-style', plugin_dir_url(__FILE__) . 'style/jquery_ui.css', array(), '1.0.0');

        // Enqueue jQuery from WordPress core
        wp_enqueue_script('jquery');

        // Enqueue JS files with jQuery dependency
        wp_enqueue_script('jquery-ui-dialog');
        wp_enqueue_script('custom-script', plugin_dir_url(__FILE__) . 'js/webshop.js', array('jquery'), '1.0.0', true);
        wp_enqueue_script('custom-cart-script', plugin_dir_url(__FILE__) . 'js/webshop_cart.js', array('jquery', 'jquery-ui-dialog'), '1.0.0', true);
        wp_enqueue_script('nav-script', plugin_dir_url(__FILE__) . 'js/main.js', array('jquery', 'jquery-ui-dialog'), '1.0.0', true);
    }
}

// Hook into WordPress
add_action('wp_enqueue_scripts', 'hh_webshop_assets_enqueue');

// Add the menu item for the settings page
add_action('admin_menu', 'hh_webshop_api_data_plugin_menu');

function hh_webshop_api_data_plugin_menu() 
{
	add_menu_page('HireHop webshop', 'HireHop', 'manage_options', 'api-data-plugin-settings', 'hh_webshop_api_data_plugin_settings_page', 'dashicons-heading');
}

function hh_webshop_api_data_plugin_settings_page() 
{
	global $wpdb;
	$hh_api = $wpdb->prefix . 'hh_api';
	
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.SchemaChange
	$api_credentials = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix ."hh_api");
	$id = $api_credentials->ID;
	$key = $api_credentials->API_KEY;
	$url = $api_credentials->API_URL;
	
	// phpcs:disable WordPress.Security.NonceVerification.Recommended
	$nonce = isset($_GET['_wpnonce']) ? sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ) : wp_create_nonce('my_nonce_action');
	// phpcs:enable WordPress.Security.NonceVerification.Recommended
	
	if (wp_verify_nonce($nonce, 'my_nonce_action')) {
		if (isset($_POST['api_data_submit']) && $id < 1) 
		{
			if ( isset( $_POST['key'] ) ) {
				$key = sanitize_text_field( wp_unslash( $_POST['key'] ) );
			}

			if ( isset( $_POST['api_url'] ) ) {
				$api_url = esc_url_raw( wp_unslash( $_POST['api_url'] ) );
			}


			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.SchemaChange
			$wpdb->insert($hh_api, array(
				'API_KEY' => $key,
				'API_URL' => $api_url,
			));

			echo 'Settings saved.';
		} 
		else if (isset($_POST['api_data_submit']) && $id > 0) 
		{
			if ( isset( $_POST['key'] ) ) {
				$key = sanitize_text_field( wp_unslash( $_POST['key'] ) );
			}

			if ( isset( $_POST['api_url'] ) ) {
				$api_url = esc_url_raw( wp_unslash( $_POST['api_url'] ) );
			}

			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.SchemaChange
			$wpdb->update($hh_api, array(
				'API_KEY' => $key,
				'API_URL' => $api_url,
				),
				array(
					'ID' => $id
				)
			);

			echo 'Settings Updated.';
		}
	}

	?>
		<form action="" method="post">
			<div class="wrap">
				<h2>HireHop webshop Settings</h2>

					<input type="text" name="api_url" value="https://hirehop.rent/wp_data.php" style="width: 75%; display:none" >

					<label for="key">Interface key:</label>
					<input type="text" name="key" value="<?php echo isset($key) ? esc_html($key) : "" ?>" style="width: 510px;" ><br><br>

					<input type="submit" name="api_data_submit" class="button-primary" value="Save Settings">
			</div>
		</form>
	<?php
	
	if(isset($key))
	{
		echo '<div class="wrap">';
		echo '<h2>Retrieve HireHop Data</h2>';
		echo '<p><strong>Click the button below to retrieve data from the HireHop:</strong></p>';
		echo '<form method="post" action="">';
		echo '<input type="submit" name="retrieve_data_submit" class="button-primary" value="Retrieve Data">';
		echo '</form>';
		echo '</div>';

		if (isset($_POST['retrieve_data_submit'])) {
			hh_webshop_retrieve_and_store_api_data();
		}
	}
}


function hh_webshop_retrieve_and_store_api_data() 
{
	global $wpdb;
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.SchemaChange
	$api_credentials = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix ."hh_api");
	
	if($api_credentials) 
	{
		$key = $api_credentials->API_KEY;
		$api_url = $api_credentials->API_URL;
		
		$response = wp_remote_get($api_url . '?key=' . urlencode($key));
		
		if (is_wp_error($response)) {
			$error = 'Error: Does not fetch data. Please contact HireHop ';
			echo esc_html($error);
		} else {
			$body = wp_remote_retrieve_body($response);
			$json = json_decode($body, true);

			
			if(empty($json['error']))
			{
				$product = $json["product"];
				$cat = $json["cat"];
				$webshop_setting = $json['HireHop_settings'];

				$wpdb->query('TRUNCATE TABLE '.$wpdb->prefix.'hh_items');
				$wpdb->query('TRUNCATE TABLE '.$wpdb->prefix.'hh_company');
				$wpdb->query('TRUNCATE TABLE '.$wpdb->prefix.'hh_category');
				
				$new_data = array();
				
				// Strings
				if(isset($webshop_setting['DATE_FORMAT'])) $new_data['DATE_FORMAT'] = absint($webshop_setting['DATE_FORMAT']);
				if(isset($webshop_setting['LENGTH_FORMAT'])) $new_data['LENGTH_FORMAT'] = absint($webshop_setting['LENGTH_FORMAT']);
				if(isset($webshop_setting['WEIGHT_FORMAT'])) $new_data['WEIGHT_FORMAT'] = absint($webshop_setting['WEIGHT_FORMAT']);
				if(isset($webshop_setting['THEME'])) $new_data['THEME'] =  wp_json_encode($webshop_setting['THEME']);
				if(isset($webshop_setting['REGION'])) $new_data['REGION'] = trim($webshop_setting['REGION']);
				if(isset($webshop_setting['HIREHOP_ID'])) $new_data['HIREHOP_ID'] = absint($webshop_setting['HIREHOP_ID']);
				if(isset($webshop_setting['COMPANY'])) $new_data['COMPANY'] = trim($webshop_setting['COMPANY']);
				if(isset($webshop_setting['CURRENCY'])) $new_data['CURRENCY'] = wp_json_encode($webshop_setting['CURRENCY']);
				if(isset($webshop_setting['LOGO'])) $new_data['LOGO'] = absint($webshop_setting['LOGO']);
				if(isset($webshop_setting['DEPOT'])) $new_data['DEPOT'] = intval($webshop_setting['DEPOT']);
				if(isset($webshop_setting['EMAIL'])) $new_data['EMAIL'] = absint($webshop_setting['EMAIL']);
				if(isset($webshop_setting['TOKEN'])) $new_data['TOKEN'] = trim($webshop_setting['TOKEN']);
				if(isset($webshop_setting['PRICE_VIEW'])) $new_data['PRICE_VIEW'] = absint($webshop_setting['PRICE_VIEW']);
				if(isset($webshop_setting['EXTRA_FIELDS'])) $new_data['EXTRA_FIELDS'] = wp_json_encode($webshop_setting['EXTRA_FIELDS']);
				
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.SchemaChange
				$wpdb->insert($wpdb->prefix .'hh_company', $new_data);
				
				// Assuming $api_data contains the retrieved data, you can now store it in the custom table
				foreach ($product as $field => $value) 
				{
					if (absint($value['IMAGE_ID']) > 0) 
					{
						// Create image link
						$image_url = 'https://hirehop.info/uploads/name_of_item/' . absint($new_data['HIREHOP_ID']) . '_' . absint($value['IMAGE_ID']) . '.png';

						// Get the filename from the URL
						$image_name = basename($image_url);

						// Custom SQL query to find existing attachment
						// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.SchemaChange
						$existing_attachment_id = $wpdb->get_row($wpdb->prepare("
							SELECT post_id 
							FROM ".$wpdb->postmeta."
							WHERE meta_key = '_wp_attached_file' 
							AND meta_value LIKE %s
							LIMIT 1", 
							'%' . $wpdb->esc_like($image_name) . '%'
						));
						
						if(empty($existing_attachments))
						{
							// Download the image
							$response = wp_remote_get($image_url);
							
							if(!is_wp_error($response)) 
							{
								// Get the image content
								$image_content = wp_remote_retrieve_body($response);
								
								//check the file content
								if(!empty($image_content)) 
								{
									// Generate a unique filename to avoid overwriting
									$upload_dir = wp_upload_dir();
									$unique_file_name = wp_unique_filename($upload_dir['path'], $image_name);
									$file_path = $upload_dir['path'] . '/' . $unique_file_name;
									
									if (!function_exists('request_filesystem_credentials')) {
										require_once ABSPATH . 'wp-admin/includes/file.php';
									}

									global $wp_filesystem;

									if (empty($wp_filesystem)) {
										// Initialize the WP_Filesystem object
										$creds = request_filesystem_credentials(site_url() . '/wp-admin/', '', false, false, null);
										if (!WP_Filesystem($creds)) {
											return; // Failed to initialize WP_Filesystem
										}
									}
									
									// Save the image file to the uploads directory
									if($wp_filesystem->put_contents($file_path, $image_content, FS_CHMOD_FILE)) 
									{
										// Prepare an array of post data for the attachment
										$file_type = wp_check_filetype($file_path, null);
										$attachment_data = array(
											'post_mime_type' => $file_type['type'],
											'post_title'     => sanitize_file_name($unique_file_name),
											'post_content'   => '',
											'post_status'    => 'inherit'
										);

										// Insert the attachment into the Media Library
										$attachment_id = wp_insert_attachment($attachment_data, $file_path);
										
										if(!is_wp_error($attachment_id)) 
										{	
											// Generate the metadata for the attachment
											require_once(ABSPATH . 'wp-admin/includes/image.php');
											$attachment_metadata = wp_generate_attachment_metadata($attachment_id, $file_path);
											wp_update_attachment_metadata($attachment_id, $attachment_metadata);

											// Get the URL of the stored file
											$value['IMAGE_URL'] = wp_get_attachment_url($attachment_id) === null ? $exist_img_url : wp_get_attachment_url($attachment_id);
										}
										else 
										{
											//error_log('Failed to insert attachment into the Media Library - ' . $file_path . ': ' . $attachment_id->get_error_message());
										}
									}
									else
									{
										//error_log('Failed to write image to disk - ' . $file_path);
										$value['IMAGE_URL'] = '';
									}
									
								}
								else 
								{
									//error_log('Error: Image content empty for URL - ' . $image_url);
									$value['IMAGE_URL'] = '';
								}
								
							}
							else
							{
								//error_log('Error downloading image from - ' . $image_url . ': ' . $response->get_error_message());
								$value['IMAGE_URL'] = '';
							}
							
						}
						else 
						{
							$value['IMAGE_URL'] = wp_get_attachment_url($existing_attachments[0]->ID);
							//error_log('Image already exists in the Media Library: ' . $image_url);
						}
					}
					// Insert data into table
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.SchemaChange
					$wpdb->insert($wpdb->prefix.'hh_items', $value);
				}

				foreach ($cat as $field => $value) {
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.SchemaChange
					$wpdb->insert($wpdb->prefix.'hh_category', $value);
				}
				$msg ='API data retrieved and stored.';
				echo esc_html($msg);
			}
			else
			{
				echo esc_html($json['error']);
			}
		}
	} else {
		$msg ='API credentials not found.';
		echo esc_html($msg);
	}
}


	//	Include the file containing Product listing function
	require_once(plugin_dir_path(__FILE__) . 'product-list.php');
	//	Register the shortcode - product
	add_shortcode('hh_webshop_product_listing', 'hh_webshop_product_listing_function');

	// Include the file containing Category listing function
	require_once(plugin_dir_path(__FILE__) . 'category_list.php');
	// Register the shortcode - category
	add_shortcode('hh_webshop_category', 'hh_webshop_category_listing_function');


// Add the dialog container to the footer
function hh_webshop_cart_add_dialog_container() 
{
    echo '<div id="custom-cart-dialog"></div>';
}
add_action('wp_footer', 'hh_webshop_cart_add_dialog_container');

// Add cart button to the header
function hh_webshop_cart_add_to_header() 
{
    ?>
		<div id="custom-cart-menu-item">
			<button id="custom-cart-button">
				Cart - 0
			</button>
		</div>
    <?php
}
add_shortcode('hh_webshop_cart_btn', 'hh_webshop_cart_add_to_header');

function hh_webshop_checkout() 
{
	ob_start();
	global $wpdb;
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.SchemaChange
	$api_credentials = $wpdb->get_row("select API_KEY from ".$wpdb->prefix ."hh_api ");
	$key = $api_credentials->API_KEY;
	
	?>
	   <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>"  method="post">
		   <input type="hidden" name="action" value="handle_custom_form">
		   <div id="checkout_container">
			   <div><b>Checkout Details</b></div>
			   <hr class="mb-1">
		   </div>
		   <div>
			   <input type="submit" name="checkout_submit">
		   </div>
	   </form>

	<?php
		
	return ob_get_clean();
}
add_shortcode('hh_webshop_checkout', 'hh_webshop_checkout');

//	include(plugin_dir_path(__FILE__) . 'checkout.php');


// Form submission handler
add_action('admin_post_handle_custom_form', 'hh_webshop_handle_custom_form');
add_action('admin_post_nopriv_handle_custom_form', 'hh_webshop_handle_custom_form');

function hh_webshop_handle_custom_form() 
{
	global $wpdb;
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.SchemaChange
	$api_credentials = $wpdb->get_row("select API_KEY from ".$wpdb->prefix ."hh_api ");
	$key = $api_credentials->API_KEY;
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.SchemaChange
	$email_credentials = $wpdb->get_row("select HIREHOP_ID, COMPANY,PRICE_VIEW, EXTRA_FIELDS AS email from ".$wpdb->prefix ."hh_company where ID = 1");
	$email = json_decode($email_credentials->email,true);
	if(!empty($email['email']))
    {
      $row = $email['email'] ;
    }
  	else
    {
      $row = [];
    }

	$price_view = $email_credentials->PRICE_VIEW;
	$company = $email_credentials->COMPANY;
	$hh_id = $email_credentials->HIREHOP_ID;
	
	// phpcs:disable WordPress.Security.NonceVerification.Recommended
	$nonce = isset($_GET['_wpnonce']) ? sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ) : wp_create_nonce('my_nonce_action');
	// phpcs:enable WordPress.Security.NonceVerification.Recommended
   
	if (wp_verify_nonce($nonce, 'my_nonce_action')) {
		if(isset($_POST))
		{
			$tk = $key;
			$data  = array();
			$item = new stdClass();
			$cnt = isset($_POST['cnt']) ? sanitize_text_field( wp_unslash( $_POST['cnt'] ) ) : '';
			for($i=0;$i<$cnt; $i++)
			{
				// Check if the superglobals are set before using them
				if ( isset( $_POST['hms_' . ($i + 1)] ) && isset( $_POST['qty_' . ($i + 1)] ) ) {
					// Unslash and sanitize the inputs
					$pid = sanitize_text_field( wp_unslash( $_POST['hms_' . ($i + 1)] ) );
					$qty = intval( wp_unslash( $_POST['qty_' . ($i + 1)] ) );

					// Assign the sanitized values
					$item->$pid = $qty;
				}
			}
		}
	}
	$data['items'] = wp_json_encode($item);
	$data['job'] = 0;
	// Check and sanitize each input
	if (isset($_POST['name'])) {
		$data['name'] = sanitize_text_field( wp_unslash( $_POST['name'] ) );
	}

	if (isset($_POST['company'])) {
		$data['company'] = sanitize_text_field( wp_unslash( $_POST['company'] ) );
	}

	if (isset($_POST['address']) && isset($_POST['postcode'])) {
		$address = sanitize_text_field( wp_unslash( $_POST['address'] ) );
		$postcode = sanitize_text_field( wp_unslash( $_POST['postcode'] ) );
		$data['address'] = $address . ', ' . $postcode;
		$data['venue_address'] = $address . ', ' . $postcode;
	}

	if (isset($_POST['email'])) {
		$data['email'] = sanitize_email( wp_unslash( $_POST['email'] ) );
	}

	if (isset($_POST['mobile'])) {
		$data['mobile'] = sanitize_text_field( wp_unslash( $_POST['mobile'] ) );
	}

	if (isset($_POST['start_date'])) {
		$data['start'] = sanitize_text_field( wp_unslash( $_POST['start_date'] ) );
		$data['out'] = sanitize_text_field( wp_unslash( $_POST['start_date'] ) );
	}

	if (isset($_POST['end_date'])) {
		$data['end'] = sanitize_text_field( wp_unslash( $_POST['end_date'] ) );
		$data['to'] = sanitize_text_field( wp_unslash( $_POST['end_date'] ) );
	}
	$data['user'] = '';
	$data['depot'] = 2;
	$data['key'] = $tk;
	$data_a = wp_json_encode($data);	

    // Prepare data to be sent
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

    if (is_wp_error($response)) 
	{
        // Handle error
        $error_message = $response->get_error_message();
        // Redirect or display error message
    } 
	else 
	{
		if(!empty($row))
		{
			$body = $response['body'];

			// Separate the JSON objects by finding the first occurrence of '}{' and split the string there
			$separated_json = explode('}{', $body);
			$separated_json[0] = $separated_json[0] . '}';

			$hh_response = json_decode($separated_json[0], true);
			$name = $data['email'];
			$email = $data['email'];
			$message = 'this is test mail';

			// Prepare data to be sent
			$to = $email; // Replace with recipient email address
			$subject = 'Your webshop enquiry - (' . $hh_response['ID'] . ')';
			// Mail body content  
			$message_content = '<div style="max-width: 600px; margin: 0 auto; padding: 20px;">
								
								 <h2>Enquiry Confirmation</h2>

								<p>Dear ' . sanitize_text_field( wp_unslash( $_POST['name'] ) ) . ',</p>

								<p>Thank you for your enquiry. A member of team will contact you shortly.</p>
								<table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
									<tr>
										<td style="border: 0; padding: 8px; text-align: left; width:130px;"><b>Enquiry no:</b></td>
										<td style="border: 0; padding: 8px; text-align: left;">' . $hh_response['ID'] . '</td>
									</tr>
									<tr>
										<td style="border: 0; padding: 8px; text-align: left;"><b>Enquiry placed:</b></td>
										<td style="border: 0; padding: 8px; text-align: left;">' . gmdate("d-m-Y") . '</td>
									</tr>
									<tr>
										<td style="border: 0; padding: 8px; text-align: left;"><b>Venue address:</b></td>
										<td style="border: 0; padding: 8px; text-align: left;">' . sanitize_text_field( wp_unslash( $_POST['address'] ) ).',<br>'. sanitize_text_field( wp_unslash ( $_POST['postcode'] ) ) . '</td>
									</tr>
									<tr>
										<td style="border: 0; padding: 8px; text-align: left;"><b>Contact no:</b></td>
										<td style="border: 0; padding: 8px; text-align: left;">' . sanitize_text_field( wp_unslash ( $_POST['mobile'] ) ) . '</td>
									</tr>
									<tr>
										<td style="border: 0; padding: 8px; text-align: left;"><b>Start date:</b></td>
										<td style="border: 0; padding: 8px; text-align: left;">' .sanitize_text_field( wp_unslash ( $_POST['start_date'] ) ) . '</td>
									</tr>
									<tr>
										<td style="border: 0; padding: 8px; text-align: left;"><b>End date:</b></td>
										<td style="border: 0; padding: 8px; text-align: left;">' . sanitize_text_field( wp_unslash ( $_POST['end_date'] ) ) . '</td>
									</tr>
								</table>

								<table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
								  <thead>
									<tr>
									  <th style="border: 1px solid #ddd; padding: 8px; text-align: center;">Product</th>
									  <th style="border: 1px solid #ddd; padding: 8px; text-align: center;">Price</th>
									  <th style="border: 1px solid #ddd; padding: 8px; text-align: center;">Quantity</th>
									</tr>
								  </thead>
								  <tbody>';
			$cnt = isset($_POST['cnt']) ? sanitize_text_field( wp_unslash( $_POST['cnt'] ) ) : '';
			for ($i = 0; $i < absint($cnt); $i++) {

				$prc = '';
				if (isset($_POST['prc_' . ($i + 1)])) {
					$prc = absint($price_view) === 1 ? sanitize_text_field(wp_unslash($_POST['prc_' . ($i + 1)])) : '';
				}

				$message_content .= '<tr>
					<td style="border: 1px solid #ddd; padding: 8px; text-align: left;">' .
					(isset($_POST['name_' . ($i + 1)]) ? sanitize_text_field(wp_unslash($_POST['name_' . ($i + 1)])) : '') . '</td>
					<td style="border: 1px solid #ddd; padding: 8px; text-align: left;">' . $prc . '</td>
					<td style="border: 1px solid #ddd; padding: 8px; text-align: left;">' .
					(isset($_POST['qty_' . ($i + 1)]) ? sanitize_text_field(wp_unslash($_POST['qty_' . ($i + 1)])) : '') . '</td>
				</tr>';

			}
			$message_content .= '</tbody>
							</table>
							<p style="margin-top: 20px;">Thank you, '.sanitize_text_field($company).'</p>
							</div>';


			$headers = array('Content-Type: text/html; charset=UTF-8');

			// Set up SMTP configuration
			$smtp_host = $row['SMTP_HOST'];
			$smtp_port = $row['SMTP_PORT']; // Adjust the port as needed
			$smtp_username = $row['SMTP_USERNAME'];
			$smtp_password = $row['SMTP_PASSWORD'];

			// Enable SMTP sending
			add_action('phpmailer_init', function($phpmailer) use ($smtp_host, $smtp_port, $smtp_username, $smtp_password) {
				$phpmailer->isSMTP();
				$phpmailer->Host = $smtp_host;
				$phpmailer->Port = $smtp_port;
				$phpmailer->SMTPAuth = true;
				$phpmailer->Username = $smtp_username;
				$phpmailer->Password = $smtp_password;
				$phpmailer->SMTPSecure = 'ssl';
				// Add any additional SMTP configuration here, such as SMTPSecure, SMTPAutoTLS, etc.
			});

			// Send email using wp_mail function
			$mail_sent = wp_mail($to, $subject, $message_content, $headers);

			if($mail_sent) 
			{

				// Redirect to home page
				wp_redirect(home_url());
				exit;
			} 
			else 
			{
				// Redirect to home page
				wp_redirect(home_url());
				exit;
			}
		}
		else
		{
			wp_redirect(home_url());
			exit;
		}
	}
}

function hh_webshop_checkout_plugin_activate() {
    // Create a new page
    $page_title = 'Checkout';
    $page_content = '[hh_webshop_checkout]'; // Content generated by the shortcode
    $page_template = ''; // Optional: You can specify a page template if needed
    $page_id = wp_insert_post(array(
        'post_title'    => $page_title,
        'post_content'  => $page_content,
        'post_type'     => 'page',
        'post_status'   => 'publish',
        'page_template' => $page_template,
    ));
    if ($page_id) {
        // Page created successfully
        update_option('hh_webshop_checkout_page_id', $page_id); // Save the page ID for future use if needed
    } else {
        // Error creating page
        // Handle error as needed
    }
}
register_activation_hook(__FILE__, 'hh_webshop_checkout_plugin_activate');

function hh_webshop_checkout_plugin_deactivate() {
    // Deactivation tasks, if any
    $page_id = get_option('hh_webshop_checkout_page_id');
    if ($page_id) {
        // Delete the page created during activation
        wp_delete_post($page_id, true); // Set second parameter to true to force delete
        delete_option('hh_webshop_checkout_page_id');
    }
}
register_deactivation_hook(__FILE__, 'hh_webshop_checkout_plugin_deactivate');

function hh_webshop_product_plugin_activate() {
    // Create a new page for products
    $page_title = 'Product';
    $page_content = '<div class="webshop_main_div"><div class="webshop_left_div" style="padding:20px;"> [hh_webshop_category]</div>';
    $page_content .= '<div class="webshop_right_div">[hh_webshop_product_listing]</div></div><div class="clear"></div>';
    $page_template = ''; // Optional: You can specify a page template if needed
    $page_id = wp_insert_post(array(
        'post_title'    => $page_title,
        'post_content'  => $page_content,
        'post_type'     => 'page',
        'post_status'   => 'publish',
        'page_template' => $page_template,
    ));
    if ($page_id) {
        // Page created successfully
        update_option('hh_webshop_product_page_id', $page_id); // Save the page ID for future use if needed
    } else {
        // Error creating page
        // Handle error as needed
    }
}
register_activation_hook(__FILE__, 'hh_webshop_product_plugin_activate');


function hh_webshop_product_plugin_deactivate() {
    // Deactivation tasks, if any
    $page_id = get_option('hh_webshop_product_page_id');
    if ($page_id) {
        // Delete the page created during activation
        wp_delete_post($page_id, true); // Set second parameter to true to force delete
        delete_option('hh_webshop_product_page_id');
    }
}
register_deactivation_hook(__FILE__, 'hh_webshop_product_plugin_deactivate');

