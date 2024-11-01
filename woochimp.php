<?php
/**
 * Plugin Name: WooChimpCommerce
 * Description: Woocommerce to Mailchimp Email Automation.
 * Version: 1.1
 * Author: Hike Branding
 * Author URI: https://hikebranding.com/
 */

add_action('admin_menu', 'wcc_mailchimp_plugin_setup_menu');
function wcc_mailchimp_plugin_setup_menu(){
	add_menu_page( 'Admin Settings', 'WooChimpCom', 'manage_options', 'mailchimp', 'wcc_test_init' );
}
 
function wcc_test_init(){
	$active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'api_key';
?>
<style>
.nav-tab-active{ background-color: #fff; color: #444; }
</style>

<h2>Mailchimp Plugin Options</h2>
<h2 class="nav-tab-wrapper">
    <a href="?page=mailchimp&tab=api_key" class="nav-tab <?php echo $active_tab == 'api_key' ? 'nav-tab-active' : ''; ?>">API Key</a>
	<a href="?page=mailchimp&tab=products" class="nav-tab <?php echo $active_tab == 'products' ? 'nav-tab-active' : ''; ?>">All Products</a>
	<a href="?page=mailchimp&tab=categories" class="nav-tab <?php echo $active_tab == 'categories' ? 'nav-tab-active' : ''; ?>">Categories</a>
	<a href="?page=mailchimp&tab=custom_field" class="nav-tab <?php echo $active_tab == 'custom_field' ? 'nav-tab-active' : ''; ?>">Custom Field</a>
	<a href="?page=mailchimp&tab=short_code" class="nav-tab <?php echo $active_tab == 'short_code' ? 'nav-tab-active' : ''; ?>">Short Code</a>
	<a href="?page=mailchimp&tab=setting" class="nav-tab <?php echo $active_tab == 'setting' ? 'nav-tab-active' : ''; ?>">Setting</a>
	
</h2>

<?php

	// API Key
	if( $active_tab == 'api_key' ) { 
		if(isset($_POST['mailchimp'])){
			$apiKey = $_POST['mailchampApi'];
			$valApi = wcc_mApiVali($apiKey);
		
			if($apiKey == ''){
				echo '<div id="setting-error-invalid_admin_email" class="error settings-error notice is-dismissible"> 
				<p><strong>API Key is Required</strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>';
			}  else if(empty($valApi)){
				echo '<div id="setting-error-invalid_admin_email" class="error settings-error notice is-dismissible"> 
				<p><strong>Invalid Mailchimp API key.</strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>';
			} else{
				update_option( 'mailchimp_api', $apiKey );
				 echo '<div id="setting-error-invalid_admin_email" style="border-left-color:green;" class="error settings-error notice is-dismissible"> 
				<p><strong>APIkey successfully saved.</strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>'; 
			}
		} ?>
		<div style="margin-top:10px; margin-left:19px;">
			<form method="post" action="#" >
				<table class="form-table">
					<tbody><tr>
						<th scope="row"><label for="blogname">Mailchimp API key</label></th>
						<td><input class="regular-text" type="text" name="mailchampApi" value="<?php echo get_option( 'mailchimp_api' ); ?>" /></td>
					</tr></tbody>
				</table>
				<?php submit_button( __( 'Submit' ), 'primary', 'mailchimp' ); ?>
			</form>
		</div><?php
		
	// All Product Order Status	
	}else if( $active_tab == 'products' ){
		$data = wcc_getMailchimpData(); ?>
		<p>* Email will be added in selected list on purchase of all products.</p><?php
		if(!empty($data)){ ?>
			<form method="post" action="#" ><?php
				$status = array("Completed","Processing","Failed","Pending payment","On-Hold","Cancelled","Refunded");
				$orderSt = array("wc-completed","wc-processing","wc-failed","wc-pending","wc-on-hold","wc-cancelled","wc-refunded");
				$proMailchimpData = get_option('add_prostatus_mailChimp');
				
					for($i=0;$i<count($status);$i++){ ?>
						<table class="form-table">
							<tbody>
								<tr>
									<th scope="row"><?=$status[$i]?></th>
									<td><fieldset><legend class="screen-reader-text"></legend><?php
									$statusOrder = $proMailchimpData['mailchimpId'][$orderSt[$i]];
									$j=0;
									foreach($data as $row){ ?>
										<label for="default_pingback_flag">
										<input type="checkbox" name="proMailchimp[<?=$orderSt[$i]?>][]" value="<?=$data[$j]['id']?>"<?php if(is_array($statusOrder) && in_array($data[$j]['id'],$statusOrder)){ echo "checked";}  ?> /><?php 
										echo $data[$j]['name']; ?></label><br/><?php
										$j++;
									} ?>
									</fieldset></td>
								</tr>
							</tbody>
						</table>
					<?php } 
				  submit_button( __( 'Submit' ), 'primary', 'AddProductmailchimp' ); ?>
			</form><?php
		} else{
			echo '<div id="setting-error-invalid_admin_email" class="error settings-error notice is-dismissible"> 
				<p><strong>Invalid Mailchimp API key</strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>';
		}
		if(isset($_POST['AddProductmailchimp'])){
			$arrProduct = array();
			$arrProduct['orderStatus'] = filter_var_array($_POST['h_status'],FILTER_SANITIZE_STRING);
			$arrProduct['mailchimpId'] = filter_var_array($_POST['proMailchimp'],FILTER_SANITIZE_STRING);	
			update_option( 'add_prostatus_mailChimp', $arrProduct );
			header('Location: ?page=mailchimp&tab=products');
		} 
	}else if( $active_tab == 'categories' ){ 
		$data = wcc_getMailchimpData();
		if(!empty($data)){ ?>
			<form method="post" action="#" ><?php
				$status = array("Completed","Processing","Failed","Pending payment","On-Hold","Cancelled","Refunded");
				$orderSt = array("wc-completed","wc-processing","wc-failed","wc-pending","wc-on-hold","wc-cancelled","wc-refunded");
				$proMailchimpData = get_option('allcat_status_mailchimp');
				$category = get_terms( array('taxonomy' => 'product_cat','hide_empty' => false) );
				
				foreach($category as $cat_row){ ?>
					<h3 style="color:#0085ba;">Category Name :  <?php echo $cat_row->name; ?></h3><?php
					for($i=0;$i<count($status);$i++){ ?>
					<table class="form-table">
						<tbody>
							<tr>
								<th scope="row"><?=$status[$i]?></th>
								<td><fieldset><legend class="screen-reader-text"></legend><?php
						$statusOrder = $proMailchimpData['mailchimpId'][$cat_row->term_id][$orderSt[$i]];
						$j=0;
						foreach($data as $row){ ?>
							<label for="default_pingback_flag">
							<input type="checkbox" name="proMailchimp[<?=$cat_row->term_id?>][<?=$orderSt[$i]?>][]" value="<?=$data[$j]['id']?>"<?php  if(is_array($statusOrder) && in_array($data[$j]['id'],$statusOrder)){ echo "checked";}   ?> /><?php 
							echo $data[$j]['name']; ?></label><br/><?php
							$j++;
						} ?>
						</fieldset></td>
							</tr>
						</tbody>
					</table>
						<?php
					}
				} ?>
				<p style="margin-left: 20px;"><?php submit_button( __( 'Submit' ), 'primary', 'AddCategoryMailchimp' ); ?></p>
			</form><?php
		}else{
			echo '<div id="setting-error-invalid_admin_email" class="error settings-error notice is-dismissible"> 
				<p><strong>Invalid Mailchimp API key</strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>';
		}
		if(isset($_POST['AddCategoryMailchimp'])){
			$arrCategory = array();
			$arrCategory['mailchimpId'] = filter_var_array($_POST['proMailchimp'],FILTER_SANITIZE_STRING);
			update_option( 'allcat_status_mailchimp', $arrCategory );
			header('Location: ?page=mailchimp&tab=categories');
		}
	}else if( $active_tab == 'custom_field' ){ 
		$data = wcc_getMailchimpData();
		if(!empty($data)){ ?>
			<form method="post" action="#" ><?php
				$customArr = array('billing_first_name','billing_last_name','billing_company','shipping_total','billing_country','billing_address_1','billing_city','billing_state','billing_postcode','billing_phone','id','date_created');
				$customArrVal = array('Firstname','Lastname','Company','Total','Country','Address1','City','State','ZIP','Phone','Order ID','Date');
				
				$arrs = array();
				for($a=0;$a<count($data);$a++){
					$list_id = $data[$a]['id'];
					$result = wcc_getCustomField($list_id);
					//$a = array("FNAME","LNAME");
					if($result==0){
						array_push($arrs, array('FNAME','LNAME'));
					}else{
						array_push($arrs, $result);
					}
					
				}
				
				$getCustom = get_option( '_mailchimpCustom' );
				
				for($i=0;$i<count($arrs);$i++){ 
					$getCustoms  = $getCustom['custom_mailchimp'][$data[$i]['id']]; 
									
					 ?>
					<h3 style="color:#0085ba;"><?=$data[$i]['name']?></h3>
					<table class="form-table">
						<tbody><?php
							foreach($arrs[$i]  as $keyV){ ?>
								<tr><th scope="row"><label for="blogname"><?=$keyV?></label></th>
									<td>
									<?php 
									if(in_array($getCustoms[$keyV],$customArr))
										$sel_value = $getCustoms[$keyV];
									else
										$sel_value = '';
										?>
										<select name="billing[<?=$data[$i]['id']?>][<?=$keyV?>]" style="width: 25%;">
											<option value="">Select</option><?php
											
											// if(in_array($getCustoms[$arrs[$i][$k]],$customArr)){ echo "selected='selected'"; } 
											for($k=0;$k<count($customArr);$k++){
												//echo $getCustoms[$arrs[$i][$k]];
											if($sel_value == $customArr[$k])
											{
												$selected  = 'selected=selected'; 
												}
												else
												{
													$selected= '';
												}
												
											?>
												<option value="<?=$customArr[$k]?>" <?php echo $selected;?>><?=$customArrVal[$k]?></option><?php 
											} ?>
										</select>
									</td>
								</tr><?php 
							} ?>
						</tbody>
					</table><?php
				}  submit_button( __( 'Submit' ), 'primary', 'addmailcustom' ); ?>
			</form> <?php
		}else{
			echo '<div id="setting-error-invalid_admin_email" class="error settings-error notice is-dismissible"> 
				<p><strong>Invalid Mailchimp API key</strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>';
		}
		if(isset($_POST['addmailcustom'])){
			$arrCustom['custom_mailchimp'] = filter_var_array($_POST['billing'],FILTER_SANITIZE_STRING);
			update_option( '_mailchimpCustom', $arrCustom );
			header('Location: ?page=mailchimp&tab=custom_field');
		}
	}else if( $active_tab == 'short_code' ){ 
		$data = wcc_getMailchimpData();
		if(!empty($data)){ ?>
			<table class="form-table">
			<tbody>
			<tr>
			<th>Mailchimp Lists</th><th>Short Code</th></tr>
					<?php
						for($i=0;$i<count($data);$i++)
						{ ?>
							<tr>
								<th scope="row"><label for="blogname"><?=$data[$i]['name']?></label></th>
								<td><?php 
									echo "[WooChimpCommerce id=".$data[$i]['id']."]"; ?>
								</td>
							</tr><?php 
						} ?>
				</tbody>
			</table><?php
		}
		else{
			echo '<div id="setting-error-invalid_admin_email" class="error settings-error notice is-dismissible"> 
				<p><strong>Invalid Mailchimp API key</strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>';
		}
	}else if( $active_tab == 'setting' ){ 
		$checkMailchimpchebox = get_option( '_mailchimp_checkbox' ); ?>
		<form method="post" action="#" >
			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row">Ask for customer's Permission </th>
						<td> <fieldset><legend class="screen-reader-text"></legend><label for="users_can_register">
						<input type="checkbox" name="mailchamp_checkbox" value="1"<?php if($checkMailchimpchebox == 1) { echo "checked='checked'"; } ?> />Display on checkout page</label>
						</fieldset></td>
					</tr>
				</tbody>
			</table>
			<?php submit_button( __( 'Submit' ), 'primary', 'mailchimpCheckout' ); ?>
		</form><?php
		if(isset($_POST['mailchimpCheckout'])){
			//$checkbox = filter_var_array($_POST['mailchamp_checkbox'],FILTER_SANITIZE_STRING);
			$checkbox = filter_var($_POST['mailchamp_checkbox'],FILTER_SANITIZE_STRING);
			update_option( '_mailchimp_checkbox', $checkbox );
			
			header('Location: ?page=mailchimp&tab=setting');
		}
	}
}

//  For connect mailchimp 
function wcc_mailchimp_curl_connect($urltopost) {
	$request_type = 'GET';
	$api_key = get_option( 'mailchimp_api' );
	if( $request_type == 'GET' )

	$headers = array(
		'Content-Type: application/json',
		'Authorization: Basic '.base64_encode( 'user:'. $api_key )
	);
	
	$response = wp_remote_post( $urltopost, array(
	   'method' => 'GET',
	   'headers' => array( 'Authorization' => 'Basic ' .base64_encode( 'user:'. $api_key ),
						   'Content-Type' => 'application/json;')
	   )
	 );	 
		 
	$args = array(
	   'headers' => array(
		   'Authorization' => 'Basic ' .base64_encode( 'user:'. $api_key )
		 )
	);

	$response = wp_remote_get( $urltopost, $args );
	return $response['body'];
} 

// Function for custom field getting from Mailchimp
function wcc_getCustomField($list_id){
	$api_key = get_option( 'mailchimp_api' );
	$dc = substr($api_key,strpos($api_key,'-')+1); 
	$data = array();
	$url = 'https://'.$dc.'.api.mailchimp.com/3.0/lists/'.$list_id.'/members';
	$body = json_decode( wcc_mailchimp_curl_connect( $url ) );
	$result = json_decode( json_encode($body->members), true);
	$arr = array();
	
	if(!empty($result)){
		foreach($result[0] as $row){
			array_push($arr, $result[0]['merge_fields']);
		}  
		$key = array_keys($arr[0]); 
		return $key;
	}else{
		return $key=0;
	}
}

// API key Validation
function wcc_mApiVali($api_key){
	$dc = substr($api_key,strpos($api_key,'-')+1);
	$args = array('headers' => array('Authorization' => 'Basic ' . base64_encode( 'user:'. $api_key )));
	$response = wp_remote_get( 'https://'.$dc.'.api.mailchimp.com/3.0/lists/', $args );
	if(is_wp_error( $response ) == 1){
		return $error;
	}else{
		$body = json_decode($response['body']);
		$res = (array)$body;
		$result = $res['lists'];
		return $data = json_decode( json_encode($result), true); 
	}
}

// Function for Mailchimp Settings
function wcc_getMailchimpData(){
	$api_key = get_option( 'mailchimp_api' );
	$dc = substr($api_key,strpos($api_key,'-')+1);
	$args = array('headers' => array('Authorization' => 'Basic ' . base64_encode( 'user:'. $api_key )));
	$response = wp_remote_get( 'https://'.$dc.'.api.mailchimp.com/3.0/lists/', $args );
	
	if(is_wp_error( $response ) == 1){
		return $error;
	}else{
		$body = json_decode($response['body']);
		$res = (array)$body;
		$result = $res['lists'];
		return $data = json_decode( json_encode($result), true); 
	}
}

// Woocommerce order creation from thank you (frontend)
add_action( 'woocommerce_thankyou', 'wcc_my_change_status_function' );
function wcc_my_change_status_function( $order_id ) {
    $order = new WC_Order( $order_id );
	$order_data = $order->get_data(); // The Order data
	
    $items = $order->get_items();
    $customer_id = get_current_user_id();
    $email_id = get_user_meta( $customer_id, 'billing_email', true ); 
	$postData = get_post($order_id);
	$postStatus = $postData->post_status;
	
    foreach ( $items as $item ) {
        $product_id =$item['product_id'];
		
		// Single Product
		$mailchimpSinPro = get_post_meta($product_id, '_mailchimpProSingle', true );
		if(!empty($mailchimpSinPro['mailchimpId'][$postStatus])){
			$mailCimpStatus = $mailchimpSinPro['mailchimpId'][$postStatus];
		}else{ $mailCimpStatus = array(); }
		
		// All Products
		$mailchimpAllPro = get_option('add_prostatus_mailChimp');
		//print_r($mailchimpAllPro);
		if(!empty($mailchimpAllPro['mailchimpId'][$postStatus])){
			$mailCimpAllProStatus = $mailchimpAllPro['mailchimpId'][$postStatus];
		}else{ $mailCimpAllProStatus = array(); }
		
		// Category Wise Product
		$mailchimpCat = get_option('allcat_status_mailchimp');
		$terms = get_the_terms( $product_id, 'product_cat' );
		$arrCat = array();	
		$arrCats = array();	
		foreach($terms as $catId){
			$arrCat[] = $mailchimpCat['mailchimpId'][$catId->term_id][$postStatus];
			foreach($arrCat as $k=>$cat){
				array_push($arrCats,$cat[$k]);
			}
		} 
		$getMailchimp = array_unique(array_merge($mailCimpStatus,$mailCimpAllProStatus,$arrCats));
		$getMailchimpId  = array_values($getMailchimp);
		$mailchimp_custom = get_option( '_mailchimpCustom' );
		//echo "<pre>"; print_r($getMailchimpId); echo "</pre>";
		//echo "<pre>"; print_r($mailchimp_custom['custom_mailchimp']); echo "</pre>";
		
		for($i=0;$i<count($getMailchimpId);$i++){
			$custom_data = $mailchimp_custom['custom_mailchimp'][$getMailchimpId[$i]]; 
			$phone1 = get_user_meta( $customer_id, 'billing_phone', true );
			if(!empty($phone1))
				$phone = "".substr($phone1, 0, 3)."-".substr($phone1, 3, 3)."-".substr($phone1,6);
			else
				$phone = '';
			//echo "<pre>"; print_r($custom_data); echo "</pre>";
			if(!empty($custom_data)){
				$custom_data = str_replace('billing_first_name', get_user_meta( $customer_id, 'billing_first_name', true ), $custom_data);
				$custom_data = str_replace('billing_last_name', get_user_meta( $customer_id, 'billing_last_name', true ), $custom_data);
				$custom_data = str_replace('billing_company', get_user_meta( $customer_id, 'billing_company', true ), $custom_data);
				$custom_data = str_replace('shipping_total', $order_data['shipping_total'], $custom_data);
				$custom_data = str_replace('billing_country', get_user_meta( $customer_id, 'billing_country', true ), $custom_data);
				$custom_data = str_replace('billing_address_1', get_user_meta( $customer_id, 'billing_address_1', true ), $custom_data);
				$custom_data = str_replace('billing_city', get_user_meta( $customer_id, 'billing_city', true ), $custom_data);
				$custom_data = str_replace('billing_state', get_user_meta( $customer_id, 'billing_state', true ), $custom_data);
				$custom_data = str_replace('billing_postcode', get_user_meta( $customer_id, 'billing_postcode', true ), $custom_data);
				$custom_data = str_replace('billing_phone', $phone, $custom_data);
				$custom_data = str_replace('id', $order_data['id'], $custom_data);
				$custom_data = str_replace('date_created', $order_data['date_created']->date('m/d/Y'), $custom_data);
				
			}else{
			// No selected Custom Field
				$keyCustom = array_keys($mailchimp_custom['custom_mailchimp']);
				if($keyCustom) { 
					$resultDiff = array_diff($getMailchimpId, $keyCustom);
					if(!empty($resultDiff)){
						$fname = get_user_meta( $customer_id, 'billing_first_name', true );
						$lname = get_user_meta( $customer_id, 'billing_last_name', true );
						$custom_data = array('FNAME'=>$fname,'LNAME'=>$lname);
					}
				}
			}
			
			if(!empty($custom_data)){
				$json = array(
					'email_address' => $email_id,
					'status'        => 'subscribed',
					'merge_fields'  => $custom_data
				); 	
			}
	
			$checkMailchimp = get_post_meta($order_id , 'mailchimp_checkbox', true);
			
			if($checkMailchimp==1){
				$result = wcc_sendMailMailChimp($getMailchimpId[$i],$email_id,$json);
			}
		} 
    }
} 
// Send Mailchimp Function
function wcc_sendMailMailChimp($mailchimpList,$email_id,$json){
	$api_key = get_option( 'mailchimp_api' ); 
	$memberID = md5(strtolower($email_id));
	$dataCenter = substr($api_key,strpos($api_key,'-')+1);
	$urltopost = 'https://' . $dataCenter . '.api.mailchimp.com/3.0/lists/' . $mailchimpList . '/members/' . $memberID;
	$status = wcc_checkMemberStatus($email_id,$api_key,$urltopost);
	$json = json_encode($json);
	
	if($status == 'subscribed'){
		$response = wp_remote_post( $urltopost, array(
			'method' => 'PUT',
			'headers' => array( 'Authorization' => 'Basic ' .base64_encode( 'user:'. $api_key ),
						'Content-Type' => 'application/json;'),
			'body' => $json
			)
		);
		$args = array(
			'headers' => array(
				'Authorization' => 'Basic ' .base64_encode( 'user:'. $api_key )
			)
		);
		$response = wp_remote_get( $urltopost, $args );
		return $response['body'];
	}else{
		$response = wp_remote_post( $urltopost, array(
			'method' => 'PUT',
			'headers' => array( 'Authorization' => 'Basic ' .base64_encode( 'user:'. $api_key ),
						'Content-Type' => 'application/json;'),
			'body' => $json
			)
		);
		$args = array(
			'headers' => array(
				'Authorization' => 'Basic ' .base64_encode( 'user:'. $api_key )
			)
		);
		$response = wp_remote_get( $urltopost, $args );
		return $response['body'];
	}
}
function wcc_checkMemberStatus($email,$apikey,$urltopost)
{
	$userid = md5($email);
    $auth = base64_encode( 'user:'. $apikey );
    $data = array(
        'apikey'        => $apikey,
        'email_address' => $email
        );
	$response = wp_remote_post( $urltopost, array(
		'method' => 'GET',
		'headers' => array( 'Authorization' => 'Basic ' .base64_encode( 'user:'. $apikey ),
						'Content-Type' => 'application/json;'),
		'body' => $data
		)
	);

	$args = array(
		'headers' => array(
			'Authorization' => 'Basic ' .base64_encode( 'user:'. $apikey )
		)
	);
	$response = wp_remote_get( $urltopost, $args );
	$result = json_decode($response['body']);	
    return $result->status;
}
// Checkbox from Checkout page
$checkMailchimpchebox = get_option( '_mailchimp_checkbox' );
if($checkMailchimpchebox == 1){
	add_action('woocommerce_after_order_notes', 'my_custom_checkout_field');
function my_custom_checkout_field( $checkout ) {

    $checked = $checkout->get_value( 'mailchimp_check' ) ? $checkout->get_value( 'mailchimp_check' ) : 1;

    echo '<div id="my-new-field"><h3>'.__('My Checkbox: ').'</h3>';

    woocommerce_form_field( 'mailchimp_check', array(
        'type'          => 'checkbox',
        'class'         => array('input-checkbox'),
        'label'         => __('Subscribe to our latest services'),
        ), $checked);

    echo '</div>';
}
	/* add_action('woocommerce_after_order_notes', 'mailchimp_checkout_field');
	function mailchimp_checkout_field( $checkout ) {
		$checked = $checkout->get_value( 'mailchimp_check' ) ? $checkout->get_value( 'mailchimp_check' ) : 1;
		echo '<div id="my-new-field">';
			woocommerce_form_field( 'mailchimp_check', array(
				'type'          => 'checkbox',
				'class'         => array('input-checkbox'),
				'label'         => __('Subscribe to our latest services'),
			), $checkout->get_value( 'mailchimp_check' ));
		echo '</div>';
	} */
}
// Save Data of checkout checkbox
add_action('woocommerce_checkout_update_order_meta', 'wcc_mailchimp_field_update_order_meta');
function wcc_mailchimp_field_update_order_meta( $order_id ) {
	if ($_POST['mailchimp_check']) update_post_meta( $order_id, 'mailchimp_checkbox', esc_attr($_POST['mailchimp_check']));
}


// Add Custom Meta for Products
add_action( 'add_meta_boxes', 'wcc_product_details_add' );     
function wcc_product_details_add() {
    add_meta_box( 'product_details', 'Add product to Mailchimp List on Purchase', 'wcc_product_details_call', 'product', 'normal', 'low' );
}

function wcc_product_details_call( $post ) {
	//global $woocommerce, $post;
	$data = wcc_getMailchimpData(); ?>
	<div class="options_group"><?php
		$status = array("Completed","Processing","Failed","Pending payment","On-Hold","Cancelled","Refunded");
		$orderSt = array("wc-completed","wc-processing","wc-failed","wc-pending","wc-on-hold","wc-cancelled","wc-refunded");
		for($i=0;$i<count($status);$i++){ ?>
			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row"><?=$status[$i]?></th><?php
						$postId = get_the_ID();
						$proMailchimpData = get_post_meta($postId, '_mailchimpProSingle', true );
						if($proMailchimpData){
							$statusOrder = $proMailchimpData['mailchimpId'][$orderSt[$i]];
						}
						$j=0; ?>
						<td><fieldset><legend class="screen-reader-text"></legend><?php
							foreach($data as $row){ ?>
								<label for="default_pingback_flag">
									<input style="float:none;" class="checkbox" name="proMailchimp[<?=$orderSt[$i]?>][]" id="proMailchimp[<?=$orderSt[$i]?>][]" value="<?=$data[$j]['id']?>"<?php if(is_array($statusOrder) && in_array($data[$j]['id'],$statusOrder)){ echo "checked";}  ?> type="checkbox"> <span class="description"><?php echo $data[$j]['name']; ?></span>
								</label><br><?php 
								$j++; 
							} ?>
						</fieldset></td>
					</tr>
				</tbody>
			</table><?php 
		} ?>
	</div><?php 
}


// Save Custom Product
add_action( 'save_post', 'wcc_product_details_save' );
function wcc_product_details_save( $post_id ) {  
	$arrProduct = array();
	$arrProduct['orderStatus'] = filter_var_array($_POST['h_status'],FILTER_SANITIZE_STRING);
	$arrProduct['mailchimpId'] = filter_var_array($_POST['proMailchimp'],FILTER_SANITIZE_STRING);
	update_post_meta( $post_id, '_mailchimpProSingle', $arrProduct );
add_action( 'woocommerce_process_product_meta', 'woo_add_custom_general_fields_save' );
} 

function wcc_shortcodeSendMail($atts)
{
	if(!empty($atts['id']))
	{
	?>
		<div class="errorMsg"></div><br>
		<input class="regular-text" type="text" name="email" id="email" value="" placeholder="Enter Your Email"/>
		<input type="hidden" id="ids" value="<?php echo $atts['id'];?>">
		<input type="submit" name="shortcode_sendmail" onclick="sendmailchimp()"><?php 
	}else
		echo "Please set your Mailchip ID.";?>

	<script>
	ajax_url = "<?php echo admin_url('admin-ajax.php'); ?>";
	function sendmailchimp()
	{
		var mail_id = jQuery('#email').val();
		var ids = jQuery('#ids').val();
	
		var data = {
				action: 'mailchimp_shortcode',
				mail_id: mail_id,
				ids:ids
				};
				jQuery.post(ajax_url, data, function(response) {
				jQuery('.errorMsg').html(response);
		});
	}
	</script>
	<?php
	
}
add_shortcode( 'WooChimpCommerce', 'wcc_shortcodeSendMail' );
// Responce Licence key
function wcc_mailchimp_shortcode_ajax() {
	
	$email = $_POST['mail_id'];
	$ids = $_POST['ids'];
		// Remove all illegal characters from email
	 if ( ! empty($ids) ) {
		 $ids = explode(",",$ids);
	 }	
	 else
	 {
		 echo "Please set your mailchimp id.";
		 die();
	 }
		// Validate e-mail
		if(!empty($email))
		{
			$email = filter_var($email, FILTER_SANITIZE_EMAIL);
			if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
				for($i=0;$i<count($ids);$i++)
				{
					$json = array(
						'email_address' => $email,
						'status'        => 'subscribed'
					); 	
					$result = wcc_sendMailMailChimp($ids[$i],$email,$json);
					
				}
				$msg = "<p style='color:#008000;'>Email added successfully..</p>";
			} 
			else {
				$msg = "<p style='color:#FF0000;'>'".$email."' is not a valid email address.</p>";
			}
		}
		else
			$msg = "<p style='color:#FF0000;'>Please enter your Email.</p>";
		echo $msg;
	die();
}
add_action('wp_ajax_mailchimp_shortcode', 'wcc_mailchimp_shortcode_ajax');
?> 