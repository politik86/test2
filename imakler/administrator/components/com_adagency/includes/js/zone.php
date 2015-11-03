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

ADAG(function(){
    ADAG('#adwidth').keyup(function(){
        ADAG('#fixedsize').prop('checked','checked');
    });
    ADAG('#adheight').keyup(function(){
        ADAG('#fixedsize').prop('checked','checked');
    });
    ADAG("input[name=bx]").click(function(){
        //alert(ADAG(this).val());
        if(ADAG(this).val() == '1'){
            ADAG('#adsize').css('display','');
            ADAG('#cSpecial input').prop('checked','');
            ADAG('#cBanners input').prop('checked','true');
            ADAG('#textad').hide();
        } else if(ADAG(this).val() == '2'){
            ADAG('#adsize').css('display','none');
            ADAG('#cSpecial input').prop('checked','');
            ADAG('#cBanners input').prop('checked','');
            ADAG('#textad').show();
        } else if(ADAG(this).val() == '3'){
            ADAG('#adsize').css('display','none');
            ADAG('#cBanners input').prop('checked','');
            ADAG('#cSpecial input').prop('checked','true');
            ADAG('#textad').hide();
        }
    });
    
	ADAG("#cBanners input").click(function(){
        ADAG('#textad').hide();
        if(ADAG(this).parent().find('input').filter('[checked=true]').length == 0){
            //ADAG('input[name=bx]').filter('[value=1]').prop('checked','');
           	//ADAG('#adsize').css('display','none');
        } else {
            //ADAG('input[name=bx]').filter('[value=1]').prop('checked','true');
            ADAG("#cSpecial input").prop('checked','');
            ADAG('#adsize').css('display','');
        }
    });
    
	ADAG("#cSpecial input").click(function(){
        ADAG('#textad').hide();
        if(ADAG(this).parent().find('input').filter('[checked=true]').length == 0){
            //ADAG('input[name=bx]').filter('[value=3]').prop('checked','');
            ADAG('#adsize').css('display','none');
        } else {
            ADAG('#adsize').css('display','none');
            //ADAG('input[name=bx]').filter('[value=3]').prop('checked','true');
            ADAG("#cBanners input").prop('checked','');
        }
    });
});

