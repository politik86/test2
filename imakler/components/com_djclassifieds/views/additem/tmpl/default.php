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

//jimport('joomla.media.images');
JHTML::_('behavior.framework','More');
JHTML::_('behavior.keepalive');
JHTML::_('behavior.formvalidation');
JHTML::_('behavior.modal');
JHTML::_('behavior.calendar');
$toolTipArray = array('className'=>'djcf_label');
JHTML::_('behavior.tooltip', '.Tips1', $toolTipArray);
$par = JComponentHelper::getParams( 'com_djclassifieds' );

$imglimit = $par->get('img_limit','3');
$unit_price = $par->get('unit_price','');	
$id = JRequest::getVar('id', 0, '', 'int' );
$user = JFactory::getUser();

$mod_attribs=array();
$mod_attribs['style'] = 'xhtml';

$document= JFactory::getDocument();
$config = JFactory::getConfig();
if($config->get('force_ssl',0)==2){
	$document->addScript("https://maps.google.com/maps/api/js?sensor=false");
}else{
	$document->addScript("http://maps.google.com/maps/api/js?sensor=false");
}	

/*if($par->get('region_add_type','1')==1){
	$document->addScript("http://maps.google.com/maps/api/js?sensor=false&language=".$par->get('region_lang','en'));
	$assets=JURI::base().'/components/com_djclassifieds/assets/';	
	$document->addScript($assets.'scripts.js');	
}*/
$points_a = $par->get('points',0);
$token = JRequest::getCMD('token', '' );
?>
<div id="dj-classifieds" class="clearfix">
	<?php 
		$modules_djcf = &JModuleHelper::getModules('djcf-top');			
		if(count($modules_djcf)>0){
			echo '<div class="djcf-ad-top clearfix">';
			foreach (array_keys($modules_djcf) as $m){
				echo JModuleHelper::renderModule($modules_djcf[$m],$mod_attribs);
			}
			echo'</div>';		
		}		
	
		$modules_djcf = &JModuleHelper::getModules('djcf-additem-top');			
		if(count($modules_djcf)>0){
			echo '<div class="djcf-ad-items-top clearfix">';
			foreach (array_keys($modules_djcf) as $m){
				echo JModuleHelper::renderModule($modules_djcf[$m],$mod_attribs);
			}
			echo'</div>';		
		}	

	?>	
	
