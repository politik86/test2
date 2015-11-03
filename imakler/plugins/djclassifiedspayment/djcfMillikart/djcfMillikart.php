<?php
/**
* @version		1.0
* @package		DJ Classifieds
* @subpackage	DJ Classifieds Payment Plugin
* @copyright	Copyright (C) 2015 biolev.com, All rights reserved.
* @license		http://www.gnu.org/licenses GNU/GPL
* @autor url    http://biolev.com
* @autor email  info@biolev.com
* @Developer    BioLev - info@biolev.com
* 
* 
* DJ Classifieds is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* DJ Classifieds is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with DJ Classifieds. If not, see <http://www.gnu.org/licenses/>.
* 
*/
defined('_JEXEC') or die('Restricted access');
jimport('joomla.event.plugin');
$lang = JFactory::getLanguage();
$lang->load('plg_djclassifiedspayment_djcfMillikart',JPATH_ADMINISTRATOR);
class plgdjclassifiedspaymentdjcfMillikart extends JPlugin
{
	function plgdjclassifiedspaymentdjcfMillikart( &$subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage('plg_djcfMillikart'); #????????????????
		$params["plugin_name"] = "djcfMillikart";
		$params["icon"] = "millikart_icon.png";
		$params["logo"] = "millikart_overview.png";
		$params["description"] = JText::_("PLG_DJCFMILLIKART_PAYMENT_METHOD_DESC");
		$params["payment_method"] = JText::_("PLG_DJCFMILLIKART_PAYMENT_METHOD_NAME");
		$params["mid"] = $this->params->get("mid");
		$params["currency_code"] = $this->params->get("currency_code");
		$params["key"] = $this->params->get("key");
		$params["testmode"] = $this->params->get("test");
		$this->params = $params;

	}
	function onProcessPayment()
	{
		$ptype = JRequest::getVar('ptype','');
		$id = JRequest::getInt('id','0');
		$html="";

			
		if($ptype == $this->params["plugin_name"])
		{
			$action = JRequest::getVar('pactiontype','');
			$pos = strpos($action, 'notify?reference=');
			if ($pos === false) {
			switch ($action)
			{
				case "process" :
				$html = $this->process($id);
				break;
				case "notify" :
				$html = $this->_notify_url();
				break;
				case "paymentmessage" :
				$html = $this->_paymentsuccess();
				break;
				default :
				$html =  $this->process($id);
				break;
			}
			} else {
				$html = $this->_notify_url();
			}
		}
		return $html;
	}
	function _notify_url()
	{
		$db = JFactory::getDBO();
		$par = JComponentHelper::getParams( 'com_djclassifieds' );
$app = JFactory::getApplication();
		$account_type=$this->params["testmode"];
		$user	= JFactory::getUser();
		
			$action = JRequest::getVar('pactiontype','');
			$pos = strpos($action, 'notify?reference=');
		if ($pos === false) {
			$id	= JRequest::getInt('reference','0');
		} else {
			$idarr	= explode('notify?reference=',$action);
			$id=(int)$idarr[1];
		}

		//$gateway_info = $_POST;

		$rurlmillikart="";
		if ($this->params["testmode"]=="1")
		{
			$rurlmillikart="http://test.millikart.az:8513/gateway/payment/status?mid=".$this->params["mid"]."&reference=".$id;
			$hostmillikart="test.millikart.az";
			$portmillikart=8513;
			$getsmillikart="/gateway/payment/status?mid=".$this->params["mid"]."&reference=".$id;
			//http://test.millikart.az:8513/gateway/payment/status?mid=test&reference=T7D3EDB885A5BC3C
		}
		elseif ($this->params["testmode"]=="0")
		{
			$rurlmillikart="https://pay.millikart.az/gateway/payment/status?mid=".$this->params["mid"]."&reference=".$id;
			$hostmillikart="ssl://pay.millikart.az";
			$portmillikart=443;
			$getsmillikart="/gateway/payment/status?mid=".$this->params["mid"]."&reference=".$id;
			//https://pay.millikart.az/gateway/payment/status?mid=test&reference=T7D3EDB885A5BC3C
		}

		$returnstatus = file_get_contents($rurlmillikart);

/*
	$ch = curl_init();

    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $rurlmillikart);

    $returnstatus = curl_exec($ch);
    curl_close($ch);
*/




echo $returnstatus;

print_r($rurlmillikart);

		$pos = strpos($returnstatus, '<RC>000</RC>');


				$query = "SELECT p.*  FROM #__djcf_payments p "
						."WHERE p.id='".$id."' ";
				$db->setQuery($query);
				$payment = $db->loadObject();
		if ($pos === false) {
			$message=JTExt::_('PLG_DJCFMILLIKART_AFTER_FAILED_MSG');
			
			$redirect= 'index.php?option=com_djclassifieds&view=item&cid=0&id='.$payment->item_id;
			$app->redirect($redirect, $message);
		} else {

				$query = "UPDATE #__djcf_payments SET status='Completed',transaction_id='".$id."' "
						."WHERE id=".$id." AND method='djcfMillikart'";
				$db->setQuery($query);
				$db->query();
				
				
					if($payment->type==2){
						$date_sort = date("Y-m-d H:i:s");
						$query = "UPDATE #__djcf_items SET date_sort='".$date_sort."' "
								."WHERE id=".$payment->item_id." ";
						$db->setQuery($query);
						$db->query();
					}else if($payment->type==1){			

						$query = "SELECT p.points  FROM #__djcf_points p WHERE p.id='".$payment->item_id."' ";
						$db->setQuery($query);
						$points = $db->loadResult();
				
						$query = "INSERT INTO #__djcf_users_points (`user_id`,`points`,`description`) "
								."VALUES ('".$payment->user_id."','".$points."','".JText::_('COM_DJCLASSIFIEDS_POINTS_PACKAGE')." Millikart <br />".JText::_('COM_DJCLASSIFIEDS_PAYMENT_ID').' '.$payment->id."')";
						$db->setQuery($query);
						$db->query();
					}else{
						$query = "SELECT c.*  FROM #__djcf_items i, #__djcf_categories c "
								."WHERE i.cat_id=c.id AND i.id='".$payment->item_id."' ";
						$db->setQuery($query);
						$cat = $db->loadObject();
				
						$pub=0;
						if(($cat->autopublish=='1') || ($cat->autopublish=='0' && $par->get('autopublish')=='1')){
							$pub = 1;
						}

                        $query='SELECT promotions FROM mj63c_djcf_items where id='.$payment->item_id.' ORDER BY id ASC LIMIT 1 ';
                        $db->setQuery($query);
                        $current_promotion = $db->loadResult();


                        $query='SELECT pay_type FROM mj63c_djcf_items where id='.$payment->item_id.' ORDER BY id ASC LIMIT 1 ';
                        $db->setQuery($query);
                        $current_pt = $db->loadResult();

                        
                        if (strpos($current_promotion,$current_pt)==0){
                            $current_promotion = $current_promotion.','.$current_pt;
                        }
                        
                        //$current_promotion = 'p_bold';
						$query = "UPDATE #__djcf_items SET payed=1, promotions='$current_promotion', pay_type='', published='".$pub."' "
								."WHERE id=".$payment->item_id." ";
						$db->setQuery($query);
						$db->query();
					}
			
			$message=JTExt::_('PLG_DJCFMILLIKART_AFTER_SUCCESSFULL_MSG');
			$redirect= 'index.php?option=com_djclassifieds&view=item&cid=0&id='.$payment->item_id;
			$app->redirect($redirect, $message);
		}
	}
	