<?php
    if($this->isLock) {
    // if the zone has ads assigned to campaigns
    // don't let the use change the ad type and size settings
?>
    ADAG(function () {
        var $suppInputRadio = ADAG('#supported_banners input[type=radio]');
        var $suppInputCheckbox = ADAG('#supported_banners input[type=checkbox]');
        var $adsizInputRadio = ADAG('#adsize input[type=radio]');
        var $adsizeInputCheckbox =  ADAG('#adsize input[type=checkbox]');
        var $elems = [$suppInputRadio, $suppInputCheckbox, $adsizInputRadio, $adsizeInputCheckbox];

        ADAG('#adag-embed').click(function(){
            ADAG.each($elems, function(i,elem){
                elem.prop({'disabled':'disabled'});
            });
        });

        ADAG('.modal2').openDOMWindow({
            height: 450,
            width: 800,
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

    });
<?php
    }
?>
function removeDisabledOnSubmit(){
    var $suppInputRadio = ADAG('#supported_banners input[type=radio]');
    var $suppInputCheckbox = ADAG('#supported_banners input[type=checkbox]');
    var $adsizInputRadio = ADAG('#adsize input[type=radio]');
    var $adsizeInputCheckbox =  ADAG('#adsize input[type=checkbox]');
    var $elems = [$suppInputRadio, $suppInputCheckbox, $adsizInputRadio, $adsizeInputCheckbox];

    ADAG.each($elems, function(i,elem){
        elem.css('opacity','0.3').removeProp('disabled');
    });
}

function IsNumeric(sText){
    var ValidChars = "0123456789";
    var IsNumber=true;
    var Char;
    for (i = 0; i < sText.length && IsNumber == true; i++) {
        Char = sText.charAt(i);
        if (ValidChars.indexOf(Char) == -1)  { IsNumber = false; }
    }
    return IsNumber;
}

function checkSupported(){
    if(typeof(ADAG('[name=bx]').filter(':checked').val())=='undefined'){
        alert('Please choose at least one banner type!');
        return false;
    }
    if((ADAG('#adsize').is(':visible'))&&(!ADAG('#anysize').is(':checked'))){
      var width = ADAG('#adwidth').val();
      var height = ADAG('#adheight').val();
		if((width == '')||(!IsNumeric(width))||(width<=0)){
			alert('<?php echo JText::_('JS_BANNER_WIDTH'); ?>');
        	return false;
		}
		else if((height == '')||(!IsNumeric(height))||(height<=0)){
			if((width == '')||(!IsNumeric(width))||(width<=0)) {
				alert('<?php echo JText::_('JS_BANNER_HEIGHT'); ?>');
				return false;
			}
			else{
				ADAG('#adheight').val(width);
			}
		}
    }
    return true;
}

function remWidthHeight(){
    if(ADAG('#anysize').is(':checked')){
        ADAG('#adwidth').val('');
        ADAG('#adheight').val('');
    }
}

Joomla.submitbutton = function (pressbutton) {
    var form = document.adminForm;

	document.getElementById("position").value = document.getElementById("fakeposition").value;

    if ((pressbutton=='save')||(pressbutton=='apply')) {
        if (form['title'].value == "") {
            alert( "<?php echo JText::_("JS_INSERT_ZONETITLE");?>" );
        } else if (!IsNumeric(document.getElementById('rotate_time').value)||(document.getElementById('rotate_time').value<10000)) {
            alert("<?php echo JText::_('ADAG_ALERT_ROTATING_TIME');?>");
            return false;
        } else {
            if(form['link_taketo'].value == 2){
				if(form['taketo_url'].value == 'http://' || form['taketo_url'].value == 'https://'){
					alert("<?php echo JText::_("ADAG_ADD_LINK_SHOULD_TAKE_TO_URL"); ?>");
					return false;
				}
			}
			
			if(checkSupported() == true){
                if(ADAG('#textad').css('display') == 'none') {
                    ADAG('#textad').remove();
                }
                remWidthHeight();
                removeDisabledOnSubmit();
                submitform( pressbutton );
            }
        }
    }
    else {
		removeDisabledOnSubmit();
        submitform( pressbutton );
    }
}

function show_hide_url(selectval){
    if(selectval == 2)
        {
            document.getElementById('taketo_url').style.display = '';
        }
    else
        {
            document.getElementById('taketo_url').style.display = 'none';
            if(document.getElementById('taketo_url').value == '' || document.getElementById('taketo_url').value == 'http://')
                document.getElementById('taketo_url').value = 'http://';
        }

}

var originalOrder = '<?php echo $modul->ordering;?>';
var originalPos = '<?php echo $modul->position;?>';
var orders = new Array();	// array in the format [key,value,text]
<?php	$i = 0;
foreach ($orders2 as $k=>$items) {
    foreach ($items as $v) {
        echo "\n	orders[".$i++."] = new Array( \"$k\",\"$v->value\",\"$v->text\" );";
    }
}
?>

</script>

<!-- <script type="text/javascript"> -->
<!--  -->
<!-- window.addEvent('domready', function(){ var JTooltips = new Tips($$('.hasTip'), { maxTitleChars: 50, fixed: false}); }); -->
<!-- window.addEvent('domready', function() { -->
<!--  -->
<!--     SqueezeBox.initialize({}); -->
<!--  -->
<!--     $$('a.modal').each(function(el) { -->
<!--         el.addEvent('click', function(e) { -->
<!--             new Event(e).stop(); -->
<!--             SqueezeBox.fromElement(el); -->
<!--         }); -->
<!--     }); -->
<!-- }); -->
<!--  -->
<!-- window.addEvent('domready', function() { -->
<!-- $('fakeposition').addEvent('change', function() { -->
<!--     changeDynaList('ordering', orders, document.adminForm.position.value, 0, 0); -->
<!--     }); -->
<!--     $('position').addEvent('change', function() { -->
<!--     changeDynaList('ordering', orders, document.adminForm.position.value, 0, 0); -->
<!--     }); -->
<!-- }); -->
<!--  -->
<!-- </script> -->
