<?php
/**
* @version		2.0
* @package		DJ Classifieds
* @subpackage	DJ Classifieds Component
* @copyright	Copyright (C) 2010 DJ-Extensions.com LTD, All rights reserved.
* @license		http://www.gnu.org/licenses GNU/GPL
* @autor url    http://design-joomla.eu
* @autor email  contact@design-joomla.eu
* @Developer    Lukasz Ciastek - lukasz.ciastek@design-joomla.eu
* 
* 
* DJ Classifieds is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* DJ Classifieds is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with DJ Classifieds. If not, see <http://www.gnu.org/licenses/>.
* 
*/
defined ('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');
jimport('joomla.html.pagination');

class DJClassifiedsViewItems extends JViewLegacy{

	public function __construct($config = array())
	{
		parent::__construct($config);
		$this->_addPath('template', JPATH_COMPONENT.  '/themes/default/views/items');
		$par = JComponentHelper::getParams( 'com_djclassifieds' );
		$theme = $par->get('theme','default');
		if ($theme && $theme != 'default') {
			$this->_addPath('template', JPATH_COMPONENT.  '/themes/'.$theme.'/views/items');
		}
	}	
	
	function display($tpl = null){
		JHTML::_( 'behavior.modal' );		
		$document =  JFactory::getDocument();
		$par 	  = JComponentHelper::getParams( 'com_djclassifieds' );
		$app	  = JFactory::getApplication();		
		$user 	  = JFactory::getUser();		
		$model 	  = $this->getModel();
		
		$cat_id	  = JRequest::getVar('cid', 0, '', 'int');
		$uid	  = JRequest::getVar('uid', 0, '', 'int');
		$se		  = JRequest::getVar('se', 0, '', 'int');
		$reset	  = JRequest::getVar('reset', 0, '', 'int');		
		$layout   = JRequest::getVar('layout','');		
		$order    = JRequest::getCmd('order', $par->get('items_ordering','date_e'));
		$ord_t    = JRequest::getCmd('ord_t', $par->get('items_ordering_dir','desc'));
		$theme 	  = $par->get('theme','default');
		
		if($layout=='favourites'){
			if($user->id=='0'){
				$uri = JFactory::getURI();
				$app->redirect('index.php?option=com_users&view=login&return='.base64_encode($uri),JText::_('COM_DJCLASSIFIEDS_PLEASE_LOGIN'));
			}			
			JRequest::setVar('fav','1');						
		}
		
		if($reset){
			$items= $model->resetSearchFilters();	
		}
		
		$catlist = ''; 
		if($cat_id>0){
			$cats= DJClassifiedsCategory::getSubCatIemsCount($cat_id,1,$par->get('subcats_ordering', 'ord'),$par->get('subcats_hide_empty', 0));
			$catlist= $cat_id;			
			foreach($cats as $c){
				$catlist .= ','. $c->id;
			}				
		}else{
			$cats= DJClassifiedsCategory::getCatAllItemsCount(1,$par->get('subcats_ordering', 'ord'),$par->get('subcats_hide_empty', 0));
		}
		
		$subcats = '';
		$cat_images='';
		foreach($cats as $c){
			if($c->parent_id==$cat_id){
				$subcats .= $c->id.',';	
			}
		}		
		if($subcats){
			$subcats = substr($subcats, 0, -1);
			$cat_images = $model->getCatImages($subcats); 
		}
		
		
		$items= $model->getItems($catlist);
		$countitems = $model->getCountItems($catlist);
		
		$menus	= $app->getMenu('site');
		$m_active = $menus->getActive();
		$cat_menu_path= array();
		$cid_menu=0;
		if($m_active){
			if(strstr($m_active->link,'com_djclassifieds') && strstr($m_active->link,'items')){
				if(isset($m_active->query['cid'])){
				  	$cid_menu = $m_active->query['cid'];
					if($cid_menu>0){
						$cat_menu_path= DJClassifiedsCategory::getParentPath(1,$cid_menu);	
					}
				}					
			}
		}	
		if($uid>0 || $se || $order != $par->get('items_ordering','date_e') || $ord_t != $par->get('items_ordering_dir','desc')){
			$document->setMetaData('robots','NOINDEX, FOLLOW');
		}
		$cat_id	= JRequest::getVar('cid', 0, '', 'int');
		$main_cat = '';
		if($cat_id>0){
			$main_cat= $model->getMainCat($cat_id);		
					
		if($main_cat->metakey!=''){
			$document->setMetaData('keywords',$main_cat->metakey);
		}else if($m_active){
			if($m_active->params->get('menu-meta_description')){
				$document->setMetaData('keywords',$m_active->params->get('menu-meta_description'));
			 }
		}
		
		if($main_cat->metadesc!=''){
			$document->setDescription($main_cat->metadesc);
		}else if($m_active){
			if($m_active->params->get('menu-meta_keywords')){
				$document->setDescription($m_active->params->get('menu-meta_keywords'));
			}
		}				
			
			$cat_path = array();			
			$pathway =$app->getPathway();
			if($main_cat->parent_id==0){
				if($cid_menu!=$cat_id){					
					$pathway->addItem($main_cat->name);	
				}	
				$cat_path[]= $main_cat;			
			}else{				
				$cat_path= DJClassifiedsCategory::getParentPath(1,$main_cat->id);	
				for($c=count($cat_path);$c>0;$c--){
					$to_b = 1;
					if(count($cat_menu_path)){
						foreach($cat_menu_path as $cm){
							if($cm->id==$cat_path[$c-1]->id){
								$to_b = 0;
								break;			
							}
						}
					}
					if($to_b){
						$pathway->addItem($cat_path[$c-1]->name, DJClassifiedsSEO::getCategoryRoute($cat_path[$c-1]->id.':'.$cat_path[$c-1]->alias));	
					}								
				}					
			}
			$cat_theme='';
			foreach($cat_path as $cp){
				if($cp->theme){
					$cat_theme = $cp->theme;
				}
			}
			DJClassifiedsTheme::includeCSSfiles($cat_theme);
			if($cat_theme){
				$this->_addPath('template', JPATH_COMPONENT.  '/themes/'.$cat_theme.'/views/items');
				$theme=$cat_theme;
			}
			
			$c_title = $document->getTitle();
			$cat_name = $main_cat->name;
			$seo_cat_path = ''; 
			$seo_title_separator = $par->get('seo_title_separator', ' - ');
			foreach($cat_path as $cp){
				if($seo_cat_path){
					$seo_cat_path .= $seo_title_separator;
				}
				$seo_cat_path .= $cp->name;					
			}
			
			$seo_title_from = array('|','<default_title>','<category_name>','<category_path>');
			$seo_title_to = array($seo_title_separator,$c_title,$cat_name,$seo_cat_path);
			$seo_title = str_ireplace($seo_title_from, $seo_title_to, $par->get('seo_title_items', '<category_path>|<default_title>'));
			$document->setTitle($seo_title);						
		}else if($se && isset($_GET['se_cats'])){
			$se_cat_id= end($_GET['se_cats']);
			if($se_cat_id=='' && count($_GET['se_cats'])>2){
				$se_cat_id =$_GET['se_cats'][count($_GET['se_cats'])-2];
			}
			$se_cat_id = str_ireplace('p', '', $se_cat_id);
			if($se_cat_id>0){
				$main_cat= $model->getMainCat($se_cat_id);
			}					
		}	 			
		

		$re	= JRequest::getVar('re', 0, '', 'int');
		$reg_id = 0;
		
		if(isset($_GET['se_regs'])){
            if (count($_GET['se_regs'])>1){
                    $main_reg = '';                  
            }
            else{
                $reg_id= end($_GET['se_regs']);						
                if($reg_id=='' && count($_GET['se_regs'])>2){
                    $reg_id =$_GET['se_regs'][count($_GET['se_regs'])-2];
                }
                $reg_id=(int)$reg_id;

                if($reg_id){
                    $main_reg= $model->getMainRegions($reg_id);	
                }else{
                    $main_reg = '';
                }
            }
		}
		

		
		if($uid>0){
			$u_name = $model->getUserName($uid);
			$this->assignRef('u_name', $u_name);
		}
		
		if($par->get('show_types','0')){
			$types = $model->getTypes();
			$this->assignRef('types', $types);
		}
		
		
		$limit	= JRequest::getVar('limit', $par->get('limit_djitem_show'), '', 'int');
		$limitstart	= JRequest::getVar('limitstart', 0, '', 'int');
		$pagination = new JPagination( $countitems, $limitstart, $limit );

	//	$main_name = $model->makePathway($cat_list);

		if ($par->get('rss_feed', 1) == 1){
			$link	= '&format=feed&limitstart=';
			$attribs = array('type' => 'application/rss+xml', 'title' => 'RSS 2.0');
			$document->addHeadLink(JRoute::_($link.'&type=rss'), 'alternate', 'rel', $attribs);
			$attribs = array('type' => 'application/atom+xml', 'title' => 'Atom 1.0');
			$document->addHeadLink(JRoute::_($link.'&type=atom'), 'alternate', 'rel', $attribs);
		}				
		
		$custom_fields = $model->getCustomFields();
			
		$this->assignRef('cats',$cats);
		$this->assignRef('cat_path',$cat_path);
		$this->assignRef('cat_images',$cat_images);
		$this->assignRef('items', $items);
		$this->assignRef('custom_fields',$custom_fields);
		$this->assignRef('countitems', $countitems);		
		$this->assignRef('main_cat', $main_cat);
		$this->assignRef('main_reg', $main_reg);
		$this->assignRef('pagination', $pagination);
		$this->assignRef('theme',$theme);

		parent::display('cat');			
        parent::display($tpl);
	}

}




