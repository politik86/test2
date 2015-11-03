<?php
/*------------------------------------------------------------------------
# com_adagency
# ------------------------------------------------------------------------
# author    iJoomla
# copyright Copyright (C) 2013 ijoomla.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.ijoomla.com
# Technical Support:  Forum - http://www.ijoomla.com/forum/index/
-------------------------------------------------------------------------*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' ); 

class paypal {

    function selfile () {
        return  "paypal_payment.php";   
    }
    
    function writeto($file, $msg) {        
        $handle = @fopen( JPATH_BASE . DS . $file, "a");        
        @fwrite($handle, date('Y-m-d H:i:s') . ': ' . $msg."\n");
        @fclose($handle);    
    }    

    function type () {
        return "payment";
    }

    function getBEData ($plugin_conf) {
        return $paypalform;
    }
    
    function insert_currency() {       
        $currencies = array (
        	'USD' => 'U.S. Dollar',
        	'AUD' => 'Australian Dollar',
        	'CAD' => 'Canadian Dollar',
        	'CHF' => 'Swiss Franc',
        	'CZK' => 'Czech Koruna',
        	'DKK' => 'Danish Krone',
        	'EUR' => 'Euro',
        	'GBP' => 'Pound Sterling',
        	'HKD' => 'Hong Kong Dollar',
        	'HUF' => 'Hungarian Forint',
        	'JPY' => 'Japanese Yen',
        	'NOK' => 'Norwegian Krone',
        	'NZD' => 'New Zealand Dollar',
        	'PLN' => 'Polish Zloty',
        	'SEK' => 'Swedish Krona',
        	'SGD' => 'Singapore Dollar'
        );

        return $currencies;        
        //echo $database->getQuery();
    }    
    
    function deleteCurrency() {
       $database = JFactory::getDBO();
            
        $sql = "DELETE FROM #__adagency_currencies WHERE plugname='" . get_class($this) . "'";
        $database->setQuery($sql);
        $database->query();
        //echo $database->getQuery(); die;
        //echo $database->getQuery(); die;

    }

	function sandbox_support(){
		return 1;	
	}
	
    function getFEData ( $items, $tax, $redirect , $profile, $plugin_conf, $configs) {
    
		$page_itemid = JRequest::getInt('Itemid','0');
		if($page_itemid != '0'){
			$Itemid = "&Itemid=".$page_itemid;
		} else {
			$Itemid = NULL;
		}
	
        $db = JFactory::getDBO();
		$mosConfig_live_site = JURI::base(); 
   		$cust_info = $profile;
		$configs->currency = $tax['currency'];
	    $tax1 = $tax;
        
        //check to see if this client has purchased this product
        
        $sid = $profile->_sid;
        $uid = $profile->_user_id;      
        
		@session_start();
		$_SESSION['order_accepted'] = 0;
		//paypal have no "failed url, but only url for cancel payment
		
		//$mosConfig_live_site = str_replace("http:", "https:", $mosConfig_live_site);
		

		$failed_url = $mosConfig_live_site.'index.php?option=com_adagency&controller=adagencyCampaigns&task=failPayment&plugin=paypal&custom='.$uid.';'.$sid.$Itemid;

		if ($plugin_conf->sandbox == 1) $paypal_url = '<form action="https://www.sandbox.paypal.com/cgi-bin/webscr" method="post" name="paymentForm">';
			else $paypal_url = '<form action="https://www.paypal.com/cgi-bin/webscr" method="post" name="paymentForm">';
        $content = $paypal_url.'
        <!-- <input type="hidden" name="cmd" value="_xclick"> -->
			<input type="hidden" name="cmd" value="_cart">
			<input type="hidden" name="upload" value="1">  
            <input type="hidden" name="business" value="'.$plugin_conf->config->data['paypal_email'].'">
		<!--
            <input type="hidden" name="item_name" value="iJoomla products">
            <input type="hidden" name="item_number" value="1">
		-->';
		$i = 0;
		$total1 = 0;

		foreach($items as $i => $item){
			if ($i < 0) continue;
			++$i;
            $price = $item->amount/$item->quantity;
			
			if(isset($_SESSION["discount"]) && trim($_SESSION["discount"]) != ""){
				$content .= "<input type='hidden' id='discount_amount_cart' name='discount_amount_cart' value='".trim($_SESSION["discount"])."'>";
			}
			

			$content .= '<input type="hidden" name="item_name_'.$i.'" value="'.str_replace('"', "'", $item->name).'">';                       
			$content .= '<input type="hidden" name="amount_'.$i.'" value="'.sprintf("%.2f",$price).'">';
			$content .= '<input type="hidden" name="quantity_'.$i.'" value="'.$item->quantity.'">';
        }
     
        $tax = $tax['value'];//$total - $total1;



		$content .= '
            <input type="hidden" name="no_note" value="1">
            <input type="hidden" name="currency_code" value="'.$configs->currency.'">
            <input type="hidden" name="bn" value="PP-BuyNowBF">
            <input type="hidden" name="notify_url" value="'.$mosConfig_live_site.'index.php?option=com_adagency&controller=adagencyCampaigns&task=notifyPayment&plugin=paypal&no_html=1&custom='.$uid.';'.$sid.'">
            <input type="hidden" name="return" value="'.$mosConfig_live_site.'index.php?option=com_adagency&controller=adagencyCampaigns&task=returnPayment&plugin=paypal&custom='.$uid.';'.$sid.$Itemid.'">
            <input type="hidden" name="cancel_return" value="'.$failed_url.'">
            <input type="hidden" name="rm" value="2" >
			<!--<input type="hidden" name="return_method" value="2" >-->
            <input type="hidden" name="custom" value="'.$uid.';'.$sid.';">';



        if($redirect){
			$content .= '<table style="margin:auto">
							<tr style="border:none;">
								<td align="center" style="border:none;">
									<span style="font-size:24px;">'.JText::_('DSPAYMENT_WITH_PAYPAL').'</span>
								</td>
							</tr>
							<tr style="border:none;">
								<td align="center" style="border:none;">
									<img border="0" alt="PAYPAL" name="ad_img_submit" src="'.JURI::root().'components/com_adagency/images/pleasewait.gif" />
								</td>
							</tr>
						</table>';
        }

		else{
            $content .= '<!--<input type="submit" name="submit" value="Pay by PayPal">-->';
        }
        $content .= '</form>';
        if ( $redirect ) {
            $content .='<script>document.paymentForm.submit();</script>';
        }

		$_SESSION["myid"] = $uid; 
        return $content;    
    }
	
	function getFEDataRenew($items, $tax, $redirect , $profile, $plugin_conf, $configs){
		$page_itemid = JRequest::getInt('Itemid','0');
		$db =& JFactory::getDBO();
		
		if($page_itemid != '0'){
			$Itemid = "&Itemid=".$page_itemid;
		}
		else{
			$Itemid = NULL;
		}
	
        $db = JFactory::getDBO();
		$mosConfig_live_site = JURI::base(); 
   		$cust_info = $profile;
		$configs->currency = $tax['currency'];
	    $tax1 = $tax;
        
        //check to see if this client has purchased this product
        $sid = $profile->_sid;
        $uid = $profile->_user_id;
        
		@session_start();
		$_SESSION['order_accepted'] = 0;
		//paypal have no "failed url, but only url for cancel payment

		$failed_url = $mosConfig_live_site.'index.php?option=com_adagency&controller=adagencyCampaigns&task=failPayment&plugin=paypal&custom='.$uid.';'.$sid.$Itemid;

		if($plugin_conf->sandbox == 1){
			$paypal_url = '<form action="https://www.sandbox.paypal.com/cgi-bin/webscr" method="post" name="paymentForm">';
		}	
		else{
			$paypal_url = '<form action="https://www.paypal.com/cgi-bin/webscr" method="post" name="paymentForm">';
		}	
        $content = $paypal_url.'
			<input type="hidden" name="cmd" value="_xclick-subscriptions">
            <input type="hidden" name="business" value="'.$plugin_conf->config->data['paypal_email'].'">';
		$i = 0;
		$total1 = 0;
	
		foreach($items as $i => $item){
			if($i < 0){
				continue;
			}
			++$i;
			$tid = JRequest::getVar("tid", "0");
			$sql = "select validity from #__ad_agency_order_type where tid=".intval($tid);
			$db->setQuery($sql);
			$db->query();
			$validity = $db->loadColumn();
			$validity = $validity["0"];
			
			$validity = explode("|", $validity);
			$period = intval($validity["0"]);
			$time_string = trim($validity["1"]);
			$time_char = "";
			switch($time_string){
				case "month" : $time_char = "M"; break;
				case "day" : $time_char = "D"; break;
				case "week" : $time_char = "W"; break;
				case "year" : $time_char = "Y"; break;
			}
			
            $price = $item->amount/$item->quantity;
			$content .= '<input type="hidden" name="item_name" value="'.str_replace('"', "'", $item->name).'">';
			$content .= '<input type="hidden" name="item_number" value="'.$item->quantity.'">';
			$content .= '<!-- Regular subscription price. -->
						 <input type="hidden" name="a3" value="'.sprintf("%.2f", $price).'"> 
						 <!--
							Subscription duration. Specify an integer value in the allowable range for the units of duration that you specify with t3. 
						 -->
						 <input type="hidden" name="p3" value="'.$period.'">  
						 
						 <!-- 
							Regular subscription units of duration.
							Allowable values are:
							D – for days; allowable range for p3 is 1 to 90
							W – for weeks; allowable range for p3 is 1 to 52
							M – for months; allowable range for p3 is 1 to 24
							Y – for years; allowable range for p3 is 1 to 5
						 -->
						 <input type="hidden" name="t3" value="'.$time_char.'">  
						 
						 <!-- Set recurring payments until canceled. -->  
						 <input type="hidden" name="src" value="1">
			';
			//for 2 payment
			//<!-- Billing 2 times, thus at first and 1 recurring -->
			//<input type="hidden" name="srt" value="2">
        }
        $tax = $tax['value'];

		$content .= '
            <input type="hidden" name="currency_code" value="'.$configs->currency.'">
            <input type="hidden" name="notify_url" value="'.$mosConfig_live_site.'index.php?option=com_adagency&controller=adagencyCampaigns&task=notifyPayment&plugin=paypal&no_html=1&custom='.$uid.';'.$sid.'">
            <input type="hidden" name="return" value="'.$mosConfig_live_site.'index.php?option=com_adagency&controller=adagencyCampaigns&task=returnPayment&plugin=paypal&custom='.$uid.';'.$sid.$Itemid.'">
            <input type="hidden" name="cancel_return" value="'.$failed_url.'">
            <input type="hidden" name="custom" value="'.$uid.';'.$sid.';">
            <img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.png" width="1" height="1">
        ';

        if($redirect){
            $content .= '<input type="image" src="https://www.paypal.com/en_US/i/btn/x-click-but6.png" border="0" name="submit" alt="'.(JText::_('DSPAYMENT_WITH_PAYPAL')).'">';
        }
		
        $content .= '</form>';
		if($redirect){
            $content .='<script>document.paymentForm.submit();</script>';
        }

		$_SESSION["myid"] = $uid; 
        return $content;    
    }
    
    function getIP() {
		if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		else if (isset($_SERVER['REMOTE_ADDR'])) $ip = $_SERVER['REMOTE_ADDR'];
		else $ip = "UNKNOWN";
		return $ip;
	}  
    
    function whoIs() {
    	// Get the Internet host name corresponding to a given IP address 
    	return gethostbyaddr($this->getIp());
    }
    
    function checkIdentity($who_is){
    	// Check the server host name to see if it matches
    	$pattern = "/$notify.paypal.com/";
    	if(preg_match($pattern, $who_is)) {
        	return true;
        } else {
        	return false;
        }
    }
    
    function checkAndUpdate() {
        $db = & JFactory::getDBO();
        $post = JRequest::get('post');
        $get = JRequest::get('get');
		
	    // Check if server is indeed PayPal or not
    	$is_paypal = $this->checkIdentity($this->whoIs());        
        if(!$is_paypal) {
        	return false;
        }
        // Check the payment status
        if(strtolower($post['payment_status']) != 'completed') {
			return false;
        }

        $cost = $post['mc_gross'];
        $currency = $post['mc_currency'];
        $data = explode(";", $get['custom']);
        $order_id = (int)$data["1"];
        $user_id = (int)$data["0"];
        
        $sql = "SELECT `aid` FROM #__ad_agency_advertis WHERE user_id = $user_id ";
        $db->setQuery($sql);
        $advertiser_id = $db->loadColumn();
		$advertiser_id = $advertiser_id["0"];
       	

		$sql = "
					SELECT card_number FROM #__ad_agency_order 
					WHERE oid = $order_id 
					AND aid = $advertiser_id 
					AND cost = $cost
					AND currency = '$currency'
					AND order_date BETWEEN curdate()-interval 1 day AND curdate() 
					AND payment_type = 'paypal_payment' 
					AND status = 'not_paid' 
                   ";
		$sql_pack[] = $sql;
		$db->setQuery($sql);
		$details = $db->loadColumn();
		$details = $details["0"];

		// If there is an order with those exact details
        if($details != NULL) {
        	// Update the order status, set it to 'paid'
        	$sql = "UPDATE #__ad_agency_order SET status = 'paid' WHERE oid = $order_id ";
            $sql_pack[] = $sql;
            $db->setQuery($sql);
            $db->query();
            
            $details2 = explode(";", $details);
            $campaign_id = (int)$details2[0];
            
			//change campaign date expiration
			$sql = "select `tid` from #__ad_agency_order WHERE `oid` = ".intval($order_id);
			$db->setQuery($sql);
            $db->query();
			$tid = $db->loadColumn();
			$tid = $tid["0"];
			
			$sql = "select `quantity`, `validity` from #__ad_agency_order_type where `tid`=".intval($tid);
			$db->setQuery($sql);
            $db->query();
			$tid_details = $db->loadAssocList();
			if(isset($tid_details) && count($tid_details) > 0){
				$validity = $tid_details["0"]["validity"];
				$quantity = intval($tid_details["0"]["quantity"]);
				
				//$start_date = date("Y-m-d H:i:s");
				$jnow = &JFactory::getDate();
				$start_date = $jnow->toMySQL();
				
				$end_date = "0000-00-00 00:00:00";
				
				if(trim($validity) != ""){
					$validity_array = explode("|", $validity);
					$today = strtotime($start_date);
					$end_date_int = strtotime("+".$validity_array["0"]." ".$validity_array["1"], $today);
					$end_date = date("Y-m-d H:i:s", $end_date_int);
				}
				$sql = "update #__ad_agency_campaign set `start_date`='".trim($start_date)."', `quantity`=".intval($quantity).", `validity`='".$end_date."' where id=".intval($campaign_id);
				$db->setQuery($sql);
            	$db->query();
			}
			//change campaign date expiration
			
            if($campaign_id > 0) {
            	// Get the auto-approve settings of the advertiser
                $sql = "SELECT `apr_cmp` FROM #__ad_agency_advertis WHERE aid = $advertiser_id ";
                $sql_pack[] = $sql;
            	$db->setQuery($sql);
                $auto_approve = $db->loadColumn();
				$auto_approve = $auto_approve["0"];
               
				// If local advertiser setting is set to "global"
                // or is not set take the global settings
           		if($auto_approve == NULL || $auto_approve == 'G') {
					$sql = "SHOW columns FROM #__ad_agency_campaign WHERE field='approved' ";
					$sql_pack[] = $sql;
					$db->setQuery($sql);
					$result = $db->loadRow();
					$auto_approve = $result[4];              
                }

				// Update the campaign if auto-approve set
	            if($auto_approve == 'Y') {
	                $sql = "UPDATE `#__ad_agency_campaign` SET `approved` = 'Y' WHERE `id` = '".$campaign_id."' ";
	                $sql_pack[] = $sql;
	                $db->setQuery($sql);
	                $db->query();
	            }
            }
			
			$sql = "select `activities` from #__ad_agency_campaign WHERE `id`=".intval($campaign_id);
			$db->setQuery($sql);
			$db->query();
			$activities = $db->loadColumn();
			$activities = $activities["0"];
			
			$sql = "";
			if(strpos($activities, "Purchased") === FALSE){
				$sql = "UPDATE `#__ad_agency_campaign` SET `activities` = concat(activities, 'Purchased(new) - ".date("Y-m-d H:i:s")."', ' - ".intval($user_id)."', ';') WHERE `id` = '".intval($campaign_id)."' ";
			}
			else{
				$sql = "UPDATE `#__ad_agency_campaign` SET `activities` = concat(activities, 'Purchased(renewal) - ".date("Y-m-d H:i:s")."', ' - ".intval($user_id)."', ';') WHERE `id` = '".intval($campaign_id)."' ";
			}
			$db->setQuery($sql);
			$db->query();
            
            return true;
		}

        return false;
    }

	function checkAndUpdate2(){
		$db = & JFactory::getDBO();
		
	    // Check if server is indeed PayPal or not
    	$is_paypal = $this->checkIdentity($this->whoIs());        
        if(!$is_paypal) {
        	//return false;     ionut
        }
        // Check the payment status
        if(strtolower($post['payment_status']) != 'completed') {
			//return false;    ionut
        }
		
		$from_req = JRequest::getVar("custom", "0;0");
		$from_req = explode(";", $from_req);
		$user_id = intval($from_req["0"]);
		$order_id = intval($from_req["1"]);
		
		if(isset($order_id) && $order_id != "0"){
			$sql = "select `card_number` from #__ad_agency_order where `oid`=".intval($order_id);
			$db->setQuery($sql);
			$db->query();
			$card_number = $db->loadColumn();
			$card_number = $card_number["0"];
			
			$card_number = explode(";", $card_number);
			$campaign_id = intval($card_number["0"]);
			$new_campaign_id = 0;
			if(isset($campaign_id) && intval($campaign_id) != "0"){
				$sql = "select * from #__ad_agency_campaign where id=".intval($campaign_id);
				$db->setQuery($sql);
				$db->query();
				$result = $db->loadAssocList();
				
				//$start_date = date("Y-m-d H:i:s");
				$jnow = &JFactory::getDate();
				$start_date = $jnow->toMySQL();
				

				$validity = "0000-00-00 00:00:00";
				
				if($result["0"]["type"] == "fr"){//with valability
					$sql = "select ot.`validity` from #__ad_agency_order_type ot, #__ad_agency_campaign c where c.otid=ot.tid and c.id=".intval($campaign_id);
					$db->setQuery($sql);
					$db->query();
					$validity = $db->loadColumn();
					$validity = $validity["0"];
					
					$validity = explode("|", $validity);
					$number_time = intval($validity["0"]);
					$format_time = trim($validity["1"]);
					$plus = "";
					switch($format_time){
						case "day" : $plus = "day"; break;
						case "week" : $plus = "week"; break;
						case "month" : $plus = "months"; break;
						case "year" : $plus = "year"; break;
					}
					$validity = strtotime("+".$number_time." ".$plus, strtotime($start_date));
					$validity = date("Y-m-d H:i:s", $validity);
				}
				//start copy campaign -------------------
				$campaign_name = $result["0"]["name"];
				$new_campaign_name = $campaign_name." round 2";
				$sql = "select `name` from #__ad_agency_campaign where name like '".$campaign_name." round%'";
				$db->setQuery($sql);
				$db->query();
				$result = $db->loadColumn();
				$result = $result["0"];
				
				if(isset($result) && trim($result) != ""){
					$temp = explode("round", trim($result));
					$int = intval($temp["1"]);
					$int ++;
					$new_campaign_name = $campaign_name." round ".$int;
				}
				
				$sql = "insert into #__ad_agency_campaign(`aid`, `name`, `notes`, `default`, `start_date`, `type`, `quantity`, `validity`, `cost`, `otid`, `approved`, `status`, `exp_notice`, `key`, `params`, `renewcmp`) values (".$result["0"]["aid"].", '".$new_campaign_name."', '".$result["0"]["notes"]."', '".$result["0"]["default"]."', '".$start_date."', '".$result["0"]["type"]."', ".$result["0"]["quantity"].", '".$validity."', '".$result["0"]["cost"]."', ".$result["0"]["otid"].", '".$result["0"]["approved"]."', ".$result["0"]["status"].", ".$result["0"]["exp_notice"].", '', '".$result["0"]["params"]."', '".$result["0"]["renewcmp"]."')";
				$db->setQuery($sql);
				if($db->query()){
					$sql = "select max(`id`) from #__ad_agency_campaign";
					$db->setQuery($sql);
					$db->query();
					$new_campaign_id = $db->loadColumn();
					$new_campaign_id = $new_campaign_id["0"];
				}
				//stop copy campaign -------------------
				
				//start assign benners to new campaign -------------------
				$sql = "select * from #__ad_agency_campaign_banner where campaign_id=".intval($campaign_id);
				$db->setQuery($sql);
				$db->query();
				$result = $db->loadAssocList();
				if(isset($result) && count($result) > 0){
					foreach($result as $key=>$banner){
						$sql = "insert into #__ad_agency_campaign_banner(`campaign_id`, `banner_id`, `relative_weighting`, `thumb`, `zone`) values (".$new_campaign_id.", ".$banner["banner_id"].", ".$banner["relative_weighting"].", '".$banner["thumb"]."', ".$banner["zone"].")";
						$db->setQuery($sql);
						$db->query();
					}
				}
				//stop assign benners to new campaign -------------------
				
				//start create new order --------------------------------
				$sql = "select * from #__ad_agency_order where oid=".intval($order_id);
				$db->setQuery($sql);
				$db->query();
				$result = $db->loadAssocList();
				$card_number = $result["0"]["card_number"];
				$card_number = str_replace($campaign_id.";", $new_campaign_id.";", $card_number);
				$sql = "insert into #__ad_agency_order(`tid`, `aid`, `type`, `quantity`, `cost`, `order_date`, `payment_type`, `card_number`, `expiration`, `card_name`,  `notes`, `status`, `pack_id`, `currency`) values (".$result["0"]["tid"].", ".$result["0"]["aid"].", '".$result["0"]["type"]."', ".$result["0"]["quantity"].", '".$result["0"]["cost"]."', '".date("Y-m-d H:i:s")."', '".$result["0"]["payment_type"]."', '".$card_number."', '".$result["0"]["expiration"]."', '".$result["0"]["card_name"]."', '".$result["0"]["notes"]."', '".$result["0"]["status"]."', '".$result["0"]["pack_id"]."', '".$result["0"]["currency"]."')";
				$db->setQuery($sql);
				$db->query();
				//stop create new order ---------------------------------
			}
			return true;
		}
		
		return false;
    }

    function notify($plugin_conf, $cart, $configs, $plugin_handler){
		$txn_type = JRequest::getVar("txn_type", "");
		if($txn_type != "subscr_payment"){//first payment
			$this->checkAndUpdate();//if is first payment, not for renew
		}
		else{
			$db =& JFactory::getDBO();
			$data = explode(";", $_GET['custom']);
			$order_id = $data["1"];
			$sql = "select `order_date` from #__ad_agency_order where oid=".intval($order_id);
			$db->setQuery($sql);
			$db->query();
			$order_date = $db->loadColumn();
			$order_date = $order_date["0"];
			
			$now = strtotime(date("Y-m-d"));
			$order_date_12 = strtotime("+24 hour", strtotime($order_date));
			if($now < $order_date_12){
				$this->checkAndUpdate();//if is first payment, not for renew
			}
			else{
				$this->checkAndUpdate2();//if is second payment, for renew
			}
		}
		die();
	}

    function return1 ($plugin_conf, $cart, $configs, $plugin_handler) {    
		$get = JRequest::get('get');		
        $data = explode(";", $get['custom']);
        $sid = $data[1];
		if (!$sid) {
        	echo JText::_('ADAG_PAYPAL_NO_SID');
            die();
        } else {
			$plugin_handler->goToSuccessURL($sid);
        }
	 	return;
    }

    function return2 ($plugin_conf, $cart, $configs, $plugin_handler) {
        $data = explode(";", $_GET['custom']);
        $now = time();
        $sid = $data[1];
		if (!$sid) {
        	echo JText::_('ADAG_PAYPAL_NO_SID');
            die();        	
		} else {
            /* When returning (cancel go back) we delete the order & campaign details */
			$db = &JFactory::getDBO();
            $user =& JFactory::getUser();
            
            $sql = "SELECT `card_number` FROM #__ad_agency_order WHERE oid = $sid AND status = 'not_paid' ";
            $db->setQuery($sql);
            $details = $db->loadColumn();
			$details = $details["0"];
			
            if($details != NULL) {
                $details2 = explode(";", $details);
                $campaign_id = $details2["0"];
                
                // Get advertiser details
                $sql = "SELECT `aid` FROM #__ad_agency_advertis WHERE user_id = $user->id ";
                $db->setQuery($sql);
                $advertiser_id = $db->loadColumn();
				$advertiser_id = $advertiser_id["0"];
                
                // Delete the order
                $sql = "DELETE FROM #__ad_agency_order WHERE oid = $sid AND aid = $advertiser_id ";
                $db->setQuery($sql);
                $db->query();
                
                // Delete the banners associated with the campaign
                //$sql = "DELETE FROM #__ad_agency_campaign_banner WHERE campaign_id = $campaign_id AND aid = $advertiser_id ";
				$sql = "DELETE FROM #__ad_agency_campaign_banner WHERE campaign_id = $campaign_id";
                $db->setQuery($sql);
                $db->query();
                
                // Delete the campaign
                $sql = "DELETE FROM #__ad_agency_campaign WHERE id = $campaign_id AND approved = 'P' AND aid = $advertiser_id ";
                $db->setQuery($sql);
                $db->query();
            }
			
            $plugin_handler->goToFailedURL($sid);
        }      
        return;
    }
   
    function get_info () {
        $info = _PLUGIN_PAYPAL_PAYMENT;
        return $info;
    }

	function get_account () {
		global $database;
		$sql = "select `value` from #__adagency_plugins where name='".get_class($this)."'";	
		$database->setQuery($sql);
		$account = $database->loadColumn();
		$account = $account["0"];
		return $account;
	}

    function old_notify_function($plugin_conf, $cart, $configs, $plugin_handler) {
		        
        $req = 'cmd=_notify-validate';

        foreach ( $_POST as $key => $value ) {
            $value = urlencode(stripslashes( $value ) );
            $req .= "&$key=$value";
        }
        // post back to PayPal system to validate
		if ($plugin_conf->sandbox == 0) {
			$url = "www.paypal.com:443";

        	$fp = fsockopen ("ssl://www.paypal.com", 443, $errno, $errstr, 30);	
        	
        } else {
			$url = "www.sandbox.paypal.com:443";
			$fp = fsockopen ("ssl://www.sandbox.paypal.com", 443, $errno, $errstr, 30);        	
        	
        }
        
        $header = "POST /cgi-bin/webscr HTTP/1.0\r\n";
        $header .= "Host: ".$url."\r\n";
        $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
        $header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
   
		$this->writeto('administrator/components/com_adagency/plugins/paypal.log', 'Header: '.$header);
		$this->writeto('administrator/components/com_adagency/plugins/paypal.log', 'REQ: '.$req);
		$this->writeto('administrator/components/com_adagency/plugins/paypal.log', 'REQUEST: '.$_REQUEST);

		if (!$fp) {
			$this->writeto('administrator/components/com_adagency/plugins/paypal.log', 'Failed is REQUEST (can\'t connect to paypal for ipn): '.print_r($_REQUEST, true));
			//can't connect to paypal for ipn 
		} else { 
			fputs ($fp, $header );
			fputs ($fp, $req);

			while (!feof($fp)) {
				$res = fgets ($fp, 1024);
				//fputs($file,$res);
				$this->writeto('administrator/components/com_adagency/plugins/paypal.log', 'Get from Paypal (res): '.str_replace("\r",'',str_replace("\n",'',$res)));
				if (strcmp ($res, "VERIFIED") == 0) {
		        	$data = explode(";", $_REQUEST['custom']);
			        //data: userid,sid
			        //save data
			        $now = time();
			        $sid = $data[1];
			        $my->id = $data[0];
					$customer = $plugin_handler->loadCustomer($sid);
					if ($customer->_user->id != $data[0]) return;
			        if (!$sid) {
			        	$sid = $_REQUEST['sd_sid']; 
			        }
					$this->writeto('administrator/components/com_adagency/plugins/paypal.log', 'REQUEST: '.print_r($_REQUEST, true));                 
			        $items = $cart->getCartItems( $customer, $configs );			 	 
			        $tax = $cart->calc_price($items, $customer, $configs);			        
			        $non_taxed = $tax['total'];//$total;
			        $total = round($tax['taxed'], 2);
			        $taxa = $tax['value'];
			        $shipping = $tax['shipping'];
			        $currency = $tax['currency'];
			        $licenses = $tax['licenses'];
					$paypal_gross = (float )$_REQUEST["mc_gross"];
					$this->writeto('administrator/components/com_adagency/plugins/paypal.log', 'Customer info: '.print_r($customer, true));
					$this->writeto('administrator/components/com_adagency/plugins/paypal.log', 'Items info: '.print_r($items, true)); 
					$this->writeto('administrator/components/com_adagency/plugins/paypal.log', 'Items info: '.print_r($tax, true));                
					if ($total != $paypal_gross) {
							$this->writeto('administrator/components/com_adagency/plugins/paypal.log', 'Wrong gross: total='.$total.'; mc_gross(paypal)='.$paypal_gross);                
							return false;
					}
					$total = $tax['taxed'];
			         //   $total = round( $total + $vat_tax + $state_tax, 2 );			
			        $orderid = $plugin_handler->addOrder($items, $customer, $now, 'paypal');
					$plugin_handler->addLicenses($items, $orderid, $now, $customer);
			        $plugin_handler->dispatchMail($orderid,$total,$licenses, $now, $items, $customer);                    
			  
				} else if (strcmp ($res, "INVALID") == 0) {
					$this->writeto('administrator/components/com_adagency/plugins/paypal.log', 'INVALID(payment): '.print_r($_REQUEST, true));   
				} else {
					//$this->writeto('administrator/components/com_adagency/plugins/paypal.log', 'Something wrong(payment): '.print_r($_REQUEST, true));   
                }
			}
		}    
    }    
    
};