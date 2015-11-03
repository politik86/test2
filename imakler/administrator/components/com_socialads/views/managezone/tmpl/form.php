<?php
defined( '_JEXEC' ) or die( ';)' );
require(JPATH_SITE.DS."administrator".DS."components".DS."com_socialads".DS."config".DS."config.php");

jimport( 'joomla.form.formvalidator' );
JHTML::_('behavior.tooltip');
jimport('joomla.html.pane');
JToolBarHelper::back( JText::_('CC_HOME') , 'index.php?option=com_socialads');
JToolBarHelper::save($task = 'save', $alt = JText::_('SAVE'));
JToolBarHelper::cancel( $task = 'cancel', $alt = JText::_('CLOSE') );
$cid[0] = -999;
$adcount = 0;
$document = JFactory::getDocument();


$document->addScript(JURI::base().'components/com_socialads/js/adminsocialads.js');
$document->addStyleSheet(JURI::base().'components/com_socialads/css/socialads.css');
$input=JFactory::getApplication()->input;
if($input->get( 'cid','','ARRAY' ))
	$cid	= $input->get( 'cid','','ARRAY' );
if($input->get( 'adcnt','' ))
{
	$adcount = $input->get( 'adcnt','' );
}

if($this->zones[0]->layout)
			{	JRequest::setVar( 'layout1',$this->zones[0]->layout );
		}
if($cid[0] == -999)
{
$this->zones[0]=array();
}

if(!empty($this->zones[0]->id)){
	$zoneid = $this->zones[0]->id;
}
else
$zoneid = 0;


?>
<style type="text/css">
.invalid { color:red;
 }

.zone_label{width:40%;}

</style>

<?php

if(JVERSION >= '1.6.0')
$js = 'Joomla.submitbutton = function(action){';
else
$js = 'function submitbutton( action ) {';

	$js .='var form = document.adminForm;
	clearinvalid();
	if( action == "cancel")
	{

		submitform( action );
		return;
	}
	var add_type_label=form.add_type
	var addtype=add_type_label.options[ add_type_label.selectedIndex].value;

	var zone_type_label=form.zone_type
	var zonetype=parseInt(zone_type_label.options[zone_type_label.selectedIndex].value);
	var flag=0
	// do field validation
	if (form.zone_name.value == ""  )
	{
		document.getElementById("validate_name").innerHTML="*'.JText::_('CC_YOU_MUST_PROVIDE_A_ZONE_NAME').'";
		flag=1;

	}
	if (parseInt(zonetype)==0)
	{
		 document.getElementById("validate_zone_type").innerHTML="*'.JText::_('CC_YOU_MUST_SELECT_ZONE_ORIENTATION').'";
		 flag=1;
	}


	if (addtype=="text")
	{

		document.getElementById("validate_img_width").innerHTML="";
		document.getElementById("validate_img_height").innerHTML="";

		if ((trim(form.max_title.value) == "") )
		{
			 document.getElementById("validate_max_title").innerHTML="*'.JText::_('CC_YOU_MUST_PROVIDE_A_MAX_TITLE_CHAR').'";
			 flag=1;
		}
		else
	    {
	    	if(isNaN(trim(form.max_title.value))|| (parseInt(form.max_title.value)==0))
	    	{
	    	 document.getElementById("validate_max_title").innerHTML="*'.JText::_('VALIDATE_NON_ZERO_NUMERIC').'";
			 flag=1;
	    	}
    	}

		if (trim(form.max_des.value) == "")
		{
			 document.getElementById("validate_max_des").innerHTML="*'.JText::_('CC_YOU_MUST_PROVIDE_A_MAX_DESC_CHAR').'";
			 flag=1;
		}
		else
		{
			if(isNaN(trim(form.max_des.value))|| (parseInt(form.max_des.value)==0))
			{
			 document.getElementById("validate_max_des").innerHTML="*'.JText::_('VALIDATE_NON_ZERO_NUMERIC').'";
			 flag=1;
			}

		}
	}

	if (addtype=="img")
	{

		 document.getElementById("validate_max_title").innerHTML="";
		 document.getElementById("validate_max_des").innerHTML="";

		if (trim(form.img_width.value) == "")
		{
			 document.getElementById("validate_img_width").innerHTML="*'.JText::_('CC_YOU_PROVIDE_A_IMG_WIDTH').'";
			 flag=1;
		}
		else
		{
			if(isNaN(trim(form.img_width.value))|| (parseInt(form.img_width.value)==0))
			{
			 document.getElementById("validate_img_width").innerHTML="* '.JText::_('VALIDATE_NON_ZERO_NUMERIC').'";
			 flag=1;
			}
		}
		if (trim(form.img_height.value) == "")
		{
			 document.getElementById("validate_img_height").innerHTML="* '.JText::_('CC_YOU_PROVIDE_A_IMG_HEIGHT').'";
			 flag=1;
		}
		else
	    {
	    	if(isNaN(trim(form.img_height.value))|| (parseInt(form.img_height.value)==0))
	    	{
	    	 document.getElementById("validate_img_height").innerHTML="* '.JText::_('VALIDATE_NON_ZERO_NUMERIC').'";
			 flag=1;
	    	}
    	}
	}

	if (addtype=="text_img")
	{

		document.getElementById("validate_img_width").innerHTML="";
		document.getElementById("validate_img_height").innerHTML="";
		document.getElementById("validate_max_title").innerHTML="";
		document.getElementById("validate_max_des").innerHTML="";
		if (trim(form.max_title.value) == "")
		{
			document.getElementById("validate_max_title").innerHTML="* '.JText::_('CC_YOU_MUST_PROVIDE_A_MAX_TITLE_CHAR').'";
			flag=1;
		}
		else
	    {

	    	if(isNaN(trim(form.max_title.value))|| (parseInt(form.max_title.value)==0))
	    	{
	    	 	document.getElementById("validate_max_title").innerHTML="* '.JText::_('VALIDATE_NON_ZERO_NUMERIC').'";
					flag=1;
	    	}
    	}
		if (trim(form.max_des.value) == "")
		{
			document.getElementById("validate_max_des").innerHTML="* '.JText::_('CC_YOU_MUST_PROVIDE_A_MAX_DESC_CHAR').'";
			flag=1;
		}
		else
	    {

	    	if(isNaN(trim(form.max_des.value))|| (parseInt(form.max_des.value)==0))
	    	{
		    	 document.getElementById("validate_max_des").innerHTML="* '.JText::_('VALIDATE_NON_ZERO_NUMERIC').'";
					 flag=1;
	    	}
    	}
		if (trim(form.img_width.value) == "")
		{

		 	document.getElementById("validate_img_width").innerHTML="* '.JText::_('CC_YOU_PROVIDE_A_IMG_WIDTH').'";
		 	flag=1;
		}
		else
	    {

	    	if(isNaN(trim(form.img_width.value))|| (parseInt(form.img_width.value)==0))
	    	{

	    	 document.getElementById("validate_img_width").innerHTML="* '.JText::_('VALIDATE_NON_ZERO_NUMERIC').'";
				 flag=1;
	    	}
    	}
		if (trim(form.img_height.value) == "")
		{
			document.getElementById("validate_img_height").innerHTML="* '.JText::_('CC_YOU_PROVIDE_A_IMG_HEIGHT').'";
		 	flag=1;
		}
		else
	    {

	    	if(isNaN(trim(form.img_height.value))|| (parseInt(form.img_height.value)==0))
	    	{

	    	 document.getElementById("validate_img_height").innerHTML="* '.JText::_('VALIDATE_NON_ZERO_NUMERIC').'";
				 flag=1;
	    	}
    	}
	}

	///////////Code to Populate Layout
	var txtSelectedValuesObj = document.getElementById("layout");
	txtSelectedValuesObj.value="";
	///////////Code to Populate Layout
	txtSelectedValuesObj = populatelayout();

	if (txtSelectedValuesObj==0)
	{

		document.getElementById("validate_layout").innerHTML="* '.JText::_('CC_YOU_MUST_PROVIDE_A_LAYOUT').'";
		flag=1;

	}

	var allow_pricing	= '.$socialads_config['zone_pricing'].';
	if(parseInt(allow_pricing))
	{

			if(jQuery("#per_click").is(":visible")	)
				if (trim(form.per_click.value) == "")
				{

					document.getElementById("validate_per_click").innerHTML="* '.JText::_('CC_YOU_MUST_PROVIDE_A_PRICE_PER_CLICK').'";
					flag=1;

				}
				else
				{
					if(isNaN(trim(form.per_click.value))|| (parseFloat(form.per_click.value)==0.00))
					{

					 document.getElementById("validate_per_click").innerHTML="* '.JText::_('VALIDATE_NON_ZERO_NUMERIC').'";
					 flag=1;
					}
				}

			if(jQuery("#per_imp").is(":visible"))
				if (trim(form.per_imp.value) == "")
				{

					document.getElementById("validate_per_imp").innerHTML="* '.JText::_('CC_YOU_MUST_PROVIDE_A_PRICE_PER_IMPR').'";
					flag=1;

				}
				else
				{
					if(isNaN(trim(form.per_imp.value))|| (parseFloat(form.per_imp.value)==0.00))
					{

					 document.getElementById("validate_per_imp").innerHTML="* '.JText::_('VALIDATE_NON_ZERO_NUMERIC').'";
					 flag=1;
					}
				}

			if( jQuery("#per_day").is(":visible"))
				if (trim(form.per_day.value) == "")
				{

					document.getElementById("validate_per_day").innerHTML="* '.JText::_('CC_YOU_MUST_PROVIDE_A_PRICE_PER_DAY').'";
					flag=1;
				}
				else
				{
					if(isNaN(trim(form.per_day.value))|| (parseFloat(form.per_day.value)==0.00))
					{

					 document.getElementById("validate_per_day").innerHTML="* '.JText::_('VALIDATE_NON_ZERO_NUMERIC').'";
					 flag=1;
					}
				}

	}
	if(!flag)
	{

		if('.$adcount.'!= 0)
		{
			var con=confirm("'.JText::_('MSG_ON_EDIT_ZONE').'");
			if(con==true)
			submitform( action );
			return;
		}
		submitform( action );
	}

}

