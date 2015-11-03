<?php
/**
 * @version $Id: view.html.php 15 2013-07-15 13:34:25Z szymon $
 * @package DJ-MediaTools
 * @copyright Copyright (C) 2012 DJ-Extensions.com LTD, All rights reserved.
 * @license http://www.gnu.org/licenses GNU/GPL
 * @author url: http://dj-extensions.com
 * @author email contact@dj-extensions.com
 * @developer Szymon Woronowski - szymon.woronowski@design-joomla.eu
 *
 * DJ-MediaTools is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * DJ-MediaTools is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with DJ-MediaTools. If not, see <http://www.gnu.org/licenses/>.
 *
 */

defined ('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');
jimport('joomla.application.component.model');

require_once(JPATH_COMPONENT.DS.'helpers'.DS.'helper.php');

class DJMediatoolsViewItem extends JViewLegacy {
	
	protected $params = null;
	protected $category = null;
	protected $slides = null;
	protected $current = null;
	protected $modules = null;
	
	function display($tpl = null) {
		
		// Initialise variables
		JModelLegacy::addIncludePath(JPATH_COMPONENT.'/models');
		$model = JModelLegacy::getInstance('Categories','DJMediaToolsModel',array('ignore_request'=>true));
		
		$model->setState('category.id',JRequest::getVar('cid', 0, '', 'int'));
		$this->current = JRequest::getVar('id', 0, '', 'int');
		$params = $model->getParams();
		$category = $model->getItem();
		
		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseWarning(500, implode("\n", $errors));
			return false;
		}
		
		if ($category === false)
		{
			JError::raiseError(404, JText::_('COM_DJMEDIATOOLS_ERROR_CATEGORY_NOT_FOUND'));
			return false;
		}
		
		// get gallery slides and layout
		$helper = DJMediatoolsLayoutHelper::getInstance('slideshow');
		$params->def('category',$category->id);
		$params->def('source',$category->source);
		$params = $helper->getParams($params);
		$this->slides = $helper->getSlides($params);
		
		//Escape strings for HTML output
		$this->pageclass_sfx = htmlspecialchars($params->get('pageclass_sfx'));

		$this->assignRef('params', $params);
		$this->assignRef('category', $category);
		
		// render modules
//		$document	= JFactory::getDocument();
		$renderer	= $this->document->loadRenderer('module');
		$position	= 'djmt-item-desc';
		$modules	= JModuleHelper::getModules($position);
		$mparams		= array('style' => 'xhtml');

		ob_start();
		foreach ($modules as $module) {
			echo $renderer->render($module, $mparams);
		}		
		$this->modules[$position] = ob_get_clean();
		
		$this->_prepareDocument();
		
        parent::display($tpl);
	}
	
	protected function _prepareDocument() {
		
		$app	= JFactory::getApplication();
		$menus	= $app->getMenu();
		$pathway= $app->getPathway();
		$title	= null;
		
		JHTML::_('behavior.framework');
		if($this->params->get('zoom',1)) { // first must be the zoomer script
			$this->document->addScript('components/com_djmediatools/assets/js/zoomer.js');
			$this->document->addScriptDeclaration(" window.addEvent('load', function(){ new Zoomer('dj-image',{big: '".$this->slides[$this->current]->image."', smooth: 10 }); }); ");
		}
		// then script to center image vertically
		$this->document->addScript('components/com_djmediatools/assets/js/item.js');
		
		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = $menus->getActive();
		if ($menu)
		{
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		} else {
			$this->params->def('page_heading', JText::_('COM_DJMEDIATOOLS'));
		}
		$title = $this->params->get('page_title', '');
		
		$id = (int) @$menu->query['id'];
		
		if (empty($title)) {
			$title = $app->getCfg('sitename');
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 1) {
			$title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 2) {
			$title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
		}
		$this->document->setTitle($title);

		if ($this->params->get('menu-meta_description'))
		{
			$this->document->setDescription($this->params->get('menu-meta_description'));
		}

		if ($this->params->get('menu-meta_keywords'))
		{
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
		}

		if ($this->params->get('robots'))
		{
			$this->document->setMetadata('robots', $this->params->get('robots'));
		}

	}

}




