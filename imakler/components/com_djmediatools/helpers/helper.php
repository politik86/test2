<?php
/**
 * @version $Id: helper.php 18 2013-10-01 15:04:53Z szymon $
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
defined('_JEXEC') or die ('Restricted access');
defined('DS') or define('DS', DIRECTORY_SEPARATOR);

require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_djmediatools'.DS.'lib'.DS.'image.php');
jimport( 'joomla.application.component.model' );
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

class DJMediatoolsLayoutHelper extends JObject
{
	protected $_prefix = null;
	protected $_modal = false;
	
	public static function getInstance($prefix = 'slideshow') {
		
		$layout = explode(':', $prefix);
		if(count($layout)>1) $prefix = $layout[1];
		
		// Get the helper file path.
		$path = dirname(__FILE__).DS.'..'.DS.'layouts'.DS.$prefix.DS.'helper.php';
		
		// Get the helper class name.
		$class = ucfirst($prefix).'DJMediatoolsLayoutHelper';

		// Include the class if not present.
		if (!class_exists($class)) {
			// If the helper file path exists, include it.
			if (file_exists($path)) {
				require_once $path;
			}
			else {
				$class = 'DJMediatoolsLayoutHelper';
			}
		}

		// Instantiate the class.
		if (class_exists($class)) {
			$instance = new $class($prefix);
		}
		else {
			JError::raiseError(500, JText::sprintf( 'COM_DJMEDIATOOLS_ERROR_INVALID_HELPER_CLASS' , $class) );
		}
		
		return $instance;
	}
	
	public function __construct($prefix = null){
		$this->_prefix = $prefix;
	}
	
	public function getParams(&$params) {
		
		// set default values and check if numeric params are numeric
		if(!is_numeric($params->get('max_images'))) $params->set('max_images', 20);
		if(!is_numeric($params->get('visible_images'))) $params->set('visible_images', 3);
		if(!is_numeric($params->get('autoplay'))) $params->set('autoplay', 1);
		if(!is_numeric($params->get('pause_autoplay'))) $params->set('pause_autoplay', 1);
		if(!is_numeric($params->get('image_width'))) $params->set('image_width', 700);
		if(!is_numeric($params->get('image_height'))) $params->set('image_height', 450);
		if(!is_numeric($params->get('space_between_images'))) $params->set('space_between_images', 20);
		$params->set('desc_position', $params->get('desc_position','over'));
		if(!$params->get('show_title') && !$params->get('show_desc')) $params->set('desc_position','over');
		if($params->get('desc_position') != 'over') { if(!is_numeric($params->get('desc_width'))) $params->set('desc_width', 200); }
		else { if(!is_numeric($params->get('desc_width'))) $params->set('desc_width', $params->get('image_width')); }
		if(!is_numeric($params->get('desc_bottom'))) $params->set('desc_bottom', 0);
		if(!is_numeric($params->get('desc_horizontal'))) $params->set('desc_horizontal', 0);
		if(!is_numeric($params->get('arrows_top'))) $params->set('arrows_top', 50);
		if(!is_numeric($params->get('arrows_horizontal'))) $params->set('arrows_horizontal', 10);
		if(!is_numeric($params->get('duration'))) $params->set('duration', 0);
		if(!is_numeric($params->get('delay'))) $params->set('delay', 6000);
		if(!is_numeric($params->get('preload'))) $params->set('preload', 0);
		
		$params->set('slider_type', $params->get('slider_type','left'));
		$params->set('desc_effect', ($params->get('desc_effect','none')=='none' ? '': $params->get('desc_effect')));
		
		$params->set('link_image', (int)$params->get('link_image', 1));
		$params->def('window_size', 'fluid');
		
		return $params;
	}
	
	public function getSlides(&$params) {
			
		// taking the slides from the source
		$app = JFactory::getApplication();
		
		$slides = null;
		$source = $params->get('source');
		
		switch($source) {
			case 'component':
				$slides = $this->getImagesFromDJMediatools($params);
				break;
			default:
				$dispatcher	= JDispatcher::getInstance();
				JPluginHelper::importPlugin('djmediatools', $source);
				$results = $dispatcher->trigger('onAlbumPrepare', array (&$source, &$params));
				if(isset($results[0])) $slides = $results[0];
				break;
		}
		
		if(is_array($slides) && count($slides)>0) foreach($slides as $key => $slide) {
			if(!$slide->resized_image = DJImageResizer::createThumbnail($slide->image, 'media/djmediatools/cache', $params->get('image_width'), $params->get('image_height'), $params->get('resizing','crop'), $params->get('quality',90))) {
				$slide->resized_image = $slide->image;
			}
			if(!$slide->thumb_image = DJImageResizer::createThumbnail($slide->image, 'media/djmediatools/cache', $params->get('thumb_width'), $params->get('thumb_height'), 'crop', 80)) {
				$slide->thumb_image = $slide->resized_image;
			}
			// fix path for SEF links but not for external image urls
			if(strcasecmp(substr($slide->image, 0, 4), 'http') != 0 && !empty($slide->image)) {
				$slide->image = JURI::root(true).'/'.$slide->image;
			}
			if(strcasecmp(substr($slide->resized_image, 0, 4), 'http') != 0 && !empty($slide->resized_image)) {
				$slide->resized_image = JURI::root(true).'/'.$slide->resized_image;
			}
			if(strcasecmp(substr($slide->thumb_image, 0, 4), 'http') != 0 && !empty($slide->thumb_image)) {
				$slide->thumb_image = JURI::root(true).'/'.$slide->thumb_image;
			}
			
			if(!isset($slide->full_desc)) $slide->full_desc = $slide->description;
			$slide->description = $this->truncateDescription($slide->description, $params->get('limit_desc'));
			if(!isset($slide->target)) $slide->target = $this->getSlideTarget($slide->link);
			if(!isset($slide->alt)) $slide->alt = $slide->title;
			
			if($params->get('link_image',1)==3) {
				$slide->item_link = JRoute::_('index.php?option=com_djmediatools&view=item&cid='.$params->get('category').'&id='.$key.'&tmpl=component');
			}
			
		} else {
			$slides = null;
		}
		
		if($params->get('link_image',1)==3) {
			$this->addModal($params);
		}
		
		return $slides;
	}
	
    private function getImagesFromDJMediatools(&$params) {
		
		$max = $params->get('max_images');
        $catid = $params->get('category',0);
		
		// build query to get slides
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('a.*');
		$query->from('#__djmt_items AS a, #__djmt_albums AS c');
		
		if (is_numeric($catid)) {
			$query->where('a.catid = ' . (int) $catid);
		}
		$query->where('a.catid = c.id');
		$query->where('c.published = 1');
		$query->where('a.published = 1');
		
		// Filter by start and end dates.
		$nullDate	= $db->Quote($db->getNullDate());
		$nowDate	= $db->Quote(JFactory::getDate()->toSql());
		
		$query->where('(a.publish_up = '.$nullDate.' OR a.publish_up <= '.$nowDate.')');
		$query->where('(a.publish_down = '.$nullDate.' OR a.publish_down >= '.$nowDate.')');
		
		if($params->get('sort_by',1)) {
			$query->order('a.ordering ASC');
		} else {
			$query->order('RAND()');
		}

		$db->setQuery($query, 0 , $max);
		$slides = $db->loadObjectList();
		
		foreach($slides as $slide){
			$slide->params = new JRegistry($slide->params);
			$slide->link = $this->getSlideLink($slide, $params);
			$slide->description = $this->getSlideDescription($slide);
			$slide->alt = $slide->title;
			$slide->target = $this->getSlideTarget($slide->link);
		}
		
		return $slides;
    }
	
	public static function getImageFromText(&$text, $remove = true)
	{
		$src = '';
		if(preg_match("/<img [^>]*src=\"([^\"]*)\"[^>]*>/", $text, $matches)){
			if($remove) $text = preg_replace("/<img[^>]*>/", '', $text);
			$src = $matches[1];
		}
		
		return $src;
	}
	
	private function getSlideLink(&$slide, &$params) {
		
		$link = '';
		$db = JFactory::getDBO();
		$app = JFactory::getApplication();
		// first check if item has linked video
		
		if($slide->video) {
			
			$this->addModal($params);
			
			return $slide->video.'?autoplay=1&amp;rel=0" class="video-link djmodal';
		}
		
		$link_type = explode(';',$slide->params->get('link_type', ''));
		switch($link_type[0]) {
			case 'menu':
				if ($menuid = $slide->params->get('link_menu',0)) {
					
					$menu = $app->getMenu();
					$menuitem = $menu->getItem($menuid);
					if($menuitem) switch($menuitem->type) {
						case 'component': 
							$link = JRoute::_($menuitem->link.'&Itemid='.$menuid);
							break;
						case 'url':
						case 'alias':
							$link = JRoute::_($menuitem->link);
							break;
					}
				}
				break;
			case 'url':
				if($itemurl = $slide->params->get('link_url',0)) {
					$link = JRoute::_($itemurl);
				}
				break;
			case 'article':
				if ($artid = $slide->params->get('id',$slide->params->get('link_article',0))) {
					jimport('joomla.application.component.model');
					require_once(JPATH_BASE.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'route.php');
					JModelLegacy::addIncludePath(JPATH_BASE.DS.'components'.DS.'com_content'.DS.'models');
					$model = JModelLegacy::getInstance('Articles', 'ContentModel', array('ignore_request'=>true));
					$model->setState('params', $app->getParams());
					$model->setState('filter.article_id', $artid);
					$model->setState('filter.article_id.include', true); // Include
					$items = $model->getItems();
					if($items && $item = $items[0]) {
						$item->slug = $item->alias ? ($item->id . ':' . $item->alias) : $item->id;
						$link = JRoute::_(ContentHelperRoute::getArticleRoute($item->slug, $item->catid));
						$slide->introtext = $item->introtext;
					}
				}
				break;
		}
		
		return $link;
	}
	
	private function getSlideTarget($link) {
		
		if(preg_match("/^http/",$link) && !preg_match("/^".str_replace(array('/','.','-'), array('\/','\.','\-'),JURI::base())."/",$link)) {
			$target = '_blank';
		} else {
			$target = '_self';
		}
		
		return $target;
	}
	
	private function getSlideDescription($slide) {
		
		$link_type = explode(';',$slide->params->get('link_type', ''));
		if($link_type=='article' && empty($slide->description)){ // if article and no description then get introtext as description
			if(isset($slide->introtext)) $slide->description = $slide->introtext;
		}
		
		return $slide->description;
	}
	
	private function truncateDescription($text, $limit) {
		
		$text = preg_replace('/{djmedia\s*(\d*)}/i', '', $text);
		
		$desc = strip_tags($text);
		if($limit && $limit < strlen($desc)) {
			$limit = strpos($desc, ' ', $limit);
			$desc = substr($desc, 0, $limit);
			if(preg_match('/[A-Za-z0-9]$/', $desc)) $desc.=' ...';
			$desc = nl2br($desc);
		} else { // no limit or limit greater than description
			$desc = $text;
		}

		return $desc;
	}
	
	public function getNavigation(&$params) {
		
		$mid = $params->get('gallery_id');
		
		$prev = $params->get('left_arrow');
		$next = $params->get('right_arrow');
		$play = $params->get('play_button');
		$pause = $params->get('pause_button');
		
		if($params->get('slider_type')=='up' || $params->get('slider_type')=='down') {			
			if(empty($prev) || !file_exists(JPATH_ROOT.DS.$prev)) $prev = 'components/com_djmediatools/layouts/'.$this->_prefix.'/images/up.png';			
			if(empty($next) || !file_exists(JPATH_ROOT.DS.$next)) $next = 'components/com_djmediatools/layouts/'.$this->_prefix.'/images/down.png';
		} else {			
			if(empty($prev) || !file_exists(JPATH_ROOT.DS.$prev)) $prev = 'components/com_djmediatools/layouts/'.$this->_prefix.'/images/prev.png';			
			if(empty($next) || !file_exists(JPATH_ROOT.DS.$next)) $next = 'components/com_djmediatools/layouts/'.$this->_prefix.'/images/next.png';
		}
		if(empty($play) || !file_exists(JPATH_ROOT.DS.$play)) $play = 'components/com_djmediatools/layouts/'.$this->_prefix.'/images/play.png';
		if(empty($pause) || !file_exists(JPATH_ROOT.DS.$pause)) $pause = 'components/com_djmediatools/layouts/'.$this->_prefix.'/images/pause.png';
		
		$prev = JURI::root(true).'/'.$prev;
		$next = JURI::root(true).'/'.$next;
		$play = JURI::root(true).'/'.$play;
		$pause = JURI::root(true).'/'.$pause;
		
		$navi = (object) array('prev'=>$prev,'next'=>$next,'play'=>$play,'pause'=>$pause);
		
		return $navi;
	}
	
	public function addScripts(&$params) {
		
		$mid = $params->get('gallery_id');
		
		JHTML::_('behavior.framework', true);
		$document = JFactory::getDocument();
		
		if($params->get('link_image',1)==2) $this->addLightbox($params->get('lightbox','picbox'));
		
		$document->addScript('components/com_djmediatools/assets/js/powertools-1.2.0.js');
		$document->addScript('components/com_djmediatools/layouts/slideshow/js/slideshow.js');
		if(file_exists(JPATH_ROOT.DS.'components'.DS.'com_djmediatools'.DS.'layouts'.DS.$this->_prefix.DS.'js'.DS.$this->_prefix.'.js')) {
			$document->addScript('components/com_djmediatools/layouts/'.$this->_prefix.'/js/'.$this->_prefix.'.js');
		}
		
		$animationOptions = "{".implode(',', $this->getAnimationOptions($params))."}";
		
		$className = ucfirst($this->_prefix);
		
		$js = "window.addEvent('domready',function(){ if(!this.DJSlideshow$mid) this.DJSlideshow$mid = new DJImage$className('dj-$this->_prefix$mid',$animationOptions) });";
		//$js = "(function($){ ".$js." })(document.id);";
		$document->addScriptDeclaration($js);
	}

	protected function addLightbox($type) {
		
		JHTML::_('behavior.framework', true);
		$document = JFactory::getDocument();
		
		$js = 'components/com_djmediatools/assets/picbox/js/picbox.js';
		$css = 'components/com_djmediatools/assets/picbox/css/picbox.css';
		
		if($type=='slimbox') {
			$js = 'components/com_djmediatools/assets/slimbox-1.8/js/slimbox.js';
			$css = 'components/com_djmediatools/assets/slimbox-1.8/css/slimbox.css';
		}
		
		$document->addStyleSheet($css);
		$document->addScript($js);
	}

	protected function addModal(&$params) {
		
		if($this->_modal) return;
		
		if($params->get('window_size') == 'fluid') $modal_options = '{handler: \'iframe\', size: {x: \'100%\', y: \'100%\'}, onOpen: function() { window.addEvent(\'resize\', function(){ this.resize({x: window.getSize().x - 70, y: window.getSize().y - 70}, true); }.bind(this) ); window.fireEvent(\'resize\'); }}';
		else $modal_options = '{handler: \'iframe\', size: {x: '.$params->get('window_width','850').', y: '.$params->get('window_height','510').'} }';
		
		JHTML::_('behavior.modal','a.modal');
		$document = JFactory::getDocument();
		$document->addScriptDeclaration("
		window.addEvent('domready', function() {
			if(Browser.ie && Browser.version < 9) {
				$$('a.djmodal').each(function(link){
					link.setProperty('target','_blank');
				});
			} else {
				SqueezeBox.assign($$('a.djmodal'), $modal_options );
			}
		});
		");
		
		$this->_modal = true;
	}
	
	public function getAnimationOptions(&$params) {
		
		$effect = $params->get('effect');
		$effect_type = $params->get('effect_type');
		$duration = $params->get('duration');
		$delay = $params->get('delay');
		
		if($params->get('slider_type')=='fade' && !$duration && !$effect_type) {
			$transition = 'Sine.easeOut';
			$duration = 800;
		} else if($params->get('slider_type')=='ifade' && !$duration && !$effect_type) {
			$transition = 'Expo.easeOut';
			$duration = 1200;
		} else switch($effect){
			case 'Linear':
				$transition = 'linear';
				if(!$duration) $duration = 600;
				break;
			case 'Circ':
			case 'Expo':
			case 'Back':
				if(!$effect_type) $transition = $effect.'.easeInOut';
				else $transition = $effect.'.'.$effect_type;
				if(!$duration) $duration = 1000;
				break;
			case 'Bounce':
				if(!$effect_type) $transition = $effect.'.easeOut';
				else $transition = $effect.'.'.$effect_type;
				if(!$duration) $duration = 1200;
				break;
			case 'Elastic':
				if(!$effect_type) $transition = $effect.'.easeOut';
				else $transition = $effect.'.'.$effect_type;
				if(!$duration) $duration = 1500;
				break;
			case 'Cubic':
			default:
				$transition = $effect.'.';
				if(!$effect_type) $transition .= 'easeInOut';
				else $transition .= $effect_type;
				if(!$duration) $duration = 800;
		}
		$delay = $delay + $duration;
		
		$width = $params->get('image_width');
		if($params->get('desc_position')!='over') $width += $params->get('desc_width');
		
		$options[] = "autoplay: ".$params->get('autoplay');
		$options[] = "pause_autoplay: ".$params->get('pause_autoplay');
		$options[] = "transition: Fx.Transitions.$transition";
		$options[] = "duration: $duration";
		$options[] = "delay: $delay";
		$options[] = "slider_type: '".$params->get('slider_type')."'";
		$options[] = "desc_effect: '".$params->get('desc_effect')."'";
		$options[] = "width: $width";
		$options[] = "height: ".$params->get('image_height');
		$options[] = "spacing: ".$params->get('space_between_images');
		$options[] = "navi_margin: ".$params->get('arrows_horizontal');
		$options[] = "preload: ".$params->get('preload');
		
		return $options;
	}
	
	public function addStyles(&$params){
		
		$app = JFactory::getApplication();
		$document = JFactory::getDocument();
		
		$options = $this->getStyleSheetParams($params);
		$file = 'media/djmediatools/css/' . $this->_prefix . '_' . md5(implode('&',$options)) . '.css';
		
		if(!JFile::exists(JPATH_ROOT . DS . $file)) {
			//$this->debug(JPath::clean(JPATH_ROOT . DS . $file));
			// Get the css file path.
			$path = JPATH_ROOT.DS.'components'.DS.'com_djmediatools'.DS.'layouts'.DS.'slideshow'.DS.'css'.DS.$this->_prefix.'.css.php';
			$ipath = JURI::root(true).'/components/com_djmediatools/layouts/slideshow';
			if(file_exists(JPATH_ROOT.DS.'templates'.DS.$app->getTemplate().DS.'css'.DS.$this->_prefix.'.css.php')) {
				$path = JPATH_ROOT.DS.'templates'.DS.$app->getTemplate().DS.'css'.DS.$this->_prefix.'.css.php';
				$ipath = JURI::root(true).'/templates/'.$app->getTemplate();
			} else if(file_exists(JPATH_ROOT.DS.'components'.DS.'com_djmediatools'.DS.'layouts'.DS.$this->_prefix.DS.'css'.DS.$this->_prefix.'.css.php')) {
				$path = JPATH_ROOT.DS.'components'.DS.'com_djmediatools'.DS.'layouts'.DS.$this->_prefix.DS.'css'.DS.$this->_prefix.'.css.php';
				$ipath = JURI::root(true).'/components/com_djmediatools/layouts/'.$this->_prefix;
			}
			
			ob_start();
			
			require($path);
			
			$buffer = ob_get_clean();
			
			if(!JFile::write(JPATH_ROOT . DS . $file, $buffer)) {
				// if write to file faild we have to add the styles anyway
				// add parameters to the css file path
				$urloptions = '';
				foreach($options as $key => $val) {
					$urloptions.='&'.$key.'='.$val;
				}
				$file = 'index.php?option=com_djmediatools&amp;task=getcss&amp;format=raw&amp;layout='.$this->_prefix.'&amp;params='.base64_encode($urloptions);
			}
			
			
		}
		
		$document->addStyleSheet(JURI::root(true).'/'.$file);
	}
	
	public function getStyleSheetParams(&$params) {
		
		$mid = $params->get('gallery_id');
		$slide_width = $params->get('image_width');
		$slide_height = $params->get('image_height');
		$desc_width = $params->get('desc_width');
		$desc_position = $params->get('desc_position');
		if($desc_position == 'over') {
			if($desc_width > $slide_width) $desc_width = $slide_width;
			$desc_bottom = $params->get('desc_bottom');
			$desc_left = $params->get('desc_horizontal');
		} else if($desc_position != 'tip' ){
			$slide_width += $desc_width;
		}
		$arrows_top = $params->get('arrows_top');
		$arrows_horizontal = $params->get('arrows_horizontal');
		$arrows_top = (($arrows_top / $slide_height) * 100);
		
		$desc_width = (($desc_width / $slide_width) * 100);
		if($desc_position == 'over') {
			$desc_left = (($desc_left / $slide_width) * 100);
			$desc_bottom = (($desc_bottom / $slide_height) * 100);
		}			
		
		$options['mid'] = $mid;
		$options['w'] = $slide_width;
		$options['h'] = $slide_height;
		$options['dp'] = $desc_position;
		$options['dw'] = $desc_width;
		$options['at'] = $arrows_top;
		$options['ah'] = $arrows_horizontal;
		$options['cnp'] = $params->get('custom_nav_pos');
		$options['cna'] = $params->get('custom_nav_align');
		
		$options['lip'] = $params->get('loader_position');
		if($desc_position == 'over') {
			$options['db'] = $desc_bottom;
			$options['dl'] = $desc_left;
		}
		
		$options['r'] = $params->get('resizing');
		
		return $options;
	}
	
	public function debug($msg,$type='message'){
	
		$app=JFactory::getApplication();
		$app->enqueueMessage('<pre>'.print_r($msg,true).'</pre>',$type);
	
	}
}