function populatelayout()
{
	var txtSelectedValuesObj = document.getElementById("layout");
	txtSelectedValuesObj.value=="";
	var count = 0;
	count = jQuery("[name=layout_select]:checkbox:checked").length;
	var allVals = [];
     jQuery("[name=layout_select]:checkbox:checked").each(function() {
       allVals.push(jQuery(this).val());

     });
     jQuery("#layout").val(allVals);
	return count;
}
function clearinvalid()
{
	document.getElementById("validate_name").innerHTML="";
	document.getElementById("validate_zone_type").innerHTML="";
	document.getElementById("validate_add_type").innerHTML="";
	document.getElementById("validate_max_title").innerHTML="";
	document.getElementById("validate_max_des").innerHTML="";
	document.getElementById("validate_img_width").innerHTML="";
	document.getElementById("validate_img_height").innerHTML="";
	document.getElementById("validate_layout").innerHTML="";

		var allow_pricing	= '.$socialads_config["zone_pricing"].';

		if(parseInt(allow_pricing))
		{
			document.getElementById("validate_per_click").innerHTML="";
			document.getElementById("validate_per_imp").innerHTML="";
			document.getElementById("validate_per_day").innerHTML="";
		}

}

function display(field)
{
	clearinvalid();
//	callajax(field.value);

	if(field.value == "text") {
		document.getElementById("img_width_row").style.display="none";
		document.getElementById("img_height_row").style.display="none";
		//document.getElementById("img_width").value="";
	 	//document.getElementById("img_height").value="";
		document.getElementById("max_title_char_row").style.display="";
		document.getElementById("max_desc_char_row").style.display="";
		document.getElementById("layout_row").style.display="";


	///////////Code to Populate Layout

		var txtSelectedValuesObj = document.getElementById("layout");
		txtSelectedValuesObj.value="";

	//////////////////////////
	}
	else if(field.value == "img") {
  		document.getElementById("img_width_row").style.display="";
		document.getElementById("img_height_row").style.display="";
		//document.getElementById("max_title").value="";
	 	//document.getElementById("max_des").value="";
		document.getElementById("max_title_char_row").style.display="none";
		document.getElementById("max_desc_char_row").style.display="none";
		//document.getElementById("layout_row").style.display="none";
		var txtSelectedValuesObj = document.getElementById("layout");
		txtSelectedValuesObj.value="";
	}
	else if(field.value == "text_img") {
	  		document.getElementById("max_title_char_row").style.display="";
			document.getElementById("max_desc_char_row").style.display="";
			document.getElementById("img_width_row").style.display="";
			document.getElementById("img_height_row").style.display="";
			document.getElementById("img_width").style.display="";
		 	document.getElementById("img_height").style.display="";
			var txtSelectedValuesObj = document.getElementById("layout");
			document.getElementById("layout_row").style.display="";
			txtSelectedValuesObj.value="";
	}


}
	window.addEvent("domready", function(){
		autoFill();
		jQuery("#add_type").change(autoFill);
	if('.$zoneid.'){
		jQuery("#wtab2").hide();
		codechanger("widget");
	}
	jQuery("#widget :input").bind("keyup change click", function() {
		if(jQuery(this).attr("id") != "wid_code")
			codechanger("widget");
	});
	jQuery("#field_target :input").bind("keyup change", function() {
		if(jQuery(this).attr("id") != "wid_code")
			codechanger("target");
	});
		function autoFill(){

			var selectedadd = document.getElementById("add_type").value;
			var sellay = "";
			if (document.getElementById("zlayout").value)
				selectedadd += "&zonelayout=" + document.getElementById("zlayout").value;
			var url = "?option=com_socialads&task=getList&controller=managezone&addtype="+selectedadd;';

			$js .='jQuery.ajax({
					type: "get",
					url:url,
					success: function(response)
					{
						var d = document.getElementById("layout_ad_ajax");
						var olddiv = document.getElementById("layout_ad1");
						d.removeChild(olddiv);
						document.getElementById("layout_ad_ajax").innerHTML="<div id=layout_ad1></div>"+response;
					}
				});
		}
		jQuery( ".yes_no_toggle label" ).on( "click", function() {
			var radiovalue = yesnoToggle(this);
		});
	});
	function yesnoToggle(elem){
		var radio_id	=	jQuery(elem).attr("for");

		jQuery("#"+radio_id).attr("checked", "checked");

		/*for jQuery 1.9 and higher*/
		jQuery("#"+radio_id).prop("checked", true)

		var radio_btn = jQuery("#"+radio_id);
		var radio_value=radio_btn.val();

		var radio_name	=	jQuery("#"+radio_id).attr("name");
		var target_div	=	radio_name	+"_div";
		jQuery(elem).parent().find("label").removeClass("btn-success").removeClass("btn-danger");
		if(radio_value	== 1)
		{
			jQuery(elem).addClass("btn-success");
		}
		if(radio_value	== 0)
		{
			jQuery(elem).addClass("btn-danger");
		}
		return radio_value;
	}
	function trim(str) {
		return str.replace(/^\s+|\s+$/g,"");
	}
