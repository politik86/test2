<?php
/**
 * @version $Id: k2.php 24 2013-12-18 09:34:54Z szymon $
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

// no direct access
defined('_JEXEC') or die;

class plgDJMediatoolsK2 extends JPlugin
{
	/**
	 * Plugin that returns the object list for DJ-Mediatools album
	 * 
	 * Each object must contain following properties (mandatory): title, description, image
	 * Optional properties: link, target (_blank or _self), alt (alt attribute for image)
	 * 
	 * @param	object	The album params
	 */
	public function onAlbumPrepare(&$source, &$params)
	{
		// Lets check the requirements
		$check = $this->onCheckRequirements($source);
		if (is_null($check) || is_string($check)) {
			return null;
		}
		
		$app = JFactory::getApplication();
		
		$default_image = $params->get('plg_k2_image');
		
		require_once(JPATH_BASE.'/modules/mod_k2_content/helper.php');
		
		// fix K2 models path inclusion, we need to add path with prefix to avoid conflicts with other extensions
		JModelLegacy::addIncludePath(JPATH_BASE.'/components/com_k2/models', 'K2Model');

		// create parameters for K2 content module helper
		$mparams = new JRegistry();		
		$mparams->def('itemCount', $params->get('max_images'));		
		$mparams->def('source', $params->get('plg_k2_source'));
		$mparams->def('catfilter', $params->get('plg_k2_catfilter'));
		$mparams->set('category_id', $params->get('plg_k2_category_id', array()));
		$mparams->def('getChildren', $params->get('plg_k2_getChildren'));
		$mparams->def('itemsOrdering', $params->get('plg_k2_itemsOrdering'));
		$mparams->def('FeaturedItems', $params->get('plg_k2_FeaturedItems'));
		$mparams->def('popularityRange', $params->get('plg_k2_popularityRange'));
		$mparams->def('videosOnly', $params->get('plg_k2_videosOnly'));
		$mparams->def('item', $params->get('plg_k2_item'));
		$mparams->set('items', $params->get('plg_k2_items', array()));
		$mparams->def('itemImage', 1);
		$mparams->def('itemIntroText', 1);
		
		//JFactory::getApplication()->enqueueMessage("<pre>".print_r($mparams, true)."</pre>");
		//$mparams->def('extra_fields', 1);
		$items = modK2ContentHelper::getItems($mparams);
		$slides = array();
		
		foreach($items as $item){
			$slide = (object) array();
			
			if(isset($item->imageXLarge)) $slide->image = str_replace(JURI::base(true), '', $item->imageXLarge);
			else $slide->image = DJMediatoolsLayoutHelper::getImageFromText($item->introtext);
			// if no image found in article images and introtext then try fulltext
			if(!$slide->image) $slide->image = DJMediatoolsLayoutHelper::getImageFromText($item->fulltext);
			// if no image found in fulltext then take default image
			if(!$slide->image) $slide->image = $default_image;
			// if no default image set then don't display this article
			if(!$slide->image) continue;

			$slide->title = $item->title;
			$slide->description = $item->introtext;
			if(empty($slide->description)) $slide->description = $item->fulltext;
			
			$slide->link = $item->link;
			
			$slides[] = $slide;
		}
		
		return $slides;		
	}

	/*
	 * Define any requirements here (such as specific extensions installed etc.)
	 * 
	 * Returns true if requirements are met or text message about not met requirement
	 */
	public function onCheckRequirements(&$source) {
		
		// Don't run this plugin when the source is different
		if ($source != $this->_name) {
			return null;
		}
		
		if(!JFile::exists(JPATH_ROOT.'/components/com_k2/k2.php')) return JText::_('PLG_DJMEDIATOOLS_K2_COMPONENT_DISABLED');
		jimport('joomla.application.component.helper');
		$com = JComponentHelper::getComponent('com_k2', true);
		if(!$com->enabled) return JText::_('PLG_DJMEDIATOOLS_K2_COMPONENT_DISABLED');
		
		if(!JFile::exists(JPATH_ROOT.'/modules/mod_k2_content/helper.php')) return JText::_('PLG_DJMEDIATOOLS_K2_CONTENT_MODULE_NOT_INSTALLED');
		
		$language = JFactory::getLanguage();
		$language->load('mod_k2_content', JPATH_SITE, null, true);
		
		return true;		
	}
	
}
