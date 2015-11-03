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
JHTML::_('behavior.framework',true);
JHTML::_('behavior.formvalidation');
$par = JComponentHelper::getParams( 'com_djclassifieds' );
$app = JFactory::getApplication();
$config = JFactory::getConfig();
$user =  JFactory::getUser();
$Itemid = JRequest::getVar('Itemid', 0,'', 'int');
$item = $this->item;
$item_class='';

$icon_new_a	= $par->get('icon_new','1');
$icon_new_date = mktime(date("G"), date("i"), date("s"), date("m"), date("d")-$par->get('icon_new_time','3'), date("Y"));
$date_start = strtotime($item->date_start);
$icon_new=0;
	if($item->promotions){
		$item_class .=' promotion '.str_ireplace(',', ' ', $item->promotions);
	}
	if($date_start>$icon_new_date && $icon_new_a){
		$icon_new=1;
		$item_class .= ' item_new';  
	}
	
	if($par->get('favourite','1') && $user->id>0){
		if($item->f_id){ $item_class .= ' item_fav'; }
	}	

	$menus	= $app->getMenu('site');
	$menu_newad_m = $menus->getItems('link','index.php?option=com_djclassifieds&view=additem',1);
	$menu_newad_itemid='';
	if($menu_newad_m){
		$menu_newad_itemid = '&Itemid='.$menu_newad_m->id;
	}
	
$mod_attribs=array();
$mod_attribs['style'] = 'xhtml';

//ADDED
if((int)$par->get('showaddetails','1') == 1) { $joomadsdetails=' jm-class-details'; } else { $joomadsdetails=''; }
if((int)$par->get('comments','0') != 0) { $joomadscomments=' jm-class-comments'; } else { $joomadscomments=''; }

?>
<div id="dj-classifieds" class="clearfix <?php echo $joomadsdetails.' '.$joomadscomments ?>">
<?php 

echo '<a class="button up_to_top" href="index.php?option=com_djclassifieds&view=payment&id='.$item->id.'&type=prom_top&Itemid='.$item->id.'" >';
								echo JText::_('Поднять вверх');
								echo ' <span>('.DJClassifiedsTheme::priceFormat($par->get('promotion_move_top_price',0),$par->get('unit_price','EUR'));
								if($par->get('promotion_move_top_points',0) && $par->get('points',0)){
									echo '&nbsp-&nbsp'.$par->get('promotion_move_top_points',0).' '.JTEXT::_('COM_DJCLASSIFIEDS_POINTS_SHORT');
								}
							echo ')</span></a>';
						
?>
<?php 
                            echo '<a class="button up_to_top" href="index.php?option=com_djclassifieds&view=payment&id='.$item->id.'&type=prom_premium&Itemid='.$item->id.'" >';
                        	echo JText::_('Сделать премиум');
								echo ' <span>('.DJClassifiedsTheme::priceFormat($par->get('promotion_move_top_price',0),$par->get('unit_price','EUR'));
								if($par->get('promotion_move_top_points',0) && $par->get('points',0)){
									echo '&nbsp-&nbsp'.$par->get('promotion_move_top_points',0).' '.JTEXT::_('COM_DJCLASSIFIEDS_POINTS_SHORT');
								}
							echo ')</span></a>';
						
?>
<?php 

                            echo '<a class="button up_to_top" href="index.php?option=com_djclassifieds&view=payment&id='.$item->id.'&type=prom_to_mark&Itemid='.$item->id.'" >';
                            echo JText::_('Выделить');
                            echo ' <span>('.DJClassifiedsTheme::priceFormat($par->get('promotion_move_top_price',0),$par->get('unit_price','EUR'));
                            if($par->get('promotion_move_top_points',0) && $par->get('points',0)){
                                echo '&nbsp-&nbsp'.$par->get('promotion_move_top_points',0).' '.JTEXT::_('COM_DJCLASSIFIEDS_POINTS_SHORT');
                            }
                            echo ')</span></a>';

