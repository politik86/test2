<?php
/**
 * @version $Id: default.php 19 2013-10-04 21:46:58Z szymon $
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

defined ('_JEXEC') or die; 

$item = $this->slides[$this->current];
?>

<div id="djmediatools" class="dj-album djmediatools<?php echo $this->params->get( 'pageclass_sfx' ); echo ($this->params->get('show_album_title') ? '':' no-title'); ?>">
	
	<?php if($this->params->get('show_album_title')) { ?>
		<h1 class="dj-album-title"><?php echo $this->escape($this->category->title); ?></h1>		
	<?php } ?>
	
	<div class="dj-album-item">
		<div class="dj-album-item-in">
		
			<div class="dj-album-item-desc">
				
				<?php if(!empty($this->category->description) && $this->params->get('show_album_desc')) : ?>
				<div class="dj-album-desc">
					<?php echo JHTML::_('content.prepare', $this->category->description); ?>
				</div>
				<?php endif; ?>
				
				<?php if($this->params->get('show_title')) { ?>
					<h2 class="dj-item-title">
						<?php if($item->link) { ?><a href="<?php echo $item->link; ?>" target="<?php echo ($item->target=='_self' ? '_parent' : $item->target ) ?>"><?php } ?>
							<?php echo $this->escape($item->title); ?>
						<?php if($item->link) { ?></a><?php } ?>
					</h2>
				<?php } ?>
				
				<?php echo JHTML::_('content.prepare', $item->full_desc); ?>
				
				<?php if($this->params->get('show_readmore') && $item->link && !$item->video) { ?>
					<div style="clear: both"></div>
					<div class="dj-readmore-wrapper">
						<a href="<?php echo $item->link; ?>" target="<?php echo ($item->target=='_self' ? '_parent' : $item->target ) ?>" class="dj-readmore"><?php echo ($this->params->get('readmore_text',0) ? $this->params->get('readmore_text') : JText::_('COM_DJMEDIATOOLS_READMORE')); ?></a>
					</div>
				<?php } ?>
				
				<?php if(!empty($this->modules['djmt-item-desc'])) : ?>
				<div class="modules-item-desc">
					<?php echo $this->modules['djmt-item-desc'] ?>
				</div>
				<?php endif; ?>
			</div>
			
			<div class="dj-album-image">
				<?php if(isset($item->video) && !empty($item->video)) { ?>
					<iframe width="100%" height="100%" src="<?php echo $item->video; ?>" frameborder="0" allowfullscreen></iframe>
				<?php } else { ?>
					<img id="dj-image" src="<?php echo $item->image ?>" alt="<?php echo $item->alt ?>" />
				<?php } ?>
				
			</div>
		
		</div>
		
	</div>
	
	<div class="dj-album-navi">
			
		<?php if($this->current > 0) : ?>
			<a class="dj-prev" href="<?php echo JRoute::_('index.php?option=com_djmediatools&view=item&cid='.$this->category->id.'&id='.($this->current - 1).'&tmpl=component') ?>"><?php echo $this->escape($this->slides[$this->current - 1]->title) ?></a>
		<?php endif; ?>
		<?php if($this->current < count($this->slides) - 1) : ?>	
			<a class="dj-next" href="<?php echo JRoute::_('index.php?option=com_djmediatools&view=item&cid='.$this->category->id.'&id='.($this->current + 1).'&tmpl=component') ?>"><?php echo $this->escape($this->slides[$this->current + 1]->title) ?></a>
		<?php endif; ?>
		
		<div class="dj-count"><?php echo JText::sprintf('COM_DJMEDIATOOLS_ITEM_CURRENT_OF_TOTAL', ($this->current+1), count($this->slides)); ?></div>
	</div>
</div>
