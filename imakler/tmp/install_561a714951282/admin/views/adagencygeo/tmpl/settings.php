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
JHtml::_('behavior.modal');
JHtml::_('behavior.tooltip');
$configs = $this->configs;
$path1 = $configs->countryloc;
$path2 = $configs->cityloc;
$path3 = $configs->codeloc;
$document = JFactory::getDocument();
$document->addStyleSheet('components/com_adagency/css/joomla16.css');
$document->addStyleSheet(JURI::root()."components/com_adagency/includes/css/adag_tip.css");
$document->addStyleSheet(JURI::root()."administrator/components/com_adagency/css/joomla16.css");
$data = $this->data;
if((!file_exists(str_replace("administrator","",JPATH_BASE)."/".$configs->countryloc."/country-AD.txt"))||(!file_exists(str_replace("administrator","",JPATH_BASE)."/".$configs->cityloc)||(!strpos($configs->cityloc,'.dat')))||(!file_exists(str_replace("administrator","",JPATH_BASE)."/".$configs->codeloc."/areacode.txt"))) {
    $disabled = ' disabled="disabled" readonly="readonly" ';
    $data['allowgeo'] = 0;
    $data['allowgeoexisting'] = 0;
    $geo_notice = "<div id=\"system-message-container\">
							<div class=\"alert alert-notice\">
								<p>".JText::_("ADAG_GEO_NOTICE")."</p>
							</div>
						<div>";
} else { $disabled = NULL;$geo_notice = NULL; }
include("components/com_adagency/includes/js/geoset.php");

?>



<form class="form-horizontal" method="post" id="adminForm" name="adminForm" enctype="multipart/form-data">
	<?php echo $geo_notice; ?>
<div class="row-fluid">
	<a class="modal adagency-video-manager pull-right" rel="{handler: 'iframe', size: {x: 750, y: 435}}" href="index.php?option=com_adagency&controller=adagencyAbout&task=vimeo&id=14940854">
		<img src="<?php echo JURI::base(); ?>components/com_adagency/images/icon_video.gif" class="video_img" />
		<?php echo JText::_("AD_VIDEO"); ?>   
	</a>
</div>

<div class="well"><?php echo JText::_('ADAG_GEOT').' - '.JText::_('ADAG_GEOSET'); ?></div>

<div class="control-group">
	<label class="control-label"><?php echo JText::_('ADAG_ALLOW_GEO');?></label>
	<div class="controls">
			   <fieldset class="radio btn-group" id="allowgeo">
					<?php
						$yes_cheched = "";
						
						if (isset($data['allowgeo'])&&($data['allowgeo'] == '1')) {
							$yes_cheched = 'checked="checked"';
						}
					?>
					<input type="hidden" name="allowgeo" value="0">
					<input type="checkbox" <?php echo $yes_cheched; ?> value="1" class="ace-switch ace-switch-5" name="allowgeo">
					<span class="lbl"></span>
				</fieldset>
				<span class="editlinktip hasTip" title="<?php echo JText::_('ADAG_ALLOW_GEO_TIP'); ?>" >
				<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
	</div>
</div>

<div class="control-group">
	<label class="control-label"><?php echo JText::_('ADAG_ALLOW_GEO2');?></label>
	<div class="controls">
			   <fieldset class="radio btn-group" id="allowgeoexisting">
					<?php
						
						$yes_cheched = "";
						
						if (isset($data['allowgeoexisting']) && ($data['allowgeoexisting'] == '1')) {
							$yes_cheched = 'checked="checked"';
						}
					?>
					<input type="hidden" name="allowgeoexisting" value="0">
					<input type="checkbox" <?php echo $yes_cheched; ?> value="1" class="ace-switch ace-switch-5" name="allowgeoexisting">
					<span class="lbl"></span>
				</fieldset>
				<span class="editlinktip hasTip" title="<?php echo JText::_('ADAG_ALLOW_GEO2_TIP'); ?>" >
				<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
			
	</div>
</div>
	
	
<div class="well"><?php echo JText::_('ADAG_ALLOW_GEOPREF')?></div>

