<?php
/**
* @version		2.0
* @package		DJ Classifieds
* @subpackage	DJ Classifieds Component
* @copyright	Copyright (C) 2010 DJ-Extensions.com LTD, All rights reserved.
* @license		http://www.gnu.org/licenses GNU/GPL
* @autor url    http://design-joomla.eu
* @autor email  contact@design-joomla.eu
* @Developer    Lukasz Ciastek - lukasz.ciastek@design-joomla.eu
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
defined ('_JEXEC') or die('Restricted access');
JHTML::_('behavior.framework' );
JHTML::_('behavior.formvalidation');
JHTML::_('behavior.modal');
$par = JComponentHelper::getParams( 'com_djclassifieds' );
$points_a = $par->get('points',0);
$points_total= $par->get('promotion_move_top_points',0);


$item_id = JRequest::getVar('Itemid');
$prom_type = JRequest::getVar('type');

print_r($prom_type);
$db =& JFactory::getDBO();

/*��� ��� ������� ��� ��� ��������� ������ � ��������������� �� �������� ������*/

$item_id = JRequest::getVar('Itemid');
$prom_type = JRequest::getVar('type');


$db =& JFactory::getDBO();

$query='SELECT promotions FROM mj63c_djcf_items where id='.$item_id.' ORDER BY id ASC LIMIT 1 ';
$db->setQuery($query);
$current_promotion = $db->loadResult();

$query='SELECT pay_type FROM mj63c_djcf_items where id='.$item_id.' ORDER BY id ASC LIMIT 1 ';
$db->setQuery($query);
$current_pay_type = $db->loadResult();

if ($prom_type=="prom_top"){
    if (strpos($current_promotion,'p_first')==0){
        $promotion_str = "p_first";
        if (strpos($current_promotion, 'p_bold')!=0){
            $promotion_str = $promotion_str.',p_bold';
        }
        if (strpos($current_promotion, 'p_special')!=0){
            $promotion_str = $promotion_str.',p_special';
        }
    }

    if (strpos($current_pay_type,'p_first')==0){
        $pay_type_str = "p_first";
        if (strpos($current_pay_type, 'p_bold')!=0){
            $pay_type_str = $pay_type_str.',p_bold';
        }
        if (strpos($current_pay_type, 'p_special')!=0){
            $pay_type_str = $pay_type_str.',p_special';
        }
    }


    $sql = "UPDATE mj63c_djcf_items SET payed=0, pay_type='$pay_type_str'  WHERE id = $item_id ";
    $db->setQuery($sql);{}
    $db->query();      
}

if ($prom_type=="prom_premium"){
    if (strpos($current_promotion,'p_special')==0){
        $promotion_str = "";
        if (strpos($current_promotion, 'p_first')!=0){
            $promotion_str = 'p_first';
        }        
        if (strpos($current_promotion, 'p_bold')!=0){
            $promotion_str = $promotion_str.',p_bold';
        }
        $promotion_str = $promotion_str.',p_special';
    }

    if (strpos($current_pay_type,'p_special')==0){
        $pay_type_str = "";
        if (strpos($current_pay_type, 'p_first')!=0){
            $pay_type_str = 'p_first';
        }
        if (strpos($current_pay_type, 'p_bold')!=0){
            $pay_type_str = $pay_type_str.',p_bold';
        }
        $pay_type_str = $pay_type_str.',p_special';
    }


    $sql = "UPDATE mj63c_djcf_items SET payed=0, pay_type='$pay_type_str'  WHERE id = $item_id ";
    $db->setQuery($sql);{}
    $db->query();     
}