<div class="dj-additem clearfix" >
<form action="index.php" method="post" class="form-validate" name="djForm" id="djForm"  enctype="multipart/form-data">
        <div class="additem_djform">
        
		    <div class="title_top"><?php 
				if(JRequest::getVar('id', 0, '', 'int' )>0){
					echo JText::_('Редактирование объявления');
				}else{
					echo JText::_('Новое объявление');	
				}
			?></div>
			<div class="additem_djform_in">
        	<center><img src='<?php echo JURI::base() ?>/components/com_djclassifieds/assets/images/long_loader.gif' alt='LOADING' style='display: none;' id='upload_loading' /><div id="alercik"></div></center>
          
            
		<div class="obshaja_infa">
            <div class="djform_row">                
               	<?php if($par->get('show_tooltips_newad','0')){ ?>
	            	<label class="label Tips1" for="cat_0" id="cat_0-lbl" title="<?php echo JTEXT::_('COM_DJCLASSIFIEDS_CATEGORY_TOOLTIP')?>">
	                    <?php echo JText::_('COM_DJCLASSIFIEDS_CATEGORY');?> *
	                    <img src="<?php echo JURI::base() ?>/components/com_djclassifieds/assets/images/tip.png" alt="?" />
	                </label>	                               			                	
				<?php }else{ ?>
	            	<label class="label" for="cat_0" id="cat_0-lbl">
	                	  <?php echo JText::_('Категория'); ?> *					
	                </label>
            	<?php } ?>
                <div class="djform_field">
                    <?php
				$cat_sel = '<select autocomplete="off" class="cat_sel required validate-djcat" id="cat_0" style="width:210px" name="cats[]" onchange="new_cat(0,this.value);getFields(this.value);"><option value="">'.JText::_('COM_DJCLASSIFIEDS_PLEASE_SELECT_CATEGORY').'</option>';
				$parent_id=0;	
				foreach($this->cats as $l){
					if($parent_id!=$l->parent_id){
						break;
					}	
					if($l->price>0){
						$l->price = $l->price/100;												
						$l->name .= ' ('.DJClassifiedsTheme::priceFormat($l->price,$unit_price);
							if($l->points>0 && $points_a){
								$l->name .= ' - '.$l->points.JTEXT::_('COM_DJCLASSIFIEDS_POINTS_SHORT');		
							}
						$l->name .= ')'; 
					}
					$cat_sel .= '<option value="'.$l->id.'">'.str_ireplace("'", "&apos;", $l->name).'</option>';
				}
					$cat_sel .= '</select>';
					echo $cat_sel;				
				
				?><div style="clear:both"></div>
				<div id="after_cat_0"></div>
				<script type="text/javascript">
					var cats=new Array();
					
				<?php
				$cat_sel = '<select style="width:210px" class="cat_sel required validate-djcat" name="cats[]" id="cat_0" onchange="new_cat(0,this.value);getFields(this.value);">';
				$parent_id=0;	
								
				$cat_req = array();
				foreach($this->cats as $l){
					if($l->ads_disabled){
						$cat_req[$l->id]=1;
					}
				}
				
				foreach($this->cats as $l){
					if($parent_id!=$l->parent_id){
						$cat_sel .= '</select>';
						echo "cats[$parent_id]='$cat_sel<div id=\"after_cat_$parent_id\"></div>';";
						$parent_id=$l->parent_id;
						$cl_select = '';
						if($l->ads_disabled || isset($cat_req[$parent_id])){
							$cl_select = ' class="cat_sel required validate-djcat" ';						
						}
						$cat_sel = '<div style="clear:both"></div><select '.$cl_select.' style="width:210px" name="cats[]" id="cat_'.$l->parent_id.'" onchange="new_cat('.$parent_id.',this.value);getFields(this.value);">';
						$cat_sel .= '<option value="p'.$parent_id.'">'.JTEXT::_('COM_DJCLASSIFIEDS_CATEGORY_SELECTOR_EMPTY_VALUE').'</option>';		
					}	
					if($l->price>0){
						$l->price = $l->price/100;						
						$l->name .= ' ('.DJClassifiedsTheme::priceFormat($l->price,$unit_price);
							if($l->points>0 && $points_a){
								$l->name .= ' - '.$l->points.JTEXT::_('COM_DJCLASSIFIEDS_POINTS_SHORT');		
							}	
						$l->name .= ')'; 
					}
					$cat_sel .= '<option value="'.$l->id.'">'.str_ireplace("'", "&apos;", $l->name).'</option>';
				}
				$cat_sel .= '</select>';	
				echo "cats[$parent_id]='$cat_sel<div id=\"after_cat_$parent_id\"></div>';";
				
				?>	
				var current=0;
				
				function new_cat(parent,a_parent){
					if(cats[a_parent]){
						//alert(cats[v]);	
						document.id('after_cat_'+parent).innerHTML = cats[a_parent]; 
						document.id('cat_'+parent).value=a_parent;
					}else{
						document.id('after_cat_'+parent).innerHTML = '';
						document.id('cat_'+parent).value=a_parent;		
					}
					document.id('after_cat_'+parent).removeClass('invalid');					
					document.id('after_cat_'+parent).setAttribute("aria-invalid", "false");
					
				}
				<?php echo $this->cat_path;?>
				</script>
					
                </div>
                <div style="clear:both"></div>
           
            <?php
            $types = DJClassifiedsType::getTypesSelect();
            if($par->get('show_types','0') && $types){?>  	
            <div class="djform_row">                
               	<?php if($par->get('show_tooltips_newad','0')){ ?>
	            	<label class="label Tips1" for="type_id" id="type_id-lbl" title="<?php echo JTEXT::_('COM_DJCLASSIFIEDS_TYPE_TOOLTIP')?>">
	                    <?php echo JText::_('COM_DJCLASSIFIEDS_TYPE');if($par->get('types_required','0')){ echo ' * ';} ?>
	                    <img src="<?php echo JURI::base() ?>/components/com_djclassifieds/assets/images/tip.png" alt="?" />
	                </label>	                               			                	
				<?php }else{ ?>
	            	<label class="label" for="type_id" id="type_id-lbl">
	                	  <?php echo JText::_('Тип объявления');if($par->get('types_required','0')){ echo ' * ';} ?>					
	                </label>
            	<?php } ?>
                <div class="djform_field">
               		<select autocomplete="off" name="type_id" id="type_id" class="inputbox<?php if($par->get('types_required','0')){ echo ' required';} ?>" >
						<option value=""><?php echo JText::_('Тип обявления');?></option>
						<?php echo JHtml::_('select.options', $types, 'value', 'text', $this->item->type_id, true);?>
					</select>
					<div style="clear:both"></div>									
                </div>
                <div style="clear:both"></div>
            </div>
            <?php }?>
            			
            <?php if(count($this->regions) && $par->get('show_regions','1')){?>
    		<div class="djform_row">
                <?php if($par->get('show_tooltips_newad','0')){ ?>
	            	<label class="label Tips1" for="reg_0" id="reg_0-lbl" title="<?php echo JTEXT::_('COM_DJCLASSIFIEDS_LOCALIZATION_TOOLTIP')?>">
	                    <?php echo JText::_('COM_DJCLASSIFIEDS_LOCALIZATION');?> *
	                    <img src="<?php echo JURI::base() ?>/components/com_djclassifieds/assets/images/tip.png" alt="?" />
	                </label>	                               			                	
				<?php }else{ ?>
	            	<label class="label" for="reg_0" id="cat_0-lbl">
	                	  <?php echo JText::_('Регион'); ?> *					
	                </label>
            	<?php } ?>
                <div class="djform_field" id="locations_list">
	                <?php	               		     	                    
							$reg_sel = '<select autocomplete="off" id="reg_0" class="required" style="width:210px" name="regions[]" onchange="new_reg(0,this.value);"><option value="">'.JText::_('COM_DJCLASSIFIEDS_SELECT_LOCALIZATION').'</option>';
							$parent_id=0;	
							foreach($this->regions as $l){
								if($parent_id!=$l->parent_id){
									break;
								}	
								$reg_sel .= '<option value="'.$l->id.'">'.str_ireplace("'", "&apos;", $l->name).'</option>';
								//$ri++;
							}
								$reg_sel .= '</select>';
								echo $reg_sel;
							
							?><div style="clear:both"></div>
							<div id="after_reg_0"></div>
							<script type="text/javascript">
								var regs=new Array();
								
							<?php
							$reg_sel = '<select style="width:210px" name="regions[]" id="reg_0" onchange="new_reg(0,this.value);">';
							$parent_id=0;	
							
							foreach($this->regions as $l){
								if($parent_id!=$l->parent_id){
									$reg_sel .= '</select>';
									echo "regs[$parent_id]='$reg_sel<div id=\"after_reg_$parent_id\"></div>';";
									$parent_id=$l->parent_id;
									$reg_sel = '<div style="clear:both"></div><select style="width:210px" name="regions[]" id="reg_'.$l->parent_id.'" onchange="new_reg('.$parent_id.',this.value);">';
									$reg_sel .= '<option value="">'.JTEXT::_('COM_DJCLASSIFIEDS_LOCATION_SELECTOR_EMPTY_VALUE').'</option>';		
								}	
								$reg_sel .= '<option value="'.$l->id.'">'.str_ireplace("'", "&apos;", $l->name).'</option>';
							}
							$reg_sel .= '</select>';	
							echo "regs[$parent_id]='$reg_sel<div id=\"after_reg_$parent_id\"></div>';";
							
							?>	
							var current=0;
							
							function new_reg(parent,a_parent){
								if(regs[a_parent]){
									//alert(cats[v]);	
									document.id('after_reg_'+parent).innerHTML = regs[a_parent]; 
									document.id('reg_'+parent).value=a_parent;
								}else{
									document.id('after_reg_'+parent).innerHTML = '';
									document.id('reg_'+parent).value=a_parent;		
								}
								
							}
							<?php echo $this->reg_path;?>
						</script>							
                </div>
                <div style="clear:both"></div>
            </div>  
            <?php }else{ ?>
            	<input type="hidden" name="regions[]" value="0" />
            <?php } ?>
            <?php if($par->get('show_address','1')){?>
	            <div class="djform_row">
	                <?php if($par->get('show_tooltips_newad','0')){ ?>
		            	<label class="label Tips1" title="<?php echo JTEXT::_('COM_DJCLASSIFIEDS_ADDRESS_TOOLTIP')?>">
		                    <?php echo JText::_('COM_DJCLASSIFIEDS_ADDRESS');?>
		                    <img src="<?php echo JURI::base() ?>/components/com_djclassifieds/assets/images/tip.png" alt="?" />
		                </label>	                               			                	
					<?php }else{ ?>
		            	<label class="label">
		                	  <?php echo JText::_('Улица'); ?>					
		                </label>
	            	<?php } ?>
	                <div class="djform_field">
	                    <input class="text_area" type="text" name="address" id="address" size="50" maxlength="250" value="<?php echo $this->item->address; ?>" />
	                </div>
	                <div style="clear:both"></div> 
	            </div>
            <?php } ?>      
            <?php if($par->get('show_postcode','0')){?>
                 
            <?php
			 }       
             $exp_days_list = $par->get('exp_days_list','');
			$exp_days = $par->get('exp_days','');
			if($par->get('durations_list','') && $id==0 && count($this->days)){
				//print_r($this->days);die();				
					?>
	    		<div class="djform_row">
	                <?php if($par->get('show_tooltips_newad','0')){ ?>
		            	<label class="label Tips1" for="exp_days" id="exp_days-lbl" title="<?php echo JTEXT::_('COM_DJCLASSIFIEDS_EXPIRE_AFTER_TOOLTIP')?>">
		                    <?php echo JText::_('COM_DJCLASSIFIEDS_EXPIRE_AFTER');?>
		                    <img src="<?php echo JURI::base() ?>/components/com_djclassifieds/assets/images/tip.png" alt="?" />
		                </label>	                               			                	
					<?php }else{ ?>
		            	<label class="label" for="exp_days" id="exp_days-lbl" >
		                	  <?php echo JText::_('Истекает через:'); ?>					
		                </label>
	            	<?php } ?>
	                <select id="exp_days" name="exp_days">
					<?php 					
						foreach($this->days as $day){
							echo '<option value="'.$day->days.'"';	
								if($day->days==$exp_days){
									echo ' SELECTED ';	
								}							
								echo '>';
								if($day->days==1){
									echo $day->days.'&nbsp;'.JText::_('День');
								}else{
									echo $day->days.'&nbsp;'.JText::_('Дней');	
								} 
								
								if($day->price !='0.00'){
									//echo '&nbsp;-&nbsp;'.$day->price.'&nbsp;'.$par->get('unit_price');	
									echo '&nbsp;-&nbsp;'.DJClassifiedsTheme::priceFormat($day->price,$par->get('unit_price'));
								}
								if($day->points>0 && $points_a){
									echo '&nbsp;-&nbsp;'.$day->points.'&nbsp;'.JTEXT::_('COM_DJCLASSIFIEDS_POINTS_SHORT');	
								}							
							echo '</option>';
						}
					?>
					</select>
	                <div style="clear:both"></div>
	            </div>                
            <?php } ?>
			 </div>
			  <div class="djform_row">
            	<?php if($par->get('show_tooltips_newad','0')){ ?>
	            	<label class="label Tips1" for="name" id="name-lbl" title="<?php echo JTEXT::_('COM_DJCLASSIFIEDS_TITLE_TOOLTIP')?>">
	                    <?php echo JText::_('COM_DJCLASSIFIEDS_TITLE');?> *
	                    <?php if($par->get('title_char_limit','0')>0){ ?>
	                    	<span id="title_limit">(<?php echo $par->get('title_char_limit');?>)</span>
	                    <?php } ?>
	                    <img src="<?php echo JURI::base() ?>/components/com_djclassifieds/assets/images/tip.png" alt="?" />
	                </label>	
				<?php }else{ ?>
            		<label class="label" for="name" id="name-lbl" >
	                    <?php echo JText::_('Заголовок');?> *
	                    <?php if($par->get('title_char_limit','0')>0){ ?>
	                    	<span id="title_limit">(<?php echo $par->get('title_char_limit');?>)</span>
	                    <?php } ?>	                
	                </label>
            	<?php } ?>                
                <div class="djform_field">                  	              	
                	<?php
                	$title_char_limit = $par->get('title_char_limit','0'); 
                	if($title_char_limit>0){
                		$input_title_limit =' onkeyup="titleLimit('.$title_char_limit.');" ';
                	}else{
                		$input_title_limit ='';
                	} ?>
                    <input class="inputbox required" <?php echo $input_title_limit; ?> type="text" name="name" id="name" size="50" maxlength="250" value="<?php echo $this->item->name; ?>" />
                </div>
                <div style="clear:both"></div>
            </div>

            <?php if($par->get('show_introdesc','1')){?>
    		<div class="djform_row">                
                <?php if($par->get('show_tooltips_newad','0')){ ?>
	            	<label class="label Tips1" for="intro_desc" id="intro_desc-lbl" title="<?php echo JTEXT::_('COM_DJCLASSIFIEDS_INTRO_DESCRIPTION_TOOLTIP')?>">
	                    <?php echo JText::_('COM_DJCLASSIFIEDS_INTRO_DESCRIPTION');?> *	                    
	                    <span id="introdesc_limit">(<?php echo $par->get('introdesc_char_limit');?>)</span>
	                    <img src="<?php echo JURI::base() ?>/components/com_djclassifieds/assets/images/tip.png" alt="?" />	                    
	                </label>	                               			                	
				<?php }else{ ?>
	            	<label class="label" for="intro_desc" id="intro_desc-lbl">                	
                    	<?php echo JText::_('Описание основное');?> * 	
 						<span id="introdesc_limit">(<?php echo $par->get('introdesc_char_limit');?>)</span>	 								
                	</label>
            	<?php } ?>
                <div class="djform_field">
		            <textarea id="intro_desc" name="intro_desc" rows="2" cols="25" class="inputbox required" onkeyup="introdescLimit(<?php echo $par->get('introdesc_char_limit');?>);" ><?php echo $this->item->intro_desc; ?></textarea>
                </div>
                <div style="clear:both"></div>
            </div>
            <?php } ?>
			 
		</div>
		  <div class="podrobnaja_infa">
		    <?php
            if($par->get('show_price','1')==1){?>
    		<div class="djform_row">
              <?php if($par->get('show_tooltips_newad','0')){ ?>
	            	<label class="label Tips1" id="price-lbl" for="price" title="<?php echo JTEXT::_('COM_DJCLASSIFIEDS_PRICE_TOOLTIP')?>">
	                    <?php echo JText::_('COM_DJCLASSIFIEDS_PRICE');?>
	                    <img src="<?php echo JURI::base() ?>/components/com_djclassifieds/assets/images/tip.png" alt="?" />
	                </label>	                               			                	
				<?php }else{ ?>
	            	<label class="label" id="price-lbl" for="price">
	                	  <?php echo JText::_('Цена'); ?>					
	                </label>
            	<?php } ?>
                <div class="djform_field">
                	<?php if ($par->get('unit_price_position','0')== 0) {	?>
                    	<input class="text_area<?php if($par->get('price_only_numbers','0')){echo 'validate-numeric';}?>" type="text" name="price" id="price" size="30" maxlength="250" value="<?php echo $this->item->price; ?>" />
                     <?php }
                     
                     if($par->get('unit_price_list','')){
                     	$c_list = explode(';', $par->get('unit_price_list',''));
						 echo '<select name="currency" class="price_currency">';
						 for($cl=0;$cl<count($c_list);$cl++){
						 	if($c_list[$cl]==$this->item->currency){
						 		$csel=' SELECTED ';
						 	}else{
						 		$csel='';
							}
						 	echo '<option '.$csel.' name="'.$c_list[$cl].' ">'.$c_list[$cl].'</option>';
						 }
						 echo '</select>';
                     	
                     }else{
                     	echo $par->get('unit_price','EUR');
						echo '<input type="hidden" name="currency" value="" >';
                     }
                     if ($par->get('unit_price_position','0')== 1) { ?>	
                        <input class="text_area<?php if($par->get('price_only_numbers','0')){echo 'validate-numeric';}?>" type="text" name="price" id="price" size="30" maxlength="250" value="<?php echo $this->item->price; ?>" />
                     <?php }
                     
                     if($par->get('show_price_negotiable','0')){ ?>
                     	<div class="price_neg_box">
                     		<input type="checkbox" autocomplete="off" name="price_negotiable" value="1" <?php if($this->item->price_negotiable){ echo 'checked="CHECKED"';}?> />
                     		<span><?php echo JText::_('COM_DJCLASSIFIEDS_PRICE_NEGOTIABLE')?></span>
                     	</div>
                     <?php  }else{ ?>
                     	<input type="hidden" name="price_negotiable" value="0" />
                     <?php } ?>
                </div>
                <div style="clear:both"></div>
            </div>
			<?php
			} ?> 
		    <?php
			    
			 if($par->get('show_video','0')){?>
            	<div class="djform_row">
	                 <?php if($par->get('show_tooltips_newad','0')){ ?>
		            	<label class="label Tips1" title="<?php echo JTEXT::_('COM_DJCLASSIFIEDS_VIDEO_TOOLTIP')?>">
		                    <?php echo JText::_('COM_DJCLASSIFIEDS_VIDEO');?>
		                    <img src="<?php echo JURI::base() ?>/components/com_djclassifieds/assets/images/tip.png" alt="?" />
		                    <br /><span><?php echo JText::_('COM_DJCLASSIFIEDS_LINK_TO_YOUTUBE_OR_VIMEO');?></span>
		                </label>	                               			                	
					<?php }else{ ?>
		            	<label class="label">
		                	  <?php echo JText::_('COM_DJCLASSIFIEDS_VIDEO'); ?>	
		                	  <br /><span><?php echo JText::_('COM_DJCLASSIFIEDS_LINK_TO_YOUTUBE_OR_VIMEO');?></span>				
		                </label>
	            	<?php } ?>
	                <div class="djform_field">
	                    <input class="text_area" type="text" name="video" id="video" size="50" maxlength="250" value="<?php echo $this->item->video; ?>" />
	                </div>
	                <div style="clear:both"></div> 
	            </div>             
            <?php
			 }  
			 ?>
		    <div class="djform_row extra_fields">        
				<div id="ex_fields"></div>        
              
            </div> 
		 </div>
    	
        <div class="contact_infa">	
		 <?php if($par->get('show_contact','1')==1){?>
    		<div class="djform_row">
               	<?php if($par->get('show_tooltips_newad','0')){ ?>
	            	<label class="label Tips1" for="contact" id="contact-lbl" title="<?php echo JTEXT::_('COM_DJCLASSIFIEDS_CONTACT_TOOLTIP')?>">
	                    <?php echo JText::_('COM_DJCLASSIFIEDS_CONTACT');?> *
	                    <img src="<?php echo JURI::base() ?>/components/com_djclassifieds/assets/images/tip.png" alt="?" />
	                </label>	                               			                	
				<?php }else{ ?>
	            	<label class="label" for="contact" id="contact-lbl" >
	                	  <?php echo JText::_('Контакты'); ?> *					
	                </label>
            	<?php } ?>
                <div class="djform_field">
		            <textarea id="contact" name="contact" rows="4" cols="55" class="inputbox required"><?php echo str_ireplace("<br />", '', $this->item->contact); ?></textarea>                  
                </div>
                <div style="clear:both"></div>
            </div>
            <?php            	           
             }
             echo  $this->loadTemplate('contactfields');
             if($par->get('email_for_guest','0') && !$user->id && !$this->item->id){ ?>
             	<div class="djform_row">
             		<?php if($par->get('show_tooltips_newad','0')){ ?>
             		   	<label for="guest_email" id="guest_email-lbl" class="label Tips1" title="<?php echo JTEXT::_('COM_DJCLASSIFIEDS_EMAIL_GUEST_TOOLTIP')?>">
             		 	   <?php echo JText::_('COM_DJCLASSIFIEDS_EMAIL');?> <?php if($par->get('email_for_guest','0')==2){echo '*';}?>
             		       <img src="<?php echo JURI::base() ?>/components/com_djclassifieds/assets/images/tip.png" alt="?" /> 
             		    </label>	                               			                	
             		<?php }else{ ?>
             		   	<label for="guest_email" id="guest_email-lbl" class="label">
             		   	   <?php echo JText::_('EMAIL'); ?> <?php if($par->get('email_for_guest','0')==2){echo '*';}?>
             		    </label>
             	  	<?php } ?>
             	    <div class="djform_field">             	    	
             	    	<input class="text_area validate-djemail <?php if($par->get('email_for_guest','0')==2){echo ' required';}?>" onchange="checkDJEmail(this.value);" type="text" name="email" id="guest_email" size="50" maxlength="250" value="" />
             	    	<span id="guest_email_loader"><img src="<?php echo JURI::base() ?>/components/com_djclassifieds/assets/images/newad_loader.gif" alt="..." /></span>
             	    	<div id="guest_email_info"></div>
             	    </div>
             	    <div style="clear:both"></div> 
             	    </div>             
                <?php
             }                
            ?>
        </div>		
		
      	           
          <div style="clear:both"></div> 
		  <div class="detail_box">
		    <div id="DoubleFlds">

            </div>	
		  </div>
		
   
 		 	<?php if($par->get('terms',1)>0 && $par->get('terms_article_id',0)>0 && $this->terms_link && JRequest::getVar('id', 0, '', 'int' )==0 && !$token){ ?>				
    		<div class="djform_row terms_and_conditions">
                <label class="label" >&nbsp;</label>
                <div class="djform_field">
                	<fieldset id="terms_and_conditions" class="checkboxes required">
                		<input type="checkbox" name="terms_and_conditions" id="terms_and_conditions0" value="1" class="inputbox" />                	
						<?php 					 
						echo ' <label class="label_terms" for="terms_and_conditions" id="terms_and_conditions-lbl" >'.JText::_('COM_DJCLASSIFIEDS_I_AGREE_TO_THE').' </label>';					
						if($par->get('terms',0)==1){
							echo '<a href="'.$this->terms_link.'" target="_blank">'.JText::_('COM_DJCLASSIFIEDS_TERMS_AND_CONDITIONS').'</a>';
						}else if($par->get('terms',0)==2){
							echo '<a href="'.$this->terms_link.'" rel="{size: {x: 700, y: 500}, handler:\'iframe\'}" class="modal" target="_blank">'.JText::_('COM_DJCLASSIFIEDS_TERMS_AND_CONDITIONS').'</a>';
						}					
						?> *
					</fieldset>
                </div>
               
            </div>
		 <?php } ?>	
 		
 		 </div>
 		 </div>
 		 <?php 
	 		 if($imglimit>0){
	 		 	echo  $this->loadTemplate('images');
	 		 }
			if($par->get('promotion','1')=='1' && count($this->promotions)>0){ ?>							
				<div class="prom_rows additem_djform">
				<div class="title_top"><?php echo JText::_('COM_DJCLASSIFIEDS_PROMOTIONS');	?>
					<?php if(count($this->promotions)>1){ ?>
						<div class="promotions_info">
							<?php echo JText::_('COM_DJCLASSIFIEDS_SELECT_EACH_PROMOTION_YOU_WISH_TO_USE')?>
						</div>
					<?php } ?>								
				</div>


				<div class="additem_djform_in">				
				<?php foreach($this->promotions as $prom){ ?>	
	    		<div class="djform_row">
	                <label class="label" >
	                	<?php 
	                		echo JText::_($prom->label).'<br /><span>'.JText::_('COM_DJCLASSIFIEDS_PRICE').'&nbsp;';
	                		echo DJClassifiedsTheme::priceFormat($prom->price,$par->get('unit_price'));
							if($points_a && $prom->points>0){
								echo '&nbsp-&nbsp'.$prom->points.JTEXT::_('COM_DJCLASSIFIEDS_POINTS_SHORT');
							}
	                		echo '</span>';
	                	?>						
	                </label>
	                <div class="djform_field">
						<div class="djform_prom_v" >
							<div class="djform_prom_v_in" >
							<input type="radio" name="<?php echo $prom->name;?>" value="1" <?php  if(strstr($this->item->promotions, $prom->name)){echo "checked";}?> /><label><?php echo JText::_('JYES'); ?></label>
							<input type="radio" name="<?php echo $prom->name;?>" value="0" <?php  if(!strstr($this->item->promotions, $prom->name)){echo "checked";}?> /><label><?php echo JText::_('JNO'); ?></label>
							</div>
						</div>
						<div class="djform_prom_img" >							
							<div class="djform_prom_img_in" >
								<?php 
									$tip_content = '<img src=\''.JURI::base().'/components/com_djclassifieds/assets/images/'.$prom->name.'_h.png\' />'; 
									echo '<img class="Tips2" title="'.$tip_content.'" src="'.JURI::base().'/components/com_djclassifieds/assets/images/'.$prom->name.'.png" />';
                                ?>
							</div>
						</div>

						<div class="djform_prom_desc" >
							<div class="djform_prom_desc_in" >
							<?php echo JText::_($prom->description); ?>
							</div>
						</div>
							
	                </div>
	                <div style="clear:both"></div>
	            </div>
	            <?php } ?>
	            </div>
            </div>

		 <?php } ?>			
        
		<label id="verification_alert"  style="display:none;color:red;" />
			<?php echo JText::_('Заполните все обязательные поля'); ?>
		</label>
     <div class="classifieds_buttons">
     	<?php if($user->id>0){
	     	$cancel_link = JRoute::_('index.php?option=com_djclassifieds&view=useritems&Itemid='.JRequest::getVar('Itemid','0'));
	     }else{
	     	$cancel_link = JRoute::_('index.php?option=com_djclassifieds&view=items&cid=0&Itemid='.JRequest::getVar('Itemid','0'));
	     } 	     
	     ?>
	     <a class="button" href="<?php echo $cancel_link;?>"><?php echo JText::_('COM_DJCLASSIFIEDS_CANCEL')?></a>
	     <button class="button validate" type="submit" id="submit_button"  ><?php echo JText::_('COM_DJCLASSIFIEDS_SAVE'); ?></button>	     
		 <input type="hidden" name="option" value="com_djclassifieds" />

		<input type="hidden" name="id" value="<?php echo JRequest::getVar('id', 0, '', 'int' ); ?>" />
		<input type="hidden" name="token" value="<?php echo $token; ?>" />
		<input type="hidden" name="view" value="additem" />
		<input type="hidden" name="task" value="save" />
		<input type="hidden" name="boxchecked" value="0" />
	</div>

