(function(undefined){
	var IE = (function(){
		var v = 3,
			div = document.createElement('div'),
			all = div.getElementsByTagName('i');

		while (
			div.innerHTML = '<!--[if gt IE ' + (++v) + ']><i></i><![endif]-->',
			all[0]
		);

		return v > 4 ? v : undefined;
	}());

	if (IE && IE < 9)
		return;

	var items = [
		{
			name: 'mightySlider &middot; Mighty Responsive Multipurpose Slider',
			description: 'Everything you ever wanted in an animated content and image slider, all packaged up into one awesome plugin! Smooth, powerful, limitless, fully responsive and touch-enabled slider plugin for everyone including designers & developers. Learn how this feature rich plugin is just waiting to amp up your website.',
			url: 'http://mightyslider.com/',
			bgColor: '#fd6a62',
			color: '#EEE',
			priority: 1
		},
		{
			name: 'WordPress Gallery Extra',
			description: 'WordPress Gallery Extra is the most intuitive and extensible gallery management tool ever created for WordPress. It comes with a lot of great features and templates, but at the same time we always try to keep things as simple and intuitive as possible, so that users with little WordPress experience have no problem using it as well.',
			url: 'https://wgextra.iprodev.com/',
			bgColor: '#127db0',
			color: '#d0e4f8',
			priority: 2
		},
		{
			name: 'iLightBox &middot; Revolutionary Lightbox Plugin',
			description: 'iLightBox allows you to easily create the most beautiful overlay windows using the jQuery Javascript library. By combining support for a wide range of media with gorgeous skins and a user-friendly API, iLightBox aims to push the Lightbox concept as far as possible.',
			url: 'http://ilightbox.net/',
			bgColor: '#4696e5',
			color: '#d0e4f8',
			priority: 1
		}
	],
	head = document.head || document.getElementsByTagName('head')[0],
	bulkItems, notifyElement, closeElement, styleElement;

	function getItem() {
		bulkItems = [];

		for (var i = 0, l = items.length; i < l; i++) {
			for (var ai = 0, al = (items[i].priority || 1); ai < al; ai++) {
				bulkItems.push(i);
			};
		};

		return items[bulkItems[Math.floor(Math.random() * bulkItems.length)]];
	}

	function showNotification () {
		if (notifyElement) {
			return;
		}

		var item = getItem();
		var HTML = "<a href=\"" + item.url + "\" target=\"_blank\" class=\"iprodev_item\"><div><h1>" + item.name + "</h1><span>" + item.description + "</span></div></a>";

		//setCSS(item);

		notifyElement = document.createElement('div');
		notifyElement.className = 'iprodev-notification iprodevSlideInUp';
		notifyElement.innerHTML = HTML;

		closeElement = document.createElement('a');
		closeElement.className = 'close_notify';
		closeElement.title = 'Close';

		notifyElement.appendChild(closeElement);
		document.documentElement.appendChild(notifyElement);

		notifyElement.classList.add('iprodevAnimated');

		closeElement.addEventListener('click', closeNotification);
	}

	function closeNotification () {
		if (!notifyElement) {
			return;
		}

		closeElement.removeEventListener('click', closeNotification);
		document.documentElement.removeChild(notifyElement);
		head.removeChild(styleElement);
		notifyElement = undefined;
		styleElement = undefined;
		closeElement = undefined;
	}

	/*function setCSS (item) {
		var css = '@-webkit-keyframes iprodevSlideInUp{0%{-webkit-transform:translate3d(0,100%,0);transform:translate3d(0,100%,0);visibility:visible}100%{-webkit-transform:translate3d(0,0,0);transform:translate3d(0,0,0)}}@keyframes iprodevSlideInUp{0%{-webkit-transform:translate3d(0,100%,0);transform:translate3d(0,100%,0);visibility:visible}100%{-webkit-transform:translate3d(0,0,0);transform:translate3d(0,0,0)}}.iprodevAnimated{-webkit-animation-duration:1s;animation-duration:1s;-webkit-animation-fill-mode:both;animation-fill-mode:both}.iprodevSlideInUp{-webkit-animation-name:iprodevSlideInUp;animation-name:iprodevSlideInUp}';

		css += '.iprodev-notification { text-align: center !important; position: fixed !important; width: 100% !important; height: 140px !important; bottom: 0 !important; background: ' + item.bgColor + ' !important;z-index: 1000000 !important; padding: 0 !important; margin: 0 !important; border: 0 !important; }';
		css += '.iprodev-notification * { font-family: \'Open Sans\', Helvetica, sans-serif; !important; color: ' + item.color + ' !important; line-height: normal !important; padding: 0 !important; margin: 0 !important; border: 0 !important; text-decoration: none !important; -webkit-box-sizing: border-box !important; box-sizing: border-box !important; text-transform: uppercase !important;  }';
		css += '.iprodev-notification a.iprodev_item { position: relative !important; display: block !important; height: 100% !important; }';
		css += '.iprodev-notification a.close_notify { position: absolute !important; opacity: 0; display: block !important; width: 48px !important; height: 48px !important; top: 50% !important; right: 30px !important; -webkit-transform: translateY(-50%); transform: translateY(-50%); cursor: pointer !important; -webkit-transition: opacity 0.3s !important; transition: opacity 0.3s !important; background: url(\'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiA/PjxzdmcgaGVpZ2h0PSIyMnB4IiB2ZXJzaW9uPSIxLjEiIHZpZXdCb3g9IjAgMCAyMiAyMiIgd2lkdGg9IjIycHgiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6c2tldGNoPSJodHRwOi8vd3d3LmJvaGVtaWFuY29kaW5nLmNvbS9za2V0Y2gvbnMiIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIj48dGl0bGUvPjxkZWZzPjxwYXRoIGQ9Ik0xMSwyMiBDMTcuMDc1MTMyNSwyMiAyMiwxNy4wNzUxMzI1IDIyLDExIEMyMiw0LjkyNDg2NzQ1IDE3LjA3NTEzMjUsMCAxMSwwIEM0LjkyNDg2NzQ1LDAgMCw0LjkyNDg2NzQ1IDAsMTEgQzAsMTcuMDc1MTMyNSA0LjkyNDg2NzQ1LDIyIDExLDIyIEwxMSwyMiBaIE0xMSwyMSBDMTYuNTIyODQ3OCwyMSAyMSwxNi41MjI4NDc4IDIxLDExIEMyMSw1LjQ3NzE1MjIzIDE2LjUyMjg0NzgsMSAxMSwxIEM1LjQ3NzE1MjIzLDEgMSw1LjQ3NzE1MjIzIDEsMTEgQzEsMTYuNTIyODQ3OCA1LjQ3NzE1MjIzLDIxIDExLDIxIFogTTYuNzU3MzU5MzEsMTUuOTQ5NzQ3NSBMNi4wNTAyNTI1MywxNS4yNDI2NDA3IEwxMC4yOTI4OTMyLDExIEw2LjA1MDI1MjUzLDYuNzU3MzU5MzEgTDYuNzU3MzU5MzEsNi4wNTAyNTI1MyBMMTEsMTAuMjkyODkzMiBMMTUuMjQyNjQwNyw2LjA1MDI1MjUzIEwxNS45NDk3NDc1LDYuNzU3MzU5MzEgTDExLjcwNzEwNjgsMTEgTDE1Ljk0OTc0NzUsMTUuMjQyNjQwNyBMMTUuMjQyNjQwNywxNS45NDk3NDc1IEwxMSwxMS43MDcxMDY4IEw2Ljc1NzM1OTMxLDE1Ljk0OTc0NzUgWiIgaWQ9InBhdGgtMSIvPjwvZGVmcz48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiIGlkPSJtaXUiIHN0cm9rZT0ibm9uZSIgc3Ryb2tlLXdpZHRoPSIxIj48ZyBpZD0iY2lyY2xlX2Nsb3NlX2RlbGV0ZV9vdXRsaW5lX3N0cm9rZSI+PHVzZSBmaWxsPSIjRkZGRkZGIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiIHhsaW5rOmhyZWY9IiNwYXRoLTEiLz48dXNlIGZpbGw9Im5vbmUiIHhsaW5rOmhyZWY9IiNwYXRoLTEiLz48L2c+PC9nPjwvc3ZnPg==\') no-repeat 50%; background-size: 100% 100%; }';
		css += '.iprodev-notification:hover a.close_notify { opacity: 0.4; }';
		css += '.iprodev-notification a.close_notify:hover { opacity: 1; }';
		css += '.iprodev-notification div { position: absolute !important; padding: 20px 100px 20px 20px !important; top: 50%; left: 50%; -webkit-transform: translate(-50%, -50%); transform: translate(-50%, -50%); display: inline-block !important; width: 100% !important; max-width: 900px !important; text-align: left !important;  }';
		css += '.iprodev-notification h1 { font-size: 20px !important; font-weight: 400 !important; color: #FFF !important; margin-bottom: 5px !important; }';
		css += '.iprodev-notification span { display: block !important; font-size: 14px !important; font-weight: 400 !important; }';
		css += '@media screen and (max-width: 900px){.iprodev-notification {height: 120px!important;}.iprodev-notification h1{font-size:14px!important;font-weight:700!important}.iprodev-notification span{font-size:11px!important;overflow:hidden!important;height:45px!important;}}';
		css += '@media screen and (max-width: 480px){.iprodev-notification span{display:none!important;}}';

		styleElement = document.createElement('style');
		styleElement.type = 'text/css';
		if (styleElement.styleSheet){
			styleElement.styleSheet.cssText = css;
		} else {
			styleElement.appendChild(document.createTextNode(css));
		}

		head.appendChild(styleElement);
	}*/

	window.addEventListener('load', function(){
		setTimeout(showNotification, 3000);
	});
})();