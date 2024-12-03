<?php

//error_reporting(E_ALL);
//ini_set('display_errors', 1);

function hh_webshop_money($value=0, $symbols=false, $currency=null)
{
	// Get the prefix & suffix
	$prefix = $currency['SYMBOL_POSITION']==0 ? $currency['SYMBOL'] : '';
	$suffix = $currency['SYMBOL_POSITION']==1 ? $currency['SYMBOL'] : '';
	// Make sure decimals is correct range
	$currency['DECIMALS'] = min(max(absint($currency['DECIMALS']),0),3);
	
	// Make sure multiplier is correct
	$currency['MULTIPLIER'] = isset($currency['MULTIPLIER']) ? floatval($currency['MULTIPLIER']) : 1;
	// Multiply number by rate and then round it
	$value = round( floatval($value * $currency['MULTIPLIER']), $currency['DECIMALS'] );
	// Do we need this formatted
	if($symbols)
	{
		// Get positive value and convert number to string with fixed decimals
		$out_val = number_format(abs($value), $currency['DECIMALS'], $currency['DECIMAL_SEPARATOR'], $currency['THOUSAND_SEPARATOR']);
		// Negative value & $currency
		$neg = $value<0;
		switch(absint($currency['NEGATIVE_FORMAT'])) {
			case 1: // -£0.00
				$out_val = ($neg?'-':'').$prefix.$out_val.$suffix;
				break;
			case 2: // £-0.00
				$out_val = $prefix.($neg?'-':'').$out_val.$suffix;
				break;
			case 3: // £0.00-
				$out_val = $prefix.$out_val.($neg?'-':'').$suffix;
				break;
			default: //0= (£0.00)
				$out_val = $prefix.$out_val.$suffix;
				if($neg) $out_val = '('.$out_val.')';
				break;
		}
	}
	else // No symbols, just money as standard float
		$out_val = $value;
	// Output formatted number
	return $out_val;
}

