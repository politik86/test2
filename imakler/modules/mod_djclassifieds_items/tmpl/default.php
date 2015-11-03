<?php
/**
* @version 2.0
* @package DJ Classifieds Menu Module
* @subpackage DJ Classifieds Component
* @copyright Copyright (C) 2010 DJ-Extensions.com LTD, All rights reserved.
* @license http://www.gnu.org/licenses GNU/GPL
* @author url: http://design-joomla.eu
* @author email contact@design-joomla.eu
* @developer Łukasz Ciastek - lukasz.ciastek@design-joomla.eu
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

$cols = $params->get('columns_nr','1');
$items_in_col =ceil (count($items) / $cols);
$col_nr = 1;  
$item_c = 0;
$last_row = count($items)%$cols;
$items_in_lr= $last_row;
?>
<div class="mod_djclassifieds_items clearfix">
	<div class="items items-cols<?php echo $cols; ?>">
		<div class="items-col icol<?php echo $col_nr; ?>"><div class="icol-in">
		<?php	

			foreach($items as $i){
				if(!$i->alias){
					$i->alias = DJClassifiedsSEO::getAliasName($i->name);
				}
				if(!$i->c_alias){
					$i->c_alias = DJClassifiedsSEO::getAliasName($i->c_name);					
				}						
				if($item_c==$items_in_col){
					$col_nr++;
					echo '<div style="clear:both"></div>';
					echo '</div></div><div class="items-col icol'.$col_nr.'"><div class="icol-in">';
					$item_c=0;
				}				
				
				if($item_c==$items_in_col-1 && $last_row==0 && $items_in_lr!=0){
					$col_nr++;
					echo '<div style="clear:both"></div>';
					echo '</div></div><div class="items-col icol'.$col_nr.'"><div class="icol-in">';
					$item_c=0;
				} 
				
				if($item_c==$items_in_col-1 && $last_row>0){
					$last_row--;
				}
				
				$item_c++;	
				
				$item_class='';
				if($i->promotions){
					$item_class .=' promotion '.str_ireplace(',', ' ', $i->promotions);
				}
				
				echo '<div class="item'.$item_class.'">';
				echo '<div class="title">';
				if($params->get('show_img')==1){
					if($i->img_path && $i->img_name && $i->img_ext){			
						echo '<a class="title_img" href="'.JRoute::_(DJClassifiedsSEO::getItemRoute($i->id.':'.$i->alias,$i->cat_id.':'.$i->c_alias)).'">';
							$img_width = ($params->get('img_width','') ? ' width="'.$params->get('img_width','').'px" ' : '');
							$img_height = ($params->get('img_height','') ? ' height="'.$params->get('img_height','').'px" ' : '');
							echo '<img '.$img_width.$img_height.' style="margin-right:3px;" src="'.JURI::base().$i->img_path.$i->img_name.'_'.$params->get('img_type','ths').'.'.$i->img_ext.'" alt="'.str_ireplace('"', "'", $i->name).'" title="'.$i->img_caption.'" />';
						echo '</a>';
					}else if($params->get('show_default_img','0')>0){			
						echo '<a class="title_img" href="'.JRoute::_(DJClassifiedsSEO::getItemRoute($i->id.':'.$i->alias,$i->cat_id.':'.$i->c_alias)).'">';								
						
						$show_cat_icon = false;
						if(isset($cat_images[$i->cat_id])){							
							if($cat_images[$i->cat_id]->name){$show_cat_icon = true;}
						}						
						if($params->get('show_default_img','0')==2 && $show_cat_icon){		
							echo '<img style="margin-right:3px;" src="'.JURI::base().$cat_images[$i->cat_id]->path.$cat_images[$i->cat_id]->name.'_ths.'.$cat_images[$i->cat_id]->ext.'" alt="'.str_ireplace('"', "'", $i->name).'" title="'.$cat_images[$i->cat_id]->caption.'" />';							
						}else{
							echo '<img style="margin-right:3px;" src="'.JURI::base().'/components/com_djclassifieds/assets/images/no-image.png" alt="'.str_ireplace('"', "'", $i->name).'" />';
						}
						echo '</a>';
					}
				}
				
				
				if(($params->get('show_date')==1) || ($params->get('show_cat')==1) || ($params->get('show_price')==1) || ($params->get('show_type','1'))){
					echo '<div class="date_cat">';
					if($params->get('show_date')==1){
						echo '<div class="date_blog">';
						echo '<div class="date_start"><div class="date_before"></div><span class="label_title"></span>'.$i->date_start.'</div>';
						echo '</div>';
					}
					if($params->get('show_price')==1 && $i->price){
						echo '<div class="price"><span class="label_title"></span>';
                            echo DJClassifiedsTheme::priceFormat($i->price,$i->currency);
                        echo '</div>';  
					}
					if($params->get('show_cat')==1){				
						echo '<span class="category">';
						if($params->get('cat_link')==1){
							
							echo '<a class="title_cat" href="'.JRoute::_(DJClassifiedsSEO::getCategoryRoute($i->cat_id.':'.$i->c_alias)).'">'.$i->c_name.'</a>';
						}else{
							echo $i->c_name;
						}				
						echo '</span>';
					}	
					if($params->get('show_type','1') && $i->type_id>0){
						if(isset($types[$i->type_id])){
							echo '<span class="type">';
								$type = $types[$i->type_id];
								if($type->params->bt_class){
									$bt_class = ' '.$type->params->bt_class;
								}else{
									$bt_class = '';
								}	
								if($type->params->bt_use_styles){
									if($params->get('show_type','1')==2){
									 	$style='style="display:inline-block;
									 			border:'.(int)$type->params->bt_border_size.'px solid '.$type->params->bt_border_color.';'
									 		   .'background:'.$type->params->bt_bg.';'
									 		   .'color:'.$type->params->bt_color.';'
									 		   .$type->params->bt_style.'"';
											   echo '<span class="type_button'.$bt_class.'" '.$style.' >'.$type->name.'</span>';										
									}else{
										echo '<span class="type_label'.$bt_class.'" >'.$type->name.'</span>';	
									}							
								}else{
									echo '<span class="type_label'.$bt_class.'" >'.$type->name.'</span>';		
								}
							echo '</span>';
						}
					}						
						
					
					echo '</div>';
				}		
				echo '</div>';
		
				if($params->get('show_description')==1){
					echo '<div class="desc">';
					if($params->get('desc_source','0')==1){
						echo $i->description;
					}else{
						if($params->get('desc_link')==1){
							echo '<a href="'.JRoute::_(DJClassifiedsSEO::getItemRoute($i->id.':'.$i->alias,$i->cat_id.':'.$i->c_alias)).'">';
						}
							$desc_c = $params->get('char_desc_nr');
							if($desc_c!=0 && $i->intro_desc!='' && strlen($i->intro_desc)>$desc_c){
								echo mb_substr($i->intro_desc, 0, $desc_c,'utf-8').' ...';
							}else{
								echo $i->intro_desc;
							}
						if($params->get('desc_link')==1){
							echo '</a>';
						}	
					} 
					
					echo '</div>';
				}
				echo '<div class="border_seperator"></div>'	;	
		    echo '<div class="blog_adress_before"></div><div class="blog_adress">'.$i->address.'</div>';
			echo '<div class="blog_item_views">Baxış sayı: '.$i->display.'</div>';
				echo '<div class="item_box_hover"><div class="item_box_show">' ;
			    echo '<div class="before_title_in"></div><h2><a href="'.DJClassifiedsSEO::getItemRoute($i->id.':'.$i->alias,$i->cat_id.':'.$i->c_alias).$anch.'" >'.$i->name.'</a></h2>';
		        echo '<div class="before_city_in"></div><div class="region"><span class="label_title"></span>'.$i->r_name.'</div>';
				echo '<div class="adress_in_before"></div><div class="adress_in">'.$i->address.'</div>';
				
				
				
				echo '</div></div>' ;
				
				
				echo '</div>';
								
			}
			
		?>
		<div style="clear:both"></div>
		</div></div>
	</div>
</div>