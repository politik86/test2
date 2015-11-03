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

JHtml::_('behavior.tooltip');
JHtml::_('behavior.framework',true);

$document = JFactory::getDocument();
$document->addStyleSheet(JURI::base()."components/com_adagency/css/joomla16.css");

	$promo = $this->promo;
	$configs = $this->configs;
	$nullDate = 0;
	JHTML::_("behavior.calendar");
	
	$params = $configs->params;
	$params = unserialize($params);
	
	$format_value = $params["timeformat"];
	$format_string = "Y-m-d";
	switch($format_value){
		case "0" : {
			$format_string = "Y-m-d H:i:s";
			break;
		}
		case "1" : {
			$format_string = "m/d/Y H:i:s";
			break;
		}
		case "2" : {
			$format_string = "d-m-Y H:i:s";
			break;
		}
		case "3" : {
			$format_string = "Y-m-d";
			break;
		}
		case "4" : {
			$format_string = "m/d/Y";
			break;
		}
		case "5" : {
			$format_string = "d-m-Y";
			break;
		}
	}
	
	
	$format_string_2 = str_replace ("-", "-%", $format_string);
	$format_string_2 = str_replace ("/", "/%", $format_string_2);
	$format_string_2 = "%".$format_string_2;
	$format_string_2 = str_replace("H:i:s", "%H:%M:%S", $format_string_2);
?>

		<script language="javascript" type="text/javascript">
		<!--

		function timeToStamp(string_date){
			var form = document.adminForm;
			var time_format = "<?php echo $params["timeformat"]; ?>";
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
			if ((pressbutton=='save')||(pressbutton=='apply')) {
				var form = document.adminForm;
				if(form['codestart'].value != "Never" && form['codestart'].value != ""){
					start_date = form['codestart'].value;
					end_date = form['codeend'].value;
					
					start_date = new Date(timeToStamp(start_date)).getTime();
					end_date = new Date(timeToStamp(end_date)).getTime();
					
					if(Date.parse(start_date) > Date.parse(end_date)){
						alert("<?php echo JText::_("ADAG_FINISH_DATE_AND_START_DATE"); ?>");
						return false;
					}
				}
			}
			
			submitform( pressbutton );
		}
		-->
		</script>
        
<form class="form-horizontal" method="post" id="adminForm" name="adminForm" enctype="multipart/form-data">
 <div class="row-fluid">
            <div class="span12 pull-right">
            	<h2 class="pub-page-title">
					<?php echo JText::_('VIEWPROMOPROMOCODESETTINGS'); ?>
				</h2>
            </div>
      </div>

	
	<div class="control-group">
			<label class="control-label"> <?php echo JText::_('VIEWPROMOTITLE'); ?> </label>
			<div class="controls">
				<input type="text" name="title" value="<?php echo $promo->title;?>" />
				<span class="editlinktip hasTip" title="<?php echo JText::_('COM_DIGISTORE_PROMOTITLE_TIP'); ?>" >
				<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
			</div>
	</div>
	
	<div class="control-group">
			<label class="control-label"> <?php echo JText::_('VIEWPROMOCODE'); ?> </label>
			<div class="controls">
				<input type="text" name="code" value="<?php echo $promo->code;?>" />
				<span class="editlinktip hasTip" title="<?php echo JText::_('COM_DIGISTORE_PROMOCODE_TIP'); ?>" >
				<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
			</div>
	</div>
	
	<div class="control-group">
			<label class="control-label"> <?php echo JText::_('VIEWPROMOUSAGELIMIT'); ?> </label>
			<div class="controls">
				<input type="text" name="codelimit" value="<?php echo ($promo->codelimit == 0?'':$promo->codelimit);?>" /> 
				<span class="editlinktip hasTip" title="<?php echo JText::_('COM_DIGISTORE_PROMOUSAGELIMIT_TIP'); ?>" >
				<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
			</div>
	</div>
	
	<div class="control-group">
			<label class="control-label"> <?php echo JText::_('VIEWPROMODISCAMOUNT'); ?> </label>
			<div class="controls">
				<input type="text" style="width:50px" name="amount" value="<?php echo $promo->amount;?>" />
				<input type="radio" name="promotype" value="0" <?php echo ($promo->promotype == 0)?"checked":""; ?> /><span class="lbl"></span><?php echo $configs->currencydef;?>
				
	 			<input type="radio" name="promotype" value="1" <?php echo ($promo->promotype == 1 || $promo->promotype !== '0')?"checked":""; ?> /><span class="lbl"></span>%
	 			
				<span class="editlinktip hasTip" title="<?php echo JText::_('COM_DIGISTORE_PROMODISCOUNT_TIP'); ?>" >
				<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
			</div>
	</div>

	<div class="control-group">
			<label class="control-label"> <?php echo JText::_('VIEWPROMOSTARTPUBLISH'); ?> </label>
			<div class="controls">
				<?php
            	if($promo->codestart == NULL){
					$promo->codestart = date($format_string, time());
				}
				else{
					$promo->codestart = date($format_string, $promo->codestart);
				}

				if(isset($promo->codeend) && $promo->codeend > 0){
					$promo->codeend = date($format_string, $promo->codeend);
				}
				else{
					$promo->codeend = "Never";
				}
				?>
			
				<?php echo JHTML::_('calendar',  $promo->codestart, 'codestart', 'codestart', $format_string_2, array('class'=>'inputbox', 'size'=>'20',  'maxlength'=>'19')); ?>
				<span class="editlinktip hasTip" title="<?php echo JText::_('COM_DIGISTORE_PROMOSTARTPUB_TIP'); ?>" >
				<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
			</div>
	</div>