</form>
</div>
</div>
<script type="text/javascript">	
/*	
function addImage(imglimit){

	lim=document.djForm['image[]'].length;
	if(!lim){
		lim=1;
	}
	
	if(document.djForm['del_img[]']){
		lim_old=document.djForm['del_img[]'].length;
		if(!lim_old){
			lim_old=1;
		}
		lim = lim + lim_old;	
	}
	
	
	if(lim==imglimit){
		alert('<?php echo JText::_('COM_DJCLASSIFIEDS_MAXIMUM_NUMBER_OF_IMAGES_IS');?> '+imglimit);
	}else{
		var inputdiv = document.createElement('input');
		inputdiv.setAttribute('name','image[]');
		inputdiv.setAttribute('type','file');
		var ni = document.id('uploader');
		ni.appendChild(document.createElement('br'))
		ni.appendChild(inputdiv);		
	}

} */


	<?php if($par->get('show_introdesc','1')){?>
		function introdescLimit(limit){
			if(document.djForm.intro_desc.value.length<=limit){
				a=document.djForm.intro_desc.value.length;
				b=limit;
				c=b-a;
				document.getElementById('introdesc_limit').innerHTML= '('+c+')';
			}else{
				document.djForm.intro_desc.value = document.djForm.intro_desc.value.substring(0, limit);
			}
		}
	<?php } ?>

	<?php if($par->get('seo_metadesc_user_edit','0')){?>
	function metadescLimit(limit){
		if(document.djForm.meta_desc.value.length<=limit){
			a=document.djForm.meta_desc.value.length;
			b=limit;
			c=b-a;
			document.getElementById('metadesc_limit').innerHTML= '('+c+')';
		}else{
			document.djForm.meta_desc.value = document.djForm.meta_desc.value.substring(0, limit);
		}
	}
	<?php } ?>
	
	<?php if($title_char_limit>0){ ?>
		function titleLimit(limit){
			if(document.djForm.name.value.length<=limit){
				a=document.djForm.name.value.length;
				b=limit;
				c=b-a;
				document.getElementById('title_limit').innerHTML= '('+c+')';
			}else{
				document.djForm.name.value = document.djForm.name.value.substring(0, limit);
			}
		}	
	<?php }?>


    function getFields(cat_id){		
     
	var el = document.getElementById("ex_fields");
	var before = document.getElementById("ex_fields").innerHTML.trim();	
	
	if(cat_id!=0){
		el.innerHTML = '<div style="text-align:center"><img src="<?php echo JURI::base(); ?>components/com_djclassifieds/assets/images/loading.gif" /></div>';
		var url = '<?php echo JURI::base()?>index.php?option=com_djclassifieds&view=additem&task=getFields&cat_id=' + cat_id <?php if($this->item->id){echo "+ '&id='+".$this->item->id;} ?>;
					  var myRequest = new Request({
				    url: '<?php echo JURI::base()?>index.php',
				    method: 'post',
					data: {
				      'option': 'com_djclassifieds',
				      'view': 'additem',
				      'task': 'getFields',
					  'cat_id': cat_id
					  <?php if($this->item->id){echo ",'id':'".$this->item->id."'";} ?>					  
					  },
				    onRequest: function(){
				        //myElement.set('html', '<div style="text-align:center;"><img style="margin-top:10px;" src="<?php echo JURI::base().'components/'.JRequest::getString('option').'/images/long_loader.gif';?>" /><br />loading...</div>');
				    },
					      onSuccess: function(responseText){	
					          var dblFlds = document.getElementById("DoubleFlds").innerHTML = responseText;    
						el.innerHTML = responseText;
						var JTooltips = new Tips($$('.Tips1'), {	
					      showDelay: 200, hideDelay: 200, className: 'djcf_label', fixed: true
					   });						 	
					   var djcals = document.getElements('.djcalendar');
						if(djcals){
							var startDate = new Date(2008, 8, 7);
							djcals.each(function(djcla,index){
								Calendar.setup({
						            inputField  : djcla.id,
						            ifFormat    : "%Y-%m-%d",                  
						            button      : djcla.id+"button",
						            date      : startDate
						         });
							});
						}
				    },
				    onFailure: function(){
				        myElement.set('html', 'Sorry, your request failed, please contact to ');
				    }
				});
				myRequest.send();	
	}else{
		el.innerHTML = '';
		//el.innerHTML='<?php echo JText::_('COM_DJCLASSIFIEDS_PLEASE_SELECT_CATEGORY');?>';
	}
	
	
}