	function process($id)
	{
		JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.DS.'tables');		
		jimport( 'joomla.database.table' );
		$db 	= JFactory::getDBO();
		$app 	= JFactory::getApplication();
		$Itemid = JRequest::getInt("Itemid",'0');
		$par 	= JComponentHelper::getParams( 'com_djclassifieds' );
		$user 	= JFactory::getUser();
		$ptype	= JRequest::getVar('ptype');
		$type	= JRequest::getVar('type','');
		$row 	= JTable::getInstance('Payments', 'DJClassifiedsTable');	

		 if($type=='prom_top'){        	        	
        	$query ="SELECT i.* FROM #__djcf_items i "
        			."WHERE i.id=".$id." LIMIT 1";
        	$db->setQuery($query);
        	$item = $db->loadObject();
        	if(!isset($item)){
        		$message = JText::_('COM_DJCLASSIFIEDS_WRONG_AD');
        		$redirect="index.php?option=com_djclassifieds&view=items&cid=0";
        	}        						 
        					 
       		$row->item_id = $id;
       		$row->user_id = $user->id;
      		$row->method = $ptype;
       		$row->status = 'Start';
      		$row->ip_address = $_SERVER['REMOTE_ADDR'];
       		$row->price = $par->get('promotion_move_top_price',0);
       		$row->type=2;        	
       		$row->store();

       		$amount = $par->get('promotion_move_top_price',0);
      		$itemname = $item->name;
       		$item_id = $row->id;
       		$item_cid = '&cid='.$item->cat_id;       	
        }else if($type=='points'){
			$query ="SELECT p.* FROM #__djcf_points p "				   
				    ."WHERE p.id=".$id." LIMIT 1";
			$db->setQuery($query);
			$points = $db->loadObject();
			if(!isset($item)){
				$message = JText::_('COM_DJCLASSIFIEDS_WRONG_POINTS_PACKAGE');
				$redirect="index.php?option=com_djclassifieds&view=items&cid=0";
			}			
				$row->item_id = $id;
				$row->user_id = $user->id;
				$row->method = $ptype;
				$row->status = 'Start';
				$row->ip_address = $_SERVER['REMOTE_ADDR'];
				$row->price = $points->price; 
				$row->type=1;
				
				$row->store();		
			
			$amount = $points->price;
			$itemname = $points->name;
			$item_id = $row->id;
			$item_cid = '';
		}else{
			$query ="SELECT i.*, c.price as c_price FROM #__djcf_items i "
				    ."LEFT JOIN #__djcf_categories c ON c.id=i.cat_id "
				    ."WHERE i.id=".$id." LIMIT 1";
			$db->setQuery($query);
			$item = $db->loadObject();
			if(!isset($item)){
				$message = JText::_('COM_DJCLASSIFIEDS_WRONG_AD');
				$redirect="index.php?option=com_djclassifieds&view=items&cid=0";
			}
			
				$amount = 0;
				
				if(strstr($item->pay_type, 'cat')){			
					$amount += $item->c_price/100; 
				}
				if(strstr($item->pay_type, 'duration_renew')){			
					$query = "SELECT d.price_renew FROM #__djcf_days d "
							."WHERE d.days=".$item->exp_days;
					$db->setQuery($query);
					$amount += $db->loadResult();
				}else if(strstr($item->pay_type, 'duration')){			
					$query = "SELECT d.price FROM #__djcf_days d "
					."WHERE d.days=".$item->exp_days;
					$db->setQuery($query);
					$amount += $db->loadResult();
				}
				
				$query = "SELECT p.* FROM #__djcf_promotions p "
					."WHERE p.published=1 ORDER BY p.id ";
				$db->setQuery($query);
				$promotions=$db->loadObjectList();
				foreach($promotions as $prom){
					if(strstr($item->pay_type, $prom->name)){	
						$amount += $prom->price; 
					}	
				}
			
				/*$query = 'DELETE FROM #__djcf_payments WHERE item_id= "'.$id.'" ';
				$db->setQuery($query);
				$db->query();
				
				
				$query = 'INSERT INTO #__djcf_payments ( item_id,user_id,method,  status)' .
						' VALUES ( "'.$id.'" ,"'.$user->id.'","'.$ptype.'" ,"Start" )'
						;
				$db->setQuery($query);
				$db->query();*/
				
					$row->item_id = $id;
					$row->user_id = $user->id;
					$row->method = $ptype;
					$row->status = 'Start';
					$row->ip_address = $_SERVER['REMOTE_ADDR'];
					$row->price = $amount;
					$row->type=0;
				
				$row->store();					
			
		
		
			$itemname = $item->name;
			$item_id = $row->id;
			$item_cid = '&cid='.$item->cat_id;
		}
		$amount = $amount*100;	
		$urlmillikart="";
		if ($this->params["testmode"]=="1")
		{
			$urlmillikart="http://test.millikart.az:8513/gateway/payment/register";
			//?mid=Test&amount=5025&currency=944&description=test1000&reference=T7D3EDB885A5BC3C&language=az&signature=055ccfa78337f1d876aba14d6f64c2df
		}
		elseif ($this->params["testmode"]=="0")
		{
			$urlmillikart="https://pay.millikart.az/gateway/payment/register";
		}
		header("Content-type: text/html; charset=utf-8");
		echo JText::_('PLG_DJCFMILLIKART_REDIRECTING_PLEASE_WAIT');