function hh_webshop_product_listing_function() 
{
	ob_start();
	global $wpdb;
	
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.SchemaChange
	$api_credentials = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."hh_api");
	
	// phpcs:disable WordPress.Security.NonceVerification.Recommended
	$nonce = isset($_GET['_wpnonce']) ? sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ) : wp_create_nonce('my_nonce_action');

	// phpcs:enable WordPress.Security.NonceVerification.Recommended
	
	if(!empty($api_credentials))
	{
		$id = $api_credentials->id;
		$key = $api_credentials->api_key;
		setcookie('token', $key, 0, "/");
	}

	// Category and item type
	if (wp_verify_nonce($nonce, 'my_nonce_action')) {
		$cat = isset($_GET['cat']) ? absint($_GET['cat']) : '';
		$type = isset($_GET['type']) ? absint($_GET['type']) : '';
	}
	else 
	{
		$cat = '';
		$type ='';
	}
	$cred = array();
	$cred[] = $cat;
	$cred[] = $type;
	$cat_heading ='';
	
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.SchemaChange
	$values = $wpdb->get_results( 
		$wpdb->prepare( 
			"SELECT `a`.`HEADING`, `b`.`WS_ID` 
			FROM `{$wpdb->prefix}hh_category` AS `a`, `{$wpdb->prefix}hh_category` AS `b` 
			WHERE `a`.`WS_ID` = %d 
			AND `b`.`LFT` BETWEEN `a`.`LFT` AND `a`.`RGT` 
			AND `a`.`TYPE` = %d",
			$cat,
			$type
		)
	);

	$cat_heading  = esc_html($values[0]->HEADING);
	foreach ( $values as $value ) {
	   $cats[] = absint($value->WS_ID);
	}
	// $current_page = max(1, isset($_GET['page']) ? absint($_GET['page']) : 1);
	$current_page = max(1, get_query_var('paged'));
	$items_per_page = isset($_GET['items_per_page']) ? absint($_GET['items_per_page']) : 20;
	// Calculate the offset for the query
	$offset = ($current_page - 1) * $items_per_page;

	// Initialize $where and $params
	$where = '';
	

	// Construct the WHERE clause with placeholders for parameters
	if ($type !== '') {
		if ($cats === NULL) 
		{	
			// Prepare the SQL statement
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.SchemaChange
			$results = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$wpdb->prefix.'hh_items WHERE ITEM_TYPE = %d LIMIT %d OFFSET %d', $type,$items_per_page,$offset));
			
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.SchemaChange
			$total_count = $wpdb->get_var($wpdb->prepare('SELECT count(*) as cnt FROM '.$wpdb->prefix.'hh_items WHERE ITEM_TYPE = %d ', $type));
		} 
		else 
		{
			// Assuming $cats is an array
			$cats = array_map('esc_sql', $cats); // Escape each item in the array

			// Create a string with placeholders for each category
			$placeholders = implode(',', array_fill(0, count($cats), '%s'));

			// Combine all parameters into a flat array
			$params = array_merge($cats, [$type, $items_per_page, $offset]);
			$params_count = array_merge($cats, [$type]);
			
			// Execute the prepared statement
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.SchemaChange
			$results = $wpdb->get_row($wpdb->prepare( "SELECT * FROM ".$wpdb->prefix."hh_items WHERE CATAGORY IN (%s) AND ITEM_TYPE = %d LIMIT %d OFFSET %d",$placeholders, $params_count,$items_per_page,$offset));
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.SchemaChange
			$total_count = $wpdb->get_row($wpdb->prepare( "SELECT count(*) as cnt FROM ".$wpdb->prefix."hh_items WHERE CATAGORY IN (%s) AND ITEM_TYPE = %d ", $placeholders,$params_count));
			
		}
	} 
	else 
	{
		$params = array();
		$params[] = $items_per_page;
		$params[] = $offset;

		// Execute the prepared statement
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.SchemaChange
		$results = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$wpdb->prefix.'hh_items  LIMIT %d OFFSET %d', $items_per_page,$offset));
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.SchemaChange
		$total_count = $wpdb->get_row('SELECT count(*) as cnt FROM '.$wpdb->prefix.'hh_items');
	}
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.SchemaChange
	$company_results = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."hh_company");
	$company_id = $company_results[0]->HIREHOP_ID;
	$currency = $company_results[0]->CURRENCY;
	$pp = absint($company_results[0]->PRICE_VIEW);
	$cur = json_decode($currency,true);


	if (!empty($results)) {
		echo '<div class="clear"></div>';
		echo '<div class="main" style="margin-bottom:5em;">';
		echo '<div class="col-s-9 col-9">';
		if($cat !== '' && $type !== '')
		{
			if($cat !=0)
			{
			if($type == 0)
				echo '<h4>Hire -> '.esc_html($cat_heading).'</h4>';
			elseif($type == 1)
				echo '<h4>Sales -> '.esc_html($cat_heading).'</h4>';
			elseif($type ==2)
				echo '<h4>Service -> '.esc_html($cat_heading).'</h4>';
			}
			else
			{
				if($type == 0)
				echo '<h4>Hire -> All</h4>';
			elseif($type == 1)
				echo '<h4>Sales -> All</h4>';
			elseif($type ==2)
				echo '<h4>Service -> All</h4>';
			}
		}
		else
		{
			echo '<h4>All</h4>';
		}
		echo '</div>';
		echo '<div class="col-s-3 col-3" style="text-align:right;"><button id="custom-cart-button" onclick=" webshop_cart('.absint($company_id).')">
			Cart - 0
		</button></div></div>';
		echo "<hr>";
		echo '<div class="main">';
		echo '<div class="col-s-12 col-12">';

		$priceduration = array("One off price", "per Hour", "per Day", "per Week", "per Month", "Every day", "Every week", "Proportioned week");
		foreach ($results as $product) {
			if ($product->IMAGE_ID !="0") {
				$src = $product->IMAGE_URL;
			} else {
				$src = "";
			}
			?>
			<div class="col-3">
				<div class="col-12 col-s-12">
					<div class="col-12 col-s-12 h1">
						<?php
							if($src === "")
							{
								the_post_thumbnail('size-2');
							}
							else{ ?>
								<img class="prdt-img" src="<?php echo esc_html($src); ?>" class="card-img-top" alt="<?php echo esc_html($product->TITLE); ?>" style="height:90%">
							<?php }
						?>
						
					</div>
					<div class="col-12 col-s-12 h2">
						<div class="h3">
							<h6 class="productname" style="font-size:14px;"><?php echo esc_html($product->TITLE) ?></h6>
							<span style="display: none" class="productId"><?php echo esc_html($product->ID) ?></span>
							<span style="display: none" class="imageId"><?php echo esc_html($product->IMAGE_ID) ?></span>
							<span style="display: none" class="imageUrl"><?php echo esc_html($product->IMAGE_URL) ?></span>
							<span class="text-truncate pl-2 hmsId" style="display: none;">
								<?php 
									$type = absint($product->ITEM_TYPE);
								if($type === 0){
									echo esc_html('b'.$product->HMS_ITEM_ID);
								}
								else if($type === 1)
								{
									echo esc_html('a'.$product->HMS_ITEM_ID);
								}
								else if($type === 2)
								{
									echo esc_html('c'.$product->HMS_ITEM_ID);
								}
								?>
							</span>
						</div>
						<span class="price"><?php echo esc_html(hh_webshop_money(floatval($product->ITEM_PRICE), true, $cur)); ?></span>
						<span class="text-truncate pl-2 price" style="font-size: 0.8rem; "><?php echo esc_html($priceduration[$product->PRICE_TYPE]);?></span>
						<input type="button" class="btn" value="Add to cart" name="addtocart" onClick='addToCart(this)' />
					</div>
				</div>
			</div>        		
			<?php
		}
		echo '</div>';
		echo '</div>';
		echo '<div class="clear"></div>';
	} else {
		echo 'No products found.';
	}
//Pagination links
	$total = absint($total_count);
	
	$total_pages = ceil(absint($total) / $items_per_page);

	echo '<div class="pagination" style="text-align:center;">';
	echo '<form method="get">'.wp_kses(wp_nonce_field( 'hirehop_webshop' ), array());
	echo wp_kses_post(paginate_links(array(
	'base' => add_query_arg('paged', '%#%'),
    'format' => '/page/%#%/',
    'current' => esc_html($current_page),
    'total' => esc_html($total_pages),
    'prev_text' => __('&laquo; Previous','hirehop_webshop'),
    'next_text' => __('Next &raquo;','hirehop_webshop'),
	)));

	echo '</form>';
	echo '</div>';
// Example: Per-page dropdown
	echo '<div class="per-page-dropdown" style="text-align:center;">';
	echo '<form method="get">'.wp_kses(wp_nonce_field( 'hirehop_webshop' ), array());
	echo '<label for="items_per_page">Per Page:</label>';
	echo '<select id="items_per_page" name="items_per_page" onchange="this.form.submit()">';
	$options = array(20, 40, 60, 80, 100); // Customize as needed
	foreach ($options as $option) {
		echo '<option value="' . esc_html($option) . '"' . selected(esc_html($items_per_page), esc_html($option), false) . '>' . esc_html($option) . '</option>';
	}
	echo '</select>';
	echo '</form>';
	echo '</div>';

	return ob_get_clean();
}
