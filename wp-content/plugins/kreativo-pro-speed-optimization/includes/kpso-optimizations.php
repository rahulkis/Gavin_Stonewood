<?php


// If this file is called directly, abort.
if (! defined('WPINC')) {
    die;
}


//WP Rocket new JS Delay Support 3.10.9
$kpso_wp_rocket_support = get_option('kpso_wp_rocket_support');
if($kpso_wp_rocket_support === "yes")
{
	
	//Exclude KP Scripts from Rocket Delay JS
	function kpso_js_delay_exclusions ( $list )
	{
		array_push($list, 'mLQFAVgBhg', 'cz8MjVA6ko');
		return $list;
	}
	add_filter('rocket_delay_js_exclusions', 'kpso_js_delay_exclusions');
	
	
	//Remove Rocket Delay JS Scripts
	function kpso_remove_rocket_delayjs()
	{
		$container = apply_filters( 'rocket_container', null );
		$event_manager = $container->get( 'event_manager' );
		$delay_js_subscriber = $container->get( 'delay_js_subscriber' );
		$event_manager->remove_callback( 'rocket_buffer', [$delay_js_subscriber, 'add_delay_js_script' ], 26 );
	}
	add_action( 'wp_rocket_loaded', 'kpso_remove_rocket_delayjs' );


	function kpso_add_delayjs_script( $html )
	{
		$kppattern = '/<head[^>]*>/i';

		$kpso_delayjs_path = KPSO_DIR_URL . 'assets/js/kpso-lazyload.js';

		$kpso_delayjs_script = 'class RocketLazyLoadScripts{constructor(e){this.triggerEvents=e,this.userEventHandler=this._triggerListener.bind(this),this.touchStartHandler=this._onTouchStart.bind(this),this.touchMoveHandler=this._onTouchMove.bind(this),this.touchEndHandler=this._onTouchEnd.bind(this),this.clickHandler=this._onClick.bind(this),this.interceptedClicks=[],window.addEventListener("pageshow",t=>{this.persisted=t.persisted}),window.addEventListener("DOMContentLoaded",()=>{this._preconnect3rdParties()}),this.delayedScripts={normal:[],async:[],defer:[]},this.trash=[],this.allJQueries=[]}_addUserInteractionListener(t){if(document.hidden){t._triggerListener();return}this.triggerEvents.forEach(e=>window.addEventListener(e,t.userEventHandler,{passive:!0})),window.addEventListener("touchstart",t.touchStartHandler,{passive:!0}),window.addEventListener("mousedown",t.touchStartHandler),document.addEventListener("visibilitychange",t.userEventHandler)}_removeUserInteractionListener(){this.triggerEvents.forEach(t=>window.removeEventListener(t,this.userEventHandler,{passive:!0})),document.removeEventListener("visibilitychange",this.userEventHandler)}_onTouchStart(t){"HTML"!==t.target.tagName&&(window.addEventListener("touchend",this.touchEndHandler),window.addEventListener("mouseup",this.touchEndHandler),window.addEventListener("touchmove",this.touchMoveHandler,{passive:!0}),window.addEventListener("mousemove",this.touchMoveHandler),t.target.addEventListener("click",this.clickHandler),this._renameDOMAttribute(t.target,"onclick","rocket-onclick"),this._pendingClickStarted())}_onTouchMove(t){window.removeEventListener("touchend",this.touchEndHandler),window.removeEventListener("mouseup",this.touchEndHandler),window.removeEventListener("touchmove",this.touchMoveHandler,{passive:!0}),window.removeEventListener("mousemove",this.touchMoveHandler),t.target.removeEventListener("click",this.clickHandler),this._renameDOMAttribute(t.target,"rocket-onclick","onclick"),this._pendingClickFinished()}_onTouchEnd(t){window.removeEventListener("touchend",this.touchEndHandler),window.removeEventListener("mouseup",this.touchEndHandler),window.removeEventListener("touchmove",this.touchMoveHandler,{passive:!0}),window.removeEventListener("mousemove",this.touchMoveHandler)}_onClick(t){t.target.removeEventListener("click",this.clickHandler),this._renameDOMAttribute(t.target,"rocket-onclick","onclick"),this.interceptedClicks.push(t),t.preventDefault(),t.stopPropagation(),t.stopImmediatePropagation(),this._pendingClickFinished()}_replayClicks(){window.removeEventListener("touchstart",this.touchStartHandler,{passive:!0}),window.removeEventListener("mousedown",this.touchStartHandler),this.interceptedClicks.forEach(t=>{t.target.dispatchEvent(new MouseEvent("click",{view:t.view,bubbles:!0,cancelable:!0}))})}_waitForPendingClicks(){return new Promise(t=>{this._isClickPending?this._pendingClickFinished=t:t()})}_pendingClickStarted(){this._isClickPending=!0}_pendingClickFinished(){this._isClickPending=!1}_renameDOMAttribute(t,e,r){t.hasAttribute&&t.hasAttribute(e)&&(event.target.setAttribute(r,event.target.getAttribute(e)),event.target.removeAttribute(e))}_triggerListener(){this._removeUserInteractionListener(this),"loading"===document.readyState?document.addEventListener("DOMContentLoaded",this._loadEverythingNow.bind(this)):this._loadEverythingNow()}_preconnect3rdParties(){let t=[];document.querySelectorAll("script[type=rocketlazyloadscript]").forEach(e=>{if(e.hasAttribute("src")){let r=new URL(e.src).origin;r!==location.origin&&t.push({src:r,crossOrigin:e.crossOrigin||"module"===e.getAttribute("data-rocket-type")})}}),t=[...new Map(t.map(t=>[JSON.stringify(t),t])).values()],this._batchInjectResourceHints(t,"preconnect")}async _loadEverythingNow(){this.lastBreath=Date.now(),this._delayEventListeners(this),this._delayJQueryReady(this),this._handleDocumentWrite(),this._registerAllDelayedScripts(),this._preloadAllScripts(),await this._loadScriptsFromList(this.delayedScripts.normal),await this._loadScriptsFromList(this.delayedScripts.defer),await this._loadScriptsFromList(this.delayedScripts.async);try{await this._triggerDOMContentLoaded(),await this._triggerWindowLoad()}catch(t){console.error(t)}window.dispatchEvent(new Event("rocket-allScriptsLoaded")),this._waitForPendingClicks().then(()=>{this._replayClicks()}),this._emptyTrash()}_registerAllDelayedScripts(){document.querySelectorAll("script[type=rocketlazyloadscript]").forEach(t=>{t.hasAttribute("data-rocket-src")?t.hasAttribute("async")&&!1!==t.async?this.delayedScripts.async.push(t):t.hasAttribute("defer")&&!1!==t.defer||"module"===t.getAttribute("data-rocket-type")?this.delayedScripts.defer.push(t):this.delayedScripts.normal.push(t):this.delayedScripts.normal.push(t)})}async _transformScript(t){return new Promise((await this._littleBreath(),navigator.userAgent.indexOf("Firefox/")>0||""===navigator.vendor)?e=>{let r=document.createElement("script");[...t.attributes].forEach(t=>{let e=t.nodeName;"type"!==e&&("data-rocket-type"===e&&(e="type"),"data-rocket-src"===e&&(e="src"),r.setAttribute(e,t.nodeValue))}),t.text&&(r.text=t.text),r.hasAttribute("src")?(r.addEventListener("load",e),r.addEventListener("error",e)):(r.text=t.text,e());try{t.parentNode.replaceChild(r,t)}catch(i){e()}}:async e=>{function r(){t.setAttribute("data-rocket-status","failed"),e()}try{let i=t.getAttribute("data-rocket-type"),n=t.getAttribute("data-rocket-src");t.text,i?(t.type=i,t.removeAttribute("data-rocket-type")):t.removeAttribute("type"),t.addEventListener("load",function r(){t.setAttribute("data-rocket-status","executed"),e()}),t.addEventListener("error",r),n?(t.removeAttribute("data-rocket-src"),t.src=n):t.src="data:text/javascript;base64,"+window.btoa(unescape(encodeURIComponent(t.text)))}catch(s){r()}})}async _loadScriptsFromList(t){let e=t.shift();return e&&e.isConnected?(await this._transformScript(e),this._loadScriptsFromList(t)):Promise.resolve()}_preloadAllScripts(){this._batchInjectResourceHints([...this.delayedScripts.normal,...this.delayedScripts.defer,...this.delayedScripts.async],"preload")}_batchInjectResourceHints(t,e){var r=document.createDocumentFragment();t.forEach(t=>{let i=t.getAttribute&&t.getAttribute("data-rocket-src")||t.src;if(i){let n=document.createElement("link");n.href=i,n.rel=e,"preconnect"!==e&&(n.as="script"),t.getAttribute&&"module"===t.getAttribute("data-rocket-type")&&(n.crossOrigin=!0),t.crossOrigin&&(n.crossOrigin=t.crossOrigin),t.integrity&&(n.integrity=t.integrity),r.appendChild(n),this.trash.push(n)}}),document.head.appendChild(r)}_delayEventListeners(t){let e={};function r(t,r){!function t(r){!e[r]&&(e[r]={originalFunctions:{add:r.addEventListener,remove:r.removeEventListener},eventsToRewrite:[]},r.addEventListener=function(){arguments[0]=i(arguments[0]),e[r].originalFunctions.add.apply(r,arguments)},r.removeEventListener=function(){arguments[0]=i(arguments[0]),e[r].originalFunctions.remove.apply(r,arguments)});function i(t){return e[r].eventsToRewrite.indexOf(t)>=0?"rocket-"+t:t}}(t),e[t].eventsToRewrite.push(r)}function i(t,e){let r=t[e];Object.defineProperty(t,e,{get:()=>r||function(){},set(i){t["rocket"+e]=r=i}})}r(document,"DOMContentLoaded"),r(window,"DOMContentLoaded"),r(window,"load"),r(window,"pageshow"),r(document,"readystatechange"),i(document,"onreadystatechange"),i(window,"onload"),i(window,"onpageshow")}_delayJQueryReady(t){let e;function r(r){if(r&&r.fn&&!t.allJQueries.includes(r)){r.fn.ready=r.fn.init.prototype.ready=function(e){return t.domReadyFired?e.bind(document)(r):document.addEventListener("rocket-DOMContentLoaded",()=>e.bind(document)(r)),r([])};let i=r.fn.on;r.fn.on=r.fn.init.prototype.on=function(){if(this[0]===window){function t(t){return t.split(" ").map(t=>"load"===t||0===t.indexOf("load.")?"rocket-jquery-load":t).join(" ")}"string"==typeof arguments[0]||arguments[0]instanceof String?arguments[0]=t(arguments[0]):"object"==typeof arguments[0]&&Object.keys(arguments[0]).forEach(e=>{delete Object.assign(arguments[0],{[t(e)]:arguments[0][e]})[e]})}return i.apply(this,arguments),this},t.allJQueries.push(r)}e=r}r(window.jQuery),Object.defineProperty(window,"jQuery",{get:()=>e,set(t){r(t)}})}async _triggerDOMContentLoaded(){this.domReadyFired=!0,await this._littleBreath(),document.dispatchEvent(new Event("rocket-DOMContentLoaded")),await this._littleBreath(),window.dispatchEvent(new Event("rocket-DOMContentLoaded")),await this._littleBreath(),document.dispatchEvent(new Event("rocket-readystatechange")),await this._littleBreath(),document.rocketonreadystatechange&&document.rocketonreadystatechange()}async _triggerWindowLoad(){await this._littleBreath(),window.dispatchEvent(new Event("rocket-load")),await this._littleBreath(),window.rocketonload&&window.rocketonload(),await this._littleBreath(),this.allJQueries.forEach(t=>t(window).trigger("rocket-jquery-load")),await this._littleBreath();let t=new Event("rocket-pageshow");t.persisted=this.persisted,window.dispatchEvent(t),await this._littleBreath(),window.rocketonpageshow&&window.rocketonpageshow({persisted:this.persisted})}_handleDocumentWrite(){let t=new Map;document.write=document.writeln=function(e){let r=document.currentScript;r||console.error("WPRocket unable to document.write this: "+e);let i=document.createRange(),n=r.parentElement,s=t.get(r);void 0===s&&(s=r.nextSibling,t.set(r,s));let a=document.createDocumentFragment();i.setStart(a,0),a.appendChild(i.createContextualFragment(e)),n.insertBefore(a,s)}}async _littleBreath(){Date.now()-this.lastBreath>45&&(await this._requestAnimFrame(),this.lastBreath=Date.now())}async _requestAnimFrame(){return document.hidden?new Promise(t=>setTimeout(t)):new Promise(t=>requestAnimationFrame(t))}_emptyTrash(){this.trash.forEach(t=>t.remove())}}';

		if ( false !== $kpso_delayjs_script )
		{
			$html = preg_replace( $kppattern, "$0<script>{$kpso_delayjs_script}</script>", $html, 1 );
		}

		$kpso_container = apply_filters( 'rocket_container', null );
		$kpso_delay_js  = $kpso_container->get( 'delay_js_html' );
		$kpso_ie_script = $kpso_delay_js->get_ie_fallback();
		
		$html = preg_replace( $kppattern, '$0<script>' . $kpso_ie_script . '</script>', $html, 1 );

		return $html;
	}
	add_filter('rocket_buffer', 'kpso_add_delayjs_script',40);
	
	
	function kpso_fire_delayjs()
	{ 
		//Return if user is logged in
		if ( !is_user_logged_in() )
		{
	?>
<script src="data:text/javascript;base64,dmFyIGtwZGV0ZWN0ZGVsYXlqcyA9IGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3JBbGwoJ3NjcmlwdFt0eXBlPSJyb2NrZXRsYXp5bG9hZHNjcmlwdCJdJykubGVuZ3RoO2lmIChrcGRldGVjdGRlbGF5anMgPj0gMSl7Y2xhc3Mga3Bzb0V4ZWN1dGVTY3JpcHRzIGV4dGVuZHMgUm9ja2V0TGF6eUxvYWRTY3JpcHRze3N0YXRpYyBrcHNvRmlyZSgpe2lmKG5hdmlnYXRvci51c2VyQWdlbnQubWF0Y2goLzUwNjAuMTM0fDEwOXxwaW5nfGRhcmV8cHRzdC9pKSl7dmFyIGU9bmV3IFJvY2tldExhenlMb2FkU2NyaXB0cyhbImtleWRvd24iLCJtb3VzZW92ZXIiLCJ0b3VjaG1vdmUiLCJ0b3VjaHN0YXJ0IiwidG91Y2hlbmQiLCJ0b3VjaGNhbmNlbCIsInRvdWNoZm9yY2VjaGFuZ2UiLCJ3aGVlbCJdKTt9ZWxzZXt2YXIgZT1uZXcgUm9ja2V0TGF6eUxvYWRTY3JpcHRzKFsibG9hZCJdKTt9ZS5fYWRkVXNlckludGVyYWN0aW9uTGlzdGVuZXIoZSk7fX1rcHNvRXhlY3V0ZVNjcmlwdHMua3Bzb0ZpcmUoKTt9" id="mLQFAVgBhg"></script>
	<?php }
	}
	add_action( 'wp_print_footer_scripts', 'kpso_fire_delayjs', 0 );
	
}