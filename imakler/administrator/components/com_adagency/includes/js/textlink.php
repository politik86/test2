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
?>

<script language="javascript" type="text/javascript">

ADAG(document).ready(function(){
	callall();
	ADAG("#initvalcamp").val(ADAG("#approved").val());
	ADAG('#remimg').click(function(event){
		ADAG('input[name=image_url]').val('');
		ADAG('img[name=imagelib]').prop('src','');
		//ADAG('#wxh').html('x');
		event.preventDefault();
	});
	ADAG('#mxsize').keyup(function(){ updateWH(); });
	ADAG('#mxtype').change(function(){ updateWH(); });
	ADAG('#parametersalign').change(function(){ setTextAlign(); });
});

ADAG(function(){

            ADAG('#change_cb').openDOMWindow({
                height: 450,
                width: 600,
                positionTop: 50,
                eventType: 'click',
                positionLeft: 50,
                windowSource: 'iframe',
                windowPadding: 0,
                loader: 1,
                loaderImagePath: '<?php echo JURI::root()."components/com_adagency/images/loading.gif"; ?>',
                loaderHeight: 31,
                loaderWidth: 31
            });
            ADAG('#close_cb').closeDOMWindow({eventType:'click'});

        <?php
            for ($i=0;$i<count($camps);$i++) {
                // store adslimit & totalbanners for each campaign
                // for later checks
        ?>
                $current_camp = ADAG('#adv_cmp<?php echo $i+1; ?>');
                if ($current_camp.length) {
                    ADAG.data($current_camp[0], 'adslim', '<?php
                        if ( isset( $camps[$i]->params['adslim'] ) ) {
                            $adslim = $camps[$i]->params['adslim'];
                        } else {
                            $adslim = '999';
                        }
                        echo $adslim;
                    ?>');
                    ADAG.data($current_camp[0], 'totalbanners', '<?php
                        if ( isset( $camps[$i]->totalbanners ) ) {
                            echo $camps[$i]->totalbanners;
                        } else {
                            echo '0';
                        }
                    ?>');
                    // check if campaign has reached max number of ads
                    // and don't allow banner assignment if so
                    $current_camp.live('click', function(event) {
                        var adslim = ADAG.data(ADAG('#adv_cmp<?php echo $i+1; ?>')[0], 'adslim'),
                        totalbanners = ADAG.data(ADAG('#adv_cmp<?php echo $i+1; ?>')[0], 'totalbanners'),
                        availableads = adslim - totalbanners,
                        val = ADAG(this).val();

                        if ( !ADAG(this).prop('checked') ) {
                            newTotalbanners = parseInt(totalbanners,10) - 1;
                            ADAG.data(ADAG('#adv_cmp<?php echo $i+1; ?>')[0], 'totalbanners', newTotalbanners);
                        } else {
                            //alert("Available ads: " + adslim + " - " + totalbanners + " = " + availableads);

                            if ( (availableads <= 0) && (ADAG('#DOMWindow').length == 0) ) {
                                event.preventDefault();
                                window.setTimeout(function() {
                                    //alert(val);
                                    var answer = confirm('<?php echo JText::sprintf('ADAG_CMP_LIMIT_AD_WARN2', $adslim); ?>');
                                    if (answer) {
                                        ADAG('#change_cb').prop(
                                            'href',
                                            '<?php echo JURI::root(); ?>index.php?option=com_adagency&controller=adagencyCampaigns&task=changecb&id=' + val + '&tmpl=component'
                                        ).click();
                                    }
                                    // ADAG('#close_cb').click();
                                }, 1);
                            } else if (availableads <= 0) {
                                event.preventDefault();
                            } else {
                                newTotalbanners = parseInt(totalbanners,10) + 1;
                                ADAG.data(ADAG('#adv_cmp<?php echo $i+1; ?>')[0], 'totalbanners', newTotalbanners);
                            }
                        }

                    });
                }
        <?php
            }
        ?>

	ADAG('.close_it').click(function(){
		ADAG('#ajax_adv').remove();
	});
	ADAG('#approve_and_email').click(function(){
		var aid = ADAG('#advertiser_aid').val();
		ADAG.ajax({
		  url: 'index.php?option=com_adagency&controller=adagencyAdvertisers&task=ajax_adv&aid='+aid+'&no_html=1&tmpl=component',
		  success: function(data) {
			data = ADAG.trim(data);
			if(data == 'ok'){
				ADAG('#ajax_adv').html('<?php echo JText::_('ADVAPPROVED')."<br /><br /><span class=\'close_it\' onclick=\'document.getElementById(\"ajax_adv\").style.display=\"none\"\' style=\'font-weight:bold; text-decoration: underline; cursor: pointer;\' >".JText::_('ADAG_CLOSE')."</span>"; ?>');
			}
			//alert('Load was performed: |' + data + '|');
		  }
		});
	});
	ADAG('#approve_no_email').click(function(){
		var aid = ADAG('#advertiser_aid').val();
		ADAG.ajax({
		  url: 'index.php?option=com_adagency&controller=adagencyAdvertisers&task=ajax_adv&aid='+aid+'&no_html=1&tmpl=component&sendmail=0',
		  success: function(data) {
			data = ADAG.trim(data);
			if(data == 'ok'){
				ADAG('#ajax_adv').html('<?php echo JText::_('ADVAPPROVED')."<br /><br /><span class=\'close_it\' onclick=\'document.getElementById(\"ajax_adv\").style.display=\"none\"\' style=\'font-weight:bold; text-decoration: underline; cursor: pointer;\' >".JText::_('ADAG_CLOSE')."</span>"; ?>');
			}
			//alert('Load was performed: |' + data + '|');
		  }
		});
	});
});

function checkZones(){
	var ok = true;
	ADAG('#campaign_table').find('tr:gt(0)').each(function(index){
  		if((ADAG(this).find('.check_camp input.formField').prop('checked') == true)&&(ADAG(this).find('.check_ad .w145').val() == 0)){
		    ok = false;
		}
	});
	return ok;
}

function refreshAdv(id){
	var id = id || null;
	ADAG.get('index.php?option=com_adagency&controller=adagencyAdvertisers&task=getAdvertisersAjax&no_html=1', function(data) {
		if(data != ''){
			var sel = '<?php echo JText::_('AD_SELECT_ADVERTISER'); ?>';
			var toAppend = '<option value="0">'+sel+'</option>';
			var temp = data.split('|');
			for(var i=0;i<=temp.length-1;i++){
				var temp2 = temp[i].split(',');
				if((id != null)&&(id == temp2[0])) { var selected = ' selected = "selected" '; } else { var selected = ''; }
				toAppend +='<option '+ selected +' value="'+ADAG.trim(temp2[0])+'">'+temp2[1]+'</option>';
			}
			//alert(toAppend);
			ADAG('#advertiser_id').find('option').remove().end().append(toAppend);
			//alert(data); */
		}
	});
}

function callall() {
	changeLinkTitle();
	changeBody();
	changeAction();
	changeFontTitle();
	changeFontBody();
	changeFontAction();
	changeFSTitle();
	changeFSBody();
	changeFSAction();
	changeFWTitle();
	changeFWBody();
	changeFWAction();
	updateColors2();
	changePadding();
	changeBorder();
	//changeSize();
	setOverflow();
	//setImageAlign();
	//updateWH();
	setTextAlign();
	//setImageWrap();
}
var imagearray = [<?php echo $realimgs?>];

function setTextAlign(){
	var val = ADAG('#parametersalign').val();
	ADAG('#textlink').css('text-align',val);
}

function setImageAlign(ia){
	var image2 = ADAG('#imgdiv2 #tlink2 img');
	if((image2.get().length>0)&&(image2.prop('src') != 'images/blank.png')) {
		//var ia = ADAG('#parametersia').val();
		var ia = ia || null;
		if(ia == 'l') {
			image2.css({'float':'left', 'padding':'5px'});
		} else if (ia == 'r') {
			image2.css({'float':'right', 'padding':'5px'});
		} else if(ia == 't') {
			//image2.removeAttr('style');
			image2.css({ 'float':'none' });
		}
	}
	setImageWrap();
}

function setImageWrap(ia,wrap_img){
	var text = ADAG('#tbody');
	var ia = ia || null;
	var wrap_img = wrap_img || null;

	if((wrap_img == '0')&&(ADAG('#rt_image').width() != null)){
		//var ia = ADAG('#parametersia').val();
		var size = ADAG('#rt_image').width() + 10; // 5*2 px for padding
		switch(ia){
			case 't':
				text.css({'margin-left':'','margin-right':''});
				break;
			case 'l':
				text.css({'margin-left':size+'px', 'margin-right':''});
				break;
			case 'r':
				text.css({'margin-right':size+'px', 'margin-left':''});
				break;
		}
	} else if( wrap_img == '1') { //ADAG('#parameterswrap_img').val()
		text.css({'margin-left':'','margin-right':''});
	}
}

function updateWH(mxtype, mxsize){
	var mxsize = mxsize || null;
	var mxtype = mxtype || null;
	//var size = ADAG('#mxsize').val()+'px';
	var size = mxsize + 'px';
	if(mxtype == 'w'){//ADAG('#mxtype').val()
		var attrbt = 'width';
		var attrbt2 = 'height';
	} else {
		var attrbt = 'height';
		var attrbt2 = 'width';
	}
	ADAG('#rt_image').css(attrbt2,'auto').css(attrbt,size);
}

function changeSize(width,height){
	//if (ADAG("#parameterssizeparam").val() == 1) { param = "%";} else { param = "px";}
	ADAG("#textlink").css('width', width + 'px');//ADAG("#thwidth").val()+param
	ADAG("#textlink").css('height',height + 'px');//ADAG("#thheight").val()+param
}

function setOverflow(){
	ADAG("#textlink").css('overflow','hidden');
}

function changeLinkTitle(){
	ADAG("#ttitle").html(ADAG("#clinktitle").val());
	changeFontTitle();
	changeFSTitle();
	changeFWTitle();
	updateColors2();
}

function changeBody(){
	ADAG("#tbody").html(ADAG("#linktext").val());
	changeFontBody();
	changeFSBody();
	changeFWBody();
	updateColors2();
}

function changeAction(){
	ADAG("#taction").html(ADAG("#clinkaction").val());
	changeFontAction();
	changeFSAction();
	changeFWAction();
	updateColors2();
}

function changeFontTitle(){
	ADAG("#ttitle").css('fontFamily',ADAG("#parametersfont_family").val());
}

function changeFontBody(){
	ADAG("#tbody").css('fontFamily',ADAG("#parametersfont_family_b").val());
}

function changeFontAction(){
	ADAG("#taction").css('fontFamily',ADAG("#parametersfont_family_a").val());
}

function changeFSTitle(){
	ADAG("#ttitle").css('fontSize',ADAG("#parametersfont_size").val()+"px");
}

function changeFSBody(){
	ADAG("#tbody").css('fontSize',ADAG("#parametersfont_size_b").val()+"px");
}

function changeFSAction(){
	ADAG("#taction").css('fontSize',ADAG("parametersfont_size_a").val()+"px");
}

function changeFWTitle(){
	var x=ADAG("#parametersfont_weight").val();
	if(x.indexOf("underlined")!=-1){
		var y=x.split(" ");
		ADAG("#ttitle").css('fontWeight',y[0]);
		ADAG("#ttitle").css('textDecoration','underline');
	} else {
		ADAG("#ttitle").css('textDecoration','');
		ADAG("#ttitle").css('fontWeight',ADAG("#parametersfont_weight").val());
	}
}

function changeFWBody(){
	var x=ADAG("#parametersfont_weight_b").val();
	if(x.indexOf("underlined")!=-1){
		var y=x.split(" ");
		ADAG("#tbody").css('fontWeight',y[0]);
		ADAG("#tbody").css('textDecoration','underline');
	} else {
		ADAG("#tbody").css('textDecoration','');
		ADAG("#tbody").css('fontWeight',ADAG("#parametersfont_weight_b").val());
	}
}

function changeFWAction(){
	var x=ADAG("#parametersfont_weight_a").val();
	if(x.indexOf("underlined")!=-1){
		var y=x.split(" ");
		ADAG("#taction").css('fontWeight',y[0]);
		ADAG("#taction").css('textDecoration', 'underline');
	} else {
		ADAG("#taction").css('textDecoration','');
		ADAG("#taction").css('fontWeight',ADAG("#parametersfont_weight_a").val());
	}
}

function changeTcolor(){
	var Tcolor = "#"+ADAG("#pick_title_colorfield").val();
	ADAG("#ttitle").css('color',Tcolor);
}

function changeBcolor(){
	var Bcolor = "#"+ADAG("#pick_body_colorfield").val();
	ADAG("#tbody").css('color',Bcolor);
}

function changeAcolor(){
	var Acolor = "#"+ADAG("#pick_action_colorfield").val();
	ADAG("#taction").css('color',Acolor);
}

function changeBCcolor(){
	var BCcolor = "#"+ADAG("#pick_colorfield").val();
	ADAG("#textlink").css('borderColor',BCcolor);
}

function changeBKcolor(){
	var BKcolor = "#"+ADAG("#pick_bg_colorfield").val();
	ADAG("#textlink").css('backgroundColor',BKcolor);
}

function changePadding(){
	ADAG("#textlink").css('padding',ADAG("#parameterspadding").val()+"px");
}
function changeBorder(){
	ADAG("#textlink").css('border',ADAG("#parametersborder").val()+"px solid");
	changeBCcolor();
}

function updateColors2(){
	changeTcolor();
	changeBcolor();
	changeAcolor();
	changeBCcolor();
	changeBKcolor();
}

function callZoneSettings(){
	var id = ADAG('#zoneId').val();
	if(id != 0){
		try{
			objectus = unserialize(ADAG('#z'+id).val());
			setImageAlign(objectus.ia);
			updateWH(objectus.mxtype, objectus.mxsize);
			changeSize(objectus.width,objectus.height);
			setImageWrap(objectus.ia,objectus.wrap_img);
		}
		catch(err){

		}
	} else {
		// revert to original
	}
}

function changeDisplayImage(){
	return true;
}

function getSelectedValue2( frmName, srcListName ) {
	var form = eval( 'document.' + frmName );
	var srcList = form[srcListName];

	i = srcList.selectedIndex;
	if (i != null && i > -1) {
		return srcList.options[i].value;
	} else {
		return null;
	}
}

function timeToStamp(string_date){
	var form = document.adminForm;
	var time_format = form["time_format"].value;
	myDate = string_date.split(" ");
	myDate = myDate[0].split("-");
	
	if(myDate instanceof Array){
	}
	else{
		myDate = myDate[0].split("/");
	}
	var newDate = '';
	
	switch (time_format){
		case "0" :
			newDate = myDate[1]+"/"+myDate[2]+"/"+myDate[0];
			break;
		case "1" :
			newDate = myDate[0]+"/"+myDate[1]+"/"+myDate[2];
			break;
		case "2" :
			newDate = myDate[1]+"/"+myDate[0]+"/"+myDate[2];
			break;
		case "3" :
			newDate = myDate[1]+"/"+myDate[2]+"/"+myDate[0];
			break;
		case "4" :
			newDate = myDate[0]+"/"+myDate[1]+"/"+myDate[2];
			break;
		case "5" :
			newDate = myDate[1]+"/"+myDate[0]+"/"+myDate[2];
			break;
	}
	
	return newDate;
}

Joomla.submitbutton = function (pressbutton) {
	var form = document.adminForm;
	// the description
	document.adminForm['parameters[alt_text]'].value = document.adminForm['linktext'].value;
	// the title
	document.adminForm['parameters[alt_text_t]'].value = document.adminForm['linktitle'].value;
	document.adminForm['parameters[alt_text_a]'].value = document.adminForm['linkaction'].value;
	if ((pressbutton=='save')||(pressbutton=='apply')) {
	if (form['title'].value == "") {
	alert( "<?php echo JText::_("JS_INSERT_TITLE");?>" );
	} else if (getSelectedValue2('adminForm','advertiser_id') < 1) {
	alert( "<?php echo JText::_("JS_SELECT_ADV");?>" );
	}/* else if (getSelectedValue2('adminForm','zone') < 1) {
	alert( "<?php echo JText::_("JS_SELECT_ZONE");?>" );
	}*/else if (form['target_url'].value == "" || form['target_url'].value == "http://") {
	alert( "<?php echo JText::_("JS_TARGETURL") ?>" );
	} else if (form['linktitle'].value == "") {
	alert( "<?php echo JText::_("JS_FILL_HTMLTITLE"); ?>" );
	} else if(checkZones() == false){
		alert("<?php echo JText::_("ADAG_CHOOSE_ZONE_FOR_CAMP"); ?>");
	}/*else if (!(form['width'].value>0)) {
	alert( "<?php echo JText::_("JS_BANNER_WIDTH") ?>" );
	} else if (!(form['height'].value>0)) {
	alert( "<?php echo JText::_("JS_BANNER_HEIGHT"); ?>" );
	}*/ else {
	
			if(form['ad_end_date'].value != "Never" && form['ad_end_date'].value != ""){
				start_date = form['ad_start_date'].value;
				end_date = form['ad_end_date'].value;
				
				start_date = new Date(timeToStamp(start_date)).getTime();
				end_date = new Date(timeToStamp(end_date)).getTime();
				
				if(Date.parse(start_date) > Date.parse(end_date)){
					alert("<?php echo JText::_("ADAG_FINISH_DATE_AND_START_DATE"); ?>");
					return false;
				}
			}
	
	<?php
	if(isset($_GET['cid'][0])&&(intval($_GET['cid'][0])>0)) {
	?>
	if((document.getElementById("approved").value != 'P')&&(document.getElementById("initvalcamp").value != document.getElementById("approved").value)) {
	if(document.getElementById("approved").value == 'Y') {
	var question = "<?php echo JText::_('ADAG_QUESTBANY');?>";
	} else if(document.getElementById("approved").value == 'N') {
	var question = "<?php echo JText::_('ADAG_QUESTBANN');?>";
	}

	var answer = confirm(question);
	if (answer) { } else { document.getElementById("sendmail").value = 0; }
	}
	<?php
	}
	?>
	<?php if(!isset($configs->geoparams['allowgeo'])&&!isset($configs->geoparams['allowgeoexisting'])) { ?>
	submitform( pressbutton );
	<?php } else {?>
	if((ADAG('#geo_type1').prop('checked') == true)&&(!checkChannel(true))) { return false;}
	sanitizeAndSubmit(pressbutton);
	<?php } ?>
	}
	}
	else
	submitform( pressbutton );
}
</script>
