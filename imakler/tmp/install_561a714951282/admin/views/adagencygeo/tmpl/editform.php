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


$configs = $this->configs;
include("components/com_adagency/includes/js/geo.php");
?>

<?php
	$document = JFactory::getDocument();
	$document->addStyleSheet(JURI::root()."administrator/components/com_adagency/css/joomla16.css");
	$document->addStyleSheet(JURI::root()."administrator/components/com_adagency/css/geo.css");
	$document->addStyleSheet(JURI::root()."administrator/components/com_adagency/css/fcb.css");
    $document->addStyleSheet(JURI::root()."components/com_adagency/includes/css/adag_tip.css");
	$document->addScript(JURI::root()."administrator/components/com_adagency/includes/js/jquery.fcbkcomplete.js");
	$current = $this->currentChannel;

	if (!function_exists('json_encode')) {
			require_once JPATH_BASE.DS.'components'.DS.'com_adagency'.DS.'helpers'.DS.'jsonwrapper.php';
	}

	if(isset($current)) {
		$counter = 1;
		$output = "ADAG(document).ready(function(){";
		if(isset($current->sets)){
			foreach($current->sets as $element) {
				$out_p = NULL;$temp_set = NULL;
				$element->data = json_decode($element->data);
				if(is_array($element->data)) {
					$temp_set = "'".implode('|', $element->data)."'";
				} else { $temp_set = 'null'; }
				$output .= " var aux = ".$temp_set."; ";
				$output .= " selim('".$element->type."', aux);";
				$output .= " ADAG('#limitation-".$counter."logical option[value=\"".$element->logical."\"]').each(function() {
						ADAG(this).attr('selected','selected');
					});";
				$output .= " ADAG('#limitation-".$counter."option option[value=\"".$element->option."\"]').each(function() {
						ADAG(this).attr('selected','selected');
					});";
				switch($element->type) {
					case 'city':
						if(isset($element->data[0]) && isset($element->data[1])) {
							$output .= " ADAG('#limitation-".$counter."city').val('".$element->data[1]."');";
						}
						break;
					case 'latitude':
						$out_p.= "
							if(index == 0) { ADAG(this).val('".$element->data->a."'); }
							else if(index == 1) { ADAG(this).val('".$element->data->b."'); }
							else if(index == 2) { ADAG(this).val('".$element->data->c."'); }
							else if(index == 3) { ADAG(this).val('".$element->data->d."'); }
						";
						$output .= " ADAG('#limitation-".$counter."option').siblings('table').find('input').each(function(index){".$out_p."}); ";
						break;
					case 'postalcode':
						$output .= " ADAG('#limitation-".$counter."option').next('p').find('input').val('".$element->data[0]."'); ";
						$output .= " window.setTimeout(function(){ ADAG('#limitation-".$counter." .maininput').hide(); },200); ";
						break;
					case 'usarea':
						//$output .= " ADAG('#limitation-".$counter."option').next('p').find('input').val('".$element->data[0]."'); ";
						//$output .= " window.setTimeout(function(){ ADAG('#limitation-".$counter." .maininput').hide(); },200); ";
						break;
					default:
						break;
				}

				$counter++;
			}
		}
		$output .= "}); ";
		$document->addScriptDeclaration($output);
	}
		$output2 = "

		function checkChannel(makeRed){
			makeRed = makeRed || false;

			var okContinent = true;
			ADAG('#opts').find('.continent').each(function() {
				var current = ADAG(this);
				if(!current.find('.holder .bit-box').length) {
					okContinent = false;
					if(makeRed) {
						current.css('border','2px solid red');
						alert('".JText::_('ADAG_JS_CONT')."');
						return false;
					}
				} else {
					current.css('border','0px none');
				}
			});

			if(okContinent){
				var okCountry = true;
				ADAG('#opts').find('.country').each(function() {
					var current2 = ADAG(this);
					if(!current2.find('.holder .bit-box').length) {
						okCountry = false;
						if(makeRed) {
							current2.css('border','2px solid red');
							alert('".JText::_('ADAG_JS_COUN')."');
							return false;
						}
					} else {
						current2.css('border','0px none');
					}
				});
			}

			if(okCountry){
				var okRegion = true;
				ADAG('#opts').find('.region').each(function() {
					var current3 = ADAG(this);
					if(!current3.find('.holder:first .bit-box').length) {
						okRegion = false;
						if(makeRed) {
							current3.css('border','2px solid red');
							alert('".JText::_('ADAG_JS_COUN')."');
							return false;
						}
					} else if(!current3.find('.holder:gt(0) .bit-box').length) {
						okRegion = false;
						if(makeRed) {
							current3.css('border','2px solid red');
							alert('".JText::_('ADAG_JS_REGI')."');
							return false;
						}
					} else {
						current3.css('border','0px none');
					}
				});
			}

			if(okRegion){
				var okCity = true;
				ADAG('#opts').find('.city').each(function() {
					var current4 = ADAG(this);
					if(!current4.find('.holder:eq(1) .bit-box').length) {
						okCity = false;
						if(makeRed) {
							current4.css('border','2px solid red');
							alert('".JText::_('ADAG_JS_CITY')."');
							return false;
						}
					} else if (!current4.find('.holder:first .bit-box').length){
						okCity = false;
						if(makeRed) {
							current4.css('border','2px solid red');
							alert('".JText::_('ADAG_JS_COUN')."');
							return false;
						}
					} else {
						current4.css('border','0px none');
					}
				});
			}

			if(okCity){
				var okLatitude = true;
				ADAG('#opts').find('.latitude').each(function() {
					var current5 = ADAG(this);
					current5.find(':input[size=20]').each(function(index) {
						if(ADAG(this).val() == '') {
							okLatitude = false;
						}
					});
					if(!okLatitude) {
						if(makeRed) {
							current5.css('border','2px solid red');
							alert('".JText::_('ADAG_JS_LATI')."');
							return false;
						}
					} else {
						current5.css('border','0px none');
					}
				});
			}

			if(okLatitude){
				var okDMA = true;
				ADAG('#opts').find('.dma').each(function() {
					var current6 = ADAG(this);
					if(!current6.find('.holder .bit-box').length) {
						okDMA = false;
						if(makeRed) {
							current6.css('border','2px solid red');
							alert('".JText::_('ADAG_JS_DMA')."');
							return false;
						}
					} else {
						current6.css('border','0px none');
					}
				});
			}

			if(okDMA){
				var okUsarea = true;
				ADAG('#opts').find('.usarea').each(function() {
					var current7 = ADAG(this);
					if(!current7.find('.holder .bit-box').length) {
						okUsarea = false;
						if(makeRed) {
							current7.css('border','2px solid red');
							alert('".JText::_('ADAG_JS_USAR')."');
							return false;
						}
					} else {
						current7.css('border','0px none');
					}
				});
			}

			if(okUsarea){
				var okPostal = true;
				ADAG('#opts').find('.postalcode').each(function() {
					var current8 = ADAG(this);
					if(current8.find(':input[size=40]').val() == '') {
						okPostal = false;
						if(makeRed) {
							current8.css('border','2px solid red');
							alert('".JText::_('ADAG_JS_POST')."');
							return false;
						}
					} else {
						current8.css('border','0px none');
					}
				});
			}

			return ((okContinent)&&(okCountry)&&(okRegion)&&(okCity)&&(okLatitude)&&(okDMA)&&(okUsarea)&&(okPostal));
		}

		function submitbutton(pressbutton) {
			var form = document.adminForm;
			if ((pressbutton=='savechannel')||(pressbutton=='applychannel')) {

				if(ADAG('#cname').val() =='') {
					alert('".JText::_('ADAG_JS_CHN_NAME')."');
					return false;
				}
				if(!checkChannel(true)) { return false;}
				sanitizeAndSubmit(pressbutton);
			} else {
				submitform(pressbutton);
			}

		}";
		$document->addScriptDeclaration($output2);
