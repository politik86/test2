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

		$document = JFactory::getDocument();
        //$document->addScript( JURI::root()."components/com_adagency/includes/js/graybox.js" );
		$document->addScript(JURI::root().'components/com_adagency/includes/js/jquery.fcbkcomplete.js');
		$document->addStyleSheet(JUri::root()."components/com_adagency/includes/css/fcb.css");

		require_once(JPATH_SITE.DS."components".DS."com_adagency".DS."includes".DS."js".DS."geo.php");

		$db =  JFactory::getDBO();

		$db->setQuery('SELECT geoparams FROM #__ad_agency_settings LIMIT 1');

		$configs = $db->loadColumn();

		$configs = @unserialize($configs["0"]);

		

		$db->setQuery('SELECT * FROM #__ad_agency_settings LIMIT 1');

		$configs2 = $db->loadObject();

		if(!isset($configs['allowgeo'])&&!isset($configs['allowgeoexisting'])) {

			echo JText::_('ADAG_GEO_NONE1').' <a href="index.php?option=com_adagency&controller=adagencyGeo&task=settings">'.JText::_('ADAG_GEO_NONE2').'</a>'.JText::_('ADAG_GEO_NONE3');

		} elseif((!file_exists(str_replace("administrator","",JPATH_BASE)."/".$configs2->countryloc."/country-AD.txt"))||(!file_exists(str_replace("administrator","",JPATH_BASE)."/".$configs2->cityloc)||(!strpos($configs2->cityloc,'.dat')))||(!file_exists(str_replace("administrator","",JPATH_BASE)."/".$configs2->codeloc."/areacode.txt"))) {

			echo JText::_('ADAG_GEO_NOT_UPL1')." <a href='index.php?option=com_adagency&controller=adagencyGeo&task=settings'>".JText::_('ADAG_GEO_NOT_UPL2')."</a>";

		} else {

?>

		<style>

        	.geo_title {

				width:300px;

			}

        </style>

		<table class="content_table" id="geo_targeting_table" width="100%" border="0" cellpadding="0" cellspacing="0" style="padding-bottom:5px;">

        	<tr>

            	<td class="sectiontableheader" colspan="2"><?php echo JText::_('ADAG_GEO'); ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a target="_blank" href="http://www.ijoomla.com/redirect/adserver/geo/geotab.htm"><?php echo JText::_('AD_VIDEO');?></a>&nbsp;&nbsp;<a target="_blank" href="http://www.ijoomla.com/redirect/adserver/geo/geotab.htm"><img alt="watch video" src="components/com_adagency/images/icon_video.gif"></a></td>

            </tr>

           <!-- <tr>

            	<td><?php echo JText::_('ADAG_GEO_OTHER');?></td>

                <td><select id='populate_geo' onchange="fpopulate();">

				<option value='0'><?php echo strtolower(JText::_('ADSELBANNER'));?></option>

				<?php

					$sql = "SELECT * FROM #__ad_agency_channels WHERE advertiser_id = '".$aid."'";

					$db->setQuery($sql);

					$list = $db->loadObjectList();

					foreach($list as $element){

						echo "<option value='".$element->id."'>".$element->name."</option>";

					}

                	//echo $aid;

				?></select></td>

            </tr>-->

            <?php if(isset($configs['allowgeo']) && ($configs['allowgeo'] == '1')) { ?>

            <?php if((isset($configs['allowcountry'])&&($configs['allowcountry'] == '1'))||(isset($configs['allowcontinent'])&&($configs['allowcontinent'] == '1'))||(isset($configs['allowlatlong'])&&($configs['allowlatlong'] == '1'))||(isset($configs['c6'])&&($configs['c6'] == '1'))||(isset($configs['c4'])&&($configs['c4'] == '1'))||(isset($configs['c5'])&&($configs['c5'] == '1'))) { ?>

            <tr>

                <td style="padding-bottom: 10px;">

                    &nbsp;&nbsp;

                    <div class="geo_title"><input type="radio" align="absmiddle" name="geo_type" id="geo_type1" value="1" />&nbsp;<?php echo JText::_('ADAG_DELIVERY_LIM');?>&nbsp;&nbsp;

                        <span class="adag_tip">

                            <img src="components/com_adagency/images/tooltip.png" border="0" />

                            <span><?php echo JText::_('ADAG_DELIVERY_LIM_TIP'); ?></span>

                        </span>

                    </div>

                </td>

            </tr>

            <tr>

				<td style="padding-bottom: 5px;">&nbsp;&nbsp;<select name="limitation" id="limitation" onchange="javascript:selim();">

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

				</select></td>

			</tr>

            <tr>

            	<td>

                    <div id="geo_container">

                    <!--<hr style="color: #0B55C4;" /><br />

                    <div style="padding-bottom:6px;">&nbsp;&nbsp;&nbsp;<?php echo JText::_('ADAG_DELIVERY_LIM2'); ?>:</div><div style="padding: 3px 0px; background-color:#f6f6f6; border-top: 1px solid #666666; border-bottom: 1px solid #666666;">&nbsp;&nbsp;&nbsp;<?php echo JText::_('ADAG_DELIVERY_LIM3'); ?>:</div><p />-->

                    <table id="opts" name="opts" style="border-collapse:collapse;margin-bottom:10px;" cellpadding="2" cellspacing="2" width="100%">

                        <tbody id="tbdy">

                        </tbody>

                    </table><!--<p />

                    &nbsp;&nbsp;<a href="#" id="remall" onClick="removeAll();"><img id="removeall" align="absmiddle" src="administrator/components/com_adagency/images/delete-icon.gif" title="Remove limitations" alt="Remove limitations" />&nbsp;<?php echo JText::_('ADAG_REM_LIM');?></a><p />-->

                    <input type="hidden" id="numberoflims" value="1" />



                    </div>

                </td>

			</tr>

            <?php

				} else {

						echo "<tr><td style='font-weight:bold;'><br />".JText::_('ADAG_NO_GEO_TYPE_SEL')."</td></tr>";

						//echo "<tr><td align='center'>-</td></tr>";

					}

				}

				if(isset($configs['allowgeoexisting']) && ($configs['allowgeoexisting'] == '1')) {

			?>

			<tr>

                <td style="padding-bottom: 10px;">&nbsp;&nbsp;<div class="geo_title"><input type="radio" align="absmiddle" name="geo_type" id="geo_type2" value="2" />&nbsp;<?php echo JText::_('ADAG_GEO_ADD_EXISTING');?>

                    &nbsp;&nbsp;

                    <span class="adag_tip">

                        <img src="components/com_adagency/images/tooltip.png" border="0" />

                        <span><?php echo JText::_('ADAG_GEO_ADD_EXISTING_TIP'); ?></span>

                    </span>

                </div></td>

            </tr>

            <tr>

            	<td>&nbsp;&nbsp;<select name="limitation_existing" id="limitation_existing" onchange="fpopulate2();">

				<option value='0'><?php echo JText::_('ADAG_SELECT_CHANNEL');?></option>

				<?php

					$sql = "SELECT * FROM `#__ad_agency_channels` order by `ordering` asc";

					$db->setQuery($sql);

					$result = $db->loadObjectList();

					if(isset($result)){

						foreach($result as $element){

							echo "<option value='".$element->id."'>".$element->name."</option>";

						}

					}

				?>

				</select>

                <div id="existing_container">

                </div>

                </td>

            </tr>

            <?php } ?>

		</table>

<?php } ?>

<script language="javascript" type="text/javascript" src="<?php echo JURI::root()."components/com_adagency/includes/js/graybox.js"; ?>"></script>

