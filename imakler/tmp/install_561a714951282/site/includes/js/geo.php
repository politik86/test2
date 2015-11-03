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
$document->addScript(JURI::root()."components/com_adagency/includes/js/geoCountry.js");

?>

<script type="text/javascript">

	// Initialise properties when adding new limitations
	function initAll(){
		ADAG('#geo_type1').prop('checked','checked');
		window.setTimeout(function(){removeTyper();},100);
	}

	<?php if(isset($configs->geoparams['allowgeo']) && ($configs->geoparams['allowgeo'] == '1')){ ?>
	ADAG(document).ready(function() {
		if(!existsLim()) {
			document.getElementById('geo_container').style.display = 'none';
		}
	});
	<?php } ?>

	// Define trim function
	String.prototype.trim = function() {
		a = this.replace(/^\s+/, '');
		return a.replace(/\s+$/, '');
	};

	// Function to check if there is at least one limitation
	function existsLim() {
		var nodelist = document.getElementById("opts");
		var tr = nodelist.tBodies[0].firstChild;
		var existsTr = false;
		while(tr){
			if(tr.nodeName == "TR") {
				existsTr = true;
			}
			tr = tr.nextSibling;
		}
		return existsTr;
	}

	// This function will remove all options [with removeChild]
	function removeAll(){
		var container = document.getElementById("tbdy");
		var nodelist = document.getElementById("opts");
		var tr = nodelist.tBodies[0].firstChild;
		var toRemove = new Array();
		i = 0;
		while(tr){
			if(tr.id) {
				toRemove[i] = tr.id;
				i++;
			}
			tr = tr.nextSibling;
		}

		for(var j=0;j<toRemove.length;j++){
			container.removeChild(document.getElementById(toRemove[j]));
		}
		ADAG('#limitation option').removeProp('disabled');
		document.getElementById('geo_container').style.display = "none";
	}

	function get_previoussibling(n)
	{
		//check if the previous sibling node is an element node
		x=n.previousSibling;
		while (x.nodeType!=1) {
  			x=x.previousSibling;
  		}
		return x;
	}

	function get_firstchild(n)
	{
		//check if the next sibling node is an element node
		x=n.firstChild;
		while (x.nodeType!=1)
  		{
  			x=x.nextSibling;
  		}
		return x;
	}

	// This function will delete the option by id [using removeChild]
	function deletelim(x){
		var container = document.getElementById("tbdy");
		var del = document.getElementById(""+x+"");
		ADAG('#limitation option:gt(0)').removeProp('disabled');	//[value='+del.className+']
		container.removeChild(del);
		if(existsLim()){
			var firstChild = get_firstchild(container);
			//document.getElementById(firstChild.id +'logical').style.display='none';
			updateColors();
		} else {
			document.getElementById("geo_container").style.display = "none";
		}
	}

	// Function to update the background color of the rows
	function updateColors(){
		var nodelist = document.getElementById("opts");
		var tr = nodelist.tBodies[0].firstChild;
		var i=1;
		var existsTr = 0;

		while(tr){
			if(tr.nodeName == "TR") {
				//if(i%2==1){
					//tr.style.backgroundColor = "#e6e6e6";
				//} else {
					//tr.style.backgroundColor = "#FFFFFF";
			//	}
				existsTr = 1;
			}

			i++;
			tr = tr.nextSibling;
		}
	}

	function selim(val, aux){
		<?php
			//echo "<pre>";var_dump($configs->geoparams);
			if(isset($configs->geoparams['c2'])&&($configs->geoparams['c2'] == '1')) { echo "var c2 = true;"; } else { echo "var c2 = 0;"; }
			if(isset($configs->geoparams['c3'])&&($configs->geoparams['c3'] == '1')) { echo "var c3 = true;"; } else { echo "var c3 = 0;"; }
		?>
		var val = val || document.getElementById("limitation").value;
		var aux = aux || null;

		if((val != 'region')&&(val != 'city')) {
			ADAG('#tbdy').html('');
		}
		if (val == 'continent') {
			if (!existsLim()) { var firstElement = true;} else { var firstElement = false;}
			var myrow = document.createElement('TR');
			var currentid = document.getElementById("numberoflims").value;
			myrow.id = 'limitation-'+currentid;
			myrow.setAttribute('class','continent');
			//myrow.style.border = '1px solid transparent';
			document.getElementById('tbdy').appendChild(myrow);
			var mycellthree = document.createElement('TD');
			myrow.appendChild(mycellthree);
			var mycellfour = document.createElement('TD');
			myrow.appendChild(mycellfour);
			var modulus = (currentid) % 2;
		//	if(modulus == 1){ myrow.style.backgroundColor = "#e6e6e6";}
			//else { myrow.style.backgroundColor = "#FFFFFF"; }
			if(currentid==1) { var display='none';} else { var display='';}
			mycellthree.innerHTML = '';
			var continents_set = 'AS,Asia|AF,Africa|EU,Europe|OC,Australia/Oceania|CA,Caribbean|SA,South America|NA,North America';
			var output = '<select id="limitation_select_'+currentid+'" name="limitation['+currentid+'][data][]" class="autocomplets">';
			var mySplitResult = continents_set.split("|");
			if(aux != null){
				temp_aux = aux.split('|');
			}

			for(var i=0;i<=mySplitResult.length-1;i++){
				var second = mySplitResult[i].split(",");
				var current_selected = '';
				if(aux != null) {
					for(var j=0;j<=temp_aux.length-1;j++){
						if(temp_aux[j] == second[0]) {
							current_selected = ' class="selected" ';
						}
					}
				}
				output += '<option value="'+second[0]+'"'+current_selected+'>'+second[1]+'</option>';
			}
			output += '</select>';
			mycellthree.innerHTML = output;

			//mycellthree.align = "left";
			//mycellthree.style.verticalAlign = "top";
			//mycellthree.style.padding = "15px 0 15px 5px";
			//-------------------
			/*mycellthree.style.maxWidth = "100%";
			ADAG("#geo_targeting_table, #geo_targeting_table tr, #opts, #opts tbody").css({display: "block", "max-width":'100%'});
			ADAG("#geo_container").css({display: "block", "max-width":'70%'});*/
			//-------------------
			mycellthree.width = "95%";
			
			mycellfour.innerHTML = '<input type="hidden" name="limitation['+currentid+'][type]" value="continent" />';	//<a href="#" onclick="javascript:deletelim(\''+myrow.id+'\');return false;" style="text-decoration: underline;" ><?php echo JText::_('ADAG_REMOVE');?></a>

			mycellfour.align = "left";
			mycellfour.width = "1%";
			mycellfour.style.padding = "15px 0px";
			mycellfour.style.verticalAlign = "top";
			document.getElementById("geo_container").style.display="";
			document.getElementById("numberoflims").value = parseInt(document.getElementById("numberoflims").value) +1;
			updateColors();
			window.setTimeout(function(){
				ADAG("#limitation_select_"+currentid).fcbkcomplete({ filter_selected: true, onselect: onSelectItem, onremove: onRemoveItem, firstselected: false });
				}, 1);
			if(aux == null){
				window.setTimeout(function(){
					var default_text = 'Start typing...';
					ADAG(".continent .holder:first .maininput:first").attr('size',default_text.length+15).val(default_text).click(function() {
						if(ADAG(this).val() == default_text){
							ADAG(this).val('');
						}
					}).blur(function(){
						if(ADAG(this).val() == ''){
							ADAG(this).attr('size',default_text.length+15).val(default_text);
						}
					});
				}, 1);
			}
		} else if(val == 'country') {
			if (!existsLim()) { var firstElement = true;} else { var firstElement = false;}
			var myrow = document.createElement('TR');
			var currentid = document.getElementById("numberoflims").value;
			myrow.id = 'limitation-'+currentid;
			//myrow.style.border = '1px solid transparent';
			myrow.setAttribute('class','country');
			document.getElementById('tbdy').appendChild(myrow);
			var mycellthree = document.createElement('TD');
			myrow.appendChild(mycellthree);
			var mycellfour = document.createElement('TD');
			myrow.appendChild(mycellfour);
			var modulus = (currentid) % 2;
			//if(modulus == 1){ myrow.style.backgroundColor = "#e6e6e6";}
			//else { myrow.style.backgroundColor = "#FFFFFF"; }
			if(currentid==1) { var display='none';} else { var display='';}
			var myString = getCountryCodes();
			if(aux != null){
				temp_aux = aux.split('|');
			}
			var outputs = "";
			var mySplitResult = myString.split("|");
			for(var i=0;i<=mySplitResult.length-1;i++){
				var second = mySplitResult[i].split(",");
				var current_selected = '';
				if(aux != null) {
					for(var j=0;j<=temp_aux.length-1;j++){
						if(temp_aux[j] == second[0]) {
							current_selected = ' class="selected" ';
						}
					}
				}
				outputs += '<option value="'+second[0]+'"'+current_selected+'>'+second[1]+'</option>';
			}
			var output = '<select id="limitation_select_'+currentid+'" name="limitation['+currentid+'][data][]" class="autocomplets country">'+outputs+'</select>';

			mycellthree.innerHTML = output;
			mycellthree.width = "95%";
			
			if(c2 || c3) { var firstOption = '<div class="span12"><input type="radio" name="coptions" id="firstOption" class="span1"/><label class="lbl span9" for="firstOption"><?php echo JText::_('ADAG_GEO_EVERYWH');?></label></div>'; } else { var firstOption = '';}
			if(c2) { var secondOption = '<div class="span12"><input type="radio" name="coptions" id="secondOption" class="span1"/><label class="lbl span9" for="secondOption"><?php echo JText::_('ADAG_GEO_BYSTPRO');?></label></div>'; } else { var secondOption = ''; }
			if(c3) { var thirdOption = '<div class="span12"><input type="radio" name="coptions" id="thirdOption" class="span1"/><label class="lbl span9" for="thirdOption"><?php echo JText::_('ADAG_GEO_BYCITY');?></label></div>'; } else { var thirdOption = ''; }
			
			
			ADAG('#limitation-'+currentid+' td:first').append('<div id="country_container">' + firstOption + secondOption + thirdOption + '</div>');
			ADAG('#country_container').hide();
			ADAG('#firstOption').prop('checked','true').change(function(){
				ADAG('#region_container').remove();
				ADAG('#tbdy tr .city').remove();
			});
			ADAG('#secondOption').change(function(){
				ADAG('<table id="region_container">').insertAfter(ADAG(this).next('label'));
				ADAG('#city_container').remove();
				selim('region');
			});
			ADAG('#thirdOption').change(function(){
				ADAG('<table id="city_container">').insertAfter(ADAG(this).next('label'));
				ADAG('#region_container').remove();
				selim('city');
			});

			mycellfour.innerHTML = '<input type="hidden" name="limitation['+currentid+'][type]" value="country" />';//<a href="#" onclick="javascript:deletelim(\''+myrow.id+'\');return false;" style="text-decoration: underline;" ><?php echo JText::_('ADAG_REMOVE');?></a>

			mycellfour.align = "left";
			mycellfour.width = "100";
			//mycellfour.style.padding = "15px 0px";
			//mycellfour.style.verticalAlign = "top";
			document.getElementById("geo_container").style.display="";
			document.getElementById("numberoflims").value = parseInt(document.getElementById("numberoflims").value) +1;
			updateColors();
			window.setTimeout(function(){
				ADAG("#limitation_select_"+currentid).fcbkcomplete({ filter_selected: true, onselect: onSelectItem, onremove: onRemoveItem, firstselected: false });
			}, 1);
			if(aux == null){
				window.setTimeout(function(){
					var default_text = '<?php echo addslashes(JText::_('ADAG_GEO_TYPE_COUN'));?>';
					ADAG(".country .holder:first .maininput:first").attr('size',default_text.length+15).val(default_text).click(function() {
						if(ADAG(this).val() == default_text){
							ADAG(this).val('');
						}
					}).blur(function(){
						if(ADAG(this).val() == ''){
							ADAG(this).attr('size',default_text.length+15).val(default_text);
						}
					});
				}, 1);
			}
		} else if (val=="region") {
			if (!existsLim()) { var firstElement = true;} else { var firstElement = false;}
			var myrow = document.createElement('TR');
			var currentid = document.getElementById("numberoflims").value;
			//myrow.style.border = '1px solid transparent';
			myrow.id = 'limitation-'+currentid;
			myrow.setAttribute('class','region');
			document.getElementById('region_container').appendChild(myrow);
			var mycellthree = document.createElement('TD');
			mycellthree.setAttribute('colspan','4');
			myrow.appendChild(mycellthree);
			//myrow.style.backgroundColor = "#e6e6e6";
			if(currentid==1) { var display='none';} else { var display='';}
			var outputs = "<table>";
			var temp_aux = null;
			if(aux != null){
				temp_aux = aux.split('|');
			} else {
				//temp_aux = ADAG('.country .holder:first .bit-box:first').attr('rel');
				temp_aux = new Array();
				temp_aux[0] = ADAG('.country .holder:first .bit-box:first').attr('rel');
			}
			var has_selected = false;
			var getRegions = "";
			if(temp_aux != null){
				var getRegs = getRegionByCountryCode(temp_aux[0]);
				for(var j=0;j<=getRegs.length-1;j++){
					var selected_region = '';
					var splitEm = getRegs[j].split(",");
					for(var k=1;k<=temp_aux.length-1;k++){
						if(temp_aux[k].toString() == splitEm[0].toString()) {
							selected_region = ' class="selected" ';
						}
					}
					getRegions += "<option value=\""+splitEm[0]+"\""+selected_region+">"+splitEm[1]+"</option>";
				}
			}

			outputs+="<tr><td style='display: none;'><?php echo JText::_('ADAG_REGS');?></td><td><select class=\"upd8regs\" id=\"updateRegions-"+currentid+"\" name=\"limitation["+currentid+"][data][]\">"+getRegions+"</select></td></tr></table><input type='hidden' name='limitation["+currentid+"][type]' value='region' />";//</td></tr>
			var output = '<p />'+outputs;

			mycellthree.innerHTML = output;
			mycellthree.align = "left";
			mycellthree.style.verticalAlign = "top";
			document.getElementById("geo_container").style.display="";
			document.getElementById("numberoflims").value = parseInt(document.getElementById("numberoflims").value) +1;
			updateColors();
			window.setTimeout(function(){
				ADAG("#updateRegions-"+currentid).fcbkcomplete({filter_selected: true, onselect: onSelectItem, firstselected: false});
				if(has_selected) {removeTyperRegion();}
			}, 1);
			if(aux == null){
				window.setTimeout(function(){
					var default_text = '<?php echo addslashes(JText::_('ADAG_GEO_TYPE_STAT'));?>';
					ADAG(".region .holder:first .maininput:first").attr('size',default_text.length+15).val(default_text).click(function() {
						if(ADAG(this).val() == default_text){
							ADAG(this).val('');
						}
					}).blur(function(){
						if(ADAG(this).val() == ''){
							ADAG(this).attr('size',default_text.length+15).val(default_text);
						}
					});
				}, 1);
			}
		} else if (val=='city') {
			if (!existsLim()) { var firstElement = true;} else { var firstElement = false;}
			var myrow = document.createElement('TR');
			var currentid = document.getElementById("numberoflims").value;
			//myrow.style.border = '1px solid transparent';
			myrow.id = 'limitation-'+currentid;
			myrow.setAttribute('class','city');
			document.getElementById('city_container').appendChild(myrow);
			var mycellthree = document.createElement('TD');
			myrow.appendChild(mycellthree);
			//myrow.style.backgroundColor = "#e6e6e6";
			if(currentid==1) { var display='none';} else { var display='';}
			var myString = getCountryCodes();
			var outputs = "<table width='100%'>";

			outputs+="<tr><td style='display: none;'><?php echo JText::_('ADAG_CITY');?></td><td><select class='upd8city' name=\"limitation["+currentid+"][data][]\" id=\"limitation-"+currentid+"city\" class='input20'></select></td></tr></table><input type='hidden' name='limitation["+currentid+"][type]' value='city' />";
			mycellthree.innerHTML = outputs;
			mycellthree.align = "left";
			mycellthree.setAttribute('colspan','3')
			mycellthree.style.verticalAlign = "top";
			document.getElementById("geo_container").style.display="";
			document.getElementById("numberoflims").value = parseInt(document.getElementById("numberoflims").value) + 1;
			updateColors();
			//alert(aux);
			if(aux != null) {
				//alert(aux);
				var temp = aux.split('|');
				for(var i=0;i<=temp.length-1;i++){
					ADAG('<option class="selected" selected="selected" value="'+temp[i]+'">'+temp[i]+'</option>').appendTo('.upd8city');
				}
			}
			window.setTimeout(function(){
				var the_country = ADAG(".country .holder:first .bit-box:first").attr('rel');
				//alert(the_country);
				if(typeof(the_country) != 'undefined') {
					ADAG("#limitation-"+currentid+" .upd8city").fcbkcomplete({ json_url: "<?php echo JUri::root().$configs->countryloc."/country-"; ?>" + the_country + ".txt", filter_selected: true, onselect: onSelectItem, onremove: onRemoveItem, firstselected: false}); //, maxitems: 1
				}
			}, 1);
			if(aux == null){
				window.setTimeout(function(){
					var default_text = '<?php echo addslashes(JText::_('ADAG_GEO_TYPE_CITY'));?>';
					ADAG(".city .holder:first .maininput:first").attr('size',default_text.length+15).val(default_text).click(function() {
						if(ADAG(this).val() == default_text){
							ADAG(this).val('');
						}
					}).blur(function(){
						if(ADAG(this).val() == ''){
							ADAG(this).attr('size',default_text.length+15).val(default_text);
						}
					});
				}, 1);
			}
		} else if (val == "latitude") {
			if (!existsLim()) { var firstElement = true;} else { var firstElement = false;}
			var myrow = document.createElement('TR');
			var currentid = document.getElementById("numberoflims").value;
			//myrow.style.border = '1px solid transparent';
			myrow.id = 'limitation-'+currentid;
			myrow.setAttribute('class','latitude');
			document.getElementById('tbdy').appendChild(myrow);
			var mycellthree = document.createElement('TD');
			myrow.appendChild(mycellthree);
			var mycellfour = document.createElement('TD');
			myrow.appendChild(mycellfour);
			var modulus = (currentid) % 2;
			//if(modulus == 1){ myrow.style.backgroundColor = "#e6e6e6";}
			//else { myrow.style.backgroundColor = "#FFFFFF"; }
			if(currentid==1) { var display='none';} else { var display='';}
			var outputs = "<table style=\"width: 50%;\" border=\"0\"><tr><td><input type=\"text\" class=\"width-50\" onkeyup=\"checkChannel();\" name=\"limitation["+currentid+"][data][a]\" size=\"20\" value=\"0.0000\"/></td><td>&nbsp;<span class=\"hidden-phone\">>&nbsp;</span>Latitude<span class=\"hidden-phone\">&nbsp;<</span>&nbsp;</td><td><input type=\"text\" class=\"width-50\" onkeyup=\"checkChannel();\" name=\"limitation["+currentid+"][data][b]\" size=\"20\" value=\"0.0000\"/></td></tr><tr><td><input class=\"width-50\" type=\"text\" onkeyup=\"checkChannel();\" name=\"limitation["+currentid+"][data][c]\" size=\"20\" value=\"0.0000\"/></td><td>&nbsp;<span class=\"hidden-phone\">>&nbsp;</span>Longitude<span class=\"hidden-phone\">&nbsp;<</span>&nbsp;</td><td><input type=\"text\" class=\"width-50\" onkeyup=\"checkChannel();\" name=\"limitation["+currentid+"][data][d]\" size=\"20\" value=\"0.0000\"/></td></tr></table>";
			var output = outputs;

			mycellthree.innerHTML = output;
			mycellthree.align = "left";
			//mycellthree.style.verticalAlign = "top";
			//mycellthree.style.padding = "15px 0 15px 5px";
			mycellfour.innerHTML = '<input type="hidden" name="limitation['+currentid+'][type]" value="latitude" />';	
			mycellfour.align = "left";
			mycellfour.width = "100";
			//mycellfour.style.padding = "15px 0px";
			//mycellfour.style.verticalAlign = "top";
			document.getElementById("geo_container").style.display="";
			document.getElementById("numberoflims").value = parseInt(document.getElementById("numberoflims").value) +1;
			updateColors();
		} else if (val == 'dma') {
			if (!existsLim()) { var firstElement = true;} else { var firstElement = false;}
			var myrow = document.createElement('TR');
			var currentid = document.getElementById("numberoflims").value;
			//myrow.style.border = '1px solid transparent';
			myrow.id = 'limitation-'+currentid;
			myrow.setAttribute('class','dma');
			document.getElementById('tbdy').appendChild(myrow);
			var mycellthree = document.createElement('TD');
			myrow.appendChild(mycellthree);
			var mycellfour = document.createElement('TD');
			myrow.appendChild(mycellfour);
			var modulus = (currentid) % 2;
			//if(modulus == 1){ myrow.style.backgroundColor = "#e6e6e6";}
			//else { myrow.style.backgroundColor = "#FFFFFF"; }
			if(currentid==1) { var display='none';} else { var display='';}
			var myString = getDMA();
			if(aux != null){
				temp_aux = aux.split('|');
			}
			var current_selected = '';
			var outputs = "<select name=\"limitation["+currentid+"][data][]\" class=\"autocomplets\">";
			var mySplitResult = myString.split("|");
			for(var i=0;i<=mySplitResult.length-1;i++){
				var second = mySplitResult[i].split("*");
				current_selected = '';
				if(aux != null) {
					for(var j=0;j<=temp_aux.length-1;j++){
						if(temp_aux[j] == second[1]) {
							current_selected = ' selected="selected" ';
						}
					}
				}
				outputs = outputs + "<option value=\""+second[1]+"\""+current_selected+">"+second[0]+"</option>";
			}
			outputs += "</select>";
			var output = outputs;

			mycellthree.innerHTML = output;
			//alert(newout);
			mycellthree.align = "left";
			//mycellthree.style.verticalAlign = "top";
			//mycellthree.style.padding = "15px 0 15px 5px";
			mycellthree.width = "95%";
			
			mycellfour.innerHTML = '<input type="hidden" name="limitation['+currentid+'][type]" value="dma" />';
			mycellfour.align = "left";
			mycellfour.width = "100";
			//mycellfour.style.padding = "15px 0px";
			//mycellfour.style.verticalAlign = "top";
			document.getElementById("geo_container").style.display="";
			document.getElementById("numberoflims").value = parseInt(document.getElementById("numberoflims").value) +1;
			updateColors();
			window.setTimeout(function(){
				ADAG("#limitation-"+currentid+" .autocomplets").fcbkcomplete({ filter_selected: true, onselect: onSelectItem, onremove: onRemoveItem, firstselected: false});
			},1);
			if(aux == null){
				window.setTimeout(function(){
					var default_text = '<?php echo addslashes(JText::_('ADAG_GEO_TYPE_DMA'));?>';
					ADAG(".dma .holder:first .maininput:first").attr('size',default_text.length+15).val(default_text).click(function() {
						if(ADAG(this).val() == default_text){
							ADAG(this).val('');
						}
					}).blur(function(){
						if(ADAG(this).val() == ''){
							ADAG(this).attr('size',default_text.length+15).val(default_text);
						}
					});
				}, 1);
			}
		} else if (val == 'usarea') {
			if (!existsLim()) { var firstElement = true;} else { var firstElement = false;}
			var myrow = document.createElement('TR');
			var currentid = document.getElementById("numberoflims").value;
			//myrow.style.border = '1px solid transparent';
			myrow.id = 'limitation-'+currentid;
			myrow.setAttribute('class','usarea');
			document.getElementById('tbdy').appendChild(myrow);
			var mycellthree = document.createElement('TD');
			myrow.appendChild(mycellthree);
			var mycellfour = document.createElement('TD');
			myrow.appendChild(mycellfour);
			var modulus = (currentid) % 2;
			//if(modulus == 1){ myrow.style.backgroundColor = "#e6e6e6";}
			//else { myrow.style.backgroundColor = "#FFFFFF"; }
			if(currentid==1) { var display='none';} else { var display='';}
			var myString = getCountryCodes();

			var outputs = "<table width='100%'>";
			outputs+="<tr><td style='display:none;'><?php echo JText::_('ADAG_AREACODE');?></td><td><select class='upd8usarea autocomplets' name=\"limitation["+currentid+"][data][]\" id=\"limitation-"+currentid+"usarea\" class='input20'></select></td></tr></table><input type='hidden' name='limitation["+currentid+"][type]' value='usarea' />";
			mycellthree.innerHTML = outputs;
			mycellthree.align = "left";
			mycellthree.setAttribute('colspan','3')
			//mycellthree.style.verticalAlign = "top";
			//mycellthree.style.padding = "15px 0 15px 5px";
			document.getElementById("geo_container").style.display="";
			document.getElementById("numberoflims").value = parseInt(document.getElementById("numberoflims").value) + 1;
			updateColors();
			//alert(aux);
			if(aux != null) {
				//alert(aux);
				var temp = aux.split('|');
				for(var i=0;i<=temp.length-1;i++){
					ADAG('<option class="selected" selected="selected" value="'+temp[i]+'">'+temp[i]+'</option>').appendTo('.upd8usarea');
				}
			}

			window.setTimeout(function(){
				ADAG("#limitation-"+currentid+" .upd8usarea").fcbkcomplete({ json_url: "<?php echo JUri::root().$configs->codeloc."/areacode.txt"; ?>", filter_selected: true, onselect: onSelectItem, onremove: onRemoveItem, firstselected: false});//, maxitems: 1
			}, 1);
			if(aux == null){
				window.setTimeout(function(){
					var default_text = '<?php echo addslashes(JText::_('ADAG_GEO_TYPE_AREA'));?>';
					ADAG(".usarea .holder:first .maininput:first").attr('size',default_text.length+15).val(default_text).click(function() {
						if(ADAG(this).val() == default_text){
							ADAG(this).val('');
						}
					}).blur(function(){
						if(ADAG(this).val() == ''){
							ADAG(this).attr('size',default_text.length+15).val(default_text);
						}
					});
				}, 1);
			}
		} else if (val == 'postalcode') {
			if (!existsLim()) { var firstElement = true;} else { var firstElement = false;}
			var myrow = document.createElement('TR');
			var currentid = document.getElementById("numberoflims").value;
			//myrow.style.border = '1px solid transparent';
			myrow.id = 'limitation-'+currentid;
			myrow.setAttribute('class','postalcode');
			document.getElementById('tbdy').appendChild(myrow);
			var mycellthree = document.createElement('TD');
			myrow.appendChild(mycellthree);
			var mycellfour = document.createElement('TD');
			myrow.appendChild(mycellfour);
			var modulus = (currentid) % 2;
			//if(modulus == 1){ myrow.style.backgroundColor = "#e6e6e6";}
			//else { myrow.style.backgroundColor = "#FFFFFF"; }
			if(currentid==1) { var display='none';} else { var display='';}
			var myString = getCountryCodes();

			var outputs = "<table width='100%'>";
			//<select class='upd8postalcode autocomplets' name=\"limitation["+currentid+"][data][]\" id=\"limitation-"+currentid+"postalcode\" class='input20'></select>
			outputs+="<tr><td style=\"display: none;\"><?php echo JText::_('ADAG_POSTALCODE');?></td><td><input size=\"40\" type=\"text\" class=\"input-zip\" onkeyup=\"checkChannel();\" name=\"limitation["+currentid+"][data][]\" class=\"input20\" value=\"\" placeholder=\"<?php echo JText::_('ADAG_ZIPNOTE');?>\" /></td></tr></table><input type='hidden' name='limitation["+currentid+"][type]' value='postalcode' id=\"postalcode_hidden\" />";
			if(aux == null) {
				window.setTimeout(function(){
					ADAG("#limitation-"+currentid+" .input20").focus(function() {
						ADAG(this).val('');
					}).blur(function(){
						if(ADAG(this).val() == '') {
							ADAG(this).val('<?php echo JText::_('Enter zip/postal code separated by comma');?>');
						}
					});
				},1);
			}
			mycellthree.innerHTML = outputs;
			mycellthree.align = "left";
			mycellthree.setAttribute('colspan','3')
			//mycellthree.style.verticalAlign = "top";
			//mycellthree.style.padding = "15px 0 15px 5px";
			document.getElementById("geo_container").style.display="";
			document.getElementById("numberoflims").value = parseInt(document.getElementById("numberoflims").value) + 1;
			updateColors();
		}

		/*ADAG('#limitation option:gt(0)').attr('disabled','disabled');//[value='+val+']
		ADAG('#limitation option:first').prop('selected','selected');*/
		ADAG("#limitation option[value="+val+"]").prop('selected','selected');
		initAll();
	}

	// Function to update regions on change for country
	function updateRegions(id,currentid,code) {
		var getR = getRegionByCountryCode(code);
		var output = "";
		for(var j=0;j<=getR.length-1;j++){
			var splitEm2 = getR[j].split(",");
			output += "<option value=\""+splitEm2[0]+"\">"+splitEm2[1]+"</option>";
		}
		document.getElementById(id).innerHTML = output;
		ADAG('#'+id).siblings().remove().end().fcbkcomplete({ filter_selected: true });
		initAll();
	}

	function onSelectItem(param){
		param.css('border','0px none');
		sanitizeLi(param);
		window.setTimeout(function(){
			if(param.hasClass('city') || param.hasClass('postalcode')) {
				if(!param.hasClass('city')) param.find('.maininput').hide();
			}
			else if(param.hasClass('region')){
				param.find('td:first').css('border','1px solid transparent');
			}
			else if(param.hasClass('country')){
				ADAG('#firstOption').prop('checked','true');
				if(param.find('.bit-box').length <= 1) {
					ADAG('#country_container').show();
				}
				else{
					ADAG('#country_container').hide();
					ADAG('#region_container').remove();
					ADAG('#city_container').remove();
				}
			}
			else{
				limitation = document.getElementById("limitation").value;
				if(limitation == "country"){
					ADAG('#firstOption').prop('checked','true');
					if(param.find('.bit-box').length <= 1) {
						ADAG('#country_container').show();
					}
					else{
						ADAG('#country_container').hide();
						ADAG('#region_container').remove();
						ADAG('#city_container').remove();
					}
				}
			}
		}, 1);
	}

	function onRemoveItem(param){
		sanitizeLi(param);
		if(param.hasClass('city')||param.hasClass('region')||param.hasClass('postalcode')) {//||param.hasClass('usarea')
			window.setTimeout(function(){
				param.find('.maininput').show();
			}, 1);
		} else if(param.hasClass('region')){
			param.find('.upd8regs').siblings().remove();
			param.find('.upd8regs').html('').fcbkcomplete();
		} else if(param.hasClass('country')){
			if(param.find('.holder:first .bit-box').length <= 2) {
				if(param.find('.holder:first .bit-box').length != 1){
					ADAG('#country_container').show();
				} else {
					ADAG('#country_container').hide();
					ADAG('#region_container').remove();
					ADAG('#city_container').remove();
				}
			} else {
				ADAG('#country_container').hide();
				ADAG('#region_container').remove();
				ADAG('#city_container').remove();
			}
		}
	}

	function initSanitizeLi(){
		ADAG('#opts tr').each(function(){
			if(!ADAG(this).hasClass('country')){
				ADAG(this).find('.holder .bit-input :not(:last)').remove();
			} else {
				ADAG(this).find('.holder:first').find('.bit-input :not(:last)').remove();
			}
		});
	}

	function sanitizeLi(param){
		if(!param.hasClass('country')){
			ADAG(this).find('.holder .bit-input :not(:last)').remove();
		} else {
			ADAG(this).find('.holder:first').find('.bit-input :not(:last)').remove();
		}
	}

	function removeTyper(){
	/*	ADAG('#opts tr').each(function(){
			if(ADAG(this).hasClass('city') && (ADAG(this).find('.selected').length != 0)) {
				ADAG(this).find('.maininput').hide();
			}
		});
		*/
	}

	function removeTyperRegion(){
	}

	function sanitizeAndSubmit(pressbutton){
		ADAG('.autocomplets .selected').each(function(){
			ADAG('<input type="hidden" name="'+ADAG(this).parents('.autocomplets').attr('name')+'" value="'+ADAG(this).val()+'" />').insertAfter(ADAG(this).parents('.autocomplets'));
		 });
		ADAG('.upd8regs .selected').each(function(){
			//alert(ADAG(this).val());
			ADAG('<input type="hidden" name="'+ADAG(this).parents('.upd8regs').attr('name')+'" value="'+ADAG(this).val()+'" />').insertAfter(ADAG(this).parents('.upd8regs'));
		 });
		ADAG('.autocomplets').remove();
		ADAG('.upd8regs').remove();
		submitform(pressbutton);
	}

	function fpopulate(){
		ADAG('#tbdy').text('');
		var value = ADAG('#populate_geo').val();
		if(value == 0){
			ADAG('#limitation option').removeProp('disabled');
			ADAG('#geo_container').hide();
		} else {
			ADAG.ajax({
					cache: false,
					type: "GET",
					url: "index.php?option=com_adagency&controller=adagencyAds&task=getChannel&bid=" + value + "&format=raw",
					dataType: "script"
			});
		}
	}

	function fpopulate2(){
		ADAG('#existing_container').text('');
		var value = ADAG('#limitation_existing').val();

		var url_address = "";

		if(typeof(document.adminForm.getElementById("administrator")) != "undefined"){
			url_address  = "<?php echo JUri::root()."index.php?option=com_adagency&controller=adagencyAds&task=getChannelInfo&cid="; ?>";
			url_address += value+"&format=raw";
		}
		else{
			url_address = "index.php?option=com_adagency&controller=adagencyAds&task=getChannelInfo&cid=" + value + "&format=raw";
		}

		if(value != 0){
			ADAG.ajax({
				cache: false,
				type: "GET",
				url: url_address,
				dataType: "html",
				success: function(data){
					ADAG('#existing_container').html("<table  cellpadding='2' cellspacing='2'><tr><td valign='top' align='left'><strong><?php echo JText::_('ADAG_CHAN_DETAILS');?>:</strong></td><td valign='top' align='left'>"+data+"</td></tr>");
					ADAG('#geo_type2').prop('checked','checked');
				}
			});
		}
	}
</script>
