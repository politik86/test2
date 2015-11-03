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

<script type="text/javascript">
	ADAG(function(){
		ADAG('.any_size_check').click(function(){
			var the_parent = ADAG(this).parent();
			if(ADAG(this).prop('checked') == true) {
				the_parent.find('.adsize_container input').prop({'disabled':'true','readonly':'true'}).val('');
			} else {
				the_parent.find('.adsize_container input').removeProp('disabled').removeProp('readonly');
			}
		});

		ADAG('#upgrade_button').click(function(){
			ADAG('.adsize_container').not(':visible').remove();
			Joomla.submitbutton('upgradezone');
		}).find('td').css('border','none').end().prev().find('td').css('border','none');

		ADAG("input[name*=bx]").click(function(){
			ADAG(this).parent().css('border','none');
			var banners_td = ADAG(this).parent('.sup_banners').parent('td');
			if(ADAG(this).val() == '1'){
				banners_td.find('.textad2').prop('checked','');
				banners_td.next().find('.adsize_container').css('display','');
				banners_td.find('.cSpecial input').prop('checked','');
				banners_td.find('.cBanners input').prop('checked','true');
			} else if(ADAG(this).val() == '2'){
				banners_td.find('.textad2').prop('checked','true');
				banners_td.next().find('.adsize_container').css('display','');
				banners_td.find('.cSpecial input').prop('checked','');
				banners_td.find('.cBanners input').prop('checked','');
			} else if(ADAG(this).val() == '3'){
				banners_td.find('.textad2').prop('checked','');
				banners_td.next().find('.adsize_container').css('display','none');
				banners_td.find('.cSpecial input').prop('checked','true');
				banners_td.find('.cBanners input').prop('checked','');
			}
		});

		ADAG(".cBanners input").click(function(){
			ADAG(this).parent().parent().css('border','none');
			var sup_banners = ADAG(this).parent().parent();
			sup_banners.find('.textad2').prop('checked','');
			if(ADAG(this).parent().find('input').filter('[checked=true]').length == 0){
				sup_banners.parent().next().find('.adsize_container').css('display','none');
				sup_banners.find('input:first').prop('checked','');
			} else {
				sup_banners.find('input:first').prop('checked','true');
				sup_banners.parent().find(".cSpecial input").prop('checked','');
				sup_banners.parent().next().find('.adsize_container').css('display','');
			}
		});

		ADAG(".cSpecial input").click(function(){
			ADAG(this).parent().parent().css('border','none');
			var sup_banners = ADAG(this).parent().parent();
			sup_banners.find('.textad2').prop('checked','');
			sup_banners.parent().next().find('.adsize_container').css('display','none');
			if(ADAG(this).parent().find('input').filter('[checked=true]').length == 0){
				sup_banners.find('input[value=3]').prop('checked','');
			} else {
				sup_banners.find('input[value=3]').prop('checked','true');
				sup_banners.find('.cBanners input').prop('checked','');
			}
		});

	});

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

	function performChecks(){

		// Check to see if every zone has at least one type of banner assigned
		var okSupporter = true;
		ADAG('.sup_banners').each(function(){
			if(typeof(ADAG(this).find('[name*=bx]').filter(':checked').val())== 'undefined'){
				ADAG(this).css('border','2px solid #FF0000');
				okSupporter = false;
			}
		});

		if(!okSupporter) {
			alert('Please select the supported banners for all the zones!');
			return false;
		} else {
			ADAG('.sup_banners').css('border','none');
		}
		// end check banner types -----------


		// Check to see if there is invalid width & height somewhere
		/*okSize = true;
		ADAG('.adsize_container:visible input').each(function(){
			if((!IsNumeric(ADAG(this).val())) || (ADAG(this).val()=='')){
				okSize = false;
			}
		});

		if(!okSize) { alert('Please enter valid sizes for zones!'); return false; }*/
		// end check sizes -----------

		return true;
	}

    Joomla.submitbutton = function (pressbutton) {
		var ok = performChecks();
		if(ok){
			submitform(pressbutton);
		} else {
			return false;
		}
	}

</script>