function getvalue(a,def) {
    if (a == undefined) {
        return null;
    }
    if (a.value) {
        return a.value;
    } else if (a.length) {
        if (a.options) {
			if(a.selectedIndex != -1)
				return a.options[a.selectedIndex].value;
        } else {
            for (var b = 0; b < a.length; b++) {
                if (a[b].checked) {
                    return a[b].value;
                }
            }
        }
    }
    return def;
}
	/* this function allow only numberic and specified char (at 0th position)
	// ascii (code 43 for +) (48 for 0 ) (57 for 9)  (45 for  - (negative sign))
				(code 46 for dot/full stop .)
		@param el :: html element
		@param allowed_ascii::ascii code that shold allow

	*/
	function checkforalpha(el,allowed_ascii)
	{
		allowed_ascii= (typeof allowed_ascii === "undefined") ? "" : allowed_ascii;
		var i =0 ;
		for(i=0;i<el.value.length;i++){
			if(el.value=="0"){
				alert("'.JText::_('COM_SOCIALADS_ZERO_VALUE_VALI_MSG').'");
				el.value = el.value.substring(0,i); break;
			}
		 if((el.value.charCodeAt(i) <= 47 || el.value.charCodeAt(i) >= 58) || (el.value.charCodeAt(i) == 45 )){
				if((allowed_ascii !=el.value.charCodeAt(i))  ){
					alert("'.JText::_('COM_SOCIALADS_NUMONLY_VALUE_VALI_MSG').'"); el.value = el.value.substring(0,i); break;
				}
			}
		}
	}
function codechanger(where){
	var wid_code = "";
	if(where =="target")
	{
		wid_code = "<script>\n var Ad_widget_sitebase = \"'.JURI::root().'\";\n";
		if(jQuery("#field_target :input").length){
			wid_code2 = "";
			jQuery("#field_target :input").each(function(i){
				fname = jQuery(this).attr("name");
				fval= getvalue(this,"")
				if(fval != ""){
					var fieldarr = fname.split("][")[1].split(",");
					if (fieldarr[0].charAt(fieldarr[0].length - 1) == "]") {
						fieldarr[0] = fieldarr[0].substr(0, fieldarr[0].length - 1);
					}
					wid_code2 += fieldarr[0]+" : \""+fval+"\",";
				}
			});
			if(wid_code2 !== ""){
				wid_code2 = wid_code2.substring(0, wid_code2.length - 1);
				wid_code += "var Ad_targeting = {\n";
				wid_code += "social_params : {";
				wid_code +=wid_code2;
				wid_code += "}\n}"+";\n";
			}
		}
		wid_code += "</"+"script>";
		document.getElementById("wid_code").innerHTML = wid_code;
	}
	if(where =="widget")
	{
	wid_code = "<script>\n";
	wid_code += "var Ad_widget = {\n";
	var ad_unit = "sa_ads"+Math.floor(Math.random()*50);
	wid_code += "ad_unit : \""+ad_unit+"\",\n";
	wid_code += "zone : '.$zoneid.',\n";
	wid_code += "num_ads : "+getvalue(document.adminForm.num_ads,2)+",\n";
	wid_code += "ad_rotation : ";
	if(getvalue(document.adminForm.rotate,0)=="1"){
		wid_code += "1,\n";
		wid_code += "ad_rotation_delay : "+getvalue(document.adminForm.rotate_delay,10)+",\n";
	}else{
		wid_code += "0,\n";
	}
	wid_code += "no_rand : ";
	if(getvalue(document.adminForm.rand,0)=="1"){
		wid_code += "1\n";
	}else{
		wid_code += "0\n";
	}
	wid_code += "}"+";\n";
	wid_code += "</"+"script>\n";
	wid_code += "<div id=\""+ad_unit+"\"></div>\n";
	wid_code += "<script type=\"text/javascript\" src=\"'.JURI::root().'components/com_socialads/js/sawidget.js";
	if(document.getElementById("if_ht").value || document.getElementById("if_wid").value || getvalue(document.adminForm.if_seam,0)=="1" ){
		wid_code += "?";
		if(document.getElementById("if_ht").value){
			wid_code += "ifheight="+document.getElementById("if_ht").value+"&";
		}
		if(document.getElementById("if_wid").value){
			wid_code += "ifwidth="+document.getElementById("if_wid").value+"&";
		}
		if( getvalue(document.adminForm.if_seam,0)=="1" ){
			wid_code += "ifseamless="+getvalue(document.adminForm.if_seam,0)+"&";
		}

		wid_code = wid_code.substring(0, wid_code.length - 1);
	}
	wid_code += "\">";

	wid_code += "</"+"script>";

	wid_code += "\n";
	wid_code += "<script type=\"text/javascript\" >";
	wid_code += "\n// browser compatibility: get method for event ";
	wid_code += "\n// addEventListener(FF, Webkit, Opera, IE9+) and attachEvent(IE5-8) ";
	wid_code += "\nvar myEventMethod = window.addEventListener ? \"addEventListener\" : \"attachEvent\" ";
	wid_code += "\n// create event listener";
	wid_code += "\n var myEventListener = window[myEventMethod]; ";
	wid_code += "\n// browser compatibility: attach event uses onmessage";
	wid_code += "\nvar myEventMessage = myEventMethod == \"attachEvent\" ? \"onmessage\" : \"message\"; ";
	wid_code += "\n// register callback function on incoming message";
	wid_code += "\nmyEventListener(myEventMessage, function (e) { ";
	wid_code += "\n	// we will get a string (better browser support) and validate ";
	wid_code += "\n	// if it is an int - set the height of the iframe #my-iframe-id ";
	wid_code += "\n	if (e.data === parseInt(e.data)) ";
	wid_code += "\n		document.getElementById(\"idIframe_"+ad_unit+"\").height = e.data + \"px\"; ";
	wid_code += "\n}, false); ";
	wid_code += "\n";
	wid_code += "</"+"script>";

	document.getElementById("widunit_code").innerHTML = wid_code;
	}
}
';

