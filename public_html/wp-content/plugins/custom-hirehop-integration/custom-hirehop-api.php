<?php
/*
Plugin Name: Custom HireHop API Integration
Description: Custom REST API endpoints for interaction between Hirehop and React APP.
Version: 1.0
Author: Suman Balayar
*/

// Add this to your plugin's main file or a separate api.php file
add_action('rest_api_init', function () {
    // Get all products
    register_rest_route('hirehop/v1', '/products', array(
        'methods' => 'GET',
        'callback' => 'hh_get_products',
        'permission_callback' => '__return_true'
    ));

    // Get product categories
    register_rest_route('hirehop/v1', '/categories', array(
        'methods' => 'GET',
        'callback' => 'hh_get_categories',
        'permission_callback' => '__return_true'
    ));

    // Submit checkout/quote request
    register_rest_route('hirehop/v1', '/submit-quote', array(
        'methods' => 'POST',
        'callback' => 'hh_submit_quote',
        'permission_callback' => '__return_true'
    ));

    // Get company settings (needed for frontend configuration)
    register_rest_route('hirehop/v1', '/settings', array(
        'methods' => 'GET',
        'callback' => 'hh_get_settings',
        'permission_callback' => '__return_true'
    ));
});

function hh_get_products(WP_REST_Request $request) {
    global $wpdb;
    
    // Get category filter if provided
    $category = $request->get_param('category');
    
    $query = "SELECT * FROM {$wpdb->prefix}hh_items";
    if ($category) {
        $query .= $wpdb->prepare(" WHERE CATEGORY = %s", $category);
    }
    
    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
    $products = $wpdb->get_results($query);
    
    return new WP_REST_Response($products, 200);
}

function hh_get_categories(WP_REST_Request $request) {
    global $wpdb;
    
    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
    $categories = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}hh_category");
    
    return new WP_REST_Response($categories, 200);
}

function hh_get_settings(WP_REST_Request $request) {
    global $wpdb;
    
    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
    $settings = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}hh_company WHERE ID = 1");
    
    // Get API credentials if needed
    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
    $api_credentials = $wpdb->get_row("SELECT API_KEY FROM {$wpdb->prefix}hh_api");
    
    $response_data = array(
        'company_settings' => $settings,
        'api_key' => $api_credentials->API_KEY
    );
    
    return new WP_REST_Response($response_data, 200);
}

function hh_submit_quote(WP_REST_Request $request) {

    global $wpdb;

    // Retrieve the API key from the database
    $api_credentials = $wpdb->get_row("SELECT API_KEY FROM {$wpdb->prefix}hh_api");
    $key = $api_credentials->API_KEY;

    // Extract data from the request
    $params = $request->get_params();
    $nonce = isset($params['_wpnonce']) ? sanitize_text_field($params['_wpnonce']) : wp_create_nonce('my_nonce_action');

    if (!wp_verify_nonce($nonce, 'my_nonce_action')) {
        return new WP_REST_Response(['error' => 'Invalid nonce'], 403);
    }

    // Prepare data based on the request payload
    $data = array();
    $items = new stdClass();
    $cnt = isset($params['cnt']) ? intval($params['cnt']) : 0;

    for ($i = 0; $i < $cnt; $i++) {
        $pid_key = 'prdt_' . ($i + 1);
        $qty_key = 'qty_' . ($i + 1);

        if (isset($params[$pid_key], $params[$qty_key])) {
            $pid = sanitize_text_field($params[$pid_key]);
            $items->$pid = intval($params[$qty_key]);
        }
    }

    $data['items'] = wp_json_encode($items);
    $data['job'] = 0;

    // Capture additional user fields
    $fields = ['name', 'company', 'address', 'telephone', 'email', 'mobile', 'start_date', 'end_date'];
    foreach ($fields as $field) {
        if (isset($params[$field])) {
            $data[$field] = sanitize_text_field($params[$field]);
        }
    }

    // Set start and end dates
    if (isset($params['start_date'])) {
        $data['start'] = sanitize_text_field($params['start_date']);
        $data['out'] = sanitize_text_field($params['start_date']);
    }
    if (isset($params['end_date'])) {
        $data['end'] = sanitize_text_field($params['end_date']);
        $data['to'] = sanitize_text_field($params['end_date']);
    }

    // Additional data setup
    $data['user'] = '';
    $data['depot'] = 2;
    $data['key'] = $key;
    $data_a = wp_json_encode($data);

    // Set up the external API request
    $request_url = 'https://hirehop.rent/wp_save_job.php?key=' . urlencode($key);
    $request_args = array(
        'headers'     => array('Content-Type' => 'application/json'),
        'body'        => $data_a,
        'method'      => 'POST',
        'data_format' => 'body',
    );

    $response = wp_remote_post($request_url, $request_args);

    // Handle the external API response
    if (is_wp_error($response)) {
        return new WP_REST_Response(['error' => $response->get_error_message()], 500);
    } else {
        $body = wp_remote_retrieve_body($response);
        $http_code = wp_remote_retrieve_response_code($response);
        $code = wp_remote_retrieve_response_message($response);

        $result = is_string($body) ? json_decode($body, true) : $body;
        if ($http_code != 200) {
            return new WP_REST_Response(['error' => 'API Error ' . $http_code . ' ' . $code], 500);
        }

        if (empty($result)) {
            return new WP_REST_Response(['success' => true, 'message' => 'Empty response from external API'], 200);
        }

        return new WP_REST_Response($result, 200);
    }
}