<?php
/** ensure this file is being included by a parent file */
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.plugin.plugin');
$lang = JFactory::getLanguage();
$lang->load('plug_ads_tax_default', JPATH_ADMINISTRATOR);

class plgAdstaxads_tax_default extends JPlugin
{

	//function Add Tax
	function addTax($amt)
	{
			//print_r( $this->tax_per);die;
		$tax_per=$this->params->get('tax_per');
		$tax_value= ($tax_per*$amt)/100;

		$return[]=$tax_per."%";
		$return[]=$tax_value;

		return $return;
	}//function promotlist ends

}//class ends
