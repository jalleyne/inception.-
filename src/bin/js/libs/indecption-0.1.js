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
			i.props = $.extend(i.data,data);
			
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
				
			
			inception.api.get('/auth/status/',null,function(resp){
				console.log(resp);
			},function(resp){
				console.log(resp);				
			});
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
			'height'		: 400,
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
			(screen.width/2)-(w/2), 
			(screen.height/2)-(h/2)
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
	
	function api(method,url,data,completeCallback,errorCallback){
		$.ajax({
		  type		: method,
		  url		: inception.data.api+url,
		  data		: data,
		  dataType 	: 'json',
		  success	: completeCallback,
		  error		: errorCallback
		});
	}
	
	return {
		get : (function(url,data,completeCallback,errorCallback){
			api('GET',
				url,
				data,
				completeCallback,errorCallback
			);
		}),
		
		post : (function(url,data,completeCallback,errorCallback){
			api('POST',
				url,
				data,
				completeCallback,errorCallback
			);
		}),
		
		put : (function(url,data,completeCallback,errorCallback){
			api('PUT',
				url,
				data,
				completeCallback,errorCallback
			);
		}),
		
		delete : (function(url,data,completeCallback,errorCallback){
			api('DELETE',
				url,
				data,
				completeCallback,errorCallback
			);
		})
		
	};
})();


/**
* Uploads helper functions
*/
inception.uploads = (function(){
	return {
		
		syncyUploadCompleteCallback : (function(){
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
				fchc.page.load( inception.data.auth_denied_redirect );
				break;
			}
		});;
	}
	
	function loadPage(link){
		/* */
		$.get(inception.data.page_request_proxy_url,{r:link},function(data){
			/* */
			inception.page.write( data );
			/* */
			_gaq.push(['_trackPageview',page]);
		},'html').error(function(data){
			/* */
			switch(parseInt(data.status)){
			case 500:
			case 404:
				inception.page.load( inception.data.page_not_found_redirect );
				break;
			case 403:
				inception.page.load( inception.data.auth_denied_redirect );
				break;
				
			}
		});
	};
	
	function writePage(html,target){
		$(target||'body').html(html)
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
		inception.ui.openWindow(
			'https://www.facebook.com/sharer/sharer.php?u='+encodeURIComponent(url)+'&t='+encodeURIComponent(message),
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
		inception.ui.openWindow(
			'http://twitter.com/intent/tweet?text='+encodeURIComponent(tweet),
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

