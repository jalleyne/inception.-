/**
 * Copyright 2011 Jovan Alleyne <me@jalleyne.ca>.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may
 * not use this file except in compliance with the License. You may obtain
 * a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations
 * under the License.
 */


/**
 * Helper script for browser app interactions
 *
 * @author Jovan Alleyne <me@jalleyne.ca>
 */

/**
*
*/
var inception = i = (function($){

	return {
		
		/**
		* Inception initialization
		*/
		init : function(data){
			
			/* */
			inception.data = $.extend(
								inception.data,
								data
							);
			
			/* initialize dynamic ajax page loading */
			if( !data.disable_dynamic_page_requests ){
				/* */
				var loc = window.location;
				if( !loc.hash.length ){
					var l = loc.protocol
					+'//'+loc.host
					+'#!'+loc.pathname
					+(loc.search.length?('?'+loc.search):'');

					window.location = l;
					return null;
				}
			}
			
			/* */
			$(inception.ready);
		},
		
		/**
		* Inception on document ready initialization 
		*/
		ready : function($){
			/* */
			if( typeof $.upgradebrowsers == 'function' )
				$.upgradebrowsers();
			
			if( !inception.data.disable_dynamic_page_requests ){
				/* */
				$(window).bind('hashchange', function() {
					inception.page.load(window.location.hash);
				}).trigger('hashchange');
			}
		}
	};	
})(jQuery);




/**
* User interface helper functions
*/
inception.ui 	= (function(){
	
	function showDialog(content){
		$('.window').fadeIn('fast');
	}
	
	function closeDialog(closer){
		$('.window').fadeOut('fast');
	}
	
	var popup_win;
	function openWindow(url, window_name, window_settings){
		/* */
		var settings = $.extend({
			'location' 		: 0,
			'status'		: 0,
			'scrollbars'	: 0,
			'width'			: 300,
			'height'		: 400
		},window_settings);
		
		/* */
		closeWindow();
		
		/* */
		popup_win = window.open(
						url, 
						window_name||'inception_popup', 
						inception.fn.toPropertyString(
								settings,
								', '
							)
					);
		popup_win.moveTo( 
			(screen.width/2)-(settings.width/2), 
			(screen.height/2)-(settings.height/2)
		);
	}
	
	function closeWindow(){
		/* */
		if( popup_win && typeof popup_win.close == 'function' ) 
			popup_win.close();
	}
	
	return {
		showDialog 	: showDialog,
		closeDialog : closeDialog,
		openPopup	: openWindow,
		closePopup 	: closeWindow
	};
})();




/**
* Form helper functions
*/
inception.forms = (function(){
	
	function reloadCaptchaImage(){
		$.get($('#captcha').data('captchaurl'),function(data){
			$('#captcha img').attr('src',data);
		});
	}
	
	
	return {
		refreshCaptcha 	: reloadCaptchaImage
	}
})();



/**
* API helper functions
*/
inception.api = (function(){
	
	function api(method,url,data,
		completeCallback,errorCallback){
		$.ajax({
		  type		: method,
		  url		: inception.data.api+url,
		  data		: data,
		  dataType 	: 'json',
		  success	: completeCallback,
		  error		: function(resp){ 
						errorCallback(JSON.parse(resp.responseText)); 
					}
		});
	}
	
	return {
		get : (function(url,data,
			completeCallback,errorCallback){
			api('GET',
				url,
				data,
				completeCallback,
				errorCallback
			);
		}),
		
		post : (function(url,data,
			completeCallback,errorCallback){
			api('POST',
				url,
				data,
				completeCallback,
				errorCallback
			);
		}),
		
		put : (function(url,data,
			completeCallback,errorCallback){
			api('PUT',
				url,
				data,
				completeCallback,
				errorCallback
			);
		}),
		
		"delete" : (function(url,data,
			completeCallback,errorCallback){
			api('DELETE',
				url,
				data,
				completeCallback,
				errorCallback
			);
		})
		
	};
})();


/**
* Uploads helper functions
*/
inception.uploads = (function(){
	return {
		
		asyncUploadCompleteCallback : (function(){
			/* */
			
		}),
		
		asyncUpload : (function(file){
			file = $(file);
			if( file.val() ){
				file.closest('form').addClass('wait').submit();
				file.attr('disabled','disabled');
			}
		})
	};
})();