?>

	<?php
		$modules_djcf = &JModuleHelper::getModules('djcf-top');
		if(count($modules_djcf)>0){
			echo '<div class="djcf-ad-top clearfix">';
			foreach (array_keys($modules_djcf) as $m){
				echo JModuleHelper::renderModule($modules_djcf[$m],$mod_attribs);
			}
			echo'</div>';		
		}	
		
		$modules_djcf = &JModuleHelper::getModules('djcf-top-cat'.$item->cat_id);
		if(count($modules_djcf)>0){
			echo '<div class="djcf-ad-top-cat clearfix">';
			foreach (array_keys($modules_djcf) as $m){
				echo JModuleHelper::renderModule($modules_djcf[$m],$mod_attribs);
			}
			echo'</div>';		
		}	

		 $modules_djcf = &JModuleHelper::getModules('djcf-item-top');
			if(count($modules_djcf)>0){
				echo '<div class="djcf-ad-item-top clearfix">';
				foreach (array_keys($modules_djcf) as $m){
					echo JModuleHelper::renderModule($modules_djcf[$m],$mod_attribs);
				}
				echo'</div>';		
			}
			
		 $modules_djcf = &JModuleHelper::getModules('djcf-item-top'.$item->id);
			if(count($modules_djcf)>0){
				echo '<div class="djcf-ad-item-top clearfix">';
				foreach (array_keys($modules_djcf) as $m){
					echo JModuleHelper::renderModule($modules_djcf[$m],$mod_attribs);
				}
				echo'</div>';		
			}	

		if($this->item_payments==0){
		 $modules_djcf = &JModuleHelper::getModules('djcf-item-top-free');
			if(count($modules_djcf)>0){
				echo '<div class="djcf-ad-item-top clearfix">';
				foreach (array_keys($modules_djcf) as $m){
					echo JModuleHelper::renderModule($modules_djcf[$m],$mod_attribs);
				}
				echo'</div>';
			}			
		}
			
			?>
			
