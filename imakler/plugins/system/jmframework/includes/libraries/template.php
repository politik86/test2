<?php
/**
 * @version $Id: template.php 15 2013-04-26 11:55:23Z michal $
 * @package JMFramework
 * @copyright Copyright (C) 2012 DJ-Extensions.com LTD, All rights reserved.
 * @license http://www.gnu.org/licenses GNU/GPL
 * @author url: http://dj-extensions.com
 * @author email contact@dj-extensions.com
 * @developer Michal Olczyk - michal.olczyk@design-joomla.eu
 *
 * JMFramework is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * JMFramework is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with JMFramework. If not, see <http://www.gnu.org/licenses/>.
 *
 */

defined('_JEXEC') or die('Restricted access');

abstract class JMFTemplate {
	/**
	 * @var JDocument
	 */
	public $document;
	
	/**
	 * @var JRegistry
	 */
	public $params;
	
	/**
	 * 
	 * Browser type - handler by Mobile_Detect class [desktop|phone|tablet]
	 * @var string
	 */
	public $browser_type;
	
	function __construct(JDocument &$document) {
		jimport('joomla.filesystem.file');
    	jimport('joomla.filesystem.folder');
    	$this->params = new JRegistry();
    	$tpl_vars = get_object_vars($document);
    	foreach ($tpl_vars as $name => $value) {
    		if (!empty($value)) {
    			$this->$name = $value;	
    		}
    	}
    	$this->document = $document;
		$this->setup();
		$this->postSetup();
	}

	protected function setup() {
		$app = JFactory::getApplication();
		$tplarray = $this->params->toArray();
		
		// loading joomla core features
		JHTML::_('behavior.modal');
		JHTML::_('behavior.tooltip');

		// determine the direction
		if ($app->input->get('direction') == 'rtl'){
			setcookie("jmfdirection", "rtl");
			$direction = 'rtl';
		} else if ($app->input->get('direction') == 'ltr') {
			setcookie("jmfdirection", "ltr");
			$direction = 'ltr';
		} else {
			if (isset($_COOKIE['jmfdirection'])) {
				$direction = $_COOKIE['jmfdirection'];
			} else {
				$direction = $app->input->get('jmfdirection', $this->document->direction);
			}
		}
		
		$this->direction = $this->params->set('direction', $direction);
		
		// handle JM Option Groups
		foreach ($tplarray as $param => $value) {
			if (is_string($value) && strstr($value,';')) {
				$parts = explode(';', $value);
				$this->params->set($param, $parts[0]);
			}
		}
		
		if (!class_exists('Mobile_Detect')) {
			require_once JMF_FRAMEWORK_PATH.DIRECTORY_SEPARATOR. 'includes' .DIRECTORY_SEPARATOR. 'libraries' .DIRECTORY_SEPARATOR. 'Mobile_Detect' .DIRECTORY_SEPARATOR. 'Mobile_Detect.php';	
		}
	    $detect = new Mobile_Detect;
	    $this->browser_type = ($detect->isMobile() ? ($detect->isTablet() ? 'tablet' : 'phone') : 'desktop');
	    
		if (!class_exists('DJModuleHelper')) {
			require_once JMF_FRAMEWORK_PATH.DIRECTORY_SEPARATOR. 'includes' .DIRECTORY_SEPARATOR. 'libraries' .DIRECTORY_SEPARATOR. 'modulehelper.php';	
		}
	}
	
	abstract function postSetUp ();
	