$document->addScriptDeclaration($js);
 $document->addScriptDeclaration("
     ");

?>

<div class="techjoomla-bootstrap">
<form action="index.php" name="adminForm" id="adminForm" class="form-validate" method="post" >
	<div class="tabbable"> <!-- Only required for left/right tabs -->
		<ul class="nav nav-tabs">
			<li class="active"><a href="#tab1" data-toggle="tab"><b><?php echo JText::_('ZONE_BAS');?></b></a></li>
			<li><a href="#tab2" data-toggle="tab"><b><?php echo JText::_('ZONE_PRIC');?></b></a></li>
			<?php if($zoneid){ ?>
			<li><a href="#tab3" data-toggle="tab"><b><?php echo JText::sprintf('COM_SOCIALADS_ZONE_WIDGET',$this->zones[0]->zone_name);?></b></a></li>
			<?php } ?>
		</ul>
		<div class="tab-content">

		<!-- zone basic -->
			<div class="tab-pane active" id="tab1">
				<fieldset>
				<table class="table table-bordered" width="58%" cellspacing="8px">
				<tr>
					<td  width="25%"><span style="color:red">* </span><?php echo JHTML::tooltip(JText::_('ZONE_NAME_TOOLTIP'), JText::_('ZONE_NAME'), '', JText::_('ZONE_NAME'));?></td>
					<td >
						<input type="text" name="zone_name" id="zone_name" class="inputbox" size="20" value="<?php if($this->zones[0]){ echo $this->zones[0]->zone_name; } ?>" autocomplete="off" />
						<span id="validate_name" name="validate_name" class="invalid"></span>
					</td>
				</tr>
				<tr>
					<td  width="25%"><?php echo JText::_('ZONE_ENABLE');?> </td>
					<td class="">

						<?php
						$publish1=$publish2=$publish1_label=$publish2_label='';
						$publish2='checked="checked"';
						$publish2_label='btn-danger';
						if(isset($this->zones[0]->published))
						{
							if($this->zones[0]->published)
							{
								$publish1='checked="checked"';
								$publish1_label='btn-success';
								$publish2 = $publish2_label='';
							}
						}
							?>
						<div class="input-append yes_no_toggle">
							<input type="radio" class="inputbox fieldlist sa_setting_radio" name="published" id="published1" value="1" <?php echo $publish1;?>  >
							<label class="first btn <?php echo $publish1_label;?>" type="button" for="published1"><?php echo JText::_('COM_SOCIALADS_YES');?></label>
							<input type="radio" class="inputbox fieldlist sa_setting_radio" name="published" id="published0" value="0" <?php echo $publish2;?>  >
							<label class="last btn <?php echo $publish2_label;?>" type="button" for="published0"><?php echo JText::_('COM_SOCIALADS_NO');?></label>
						</div>
					</td>
				</tr>
				<tr>
					<td  width="25%"><?php echo JHTML::tooltip(JText::_('SELECT_ADD_TYPE_TOOLTIP'), JText::_('SELECT_ADD_TYPE'), '', JText::_('SELECT_ADD_TYPE'));?></td>
					<td class="setting-td">

					<?php
						$javascript = ' onchange="document.adminForm.showtype();"';

						//$add_type[] = JHTML::_('select.option', '0' , 'Select');
					if(in_array('text_img',$socialads_config['ad_type_allow']))
					$add_type[] = JHTML::_('select.option','text_img', JText::_('AD_TYP_TXT_IMG'));
					if(in_array('text',$socialads_config['ad_type_allow']))
					$add_type[] = JHTML::_('select.option','text', JText::_('AD_TYP_TXT'));
					if(in_array('img',$socialads_config['ad_type_allow']))
					$add_type[] = JHTML::_('select.option','img',JText::_('AD_TYP_IMG'));

						if($this->zones[0]){
			/*jugad code*/
			$rawresult = str_replace('||',',',$this->zones[0]->ad_type);
			$rawresult = str_replace('|','',$rawresult);
								$zone_type = explode(",",$rawresult);
			/*jugad code*/
							$default_layout=$zone_type[0];
						}
						else
							$default_layout='text_img';

						echo JHTML::_('select.genericlist', $add_type, 'add_type', 'class="inputbox" onchange=display(this);', 'value', 'text',$default_layout );
					?>
					<span id="validate_add_type" name="validate_add_type" class="invalid"></span>
					</td>

				</tr>
				<tr>
					<td  width="25%"><?php echo JHTML::tooltip(JText::_('SELECT_AFFILIATE_TOOLTIP'), JText::_('SELECT_AFFILIATE'), '', JText::_('SELECT_AFFILIATE'));?></td>
					<td >
					<?php
						$publish1=$publish2=$publish1_label=$publish2_label='';
						$publish2='checked="checked"';
						$publish2_label = 'btn-danger';
						if($this->zones[0]){
							 if(in_array('affiliate',$zone_type) )
							{
								$publish1='checked="checked"';
								$publish1_label = 'btn-success';
								$publish2 = $publish2_label='';
							}
						}
							?>
						<div class="input-append yes_no_toggle">
							<input type="radio" class="inputbox fieldlist sa_setting_radio" name="affiliate" id="affiliate1" value="1" <?php echo $publish1;?>  >
							<label class="first btn <?php echo $publish1_label;?>" type="button" for="affiliate1"><?php echo JText::_('COM_SOCIALADS_YES');?></label>
							<input type="radio" class="inputbox fieldlist sa_setting_radio" name="affiliate" id="affiliate0" value="0" <?php echo $publish2;?>  >
							<label class="last btn <?php echo $publish2_label;?>" type="button" for="affiliate0"><?php echo JText::_('COM_SOCIALADS_NO');?></label>
						</div>

					<!--<input type="checkbox" name="affiliate" value="on" id="affiliate"  <?php if($this->zones[0]){ echo (in_array('affiliate',$zone_type) )?'checked' :''; } ?> >-->
					<span id="validate_affiliate" name="validate_affiliate" class="invalid"></span>
					</td>
				</tr>
				<tr>
					<td  width="25%"><?php echo JHTML::tooltip(JText::_('SELECT_TYPE_ZONE_TOOLTIP'), JText::_('SELECT_TYPE_ZONE'), '', JText::_('SELECT_TYPE_ZONE'));?></td>
					<td class="setting-td">
					<?php
						//$zone_type[] = JHTML::_('select.option', '0' , JText::_("SA_SELONE"));
						$zone_orientation[] = JHTML::_('select.option', '1', JText::_("Z_HORI"));
						$zone_orientation[] = JHTML::_('select.option', '2', JText::_("Z_VERTI"));
						if($this->zones[0]){
						if($this->zones[0]->zone_type=='1')
							$zone_type1='1';
						else if($this->zones[0]->zone_type=='2')
							$zone_type1='2';
						}
						else
							$zone_type1='0';
						echo JHTML::_('select.genericlist', $zone_orientation, 'zone_type', 'class="inputbox "', 'value', 'text',$zone_type1);
					?>

					<span id="validate_zone_type" name="validate_zone_type" class="invalid"></span>
					</td>
				</tr>
				<tr id="img_width_row" name="img_width_row" <?php echo (($default_layout=='text_img' || $default_layout=='img' ) ? 'style="display:table-row;"': 'style="display:none;"'); ?>>
						<td  width="25%"><span style="color:red">* </span><?php echo JHTML::tooltip(JText::_('IMAGE_WIDTH_TOOLTIP'), JText::_('IMAGE_WIDTH'), '', JText::_('IMAGE_WIDTH'));?></td>
						<td class="setting-td">
						<input type="text" name="img_width" id="img_width" class="inputbox" size="20" value="<?php if($this->zones[0]){ echo $this->zones[0]->img_width; } ?>" autocomplete="off" Onkeyup= "checkforalpha(this);">
							<span id="validate_img_width" name="validate_img_width" class="invalid validate[numeric]"></span>
						</td>
				</tr>
				<tr id="img_height_row" name="img_height_row" <?php echo (($default_layout=='text_img' || $default_layout=='img' ) ? 'style="display:table-row;"': 'style="display:none;"'); ?>>
						<td  width="25%"><span style="color:red">* </span><?php echo JHTML::tooltip(JText::_('IMAGE_HEIGHT_TOOLTIP'), JText::_('IMAGE_HEIGHT'), '', JText::_('IMAGE_HEIGHT'));?></td>

						<td class="setting-td">
							<input type="text" name="img_height" id="img_height" class="inputbox" size="20" value="<?php if($this->zones[0]){ echo $this->zones[0]->img_height; } ?>" autocomplete="off"  Onkeyup= "checkforalpha(this);">
							<span id="validate_img_height" name="validate_img_height" class="invalid"></span>
						</td>

				</tr>
				<tr id="max_title_char_row" <?php echo (($default_layout=='text_img' || $default_layout=='text' ) ? 'style="display:table-row;"': 'style="display:none;"'); ?>>
						<td  width="25%"><span style="color:red">* </span><?php echo JHTML::tooltip(JText::_('MAX_TITLE_CHAR_TOOLTIP'), JText::_('MAX_TITLE_CHAR'), '', JText::_('MAX_TITLE_CHAR'));?></td>

						<td class="setting-td">
							<input type="text" name="max_title" id="max_title" class="inputbox" size="20" value="<?php if($this->zones[0]){ echo $this->zones[0]->max_title; } ?>" autocomplete="off"  Onkeyup= "checkforalpha(this);">
							<span id="validate_max_title" name="validate_max_title" class="invalid"></span>
						</td>
				</tr>

				<tr id="max_desc_char_row"  <?php echo (($default_layout=='text_img' || $default_layout=='text' ) ? 'style="display:table-row;"': 'style="display:none;"'); ?>>
					<td  width="25%"><span style="color:red">* </span><?php echo JHTML::tooltip(JText::_('MAX_DESC_CHAR_TOOLTIP'), JText::_('MAX_DESC_CHAR'), '', JText::_('MAX_DESC_CHAR'));?></td>
					<td class="setting-td">
						<input type="text" name="max_des" id="max_des" class="inputbox" size="20" value="<?php  if($this->zones[0]){ echo $this->zones[0]->max_des; } ?>" autocomplete="off"  Onkeyup= "checkforalpha(this);">
						<span id="validate_max_des" name="validate_max_des" class="invalid"></span>
					</td>
				</tr>

				<tr id="layout_row">
					<td  width="25%"><span style="color:red">* </span><?php echo JHTML::tooltip(JText::_('LAYOUT_TOOLTIP'), JText::_('LAYOUT'), '', JText::_('LAYOUT'));?></td>
					<td >
					<input type="hidden" id="zlayout" name="zlayout"  value="<?php  if($this->zones[0]){ echo $this->zones[0]->layout; } ?>">
					<input type="hidden" id="layout" name="layout"  value="<?php echo $this->zones[0]->layout;?>">
					<div id='layout_ad_ajax'>
					<div id='layout_ad1'>
						</div>
					</div>

					<span id="validate_layout" name="validate_layout" class="invalid"></span>
					</td>
				</tr>
				</table>
				</fieldset>
			</div>
		<!-- zone basic ends -->
		<!-- zone pricing -->
		<div class="tab-pane" id="tab2">
			<fieldset>
				<table class="table table-bordered" width="60%">
				<?php
				if($socialads_config['zone_pricing'])
				{
					//foreach($socialads_config['pricing_opt'] as $k=> $v){
				if(in_array('1', $socialads_config['pricing_opt'])){
				?>
					 <tr>
				<?php }
				else{ ?> <tr style="display:none;">
				<?php } ?>
						<td  width="25%"><span style="color:red">* </span><?php echo JHTML::tooltip(JText::_('ZONE_PRICE_CLICK_TOOLTIP'), JText::_('CLICKS_PRICE'), '', JText::_('CLICKS_PRICE'));?></td>
						<td class="setting-td">
							<input type="text" name="per_click" id="per_click" class="inputbox required" size="20" value="<?php if($this->zones[0]){ echo $this->zones[0]->per_click; } ?>" autocomplete="off" Onkeyup= "checkforalpha(this,46);" />
							<span id="validate_per_click" name="validate_per_click" class="invalid"></span>
						</td>
					</tr>
				<?php
				if(in_array('0', $socialads_config['pricing_opt'])){
				?>
					<tr >
				<?php }
				else{ ?> <tr style="display:none;">
				<?php } ?>

						<td  width="25%"><span style="color:red">* </span><?php echo JHTML::tooltip(JText::_('ZONE_PRICE_IMPR_TOOLTIP'), JText::_('IMPR_PRICE'), '', JText::_('IMPR_PRICE'));?></td>
						<td class="setting-td">
							<input type="text" name="per_imp" id="per_imp" class="inputbox required" size="20" value="<?php if($this->zones[0]){ echo $this->zones[0]->per_imp; } ?>" autocomplete="off" Onkeyup= "checkforalpha(this,46);" />
							<span id="validate_per_imp" name="validate_per_imp" class="invalid"></span>
						</td>
					</tr>
				<?php
				if(in_array('2', $socialads_config['pricing_opt'])){
				?>
					<tr>
				<?php }
				else{ ?> <tr style="display:none;">
				<?php } ?>

						<td  width="25%"><span style="color:red">* </span><?php echo JHTML::tooltip(JText::_('ZONE_PRICE_DATE_TOOLTIP'), JText::_('DATE_PRICE'), '', JText::_('DATE_PRICE'));?></td>
						<td class="setting-td">
							<input type="text" name="per_day" id="per_day" class="inputbox required" size="20" value="<?php if($this->zones[0]){ echo $this->zones[0]->per_day; } ?>" autocomplete="off" Onkeyup= "checkforalpha(this,46);" />
							<span id="validate_per_day" name="validate_per_day" class="invalid"></span>
						</td>
					</tr>
				<?php
					//}
				}
				else
				{
				?>
				<tr >
					<td  width="25%"></td>
					<td >
					<span id="validate_zone_price" name="validate_zone_price" class="invalid"><?php echo JText::_('ZONE_PRICE_ENABLE'); ?></span>

					</td>
				</tr>
				<?php }?>

				</table>
			</fieldset>
		</div>
		<!-- zone pricing ends -->

			<input type="hidden" name="zone_id" id="zone_id" value="<?php if($this->zones[0]){ echo $this->zones[0]->id; } ?>" />
			<input type="hidden" name="option" value="com_socialads" />
			<input type="hidden" id='hidid' name="id" value="" />
			<input type="hidden" id='hidstat' name="status" value="" />
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="view" value="managezone" />
			<input type="hidden" name="controller" value="managezone" />

		<?php if(!empty($this->zones[0]->id)){ ?>
		<div class="tab-pane" id="tab3">

		<fieldset>
		<div class="row-fluid">
		<div class="span6">
			<div class="tabbable tabs-left">
				<ul class="nav nav-pills">
					<li onclick="jQuery('#wtab2').hide();" class="active" ><a href="#wtab1" data-toggle="tab"><?php echo JText::_('COM_SOCIALADS_ZONE_WIDGET_CUSTOM');?></a></li>
					<?php
					if($socialads_config['integration'] != 2){ ?>
					<li onclick="jQuery('#wtab2').show();"><a  href="#wtab2" data-toggle="tab"><?php echo JText::_('COM_SOCIALADS_ZONE_WIDGET_TARGET');?></a></li>
					<?php } ?>
				</ul>
				<div class="tab-content">
					<div class="tab-pane active" id="wtab1">
						<table id="widget" class="table table-bordered " cellspacing="8px">
							<tr>
								<td  width="25%"><?php echo JHTML::tooltip(JText::_('COM_SOCIALADS_ZONE_NUM_ADS_TOOLTIP'), JText::_('COM_SOCIALADS_ZONE_NUM_ADS'), '', JText::_('COM_SOCIALADS_ZONE_NUM_ADS'));?><span style="color:red" class="star">&nbsp;*</span></td>
								<td >
									<input type="text" name="num_ads" id="num_ads" class="inputbox input-small" size="10" value="2" autocomplete="off"  onkeyup="checkforalpha(this);"/>
								</td>
							</tr>
							<tr>
								<td  width="25%"><?php echo JHTML::tooltip(JText::_('COM_SOCIALADS_ZONE_ROTATE_ADS_TOOLTIP'), JText::_('COM_SOCIALADS_ZONE_ROTATE_ADS'), '', JText::_('COM_SOCIALADS_ZONE_ROTATE_ADS'));?></td>
								<td >
									<div class="input-append yes_no_toggle">
										<input type="radio" name="rotate" id="publish1" value="1"  >
										<label class="first btn " type="button" for="publish1"><?php echo JText::_('COM_SOCIALADS_YES');?></label>
										<input type="radio" name="rotate" id="publish2" value="0" checked="checked" >
										<label class="last btn btn-danger" type="button" for="publish2"><?php echo JText::_('COM_SOCIALADS_NO');?></label>
									</div>
								</td>
							</tr>
							<tr>
								<td  width="25%"><?php echo JHTML::tooltip(JText::_('COM_SOCIALADS_ZONE_ROTATE_ADS_DELAY_TOOLTIP'), JText::_('COM_SOCIALADS_ZONE_ROTATE_ADS_DELAY'), '', JText::_('COM_SOCIALADS_ZONE_ROTATE_ADS_DELAY'));?></td>
								<td >
									<input type="text" name="rotate_delay" id="rotate_delay" class="inputbox input-small" size="10" value="10" autocomplete="off" onkeyup="checkforalpha(this);" />
								</td>
							</tr>
							<tr>
								<td  width="25%"><?php echo JHTML::tooltip(JText::_('COM_SOCIALADS_ZONE_RAND_ADS_TOOLTIP'), JText::_('COM_SOCIALADS_ZONE_RAND_ADS'), '', JText::_('COM_SOCIALADS_ZONE_RAND_ADS'));?></td>
								<td >
									<div class="input-append yes_no_toggle">
										<input type="radio" name="rand" id="rand1" value="1"  >
										<label class="first btn" type="button" for="rand1"><?php echo JText::_('COM_SOCIALADS_YES');?></label>
										<input type="radio" name="rand" id="rand2" value="0" checked="checked" >
										<label class="last btn btn-danger" type="button" for="rand2"><?php echo JText::_('COM_SOCIALADS_NO');?></label>
									</div>
								</td>
							</tr>
							<tr>
								<td  width="25%"><?php echo JHTML::tooltip(JText::_('COM_SOCIALADS_ZONE_IFWID_ADS_TOOLTIP'), JText::_('COM_SOCIALADS_ZONE_IFWID_ADS'), '', JText::_('COM_SOCIALADS_ZONE_IFWID_ADS'));?></td>
								<td >
									<div class="input-append">
										<input type="text" name="if_wid" id="if_wid" class="inputbox input-mini" size="10" value="" placeholder="<?php echo JText::_('COM_SOCIALADS_IF_WID_HOLDER');?>" autocomplete="off" />
										<span class="add-on">px</span>
									</div>
								</td>
							</tr>
							<tr>
								<td  width="25%"><?php echo JHTML::tooltip(JText::_('COM_SOCIALADS_ZONE_IFHT_ADS_TOOLTIP'), JText::_('COM_SOCIALADS_ZONE_IFHT_ADS'), '', JText::_('COM_SOCIALADS_ZONE_IFHT_ADS'));?></td>
								<td >
									<div class="input-append">
									<input type="text" name="if_ht" id="if_ht" class="inputbox input-mini" placeholder="<?php echo JText::_('COM_SOCIALADS_IF_HT_HOLDER');?>" size="10" value="" autocomplete="off" />
										<span class="add-on">px</span>
									</div>
								</td>
							</tr>
							<tr>
								<td  width="25%"><?php echo JHTML::tooltip(JText::_('COM_SOCIALADS_ZONE_IF_SEAMLS_ADS_TOOLTIP'), JText::_('COM_SOCIALADS_ZONE_IF_SEAMLS_ADS'), '', JText::_('COM_SOCIALADS_ZONE_IF_SEAMLS_ADS'));?></td>
								<td >
									<div class="input-append yes_no_toggle">
										<input type="radio" name="if_seam" id="if_seam1" value="1"  checked="checked"  >
										<label class="first btn btn-success" type="button" for="if_seam1"><?php echo JText::_('COM_SOCIALADS_YES');?></label>
										<input type="radio" name="if_seam" id="if_seam2" value="0">
										<label class="last btn" type="button" for="if_seam2"><?php echo JText::_('COM_SOCIALADS_NO');?></label>
									</div>
								</td>
							</tr>
						</table>

					</div>
					<?php if($socialads_config['integration'] != 2){ ?>
					<div class="tab-pane active" id="wtab2">
					<?php
						if(!empty($this->fields)){ ?>
							<!-- field_target starts here -->
							<div id="field_target">
								<!-- floatmain starts here -->
								<div id="floatmain" >
									<div id="mapping-field-table">
								<!--for loop which shows JS fields with select types-->
								<table class="table table-bordered widget" cellspacing="8px">
									<?php
									if($socialads_config['integration'] == 0){
										require(JPATH_SITE.DS."components".DS."com_comprofiler".DS."plugin".DS."language".DS."default_language".DS."default_language.php");
										global $_CB_framework, $_CB_database, $ueConfig, $mainframe;
										include_once( JPATH_ADMINISTRATOR . '/components/com_comprofiler/plugin.foundation.php' );
									}
									$i=1;
									foreach($this->fields as $fields)
									{
										if($fields->mapping_fieldtype!='targeting_plugin'){

									?>
									<tr>
										<td>
										<div class="control-group">
											<label class="ad-fields-lable "><?php
												if($socialads_config['integration'] == 0){
												$fields->mapping_label = htmlspecialchars( getLangDefinition( $fields->mapping_label));
												}
												else{
													$fields->mapping_label = JText::_("$fields->mapping_label");
												}

												echo $fields->mapping_label;?>
											</label>
										</td>
										<td>
											<div class="controls">

											   <!--Numeric Range-->
												<?php
												//for easysocial fileds of those app are created..(gender,boolean and address)

												if($fields->mapping_fieldtype=="gender")
												{
													$gender[] = JHTML::_('select.option','', JText::_("SELECT"));
													$gender[] = JHTML::_('select.option','2', JText::_("FEMALE"));
													$gender[] = JHTML::_('select.option','1', JText::_("MALE"));
													echo JHTML::_('select.genericlist', $gender, 'mapdata[]['.$fields->mapping_fieldname.',select]', ' class="ad-fields-inputbox" id="mapdata[]['.$fields->mapping_fieldname.',select]" size="1"',   'value', 'text', $flds[$fields->mapping_fieldname.',select']);
												}
												if($fields->mapping_fieldtype=="boolean")
												{
													$boolean[] = JHTML::_('select.option','', JText::_("SELECT"));
													$boolean[] = JHTML::_('select.option','1', JText::_("YES"));
													$boolean[] = JHTML::_('select.option','0', JText::_("NO"));
													echo JHTML::_('select.genericlist', $boolean, 'mapdata[]['.$fields->mapping_fieldname.',select]', ' class="ad-fields-inputbox" id="mapdata[]['.$fields->mapping_fieldname.',select]" size="1"',   'value', 'text', $flds[$fields->mapping_fieldname.',select']);
												}
												/*
												if($fields->mapping_fieldtype=="address")
												{

												}
												*/
												if($fields->mapping_fieldtype=="numericrange")
														{
															$lowvar = $fields->mapping_fieldname.'_low';
															$highvar = $fields->mapping_fieldname.'_high';
														if(isset($flds[$fields->mapping_fieldname.'_low']) || isset($this->addata_for_adsumary_edit->$lowvar))
														{
															$grad_low=0;
															$grad_high=2030;
															if($this->edit_ad_adsumary)
															{
																$onkeyup=" ";
																if(strcmp($this->addata_for_adsumary_edit->$lowvar,$grad_low)==0)
																		$this->addata_for_adsumary_edit->$lowvar = '';
																if( (strcmp($this->addata_for_adsumary_edit->$highvar,$grad_high)==0) || (strcmp($this->addata_for_adsumary_edit->$highvar,$grad_low)==0) )
																		$this->addata_for_adsumary_edit->$highvar = '';
																		?>
																		<input type="textbox"  class="ad-fields-inputbox" name="mapdata[][<?php echo $fields->mapping_fieldname.'_low|numericrange|0'; ?>]" value="<?php echo $this->addata_for_adsumary_edit->$lowvar; ?>" <?php echo $display_reach; ?> />
																		<?php echo JText::_('SA_TO'); ?>
																		<input type="textbox" class="ad-fields-inputbox" name="mapdata[][<?php echo $fields->mapping_fieldname.'_high|numericrange|1'; ?>]" value="<?php echo $this->addata_for_adsumary_edit->$highvar?>" <?php echo $display_reach; ?> />

													<?php		}
															else
															{
																$onkeyup="  Onkeyup = checkforalpha(this) ";
																if(strcmp($flds[$fields->mapping_fieldname.'_low'],$grad_low)==0)
																		$flds[$fields->mapping_fieldname.'_low'] = '';
																if( (strcmp($flds[$fields->mapping_fieldname.'_high'],$grad_high)==0)|| (strcmp($flds[$fields->mapping_fieldname.'_high'],$grad_high)==0) )
																		$flds[$fields->mapping_fieldname.'_high'] = '';
																?>
																	<input type="textbox"  class="ad-fields-inputbox" name="mapdata[][<?php echo $fields->mapping_fieldname.'_low|numericrange|0'; ?>]" value="<?php echo $flds[$fields->mapping_fieldname.'_low']?>" Onkeyup = checkforalpha(this); <?php echo $display_reach; ?> />
																	<?php echo JText::_('SA_TO'); ?>
																	<input type="textbox" class="ad-fields-inputbox" name="mapdata[][<?php echo $fields->mapping_fieldname.'_high|numericrange|1'; ?>]" value="<?php echo $flds[$fields->mapping_fieldname.'_high']?>" <?php echo $display_reach; ?> Onkeyup = checkforalpha(this); />
														<?php	}
														?>
														<?php }
														else
														{ ?>
														<input type="textbox"  class="ad-fields-inputbox" name="mapdata[][<?php echo $fields->mapping_fieldname.'_low|numericrange|0'; ?>]" value="" <?php echo $display_reach; ?> <?php echo $onkeyup; ?> />
														<?php echo JText::_('SA_TO'); ?>
														<input type="textbox" class="ad-fields-inputbox" name="mapdata[][<?php echo $fields->mapping_fieldname.'_high|numericrange|1'; ?>]" value="" <?php echo $display_reach; ?> <?php echo $onkeyup; ?> />
													<?php } } ?>
												<!--Freetext-->
												<?php if($fields->mapping_fieldtype=="textbox")
												{
													$textvar = $fields->mapping_fieldname;
													if(isset($flds[$fields->mapping_fieldname]) || isset($this->addata_for_adsumary_edit->$textvar))
													{
														if($this->edit_ad_adsumary)
														{
														?>
														<input type="textbox" class="ad-fields-inputbox" name="mapdata[][<?php  echo $fields->mapping_fieldname; ?>]" value="<?php echo $this->addata_for_adsumary_edit->$textvar; ?>" <?php echo $display_reach; ?> />
														<?php }
														else
														{
															?>
													<input type="textbox" class="ad-fields-inputbox" name="mapdata[][<?php echo $fields->mapping_fieldname; ?>]" value="<?php echo $flds[$fields->mapping_fieldname]; ?>" <?php echo $display_reach; ?>/>
													<?php	}
													}
													else
														{?>
													<input type="textbox" class="ad-fields-inputbox" name="mapdata[][<?php echo $fields->mapping_fieldname; ?>]" value=""
														<?php echo $display_reach; ?> />
													<?php }
												}?>
												<!--Single Select-->
												<?php
													if($fields->mapping_fieldtype=="singleselect")
													{
														$singlevar = $fields->mapping_fieldname;
														if(isset($flds[$fields->mapping_fieldname.',select']) || isset($this->addata_for_adsumary_edit->$singlevar))
														{
															$singleselect = $fields->mapping_options;
															$singleselect = explode("\n",$singleselect);
															for($count=0;$count<count($singleselect); $count++){


																		$options[] = JHTML::_('select.option',$singleselect[$count],JText::_($singleselect[$count]),'value','text');
																}

															$s= array();
															$s[0]->value = '';
															$s[0]->text = JText::_('SINGSELECT');
															$options = array_merge($s, $options);
															if($this->edit_ad_adsumary)
															{
																$mdata = str_replace('||',',',$this->addata_for_adsumary_edit->$singlevar);
																$mdata = str_replace('|','',$mdata);
																echo JHTML::_('select.genericlist', $options, 'mapdata[]['.$fields->mapping_fieldname.',select]', 'class="ad-fields-inputbox" size="1" '.$display_reach,   'value', 'text', $mdata);
															}
															else
															echo JHTML::_('select.genericlist', $options, 'mapdata[]['.$fields->mapping_fieldname.',select]', ' class="ad-fields-inputbox"'.$display_reach.' id="mapdata[]['.$fields->mapping_fieldname.',select]" size="1"',   'value', 'text', $flds[$fields->mapping_fieldname.',select']);
															$options= array();
														}
														else
														{
															$singleselect = $fields->mapping_options;
															$singleselect = explode("\n",$singleselect);
															for($count=0;$count<count($singleselect); $count++)
																{

																		$options[] = JHTML::_('select.option',$singleselect[$count], JText::_($singleselect[$count]),'value','text');
																}

															$s= array();
															$s[0]->value = '';
															$s[0]->text = JText::_('SINGSELECT');
															$options = array_merge($s, $options);

															echo JHTML::_('select.genericlist', $options, 'mapdata[]['.$fields->mapping_fieldname.',select]', 'class="ad-fields-inputbox"  id="mapdata[]['.$fields->mapping_fieldname.',select]"'.$display_reach.' size="1"',   'value', 'text', '');
															$options= array();
														}
													}
											//Multiselect
													if($fields->mapping_fieldtype=="multiselect" )
													{
														$multivar = $fields->mapping_fieldname;
														$options= array();
														if(isset($flds[$fields->mapping_fieldname.',select']) || isset($this->addata_for_adsumary_edit->$multivar))
															{
																$multiselect = $fields->mapping_options;
																$multiselect = explode("\n",$multiselect);
																if($this->edit_ad_adsumary)
																{
																	$mdata = str_replace('||',',',$this->addata_for_adsumary_edit->$multivar);
																	$mdata = str_replace('|','',$mdata);
																	$multidata = explode(",",$mdata);
																	//print_r($multidata);
																}
																	for($cnt=0;$cnt<count($multiselect); $cnt++)
																	{

																		$options[] = JHTML::_('select.option',$multiselect[$cnt], JText::_($multiselect[$cnt]),'value','text');
																	}

																	if($cnt > 20)
																	{
																		$size = '6';
																	}
																	else
																	{
																		$size = '3';
																	}

																	echo JHTML::_('select.genericlist', $options, 'mapdata[]['.$fields->mapping_fieldname.',select]', 'class="ad-fields-inputbox inputbox chzn-done" id="mapdata[]['.$fields->mapping_fieldname.',select]" size="'.$size.'"  multiple="multiple" '.$display_reach,   'value', 'text', $multidata);
																	$options= array();
															}
															else
															{

																$multiselect = $fields->mapping_options;
																$multiselect = explode("\n",$multiselect);
																for($cnt=0;$cnt<count($multiselect); $cnt++)
																{

																		$options[] = JHTML::_('select.option',$multiselect[$cnt], JText::_($multiselect[$cnt]),'value','text');

																}

																if($cnt > 20)
																{	$size = '6';}
																else
																	$size = '3';
																echo JHTML::_('select.genericlist', $options, 'mapdata[]['.$fields->mapping_fieldname.',select]', 'class="ad-fields-inputbox  inputbox chzn-done"  size="'.$size.'" id="mapdata[]['.$fields->mapping_fieldname.',select]" multiple="multiple"'.$display_reach,   'value', 'text', '');

																$options= array();
															}
												  }
													 //daterange
													if($fields->mapping_fieldtype=="daterange")
													{
														$this->datelowvar = $fields->mapping_fieldname.'_low';
														$this->datehighvar = $fields->mapping_fieldname.'_high';
														if(isset($flds[$fields->mapping_fieldname.'_low']) || isset($this->addata_for_adsumary_edit->$this->datelowvar))
														{
																	$date_low = date('Y-m-d 00:00:00', mktime(0,0,0,01,1,1910));
																	$date_high = date('Y-m-d 00:00:00', mktime(0,0,0,01,1,2030));
																if($this->edit_ad_adsumary){
																	if(strcmp($this->addata_for_adsumary_edit->$this->datelowvar,$date_low)==0)
																	$this->addata_for_adsumary_edit->$this->datelowvar = '';

																	if(strcmp($this->addata_for_adsumary_edit->$this->datehighvar,$date_high)==0)
																	$this->addata_for_adsumary_edit->$this->datehighvar = '';
																	echo JHTML::_('calendar', $this->addata_for_adsumary_edit->$this->datelowvar, 'mapdata[]['.$fields->mapping_fieldname.'_low|daterange|0]', 'mapdata[]['.$fields->mapping_fieldname.'_low]', '%Y-%m-%d', array('class'=>'ad-fields-inputbox',$display_reach_fun));
																	echo JText::_('SA_TO');
																	echo JHTML::_('calendar', $this->addata_for_adsumary_edit->$this->datehighvar, 'mapdata[]['.$fields->mapping_fieldname.'_high|daterange|1]', 'mapdata[]['.$fields->mapping_fieldname.'_high]', '%Y-%m-%d', array('class'=>'ad-fields-inputbox',$display_reach_fun));
																}
																else
																{
																	if(strcmp($flds[$fields->mapping_fieldname.'_low'],$date_low)==0)
																		$flds[$fields->mapping_fieldname.'_low'] = '';
																	if(strcmp($flds[$fields->mapping_fieldname.'_high'],$date_high)==0)
																		$flds[$fields->mapping_fieldname.'_high'] = '';

																	echo JHTML::_('calendar', $flds[$fields->mapping_fieldname.'_low'], 'mapdata[]['.$fields->mapping_fieldname.'_low]', 'mapdata[]['.$fields->mapping_fieldname.'_low]', '%Y-%m-%d', array('class'=>'ad-fields-inputbox',$display_reach_fun));
																	echo JText::_('SA_TO');
																	echo JHTML::_('calendar', $flds[$fields->mapping_fieldname.'_high'], 'mapdata[]['.$fields->mapping_fieldname.'_high]', 'mapdata[]['.$fields->mapping_fieldname.'_high]', '%Y-%m-%d', array('class'=>'ad-fields-inputbox',$display_reach_fun));
																}
														}
														else
														{
															if($this->edit_ad_adsumary)
															{
																	echo JHTML::_('calendar', '', 'mapdata[]['.$fields->mapping_fieldname.'_low|daterange|0]', 'mapdata[]['.$fields->mapping_fieldname.'_low]', '%Y-%m-%d', array('class'=>'ad-fields-inputbox','onchange'=>'calculatereach()'));
																	echo JText::_('SA_TO');
																	echo JHTML::_('calendar', '', 'mapdata[]['.$fields->mapping_fieldname.'_high|daterange|1]', 'mapdata[]['.$fields->mapping_fieldname.'_high]', '%Y-%m-%d', array('class'=>'ad-fields-inputbox',$display_reach_fun));
															}
															else
															{
																	echo JHTML::_('calendar', '', 'mapdata[]['.$fields->mapping_fieldname.'_low|daterange|0]', 'mapdata[]['.$fields->mapping_fieldname.'_low]', '%Y-%m-%d', array('class'=>'ad-fields-inputbox',$display_reach_fun));
																	echo JText::_('SA_TO');
																	echo JHTML::_('calendar', '', 'mapdata[]['.$fields->mapping_fieldname.'_high|daterange|1]', 'mapdata[]['.$fields->mapping_fieldname.'_high]', '%Y-%m-%d', array('class'=>'ad-fields-inputbox',$display_reach_fun));
															}
														}
														if($this->datelow==null) { $this->datelow = $fields->mapping_fieldname; } else {  $this->datelow .= ','.$fields->mapping_fieldname; }

													}

												 //date
														if($fields->mapping_fieldtype=="date")
														{
															$datevar = $fields->mapping_fieldname;
															if(isset($flds[$fields->mapping_fieldname]) || isset($this->addata_for_adsumary_edit->$datevar))
															{
																if($this->edit_ad_adsumary)
																{
																	echo JHTML::_('calendar', $this->addata_for_adsumary_edit->$datevar , 'mapdata[]['.$fields->mapping_fieldname.']', 'mapdata[]['.$fields->mapping_fieldname.']','%Y-%m-%d', array('class'=>'ad-fields-inputbox',$display_reach_fun));
																}
																else
																{
																	echo JHTML::_('calendar', $flds[$fields->mapping_fieldname] , 'mapdata[]['.$fields->mapping_fieldname.']',
																	'mapdata[]['.$fields->mapping_fieldname.']','%Y-%m-%d', array('class'=>'ad-fields-inputbox',$display_reach_fun));
																}
														}
														else
														{
															echo JHTML::_('calendar', '', 'mapdata[]['.$fields->mapping_fieldname.']',
															'mapdata[]['.$fields->mapping_fieldname.']','%Y-%m-%d', array('class'=>'ad-fields-inputbox',$display_reach_fun));
														} ?>
											  <?php }?>

											</div>
										</div>
										</td>
									</tr>
								 <?php
											$i++;
										}
									} ?>
								</table>

										<div style="clear:both"></div>
									</div>
								</div><!-- End fo floatmain div -->
							</div><!-- End fo field_target div -->
							<?php }//end for fields not empty condition
							?>
					</div>
					<?php } ?>
				</div>
			</div>
		</div>
		<div class="span6">
			<div class="well">
				<label><?php echo JText::_('COM_SOCIALADS_WIDGET_CODE');?></label>
				<?php
				$wdiget_code = "<script>\n var Ad_widget_sitebase = '".JURI::root()."';\n";
				$wdiget_code .= "</"."script>";
				?>
				<textarea id="wid_code" rows="5" cols="80" onclick="this.select()" spellcheck="false" style="width: 100% !important;"><?php echo $wdiget_code;
				  ?></textarea>
				<label><?php echo JText::_('COM_SOCIALADS_WIDGETUNIT_CODE');?></label>
				<textarea id="widunit_code" rows="15" cols="80" onclick="this.select()" spellcheck="false" style="width: 100% !important;"></textarea>
			</div>
		</div>
		</div>
		</fieldset>

		</div>
		<?php } ?>

		<script type="text/javascript">
		<?php
			if($this->zones[0])
				{
						if($this->zones[0]->ad_type=="text_img")
						{?>
							document.getElementById('img_width_row').style.display="";
							document.getElementById('img_height_row').style.display="";
							document.getElementById('max_title_char_row').style.display="";
							document.getElementById('max_desc_char_row').style.display="";
						<?php
						}
						else if($this->zones[0]->ad_type=="img")
						{?>
							document.getElementById('img_width_row').style.display="";
							document.getElementById('img_height_row').style.display="";
							document.getElementById('max_title_char_row').style.display="none";
							document.getElementById('max_desc_char_row').style.display="none";

						<?php
						}
						else if($this->zones[0]->ad_type=="text")
						{
					?>
							document.getElementById('img_width_row').style.display="none";
							document.getElementById('img_height_row').style.display="none";
							document.getElementById('max_title_char_row').style.display="";
							document.getElementById('max_desc_char_row').style.display="";
						<?php
						}
				}
				?>
		</script>

		</div>
	</div>
</form>
</div>