<div class="dj-item<?php echo $item_class; ?>">
<?php	
	echo '<div class="title_top info"><h2>'.$item->name.'</h2>';
		if($user->id==$item->user_id && $user->id>0){
			echo '<a href="index.php?option=com_djclassifieds&view=additem&id='.$item->id.$menu_newad_itemid.'" class="title_edit button">'.JText::_('COM_DJCLASSIFIEDS_EDIT').'</a>';
		}						
		if($par->get('show_types','0') && $item->type_id>0){
			$registry = new JRegistry();			
			$registry->loadString($item->t_params);			
			$item->t_params = $registry->toObject();
			if($item->t_params->bt_class){
				$bt_class = ' '.$item->t_params->bt_class;
			}else{
				$bt_class = '';
			}			
			if($item->t_params->bt_use_styles){
			 	$style='style="display:inline-block;
			 			border:'.(int)$item->t_params->bt_border_size.'px solid '.$item->t_params->bt_border_color.';'
			 		   .'background:'.$item->t_params->bt_bg.';'
			 		   .'color:'.$item->t_params->bt_color.';'
			 		   .$item->t_params->bt_style.'"';
					   echo '<span class="type_button'.$bt_class.'" '.$style.' >'.$item->t_name.'</span>';							
			}else{
				echo '<span class="type_label'.$bt_class.'" >'.$item->t_name.'</span>';	
			}
		} 
		if($item->price || $item->price_negotiable){ ?>
		<div class="price_in_gd">
			<div class="price_wrap">
				<?php if($item->price){?>
				
				<span class="row_value">
					<?php echo DJClassifiedsTheme::priceFormat($item->price,$item->currency);?>
				</span>
				<?php
					}
					if($item->price_negotiable){ ?>		
					<span class="row_negotiable">
						<?php echo JText::_('COM_DJCLASSIFIEDS_PRICE_IS_NEGOTIABLE'); ?>
					</span>
				<?php } ?>
			</div>
		</div>
		
		<?php } ?>
		<?php 
		echo '<div class="disp_item"><img src="http://imakler.az/templates/jm-joomads/images/img-view.png" alt="Date-icon" height="20" width="20" style="padding-right: 5px;">';
		echo $item->display;
		echo '</div>';
		
		echo '<div class="pub_date"><img src="http://imakler.az/templates/jm-joomads/images/date.png" alt="Date-icon" height="20" width="20" style="padding-right: 5px;">';
		echo DJClassifiedsTheme::formatDate(strtotime($item->date_start));
		echo '</div>';
		?>
		<?php		
		if($par->get('favourite','1')){
			if($user->id>0 && $item->f_id){
				echo '<a title="'.JText::_('COM_DJCLASSIFIEDS_DELETE_FROM_FAVOURITES').'" href="index.php?option=com_djclassifieds&view=item&task=removeFavourite&cid='.$item->cat_id.'&id='.$item->id.'&Itemid='.$Itemid.'">';
					echo ' <img src="'.JURI::base().'/components/com_djclassifieds/assets/images/fav_a.png" width="16px" class="fav_ico"/>';
					echo '<span class="fav_label">'.JText::_('COM_DJCLASSIFIEDS_FAVOURITE').'</span>'; 
				echo '</a>';
			}else{
				echo '<a title="'.JText::_('COM_DJCLASSIFIEDS_ADD_TO_FAVOURITES').'" href="index.php?option=com_djclassifieds&view=item&task=addFavourite&cid='.$item->cat_id.'&id='.$item->id.'&Itemid='.$Itemid.'">';
					echo ' <img src="'.JURI::base().'/components/com_djclassifieds/assets/images/fav_na.png" width="16px" class="fav_ico"/>';
					echo '<span class="nfav_label">'.JText::_('COM_DJCLASSIFIEDS_ADD_TO_FAVOURITES').'</span>';
				echo '</a>';
			}	
						
		}
		if($icon_new){
			echo ' <span class="new_icon">'.JText::_('COM_DJCLASSIFIEDS_NEW').'</span>';
		} 
		
		if($par->get('sb_position','0')=='top' && $par->get('sb_code','')!=''){
			echo '<span class="sb_top">'.$par->get('sb_code','').'</span>';
		 }
		
	echo '</div>'; ?>
	<?php if($item->event->afterDJClassifiedsDisplayTitle) { ?>
		<div class="djcf_after_title">
			<?php echo $this->item->event->afterDJClassifiedsDisplayTitle; ?>
		</div>
	<?php } ?>
	<div class="dj-item-in">
	
			<div class="djcf_images_generaldet_box">	
				<?php if(count($this->item_images)){ echo  $this->loadTemplate('images');  }?>			
			</div>
			
			 <div class="my_gen_detail">
			   <div class="my_detail">
			 <?php echo  $this->loadTemplate('generaldetails'); ?>
			   </div>
			
			   <div class="my_detail">
			 <?php echo  $this->loadTemplate('customdetails'); ?>
			   </div>
			 </div>
			<?php echo  $this->loadTemplate('description'); ?>
            <div id="jm-custom-loc-wrapper">
   
                <div class="jm-box-margin"></div>
                <?php echo  $this->loadTemplate('localization'); ?> 
                </div>
            
            <?php echo  $this->loadTemplate('video'); ?>
            
            <?php if($joomadsdetails!='' or $joomadscomments!='') { ?>
            <div id="jm-add-com-wrapper">
                <?php echo  $this->loadTemplate('adddetails'); ?>
               
                <?php  echo $this->loadTemplate('comments'); ?>
            </div>
            <?php } ?>  
                                    
            <?php if($par->get('sb_position','0')=='bottom' && $par->get('sb_code','')!=''){
                echo '<span class="sb_bottom">'.$par->get('sb_code','').'</span>';
            }?>
                                 
        <div style="clear:both" ></div>
    </div>
    </div>	
	<?php 
	 $modules_djcf = &JModuleHelper::getModules('djcf-item-bottom');
		if(count($modules_djcf)>0){
			echo '<div class="djcf-ad-item-bottom clearfix">';
			foreach (array_keys($modules_djcf) as $m){
				echo JModuleHelper::renderModule($modules_djcf[$m],$mod_attribs);
			}
			echo'</div>';		
		}
	?>
</div>