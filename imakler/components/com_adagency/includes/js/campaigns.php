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

		function checkZones(){
			var ok = true;
			ADAG('#banner_table tr:gt(0)').each(function(index){
				if((ADAG(this).find('.add_column input:checked').length>0)&&(ADAG(this).find('.w145[value=]').length>0)){
					ok = false;
				}
			});
			return ok;
		}

		ADAG(function(){

            /*ADAG('.modal').openDOMWindow({
                height: 450,
                width: 800,
                positionTop: 50,
                eventType: 'click',
                positionLeft: 50,
                windowSource: 'iframe',
                windowPadding: 0,
                loader: 1,
                loaderImagePath: '<?php //echo JURI::root()."components/com_adagency/images/loading.gif"; ?>',
                loaderHeight: 31,
                loaderWidth: 31
            });*/

			ADAG('.w145').change(function(){
   				if(ADAG(this).val()) {
    				ADAG(this).parent().parent().find('.add_column input').prop('checked','true');
			    }
			});

			if ("<?php echo JRequest::getVar('task','','post');	?>" == "refresh") {
				setTimeout("window.location.hash = 'refreshpackage'", 500);
			}

            ADAG('#banner_table input').click(function(event) {
                if (ADAG('#banner_table input:checked').length > '<?php echo $adslim; ?>') {
                    alert('<?php echo JText::sprintf('ADAG_CMP_LIMIT_AD_WARN', $adslim); ?>');
                    event.preventDefault();
                }
            });

		});


        function limitAds() {
            <?php 
				if(trim($adslim) == "-"){
					$adslim = 0;
				}
			?>
			
			return !!(ADAG('.add_column').find(':checked').length <= '<?php echo $adslim; ?>' );
        }
		
		function trimString(text){
			if(typeof(text) !== "undefined"){
				for(var i=0; i<text.length; i++){
					if(text[0] == " "){						
						text = text.substr(1);
					}
					else{
						break;
					}
				}
				for(var i=text.length-1; i>0; i--){
					if(text[i] == " "){
						text = text.substr(0, text.length-1);
					}
					else{
						break;
					}
				}
				if(text == " "){
					return "";
				}					
				return text;
			}
			else{
				return "";
			}
		}

		Joomla.submitbutton = function (pressbutton) {
			var form = document.adminForm;
			if (pressbutton=='save') {
				<?php
				
					if(!isset($get_data['cid'][0])||($get_data['cid'][0]==0)) {
				?>
				ok = false;
				if(typeof(ADAG('#countbids').get(0)) != 'undefined'){
					for(var i=1;i<=document.getElementById('countbids').value;i++){
						if(document.getElementById('bid['+i+']').checked == true) {
							ok = true;
						}
					}
				}
			
                <?php if ($camp_row->id<1) { ?>
                ok = true;
                <?php } ?>                
				if(ok==false) {
					alert('<?php echo JText::_('ADAG_NOCMPAD');?>');
					return true;
				}
				<?php
					}
				?>
				
				// check promo code -----------------------------------------
				continue_script = true;
				if(!form['promocode']){continue_script = true;}
				else if(form['promocode'].value != ""){
					var req = new Request.HTML({
						method: 'get',
						url: 'index.php?option=com_adagency&controller=adagencyCampaigns&task=checkPromoCode&promo='+form['promocode'].value+'&tmpl=component&format=raw&no_html=1',
						async: false,
						onComplete: function(response){
							document.getElementById("ajax-response").empty().adopt(response);
							return_val = document.getElementById("ajax-response").innerHTML;
							return_val = return_val.replace(/^\s*[\r\n\t]/gm, "");
							return_val = return_val.replace(" ", "");
							if(return_val == "invalid"){
								continue_script = false;
								alert("<?php echo JText::_("ADAG_INVALID_PROMO");?>");
								return false;
							}
							else if(return_val == "expired"){
								continue_script = false;
								alert("<?php echo JText::_("ADAG_EXPIRED_PROMO");?>");
								return false;
							}
						}
					}).send();
				}
				
				if(!continue_script){
					return false;
				}
				// check promo code -----------------------------------------
				
				if (form['name'].value == "") {
					alert( "<?php echo JText::_("JS_INSERT_CMPNAME");?>" );
				<?php if ($camp_row->id<1) { ?>
				} else if (getSelectedValue2('adminForm','otid') < 1) {
					alert( "<?php echo JText::_("JS_SELECT_PACKAGE");?>" );
				<?php } ?>
				} else if (eval(form['start_date']) && form['start_date'].value == "") {
					alert( "<?php echo JText::_("JS_INSERT_DATE");?>" );
				} else if (checkZones() == false){
					alert( "<?php echo JText::_("ADAG_CHOOSE_ZONE_EACH_AD"); ?>" );
				} else if (!limitAds()) {
                    alert( "<?php echo JText::_("ADS_LIM_WARN"); ?>" );
                } else {
					/*<?php if(isset($camp_row->id)&&($camp_row->id>0)&&isset($pstatus)&&($pstatus != 'Y')) { ?>
						var answer = confirm('<?php echo JText::_('ADAG_JS_CONFIRM_CAMP'); ?>');
					<?php } else { ?>
						var answer = true;
					<?php }	?>
					if(answer) {
						submitform( pressbutton );
					} else {
						return false;
					}*/
					
					submitform( pressbutton );
				}
			}
			if(pressbutton == 'refresh') { submitform(pressbutton); }
		}
		-->
		</script>
