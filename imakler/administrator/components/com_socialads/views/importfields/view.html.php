<?php

// no direct access
defined( '_JEXEC' ) or die( ';)' );

jimport( 'joomla.application.component.view');

/**
 * HTML View class for the socialads Component
 *
 * @package    socialads
 * @subpackage Views
 */
class socialadsViewImportfields extends JViewLegacy
{
    /**
     * Importfields view display method
     * @return void
     **/
	function display($tpl = null)
	{
		$style='';
		$style1='';
		if(JVERSION < 3.0)
		{
				$style = '<span title="Save" class="icon-32-save"></span>';
				$style1 = '<span title="Save" class="icon-reset"></span>';
		}
		$button = '<a class="toolbar btn btn-small validate" type="submit" onclick="javascript:resetTargeting();" href="#"><i class="icon-remove ">'.$style1.' </i>Reset</a>';
        $bar =  JToolBar::getInstance('toolbar');
          JToolBarHelper::back( JText::_('CC_HOME') , 'index.php?option=com_socialads');
        $bar->appendButton( 'Custom', $button);
	    $button = '<a class="toolbar btn btn-small validate" type="submit" onclick="javascript:saveTargeting()" href="#"><i class="icon-save "> '.$style.'</i>Save</a>';

        $bar =  JToolBar::getInstance('toolbar');
      
        $bar->appendButton( 'Custom', $button);
		JToolBarHelper::title( JText::_( 'IMPORT_FIELDS' ), 'icon-48-social.png' );
		//JToolBarHelper::cancel( 'cancel', 'Close' );
		
		$adresult = $this->get('AdData');
		
		$this->assignRef('adcount', $adresult);

		/*Start Vaibhav*/
		$pluginresult = $this->get('PluginData');
		$this->assignRef('pluginresult', $pluginresult);
		$colfields = $this->get('colfields');
		$this->assignRef('colfields', $colfields);
		
		/*End Vaibhav*/

		$mappinglistt[] = JHTML::_('select.option','0', JText::_("DONT_MAP"));
		$mappinglistt[] = JHTML::_('select.option','textbox', JText::_("FRETXT"));
		$mappinglistt[] = JHTML::_('select.option','numericrange', JText::_("NUM_RAN"));

		$mappinglista[] = JHTML::_('select.option','0', JText::_("DONT_MAP"));
		$mappinglista[] = JHTML::_('select.option','textbox', JText::_("FRETXT"));
		
		$mappinglists[] = JHTML::_('select.option','0', JText::_("DONT_MAP"));	
		$mappinglists[] = JHTML::_('select.option','singleselect', JText::_("SIN_SEL"));
		$mappinglists[] = JHTML::_('select.option','multiselect', JText::_("MUL_SEL"));	
		
		$mappinglistd[] = JHTML::_('select.option','0', JText::_("DONT_MAP"));
		$mappinglistd[] = JHTML::_('select.option','daterange', JText::_("DAT_RANG"));
		$mappinglistd[] = JHTML::_('select.option','date', JText::_("DATE"));	
		
		$mapall[] = JHTML::_('select.option','0', JText::_("DONT_MAP"));
		$mapall[] = JHTML::_('select.option','textbox', JText::_("FRETXT"));
		$mapall[] = JHTML::_('select.option','numericrange', JText::_("NUM_RAN"));
		$mapall[] = JHTML::_('select.option','singleselect', JText::_("SIN_SEL"));
		$mapall[] = JHTML::_('select.option','multiselect', JText::_("MUL_SEL"));	
		$mapall[] = JHTML::_('select.option','daterange', JText::_("DAT_RANG"));
		$mapall[] = JHTML::_('select.option','date', JText::_("DATE"));	

		$this->assignRef('mappinglista', $mappinglista);		
		$this->assignRef('mappinglistt', $mappinglistt);
		$this->assignRef('mappinglistd', $mappinglistd);
		$this->assignRef('mappinglists', $mappinglists);
		$this->assignRef('mapall', $mapall);
		
	
		$fields = $this->get('ImportFields');
		$this->assignRef('fields', $fields);
				if(JVERSION>=3.0)
			$this->sidebar = JHtmlSidebar::render();
		parent::display($tpl);
	}//function display ends here
	
}// class