?>

<form class="form-horizontal" method="post" id="adminForm" name="adminForm" enctype="multipart/form-data">
	
    <link rel="stylesheet" href="<?php echo JURI::root(); ?>administrator/components/com_adagency/css/jquery-ui.css" />
	<script src="<?php echo JURI::root(); ?>administrator/components/com_adagency/includes/js/jquery-1.9.1.js"></script>
    <script src="<?php echo JURI::root(); ?>administrator/components/com_adagency/includes/js/jquery-ui.js"></script>
    
    <script>
        var availableTags = [];
        var availableRegions = [];
		var availableCity = [];

        function addNewRegions(regions){
			var i = availableRegions.length;
            for(var j = 0; j <= regions.length-1; j++){
                var second = regions[j].split(",");
                availableRegions[i] = second[0]+"-"+second[1];
                i++;
            }
        }
    
		function startAutocompleteCountry(){
			$(function() {
				var countries = getCountryCodes();
				var countries_list = countries.split("|");
				
				for(var i=0; i <= countries_list.length-1; i++){
					var second = countries_list[i].split(",");
					availableTags[i] = second[0]+"-"+second[1];
				}
				
				function split( val ) {
					return val.split( /,\s*/ );
				}
				
				function extractLast( term ) {
					return split( term ).pop();
				}
				
				$('[id^="country"]')
				// don't navigate away from the field on tab when selecting an item
				.bind( "keydown", function( event ) {
					if ( event.keyCode === $.ui.keyCode.TAB &&
					$( this ).data( "ui-autocomplete" ).menu.active ) {
						event.preventDefault();
					}
				})
				
				.autocomplete({
					minLength: 0,
					source: function( request, response ) {
						// delegate back to autocomplete, but extract the last term
						response( $.ui.autocomplete.filter(
						availableTags, extractLast( request.term ) ) );
					},
					
					focus: function() {
						// prevent value inserted on focus
						return false;
					},
					
					select: function( event, ui ) {
						var terms = split( this.value );
						// remove the current input
						terms.pop();
						// add the selected item
						
						var selected_item = ui.item.value.split("-");
						country_code = selected_item[0];
						country_name = selected_item[1];
						
						regions = getRegionByCountryCode(country_code);
						addNewRegions(regions);
						
						//---------------------------------------------------------------------
						cities = "";
						var xmlhttp;
						if (window.XMLHttpRequest){
							// code for IE7+, Firefox, Chrome, Opera, Safari
							xmlhttp=new XMLHttpRequest();
						}
						else{
							// code for IE6, IE5
							xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
						}
						
						xmlhttp.onreadystatechange = function(){
							if (xmlhttp.readyState==4 && xmlhttp.status==200){
								var cities_list = xmlhttp.responseText;
								cities_list = jQuery.parseJSON(cities_list);
								
								var k = availableCity.length;
								for(var i=0; i <= cities_list.length-1; i++){
									var city_name = cities_list[i].value;
									availableCity[k] = city_name;
									k++;
								}
							}
						}
						
						xmlhttp.open("GET", '<?php echo JUri::root().$configs->countryloc."/country-"; ?>' + country_code + '.txt', true);
						xmlhttp.send();
						//---------------------------------------------------------------------
						
						terms.push(country_name);
						// add placeholder to get the comma-and-space at the end
						terms.push("");
						this.value = terms.join( ", " );
						return false;
					}
				});
			});
		}
        
        function startAutocompleteRegion(){
			$(function() {
				function split( val ) {
					return val.split( /,\s*/ );
				}
				
				function extractLast( term ) {
					return split( term ).pop();
				}
				
				$('[id^="region"]')
				// don't navigate away from the field on tab when selecting an item
				.bind( "keydown", function( event ) {
					if ( event.keyCode === $.ui.keyCode.TAB &&
					$( this ).data( "ui-autocomplete" ).menu.active ) {
					event.preventDefault();
					}
				})
				
				.autocomplete({
					minLength: 0,
					source: function( request, response ) {
						// delegate back to autocomplete, but extract the last term
						response( $.ui.autocomplete.filter(
						availableRegions, extractLast( request.term ) ) );
					},
					
					focus: function() {
						// prevent value inserted on focus
						return false;
					},
					
					select: function( event, ui ) {
						var terms = split( this.value );
						// remove the current input
						terms.pop();
						// add the selected item
						
						var selected_item = ui.item.value.split("-");
						selected_item.shift();
						element_name = "";
						
						if(selected_item.length > 1){
							for(var k = 0; k <= selected_item.length-1; k++){
								element_name += selected_item[k];
								if(k < selected_item.length-1){
									element_name += "-";
								}
							}
						}
						else{
							element_name = selected_item;
						}
	
						terms.push(element_name);
						// add placeholder to get the comma-and-space at the end
						terms.push("");
						this.value = terms.join( ", " );
						return false;
					}
				});
				
				$('[id^="city"]')
				// don't navigate away from the field on tab when selecting an item
				.bind( "keydown", function( event ) {
					if ( event.keyCode === $.ui.keyCode.TAB &&
					$( this ).data( "ui-autocomplete" ).menu.active ) {
					event.preventDefault();
					}
				})
				
				.autocomplete({
					minLength: 0,
					source: function( request, response ) {
						// delegate back to autocomplete, but extract the last term
						response( $.ui.autocomplete.filter(
						availableCity, extractLast( request.term ) ) );
					},
					
					focus: function() {
						// prevent value inserted on focus
						return false;
					},
					
					select: function( event, ui ) {
						var terms = split( this.value );
						// remove the current input
						terms.pop();
						// add the selected item
						
						element_name = ui.item.value;
	
						terms.push(element_name);
						// add placeholder to get the comma-and-space at the end
						terms.push("");
						this.value = terms.join( ", " );
						return false;
					}
				});
			});
		}
		
		function setOnlyOneCountry(countr_id){
			value = document.getElementById(countr_id).value;
			var value_array = value.split(",");
			if(value_array.length > 1){
				document.getElementById(countr_id).value = value_array[0];
			}
		}
    </script>
    
    <div class="row-fluid">
            <div class="span12 pull-right">
            	<h2 class="pub-page-title">
					<?php if(!isset($current->id)) {echo JText::_('ADAG_GEONEWCH');} else {echo JText::_('ADAG_GEOEDCH');} ?>
				</h2>
            </div>
      </div>
		
			
		<div class="control-group">
			<label class="control-label"> <?php echo JText::_('ADAG_NAME'); ?> </label>
			<div class="controls">
				<input type="text" id="cname" name="cname" size="50" value="<?php if(isset($current->name)) {
					echo $current->name;
				}?>" />
				<span class="editlinktip hasTip" title="<?php echo JText::_('ADAG_NAME_TIP'); ?>" >
				<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
			</div>
		</div>
		
		<div class="control-group">
			<label class="control-label"> <?php echo JText::_('ADAG_PUBLIC'); ?> </label>
			<div class="controls">
				<fieldset class="radio btn-group" id="public">
					<?php
						$no_checked = "";
						$yes_cheched = "";
						
						if(!isset($current->public)|| $current->public =="Y" ) {
							$yes_cheched = 'checked="checked"';
						}
						else{
							$no_checked = 'checked="checked"';
						}
					?>
					<input type="hidden" name="public" value="N">
					<input type="checkbox" <?php echo $yes_cheched; ?> value="Y" class="ace-switch ace-switch-5" name="public">
					<span class="lbl"></span>
				</fieldset>
				<span class="editlinktip hasTip" title="<?php echo JText::_('ADAG_PUBLIC_TIP'); ?>" >
				<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
			</div>
		</div>
		
		<div class="control-group">
			<label class="control-label"> <?php echo JText::_('ADAG_DELIVERY_LIM'); ?> </label>
			<div class="controls">
				<select name="limitation" id="limitation">
					<option><?php echo JText::_('ADAG_SEL_TYPE');?></option>
					<option value="continent"><?php echo JText::_('ADAG_CONTINENT');?></option>
					<option value="country"><?php echo JText::_('ADAG_COUNTRY');?></option>
					<option value="region"><?php echo JText::_('ADAG_CREGION');?></option>
					<option value="city"><?php echo JText::_('ADAG_CCITY');?></option>
					<option value="latitude"><?php echo JText::_('ADAG_LATLONG');?></option>
					<option value="dma"><?php echo JText::_('ADAG_DMA');?></option>
					<option value="usarea"><?php echo JText::_('ADAG_USAREA');?></option>
					<option value="postalcode"><?php echo JText::_('ADAG_POSTAL_COD');?></option>
				</select>
				<input type="button" class="btn btn-primary" value="Add" onclick="javascript:selim();" />
				<span class="editlinktip hasTip" title="<?php echo JText::_('ADAG_GEO_ADD_LIM_TIP'); ?>" >
				<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
				
			</div>
		</div>
		