if ($prom_type=="prom_to_mark"){



    if (strpos($current_pay_type,'prom_to_mark')==0){
        $pay_type_str = "";
        if (strpos($current_pay_type, 'p_first')!=0){
            $pay_type_str = 'p_first';
        }
        $pay_type_str = $pay_type_str.',p_bold';
        if (strpos($current_pay_type, 'p_special')!=0){
            $pay_type_str = $pay_type_str.',p_special';
        }
    }

    
    $sql = "UPDATE mj63c_djcf_items SET payed=0, pay_type='$pay_type_str'  WHERE id = $item_id ";

    $db->setQuery($sql);{}
    $db->query();     
}
?>
<div id="dj-classifieds">
	<table cellpadding="0" cellspacing="0" width="98%" border="0" class="paymentdetails first">
		<tr>
			<td class="td_title">
				<h2><?php echo JText::_('COM_DJCLASSIFIEDS_PAYMENT_DETAILS');?></h2>
			</td>
		</tr>
		<tr>
			<td class="td_pdetails">
				<?php 																												
					echo '<div class="pd_row"><span>'.JText::_('COM_DJCLASSIFIEDS_PROMOTION_MOVE_TO_TOP').'</span>';
					echo '<span class="price">'.DJClassifiedsTheme::priceFormat($par->get('promotion_move_top_price',0),$par->get('unit_price','EUR'));
						 if($par->get('promotion_move_top_points',0) && $points_a){
							echo '&nbsp-&nbsp'.$par->get('promotion_move_top_points',0).' '.JTEXT::_('COM_DJCLASSIFIEDS_POINTS_SHORT');
						 }
					echo '</span></div>';
										
					echo '<div class="pd_row_total"><span>'.JText::_('COM_DJCLASSIFIEDS_PRICE').'</span>';
					echo '<span class="price">'.DJClassifiedsTheme::priceFormat($par->get('promotion_move_top_price',0),$par->get('unit_price','EUR'));
						 if($par->get('promotion_move_top_points',0) && $points_a){
							echo '&nbsp-&nbsp'.$par->get('promotion_move_top_points',0).' '.JTEXT::_('COM_DJCLASSIFIEDS_POINTS_SHORT');
						 }
					echo '</span></div>';
					
				?>
			</td>
		</tr>			
	</table>
	<table cellpadding="0" cellspacing="0" width="98%" border="0" class="paymentdetails">
		<tr>
			<td class="td_title">
				<h2><?php echo JText::_("COM_DJCLASSIFIEDS_PAYMENT_METHODS"); ?></h2>
			</td>
		</tr>
		<tr>
			<td class="table_payment">
				<table cellpadding="0" cellspacing="0" width="100%" border="0">
					<?php
					if($points_a && $points_total){ ?>
							<tr>
								<td class="payment_td">
									<table width="100%" cellspacing="0" cellpadding="5" border="0">
										<tr>
											<td width="160" align="center" class="td1">
												<img title="<?php echo JText::_('COM_DJCLASSIFIEDS_POINTS')?>" src="components/com_djclassifieds/assets/images/points.png">
											</td>
											<td class="td2">
												<h2><?php echo JText::_('COM_DJCLASSIFIEDS_POINTS_PACKAGE')?></h2>
												<p style="text-align:left;"><?php echo JText::_('COM_DJCLASSIFIEDS_POINTS_AVAILABLE').': '.$this->user_points;?></p>
											</td>
											<td width="130" align="center" class="td3">
												<?php 
												if($this->user_points>=$points_total){ 
													echo '<a class="button" href="index.php?option=com_djclassifieds&view=payment&task=payPoints&id='.$this->item->id.'&type=prom_top" style="text-decoration:none;">'.JText::_('COM_DJCLASSIFIEDS_USE_POINTS').'</a>';	
												}else{ 
													echo '<a target="_blank" class="button" href="'.JRoute::_('index.php?option=com_djclassifieds&view=points'.$itemid).'" style="text-decoration:none;">'.JText::_('COM_DJCLASSIFIEDS_BUY_POINTS').'</a>';	
												} ?>
												
											</td>
									</tr>
									</table>
								</td>
							</tr>
						<?php }
						$i = 0;					
						foreach($this->PaymentMethodDetails AS $pminfo)
						{
							if($pminfo==''){
								continue;
							}
							//$paymentLogoPath = JURI::root()."plugins/djclassifiedspayment/".$this->plugin_info[$i]->name."/images/".$pminfo["logo"];
							?>
								<tr>
									<td class="payment_td">
										<?php echo $pminfo; ?>
									</td>
								</tr>
							<?php
							$i++;
						}
					?>
				</table>
			</td>
		</tr>
	</table>
</div>