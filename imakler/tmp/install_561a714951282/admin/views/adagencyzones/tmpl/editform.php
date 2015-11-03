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
JHtml::_('behavior.tooltip');
$zone = $this->zone;
$modul = $this->modul;
////new

if(isset($modul->params) && $modul->params != ""){
	$parammss = json_decode($modul->params);
	$cachhe = @$parammss->cache;
}
else{
	$cachhe = 0;
}
////
$lists = $this->lists;
$positions = $this->positions;

$orders2 = $this->orders2;
$configs = $this->configs;
$nullDate = 0;
$mosConfig_live_site  = str_replace("/administrator","",JURI::base());
if (!isset($modul->position)) $modul->position = 'left';
JHTML::_('behavior.combobox');
$script_content = NULL;
$document =JFactory::getDocument();

if(isset($zone->adparams)&&($zone->adparams != NULL)&&($zone->adparams != '')) {
    $script_content = " ADAG(function(){ ";
    foreach($zone->adparams as $key=>$val){
        if(($key != 'width')&&($key != 'height')) {
            $script_content .= " window.setTimeout(function(){ADAG('#supported_banners .".$key."').click();},100); ";
        }
        if(($key == 'standard')||($key == 'affiliate')||($key == 'flash')) {
            $script_content .= " window.setTimeout(function(){ADAG('#adsize').css('display','');},222); ";
        }
    }
    $script_content .= "window.setTimeout(function(){
        if(ADAG('#cBanners').find('input:checked').length >0) {
            ADAG('input[name=bx]:eq(0)').prop('checked','true');
        } else if(ADAG('#cSpecial').find('input:checked').length >0) {
            ADAG('input[name=bx]:eq(2)').prop('checked','true');
        }
    },200);";
    $script_content .= " });";
}
$document->addScriptDeclaration($script_content);
$document->addStyleSheet(JURI::base()."components/com_adagency/css/zone.css");
$document->addStyleSheet(JURI::base()."components/com_adagency/css/joomla16.css");
$document->addStyleSheet(JURI::root()."components/com_adagency/includes/css/adag_tip.css");

include(JPATH_BASE."/components/com_adagency/includes/js/zone.php");
?>

<script type="text/javascript" language="javascript">
	function setInventory(value){
		if(value == 1){
			document.getElementById("bx3").style.display = "none";
			document.getElementById("bx3_label").style.display = "none";
			document.getElementById("cSpecial").style.display = "none";
			document.getElementById("textad").style.display = "none";
			document.getElementById("show_hide1").style.display = "none";
			document.getElementById("show_hide2").style.display = "none";
			document.getElementById("show_hide3").style.display = "none";
			document.getElementById("show_hide4").style.display = "none";
		}
		else{
			document.getElementById("bx3").style.display = "";
			document.getElementById("bx3_label").style.display = "";
			document.getElementById("cSpecial").style.display = "";
			document.getElementById("textad").style.display = "";
			document.getElementById("show_hide1").style.display = "";
			document.getElementById("show_hide2").style.display = "";
			document.getElementById("show_hide3").style.display = "";
			document.getElementById("show_hide4").style.display = "";
		}
	}
</script>

<form action="index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data" class="form-horizontal"> 
	 <div class="row-fluid">
            <div class="span12 pull-right">
            	<h2 class="pub-page-title">
					<?php if($zone->zoneid == 0) {echo JText::_('ADAG_NEWZONE');} else {echo JText::_('ADAG_EDITZONE');} ?>
				</h2>
				
            </div>
      </div>
      
      
      
<ul class="nav nav-tabs">
		<li class="active"><a href="#general" data-toggle="tab"><?php echo JText::_('VIEWCONFIGCATGENERAL');?></a></li>
        <li><a href="#details" data-toggle="tab"><?php echo JText::_('ADAG_ZONEADSSETS');?></a></li>
         <?php
			if($zone->zoneid != 0) { ?>
            <li><a href="#embed" data-toggle="tab"><?php echo JText::_('ADAG_EMBED');?></a></li>
         <?php } ?>
        <li><a href="#htmlcontent" data-toggle="tab"><?php echo JText::_('JAS_HTMLCONTENT');?></a></li>
        