function getCities(region_id){
	var el = document.getElementById("city");
	var before = document.getElementById("city").innerHTML.trim();	
	
	if(region_id>0){
		el.innerHTML = '<img src="<?php echo JURI::base(); ?>components/com_djclassifieds/assets/images/loading.gif" />';
		var url = '<?php echo JURI::base()?>index.php?option=com_djclassifieds&view=additemtask=getCities&r_id=' + region_id <?php if($this->item->id){echo "+ '&id='+".$this->item->id;} ?>;
			var myRequest = new Request({
				    url: '<?php echo JURI::base()?>index.php',
				    method: 'post',
					data: {
				      'option': 'com_djclassifieds',
				      'view': 'additem',
				      'task': 'getCities',
					  'r_id': region_id
					  <?php if($this->item->id){echo ",'id':'".$this->item->id."'";} ?>					  
					  },
				    onRequest: function(){
				        //myElement.set('html', '<div style="text-align:center;"><img style="margin-top:10px;" src="<?php echo JURI::base().'components/'.JRequest::getString('option').'/images/long_loader.gif';?>" /><br />loading...</div>');
				    },
				    onSuccess: function(responseText){																
						el.innerHTML = responseText;						 	
				    },
				    onFailure: function(){
				        myElement.set('html', 'Sorry, your request failed, please contact to ');
				    }
				});
				myRequest.send();	
	}else{
		el.innerHTML='<?php echo JText::_('COM_DJCLASSIFIEDS_PLEASE_SELECT_REGION');?>';
	}
	
}	


	function checkDJEmail(value){
		document.id('guest_email_loader').setStyle('display','inline-block');	   		
		var myRequest = new Request({
			    url: '<?php echo JURI::base()?>index.php',
		    method: 'post',
			data: {
		      'option': 'com_djclassifieds',
		      'view': 'additem',
		      'task': 'checkEmail',
			  'email': value					  
			  },
		    onRequest: function(){
		        //myElement.set('html', '<div style="text-align:center;"><img style="margin-top:10px;" src="<?php echo JURI::base().'components/'.JRequest::getString('option').'/images/long_loader.gif';?>" /><br />loading...</div>');
		    },
		    onSuccess: function(responseText){			
		    	if(responseText){
		    		document.id('guest_email_info').innerHTML = responseText;
		    		document.id('guest_email_info').setStyle('display','block');
		    		document.id('guest_email').addClass('invalid');
		    		document.id('guest_email').addClass('djinvalid');
		    		document.id('guest_email-lbl').addClass('invalid');
		    		document.id('guest_email').set('aria-invalid','true'); 
		    		document.id('guest_email_loader').setStyle('display','none');			    		
				}else{
					document.id('guest_email_info').innerHTML = '';
					document.id('guest_email_info').setStyle('display','none');
					document.id('guest_email_loader').setStyle('display','none');
					if(document.id('guest_email').hasClass('djinvalid')){
						document.id('guest_email').removeClass('invalid');
						document.id('guest_email').removeClass('djinvalid');
						document.id('guest_email-lbl').removeClass('invalid');
			    		document.id('guest_email').set('aria-invalid','false'); 
				    }
						 						
				}				 	
		    },
		    onFailure: function(){		    
		    }
		});
		myRequest.send();	 	      
	}

	
	<?php if($par->get('allow_user_lat_lng','0')){ 
		if(($id || $token) && $this->item->latitude && $this->item->longitude){
			$lat = $this->item->latitude;
			$lon = $this->item->longitude;
		}else if(isset($_COOKIE["djcf_latlon"])) {
			$lat_lon = explode('_', $_COOKIE["djcf_latlon"]);
			$lat = $lat_lon[0];
			$lon = $lat_lon[1];
		}else{
			$loc_coord = DJClassifiedsGeocode::getLocation($par->get('map_lat_lng_address','England, London'));
			if(is_array($loc_coord)){
				$lat = $loc_coord['lat'];
				$lon = $loc_coord['lng'];
			}else{
			
			}
		}				
		?>	
	    var map         = null;
	    var marker      = null;
	    var start_lat   = '<?php echo $lat; ?>';
	    var start_lon   = '<?php echo $lon; ?>';
	    var start_zoom  = <?php echo $par->get('gm_zoom','10'); ?>;
		var my_lat = start_lat;
		var my_lng = start_lon;
		var geokoder = new google.maps.Geocoder();
		
	    function initDjMap() {
	        //var zoom    = 13;
	        var coord   = new google.maps.LatLng(start_lat, start_lon);
	
	        var mapoptions = {
	            zoom: start_zoom,
	            center: coord,
	            mapTypeControl: true,
	            navigationControl: true,
	            zoomControl: true,        
	            mapTypeId: google.maps.MapTypeId.ROADMAP
	        }
	
	        // create the map
	        map = new google.maps.Map(document.getElementById('djmap'),mapoptions);
	
	        marker  = new google.maps.Marker({
	            position: coord,
	            draggable: true,
	            visible: true,
	            clickable: false,
	            map: map
	        });
	
	        google.maps.event.addListener(marker, 'dragend', function(event) {
	            latlng  = marker.getPosition();
	            my_lat     = latlng.lat();
	            my_lng     = latlng.lng();
	            document.getElementById('latitude').value   = my_lat;
	            document.getElementById('longitude').value  = my_lng;
	        });
	
	        google.maps.event.trigger(map, 'resize');
			map.setCenter(new google.maps.LatLng(my_lat,my_lng));
			
			map.setZoom( map.getZoom() );
	
			document.id('latitude').addEvent('change', function(){
				my_lat = this.value;
				map.setCenter(new google.maps.LatLng(my_lat,my_lng));
				coord   = new google.maps.LatLng(my_lat, my_lng);
			    marker.setPosition(coord);						
			});
			document.id('longitude').addEvent('change', function(){
				my_lng = this.value;			
				map.setCenter(new google.maps.LatLng(my_lat,my_lng));
				coord   = new google.maps.LatLng(my_lat, my_lng);
			    marker.setPosition(coord);	
			});
			document.id('map_use_my_location').addEvent('click', function(){
			  if(navigator.geolocation){
				  navigator.geolocation.getCurrentPosition(showDJPosition);
			   }else{
				   x.innerHTML="<?php echo JText::_('COM_DJCLASSIFIEDS_GEOLOCATION_IS_NOT_SUPPORTED_BY_THIS_BROWSER');?>";}
			});
			document.id('map_update_latlng').addEvent('click', function(){
				updateLatLngFromAddress();
			});		
			<?php if(!$id && $par->get('show_address','1')){ ?>
				document.id('address').addEvent('change', function(){
					updateLatLngFromAddress();
				});
			<?php } ?>													
		}
		
	    function showDJPosition(position){
		  	var exdate=new Date();
		  	exdate.setDate(exdate.getDate() + 1);
			var ll = position.coords.latitude+'_'+position.coords.longitude;
		  	document.cookie = "djcf_latlon=" + ll + "; expires=" + exdate.toUTCString();
		  	my_lat = position.coords.latitude;
		  	my_lng = position.coords.longitude;				
		  	document.getElementById('latitude').value   = my_lat;
            document.getElementById('longitude').value  = my_lng;
			map.setCenter(new google.maps.LatLng(my_lat,my_lng));
			var coord   = new google.maps.LatLng(my_lat, my_lng);
		    marker.setPosition(coord);	
	  	}

	  	function updateLatLngFromAddress(){
		  	var address = '';
		  	$$(document.getElementsByName('regions[]')).each(function(el){
			  	if(el.value){address = address+el.getSelected().get('text')+', ';}
		  	});
		  	address = address+document.id('address').value;
		  		geokoder.geocode(
				  	{address: address}, 
				  	function (results, status){
					    if(status == google.maps.GeocoderStatus.OK){
					    	my_lat = results[0].geometry.location.lat();
						  	my_lng = results[0].geometry.location.lng();				
						  	document.getElementById('latitude').value   = my_lat;
				            document.getElementById('longitude').value  = my_lng;
							map.setCenter(new google.maps.LatLng(my_lat,my_lng));
							var coord   = new google.maps.LatLng(my_lat, my_lng);
						    marker.setPosition(coord);	
						}else{
							document.id('mapalert').setStyle('display','block');
					      	(function() {
							    document.id('mapalert').setStyle('display','none');
							  }).delay(5000);   
						}
				});
		}
    <?php } ?>	
	

