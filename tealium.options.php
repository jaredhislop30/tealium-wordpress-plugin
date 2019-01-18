<?php
// Create a dropdown field
function tealiumSelectList( $id, $options, $multiple = false ) {
	$opt    = get_option( $id );
	$output = '<select class="select" name="' . $id . '" id="' . $id . '">';
	foreach ( $options as $val => $name ) {
		$sel = '';
		if ( $opt == $val )
			$sel = ' selected="selected"';
		if ( $name == '' )
			$name = $val;
		$output .= '<option value="' . $val . '"' . $sel . '>' . $name . '</option>';
	}
	$output .= '</select>';
	return $output;
}

// Create a friendly alias from UDO parameters
function tealiumFormatAsName( $key ) {
	// '_product_photo' becomes 'Product Photo'
	$key = ucwords( trim( str_replace( '_', ' ', $key ) ) );

	// Handle camelCase
	$key = join( preg_split( '/(^[^A-Z]+|[A-Z][^A-Z]+)/', $key, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE ), ' ');

	// Remove multiple spaces etc.
	$key = preg_replace( '/(\s\s+|\t|\n)/', ' ', $key );
	
	return $key;
}

// Create an exhaustive list of possible variables
function tealiumGenerateBulkDataSourceList() {
	$output = '';

	$UDOString = 'UDO Variable';
	$bulkString = "Imported from WordPress";

	// Array of basic variables
	$basicLayer = array(
		"siteName" => "Contains the site's name",
		"siteDescription" => "Contains the site's description",
		"postCategory" => "Contains the post's category, e.g. 'technology'",
		"postTags" => "Contains the post tags, e.g. 'tag management'",
		"pageType" => "Contains the page type, e.g. 'archive', 'homepage', or 'search'",
		"postTitle" => "Contains the post's title",
		"postAuthor" => "Contains the post author",
		"postDate" => "Contains the post date",
		"searchKeyword" => "Value of search text entered by user. eg. 'long sleeve'",
		"searchResults" => "Contains the number of search results returned",
		"cartTotal_items" => "Total number of all items in the cart.",
		"cartTotal_value" => "Total value for all items in the cart as a string with only digits and decimal",
		"categoryId" => "A unique identifier for the category being viewed eg. '243', 'MENS_SHOES', etc.",
		"categoryName" => "A user-friendly name for the category being viewed eg. 'Shoes: Boots'",
		"checkoutStep" => "Specifies which step number the user is on during the checkout process",
		"countryCode" => "Country Code eg. us, uk, mx, ca, jp, etc.",
		"customerCity" => "Contains the customer's city of residence.",
		"customerCountry" => "Contains the customer's country of residence.",
		"customerEmail" => "Contains the customer's email address.",
		"customerFirstName" => "The first name of the customer.",
		"customerId" => "Contains the unique customer ID.",
		"customerLastName" => "The last name of the customer.",
		"customerPostalCode" => "Contains the customer's postal code.",
		"customerState" => "Contains the customer's state of residence.",
		"languageCode" => "Language Code eg.  us, es, fr, etc.",
		"orderCurrencyCode" => "Currency code for the site eg. USD, GBP, EUR, CAD",
		"orderDiscountAmount" => "Contains the order-level discount amount. eg. 10.00  as a string with only digits and decimal",
		"orderTotal" => "Total Amount of the Order including tax and shipping but less all discounts as a string with only digits and decimal",
		"orderId" => "Unique Identifier for an order, should only be populated on Order Confirmation page.",
		"orderPaymentType" => "Contains the type of payment eg. visa, paypal",
		"orderPromoCode" => "String list of comma separated promotion codes.",
		"orderShippingAmount" => "Contains the total value for shipping as a string with only digits and decimal.",
		"orderShippingType" => "Contains the type of shipping. eg. 'FedEx Priority'.",
		"orderStore" => "Identifier of store type (i.e. web or mobile web)",
		"orderSubtotal" => "Contains price of all items including any product or order level discounts, but excluding tax and shipping as a string with only digits and decimal",
		"orderTaxAmount" => "Total tax amount for this order as a string with only digits and decimal.",
		"orderType" => "The type of conversion that just took place or user that is on the site.",
		"pageName" => "Tealium variable to identify the page name",
		"productBrand" => "An array of product brands.",
		"productCategory" => "An array of product categories",
		"productDiscountAmount" => "An array of product discount amounts, usually as a result of product-level coupons as strings with only digits and decimal",
		"productId" => "An array of product IDs",
		"productImageUrl" => "URL to the main product image.",
		"productName" => "An array of product names.",
		"productOriginalPrice" => "An array of original suggested retail product prices as strings with only digits and decimal",
		"productPrice" => "An array of product selling prices as strings with only digits and decimal",
		"productPromoCode" => "An array of promo/coupon codes applied to specific products eg. SHOES10OFF",
		"productQuantity" => "An array of quantities for each product.",
		"productSku" => "An array of product skus",
		"productSubcategory" => "An array of product sub-categories eg. Apparel",
		"productUrl" => "URL to the individual product",
		"siteSection" => "The high-level sections of your site eg. Apparel, Accessories, Help, etc."
	);

	if ( get_option( 'tealiumDataStyle' ) == '1' ) {
		// Convert camel case to underscore
		$basicLayer = apply_filters( 'tealium_convertCamelCase', $basicLayer );
	}

	// Get all meta keys from WP DB
	if ( "1" !== get_option( 'tealiumExcludeMetaData' ) ) {
		global $wpdb;
		$metaKeys = $wpdb->get_results( "SELECT DISTINCT(meta_key) FROM {$wpdb->postmeta} ORDER BY meta_key ASC" );

		$metaLayer = array();

		if ( $metaKeys ) {
			foreach ( $metaKeys as $metaKey ) {
				// Exclude meta keys with invalid characters
				if ( !preg_match( '/[^a-zA-Z0-9_$.]/', $metaKey->meta_key ) ) {
					$metaLayer[$metaKey->meta_key] = $bulkString;
				}
			}
		}

		$dataLayer = array_merge( $basicLayer, $metaLayer );
	}
	else {
		$dataLayer = $basicLayer;
	}

	// Remove excluded keys
	$dataLayer = apply_filters( 'tealium_removeExclusions', $dataLayer );

	if ( $dataLayer ) {
		foreach ( $dataLayer as $key => $value ) {
			$output .= $key . ', "'. $UDOString .'", "'. tealiumFormatAsName( $key ) .'", "'. $value .'"&#13;&#10;';
		}
	}

	return $output;
}
?>