</ul>
      
     <div class="tab-content">
    	<div class="tab-pane active" id="general"> 
    		 
           <?php if ($modul->id) { ?> 
             <div class="control-group">
                <label class="control-label">
                   <?php echo JText::_('ZONEID'); ?>
                </label>
                <div class="controls">
                   <?php echo $modul->id; ?>
                </div>
            </div>
    	 <?php } ?>
    	 
    	  <div class="control-group">
                <label class="control-label">
                    <?php echo JText::_('EDITZONETITLE');?>
                </label>
                <div class="controls">
                    <input class="text_area" type="text" name="title" size="35" value="<?php if (@$_REQUEST['title']!="") { echo @$_REQUEST['title']; } else { echo $modul->title; } ?>" />
					<span class="editlinktip hasTip" title="<?php echo JText::_('EDITZONETITLE_TIP'); ?>" >
					<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
                </div>
             </div>
    	 
    	 
    	   <div class="control-group">
                <label class="control-label">
                    <?php echo JText::_('EDITZONESUFFIX');?>
                </label>
                <div class="controls">
                  <?php
								$params_value = "";
								$params =  json_decode($modul->params);
								@$params_value = $params->moduleclass_sfx;
								
							?>
							<input class="text_area" type="text" name="suffix" size="35" value="<?php echo $params_value; ?>" />
					<span class="editlinktip hasTip" title="<?php echo JText::_('ADAG_ZONE_SUFFIX_TIP'); ?>" >
					<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
                </div>
             </div>
    	 	
    	 	 <div class="control-group">
                <label class="control-label">
                    <?php echo JText::_('ZONEPOSITION');?><font color="#ff0000">*</font>
                </label>
                <div class="controls">
                    <select class="positions" id="fakeposition-select" class="combobox" name="fakeposition-select" onChange="document.getElementById('position').value = this.value; document.getElementById('fakeposition').value = this.value;">
                         <?php
                              for($i=0,$n=count($positions); $i<$n; $i++){
                                  if(isset($modul->position) && $modul->position != $positions[$i]){ $sel=''; }
                                  else{ $sel='selected="selected"'; }
                                  echo '<option value="'.$positions[$i].'" '.$sel.'>'.$positions[$i].'</option>'; }
                          ?>
                    </select>
                    <input class="positions-input" type="text" value="<?php echo $modul->position; ?>" id="fakeposition" name="fakeposition"  />
                    
                    <input type="hidden"  id="position" name="position" value="<?php echo $modul->position;?>" />
                    <a rel="{handler: 'iframe', size: {x: 800, y: 450}}"  class="modal"  href="index.php?option=com_adagency&controller=adagencyPackages&task=preview&tmpl=component&no_html=1&cid[]=<?php echo '0'; ?>"><?php echo JText::_('VIEWPACKAGE_ZONES_PREVIEW');?></a>
					<span class="editlinktip hasTip" title="<?php echo JText::_('ZONEPOSITION_TIP'); ?>" >
					<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
                </div>
             </div>
    	 
    	  <div class="control-group">
                <label class="control-label">
                    <?php echo JText::_('EDITZONEMORDER');?>
                </label>
                <div class="controls">
                    <script language="javascript" type="text/javascript">
							<!--
							writeDynaList( 'class="inputbox" name="ordering" id="ordering" size="1"', orders, originalPos, originalPos, originalOrder );
							//-->
					</script>
                    <span class="editlinktip hasTip" title="<?php echo JText::_('EDITZONEMORDER_TIP'); ?>" >
					<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
                </div>
           </div>
    	 
    	  <div class="control-group">
                <label class="control-label">
                    <?php echo JText::_('ZONEPUB');?>
                </label>
                <div class="controls">
                	<fieldset class="radio btn-group" id="published">
					<?php
						
						$yes_cheched = "";
						
						if($lists['published'] ==1) {
							$yes_cheched = 'checked="checked"';
						}
					?>
					<input type="hidden" name="published" value="0">
					<input type="checkbox" <?php echo $yes_cheched; ?> value="1" class="ace-switch ace-switch-5" name="published">
					<span class="lbl"></span>
				</fieldset>
                    
                    <span class="editlinktip hasTip" title="<?php echo JText::_('ZONEPUB_TIP'); ?>" >
					<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
                </div>
           </div>
           
            <div class="control-group">
                <label class="control-label">
                    <?php echo JText::_('EDITZONESTITLE');?>
                </label>
                <div class="controls">
                	<fieldset class="radio btn-group" id="showtitle">
					<?php
						$yes_cheched = "";
						
						if($lists['showtitle'] ==1) {
							$yes_cheched = 'checked="checked"';
						}
					?>
					<input type="hidden" name="showtitle" value="0">
					<input type="checkbox" <?php echo $yes_cheched; ?> value="1" class="ace-switch ace-switch-5" name="showtitle">
					<span class="lbl"></span>
				</fieldset>            
                    <span class="editlinktip hasTip" title="<?php echo JText::_('EDITZONESTITLE_TIP'); ?>" >
					<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
                </div>
           </div>
    	 
    	 
            <div class="control-group">
                <label class="control-label">
                    <?php echo JText::_('ZONEPADDING');?>
                </label>
                <div class="controls">
                    <?php echo $lists['cellpadding'];?>
                    <span class="editlinktip hasTip" title="<?php echo JText::_('ADAG_ZONE_PADDING_TIP'); ?>" >
					<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
                </div>
           </div>
           <div class="control-group">
                <label class="control-label">
                   <?php echo JText::_('ZONE_SHOW_ADVERTISE_LINK'); ?>:
                </label>
                <div class="controls">
                   <select name="show_adv_link">
								<option value="0" <?php if ($zone->show_adv_link=='0') echo 'selected="selected"'; ?>><?php echo JText::_('ZONE_SHOW_ADV_LINK_DONT'); ?></option>
								<option value="1" <?php if ($zone->zoneid == 0 || $zone->show_adv_link=='1') echo 'selected="selected"'; ?>><?php echo JText::_('ZONE_SHOW_ADV_LINK_BOTTOM'); ?></option>
								<option value="2" <?php if ($zone->show_adv_link=='2') echo 'selected="selected"'; ?>><?php echo JText::_('ZONE_SHOW_ADV_LINK_TOP'); ?></option>
								<option value="3" <?php if ($zone->show_adv_link=='3') echo 'selected="selected"'; ?>><?php echo JText::_('ZONE_SHOW_ADV_LINK_BOTTOMANDTOP'); ?></option>
					</select>
                    <span class="editlinktip hasTip" title="<?php echo JText::_('ZONE_SHOW_ADVERTISE_LINK_TIP'); ?>" >
					<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
                </div>
           </div>
           
            <div class="control-group">
                <label class="control-label">
                   <?php echo JText::_('ZONE_LINK_SHOULD_TAKE_TO'); ?>:
                </label>
                <div class="controls">
                   <select onChange="show_hide_url(this.value)" name="link_taketo">
								<option value="0" <?php if ($zone->link_taketo=='0') echo 'selected="selected"'; ?>><?php echo JText::_('ZONE_LINK_TAKE_TO_PACKAGES'); ?></option>
								<option value="1" <?php if ($zone->link_taketo=='1') echo 'selected="selected"'; ?>><?php echo JText::_('ZONE_LINK_TAKE_TO_REGISTRATION'); ?></option>
								<option value="2" <?php if ($zone->link_taketo=='2') echo 'selected="selected"'; ?>><?php echo JText::_('ZONE_LINK_TAKE_TO_URL'); ?></option>
								<option value="3" <?php if ($zone->zoneid == 0 || $zone->link_taketo=='3') echo 'selected="selected"'; ?>><?php echo JText::_('ZONE_LINK_TAKE_TO_OVERVIEW'); ?></option>
							</select>
							<?php
									if ($zone->link_taketo != 2)
										$style = 'style="display:none"';
									else
										$style = '';
							?>
								<input id="taketo_url" name="taketo_url" <?php echo $style; ?> value="<?php if ($zone->taketo_url == '' || $zone->taketo_url == 'http://') echo 'http://' ; else echo $zone->taketo_url; ?>" size="50">
								
                    <span class="editlinktip hasTip" title="<?php echo JText::_('ZONE_SHOW_ADVERTISE_LINK_TIP'); ?>" >
					<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
                </div>
           </div>
           
    	    <div class="control-group">
                <label class="control-label">
                    <?php echo JText::_('ZONE_CACHING');?>
                </label>
                <div class="controls">             	
                	
                    <select name="cache">
						<option value="0" <?php if ($cachhe == '0') echo 'selected="selected"'; ?>><?php echo JText::_('ZONE_NO_CACHING'); ?></option>
						<option value="1" <?php if ($cachhe == '1') echo 'selected="selected"'; ?>><?php echo JText::_('ZONE_USE_GLOBAL'); ?></option>
					</select>
                    <span class="editlinktip hasTip" title="<?php echo JText::_('ZONE_CACHING_TIP'); ?>" >
					<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
                </div>
           </div>
    	 
   
      
    <div class="row-fluid">
    	<div class="span12">
	    	<img src="components/com_adagency/images/jomsocial_logo.gif"/>
			<?php echo "<div class='pull-right'>".JText::_("ADAG_JOMSOCIAL")."<p />"; ?>
			<?php echo "<a href='http://www.ijoomla.com/redirect/adagency/jomsocial.htm' target='_blank'><img src='components/com_adagency/images/icon_video.gif' />&nbsp;&nbsp;".JText::_("ADAG_JOM2")."</a></div>"; ?>	
 		</div>
    </div>
    
  <div class="well">
      <?php echo JText::_('ZONEPAGES');?>
  </div>
  

                   <script type="text/javascript">
											function allselections() {
												var e = document.getElementById('selections');
													e.disabled = true;
												var i = 0;
												var n = e.options.length;
												for (i = 0; i < n; i++) {
													e.options[i].disabled = true;
													e.options[i].selected = true;
												}
											}
											function disableselections() {
												var e = document.getElementById('selections');
													e.disabled = true;
												var i = 0;
												var n = e.options.length;
												for (i = 0; i < n; i++) {
													e.options[i].disabled = true;
													e.options[i].selected = false;
												}
											}
											function enableselections() {
												var e = document.getElementById('selections');
													e.disabled = false;
												var i = 0;
												var n = e.options.length;
												for (i = 0; i < n; i++) {
													e.options[i].disabled = false;
												}
											}
						</script>
  
    <div class="control-group">
                <label class="control-label">
                   <?php echo JText::_( 'EDITZONEMITEMS' ); ?>:
                </label>
                <div class="controls">
                   <?php if ($modul->client_id != 1) : ?>
						<?php if ($modul->pages == 'all') { ?>
							<input id="menus-all" type="radio" name="menus" value="all" onclick="allselections();" checked="checked" />
							<span class="lbl"></span>
							<label for="menus-all"><?php echo JText::_( 'All' ); ?></label>
							<input id="menus-none" type="radio" name="menus" value="none" onclick="disableselections();" />
							<span class="lbl"></span>
							<label for="menus-none"><?php echo JText::_( 'None' ); ?></label>
							<input id="menus-select" type="radio" name="menus" value="select" onclick="enableselections();" />
							<span class="lbl"></span>
							<label for="menus-select"><?php echo JText::_( 'Select From List' ); ?>
							<?php } elseif ($modul->pages == 'none') { ?>
							<input id="menus-all" type="radio" name="menus" value="all" onclick="allselections();" />
							<span class="lbl"></span>
							<label for="menus-all"><?php echo JText::_( 'All' ); ?></label>
							<input id="menus-none" type="radio" name="menus" value="none" onclick="disableselections();" checked="checked" />
							<span class="lbl"></span>
							<label for="menus-none"><?php echo JText::_( 'None' ); ?></label>
							<input id="menus-select" type="radio" name="menus" value="select" onclick="enableselections();" />
							<span class="lbl"></span>
							<label for="menus-select"><?php echo JText::_( 'Select From List' ); ?></label>
							<?php } else { ?>
							<input id="menus-all" type="radio" name="menus" value="all" onclick="allselections();" />
							<span class="lbl"></span>
							<label for="menus-all"><?php echo JText::_( 'All' ); ?></label>
							<input id="menus-none" type="radio" name="menus" value="none" onclick="disableselections();" />
							<span class="lbl"></span>
							<label for="menus-none"><?php echo JText::_( 'None' ); ?></label>
							<input id="menus-select" type="radio" name="menus" value="select" onclick="enableselections();" checked="checked" />
							<span class="lbl"></span>
							<label for="menus-select"><?php echo JText::_( 'Select From List' ); ?></label>
							<?php } ?>
				 <?php endif; ?>
                </div>
           </div>
    	   <div class="control-group">
                <label class="control-label">
                    <?php echo JText::_( 'Menu Selection' ); ?>:
                </label>
                <div class="controls">
                    <?php echo $lists['selections']; ?>
                </div>
           </div>