    $rus = array('А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ъ', 'Ы', 'Ь', 'Э', 'Ю', 'Я', 'а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ы', 'ь', 'э', 'ю', 'я');
    $lat = array('A', 'B', 'V', 'G', 'D', 'E', 'E', 'Gh', 'Z', 'I', 'Y', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F', 'H', 'C', 'Ch', 'Sh', 'Sch', 'Y', 'Y', 'Y', 'E', 'Yu', 'Ya', 'a', 'b', 'v', 'g', 'd', 'e', 'e', 'gh', 'z', 'i', 'y', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'h', 'c', 'ch', 'sh', 'sch', 'y', 'y', 'y', 'e', 'yu', 'ya');
    $itemname = str_replace($rus,$lat,$itemname);


		$form ='<form id="millikartform" action="'.$urlmillikart.'" method="get">';
		$form .='<input type="hidden" name="redirect" value="1">';
		$form .='<input type="hidden" name="mid" value="'.$this->params["mid"].'">';
		$form .='<input type="hidden" name="amount" value="'.$amount.'">';
		$form .='<input type="hidden" name="currency" value="'.$this->params["currency_code"].'">';
		$form .='<input type="hidden" name="description" value="'.$itemname.'">';
		$form .='<input id="custom" type="hidden" name="reference" value="'.$item_id.'">';
		$form .='<input TYPE="hidden" name="language" value="az">';
		#http://imakler.az/index.php?option=com_djclassifieds&task=processPayment&ptype=djcfMillikart&pactiontype=notify
		#http://imakler.az/index.php?option=com_djclassifieds&task=paymentReturn&r=error
		//$form .='<input type="hidden" name="gateway.merchants.url" value="'.JRoute::_(JURI::root().'index.php?option=com_djclassifieds&task=processPayment&ptype='.$this->params["plugin_name"].'&pactiontype=notify&id='.$item_id.'&Itemid='.$Itemid).'">';
        //$signature = md5(strlen($mid).$mid.strlen($amount).$amount.strlen($currency).$currency.(!empty($description)? strlen($description).$description :"0").strlen($reference).$reference.strlen($language).$language.$key);
		$signature = mb_strtoupper(md5(strlen($this->params["mid"]).$this->params["mid"].strlen($amount).$amount.strlen($this->params["currency_code"]).$this->params["currency_code"].(!empty($itemname)? strlen($itemname).$itemname :"0").strlen($item_id).$item_id.strlen('az').'az'.$this->params["key"]));
		$form .='<input TYPE="hidden" name="signature" value="'.$signature.'">';
		$form .='</form>';
		echo $form;
	?>
		<script type="text/javascript">
			callpayment()
			function callpayment(){
				var id = document.getElementById('custom').value ;
				if ( id > 0 && id != '' ) {
					document.getElementById('millikartform').submit();
				}
			}
		</script>
	<?php
	}

