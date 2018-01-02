// cross-browser asynchronous script loading for zxcvbn.
// adapted from http://friendlybit.com/js/lazy-loading-asyncronous-javascript/

(function() {
	var scripts = document.querySelectorAll( 'script[src]' );
	var currentScript = scripts[ scripts.length - 1 ].src;
	var currentScriptChunks = currentScript.split( '/' );
	var currentScriptFile = currentScriptChunks[ currentScriptChunks.length - 1 ];
	var currentScriptPath = currentScript.replace( currentScriptFile, '' );
	
	var ZXCVBN_SRC = currentScriptPath+'/zxcvbn.js';   // eg. for a standard bower setup, 'bower_components/zxcvbn/zxcvbn.js'
	
	var async_load = function() {
		var first, s;
		s = document.createElement('script');
		s.src = ZXCVBN_SRC;
		s.type = 'text/javascript';
		s.async = true;
		first = document.getElementsByTagName('script')[0];
		return first.parentNode.insertBefore(s, first);
	};
	
	if (window.attachEvent != null) {
		window.attachEvent('onload', async_load);
	} else {
		window.addEventListener('load', async_load, false);
	}
}).call(this);