<?php if ($modul->client_id != 1) : ?>
<?php if ($modul->pages == 'all') { ?>
<script type="text/javascript">allselections();</script>
<?php } elseif ($modul->pages == 'none') { ?>
<?php } else { ?>
<?php } ?>
<?php endif; ?>	 
  	</div>

   <div class="tab-pane " id="details">    
    	    <div class="control-group">
                <label class="control-label">
                    <?php echo JText::_('ADAG_INVENTORY_ZONE');?>
                </label>
                <div class="controls">
                   <?php
								if(intval($zone->zoneid) != 0 && intval($zone->inventory_zone) == 0 && $this->existAdsForZome($zone->zoneid)){
									echo '<div class="alert alert-notice">
												'.JText::_("ADAG_ADS_ASSIGNED_NO_INVENTORY").'
										  </div>';
								}
								else {
							?>
				 <fieldset class="radio btn-group" id="inventory_zone">
					<?php
						
						$yes_cheched = "";	
						if($zone->inventory_zone == 1) {
							$yes_cheched = 'checked="checked"';
						}
						
					?>
					<input type="hidden" name="inventory_zone" value="0">
					<input type="checkbox" <?php echo $yes_cheched; ?> value="1" class="ace-switch ace-switch-5" name="inventory_zone">
					<span class="lbl"></span>
				</fieldset>	
                    <span class="editlinktip hasTip" title="<?php echo JText::_('ADAG_INVENTORY_ZONE_TIP'); ?>" >
					<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
					          </span>
                            <?php
								}
							?>
                </div>
           </div>
           
            <div class="control-group">
                <label class="control-label">
                    	<?php echo JText::_('ADAG_ZKEYWS'); ?>:
                </label>
                <div class="controls">
                	 <fieldset class="radio btn-group" id="showkeyws">
					<?php						
						$yes_cheched = "";
						
						if($lists['showkeyws'] == 1) {
							$yes_cheched = 'checked="checked"';
						}
						
					?>
					<input type="hidden" name="showkeyws" value="0">
					<input type="checkbox" <?php echo $yes_cheched; ?> value="1" class="ace-switch ace-switch-5" name="showkeyws">
					<span class="lbl"></span>
				</fieldset>	
                   
                    <span class="editlinktip hasTip" title="<?php echo JText::_('ADAG_ZKEYWS_TIP'); ?>" >
					<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
                </div>
           </div>
           
           <div class="control-group">
                <label class="control-label">
                    	<?php echo JText::_('ZONEADSZONE'); ?>:
                </label>
                <div class="controls">
                   <?php
                        	if($zone->inventory_zone == 1 && $zone->zoneid > 0){
								echo '<input type="hidden" name="banners" value="'.$zone->banners.'">';
								echo '<input type="hidden" name="banners_cols" value="'.$zone->banners_cols.'">';
							}
						?>
						<?php echo $lists['adsinzone'].' '.JText::_('ZONEADS_ROWS');?>
						<?php echo $lists['adsinzone_cols'].' '.JText::_('ZONEADS_COLS');?>
                    <span class="editlinktip hasTip" title="<?php echo JText::_('ZONEADSZONE_TIP'); ?>" >
					<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
                </div>
           </div>
           <?php
						$display = "";
						if($this->isLock) {
							$display = 'style="display:none;"';
			?>
           <div class="alert alert-notice"><?php echo JText::_('ADAG_CANT_MOD_TYPE_SIZE'); ?></div>
          <?php } ?>
          
           <div class="control-group">
                <label class="control-label">
                    	<?php echo JText::_('ADAG_SUPPORTED_TYPES'); ?>:
                </label>
                <div class="controls">
                 	<?php
                        
                        if($this->isLock){
							foreach($zone->adparams as $key=>$value){
								if($key != "width" && $key != "height"){
									echo '<input type="hidden" name="adparams['.$key.']" value="1" />';
								}
							}
						
                            $zone_params = $zone->adparams;
                            if(isset($zone_params["width"])){
                                unset($zone_params["width"]);
                            }
                            
                            if(isset($zone_params["height"])){
                                unset($zone_params["height"]);
                            }
                            
                            $zone_params = array_keys($zone_params);
                            $zone_params_string = implode(", ", $zone_params);
                            
                            $zone_params_string = str_replace("affiliate", "affiliate code", $zone_params_string);
                            $zone_params_string = str_replace("popup", "pop-up", $zone_params_string);
                            $zone_params_string = str_replace("textad", " text ad", $zone_params_string);
                            
                            echo '<div>'.ucwords($zone_params_string).'</div>';
                        }
                        
                    ?>
                     <div id="supported_banners" <?php echo $display; ?> >
                     		<div>
                            <input type="radio" name="bx" value="1" />
                            <span class="lbl"></span>
                            <label><?php echo JText::_('ADAG_BANNERS');?></label>
                            </div>
                            <div class="embed_container2" id="cBanners">
                            	<div>
	                                <input type="checkbox" class="standard" name="adparams[standard]" value="1" />
	                                <span class="lbl"></span>
	                                <label><?php echo JText::_('JAS_STANDART');?></label>
                                </div>
                                <div>
	                                <input type="checkbox" class="affiliate" name="adparams[affiliate]" value="1" />
	                                <span class="lbl"></span>
	                                <label><?php echo JText::_('JAS_BANNER_CODE');?></label>
                                </div>
                                <div>	 
	                                <input type="checkbox" class="flash" name="adparams[flash]" value="1" />
	                                <span class="lbl"></span>
	                                <label><?php echo JText::_('JAS_FLASH');?></label>
                                </div>
                            </div>
                            <div>
                            <input type="radio" name="bx" value="2" class="textad" />
                            <span class="lbl"></span>
                            <label><?php echo JText::_('JAS_TEXT_LINK');?></label>
                           </div>
                           <div>
                            <input type="radio" name="bx" value="3" id="bx3" />
                            <span class="lbl"></span>
                            <label id="bx3_label"><?php echo JText::_('ADAG_SPECIAL_BANNERS');?></label>
                           	</div>
                            <div class="embed_container2" id="cSpecial">
                            	<div>
                                <input type="checkbox" class="popup" name="adparams[popup]" value="1" />
                           		<span class="lbl"></span>
                                <label><?php echo JText::_('JAS_POPUP'); ?></label>
                                </div>
                                <div>
                                <input type="checkbox" class="transition" name="adparams[transition]" value="1" />
                                <span class="lbl"></span>
                                <label><?php echo JText::_('JAS_TRANSITION'); ?></label> <br />
                               	</div>
                               	<div>
                                <input type="checkbox" class="floating" name="adparams[floating]" value="1" />
                                <span class="lbl"></span>
                                <label><?php echo JText::_('JAS_FLOATING'); ?></label>
                                </div>
                            </div>
                        </div>
                    <span class="editlinktip hasTip" title="<?php echo JText::_('ZONEADSZONE_TIP'); ?>" >
					<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
                </div>
           </div>
           
             <div id="adsize" class="control-group" style="display:none;">
                <label class="control-label">
                    	<?php echo JText::_('ADAG_ADSIZE'); ?>:
                </label>
                <div class="controls">
                   <input type="radio" id="anysize" name="tdsr" <?php if(!isset($zone->adparams['width'])||($zone->adparams['width'] == '')) { echo ' checked="checked"'; } ?> /> <span class="lbl"></span> <?php echo JText::_('ADAG_ANYSIZE'); ?> <br />
				  
				   <input type="radio" id="fixedsize" name="tdsr" <?php if(isset($zone->adparams['width'])&&($zone->adparams['width'] != '')) { echo ' checked="checked"'; } ?> /><span class="lbl"></span><input type="text" value="<?php if(isset($zone->adparams['width'])) {echo $zone->adparams['width'];} ?>" size="3" name="adparams[width]" class="inputbox" id="adwidth"> x <input type="text" value="<?php if(isset($zone->adparams['height'])) {echo $zone->adparams['height'];} ?>" size="3" name="adparams[height]" class="inputbox" id="adheight">
				  
				   <?php echo JText::_('ADAG_ADSIZE_WHPX');?>
                </div>
           </div>
           
           	
				<div id="textad" class="control-group">
								<div class="well"><?php echo JText::_('ADAG_TAIP'); ?></div>
								<div class="control-group">
					                <label class="control-label">
					                    	<?php echo JText::_('ADAG_MAX_IMG_SIZE'); ?>:
					                </label>
					                <div class="controls">
					                  <input type="text" id="mxsize" name="textadparams[mxsize]" size="3" value="<?php if(isset($zone->textadparams['mxsize'])) { echo $zone->textadparams['mxsize']; } else { echo "50";} ?>" />&nbsp;px&nbsp;<select id="mxtype" name="textadparams[mxtype]"><option <?php if(isset($zone->textadparams['mxtype'])&&($zone->textadparams['mxtype'] == 'w')) { echo 'selected="selected"'; }  ?> value="w"><?php echo JText::_('ADAG_WIDTH'); ?></option><option <?php if(isset($zone->textadparams['mxtype'])&&($zone->textadparams['mxtype'] == 'h')) { echo 'selected="selected"'; }  ?> value="h"><?php echo JText::_('ADAG_HEIGHT'); ?></option></select>
					               	 <span class="editlinktip hasTip" title="<?php echo JText::_('ADAG_MAX_IMG_SIZE_TIP'); ?>" >
					                 <img src="components/com_adagency/images/tooltip.png" border="0"/></span>
					                </div>
					           </div>
								<div class="control-group">
					                <label class="control-label">
					                    	<?php echo JText::_('ADAG_IMG_ALIGNMENT'); ?>:
					                </label>
					                <div class="controls">
					                 <?php echo $lists['ia'];?>
					               	 <span class="editlinktip hasTip" title="<?php echo JText::_('ADAG_IMG_ALIGNMENT_TIP'); ?>" >
					                 <img src="components/com_adagency/images/tooltip.png" border="0"/></span>
					                </div>
					           </div>
					           
					           	<div class="control-group">
					                <label class="control-label">
					                    	<?php echo JText::_('ADAG_WRAP_IMG'); ?>:
					                </label>
					                <div class="controls">
					                 <fieldset class="radio btn-group" id="wrap_img">
											<?php
												echo $lists['wrap_img'];
																								
											?>
										</fieldset>	
					               	 <span class="editlinktip hasTip" title="<?php echo JText::_('ADAG_WRAP_IMG_TIP'); ?>" >
					                 <img src="components/com_adagency/images/tooltip.png" border="0"/></span>
					                </div>
					           </div>
								
							</div>
           
					<div id="show_hide1" class="control-group">
						<label class="control-label"><?php echo JText::_('ADAG_DEFAULT_AD');?>:</label>
						  <div class="controls">
						  	<?php echo $lists['adlist'];?>
							 <span class="editlinktip hasTip" title="<?php echo JText::_('ADAG_DEFAULT_AD_TIP'); ?>" >
							<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
						</div>
					</div>
					<div id="show_hide2" class="control-group">
						<label class="control-label"><?php echo JText::_('ZONE_ROTATE_BANNERS');?>:</label>
						  <div class="controls">
						  	 <fieldset class="radio btn-group" id="rotatebanners">
					<?php
						
						$yes_cheched = "";
						
						if($lists['rotatebanners'] == 1) {
							$yes_cheched = 'checked="checked"';
						}
						
					?>
					<input type="hidden" name="rotatebanners" value="0">
					<input type="checkbox" <?php echo $yes_cheched; ?> value="1" class="ace-switch ace-switch-5" name="rotatebanners">
					<span class="lbl"></span>
				</fieldset>	
						  	
							 <span class="editlinktip hasTip" title="<?php echo JText::_('ZONE_ROTATE_BANNERS_TIP'); ?>" >
							<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
						</div>
					</div>
					
					<div id="show_hide3" class="control-group">
						<label class="control-label"><?php echo JText::_('ZONE_ROTATING_TIME');?>:</label>
						  <div class="controls">
						  	<input class="text_area" id="rotate_time" type="text" name="rotating_time" size="8" value="<?php if ($zone->rotating_time!=0) echo $zone->rotating_time; else echo "10000";?>" /> ms
							 <span class="editlinktip hasTip" title="<?php echo JText::_('ZONE_ROTATING_TIME_TIP'); ?>" >
							<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
						</div>
					</div>
           
					<div id="show_hide4" class="control-group">
						<label class="control-label"><?php echo JText::_('ZONE_ROTATE_RANDOMIZE');?>:</label>
						  <div class="controls">
						  		  	 <fieldset class="radio btn-group" id="rotaterandomize">
					<?php
						
						$yes_cheched = "";
						
						if($lists['rotaterandomize'] == 1) {
							$yes_cheched = 'checked="checked"';
						}
					
					?>
					<input type="hidden" name="rotaterandomize" value="0">
					<input type="checkbox" <?php echo $yes_cheched; ?> value="1" class="ace-switch ace-switch-5" name="rotaterandomize">
					<span class="lbl"></span>
				</fieldset>	
							 <span class="editlinktip hasTip" title="<?php echo JText::_('ZONE_ROTATE_RANDOMIZE_TIP'); ?>" >
							<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
						</div>
					</div>
   </div>

           <?php
			if($zone->zoneid != 0) {
			?>
            <div class="tab-pane " id="embed">    
            	<div class="embed_container">
                    <div class="well">1. <?php echo JText::_('ADAG_EMBED_ANYWHERE');?></div>
                    <div><?php echo JText::_('EDITZONECODEMSG1');?></div>
                    <textarea class= "span9" id="zidcode" wrap="soft" rows="10" ><?php echo "<script type=\"text/javascript\" language=\"javascript\" src=\"".$mosConfig_live_site."index.php?option=com_adagency&controller=adagencyAds&task=remote_ad&tmpl=component&format=raw&zid=".$modul->id."\"></script>";?></textarea><br /><br />

                    <div class="well">2. <?php echo JText::_('ADAG_EMBED_USE'); ?></div>
                    1. <?php echo JText::_('ADAG_EMBED_INSTALL');?> <p />
                    2. <?php echo JText::_('ADAG_EMBED_OPEN');?> <p />
                    3. <?php echo JText::_('ADAG_ENTER_URL');?>  <input type="text" maxlength="120" size="60" readonly="readonly" value="<?php echo $mosConfig_live_site;?>" /> <p />
                    4. <?php echo JText::_('Enter this ID');?>  <input type="text" maxlength="5" size="5" readonly="readonly" value="<?php echo $modul->id;?>" /> <p />
                </div>
	
					<div class="control-group">
						<label class="control-label"><?php echo JText::_('ADAG_EM_IGN_STYLE');?>:</label>
						  <div class="controls">
						  	<fieldset class="radio btn-group" id="showtxtimg_yes">
								<?php
									$no_checked = "";
									$yes_cheched = "";
									
									if(isset($zone->ignorestyle)&&($zone->ignorestyle == '1')) {
										$yes_cheched = 'checked="checked"';
									}
									else{
										$no_checked = 'checked="checked"';
									}
								?>
								<input type="hidden" name="params[showtxtimg]" value="0">
								<input type="checkbox" <?php echo $yes_cheched; ?> value="1" class="ace-switch ace-switch-5" name="params[showtxtimg]">
								<span class="lbl"></span>
							</fieldset>
						</div>
					</div>
            
                </div>
           <?php
			}
			?>
   
