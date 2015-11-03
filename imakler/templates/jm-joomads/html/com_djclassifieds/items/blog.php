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
$toolTipArray = array('className'=>'djcf');
JHTML::_('behavior.tooltip', '.Tips1', $toolTipArray);
$par = JComponentHelper::getParams( 'com_djclassifieds' );
$user= JFactory::getUser();

$main_id= JRequest::getVar('cid', 0, '', 'int');
$se = JRequest::getVar('se', '0', '', 'int');
$fav_a	= $par->get('favourite','1');
$icon_new_a	= $par->get('icon_new','1');
$icon_new_date = mktime(date("G"), date("i"), date("s"), date("m"), date("d")-$par->get('icon_new_time','3'), date("Y"));

$order = JRequest::getCmd('order', $par->get('items_ordering','date_e'));
$ord_t = JRequest::getCmd('ord_t', $par->get('items_ordering_dir','desc'));
if($ord_t=="desc"){
	$ord_t='asc';
}else{
	$ord_t='desc';
}

$sw = JRequest::getVar('search', '', '', 'string');
$uid	= JRequest::getVar('uid', 0, '', 'int');
$se = JRequest::getVar('se', '0', '', 'int');

$Itemid = JRequest::getVar('Itemid', 0, 'int');

if($main_id>0 || $se>0 || JRequest::getInt('fav','0') || $uid>0 || ($main_id==0 && $par->get('items_in_main_cat',1)) ){
?>

<div class="djcf_items_blog">
  
 </div>

	<div class="dj-items-blog">
	
		<?php

		$blog_sort_v = $par->get('blog_sorting_fields',array());
		if($par->get('blog_sorting',0) && count($blog_sort_v)){			
			$order = JRequest::getCmd('order', $par->get('items_ordering','date_e'));
			$ord_t = JRequest::getCmd('ord_t', $par->get('items_ordering_dir','desc'));
			$current_uri = JFactory::getURI();
			?>
			<div class="blog_sorting_box">
				<form action="<?php echo $current_uri; ?>" method="get" name="djblogsort" id="djblogsort_form" >
					<select autocomplete="off" id="blogorder_select" class="inputbox" >
						<?php 				
							foreach($blog_sort_v as $sort_v){
								$option_selected = '';
								if($order==$sort_v && $ord_t=='asc'){
									$option_selected = 'selected="SELECTED"';
								}						
								echo '<option value="'.$sort_v.'-asc" '.$option_selected.' >';
									echo JText::_('COM_DJCLASSIFIEDS_SORT_BY').' '; 
									if($sort_v=='date_a'){ echo JText::_('COM_DJCLASSIFIEDS_DATE_ADDED');
									}else if($sort_v=='date_sort'){ echo JText::_('COM_DJCLASSIFIEDS_DATE_EXPIRATION');
									}else if($sort_v=='title'){ echo JText::_('COM_DJCLASSIFIEDS_TITLE');
									}else if($sort_v=='cat'){ echo JText::_('COM_DJCLASSIFIEDS_CATEGORY');
									}else if($sort_v=='loc'){ echo JText::_('COM_DJCLASSIFIEDS_LOCALIZATION');
									}else if($sort_v=='price'){ echo JText::_('COM_DJCLASSIFIEDS_PRICE');
									}else if($sort_v=='display'){ echo JText::_('COM_DJCLASSIFIEDS_DISPLAYED'); 
									}else if($sort_v=='distance'){ echo JText::_('COM_DJCLASSIFIEDS_DISTANCE'); }
									else{echo $sort_v;}
									echo ' '.JText::_('COM_DJCLASSIFIEDS_SORT_BY_ASC');
								echo  '</option>';
								
								$option_selected = '';
								if($order==$sort_v && $ord_t=='desc'){
									$option_selected = 'selected="SELECTED"';
								}
								
								echo '<option value="'.$sort_v.'-desc" '.$option_selected.' >';
									echo JText::_('COM_DJCLASSIFIEDS_SORT_BY').' '; 
									if($sort_v=='date_a'){ echo JText::_('COM_DJCLASSIFIEDS_DATE_ADDED');
									}else if($sort_v=='date_sort'){ echo JText::_('COM_DJCLASSIFIEDS_DATE_EXPIRATION');
									}else if($sort_v=='title'){ echo JText::_('COM_DJCLASSIFIEDS_TITLE');
									}else if($sort_v=='cat'){ echo JText::_('COM_DJCLASSIFIEDS_CATEGORY');
									}else if($sort_v=='loc'){ echo JText::_('COM_DJCLASSIFIEDS_LOCALIZATION');
									}else if($sort_v=='price'){ echo JText::_('COM_DJCLASSIFIEDS_PRICE');
									}else if($sort_v=='display'){ echo JText::_('COM_DJCLASSIFIEDS_DISPLAYED'); 
									}else if($sort_v=='distance'){ echo JText::_('COM_DJCLASSIFIEDS_DISTANCE'); }
									else{echo $sort_v;}
									echo ' '.JText::_('COM_DJCLASSIFIEDS_SORT_BY_DESC');
								echo  '</option>';
							}	
						?>
					</select> 
					<input type="hidden" name="order" id="blogorder_v" value="<?php echo $order;?>" />
					<input type="hidden" name="ord_t" id="blogorder_t_v" value="<?php echo $ord_t;?>" />
					<script type="text/javascript">
						window.addEvent('load', function(){		
							var slider_box = document.id('blogorder_select');
							slider_box.addEvent('change',function(event){
								var order_v = this.value.toString().split('-');
								document.id('blogorder_v').value=order_v[0];
								document.id('blogorder_t_v').value=order_v[1];
								document.id('djblogsort_form').submit();
							})
						});
					</script>
					<?php
					if($se){
						echo '<input type="hidden" name="se" value="1" />';							
						if($sw){ echo '<input type="hidden" name="search" value="'.$sw.'" />';}
						foreach($_GET as $key=>$get_v){
							if(strstr($key, 'se_')){
								if(is_array($get_v)){
									for($gvi=0;$gvi<count($get_v);$gvi++){
										echo '<input type="hidden" name="'.$key.'[]" value="'.$get_v[$gvi].'" />';
									}
								}else{
									echo '<input type="hidden" name="'.$key.'" value="'.$get_v.'" />';
								}
							}
						}
					}
					?>					
				</form>	
			</div>		
			<?php 	
		} ?>

		<div class="djcf_items_blog">
       <h1 class="header_info"><strong>Son elavə olunmuş elanlar</strong></h1>
		<?php 		
		$r=TRUE;

		if($par->get('showitem_jump',0)){
			$anch = '#dj-classifieds';
		}else{
			$anch='';
		}
		//$img_w = $par->get("middleth_width")+15;
		$col_n = 100 / $par->get('blog_columns_number',2) - 0.1;
		$col_limit = $par->get('blog_columns_number',2);	
		$ii=0;	
		
		foreach($this->items as $i){
			if(!$i->alias){
				$i->alias = DJClassifiedsSEO::getAliasName($i->name);
			}
			if(!$i->c_alias){
				$i->c_alias = DJClassifiedsSEO::getAliasName($i->c_name);					
			}			
			$cn= $ii%$par->get('blog_columns_number');
			//echo $col_limit.' '.$cn;
			if($cn==$col_limit-1){
				$cn .= ' last_col';
			}
			$ii++;
			//if($i->special==1){$row=' special special_first';}else{$row='';}
			$row = '';
			if($i->promotions){
				$row .=' promotion '.str_ireplace(',', ' ', $i->promotions);
			}
			$icon_fav=0;
			if($user->id>0 && $fav_a){
				if($i->f_id){
					$icon_fav=1;
					$row .= ' item_fav';  
				}
			}
			$icon_new=0;		
			$date_start = strtotime($i->date_start);
			if($date_start>$icon_new_date && $icon_new_a){
				$icon_new=1;
				$row .= ' item_new';  
			}
			echo '<div class="item_box'.$row.' clearfix" style="width:'.$col_n.'%;"><div class="item_box_bg'.$cn.'"><div class="item_box_in"><div class="item_box_in2">';
			echo '<div class="title">';
						
				if($par->get('show_types','0') && $i->type_id>0){
					if(isset($this->types[$i->type_id])){
						$type = $this->types[$i->type_id];
						if($type->params->bt_class){
							$bt_class = ' '.$type->params->bt_class;
						}else{
							$bt_class = '';
						}	
						if($type->params->bt_use_styles){
						 	$style='style="display:inline-block;
						 			border:'.(int)$type->params->bt_border_size.'px solid '.$type->params->bt_border_color.';'
						 		   .'background:'.$type->params->bt_bg.';'
						 		   .'color:'.$type->params->bt_color.';'
						 		   .$type->params->bt_style.'"';
								   echo '<span class="type_button'.$bt_class.'" '.$style.' >'.$type->name.'</span>';							
						}else{
							echo '<span class="type_label'.$bt_class.'" >'.$type->name.'</span>';		
						}
					}
				}								
				if($icon_fav){
					echo ' <img src="'.JURI::base().'/components/com_djclassifieds/assets/images/fav_a.png" width="16px" class="fav_ico"/>';
				}			
				if($icon_new){
					echo ' <span class="new_icon">'.JText::_('COM_DJCLASSIFIEDS_NEW').'</span>';
				}
			echo '</div>';
			echo '<div class="blog_det">';	
				
				if($par->get('blog_desc_position','right')=='right'){
					echo '<div class="item_box_right">';
				}									
					if(count($i->images) && $par->get('column_image','1')){
						echo '<div class="item_img">';						
						echo '<a href="'.DJClassifiedsSEO::getItemRoute($i->id.':'.$i->alias,$i->cat_id.':'.$i->c_alias).$anch.'">';					
							echo '<img src="'.JURI::base().$i->images[0]->thumb_m.'" alt="'.str_ireplace('"', "'", $i->images[0]->caption).'"  />'; 
						echo '</a>';
						echo '</div>';
					}			
					if($par->get('column_date_a','1')){
						echo '<div class="date_blog">';
						echo '<div class="date_start"><div class="date_before"></div><span class="label_title"></span>'.$i->date_start.'</div>';
						echo '</div>';
					}	
                    if($par->get('blog_price','1') && $i->price && $par->get('show_price','1')){
                        echo '<div class="price"><span class="label_title"></span>';
                            echo DJClassifiedsTheme::priceFormat($i->price,$i->currency);
                        echo '</div>';  
                    }
					
                    if($par->get('blog_category','0')){
                        echo '<div class="category"><span class="label_title"></span>';
                        if($par->get('blog_category','0')==2){
                            echo '<a href="'.DJClassifiedsSEO::getCategoryRoute($i->cat_id.':'.$i->c_alias).'" >'.$i->c_name.'</a>';
                        }else{
                            echo $i->c_name;    
                        }                       
                        echo '</div>';
                    }
					
                    
                    
                    
                    																			
								//echo '<div class="cat_name"><span class="label_title">'.JText::_('COM_DJCLASSIFIEDS_CATEGORY').'</span>'.$i->c_name.'</div>';								
				if($par->get('blog_desc_position','right')=='right') {
					/* Вырезаны описания */	
					echo '<div style="clear:both"></div>';	
					echo '</div>';		
				}						
					/* if($par->get('blog_desc_position','right')=='bottom'){			
						echo '<div class="item_box_bottom">';		
							echo '<div class="item_desc"><span class="label_title">'.JText::_('COM_DJCLASSIFIEDS_DESCRIPTION').'</span>';
								echo '<span class="desc_info">'.mb_substr(strip_tags($i->intro_desc), 0,$par->get('introdesc_char_limit','120'),'UTF-8');
							echo '</span></div>';					
						echo '</div>';	
					}	*/	
                    		
     
						
					
						
					foreach($i->fields as $f_id => $field){
						if($this->custom_fields[$f_id]->in_blog && $field!=''){
							echo '<div class="cf_box">';
								echo '<span class="label_title">'.$this->custom_fields[$f_id]->label.' : </span>';
								if($this->custom_fields[$f_id]->type=='checkbox'){
									echo str_ireplace(';', ', ', substr($field,1,-1));
								}else if($this->custom_fields[$f_id]->type=='link'){
									if($field==''){echo '---'; }
									else{
										if(strstr($field, 'http://') || strstr($field, 'https://')){
											echo '<a '.$this->custom_fields[$f_id]->params.' href="'.$field.'">'.str_ireplace(array("http://","https://"), array('',''), $field).'</a>';;
										}else{
											echo '<a '.$this->custom_fields[$f_id]->params.' href="http://'.$field.'">'.$field.'</a>';;
										}
									}
								}else{
									
									if($f_id=='41'){echo '<div class="ploshad_before"></div><div class="ploshad">'.$field.'&nbsp;м<sup><small>2</small></sup></div>'; }
									if($f_id=='50'){echo '<div class="sotok_before"></div><div class="sotok">'.$field.'&nbsp;сот</div>'; }
									if($f_id=='40'){echo '<div class="room_before"></div><div class="room">'.$field.'</div>'; }
									if($f_id=='39'){echo '<div class="etaj_before"></div><div class="etaj">'.$field.'</div>'; }
									if($f_id=='38'){echo '<div class="etaj_before"></div><div class="etajnost">'.$field.'</div>'; }
							
								}   
                           							
							echo '</div>';
						
						}
					}		
                  
                    
			echo '<div class="border_seperator"></div>'	;	
		    echo '<div class="blog_adress_before"></div><div class="blog_adress">'.$i->address.'</div>';
			echo '<div class="blog_item_views">Baxış sayı: '.$i->display.'</div>';
			echo '</div>';
			
			if(strstr($i->promotions, 'p_special')){
				echo '<span class="p_special_img">&nbsp;</span>';
			} 
			echo '<div class="item_box_hover"><div class="item_box_show">' ;
			echo '<div class="before_title_in"></div><h2><a href="'.DJClassifiedsSEO::getItemRoute($i->id.':'.$i->alias,$i->cat_id.':'.$i->c_alias).$anch.'" >'.$i->name.'</a></h2>';	
			if($par->get('blog_location','1') && $i->r_name && $par->get('show_regions','1')){
                        echo '<div class="before_city_in"></div><div class="region"><span class="label_title"></span>'.$i->r_name.'</div>';
                    }
			echo '<div class="adress_in_before"></div><div class="adress_in">'.$i->address.'</div>';
			
			
			
			foreach($i->fields as $f_id => $field){
						if($this->custom_fields[$f_id]->in_blog && $field!=''){
							echo '<div class="cf_box">';
								
								if($this->custom_fields[$f_id]->type=='checkbox'){
									echo str_ireplace(';', ', ', substr($field,1,-1));
								}else if($this->custom_fields[$f_id]->type=='link'){
									if($field==''){echo '---'; }
									else{
										if(strstr($field, 'http://') || strstr($field, 'https://')){
											echo '<a '.$this->custom_fields[$f_id]->params.' href="'.$field.'">'.str_ireplace(array("http://","https://"), array('',''), $field).'</a>';;
										}else{
											echo '<a '.$this->custom_fields[$f_id]->params.' href="http://'.$field.'">'.$field.'</a>';;
										}
									}
								}else{
									
									if($f_id=='47'){echo '<div class="vladelec_in_before"></div><div class="vladelec_in">'.$field.'</div>'; }
									if($f_id=='44'){echo '<div class="doc_in_before"></div><div class="doc_in">'.$field.'</div>'; }
									if($f_id=='37'){echo '<div class="metro_in">'.$field.'</div>'; }
									
									
                                    									
								}   
                           							
							echo '</div>';
						
						}
					}		
                  
			
			
			
			
			if($par->get('blog_readmore','1')){					
						echo '<div class="see_details_box"><a class="see_details" href="'.DJClassifiedsSEO::getItemRoute($i->id.':'.$i->alias,$i->cat_id.':'.$i->c_alias).$anch.'" >'.JText::_('Ətraflı').'</a></div>';
					}	
			echo '</div></div></div></div></div></div>';

		}
		?>	
		<?php
			echo '<div style="clear:both" ></div>';
			
		if(count($this->items)==0){
			echo '<div class="no_results" style="padding-left:30px;">';
				if($se>0){
					echo JText::_('COM_DJCLASSIFIEDS_NO_RESULTS');	
				}else if($main_id){
					echo JText::_('COM_DJCLASSIFIEDS_NO_CATEGORY_RESULTS');
				}
				
			echo '</div>';
		}
		?>
		</div>
		<?php 
			if($this->pagination->getPagesLinks()){
				echo '<div class="pagination" >';
				echo $this->pagination->getPagesLinks();
				echo '</div>';
			}		
		?>
	</div>	
<?php }?>
</div>

 <!-- script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script!-->
<script>
$( ".p_special" ).clone().prependTo( ".vip-ads" );
</script>
<script type="text/javascript">
	function DJCatMatchModules(className){
		var maxHeight = 0;
		var divs = null;
		if (typeof(className) == 'string') {
			divs = document.id(document.body).getElements(className);
		} else {
			divs = className;
		}
		if (divs.length > 1) {						
			divs.each(function(element) {
				//maxHeight = Math.max(maxHeight, parseInt(element.getStyle('height')));
				maxHeight = Math.max(maxHeight, parseInt(element.getSize().y));
			});
			
			divs.setStyle('height', maxHeight);
			
		}
}

window.addEvent('load', function(){
	DJCatMatchModules('.item_box_in2');
});

</script>