
  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
  <script>window.jQuery || document.write('<script src="/js/libs/jquery-1.6.2.min.js"><\/script>')</script>

  <!-- upgrade browser script-->
  <script src="/js/libs/jquery.upgradebrowsers.min.js"></script>
  <!-- end upgrade browser script-->

  <!-- inception script-->
  <script>
  // Load Inception.* asynchronously
  (function(d,inceptionAsyncInit){
     var js = d.createElement('script'); js.async = true; js.id = "inception-script"
     js.src = "/js/libs/indecption-0.1.js";
     d.getElementsByTagName('head')[0].appendChild(js);
	 window.inceptionAsyncInit = inceptionAsyncInit;
   }(document,function(){
		/* */
		inception.init({
			
			'api'					: '/i',
			
			'page_request_proxy_url' : '/pageproxy.php',
			'page_not_found_redirect': '/404/',
			'auth_denied_redirect'	 : '/login/'
		})	
	}));
  </script>
  <!-- end inception script-->

  <!-- scripts concatenated and minified via ant build script-->
  <script defer src="/js/plugins.js"></script>
  <script defer src="/js/script.js"></script>
  <!-- end scripts-->


  <script> // 
    window._gaq = [['_setAccount','<?php echo ANALYTICS_UA?>'],['_trackPageview'],['_trackPageLoadTime']];
    Modernizr.load({
      load: ('https:' == location.protocol ? '//ssl' : '//www') + '.google-analytics.com/ga.js'
    });
  </script>


  <!--[if lt IE 7 ]>
    <script src="//ajax.googleapis.com/ajax/libs/chrome-frame/1.0.3/CFInstall.min.js"></script>
    <script>window.attachEvent('onload',function(){CFInstall.check({mode:'overlay'})})</script>
  <![endif]-->
