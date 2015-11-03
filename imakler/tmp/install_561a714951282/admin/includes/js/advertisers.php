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
		<!--
		var request_processed = 0;

        function populateShipping () {
        	var names = Array ('address','zipcode', 'city');
        	var i;
        	for (i = 0; i < names.length; i++) {
        		val = document.getElementById(names[i]).value;
        		document.getElementById('ship' + names[i]).value = val;
        	}
        	idx = document.getElementById('country').selectedIndex;
        	document.getElementById('shipcountry').selectedIndex = idx;

        	changeProvince_ship();
        	request_processed = 1;
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
        Joomla.submitbutton = function (pressbutton) {
			var form = document.adminForm;
			var regexp = /[\w\.\-]+@\w+[\w\.\-]*?\.\w{1,4}/
			<?php //if($advertiser->aid<1) {}?>
			if ((pressbutton=='save')||(pressbutton=='save_graybox')) {
				if (form['name'].value == "") {
					alert( "<?php echo JText::_("JS_INSERT_NAME");?>" );
					return false;
				}
			    if (!regexp.test(form['email'].value)) {
					alert( "<?php echo JText::_("JS_INSERT_VALIDMAIL");?>" );
					return false;
				}
                <?php if ( isset($configs->show) && (in_array('phone',$configs->show)) &&
                        isset($configs->mandatory) && (in_array('phone',$configs->mandatory))) { ?>
				if (form['telephone'].value == "") {
					alert( "<?php echo JText::_("ADAG_JS_INSERT_PHONE");?>" );
					return false;
                }
                <?php } ?>
                <?php if ( isset($configs->show) && (in_array('url',$configs->show)) &&
                        isset($configs->mandatory) && (in_array('url',$configs->mandatory))) { ?>
				if ((form['website'].value == "")||(form['website'].value == "http://")) {
					alert( "<?php echo JText::_("ADAG_JS_INSERT_WEB");?>" );
					return false;
				}
                <?php } ?>
				if (form['username'].value == "") {
					alert( "<?php echo JText::_("JS_INSERT_USERNAME");?>" );
					return false;
				}
				<?php if ($advertiser->aid < 1) { ?>
				if (form['password'].value == "") {
					alert( "<?php echo JText::_("JS_INSERT_PASS");?>" );
					return false;
				}
				if (form['password'].value != form['password2'].value) {
						alert( "<?php echo JText::_("ADAG_JS_MATCH_PASS");?>" );
						return false;
				}
				<?php } ?>

				<?php // Checking for company fields if mandatory

				if(isset($configs->show)&&(in_array('company',$configs->show))&&isset($configs->mandatory)&&(in_array('company',$configs->mandatory))) {?>
				if (form['company'].value == "") {
					alert( "<?php echo JText::_("JS_INSERT_COMPANY");?>" );
					return false;
				}
				if (form['description'].value == "") {
					alert( "<?php echo JText::_("ADAG_JS_CMP_DSC");?>" );
					return false;
				}
				<?php } // End checking for company fields ?>

				<?php // Checking for address fields if mandatory

				if(isset($configs->show)&&(in_array('address',$configs->show))&&isset($configs->mandatory)&&(in_array('address',$configs->mandatory))) {?>
				if (form['address'].value == "") {
					alert( "<?php echo JText::_("ADAG_JS_ADR");?>" );
					return false;
				}
				if (getSelectedValue2('adminForm','country') < 1) {
					alert( "<?php echo JText::_("JS_SELECT_COUNTRY");?>" );
					return false;
				}
				if (form['zip'].value == "") {
					alert( "<?php echo JText::_("ADAG_JS_ZIP");?>" );
					return false;
				}
				<?php } // End checking for address ?>

						<?php
							if(isset($_GET['cid'][0])&&(intval($_GET['cid'][0])>0)) {
						?>
							if((document.getElementById("approved").value != 'P')&&(document.getElementById("initvalcamp").value != document.getElementById("approved").value)) {
								if(document.getElementById("approved").value == 'Y') {
									var question = "<?php echo JText::_('ADAG_QUESTADVY');?>";
								} else if(document.getElementById("approved").value == 'N') {
									var question = "<?php echo JText::_('ADAG_QUESTADVN');?>";
								}

								var answer = confirm(question);
								if (answer) { } else { document.getElementById("sendmail").value = 0; }
							}
						<?php
							}
						?>

				submitform( pressbutton );
			}
			else
				submitform( pressbutton );
		}
		-->
		window.onload = function(){
			document.getElementById("initvalcamp").value = document.getElementById("approved").value;
		};
		</script>
