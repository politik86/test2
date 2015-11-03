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

Joomla.submitbutton = function (pressbutton) {
<?php
    echo $editor->save( 'bodyafterreg' );
    echo $editor->save( 'bodyactivation' );
    echo $editor->save( 'bodyrep' );
    echo $editor->save( 'bodycmpappv' );
    echo $editor->save( 'bodycmpdis' );
    echo $editor->save( 'bodyadappv' );
    echo $editor->save( 'bodyaddisap' );
    echo $editor->save( 'bodyadvdis' );
    echo $editor->save( 'bodynewad' );
    echo $editor->save( 'bodynewcmp' );
    echo $editor->save( 'bodycmpex' );
    echo $editor->save( 'bodynewuser' );
    echo $editor->save( 'overviewcontent' );
?>
    ADAG('#tab').prop('value',ADAG('dt.open').prop('id'));
    
<?php
	$task2 = JRequest::getVar("task2", "");
	if(trim($task2) == "general"){
?>
		if(document.adminForm.maxchars.value <= 0){
			alert("<?php echo addslashes(JText::_("ADAG_TEXT_ADD_SIZE_MINUS")); ?>");
			return false;
		}
<?php		
	}
?>
	
	submitform( pressbutton );
}

function buz(i){
	if(i == ""){
		document.getElementById("variables").style.display = 'none';
	}
	else{
		document.getElementById("variables").style.display = '';
	}
	
    for(j=1; j<=16; j++){
        document.getElementById("emset"+j).style.display='none';
    }
	
    if(i>0){
		document.getElementById("emset"+i).style.display='';
	}
    vbls(i);
	
	/* edit/hide select option to send email or not ------------------------------------ */
	document.getElementById('th-send-email').style.display = '';
	for(k=1; k<=15; k++){
		document.getElementById('send_'+k).style.display = 'none';
	}
	
	showem = document.getElementById("showem").value;
	if(showem == 2){
		document.getElementById('send_1').style.display='';
	}
	else if(showem == 14){
		document.getElementById('send_2').style.display='';
	}
	else if(showem == 3){
		document.getElementById('send_3').style.display='';
	}
	else if(showem == 4){
		document.getElementById('send_4').style.display='';
	}
	else if(showem == 5){
		document.getElementById('send_5').style.display='';
	}
	else if(showem == 6){
		document.getElementById('send_6').style.display='';
	}
	else if(showem == 7){
		document.getElementById('send_7').style.display='';
	}
	else if(showem == 8){
		document.getElementById('send_8').style.display='';
	}
	else if(showem == 9){
		document.getElementById('send_9').style.display='';
	}
	else if(showem == 11){
		document.getElementById('send_10').style.display='';
	}
	else if(showem == 12){
		document.getElementById('send_11').style.display='';
	}
	else if(showem == 10){
		document.getElementById('send_12').style.display='';
	}
	else if(showem == 13){
		document.getElementById('send_13').style.display='';
	}
	else if(showem == 15){
		document.getElementById('send_14').style.display='';
	}
	else if(showem == 16){
		document.getElementById('send_15').style.display='';
	}
	else{
		document.getElementById('th-send-email').style.display = 'none';
	}
	/* edit/hide select option to send email or not ------------------------------------ */
}

function selafter(x,y){
    document.getElementById("aftercamp"+x).checked = false;
    document.getElementById("aftercamp"+y).checked = false;
}

function vbls(i){
    for(j=0;j<=28;j++){
		document.getElementById("vbl"+j).style.display='none';
    }
	
    if(i>0){
        document.getElementById("vbl0").style.display='';
        if((i==1)||(i==14)){
            document.getElementById('vbl1').style.display='';
            document.getElementById('vbl2').style.display='';
            document.getElementById('vbl3').style.display='';
        } else if(i==2) {
            document.getElementById('vbl1').style.display='';
            document.getElementById('vbl2').style.display='';
        } else if(i==3) {
            document.getElementById('vbl1').style.display='';
            document.getElementById('vbl2').style.display='';
            document.getElementById('vbl3').style.display='';
            document.getElementById('vbl5').style.display='';
        } else if(i==4) {
            document.getElementById('vbl1').style.display='';
            document.getElementById('vbl6').style.display='';
            document.getElementById('vbl8').style.display='';
            document.getElementById('vbl9').style.display='';
            document.getElementById('vbl10').style.display='';
        } else if((i==5)||(i==6)) {
            document.getElementById('vbl1').style.display='';
            document.getElementById('vbl6').style.display='';
        } else if((i==7)||(i==8)) {
            document.getElementById('vbl1').style.display='';
            document.getElementById('vbl7').style.display='';
        } else if((i==10) || (i==16)){
            document.getElementById('vbl1').style.display='';
            document.getElementById('vbl7').style.display='';
            document.getElementById('vbl11').style.display='';
            document.getElementById('vbl12').style.display='';
            document.getElementById('vbl13').style.display='';
            document.getElementById('vbl14').style.display='';
            document.getElementById('vbl15').style.display='';
            document.getElementById('vbl19').style.display='';
        } else if(i==11){
            document.getElementById('vbl1').style.display='';
            document.getElementById('vbl4').style.display='';
            document.getElementById('vbl6').style.display='';
            document.getElementById('vbl11').style.display='';
            document.getElementById('vbl14').style.display='';
            document.getElementById('vbl15').style.display='';
            document.getElementById('vbl16').style.display='';
            document.getElementById('vbl17').style.display='';
            document.getElementById('vbl18').style.display='';
        } else if(i==9) {
            document.getElementById('vbl1').style.display='';
        } else if((i==12)||(i==15)) {
            document.getElementById('vbl1').style.display='';
            document.getElementById('vbl11').style.display='';
            document.getElementById('vbl6').style.display='';
            document.getElementById('vbl21').style.display='';
            document.getElementById('vbl31').style.display='';
            document.getElementById('vbl32').style.display='';
            document.getElementById('vbl33').style.display='';
        } else if(i==13){
            document.getElementById('vbl1').style.display='';
            document.getElementById('vbl4').style.display='';
            document.getElementById('vbl11').style.display='';
            document.getElementById('vbl15').style.display='';
            document.getElementById('vbl20').style.display='';
            document.getElementById('vbl21').style.display='';
            document.getElementById('vbl22').style.display='';
            document.getElementById('vbl23').style.display='';
            document.getElementById('vbl24').style.display='';
            document.getElementById('vbl25').style.display='';
            document.getElementById('vbl26').style.display='';
            document.getElementById('vbl27').style.display='';
            document.getElementById('vbl28').style.display='';
        }
    }
}

</script>