<div class="control-group">
			<label class="control-label"> <?php echo JText::_('VIEWPROMOENDPUB'); ?> </label>
			<div class="controls">
				
				<?php
					if($promo->codeend == "Never"){
						$promo->codeend = "";
					}
					
					$calendar = JHtml::calendar(trim($promo->codeend), 'ad_end_date', 'ad_end_date', $format_string_2, ''); 
					
					if($promo->codeend == ""){
						$calendar = str_replace('value=""', 'value="Never"', $calendar);
					}
					echo $calendar;
				?>
				<span class="editlinktip hasTip" title="<?php echo JText::_('COM_DIGISTORE_PROMOENDPUB_TIP'); ?>" >
				<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
			</div>
	</div>
	
	<div class="control-group">
			<label class="control-label"> <?php echo JText::_('VIEWPROMOOFEC'); ?> </label>
			<div class="controls">
				 <fieldset class="radio btn-group" id="forexisting">
					<?php
						$no_checked = "";
						$yes_cheched = "";
						
						if(!isset($promo->forexisting) || $promo->forexisting == "1") {
							$yes_cheched = 'checked="checked"';
						}
						else{
							$no_checked = 'checked="checked"';
						}
					?>
					<input type="hidden" name="forexisting" value="0">
					<input type="checkbox" <?php echo $yes_cheched; ?> value="1" class="ace-switch ace-switch-5" name="forexisting">
					<span class="lbl"></span>
				</fieldset>
				<span class="editlinktip hasTip" title="<?php echo JText::_('COM_DIGISTORE_PROMOOFEC_TIP'); ?>" >
				<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
			</div>
	</div>
	
	<div class="control-group">
			<label class="control-label"> <?php echo JText::_('VIEWPROMOPUBLISHING'); ?> </label>
			<div class="controls">
				 <fieldset class="radio btn-group" id="published">
					<?php
						$no_checked = "";
						$yes_cheched = "";
						
						if(isset($promo->published)) {
							$yes_cheched = 'checked="checked"';
						}
						else{
							$no_checked = 'checked="checked"';
						}
					?>
					<input type="hidden" name="published" value="0">
					<input type="checkbox" <?php echo $yes_cheched; ?> value="1" class="ace-switch ace-switch-5" name="published">
					<span class="lbl"></span>
				</fieldset>
				<span class="editlinktip hasTip" title="<?php echo JText::_('COM_DIGISTORE_PROMOPUBLISHING_TIP'); ?>" >
				<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
			</div>
	</div>
	<div class="well"><?php echo JText::_('VIEWPROMOSTATS');?></div>

<?php
  	if ($promo->codeend != $nullDate) {
  		$startdate =strtotime($promo->codestart);
		$enddate = strtotime($promo->codeend);
  		$period = $enddate - $startdate; //$promo->codestart;
	$days = (int ) ($period / (3600 * 24)) ;
	$left = $period % (3600 * 24);
	$hours = (int ) ($left / 3600 );
	$mins = (int )(($left - $hours*3600)/60) ;//$left % (3600 );

  	} else {
  		$period = 0;// $promo->codeend - time(); //$promo->codestart;
  		$days = JText::_("VIEWPROMOUNLIM");//(int ) ($period / (3600 * 24)) ;
		$left = JText::_("VIEWPROMOUNLIM");//$period % (3600 * 24);
		$hours = JText::_("VIEWPROMOUNLIM");//(int ) ($left / 3600 );
		$mins = JText::_("VIEWPROMOUNLIM");//(int )(($left - $hours*3600)/60) ;//$left % (3600 );
  		
  	}
	
  	$codelimit = ($promo->codelimit != 0)?$promo->codelimit:JText::_("VIEWPROMOINF");
  	$codeleft = ($promo->codelimit != 0)?($promo->codelimit - $promo->used):JText::_("VIEWPROMOINF");
?>        
        <table class="table" border="0">
               <tr>
                     <td> <?php echo "<strong>".JText::_("VIEWPROMOTOTALUSES").":</strong> ".$codelimit;?></td>
                     <td><?php echo "<strong>".JText::_("VIEWPROMOREMUSES").":</strong> ".$codeleft;?></td>
                     <td><?php echo "<strong>".JText::_("VIEWPROMOUSED").":</strong> ".$promo->used;?></td>
				</tr>
	             <tr>
						<?php
							if($days < 0){
								$days = 0;
							}
							
							if($hours < 0){
								$hours = 0;
							}
							
							if($mins < 0){
								$mins = 0;
							}
						?>
				   
	                     <td colspan="3"><?php echo "<strong>".JText::_("VIEWPROMOTTL") .":</strong> ". $days ." ". JText::_("VIEWPROMOTTLDAYS"); ?> &nbsp; &nbsp;
	                     <?php echo $hours ." ". JText::_("VIEWPROMOTTLHOWRS"); ?> &nbsp; &nbsp;
	                     <?php echo $mins ." ". JText::_("VIEWPROMOTTLMIN"); ?></td>
				</tr>
    	 </table>
  	<input type="hidden" name="images" value="" />                
	        <input type="hidden" name="option" value="com_adagency" />
	        <input type="hidden" name="id" value="<?php echo $promo->id; ?>" />
	        <input type="hidden" name="task" value="" />
		<input type="hidden" name="controller" value="adagencyPromocodes" />
 </form>