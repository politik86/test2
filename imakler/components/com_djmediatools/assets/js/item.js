/**
 * @version $Id: item.js 16 2013-07-30 09:59:57Z szymon $
 * @package DJ-MediaTools
 * @subpackage DJ-MediaTools galleryGrid layout
 * @copyright Copyright (C) 2012 DJ-Extensions.com, All rights reserved.
 * @license DJ-Extensions.com Proprietary Use License
 * @author url: http://dj-extensions.com
 * @author email contact@dj-extensions.com
 * @developer Szymon Woronowski - szymon.woronowski@design-joomla.eu
 *
 */

function centerImage(){
	var wrapper = document.id('djmediatools').getElement('.dj-album-image');
	var image = wrapper.getElement('div,img');
	if(image) {
		var margin = (wrapper.getSize().y - image.getSize().y) / 2;
		image.set('tween',{duration:'short',transition:'expo:out'});
		if(margin > 0) {
			image.tween('margin-top', margin);
		} else {
			image.tween('margin-top', 0);
		}
	}
}

window.addEvent('load',function(){
	(function(){
		window.addEvent('resize',centerImage);
		window.fireEvent('resize');
	}).delay(50);
});