window.addEvent("load", function(){
	<?php if($par->get('show_introdesc','1')){?>
	introdescLimit(<?php echo $par->get('introdesc_char_limit');?>);
	<?php } ?>
	<?php if($par->get('seo_metadesc_user_edit','0')){?>
	metadescLimit(<?php echo $par->get('seo_metadesc_char_limit','160');?>);
	<?php } ?>
	<?php if($title_char_limit>0){?>
	titleLimit(<?php echo $title_char_limit;?>);
	<?php } ?>
	getFields(<?php echo $this->item->cat_id; ?>);	
	<?php if($par->get('allow_user_lat_lng','0')){ ?>
	initDjMap();
	<?php } ?>	
});

window.addEvent('domready', function(){ 
   var JTooltips = new Tips($$('.Tips1'), {
      showDelay: 200, hideDelay: 200, className: 'djcf_label', fixed: true
   });
   var JTooltips = new Tips($$('.Tips2'), {
      showDelay: 200, hideDelay: 200, className: 'djcf_prom', fixed: false
   });
   
   document.formvalidator.setHandler('djcat', function(value) {
      regex=/^p/;
      return !regex.test(value);
   });

   document.formvalidator.setHandler('djemail', function(value) {
	   var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
	   if(re.test(value)){
		   if(document.id('guest_email').hasClass('djinvalid')){
			  	return false;  
			}else{
				return true;
			}
	   }else{
		   return false;
	   }
   });
   
   document.id('submit_button').addEvent('click', function(){
   	
	   var cat_list = 	document.id('after_cat_0').getElements('select.cat_sel.required');
	   cat_list.each(function(cat_l){
		    var check_s = /^p/;
		    if(check_s.test(cat_l.get('value'))){			   
			   cat_l.addClass('invalid');
			   cat_l.setAttribute("aria-invalid", "true");
			   document.id('cat_0-lbl').addClass('invalid');
			   document.id('cat_0-lbl').setAttribute("aria-invalid", "true");			   
			   //console.log('invalid');		   
			}else{
			   cat_l.removeClass('invalid');
			   cat_l.setAttribute("aria-invalid", "false");
			   document.id('cat_0-lbl').removeClass('invalid');
			   document.id('cat_0-lbl').setAttribute("aria-invalid", "false");
			   //console.log('ok');
			}				   
		})
        
      if(document.getElements('#djForm .invalid').length>0){
      	document.id('verification_alert').setStyle('display','block');
      	(function() {
		    document.id('verification_alert').setStyle('display','none');
		  }).delay(3000);      	
      	  return false;
      }else{
      	  return true;
      }             
	});
});

</script>