<?php
/**
 *  @package    Social Ads
 *  @copyright  Copyright (c) 2009-2013 TechJoomla. All rights reserved.
 *  @license    GNU General Public License version 2, or later
 */
defined('_JEXEC') or die( 'Restricted access' );

	// show show ad review view
	$ads_calledFromCkout = 1;  // used in showad view
	$socialadshelper = new socialadshelper();
	$billpath = $socialadshelper->getViewpath('showad','default');
	ob_start();
		include($billpath);
		$html = ob_get_contents();
	ob_end_clean();
	echo $html;

	// show payment gateway

		$default="";
		$lable=JText::_( 'ADS_SEL_GATEWAY' );
		$gateway_div_style=1;
		if(!empty($this->gateways)) //if only one geteway then keep it as selected
		{
			$default=$this->gateways[0]->id; // id and value is same
		}
		if(!empty($this->gateways) && count($this->gateways)==1) //if only one geteway then keep it as selected
		{
			$default=$this->gateways[0]->id; // id and value is same
			$lable=JText::_( 'QTC_GATEWAY_IS' );
			$gateway_div_style=0;
		}
		?>
		<label for="" class="control-label"><?php echo $lable ?></label>
		<div class="controls" style="<?php echo ($gateway_div_style==1)?"" : "display:none;" ?>">
			<?php
			if(empty($this->gateways))
				echo JText::_( 'NO_PAYMENT_GATEWAY' );
			else
			{
				$pg_list = JHtml::_('select.radiolist', $this->gateways, 'gateways', 'class="inputbox required" ', 'id', 'name',$default,false);
				echo $pg_list;
			}
			?>
		</div>
		<?php
		if(empty($gateway_div_style))
		{
			?>
				<div class="controls qtc_left_top">
				<?php echo 	$this->gateways[0]->name; // id and value is same ?>
				</div>
			<?php
		}
?>

		<!-- show payment html -->
		<div class="" id="ads_ckout_payhtml">

		</div>


<script type="text/javascript">

</script>


