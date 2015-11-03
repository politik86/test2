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

	function output_geoform($aid = NULL){
		$db = JFactory::getDBO();
		$db->setQuery('SELECT geoparams FROM #__ad_agency_settings LIMIT 1');
		$configs = @unserialize($db->loadResult());
		$db->setQuery('SELECT * FROM #__ad_agency_settings LIMIT 1');
		$configs2 = $db->loadObject();
		//echo "<pre><hr />";var_dump($configs);echo "</pre><hr />";
		if((!isset($configs['allowgeo'])&&!isset($configs['allowgeoexisting']))||((!file_exists(str_replace("administrator","",JPATH_BASE)."/".$configs2->countryloc."/country-AD.txt"))||(!file_exists(str_replace("administrator","",JPATH_BASE)."/".$configs2->cityloc)||(!strpos($configs2->cityloc,'.dat')))||(!file_exists(str_replace("administrator","",JPATH_BASE)."/".$configs2->codeloc."/areacode.txt")))) { return false; }
?>
		
        <div class="control-group">
            <div>
                <span class="agency_subtitle"><?php echo JText::_('ADAG_GEO'); ?></span>
            </div>
        </div>
     
               <div id="geo_targeting_table" width="100%" border="0" cellpadding="0" cellspacing="0">
                  
                    <?php if(isset($configs['allowgeo']) && ($configs['allowgeo'] == '1')) { ?>
                    <?php if((isset($configs['allowcountry'])&&($configs['allowcountry'] == '1'))||(isset($configs['allowcontinent'])&&($configs['allowcontinent'] == '1'))||(isset($configs['allowlatlong'])&&($configs['allowlatlong'] == '1'))||(isset($configs['c6'])&&($configs['c6'] == '1'))||(isset($configs['c4'])&&($configs['c4'] == '1'))||(isset($configs['c5'])&&($configs['c5'] == '1'))) { ?>
                    <div class="adg_row adg_table_row">
                        <div class="adg_cell adg_table_cell span6" ><div class="geo_title"><input type="radio" align="absmiddle" name="geo_type" id="geo_type1" value="1" /><?php echo JText::_('ADAG_DELIVERY_LIM');?></div></div>
                   
                        <div class="adg_cell adg_table_cell span6"><select name="limitation" id="limitation" onchange="javascript:selim();">
                        <option value=""><?php echo JText::_('ADAG_SEL_TYPE');?></option>
                        <?php if(isset($configs['allowcountry'])&&($configs['allowcountry'] == '1')) { ?>
                            <option value="country"><?php echo JText::_('ADAG_COUNTRY_C_S');?></option>
                        <?php } ?>
                        <?php if(isset($configs['allowcontinent'])&&($configs['allowcontinent'] == '1')) { ?>
                            <option value="continent"><?php echo JText::_('ADAG_CONTINENT');?></option>
                        <?php } ?>
                        <!--<option value="region"><?php echo JText::_('ADAG_CREGION');?></option>-->
                        <!--<option value="city"><?php echo JText::_('ADAG_CCITY');?></option>-->
                        <?php if(isset($configs['allowlatlong'])&&($configs['allowlatlong'] == '1')) { ?>
                            <option value="latitude"><?php echo JText::_('ADAG_LATLONG');?></option>
                        <?php } ?>
                        <?php if(isset($configs['c6'])&&($configs['c6'] == '1')) { ?>
                            <option value="dma"><?php echo JText::_('ADAG_DMA');?></option>
                        <?php } ?>
                        <?php if(isset($configs['c4'])&&($configs['c4'] == '1')) { ?>
                            <option value="usarea"><?php echo JText::_('ADAG_USAREA');?></option>
                        <?php } ?>
                        <?php if(isset($configs['c5'])&&($configs['c5'] == '1')) { ?>
                            <option value="postalcode"><?php echo JText::_('ADAG_POSTAL_COD');?></option>
                        <?php } ?>
                        </select>
                        </div>
                    </div>
                    <div class="adg_row adg_table_row">
                        <div class="adg_cell adg_table_cell">
                            <div id="geo_container">
                            <!--<hr style="color: #0B55C4;" /><br />
                            <div style="padding-bottom:6px;">&nbsp;&nbsp;&nbsp;<?php echo JText::_('ADAG_DELIVERY_LIM2'); ?>:</div><div style="padding: 3px 0px; background-color:#f6f6f6; border-top: 1px solid #666666; border-bottom: 1px solid #666666;">&nbsp;&nbsp;&nbsp;<?php echo JText::_('ADAG_DELIVERY_LIM3'); ?>:</div><p />-->
                            <table id="opts" name="opts"  cellpadding="2" cellspacing="2" width="100%">
                                <tbody id="tbdy">
                                </tbody>
                            </table><!--<p />
                            &nbsp;&nbsp;<a href="#" id="remall" onClick="removeAll();"><img id="removeall" align="absmiddle" src="administrator/components/com_adagency/images/delete-icon.gif" title="Remove limitations" alt="Remove limitations" />&nbsp;<?php echo JText::_('ADAG_REM_LIM');?></a><p />-->
                            <input type="hidden" id="numberoflims" value="1" />
                            
                            </div>
                        </div>
                    </div>
                    <?php }
                        }
                        if(isset($configs['allowgeoexisting']) && ($configs['allowgeoexisting'] == '1')) {
                    ?>
                    <div class="adg_row adg_table_row">
                        <div class="adg_cell adg_table_cell span6"><div class="geo_title"><input type="radio" align="absmiddle" name="geo_type" id="geo_type2" value="2" /><?php echo JText::_('ADAG_GEO_ADD_EXISTING');?></div></div>
                        <div class="adg_cell adg_table_cell span6"><select name="limitation_existing" id="limitation_existing" onchange="fpopulate2();">
                        <option value='0'><?php echo JText::_('ADAG_SELECT_CHANNEL');?></option>
                        <?php
                            $user = JFactory::getUser();
                            if(isset($user->id)&&($user->id>0)) { $extra_user_condition = " OR created_by = ".$user->id; } else { $extra_user_condition = NULL;  }
                            $sql = "SELECT * FROM `#__ad_agency_channels` WHERE public = 'Y'".$extra_user_condition." order by `ordering` asc";
                            $db->setQuery($sql);
                            $result = $db->loadObjectList();
                            if(isset($result)){
                                foreach($result as $element){
                                    echo "<option value='".$element->id."'>".$element->name."</option>";	
                                }
                            }
                        ?>
                        </select>
                        </div>
                    </div>
                    <div class="adg_row adg_table_row">
                    	<div class="adg_cell adg_table_cell">
                    		<div id="existing_container">
                        	</div>    
                    	</div>
                    </div>
                    <?php } ?>
                </div>
<?php } ?>