<div class="control-group">
	<label class="control-label"><?php echo JText::_('ADAG_CONTINENT');?></label>
	<div class="controls">
			   <fieldset class="radio btn-group" id="allowcontinent">
					<?php
						$yes_cheched = "";
						
						if (isset($data['allowcontinent'])  && ($data['allowcontinent'] == '1')) {
							$yes_cheched = 'checked="checked"';
						}
					?>
					<input type="hidden" name="allowcontinent" value="0">
					<input type="checkbox" <?php echo $yes_cheched; ?> value="1" class="ace-switch ace-switch-5" name="allowcontinent">
					<span class="lbl"></span>
				</fieldset>
				<span class="editlinktip hasTip" title="<?php echo JText::_('ADAG_CONTINENT_TIP'); ?>" >
				<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
			
	</div>
</div>

<div class="control-group">
	<label class="control-label"><?php echo JText::_('ADAG_CONTINENT');?></label>
	<div class="controls">
			   <fieldset class="radio btn-group" id="allowlatlong">
					<?php
						$yes_cheched = "";
						
						if (isset($data['allowlatlong']) && ($data['allowlatlong'] == '1')) {
							$yes_cheched = 'checked="checked"';
						}
					?>
					<input type="hidden" name="allowlatlong" value="0">
					<input type="checkbox" <?php echo $yes_cheched; ?> value="1" class="ace-switch ace-switch-5" name="allowlatlong">
					<span class="lbl"></span>
				</fieldset>
				<span class="editlinktip hasTip" title="<?php echo JText::_('ADAG_LATLONG_TIP'); ?>" >
				<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
			
	</div>
</div>
<div class="control-group">
	<label class="control-label"><?php echo JText::_('ADAG_LATLONG');?></label>
	<div class="controls">
			   <fieldset class="radio btn-group" id="allowcountry">
					<?php
						$yes_cheched = "";						
						if (isset($data['allowcountry']) && ($data['allowcountry'] == '1')) {
							$yes_cheched = 'checked="checked"';
						}
					?>
					<input type="hidden" name="allowcountry" value="0">
					<input type="checkbox" <?php echo $yes_cheched; ?> value="1" class="ace-switch ace-switch-5" name="allowcountry">
					<span class="lbl"></span>
				</fieldset>
				<span class="editlinktip hasTip" title="<?php echo JText::_('ADAG_COUNTRY_TIP'); ?>" >
				<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
			
	</div>
</div>

<div class="control-group">
	<label class="control-label"><?php echo JText::_('ADAG_GEO_EVERYWH');?></label>
	<div class="controls">
			   <fieldset class="radio btn-group" id="c1">
					<?php
						$yes_cheched = "";
						
						if (isset($data['c1']) && ($data['c1'] == '1')) {
							$yes_cheched = 'checked="checked"';
						}
						
					?>
					<input type="hidden" name="c1" value="0">
					<input type="checkbox" <?php echo $yes_cheched; ?> value="1" class="ace-switch ace-switch-5" name="c1">
					<span class="lbl"></span>
				</fieldset>
				<span class="editlinktip hasTip" title="<?php echo JText::_('ADAG_GEO_EVERYWH_TIP'); ?>" >
				<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
	</div>
</div>

<div class="control-group">
	<label class="control-label"><?php echo JText::_('ADAG_GEO_BYSTPRO');?></label>
	<div class="controls">
			   <fieldset class="radio btn-group" id="c2">
					<?php
						$yes_cheched = "";
						
						if (isset($data['c2']) && ($data['c2'] == '1')) {
							$yes_cheched = 'checked="checked"';
						}
						
					?>
					<input type="hidden" name="c2" value="0">
					<input type="checkbox" <?php echo $yes_cheched; ?> value="1" class="ace-switch ace-switch-5" name="c2">
					<span class="lbl"></span>
				</fieldset>
				<span class="editlinktip hasTip" title="<?php echo JText::_('ADAG_GEO_BYSTPRO_TIP'); ?>" >
				<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
	</div>
</div>