<div class="tab-pane" id="htmlcontent">
   	<?php
		$zone_text_below = $zone->zone_text_below;
		$zone_content_location = $zone->zone_content_location;
		$zone_content_visibility = $zone->zone_content_visibility;
	?>
	<div class="well"><?php echo JText::_("JAS_SHOW_CONTENT_BELOW_ZONE"); ?></div>
	<div class="control-group">
		<?php
			$editor =JFactory::getEditor();
			echo $editor->display('zone_text_below', ''.stripslashes($zone_text_below) , '100%', '250px', '10', '45');
		?>
	</div>
					<div class="control-group">
						<label class="control-label"><?php echo JText::_('JAS_CONTENT_ZONE_VISIBILITY');?>:</label>
						  <div class="controls">
						  	<input type="radio" value="0" name="zone_content_visibility" <?php if($zone_content_visibility == 0){ echo 'checked="checked"'; } ?> /><span class="lbl"></span>&nbsp;<?php echo JText::_("JAS_VIZIBILITY_ADVER"); ?>
							<input type="radio" value="1" name="zone_content_visibility" <?php if($zone_content_visibility == 1){ echo 'checked="checked"'; } ?> /><span class="lbl"></span>&nbsp;<?php echo JText::_("JAS_VIZIBILITY_ANYONE"); ?>
						</div>
					</div>
					<div class="control-group">
						<label class="control-label"><?php echo JText::_('JAS_CONTENT_ZONE_LOCATION');?>:</label>
						  <div class="controls">
						  	<input type="radio" value="0" name="zone_content_location" <?php if($zone_content_location == 0){ echo 'checked="checked"'; } ?> /><span class="lbl"></span>&nbsp;<?php echo JText::_("JAS_BELOW_ADS"); ?>
						  	<input type="radio" value="1" name="zone_content_location" <?php if($zone_content_location == 1){ echo 'checked="checked"'; } ?> /><span class="lbl"></span>&nbsp;<?php echo JText::_("JAS_ABOVE_ADS"); ?>	
						</div>
					</div>
   </div> 
</div>
	<input type="hidden" name="zoneid" value="<?php echo $modul->id;?>" />
	<input type="hidden" name="option" value="com_adagency" />
	<input type="hidden" name="task" value="edit" />
	<input type="hidden" name="controller" value="adagencyZones" />
</form>