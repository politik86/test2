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
			ADAG('#aid').find('option').remove().end().append(toAppend);
		}
	});
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

ADAG(function(){
    document.getElementById("initvalcamp").value = document.getElementById("approved").value;
    ADAG('.w145').change(function(){
        if(ADAG(this).val()) {
            ADAG(this).parent().parent().find('.add_column input').prop('checked','true');
        }
    });
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

function limitAds() {
    return !!(ADAG('.add_column').find(':checked').length <= <?php echo $adslim; ?> );
}

function checkZones(){
    var ok = true;
    ADAG('#banner_table tr:gt(0)').each(function(index){
        if((ADAG(this).find('.add_column input:checked').length>0)&&(ADAG(this).find('.w145[value=]').length>0)){
            ok = false;
        }
    });
    return ok;
}

function getTimestamp(str) {
  var d = str.match(/\d+/g); // extract date parts
  return +new Date(d[0], d[1] - 1, d[2], d[3], d[4], d[5]); // build Date object
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
    if (pressbutton=='save') {
        if (document.getElementById('aid').value < 1) {
                alert( "<?php echo JText::_("JS_SELECT_ADV");?>" );
            } else if (form['name'].value == "") {
                alert( "<?php echo JText::_("JS_INSERT_CMPNAME");?>" );
            <?php if ($camp_row->id<1) { ?>
            } else if (getSelectedValue2('adminForm','otid') < 1) {
                alert( "<?php echo JText::_("JS_SELECT_PACKAGE");?>" );
            <?php } ?>
            } else if (checkZones() == false){
                alert( "<?php echo JText::_("ADAG_CHOOSE_ZONE_EACH_AD"); ?>" );
            } else if (!limitAds()) {
                alert( "<?php echo JText::_("ADS_LIM_WARN"); ?>" );
            } else {
				if(eval(form['validity'])){
					if(form['validity'].value != "Never" && form['validity'].value != ""){
						start_date = form['start_date'].value;
						end_date = form['validity'].value;

						start_date = new Date(timeToStamp(start_date)).getTime();
						end_date = new Date(timeToStamp(end_date)).getTime();
						
						if(Date.parse(start_date) > Date.parse(end_date)){
							alert("<?php echo JText::_("ADAG_FINISH_DATE_AND_START_DATE"); ?>");
							return false;
						}
					}
				}
			
                <?php
                    if(isset($_GET['cid'][0])&&(intval($_GET['cid'][0])>0)) {
                ?>
                    if((document.getElementById("approved").value != 'P')&&(document.getElementById("initvalcamp").value != document.getElementById("approved").value)) {
                        if(document.getElementById("approved").value == 'Y') {
                            var question = "<?php echo JText::_('ADAG_QUESTCAMPY');?>";
                        } else if(document.getElementById("approved").value == 'N') {
                            var question = "<?php echo JText::_('ADAG_QUESTCAMPN');?>";
                        }

                        var answer = confirm(question);
                        if (answer) { } else { document.getElementById("sendmail").value = 0; }
                    }
                <?php
                    }
                ?>
                submitform( pressbutton );
            }
    } else {
        submitform( pressbutton );
    }
}

</script>
