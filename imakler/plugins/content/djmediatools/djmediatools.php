<?php
/**
 * @version $Id: djmediatools.php 18 2013-10-01 15:04:53Z szymon $
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

class plgContentDJMediatools extends JPlugin
{
	protected static $galleries = array();
	/**
	 * Plugin that loads DJ-Mediatools gallery within content
	 *
	 * @param	string	The context of the content being passed to the plugin.
	 * @param	object	The article object.  Note $article->text is also available
	 * @param	object	The article params
	 * @param	int		The 'page' number
	 */
	public function onContentPrepare($context, &$article, &$params, $page = 0)
	{
		// Don't run this plugin when the content is being indexed
		if ($context == 'com_finder.indexer') {
			return true;
		}

		// simple performance check to determine whether bot should process further
		if (strpos($article->text, 'djmedia') === false && strpos($article->text, 'djmediatools') === false) {
			return true;
		}

		// expression to search for (positions)
		$regex		= '/{djmedia\s*(\d*)}/i';
		$regex2		= '/<img [^>]*alt="djmedia:(\d*)"[^>]*>/i';
		//$style		= $this->params->def('style', 'none');
		
		// replace the image placeholder with plugin code
		$article->text = preg_replace($regex2, '{djmedia $1}', $article->text);
		
		// Find all instances of plugin and put in $matches for djmedia code
		// $matches[0] is full pattern match, $matches[1] is the album ID
		preg_match_all($regex, $article->text, $matches, PREG_SET_ORDER);
		// No matches, skip this
		if ($matches) {
			foreach ($matches as $match) {
				$output = '';
				// Chceck if album ID is set.
				if (isset($match[1]) && (int)$match[1] > 0) {
					$output = $this->_load($match[1]);
				}
				// We should replace only first occurrence in order to allow the same category to regenerate their content:
				$article->text = preg_replace("|$match[0]|", addcslashes($output, '\\$'), $article->text, 1);
			}
		}
	}

	protected function _load($cid)
	{
		if (!isset(self::$galleries[$cid])) {
			self::$galleries[$cid] = '';
			
			jimport( 'joomla.application.module.helper' );
			// Include the syndicate functions only once
			require_once(JPATH_ROOT.'/components/com_djmediatools/helpers/helper.php');
			JModelLegacy::addIncludePath(JPATH_ROOT.'/components/com_djmediatools/models');
			JModelLegacy::addTablePath(JPATH_ADMINISTRATOR.'/components/com_djmediatools/tables');
			
			$model = JModelLegacy::getInstance('Categories', 'DJMediatoolsModel', array('ignore_request'=>true));
			$model->setState('category.id', $cid);
			$model->setState('filter.published', 1);
			$params = $model->getParams(false);
			$category = $model->getItem();
			
			if ($category === false) {
				//JError::raiseError(404, JText::_('COM_DJMEDIATOOLS_ERROR_CATEGORY_NOT_FOUND'));
				return null;
			}
			
			$lang = JFactory::getLanguage();
			$lang->load('com_djmediatools', JPATH_SITE, 'en-GB', false, false);
    		$lang->load('com_djmediatools', JPATH_SITE . '/components/com_djmediatools', 'en-GB', false, false);
    		$lang->load('com_djmediatools', JPATH_SITE, null, true, false);
    		$lang->load('com_djmediatools', JPATH_SITE . '/components/com_djmediatools', null, true, false);
			
			// get gallery slides and layout
			$helper = DJMediatoolsLayoutHelper::getInstance($params->get('layout', 'slideshow'));
			$mid = $category->id.'p';
			$params->set('gallery_id',$mid);
			$params->set('category',$category->id);
			$params->set('source',$category->source);
			$params = $helper->getParams($params);
			$slides = $helper->getSlides($params);
			if($slides) {
				$helper->addScripts($params);
				$helper->addStyles($params);
				$navigation = $helper->getNavigation($params);
			} else {
				return JText::_('COM_DJMEDIATOOLS_EMPTY_CATEGORY');
			}
						
			ob_start();

			require JModuleHelper::getLayoutPath('mod_djmediatools', $params->get('layout', 'slideshow'));

			self::$galleries[$cid] = ob_get_clean();
		}
		return self::$galleries[$cid];
	}
	
}
