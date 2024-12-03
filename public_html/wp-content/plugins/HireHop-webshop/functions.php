<?php

/**api_data_plugin_activate
 * 
 * @global type $wpdb
 * create custom tables as per 1:wp_company , 2: wp_api_credentials , 3: wp_hh_items_data , 4: wp_hh_category 
 */

function hh_webshop_api_data_plugin_activate() {
    // Create a custom database table to store API credentials
    global $wpdb;
	
	$hh_api = $wpdb->prefix.'hh_api';

    $charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE $hh_api (
			`ID` bigint unsigned NOT NULL AUTO_INCREMENT,
			`API_KEY` VARCHAR(255) NULL DEFAULT NULL,
			`API_URL` VARCHAR(255) NULL DEFAULT NULL,
			`DATE_TIME` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (`ID`),
			UNIQUE KEY `ID_UNIQUE` (`ID`)
	)$charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);

	
	
	
	//Wordpress database table name
    $hh_company = $wpdb->prefix.'hh_company';
	
	//set database charset setting
    $charset_collate = $wpdb->get_charset_collate();

	//create the table
    $sql = "CREATE TABLE $hh_company (
        `ID` bigint unsigned NOT NULL AUTO_INCREMENT,
        `HIREHOP_ID` bigint unsigned NOT NULL COMMENT 'HIREHOP COMPANY ID',
        `COMPANY` varchar(85) DEFAULT NULL COMMENT 'HIRE HOP COMPANY NAME',
        `CURRENCY`  longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`CURRENCY`)),
        `LOGO` tinyint unsigned NOT NULL DEFAULT '0' COMMENT 'The version number of the company logo.  This prevents cache errors.',
        `WEBSITE` varchar(75) NOT NULL DEFAULT '' COMMENT 'Company website (for marketing)',
        `THEME`  longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`THEME`)),
        `EXTRA_FIELDS`  longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`EXTRA_FIELDS`)),
        `DATE_FORMAT` tinyint NOT NULL DEFAULT '0' COMMENT 'null or 0 = D M Y\\\\n1 = Y M D\\\\n2 = M D Y',
        `LENGTH_FORMAT` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '0 = meters\\n1 = yards',
        `WEIGHT_FORMAT` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '0 = kg\\n1 = lbs',
        `REGION` varchar(7) NOT NULL DEFAULT 'en-GB' COMMENT 'Language region',
        `EMAIL` varchar(45) NOT NULL DEFAULT '',
        `TOKEN` text,
        `DEPOT` INT NULL DEFAULT '0',
        `PRICE_VIEW` TINYINT NULL DEFAULT '0',
		`LOGIN_LOG`  longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`LOGIN_LOG`)),
        PRIMARY KEY (`ID`),
        UNIQUE KEY `ID_UNIQUE` (`ID`)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);

   
    $hh_items = $wpdb->prefix.'hh_items';

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $hh_items (
        `ID` bigint unsigned NOT NULL AUTO_INCREMENT,
	`HMS_ITEM_ID` bigint unsigned NOT NULL,
	`ITEM_TYPE` tinyint  NOT NULL,
	`TITLE` varchar(255)  NOT NULL,
	`ALT_TITLE` varchar(255)   DEFAULT NULL,
	`DESCRIPTION` text  ,
	`ITEM_PRICE` decimal(22,4) DEFAULT NULL COMMENT 'item price, used for sales or labour items',
    `VAT_RATE` decimal(22,4) DEFAULT NULL,
	`ITEM_COST` decimal(22,4) DEFAULT NULL COMMENT 'Item cost',
	`PART_NO` varchar(65)   DEFAULT NULL,
	`BARCODE` varchar(85)   DEFAULT NULL,
	`CATAGORY` bigint unsigned NOT NULL,
	`cat_name` varchar(255) NOT NULL,
	`IMAGE_ID` bigint unsigned NOT NULL,
	`WEIGHT` decimal(10,2) NOT NULL,
	`WIDTH` decimal(10,2) NOT NULL,
	`LENGTH` decimal(10,2) NOT NULL,
	`HEIGHT` decimal(10,2) NOT NULL,
	`PART_NUMBER` varchar(255) DEFAULT NULL,
	`COUNTRY_ORIGIN` varchar(255) DEFAULT NULL,
	`LAST_UPDATE` datetime NOT NULL,
	`STATUS` tinyint NOT NULL,
	`PRICE_TYPE` tinyint NOT NULL, 
    `EXTRA_FIELDS`  longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`EXTRA_FIELDS`)),
	`IMAGE_URL` TEXT NULL DEFAULT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `ID_UNIQUE` (`ID`)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
	
	
	$hh_category = $wpdb->prefix.'hh_category';

	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE $hh_category (
  `ID` bigint unsigned NOT NULL AUTO_INCREMENT,
  `COMPANY_ID` bigint unsigned NOT NULL,
  `WS_ID` bigint unsigned NOT NULL,
  `TYPE` int NOT NULL COMMENT 'Which catagory group 0=Hire, 1=Consumable/sales, 2=Service/labour',
  `HEADING` varchar(45) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `LFT` int unsigned NOT NULL DEFAULT '0',
  `RGT` int unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`),
  UNIQUE INDEX `ID_UNIQUE` (`ID` ASC) 
    ) $charset_collate;";

	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	dbDelta($sql);
}
function hh_webshop_api_data_plugin_deactivate() {
	// Deactivation code (if needed)
	 // Drop the custom database table when the plugin is deactivated
    global $wpdb;

	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.SchemaChange
	$wpdb->query("DROP TABLE IF EXISTS ".$wpdb->prefix."hh_api");
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.SchemaChange
	$wpdb->query("DROP TABLE IF EXISTS ".$wpdb->prefix."hh_items");
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.SchemaChange
	$wpdb->query("DROP TABLE IF EXISTS ".$wpdb->prefix."hh_category");
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.SchemaChange
	$wpdb->query("DROP TABLE IF EXISTS ".$wpdb->prefix."hh_company");
    
}