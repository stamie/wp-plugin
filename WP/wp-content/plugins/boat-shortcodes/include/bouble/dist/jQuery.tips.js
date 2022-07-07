/*! CopyRight: Aisin http://github.com/aisin/Tips, Licensed under: MIT */

;(function(jQuery, win){

	jQuery.fn.tips = function(options){

		var defaults = {
			
			destroy : false,
			skin    : 'black',
			msg     : ''
			
		};

		var data = jQuery.extend( {}, defaults, options || {} );

		return this.each(function(){

			var jQuerythis = jQuery(this);
			
			if (data.destroy) {
			
				jQuerythis.unbind('hover');
				return false;
				
			}

			jQuerythis.hover(function(){

				// Get the element's params
				var left = jQuerythis.offset().left;
				var top = jQuerythis.offset().top;
				var height = jQuerythis.outerHeight();

				var jQuerytipDom = jQuery('<div class="tip"><div class="tip-inner"><div class="tip-content"><div class="tip-msg">'+ data.msg +'</div></div></div><div class="arrow arrow-back"></div><div class="arrow arrow-front"></div></div>');

				jQuerytipDom.css({ left : left }).appendTo('body');

				var jQuerytip = jQuery('.tip');
				var tipHeight = jQuerytip.outerHeight();
				var scrollTop = jQuery(win).scrollTop();

				if(top - scrollTop < tipHeight){
				
					top += height;
					jQuerytip.addClass('bottom');
					
				} else { 
				
					top -= tipHeight;
					jQuerytip.addClass('top');
					
				}

				jQuerytip.addClass(data.skin).css({ top : top }).fadeIn(300);

			}, function(){

				jQuery('.tip').fadeOut(300, function(){ jQuery(this).remove() });
				
			});
		});
	}
})(jQuery, window);