/**
* Page loaded/ content update helper methods
*/
inception.page = (function(){
	
	function updateSection(section,uri,arg3,arg4){
		/* */
		var params = {r:uri};
		if( typeof arg3 == 'object' )
			 $.extend( params, arg3 );
		else if( typeof arg3 == 'function' )
			var callback = arg3;
		else if( typeof arg4 == 'function' )
			var callback = arg4;
		/* */
		$.get(inception.data.page_request_proxy_url,params,function(data){
			$(section).html(data);
			if( typeof callback == 'function' ) callback();
		},'html').error(function(data){
			switch(parseInt(data.status)){
			case 403:
				inception.page.load( inception.data.auth_denied_redirect );
				break;
			}
		});;
	}
	
	function loadPage(link,page_target){
		/* */
		$.get(
			inception.data.page_request_proxy_url,
			{
				r:link
			},function(data){
			/* */
			inception.page.write( data, page_target );
			/* */
			_gaq.push([
				'_trackPageview',
				link.replace('#!','')
			]);
		},'html').error(function(data){
			/* */
			switch(parseInt(data.status)){
			case 500:
			case 404:
				inception.page.load( 
					inception.data.page_not_found_redirect, 
					page_target
				);
				break;
			case 403:
				inception.page.load( 
					inception.data.auth_denied_redirect, 
					page_target
				);
				break;
				
			}
		});
	};
	
	function writePage(html_string,container){
		/* */
		var html = $(html_string);
		
		/* */
		if( $('header',html).length ){
			if( $('header').html() != $('header',html).html() ){
				$('header').html(
					$('header',html).html()
				);
			}
		}
		else $('header').html('');
		
		/* */
		container = container||'#main';
		
		/* */
		$(container).html(
			$(container+' *',html).html()
		);
		
		
		/* */
		if( $(html).find('footer').length ){
			if( $('footer').html() != $('footer',html).html() ){
				$('footer').html(
					$('footer',html).html()
				);
			}
		}
		else $('footer').html('');
		
		/* */
		$('a[href^="/"]').not('a[target^="_"]').each(function(){
			$(this).attr(
				'href',
				'#!'+$(this).attr('href')
			);	
		});
	}
	
	return {
		load 	: loadPage,
		write 	: writePage,
		update	: updateSection
	};
})();


/**
* Social Media helper functions
*/
inception.social = (function(){
	
	function fbShare(url,message){
		inception.ui.openPopup(
			'https://www.facebook.com/sharer/sharer.php?u='
			+encodeURIComponent(url)
			+'&t='+encodeURIComponent(message),
			'fb-share',
			{
				'width' : 600,
				'height': 400
			}
		);
	}
	
	function fbFeedPublish(link,name,picture,caption,description,message){
		if( FB && typeof FB.ui == 'function' ){
			FB.ui({
	            method		: 'feed',
	            link		: link || document.location.href,
	            picture		: picture || $('meta[property="og:image"]').attr('content'),
	            name		: name,
	            caption		: caption,
	            message		: message,
	            description	: description || $('meta[property="og:description"]').attr('content')
	          });
		}
	}
	
	function fbSend(){
		if( FB && typeof FB.ui == 'function' ){
			FB.ui({
		          method: 'send',
		          display: 'popup',
		          name: $('title').text(),
		          link: link
	          });
		}
	}
	
	
	function tweet(tweet){
		inception.ui.openPopup(
			'http://twitter.com/intent/tweet?text='
			+encodeURIComponent(tweet),
			'tweet-share',
			{
				'width' : 600,
				'height': 300
			}
		);
	}
	
	return {
		fbShare 		: fbShare,
		fbFeedPublish 	: fbFeedPublish,
		fbSend 			: fbSend,
		tweet			: tweet
	};
})();


/**
* Generic helper functions
*/
inception.fn = (function(){
	
	function toPropertyString(obj,delimiter){
		var str = [];
		  for(var p in obj)
		     str.push(p+"="+obj[p]);
		  return str.join(delimiter||'&');
	}
	
	return {
		toPropertyString : toPropertyString
	};
})();


/**
* Inceptions properties
*/
inception.data = (function(){
	return {
		
	};
})();


if( typeof window.inceptionAsyncInit == 'function' )
{
	window.inceptionAsyncInit();
}