	function onPaymentMethodList($val)
	{
		$type='';
		if($val['type']){
			$type='&type='.$val['type'];	
		}		
		$html ='';
		if($this->params["key"]!=''){
			$paymentLogoPath = JURI::root()."plugins/djclassifiedspayment/".$this->params["plugin_name"]."/".$this->params["plugin_name"]."/images/".$this->params["logo"];
			$form_action = JRoute :: _("index.php?option=com_djclassifieds&task=processPayment&ptype=".$this->params["plugin_name"]."&pactiontype=process&id=".$val["id"].$type, false);
			$html ='<table cellpadding="5" cellspacing="0" width="100%" border="0">
				<tr>';
					if($this->params["logo"] != ""){
				$html .='<td class="td1" width="160" align="center">
						<img src="'.$paymentLogoPath.'" title="'. $this->params["payment_method"].'"/>
					</td>';
					 }
					$html .='<td class="td2">
						<h2>Millikart</h2>
						<p style="text-align:justify;">'.$this->params["description"].'</p>
					</td>
					<td class="td3" width="130" align="center">
						<a class="button" style="text-decoration:none;" href="'.$form_action.'">'.JText::_('COM_DJCLASSIFIEDS_BUY_NOW').'</a>
					</td>
				</tr>
			</table>';
		}
		return $html;
	}
}

?>