<div id="container">
	<div class="well"><?php echo JText::_('ADAG_DELIVERY_LIM2'); ?></div>
	<div class="span12"><?php echo JText::_('ADAG_DELIVERY_LIM3'); ?>:</div>
	
		<table id="opts" name="opts" style="margin-left:6px; width:90%; border-collapse:collapse;" cellpadding="2" cellspacing="2">
			<tbody id="tbdy">
			</tbody>
		</table>
		
		<div class="control-group">
			<a href="#" id="remall" onclick="removeAll();">
			<img id="removeall" align="absmiddle" src="components/com_adagency/images/delete-icon.gif" title="Remove limitations" alt="Remove limitations" />&nbsp;<?php echo JText::_('ADAG_REM_LIM');?></a>
		<input type="hidden" id="numberoflims" value="1" />
			</div>
		</div>
		
		
</div>

	<input type="hidden" value="com_adagency" name="option"/>
	<input type="hidden" value="" name="task"/>
	<input type="hidden" value="<?php if(isset($current->id)) {echo $current->id;} ?>" name="cid[]"/>
	<input type="hidden" value="0" name="boxchecked"/>
	<input type="hidden" value="adagencyGeo" name="controller"/>
    <input type="hidden" name="banner_id" value="<?php if(isset($current->banner_id)) { echo $current->banner_id; } else { echo '0'; } ?>" />
    <input type="hidden" name="advertiser_id" value="<?php if(isset($current->advertiser_id)) { echo $current->advertiser_id; } else { echo '0'; } ?>" />
</form>
