<?php

	
function hh_webshop_category_listing_function() {
	ob_start();
	global $wpdb;

	// SQL FOR HIRE CATEGORY
//	$data = $wpdb->get_results($wpdb->prepare("SELECT `WS_ID`, `HEADING`, `TYPE`, `LFT`, `RGT` FROM ".$wpdb->prefix ."hh_category WHERE `TYPE` = %d", 0));
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.SchemaChange
	$data = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT `WS_ID`, `HEADING`, `TYPE`, `LFT`, `RGT` FROM {$wpdb->prefix}hh_category WHERE `TYPE` = %d",
			0
		)
	);
	$hire_cat = json_decode(wp_json_encode($data), true);

	$json = '';
	if (!empty($data)) {
		//sort the categories
		usort($hire_cat, function ($a, $b) {
			return intval($a['LFT']) - intval($b['LFT']);
		});

		// Build a category tree
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.SchemaChange
		$result = $wpdb->get_results($wpdb->prepare("SELECT `ID`,`WS_ID`, `HEADING`, `LFT`, `RGT` FROM ".$wpdb->prefix ."hh_category where `TYPE` = %d order by `LFT`", 0));
		$results = json_decode(wp_json_encode($result), true);

		// The category in our results
		$current_cat = reset($hire_cat);
		// Flat array of $tree elements (last one is current parent)
		$flat_parents = array();
		// Loop through and build the tree
		foreach ($results as $row) {
			// Remove flat parents if item LFT > last heading RGT
			while (!empty($flat_parents) && $row['LFT'] > end($flat_parents)) {
				array_pop($flat_parents);
				$json .= ']}';
			}

			// Moved past so get next
			if($current_cat) {
				if ($current_cat['LFT'] < $row['LFT']) 
				{
					$current_cat = next($hire_cat);
				}
				// Is the cycled category a parent or the category
				if ($row['LFT'] <= $current_cat['LFT'] && $row['RGT'] >= $current_cat['RGT']) {
					// Do we need a comma
					if ($json != '' && substr($json, -1) != '[')
						$json .= ',';
					// Save the level
					$row['LEVEL'] = count($flat_parents);
					// Add the json row and omit the last }
					$json .= mb_substr(wp_json_encode($row), 0, -1) . ',"children":[';
					// Add RGT to parents
					$flat_parents[] = $row['RGT'];
				}
			}
		}
	}
	// Close any remaining rows
	while (!empty($flat_parents)) {
		array_pop($flat_parents);
		$json .= ']}';
	}
	// Encapsulate as an array
	$hire = '[' . $json . ']';
	$hh_hire = json_decode($hire, true);

	// SQL FOR Consumables/ sales CATEGORY
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.SchemaChange
	$data = $wpdb->get_results($wpdb->prepare("SELECT `WS_ID`,`HEADING`, `TYPE`,`LFT`,`RGT` FROM ".$wpdb->prefix ."hh_category WHERE `TYPE` = %d", 1));
	$cons_cat = json_decode(wp_json_encode($data), true);

	$json = '';
	if (!empty($data)) {
		//sort the categories
		usort($cons_cat, function ($a, $b) {
			return intval($a['LFT']) - intval($b['LFT']);
		});

		// Build a category tree
		// Query to fetch products from your database table
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.SchemaChange
		$result = $wpdb->get_results($wpdb->prepare("SELECT `ID`,`WS_ID`, `HEADING`, `LFT`, `RGT` FROM ".$wpdb->prefix ."hh_category where `TYPE` = %d order by `LFT`", 1));
		$results = json_decode(wp_json_encode($result), true);

		// The category in our results
		$current_cat = reset($cons_cat);
		// Flat array of $tree elements (last one is current parent)
		$flat_parents = array();
		// Loop through and build the tree
		foreach ($results as $row) {
			// Remove flat parents if item LFT > last heading RGT
			while (!empty($flat_parents) && $row['LFT'] > end($flat_parents)) {
				array_pop($flat_parents);
				$json .= ']}';
			}

			// Moved past so get next
			if ($current_cat) {
				if ($current_cat['LFT'] < $row['LFT']) {

					$current_cat = next($cons_cat);
				}
				// Is the cycled category a parent or the category
				if ($row['LFT'] <= $current_cat['LFT'] && $row['RGT'] >= $current_cat['RGT']) {
					// Do we need a comma
					if ($json != '' && substr($json, -1) != '[')
						$json .= ',';
					// Save the level
					$row['LEVEL'] = count($flat_parents);
					// Add the json row and omit the last }
					$json .= mb_substr(wp_json_encode($row), 0, -1) . ',"children":[';
					// Add RGT to parents
					$flat_parents[] = $row['RGT'];
				}
			}
		}
	}
	// Close any remaining rows
	while (!empty($flat_parents)) {
		array_pop($flat_parents);
		$json .= ']}';
	}
	// Encapsulate as an array
	$cons = '[' . $json . ']';
	$hh_cons = json_decode($cons, true);

	// SQL FOR Labour / Service CATEGORY
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.SchemaChange
	$data = $wpdb->get_results($wpdb->prepare("SELECT `WS_ID`,`HEADING`, `TYPE`,`LFT`,`RGT` FROM ".$wpdb->prefix ."hh_category WHERE `TYPE` = %d", 2));
	$service_cat = json_decode(wp_json_encode($data), true);

	$json = '';
	if (!empty($data)) {
		//sort the categories
		usort($service_cat, function ($a, $b) {
			return intval($a['LFT']) - intval($b['LFT']);
		});

		// Build a category tree
		// Query to fetch products from your database table
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.SchemaChange
		$result = $wpdb->get_results($wpdb->prepare("SELECT `ID`,`WS_ID`, `HEADING`, `LFT`, `RGT` FROM ".$wpdb->prefix ."hh_category where `TYPE` = %d order by `LFT`", 2));
		$results = json_decode(wp_json_encode($result), true);

		// The category in our results
		$current_cat = reset($service_cat);
		// Flat array of $tree elements (last one is current parent)
		$flat_parents = array();
		// Loop through and build the tree
		foreach ($results as $row) {
			// Remove flat parents if item LFT > last heading RGT
			while (!empty($flat_parents) && $row['LFT'] > end($flat_parents)) {
				array_pop($flat_parents);
				$json .= ']}';
			}

			// Moved past so get next
			if ($current_cat) {
				if ($current_cat['LFT'] < $row['LFT']) {

					$current_cat = next($service_cat);
				}
				// Is the cycled category a parent or the category
				if ($row['LFT'] <= $current_cat['LFT'] && $row['RGT'] >= $current_cat['RGT']) {
					// Do we need a comma
					if ($json != '' && substr($json, -1) != '[')
						$json .= ',';
					// Save the level
					$row['LEVEL'] = count($flat_parents);
					// Add the json row and omit the last }
					$json .= mb_substr(wp_json_encode($row), 0, -1) . ',"children":[';
					// Add RGT to parents
					$flat_parents[] = $row['RGT'];
				}
			}
		}
	}
	// Close any remaining rows
	while (!empty($flat_parents)) {
		array_pop($flat_parents);
		$json .= ']}';
	}
	// Encapsulate as an array
	$service = '[' . $json . ']';
	$hh_service = json_decode($service, true);
	// Output the product listing
	
	// phpcs:disable WordPress.Security.NonceVerification.Recommended
	$nonce = isset($_GET['_wpnonce']) ? sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ) : wp_create_nonce('my_nonce_action');

	// phpcs:enable WordPress.Security.NonceVerification.Recommended
	
	$url_type = !isset($_GET['type'])? 0 : absint($_GET['type']);
	
	?>