<div class="control-group">
	<label class="control-label"><?php echo JText::_('ADAG_GEO_BYCITY');?></label>
	<div class="controls">
			   <fieldset class="radio btn-group" id="c3">
					<?php
						$yes_cheched = "";
						
						if (isset($data['c3']) && ($data['c3'] == '1')) {
							$yes_cheched = 'checked="checked"';
						}
						
					?>
					<input type="hidden" name="c3" value="0">
					<input type="checkbox" <?php echo $yes_cheched; ?> value="1" class="ace-switch ace-switch-5" name="c3">
					<span class="lbl"></span>
				</fieldset>
				<span class="editlinktip hasTip" title="<?php echo JText::_('ADAG_GEO_BYCITY_TIP'); ?>" >
				<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
	</div>
</div>

<div class="control-group">
	<label class="control-label"><?php echo JText::_('ADAG_GEO_BYAREACOD');?></label>
	<div class="controls">
			   <fieldset class="radio btn-group" id="c4">
					<?php
						$yes_cheched = "";
						
						if (isset($data['c4']) && ($data['c4'] == '1')) {
							$yes_cheched = 'checked="checked"';
						}
						
					?>
					<input type="hidden" name="c4" value="0">
					<input type="checkbox" <?php echo $yes_cheched; ?> value="1" class="ace-switch ace-switch-5" name="c4">
					<span class="lbl"></span>
				</fieldset>
				<span class="editlinktip hasTip" title="<?php echo JText::_('ADAG_GEO_BYAREACOD_TIP'); ?>" >
				<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
	</div>
</div>

<div class="control-group">
	<label class="control-label"><?php echo JText::_('ADAG_GEO_BYZIPPC');?></label>
	<div class="controls">
			   <fieldset class="radio btn-group" id="c5">
					<?php
						$yes_cheched = "";
						
						if (isset($data['c5']) && ($data['c5'] == '1')) {
							$yes_cheched = 'checked="checked"';
						}
						
					?>
					<input type="hidden" name="c5" value="0">
					<input type="checkbox" <?php echo $yes_cheched; ?> value="1" class="ace-switch ace-switch-5" name="c5">
					<span class="lbl"></span>
				</fieldset>
				<span class="editlinktip hasTip" title="<?php echo JText::_('ADAG_GEO_BYZIPPC_TIP'); ?>" >
				<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
	</div>
</div>

<div class="control-group">
	<label class="control-label"><?php echo JText::_('ADAG_GEO_BYDMA');?></label>
	<div class="controls">
			   <fieldset class="radio btn-group" id="c6">
					<?php
						$yes_cheched = "";
						
						if (isset($data['c6']) && ($data['c6'] == '1')) {
							$yes_cheched = 'checked="checked"';
						}
						
					?>
					<input type="hidden" name="c6" value="0">
					<input type="checkbox" <?php echo $yes_cheched; ?> value="1" class="ace-switch ace-switch-5" name="c6">
					<span class="lbl"></span>
				</fieldset>
				<span class="editlinktip hasTip" title="<?php echo JText::_('ADAG_GEO_BYDMA_TIP'); ?>" >
				<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
	</div>
</div>

<div class="well"><?php echo JText::_('ADAG_GEO_INSTALL'); ?>
	<span style="background-color:transparent; font-size:12px; padding: 3px 0; margin-left:25px;">
		<a class="modal adagency-video-manager pull-right" rel="{handler: 'iframe', size: {x: 750, y: 435}}" href="index.php?option=com_adagency&controller=adagencyAbout&task=vimeo&id=15034955">
		<img src="<?php echo JURI::base(); ?>components/com_adagency/images/icon_video.gif" class="video_img" />
		<?php echo JText::_("AD_VIDEO"); ?>   
	</a></span>
	