	public function cacheStyleSheet($generator) {
		if (JFolder::exists(JPATH_ROOT.DIRECTORY_SEPARATOR."cache".DIRECTORY_SEPARATOR.'tpl-'.$this->template) == false) {
	        if (!JFolder::create(JPATH_ROOT.DIRECTORY_SEPARATOR."cache".DIRECTORY_SEPARATOR.'tpl-'.$this->template)) {
	        	if (JDEBUG) {
	        		throw new Exception(JText::_('PLG_SYSTEM_JMFRAMEWORK_CACHE_FOLDER_NOT_ACCESSIBLE'));	
	        	} else {
	        		return false;
	        	}
	        }
	    }
	    
	    $tplParamsHash = md5($this->params->toString());
	    
		// file name
		$css = current(explode('.', $generator)).'_'.$tplParamsHash.'.css';
	    // CSS path
		$cssPath = JPATH_ROOT.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'tpl-'.$this->template.DIRECTORY_SEPARATOR.$css;
		// CSS URL
		$cssURL = JURI::base().'cache/tpl-'.$this->template.'/'.$css;
		// CSS generator
		$cssGenerator = JMF_TPL_PATH.DIRECTORY_SEPARATOR.'css'.DIRECTORY_SEPARATOR.$generator;
		
		if (!JFile::exists($cssGenerator)) {
			if (JDEBUG) {
        		throw new Exception(JText::sprintf('PLG_SYSTEM_JMFRAMEWORK_MISSING_CSS_GENERATOR', $generator));	
        	} else {
        		return false;
        	}
		}
	    
		if ((!JFile::exists($cssPath) || $this->params->get('devmode', false) == true) && JFile::exists($cssGenerator)) {
		    if (JFile::exists($cssPath)) {
		    	JFile::delete($cssPath);
		    }
			// if there's nothing in cache, let's cache the css.    
		    ob_start();
	        // PHP file which uses template parameters to generate CSS content
	        include($cssGenerator);
	        
	        $cssContent = ob_get_contents();
	        ob_end_clean();
	        if ($cssContent) {
	            if (!JFile::write($cssPath, $cssContent)) {
		            if (JDEBUG) {
		        		throw new Exception(JText::_('PLG_SYSTEM_JMFRAMEWORK_CACHE_FOLDER_NOT_ACCESSIBLE'));	
		        	} else {
		        		return false;
		        	}
	            }
	        }
		}
	    
	    // if CSS exists return its URL
	    if (JFile::exists($cssPath)) {
	        return $cssURL;
	    }
	    return false;
	}
	
	public function countModules($condition) {
		return $this->document->countModules($condition);
	}
	
	public function countMenuChildren()
	{
		return $this->document->countMenuChildren();
	}
	
	public function addStyleSheet($path, $type = 'text/css', $media = null, $attribs = array()) {
		return $this->document->addStyleSheet($path, 'text/css', $media, $attribs);
	}
	
	public function addCompiledStyleSheet($path) {
		$path = $this->lessToCss($path);
		if ($path) {
			return $this->document->addStyleSheet($path);
		}
	}
	
	public function addStyleDeclaration($content, $type = 'text/css'){
		return $this->document->addStyleDeclaration($content, $type);
	}
	
	public function addScript($url, $type = "text/javascript", $defer = false, $async = false)
	{
		return $this->document->addScript($url, $type, $defer, $async);
	}

	public function addScriptDeclaration($content, $type = 'text/javascript')
	{
		return $this->document->addScriptDeclaration($content, $type);
	}

	public function renderBlock($block_name, $is_scheme = false) {
		$block_name = ($is_scheme) ? $block_name : 'blocks'.DIRECTORY_SEPARATOR.$block_name;
		$layout_file = JMF_TPL_PATH.DIRECTORY_SEPARATOR.'tpl'.DIRECTORY_SEPARATOR.$block_name.'.php';
		if (JFile::exists($layout_file)) {
			include($layout_file);
		} else {
			throw new Exception(JText::_('PLG_SYSTEM_JMFRAMEWORK_MISSING_BLOCK_FILE'), 400);
		}
	}
	
	public function renderScheme($scheme_name) {
		return $this->renderBlock($scheme_name, true);
	}
	protected function lessToCss($lessPath) {
		if (class_exists('lessc') == false) {
			require_once JMF_FRAMEWORK_PATH.DIRECTORY_SEPARATOR. 'includes' .DIRECTORY_SEPARATOR. 'libraries' .DIRECTORY_SEPARATOR. 'less' .DIRECTORY_SEPARATOR. 'lessc.inc.php';	
		}
		
		$developer_mode = ($this->params->get('devmode', false) == '1') ? true : false;
		
		$filename = JFile::stripExt(JFile::getName($lessPath));

		$cssPath = JMF_TPL_PATH . DIRECTORY_SEPARATOR. 'css' .DIRECTORY_SEPARATOR. $filename. '.css';
		
		if (!JFile::exists($lessPath)) {
			$lessPath = JMF_TPL_PATH . DIRECTORY_SEPARATOR. 'less' .DIRECTORY_SEPARATOR. $filename. '.less';
		}
		
		if (JFile::exists($lessPath) && JFile::exists($cssPath)) {
			$lessTime = filemtime($lessPath);
			$cssTime = filemtime($cssPath);
			if ($lessTime <= $cssTime && $developer_mode == false) {
				return JMF_TPL_URL. '/css/' . $filename.'.css';
			}
		}
		
		if (JFile::exists($cssPath)) {
			JFile::delete($cssPath);
		}
		try {
			lessc::ccompile($lessPath, $cssPath);
		}
		catch (exception $e) {
			throw new Exception(JText::sprintf('PLG_SYSTEM_JMFRAMEWORK_LESS_ERROR', $e->getMessage()));
		}
		
		return JMF_TPL_URL. '/css/' . $filename.'.css';
	}
}