<div class="wrap">
	<h1 class="tealium-icon">
		<span class="tealium-title"><?php _e( 'Tealium Settings', 'tealium' ); ?></span>
	</h1>

	<?php $active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'basic_settings'; ?>

	<h2 class="nav-tab-wrapper">
    	<a href="?page=tealium&tab=basic_settings" class="nav-tab <?php echo $active_tab == 'basic_settings' ? 'nav-tab-active' : ''; ?>"><?php _e( 'Basic Settings', 'tealium' ); ?></a>
    	<a href="?page=tealium&tab=advanced_settings" class="nav-tab <?php echo $active_tab == 'advanced_settings' ? 'nav-tab-active' : ''; ?>"><?php _e( 'Advanced Settings', 'tealium' ); ?></a>
    	<a href="?page=tealium&tab=data_export" class="nav-tab <?php echo $active_tab == 'data_export' ? 'nav-tab-active' : ''; ?>"><?php _e( 'Variable Bulk Export', 'tealium' ); ?></a>
	</h2>

	<?php
	if ( $active_tab == 'basic_settings' ) {
		?>
		<form method="post" action="options.php">
			<?php wp_nonce_field( 'update-options' ); ?>
			<?php settings_fields( 'tealiumTagBasic' ); ?>

			<table class="form-table basic">
				<tr>
					<th scope="row"><label for="tealiumAccount"><?php _e( 'Account', 'tealium' ); ?></label></th>
					<td>
						<input name='tealiumAccount' id='tealiumAccount' size='30' type='text' value='<?php echo get_option( 'tealiumAccount' ); ?>' class='regular-text' />
						<p class="description"><?php _e( 'For example: <code>companyname</code>', 'tealium' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="tealiumProfile"><?php _e( 'Profile', 'tealium' ); ?></label></th>
					<td>
						<input name='tealiumProfile' id='tealiumProfile' size='30' type='text' value='<?php echo get_option( 'tealiumProfile' ); ?>' class='regular-text' />
						<p class="description"><?php _e( 'For example: <code>main</code>', 'tealium' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="tealiumEnvironment"><?php _e( 'Environment', 'tealium' ); ?></label></th>
					<td>
						<input name='tealiumEnvironment' id='tealiumEnvironment' size='30' type='text' value='<?php echo get_option( 'tealiumEnvironment' ); ?>' class='regular-text' />
						<p class="description"><?php _e( 'For example: <code>prod</code>', 'tealium' ); ?></p>
					</td>
				</tr>
			</table>

			<input type="hidden" name="action" value="update" />

			<p class="submit"><input type="submit" class="button-primary" value="<?php _e( 'Save Changes', 'tealium' ); ?>" /></p>
		</form>
		<?php
	}
	else if ( $active_tab == 'advanced_settings' ) {
		?>
		<form method="post" action="options.php">
			<?php wp_nonce_field( 'update-options' ); ?>
			<?php settings_fields( 'tealiumTagAdvanced' ); ?>

			<table class="form-table advanced">
				<tr>
					<th scope="row"><label for="tealiumTagLocation"><?php _e( 'Tag location', 'tealium' ); ?></label></th>
					<td>
						<?php
						$options = array();
						$options[] = __( 'After opening body tag (recommended)', 'tealium' );
						$options[] = __( 'Header - Before closing head tag', 'tealium' );
						$options[] = __( 'Footer - Before closing body tag', 'tealium' );
						$options[] = __( 'Immediately after opening head tag', 'tealium' );
						echo tealiumSelectList( 'tealiumTagLocation', $options );
						?>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="tealiumTagType"><?php _e( 'Tag type', 'tealium' ); ?></label></th>
					<td>
						<?php
						$options = array();
						$options[] = __( 'Asynchronous (recommended)', 'tealium' );
						$options[] = __( 'Synchronous', 'tealium' );
						echo tealiumSelectList( 'tealiumTagType', $options );
						?>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="tealiumDataStyle"><?php _e( 'Data layer style', 'tealium' ); ?></label></th>
					<td>
						<?php
						$options = array();
						$options[] = __( 'CamelCase (legacy)', 'tealium' );
						$options[] = __( 'Underscore (recommended)', 'tealium' );
						echo tealiumSelectList( 'tealiumDataStyle', $options );
						?>
						<p class="description"><?php _e( 'CamelCase = <code>postDate, siteName</code> Underscore = <code>post_date, site_name</code>', 'tealium' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php _e( 'Track Woocommerce Data', 'tealium' ); ?></th>
					<td>
						<label for="tealiumIncludeWooCommerceTracking">
							<input type="checkbox" name="tealiumIncludeWooCommerceTracking" id="tealiumIncludeWooCommerceTracking" value="1"<?php checked( 1 == get_option( 'tealiumIncludeWooCommerceTracking' ) ); ?> />
							<?php _e( 'Track Product, Cart, and Order data', 'tealium' ); ?>
						</label>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php _e( 'Track Customer Data', 'tealium' ); ?></th>
					<td>
						<label for="tealiumTrackCustomerData">
							<input type="checkbox" name="tealiumTrackCustomerData" id="tealiumTrackCustomerData" value="1"<?php checked( 1 == get_option( 'tealiumTrackCustomerData' ) ); ?> />
							<?php _e( 'If user is logged in, capture Email, User Login, Display Name, User ID and ', 'tealium' ); ?>
						</label>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="tealiumExclusions"><?php _e( 'Data layer exclusions', 'tealium' ); ?></label></th>
					<td>
						<input name='tealiumExclusions' id='tealiumExclusions' size='50' type='text' value='<?php echo get_option( 'tealiumExclusions' ); ?>' class='regular-text' />
						<p class="description"><?php _e( 'Comma separated list, e.g. <code>postDate, custom_field_1</code>', 'tealium' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php _e( 'Exclude meta data', 'tealium' ); ?></th>
					<td>
						<label for="tealiumExcludeMetaData">
							<input type="checkbox" name="tealiumExcludeMetaData" id="tealiumExcludeMetaData" value="1"<?php checked( 1 == get_option( 'tealiumExcludeMetaData' ) ); ?> />
							<?php _e( 'Remove ALL WordPress meta data from data layer', 'tealium' ); ?>
						</label>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php _e( 'Synchronous file', 'tealium' ); ?></th>
					<td>
						<label for="tealiumUtagSync">
							<input type="checkbox" name="tealiumUtagSync" id="tealiumUtagSync" value="1"<?php checked( 1 == get_option( 'tealiumUtagSync' ) ); ?> />
							<?php _e( 'This profile uses a utag.sync.js file', 'tealium' ); ?>
						</label>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php _e( 'Cache buster', 'tealium' ); ?></th>
					<td>
						<label for="tealiumCacheBuster">
							<input type="checkbox" name="tealiumCacheBuster" id="tealiumCacheBuster" value="1"<?php checked( 1 == get_option( 'tealiumCacheBuster' ) ); ?> />
							<?php _e( 'Add a cache buster for content editors', 'tealium' ); ?>
						</label>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php _e( 'DNS prefetching', 'tealium' ); ?></th>
					<td>
						<label for="tealiumDNSPrefetch">
							<input type="checkbox" name="tealiumDNSPrefetch" id="tealiumDNSPrefetch" value="1"<?php checked( 1 == get_option( 'tealiumDNSPrefetch' ) ); ?> />
							<?php _e( 'Enable DNS Prefetching', 'tealium' ); ?>
						</label>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php _e( 'EU only', 'tealium' ); ?></th>
					<td>
						<label for="tealiumEUOnly">
							<input type="checkbox" name="tealiumEUOnly" id="tealiumEUOnly" value="1"<?php checked( 1 == get_option( 'tealiumEUOnly' ) ); ?> />
							<?php _e( 'Only use EU based CDN nodes', 'tealium' ); ?>
						</label>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="tealiumNamespace"><?php _e( 'Custom namespace', 'tealium' ); ?></label></th>
					<td>
						<input name='tealiumNamespace' id='tealiumNamespace' size='50' type='text' value='<?php echo get_option( 'tealiumNamespace' ); ?>' class='regular-text' />
						<p class="description"><?php _e( 'Use a custom namespace for the data layer instead of the default <code>utag_data</code>', 'tealium' ); ?></p>
					</td>
				</tr>
			</table>

			<h3 class="advanced"><label for="tealiumTagCode"><?php _e( 'Advanced Tag Code', 'tealium' ); ?></label></h3>
			<p class="description"><?php _e( 'Optional: Tealium tag code pasted below will be used instead of any account/profile/environment values entered under Basic Settings.', 'tealium' ); ?></p>
			<textarea name="tealiumTagCode" id="tealiumTagCode" rows="10" cols="100"><?php echo get_option( 'tealiumTagCode' ); ?></textarea>

			<input type="hidden" name="action" value="update" />

			<p class="submit"><input type="submit" class="button-primary" value="<?php _e( 'Save Changes', 'tealium' ); ?>" /></p>
		</form>
		<?php
		}
	else {
		?>
		<p>
			<p class="description"><?php _e( 'Bulk export of basic variables from all valid custom fields. Copy and paste into the \'Bulk Import from CSV\' option under Data Layer in Tealium IQ.', 'tealium' ); ?></p>
			<p><textarea readonly="readonly" name="csvExport" rows="20" cols="90"><?php echo tealiumGenerateBulkDataSourceList() ?></textarea></p>
		</p>
		<?php
	}
	?>
</div>