</div>

 <table class="table table-striped table-bordered">
            <thead>
                <th>
                    <?php echo JText::_('ADAG_NAME');?>
                </th>
                <th>
                    <?php echo JText::_('ADAG_TYPE');?>
                </th>
                <th>
                    <?php echo JText::_('ADAG_LOCATION');?>
                </th>
                <th>
                    <?php echo JText::_('AD_STATUS');?>
                </th>
                <th>&nbsp;</th>

            </thead>
			<tbody>
            <tr>
                <td><?php echo JText::_('ADAG_CITIES_LIST');?></td>
                <td><?php echo JText::_('ADAG_FILE');?></td>
                <td>joomla_root/<input type="text" value="<?php echo $path2;?>" id="cityloc" name="cityloc" /></td>
                <td><i><?php if(file_exists(str_replace("administrator","",JPATH_BASE)."/".$path2)&&(strpos($path2,'.dat')>0)){ ?><font color="#006633"><?php echo JText::_('ADAG_FILEINST'); ?></font><?php } else {?><font color="#FF0000"><?php echo JText::_('ADAG_FILEMISS');?></font><?php }?></i></td>
                <td>
                	<div class="well">
	                    1. <a href="http://www.ijoomla.com/redirect/adserver/geo/city_download.htm" target="_blank"><?php echo JText::_('ADAG_DOWNMM');?></a><br />
	                    2. <?php echo JText::_('ADAG_GEOUNZ');?><br />
	                    3. <?php echo JText::_('ADAG_GEOUP'); ?> (<?php echo JText::_('ADAG_FILENAME');?>: GeoLiteCity.dat)<br />
                    </div>
                </td>
            </tr>

            <tr>
                <td><?php echo JText::_('ADAG_COUNTRIES_LIST');?></td>
                <td><?php echo JText::_('ADAG_FOLDER');?></td>
                <td>joomla_root/<input type="text" value="<?php echo $path1;?>" id="countryloc" name="countryloc" /></td>
                <td><i><?php if(file_exists(str_replace("administrator","",JPATH_BASE)."/".$path1."/country-AD.txt")){ ?><font color="#006633"><?php echo JText::_('ADAG_FILEINST'); ?></font><?php } else {?><font color="#FF0000"><?php echo JText::_('ADAG_FILEMISS');?></font><?php }?></i></td>
                <td rowspan="2">
                	<div class="well">
	                    1. <a href="http://www.ijoomla.com/redirect/adserver/geo/geo_download.htm" target="_blank"><?php echo JText::_('ADAG_DOWNMM2');?></a><br />
	                    2. <?php echo JText::_('ADAG_GEOUNZ');?><br />
	                    3. <?php echo JText::_('ADAG_EXTRA1');?><br />
	                    4. <?php echo JText::_('ADAG_GEOUP2'); ?>
                    </div>
                </td>
            </tr>

            <tr>
                <td><?php echo JText::_('ADAG_ZIP_LIST');?></td>
                <td><?php echo JText::_('ADAG_FOLDER');?></td>
                <td>joomla_root/<input type="text" value="<?php echo $path3;?>" id="codeloc" name="codeloc" /></td>
                <td><i><?php if(file_exists(str_replace("administrator","",JPATH_BASE)."/".$path3."/areacode.txt")){ ?><font color="#006633"><?php echo JText::_('ADAG_FILEINST'); ?></font><?php } else {?><font color="#FF0000"><?php echo JText::_('ADAG_FILEMISS');?></font><?php }?></i></td>
            </tr>
           	</tbody>
</table>
<div class="well"> 
	<strong><?php echo JText::_('ADAG_GEO1');?></strong>
    <p style="margin-left:10px;">
    <?php echo JText::_('ADAG_GEO2'); ?><br /><?php echo JText::_('ADAG_GEO3');?>
    </p>