<div class="clear"></div>
	<div class="main clear">
		<div class="col-">
			<nav class="navbar navbar-expand-lg bg-light navbar-light py-3 py-lg-0 px-0 ">
				<h6 class="m-0 " style=" font-size: 16px; margin-top:0; margin-bottom:0"><b>Categories</b></h6>
					<ul class="hh_tree" id="myUL" style="list-style:none; padding-left:0;font-size:14px;">
<?php
	if (!empty($hire_cat)) 
	{
		
		

		if (wp_verify_nonce($nonce, 'my_nonce_action')) {
			$cls = $url_type === 0 ? "caret-down bold-1" : '';
			$act = $url_type === 0 ? "active" : '';
		} else {
			// Nonce verification failed, handle accordingly (e.g., show an error message).
			$cls = '';
			$act = '';
		}

		
?>
						<li><span class="caret <?php echo esc_attr($cls); ?>"><a href="javascript: change_category(0,0)">Hire</a></span></li>
							<ul class="nested <?php echo esc_attr($act); ?> " style="margin-left: 1.5em;">
<?php

function nav_draw_hire_cats_nodes($nodes) 
{
    $txt = '';
    foreach ($nodes as $row) {
        $row_ws_id = isset($row['WS_ID']) ? esc_attr($row['WS_ID']) : '';
        $row_heading = isset($row['HEADING']) ? esc_html($row['HEADING']) : '';

		$nonce = wp_create_nonce('my_nonce_action');
		 $url = esc_url(add_query_arg(array('cat' => absint($row_ws_id), 'type' => 0, 'nonce' => $nonce), get_permalink()));

        if (!empty($row['children'])) {
            $child_nodes = $row['children'];
			if (wp_verify_nonce($nonce, 'my_nonce_action')) {
				if (isset($_GET['cat']) && ($row_ws_id == $_GET['cat'] || in_array($_GET['cat'], array_column($child_nodes, 'WS_ID')))) {
					$txt .= '<li><span class="caret caret-down"><a href="' . $url . '"><b>' . $row_heading . '</b></a></span>' .
						'<span style="display:none;" class="cat_comma">,</span>';
				} else {
					$txt .= '<li><span class="caret"><a href="' . $url . '">' . $row_heading . '</a></span>' .
						'<span style="display:none;" class="cat_comma">,</span>';
				}
			}
            $txt .= '<ul class="nested ' . ((isset($_GET['cat']) && ($row_ws_id == $_GET['cat'] || in_array($_GET['cat'], array_column($child_nodes, 'WS_ID')))) ? 'active' : '') . '">' . nav_draw_hire_cats_nodes($child_nodes) . '</ul>';
        } else {
			if (wp_verify_nonce($nonce, 'my_nonce_action')) {
				if (isset($_GET['cat']) && ($row_ws_id == $_GET['cat'] || in_array($_GET['cat'], array_column($row['children'], 'WS_ID')))) {
					$txt .= '<li><span class="caret"><a href="' . $url . '"><b>' . $row_heading . '</b></a></span>' .
						'<span style="display:none;" class="cat_comma">,</span>';
				} else {
					$txt .= '<li><span class="caret"><a href="' . $url . '">' . $row_heading . '</a></span>' .
						'<span style="display:none;" class="cat_comma">,</span>';
				}
			}
        }
        $txt .= '</li>';
    }
    return $txt;
}

// Escape the final output with custom rules
echo wp_kses_post(nav_draw_hire_cats_nodes($hh_hire));


?>	
							</ul>
						</li>
						<li>
							<hr>
						</li>
<?php } 
if(!empty($hh_cons)) 
{
		if (wp_verify_nonce($nonce, 'my_nonce_action')) {
			$cls = $url_type === 1 ? "caret-down bold-1" : '';
			$act = $url_type === 1 ? "active" : '';
		} else {
			// Nonce verification failed, handle accordingly (e.g., show an error message).
			$cls = '';
			$act = '';
		}

	?>
						<li>
							<span class="caret <?php echo esc_attr($cls); ?>"><a href="javascript: change_category(0,1)">Sales</a></span>
							<ul class="nested <?php echo esc_attr($act); ?> ">
<?php
// print main categories and sub category in tree view
function nav_draw_sales_cats_nodes($nodes) {
    $txt = '';
    foreach ($nodes as $row) {
        $row_ws_id = isset($row['WS_ID']) ? esc_attr($row['WS_ID']) : '';
        $row_heading = isset($row['HEADING']) ? esc_html($row['HEADING']) : '';
		
		$nonce = wp_create_nonce('my_nonce_action');
		$url = esc_url(add_query_arg(array('cat' => absint($row_ws_id), 'type' => 0, 'nonce' => $nonce), get_permalink()));
		
        if (empty($row['children'])) {
			if (wp_verify_nonce($nonce, 'my_nonce_action')) {
				if (isset($_GET['cat']) && ($row_ws_id == $_GET['cat'] || in_array($_GET['cat'], array_column($row['children'], 'WS_ID')) )) {
					// Echo the row
					$txt .= '<li><span class="caret"><a href="' . $url . '"><b>' . esc_html($row_heading) . '</b></a></span>' .
						'<span style="display:none;" class="cat_comma">,</span></li>'; // Invisible comma for itemprop to separate keywords
				} else {
					// Echo the row
					$txt .= '<li><span class="caret"><a href="' . $url . '">' . esc_html($row_heading) . '</a></span>' .
						'<span style="display:none;" class="cat_comma">,</span></li>'; // Invisible comma for itemprop to separate keywords
				}
			}
        } else {
			if (wp_verify_nonce($nonce, 'my_nonce_action')) {
				if (isset($_GET['cat']) && $row_ws_id == $_GET['cat'] || in_array($_GET['cat'], array_column($row['children'], 'WS_ID'))) {
					$txt .= '<li><span class="caret "><a href="' . $url . '">' . esc_html($row_heading) . '</a></span>' .
						'<span style="display:none;" class="cat_comma">,</span>';
					$txt .= '<ul class="nested ">' . nav_draw_sales_cats_nodes($row['children']) . '</ul>';
				} else {
					$txt .= '<li><span class="caret"><a href="' . $url . '">' . esc_html($row_heading) . '</a></span>' .
						'<span style="display:none;" class="cat_comma" >,</span>';
					$txt .= '<ul class="nested ">' . nav_draw_sales_cats_nodes($row['children']) . '</ul>';
				}
			}
            $txt .= '</li>';
        }
    }
    return $txt;
}

echo wp_kses_post(nav_draw_sales_cats_nodes($hh_cons));

?>	
							</ul>
						</li>
						<li>
							<hr>
						</li>
<?php 
} 
if(!empty($hh_service)) 
{ 

		if (wp_verify_nonce($nonce, 'my_nonce_action')) {
			$cls = $url_type === 2 ? "caret-down bold-1" : '';
			$act = $url_type === 2 ? "active" : '';
		} else {
			// Nonce verification failed, handle accordingly (e.g., show an error message).
			$cls = '';
			$act = '';
		}
	?>
						<li>
							<span class="caret <?php echo esc_attr($cls); ?>"><a href="javascript: change_category(0,2)">Service</a></span>
							<ul class="nested <?php echo esc_attr($act); ?>">
								<?php

								// print main categories and sub category in tree view
								function nav_draw_service_cats_nodes($nodes) {
    $txt = '';
    foreach ($nodes as $row) {
        $row_ws_id = isset($row['WS_ID']) ? esc_attr($row['WS_ID']) : '';
        $row_heading = isset($row['HEADING']) ? esc_html($row['HEADING']) : '';
		
		$nonce = wp_create_nonce('my_nonce_action');
		$url = esc_url(add_query_arg(array('cat' => absint($row_ws_id), 'type' => 0, 'nonce' => $nonce), get_permalink()));
		
        if (empty($row['children'])) {
			if (wp_verify_nonce($nonce, 'my_nonce_action')) {
				if (isset($_GET['cat']) && ($row_ws_id == $_GET['cat'] || in_array($_GET['cat'], array_column($row['children'], 'WS_ID')) )) {
					// Echo the row
					$txt .= '<li><span class="caret"><a href="' . $url . '"><b>' . esc_html($row_heading) . '</b></a></span>' .
						'<span style="display:none;" class="cat_comma" >,</span></li>'; // Invisible comma for itemprop to separate keywords
				} else {
					// Echo the row
					$txt .= '<li><span class="caret"><a href="' . $url . '">' . esc_html($row_heading) . '</a></span>' .
						'<span style="display:none;" class="cat_comma">,</span></li>'; // Invisible comma for itemprop to separate keywords
				}
			}
        } else {
			if (wp_verify_nonce($nonce, 'my_nonce_action')) {
				if (isset($_GET['cat']) && $row_ws_id == $_GET['cat'] || in_array($_GET['cat'], array_column($row['children'], 'WS_ID'))) {
					$txt .= '<li><span class="caret caret-down"><a href="' . $url . '">' . esc_html($row_heading) . '</a></span>' .
						'<span style="display:none;" class="cat_comma">,</span>';
					$txt .= '<ul class="nested active">' . nav_draw_service_cats_nodes($row['children']) . '</ul>';
				} else {
					$txt .= '<li><span class="caret"><a href="' . $url . '">' . esc_html($row_heading) . '</a></span>' .
						'<span style="display:none;" class="cat_comma">,</span>';
					$txt .= '<ul class="nested ">' . nav_draw_service_cats_nodes($row['children']) . '</ul>';
				}
			}
            $txt .= '</li>';
        }
    }
    return $txt;
}

echo wp_kses_post(nav_draw_service_cats_nodes($hh_service));

								
								?>	
							</ul>
						</li>
						<?php 
						} 

						?>
					</ul>
				</nav>
		</div>
		</div>
<?php
	return ob_get_clean();
}
						