</div>
<!--
    <fieldset class="adminForm">
       
        <table class="adminForm" border="0" width="100%" cellpadding="10" cellspacing="10" style="border-collapse:collapse;">
              
                    </table>
                </td>
            </tr>
            <tr >
                <td colspan="5" class="well well-minimized">
                    <?php echo JText::_('ADAG_GEO_INSTALL');?><span style="background-color:transparent; font-size:12px; padding: 3px 0; margin-left:25px;"><a class="modal adagency-video-manager" rel="{handler: 'iframe', size: {x: 750, y: 435}}" href="index.php?option=com_adagency&controller=adagencyAbout&task=vimeo&id=15034955">
		<img src="<?php echo JURI::base(); ?>components/com_adagency/images/icon_video.gif" class="video_img" />
		<?php echo JText::_("AD_VIDEO"); ?>   
	</a></span>
                </td>
            </tr>
			<tr style="height:20px;"> </tr>
            <tr style="background-color:#F2F2F2; color:#000000; font-weight:bold; font-size:12px;">
                <td>
                    <?php echo JText::_('ADAG_NAME');?>
                </td>
                <td>
                    <?php echo JText::_('ADAG_TYPE');?>
                </td>
                <td>
                    <?php echo JText::_('ADAG_LOCATION');?>
                </td>
                <td>
                    <?php echo JText::_('AD_STATUS');?>
                </td>
                <td>&nbsp;</td>

            </tr>

            <tr>
                <td valign="top"><?php echo JText::_('ADAG_CITIES_LIST');?></td>
                <td valign="top"><?php echo JText::_('ADAG_FILE');?></td>
                <td valign="top">joomla_root/<input type="text" size="35" value="<?php echo $path2;?>" id="cityloc" name="cityloc" /></td>
                <td valign="top"><i><?php if(file_exists(str_replace("administrator","",JPATH_BASE)."/".$path2)&&(strpos($path2,'.dat')>0)){ ?><font color="#006633"><?php echo JText::_('ADAG_FILEINST'); ?></font><?php } else {?><font color="#FF0000"><?php echo JText::_('ADAG_FILEMISS');?></font><?php }?></i></td>
                <td valign="top">
                    1. <a href="http://www.ijoomla.com/redirect/adserver/geo/city_download.htm" target="_blank"><?php echo JText::_('ADAG_DOWNMM');?></a><br />
                    2. <?php echo JText::_('ADAG_GEOUNZ');?><br />
                    3. <?php echo JText::_('ADAG_GEOUP'); ?> (<?php echo JText::_('ADAG_FILENAME');?>: GeoLiteCity.dat)<br />
                </td>
            </tr>

            <tr style="border-top:1px solid #999999;">
                <td valign="top"><?php echo JText::_('ADAG_COUNTRIES_LIST');?></td>
                <td valign="top"><?php echo JText::_('ADAG_FOLDER');?></td>
                <td valign="top">joomla_root/<input type="text" size="35" value="<?php echo $path1;?>" id="countryloc" name="countryloc" /></td>
                <td valign="top"><i><?php if(file_exists(str_replace("administrator","",JPATH_BASE)."/".$path1."/country-AD.txt")){ ?><font color="#006633"><?php echo JText::_('ADAG_FILEINST'); ?></font><?php } else {?><font color="#FF0000"><?php echo JText::_('ADAG_FILEMISS');?></font><?php }?></i></td>
                <td rowspan="2">
                    1. <a href="http://www.ijoomla.com/redirect/adserver/geo/geo_download.htm" target="_blank"><?php echo JText::_('ADAG_DOWNMM2');?></a><br />
                    2. <?php echo JText::_('ADAG_GEOUNZ');?><br />
                    3. <?php echo JText::_('ADAG_EXTRA1');?><br />
                    4. <?php echo JText::_('ADAG_GEOUP2'); ?>
                </td>
            </tr>

            <tr style="border-top:1px solid #999999;">
                <td valign="top"><?php echo JText::_('ADAG_ZIP_LIST');?></td>
                <td valign="top"><?php echo JText::_('ADAG_FOLDER');?></td>
                <td valign="top">joomla_root/<input type="text" size="35" value="<?php echo $path3;?>" id="codeloc" name="codeloc" /></td>
                <td valign="top"><i><?php if(file_exists(str_replace("administrator","",JPATH_BASE)."/".$path3."/areacode.txt")){ ?><font color="#006633"><?php echo JText::_('ADAG_FILEINST'); ?></font><?php } else {?><font color="#FF0000"><?php echo JText::_('ADAG_FILEMISS');?></font><?php }?></i></td>
            </tr>

            <tr>
                <td colspan="5">
                    <strong><?php echo JText::_('ADAG_GEO1');?></strong>
                    <p style="margin-left:10px;">
                        <?php echo JText::_('ADAG_GEO2'); ?><br />
                        <?php echo JText::_('ADAG_GEO3');?>
                    </p>
                </td>
            </tr>
        </table>
 -->
    <input type="hidden" value="com_adagency" name="option"/>
    <input type="hidden" value="" name="task"/>
    <input type="hidden" value="0" name="boxchecked"/>
    <input type="hidden" value="adagencyGeo" name="controller"/>
</form>
