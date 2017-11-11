/*! Magnific Popup - v1.1.0 - 2016-02-20
 * http://dimsemenov.com/plugins/magnific-popup/
 * Copyright (c) 2016 Dmitry Semenov; */
!function(a){"function"==typeof define&&define.amd?define(["jquery"],a):a("object"==typeof exports?require("jquery"):window.jQuery||window.Zepto)}(function(a){var b,c,d,e,f,g,h="Close",i="BeforeClose",j="AfterClose",k="BeforeAppend",l="MarkupParse",m="Open",n="Change",o="mfp",p="."+o,q="mfp-ready",r="mfp-removing",s="mfp-prevent-close",t=function(){},u=!!window.jQuery,v=a(window),w=function(a,c){b.ev.on(o+a+p,c)},x=function(b,c,d,e){var f=document.createElement("div");return f.className="mfp-"+b,d&&(f.innerHTML=d),e?c&&c.appendChild(f):(f=a(f),c&&f.appendTo(c)),f},y=function(c,d){b.ev.triggerHandler(o+c,d),b.st.callbacks&&(c=c.charAt(0).toLowerCase()+c.slice(1),b.st.callbacks[c]&&b.st.callbacks[c].apply(b,a.isArray(d)?d:[d]))},z=function(c){return c===g&&b.currTemplate.closeBtn||(b.currTemplate.closeBtn=a(b.st.closeMarkup.replace("%title%",b.st.tClose)),g=c),b.currTemplate.closeBtn},A=function(){a.magnificPopup.instance||(b=new t,b.init(),a.magnificPopup.instance=b)},B=function(){var a=document.createElement("p").style,b=["ms","O","Moz","Webkit"];if(void 0!==a.transition)return!0;for(;b.length;)if(b.pop()+"Transition"in a)return!0;return!1};t.prototype={constructor:t,init:function(){var c=navigator.appVersion;b.isLowIE=b.isIE8=document.all&&!document.addEventListener,b.isAndroid=/android/gi.test(c),b.isIOS=/iphone|ipad|ipod/gi.test(c),b.supportsTransition=B(),b.probablyMobile=b.isAndroid||b.isIOS||/(Opera Mini)|Kindle|webOS|BlackBerry|(Opera Mobi)|(Windows Phone)|IEMobile/i.test(navigator.userAgent),d=a(document),b.popupsCache={}},open:function(c){var e;if(c.isObj===!1){b.items=c.items.toArray(),b.index=0;var g,h=c.items;for(e=0;e<h.length;e++)if(g=h[e],g.parsed&&(g=g.el[0]),g===c.el[0]){b.index=e;break}}else b.items=a.isArray(c.items)?c.items:[c.items],b.index=c.index||0;if(b.isOpen)return void b.updateItemHTML();b.types=[],f="",c.mainEl&&c.mainEl.length?b.ev=c.mainEl.eq(0):b.ev=d,c.key?(b.popupsCache[c.key]||(b.popupsCache[c.key]={}),b.currTemplate=b.popupsCache[c.key]):b.currTemplate={},b.st=a.extend(!0,{},a.magnificPopup.defaults,c),b.fixedContentPos="auto"===b.st.fixedContentPos?!b.probablyMobile:b.st.fixedContentPos,b.st.modal&&(b.st.closeOnContentClick=!1,b.st.closeOnBgClick=!1,b.st.showCloseBtn=!1,b.st.enableEscapeKey=!1),b.bgOverlay||(b.bgOverlay=x("bg").on("click"+p,function(){b.close()}),b.wrap=x("wrap").attr("tabindex",-1).on("click"+p,function(a){b._checkIfClose(a.target)&&b.close()}),b.container=x("container",b.wrap)),b.contentContainer=x("content"),b.st.preloader&&(b.preloader=x("preloader",b.container,b.st.tLoading));var i=a.magnificPopup.modules;for(e=0;e<i.length;e++){var j=i[e];j=j.charAt(0).toUpperCase()+j.slice(1),b["init"+j].call(b)}y("BeforeOpen"),b.st.showCloseBtn&&(b.st.closeBtnInside?(w(l,function(a,b,c,d){c.close_replaceWith=z(d.type)}),f+=" mfp-close-btn-in"):b.wrap.append(z())),b.st.alignTop&&(f+=" mfp-align-top"),b.fixedContentPos?b.wrap.css({overflow:b.st.overflowY,overflowX:"hidden",overflowY:b.st.overflowY}):b.wrap.css({top:v.scrollTop(),position:"absolute"}),(b.st.fixedBgPos===!1||"auto"===b.st.fixedBgPos&&!b.fixedContentPos)&&b.bgOverlay.css({height:d.height(),position:"absolute"}),b.st.enableEscapeKey&&d.on("keyup"+p,function(a){27===a.keyCode&&b.close()}),v.on("resize"+p,function(){b.updateSize()}),b.st.closeOnContentClick||(f+=" mfp-auto-cursor"),f&&b.wrap.addClass(f);var k=b.wH=v.height(),n={};if(b.fixedContentPos&&b._hasScrollBar(k)){var o=b._getScrollbarSize();o&&(n.marginRight=o)}b.fixedContentPos&&(b.isIE7?a("body, html").css("overflow","hidden"):n.overflow="hidden");var r=b.st.mainClass;return b.isIE7&&(r+=" mfp-ie7"),r&&b._addClassToMFP(r),b.updateItemHTML(),y("BuildControls"),a("html").css(n),b.bgOverlay.add(b.wrap).prependTo(b.st.prependTo||a(document.body)),b._lastFocusedEl=document.activeElement,setTimeout(function(){b.content?(b._addClassToMFP(q),b._setFocus()):b.bgOverlay.addClass(q),d.on("focusin"+p,b._onFocusIn)},16),b.isOpen=!0,b.updateSize(k),y(m),c},close:function(){b.isOpen&&(y(i),b.isOpen=!1,b.st.removalDelay&&!b.isLowIE&&b.supportsTransition?(b._addClassToMFP(r),setTimeout(function(){b._close()},b.st.removalDelay)):b._close())},_close:function(){y(h);var c=r+" "+q+" ";if(b.bgOverlay.detach(),b.wrap.detach(),b.container.empty(),b.st.mainClass&&(c+=b.st.mainClass+" "),b._removeClassFromMFP(c),b.fixedContentPos){var e={marginRight:""};b.isIE7?a("body, html").css("overflow",""):e.overflow="",a("html").css(e)}d.off("keyup"+p+" focusin"+p),b.ev.off(p),b.wrap.attr("class","mfp-wrap").removeAttr("style"),b.bgOverlay.attr("class","mfp-bg"),b.container.attr("class","mfp-container"),!b.st.showCloseBtn||b.st.closeBtnInside&&b.currTemplate[b.currItem.type]!==!0||b.currTemplate.closeBtn&&b.currTemplate.closeBtn.detach(),b.st.autoFocusLast&&b._lastFocusedEl&&a(b._lastFocusedEl).focus(),b.currItem=null,b.content=null,b.currTemplate=null,b.prevHeight=0,y(j)},updateSize:function(a){if(b.isIOS){var c=document.documentElement.clientWidth/window.innerWidth,d=window.innerHeight*c;b.wrap.css("height",d),b.wH=d}else b.wH=a||v.height();b.fixedContentPos||b.wrap.css("height",b.wH),y("Resize")},updateItemHTML:function(){var c=b.items[b.index];b.contentContainer.detach(),b.content&&b.content.detach(),c.parsed||(c=b.parseEl(b.index));var d=c.type;if(y("BeforeChange",[b.currItem?b.currItem.type:"",d]),b.currItem=c,!b.currTemplate[d]){var f=!!b.st[d]&&b.st[d].markup;y("FirstMarkupParse",f),f?b.currTemplate[d]=a(f):b.currTemplate[d]=!0}e&&e!==c.type&&b.container.removeClass("mfp-"+e+"-holder");var g=b["get"+d.charAt(0).toUpperCase()+d.slice(1)](c,b.currTemplate[d]);b.appendContent(g,d),c.preloaded=!0,y(n,c),e=c.type,b.container.prepend(b.contentContainer),y("AfterChange")},appendContent:function(a,c){b.content=a,a?b.st.showCloseBtn&&b.st.closeBtnInside&&b.currTemplate[c]===!0?b.content.find(".mfp-close").length||b.content.append(z()):b.content=a:b.content="",y(k),b.container.addClass("mfp-"+c+"-holder"),b.contentContainer.append(b.content)},parseEl:function(c){var d,e=b.items[c];if(e.tagName?e={el:a(e)}:(d=e.type,e={data:e,src:e.src}),e.el){for(var f=b.types,g=0;g<f.length;g++)if(e.el.hasClass("mfp-"+f[g])){d=f[g];break}e.src=e.el.attr("data-mfp-src"),e.src||(e.src=e.el.attr("href"))}return e.type=d||b.st.type||"inline",e.index=c,e.parsed=!0,b.items[c]=e,y("ElementParse",e),b.items[c]},addGroup:function(a,c){var d=function(d){d.mfpEl=this,b._openClick(d,a,c)};c||(c={});var e="click.magnificPopup";c.mainEl=a,c.items?(c.isObj=!0,a.off(e).on(e,d)):(c.isObj=!1,c.delegate?a.off(e).on(e,c.delegate,d):(c.items=a,a.off(e).on(e,d)))},_openClick:function(c,d,e){var f=void 0!==e.midClick?e.midClick:a.magnificPopup.defaults.midClick;if(f||!(2===c.which||c.ctrlKey||c.metaKey||c.altKey||c.shiftKey)){var g=void 0!==e.disableOn?e.disableOn:a.magnificPopup.defaults.disableOn;if(g)if(a.isFunction(g)){if(!g.call(b))return!0}else if(v.width()<g)return!0;c.type&&(c.preventDefault(),b.isOpen&&c.stopPropagation()),e.el=a(c.mfpEl),e.delegate&&(e.items=d.find(e.delegate)),b.open(e)}},updateStatus:function(a,d){if(b.preloader){c!==a&&b.container.removeClass("mfp-s-"+c),d||"loading"!==a||(d=b.st.tLoading);var e={status:a,text:d};y("UpdateStatus",e),a=e.status,d=e.text,b.preloader.html(d),b.preloader.find("a").on("click",function(a){a.stopImmediatePropagation()}),b.container.addClass("mfp-s-"+a),c=a}},_checkIfClose:function(c){if(!a(c).hasClass(s)){var d=b.st.closeOnContentClick,e=b.st.closeOnBgClick;if(d&&e)return!0;if(!b.content||a(c).hasClass("mfp-close")||b.preloader&&c===b.preloader[0])return!0;if(c===b.content[0]||a.contains(b.content[0],c)){if(d)return!0}else if(e&&a.contains(document,c))return!0;return!1}},_addClassToMFP:function(a){b.bgOverlay.addClass(a),b.wrap.addClass(a)},_removeClassFromMFP:function(a){this.bgOverlay.removeClass(a),b.wrap.removeClass(a)},_hasScrollBar:function(a){return(b.isIE7?d.height():document.body.scrollHeight)>(a||v.height())},_setFocus:function(){(b.st.focus?b.content.find(b.st.focus).eq(0):b.wrap).focus()},_onFocusIn:function(c){return c.target===b.wrap[0]||a.contains(b.wrap[0],c.target)?void 0:(b._setFocus(),!1)},_parseMarkup:function(b,c,d){var e;d.data&&(c=a.extend(d.data,c)),y(l,[b,c,d]),a.each(c,function(c,d){if(void 0===d||d===!1)return!0;if(e=c.split("_"),e.length>1){var f=b.find(p+"-"+e[0]);if(f.length>0){var g=e[1];"replaceWith"===g?f[0]!==d[0]&&f.replaceWith(d):"img"===g?f.is("img")?f.attr("src",d):f.replaceWith(a("<img>").attr("src",d).attr("class",f.attr("class"))):f.attr(e[1],d)}}else b.find(p+"-"+c).html(d)})},_getScrollbarSize:function(){if(void 0===b.scrollbarSize){var a=document.createElement("div");a.style.cssText="width: 99px; height: 99px; overflow: scroll; position: absolute; top: -9999px;",document.body.appendChild(a),b.scrollbarSize=a.offsetWidth-a.clientWidth,document.body.removeChild(a)}return b.scrollbarSize}},a.magnificPopup={instance:null,proto:t.prototype,modules:[],open:function(b,c){return A(),b=b?a.extend(!0,{},b):{},b.isObj=!0,b.index=c||0,this.instance.open(b)},close:function(){return a.magnificPopup.instance&&a.magnificPopup.instance.close()},registerModule:function(b,c){c.options&&(a.magnificPopup.defaults[b]=c.options),a.extend(this.proto,c.proto),this.modules.push(b)},defaults:{disableOn:0,key:null,midClick:!1,mainClass:"",preloader:!0,focus:"",closeOnContentClick:!1,closeOnBgClick:!0,closeBtnInside:!0,showCloseBtn:!0,enableEscapeKey:!0,modal:!1,alignTop:!1,removalDelay:0,prependTo:null,fixedContentPos:"auto",fixedBgPos:"auto",overflowY:"auto",closeMarkup:'<button title="%title%" type="button" class="mfp-close">&#215;</button>',tClose:"Close (Esc)",tLoading:"Loading...",autoFocusLast:!0}},a.fn.magnificPopup=function(c){A();var d=a(this);if("string"==typeof c)if("open"===c){var e,f=u?d.data("magnificPopup"):d[0].magnificPopup,g=parseInt(arguments[1],10)||0;f.items?e=f.items[g]:(e=d,f.delegate&&(e=e.find(f.delegate)),e=e.eq(g)),b._openClick({mfpEl:e},d,f)}else b.isOpen&&b[c].apply(b,Array.prototype.slice.call(arguments,1));else c=a.extend(!0,{},c),u?d.data("magnificPopup",c):d[0].magnificPopup=c,b.addGroup(d,c);return d};var C,D,E,F="inline",G=function(){E&&(D.after(E.addClass(C)).detach(),E=null)};a.magnificPopup.registerModule(F,{options:{hiddenClass:"hide",markup:"",tNotFound:"Content not found"},proto:{initInline:function(){b.types.push(F),w(h+"."+F,function(){G()})},getInline:function(c,d){if(G(),c.src){var e=b.st.inline,f=a(c.src);if(f.length){var g=f[0].parentNode;g&&g.tagName&&(D||(C=e.hiddenClass,D=x(C),C="mfp-"+C),E=f.after(D).detach().removeClass(C)),b.updateStatus("ready")}else b.updateStatus("error",e.tNotFound),f=a("<div>");return c.inlineElement=f,f}return b.updateStatus("ready"),b._parseMarkup(d,{},c),d}}});var H,I="ajax",J=function(){H&&a(document.body).removeClass(H)},K=function(){J(),b.req&&b.req.abort()};a.magnificPopup.registerModule(I,{options:{settings:null,cursor:"mfp-ajax-cur",tError:'<a href="%url%">The content</a> could not be loaded.'},proto:{initAjax:function(){b.types.push(I),H=b.st.ajax.cursor,w(h+"."+I,K),w("BeforeChange."+I,K)},getAjax:function(c){H&&a(document.body).addClass(H),b.updateStatus("loading");var d=a.extend({url:c.src,success:function(d,e,f){var g={data:d,xhr:f};y("ParseAjax",g),b.appendContent(a(g.data),I),c.finished=!0,J(),b._setFocus(),setTimeout(function(){b.wrap.addClass(q)},16),b.updateStatus("ready"),y("AjaxContentAdded")},error:function(){J(),c.finished=c.loadError=!0,b.updateStatus("error",b.st.ajax.tError.replace("%url%",c.src))}},b.st.ajax.settings);return b.req=a.ajax(d),""}}});var L,M=function(c){if(c.data&&void 0!==c.data.title)return c.data.title;var d=b.st.image.titleSrc;if(d){if(a.isFunction(d))return d.call(b,c);if(c.el)return c.el.attr(d)||""}return""};a.magnificPopup.registerModule("image",{options:{markup:'<div class="mfp-figure"><div class="mfp-close"></div><figure><div class="mfp-img"></div><figcaption><div class="mfp-bottom-bar"><div class="mfp-title"></div><div class="mfp-counter"></div></div></figcaption></figure></div>',cursor:"mfp-zoom-out-cur",titleSrc:"title",verticalFit:!0,tError:'<a href="%url%">The image</a> could not be loaded.'},proto:{initImage:function(){var c=b.st.image,d=".image";b.types.push("image"),w(m+d,function(){"image"===b.currItem.type&&c.cursor&&a(document.body).addClass(c.cursor)}),w(h+d,function(){c.cursor&&a(document.body).removeClass(c.cursor),v.off("resize"+p)}),w("Resize"+d,b.resizeImage),b.isLowIE&&w("AfterChange",b.resizeImage)},resizeImage:function(){var a=b.currItem;if(a&&a.img&&b.st.image.verticalFit){var c=0;b.isLowIE&&(c=parseInt(a.img.css("padding-top"),10)+parseInt(a.img.css("padding-bottom"),10)),a.img.css("max-height",b.wH-c)}},_onImageHasSize:function(a){a.img&&(a.hasSize=!0,L&&clearInterval(L),a.isCheckingImgSize=!1,y("ImageHasSize",a),a.imgHidden&&(b.content&&b.content.removeClass("mfp-loading"),a.imgHidden=!1))},findImageSize:function(a){var c=0,d=a.img[0],e=function(f){L&&clearInterval(L),L=setInterval(function(){return d.naturalWidth>0?void b._onImageHasSize(a):(c>200&&clearInterval(L),c++,void(3===c?e(10):40===c?e(50):100===c&&e(500)))},f)};e(1)},getImage:function(c,d){var e=0,f=function(){c&&(c.img[0].complete?(c.img.off(".mfploader"),c===b.currItem&&(b._onImageHasSize(c),b.updateStatus("ready")),c.hasSize=!0,c.loaded=!0,y("ImageLoadComplete")):(e++,200>e?setTimeout(f,100):g()))},g=function(){c&&(c.img.off(".mfploader"),c===b.currItem&&(b._onImageHasSize(c),b.updateStatus("error",h.tError.replace("%url%",c.src))),c.hasSize=!0,c.loaded=!0,c.loadError=!0)},h=b.st.image,i=d.find(".mfp-img");if(i.length){var j=document.createElement("img");j.className="mfp-img",c.el&&c.el.find("img").length&&(j.alt=c.el.find("img").attr("alt")),c.img=a(j).on("load.mfploader",f).on("error.mfploader",g),j.src=c.src,i.is("img")&&(c.img=c.img.clone()),j=c.img[0],j.naturalWidth>0?c.hasSize=!0:j.width||(c.hasSize=!1)}return b._parseMarkup(d,{title:M(c),img_replaceWith:c.img},c),b.resizeImage(),c.hasSize?(L&&clearInterval(L),c.loadError?(d.addClass("mfp-loading"),b.updateStatus("error",h.tError.replace("%url%",c.src))):(d.removeClass("mfp-loading"),b.updateStatus("ready")),d):(b.updateStatus("loading"),c.loading=!0,c.hasSize||(c.imgHidden=!0,d.addClass("mfp-loading"),b.findImageSize(c)),d)}}});var N,O=function(){return void 0===N&&(N=void 0!==document.createElement("p").style.MozTransform),N};a.magnificPopup.registerModule("zoom",{options:{enabled:!1,easing:"ease-in-out",duration:300,opener:function(a){return a.is("img")?a:a.find("img")}},proto:{initZoom:function(){var a,c=b.st.zoom,d=".zoom";if(c.enabled&&b.supportsTransition){var e,f,g=c.duration,j=function(a){var b=a.clone().removeAttr("style").removeAttr("class").addClass("mfp-animated-image"),d="all "+c.duration/1e3+"s "+c.easing,e={position:"fixed",zIndex:9999,left:0,top:0,"-webkit-backface-visibility":"hidden"},f="transition";return e["-webkit-"+f]=e["-moz-"+f]=e["-o-"+f]=e[f]=d,b.css(e),b},k=function(){b.content.css("visibility","visible")};w("BuildControls"+d,function(){if(b._allowZoom()){if(clearTimeout(e),b.content.css("visibility","hidden"),a=b._getItemToZoom(),!a)return void k();f=j(a),f.css(b._getOffset()),b.wrap.append(f),e=setTimeout(function(){f.css(b._getOffset(!0)),e=setTimeout(function(){k(),setTimeout(function(){f.remove(),a=f=null,y("ZoomAnimationEnded")},16)},g)},16)}}),w(i+d,function(){if(b._allowZoom()){if(clearTimeout(e),b.st.removalDelay=g,!a){if(a=b._getItemToZoom(),!a)return;f=j(a)}f.css(b._getOffset(!0)),b.wrap.append(f),b.content.css("visibility","hidden"),setTimeout(function(){f.css(b._getOffset())},16)}}),w(h+d,function(){b._allowZoom()&&(k(),f&&f.remove(),a=null)})}},_allowZoom:function(){return"image"===b.currItem.type},_getItemToZoom:function(){return!!b.currItem.hasSize&&b.currItem.img},_getOffset:function(c){var d;d=c?b.currItem.img:b.st.zoom.opener(b.currItem.el||b.currItem);var e=d.offset(),f=parseInt(d.css("padding-top"),10),g=parseInt(d.css("padding-bottom"),10);e.top-=a(window).scrollTop()-f;var h={width:d.width(),height:(u?d.innerHeight():d[0].offsetHeight)-g-f};return O()?h["-moz-transform"]=h.transform="translate("+e.left+"px,"+e.top+"px)":(h.left=e.left,h.top=e.top),h}}});var P="iframe",Q="//about:blank",R=function(a){if(b.currTemplate[P]){var c=b.currTemplate[P].find("iframe");c.length&&(a||(c[0].src=Q),b.isIE8&&c.css("display",a?"block":"none"))}};a.magnificPopup.registerModule(P,{options:{markup:'<div class="mfp-iframe-scaler"><div class="mfp-close"></div><iframe class="mfp-iframe" src="//about:blank" frameborder="0" allowfullscreen></iframe></div>',srcAction:"iframe_src",patterns:{youtube:{index:"youtube.com",id:"v=",src:"http://www.youtube.com/embed/%id%?autoplay=1"},vimeo:{index:"vimeo.com/",id:"/",src:"http://player.vimeo.com/video/%id%?autoplay=1"},gmaps:{index:"http://maps.google.",src:"%id%&output=embed"}}},proto:{initIframe:function(){b.types.push(P),w("BeforeChange",function(a,b,c){b!==c&&(b===P?R():c===P&&R(!0))}),w(h+"."+P,function(){R()})},getIframe:function(c,d){var e=c.src,f=b.st.iframe;a.each(f.patterns,function(){return e.indexOf(this.index)>-1?(this.id&&(e="string"==typeof this.id?e.substr(e.lastIndexOf(this.id)+this.id.length,e.length):this.id.call(this,e)),e=this.src.replace("%id%",e),!1):void 0});var g={};return f.srcAction&&(g[f.srcAction]=e),b._parseMarkup(d,g,c),b.updateStatus("ready"),d}}});var S=function(a){var c=b.items.length;return a>c-1?a-c:0>a?c+a:a},T=function(a,b,c){return a.replace(/%curr%/gi,b+1).replace(/%total%/gi,c)};a.magnificPopup.registerModule("gallery",{options:{enabled:!1,arrowMarkup:'<button title="%title%" type="button" class="mfp-arrow mfp-arrow-%dir%"></button>',preload:[0,2],navigateByImgClick:!0,arrows:!0,tPrev:"Previous (Left arrow key)",tNext:"Next (Right arrow key)",tCounter:"%curr% of %total%"},proto:{initGallery:function(){var c=b.st.gallery,e=".mfp-gallery";return b.direction=!0,!(!c||!c.enabled)&&(f+=" mfp-gallery",w(m+e,function(){c.navigateByImgClick&&b.wrap.on("click"+e,".mfp-img",function(){return b.items.length>1?(b.next(),!1):void 0}),d.on("keydown"+e,function(a){37===a.keyCode?b.prev():39===a.keyCode&&b.next()})}),w("UpdateStatus"+e,function(a,c){c.text&&(c.text=T(c.text,b.currItem.index,b.items.length))}),w(l+e,function(a,d,e,f){var g=b.items.length;e.counter=g>1?T(c.tCounter,f.index,g):""}),w("BuildControls"+e,function(){if(b.items.length>1&&c.arrows&&!b.arrowLeft){var d=c.arrowMarkup,e=b.arrowLeft=a(d.replace(/%title%/gi,c.tPrev).replace(/%dir%/gi,"left")).addClass(s),f=b.arrowRight=a(d.replace(/%title%/gi,c.tNext).replace(/%dir%/gi,"right")).addClass(s);e.click(function(){b.prev()}),f.click(function(){b.next()}),b.container.append(e.add(f))}}),w(n+e,function(){b._preloadTimeout&&clearTimeout(b._preloadTimeout),b._preloadTimeout=setTimeout(function(){b.preloadNearbyImages(),b._preloadTimeout=null},16)}),void w(h+e,function(){d.off(e),b.wrap.off("click"+e),b.arrowRight=b.arrowLeft=null}))},next:function(){b.direction=!0,b.index=S(b.index+1),b.updateItemHTML()},prev:function(){b.direction=!1,b.index=S(b.index-1),b.updateItemHTML()},goTo:function(a){b.direction=a>=b.index,b.index=a,b.updateItemHTML()},preloadNearbyImages:function(){var a,c=b.st.gallery.preload,d=Math.min(c[0],b.items.length),e=Math.min(c[1],b.items.length);for(a=1;a<=(b.direction?e:d);a++)b._preloadItem(b.index+a);for(a=1;a<=(b.direction?d:e);a++)b._preloadItem(b.index-a)},_preloadItem:function(c){if(c=S(c),!b.items[c].preloaded){var d=b.items[c];d.parsed||(d=b.parseEl(c)),y("LazyLoad",d),"image"===d.type&&(d.img=a('<img class="mfp-img" />').on("load.mfploader",function(){d.hasSize=!0}).on("error.mfploader",function(){d.hasSize=!0,d.loadError=!0,y("LazyLoadError",d)}).attr("src",d.src)),d.preloaded=!0}}}});var U="retina";a.magnificPopup.registerModule(U,{options:{replaceSrc:function(a){return a.src.replace(/\.\w+$/,function(a){return"@2x"+a})},ratio:1},proto:{initRetina:function(){if(window.devicePixelRatio>1){var a=b.st.retina,c=a.ratio;c=isNaN(c)?c():c,c>1&&(w("ImageHasSize."+U,function(a,b){b.img.css({"max-width":b.img[0].naturalWidth/c,width:"100%"})}),w("ElementParse."+U,function(b,d){d.src=a.replaceSrc(d,c)}))}}}}),A()});

"use strict";

function getBaseUrl() {
    var a = new RegExp(/^.*\//);
    return a.exec(window.location.href)
}
jQuery(document).ready(function() {

    jQuery(".busi-photos-wrapper").magnificPopup({
        delegate: "a",
        type: "image",
        closeOnContentClick: false,
        closeBtnInside: false,
        mainClass: "mfp-fade",
        image: {
            verticalFit: true,
            titleSrc: function(a) {
                return a.el.data("alt")
            }
        },
        gallery: {
            enabled: true,
            navigateByImgClick: true
        },
        zoom: {
            enabled: true
        }
    })
}), jQuery.fn.isAboveScreen = function() {
    var a = {};
    a.top = jQuery(window).scrollTop(), a.bottom = a.top + jQuery(window).height();
    var b = {};
    return b.top = (undefined != this.offset() ? this.offset().top:0),
     b.bottom = b.top + this.outerHeight(),
      b.bottom < a.top
},
jQuery(document).ready(function() {
	toastr.options.closeMethod = 'fadeOut';
	toastr.options.showEasing = 'swing';
	toastr.options.hideEasing = 'swing';
	toastr.options.closeDuration = 300;
	toastr.options.preventDuplicates = true;
	toastr.options.timeOut = 1000;

	var canScrollRates = true;
	var ratingsPage = 1;

	var previousActive;

	jQuery(function(){
		var hash = window.location.hash;
		hash && jQuery('a.business-tab[href="' + hash + '"]').tab('show');
		if(hash)previousActive = jQuery('a.business-tab[href="' + hash + '"]');
		else previousActive=jQuery('#business-tabs>li:first-child>a');

		previousActive.addClass('wyz-prim-color');
		previousActive.tab('show');

		jQuery('.business-tab').click(function (e) {e.preventDefault();
			jQuery(this).tab('show');
			previousActive.removeClass('wyz-prim-color');
			previousActive=jQuery(this);
			jQuery(this).addClass('wyz-prim-color');
			var scrollmem = jQuery('body').scrollTop() || jQuery('html').scrollTop();
			window.location.hash = this.hash;
			jQuery('html,body').scrollTop(scrollmem);

			
		});

		jQuery('#book-bus').on('click',function(e){
			if(!jQuery('#business-tabs #bookings-btn').length)return;
			e.preventDefault();
			jQuery('#business-tabs #bookings-btn').trigger('click');
			 setTimeout(function() {
                jQuery("html, body").animate({
                    scrollTop: jQuery('#business-tabs #bookings-btn').offset().top
                }, 300)
            }, 100);
		});

		jQuery('#photo-btn').on('click',function(e){
			wyz_align_gallery_images();
		});

		jQuery('#rate-bus').on('click',function(e){
			if(!jQuery('#business-tabs #ratings-btn').length)return;
			e.preventDefault();
			jQuery('#business-tabs #ratings-btn').trigger('click');
			setTimeout(function() {
                jQuery("html, body").animate({
                    scrollTop: jQuery('#business-tabs #ratings-btn').offset().top
                }, 300)
            }, 100);
		});
	});

    function a() {
    	wyz_align_gallery_images();
        jQuery(window).width() < 992 && !e ? (e = true, jQuery(".business-tab").click(function() {
        	var href = jQuery(this).attr('href');
            setTimeout(function() {
                jQuery("html, body").animate({
                    scrollTop: jQuery(href).offset().top
                }, 300)
            }, 100);
        })) : jQuery(window).width() >= 992 && e && (e = false, jQuery(".business-tab").unbind("click"))
    }

    if(jQuery('#bookings-btn').length) {
    	jQuery('#bookings-btn').on('click',function(){
    		setTimeout(function(){jQuery(window).trigger('resize');},300);
    	});
    }

    function b(a, b) {
        b && d && (d = false, jQuery("#loadmoreajaxloader").fadeTo("fast", 1), jQuery.ajax({
            type: "POST",
            url: ajaxurl,
            data: "action=bus_inf_scrll&nonce=" + ajaxnonce + "&bus-id=" + business.businessId + "&is-current-user-author=" + business.isCurrentUserAuthor + "&page=" + f + "&logged-in-user=" + business.loggedInUser,
            success: function(a) {
                if (f++, "" !== a) {
                    d = !0;
                    var b = a.indexOf("wyz_space");
                    jQuery("#postswrapper").append(a.substring(b + 9, a.length)), business.postIndx = parseInt(a.substring(0, b)), jQuery("#loadmoreajaxloader").fadeTo("fast", 0)
                    
                } else jQuery("div#loadmoreajaxloader").html('<center>'+business.noPostsMsg+'</center>'), jQuery(window).unbind("inview")
            }
        }))
    }

    function ajax_loadmore_ratings(event, visible) {
		if (visible && canScrollRates) {
			canScrollRates = false;
			jQuery("#loadmoreratingsajaxloader").fadeTo("fast", 1);
			jQuery.ajax({
				type: "POST",
				url: ajaxurl,
				data: "action=bus_inf_rate_scrll&nonce=" + ajaxnonce + "&bus-id=" + business.businessId +
					 "&page=" + ratingsPage/* + "&logged-in-user=" + business.loggedInUser*/,
				success: function(html) {
					ratingsPage++;
					if ('' !== html) {
						canScrollRates = true;
						var spaceIndex = html.indexOf('wyz_space');
						jQuery("#ratingswrapper").append(html.substring(spaceIndex + 9, html.length));
						business.ratingIndex = parseInt(html.substring(0, spaceIndex));
						jQuery("#loadmoreratingsajaxloader").fadeTo("fast", 0);
					} else {
						jQuery('#loadmoreratingsajaxloader').html('<center class="text-center">'+business.noMoreRatings+'</center>');
						jQuery('#loadmoreratingsajaxloader').unbind('inview');
					}
				}
			});
		}
	}

	jQuery('.fav-bus').click(favoriteBus);
	function favoriteBus(event){
		event.preventDefault();
		var bus_id = jQuery(this).data('busid');
		if( '' == bus_id || undefined == bus_id ) return;
		var isFav = jQuery(this).data('fav');
		jQuery(this).parent().addClass('fade-loading');
		jQuery(this).unbind('favoriteBus');
		var favType = isFav == 1 ? 'unfav' : 'fav';
		var target = jQuery(this);

		jQuery.ajax({
			type: "POST",
			url: ajaxurl,
			data: "action=business_favorite&nonce=" + ajaxnonce + "&business_id=" + bus_id + "&fav_type=" + favType,
			success: function(result) {
				target.parent().removeClass('fade-loading');
				if(favType=='fav'){
					target.find('i').removeClass('fa-heart-o');
					target.find('i').addClass('fa-heart');
					target.data('fav',1 );
				} else {
					target.find('i').removeClass('fa-heart');
					target.find('i').addClass('fa-heart-o');
					target.data('fav',0 );
				}
			}
		});
	}

    var c, d = !0,
        e = !1, DropDn, h = !1,
        editing=false;

    if (jQuery(window).resize(function() {
            clearTimeout(c), c = setTimeout(a, 500)
        }), jQuery(window).load(a), jQuery(document).on("click", ".bus-post-x", function(event) {
            event.preventDefault();
            var a = jQuery(this).find(".delete-dropdown");
            if (!a.length) {
                var b = jQuery(this).position(),
                    a = DeleteDropdown.getDropdown(jQuery(this).data("id"), -90, b.top + 5, jQuery(this).data("comm_enabled"));
                jQuery(this).append(a), a = jQuery(this).find(".delete-dropdown");
                DropDn = a;
            }
            a.slideDown("fast"), jQuery("body").click(function(b) {
                jQuery(b.target).closest(a).length || jQuery(a).slideUp("fast")
            })
        }), jQuery(".delete-dropdown-a").live("click", function() {
            jQuery(this).unbind("click"), jQuery.ajax({
                type: "POST",
                url: ajaxurl,
                data: "action=bus_post_delete&nonce=" + ajaxnonce + "&post-id=" + jQuery(this).data("id") + "&bus-id=" + business.businessId,
                success: function(a) {
                    a ? location.reload() : toastr.error(business.postDeleteError)
                }
            })
        }),
        jQuery('#business-edit-post-cancel').click(function(){
        	jQuery("#business_edit_post_image").attr("imgid", "");
        	jQuery("#business_edit_post_image").val("");
        	jQuery("#business-edit-post-image").removeClass("has-image");
        	jQuery("#e-tc").hide();
    		jQuery("#edit-post-txt").val("");
            jQuery('#business-post-edit').fadeOut('fast');
    	}),jQuery("#business-edit-post-image").click(function(a) {
            	var i;
	            return a.preventDefault(), j ? (j = !1, jQuery("#business_edit_post_image").attr("imgid", ""), jQuery("#business_edit_post_image").val(""), jQuery("#business-edit-post-image").removeClass("has-image"), void jQuery("#e-tc").hide()) : (i = wp.media({
	                title: business.uploadImage,
	                multiple: !1
	            }), i.on("open", function() {
	                var a = i.state().get("selection"),
	                    b = jQuery("#business_edit_post_image").attr("imgid");
	                if ("" !== b) {
	                    var c = wp.media.attachment(b);
	                    c.fetch(), a.add(c ? [c] : [])
	                }
	            }), i.on("select", function() {
	                var a = i.state().get("selection");
	                a.map(function(a) {
	                    a = a.toJSON(), jQuery("#business_edit_post_image").val(a.id), jQuery("#business_edit_post_image").attr("imgid", a.id), jQuery("#business-edit-post-image").addClass("has-image"), jQuery("#e-tc").show(), j = !0
	                })
	            }), void i.open())
		}),jQuery('.post-fb-btnShare').live('click',function(){
			elem = jQuery(this);
			postToFeed(elem.data('title'), elem.data('desc'), elem.prop('href'), elem.data('image'));
			return false;
		}),jQuery(".edit-dropdown-a").live("click", function() {
        	if(editing)return;
        	editing=true;

        	var post_id = jQuery(this).data("id");
            jQuery.ajax({
                type: "POST",
                url: ajaxurl,
                data: "action=bus_post_edit_get&nonce=" + ajaxnonce + "&post-id=" + post_id,
                success: function(a) {
                    DropDn.slideUp('fast');
                    editing=false;
                    a = JSON.parse(a);
                    jQuery("#edit-post-txt").val(a[0]);
                    jQuery("#business_edit_post_image").val(a[1]);
                    jQuery("#business_edit_post_image").attr("imgid",a[1]);

                    var j = !1;

                    var modal = jQuery('#business-post-edit');
 					modal.css('display',"block");

                    if ( '' != a[1] ){
                    	jQuery("#business-edit-post-image").addClass("has-image");
                    	jQuery("#e-tc").show();
                    	j=1;
                    }

                    jQuery("#busi_edit_post_submit").unbind('click');

					jQuery("#busi_edit_post_submit").on("click", function() {
						var a = encodeURIComponent(jQuery("#edit-post-txt").val()),
						    b = jQuery("#business_edit_post_image").val(),
						    c = jQuery("#wyz_business_edit_post_nonce").val();
						if ("" === a && "" === b) toastr.warning(business.emptyPostError);
						else {
						    jQuery(this).prop("disabled", !0), jQuery(this).addClass("busi_post_submit-dis wyz-primary-color-txt  wyz-prim-color-txt"), jQuery(this).removeClass("busi_post_submit"), jQuery(this).prop("value", "Updating...");
						    var d = jQuery(this);
						    jQuery.ajax({
						        type: "POST",
						        url: ajaxurl,
						        data: "action=updatebuspost&id=" + business.businessId + "&post-id=" + post_id + "&nonce=" + c + "&post-txt=" + a + "&img=" + b,
						        success: function(a) {
						        if(!a){
						        	toastr.error(business.unhandledError);
						        	jQuery("#edit-post-txt").val("");
								    jQuery("#business_edit_post_image").val("");
								     modal.style.display = "none";
						        }
						        	else{
						        		toastr.success(business.updateComplete);
						        		location.reload();
						        	}
						        }
						    })
						}
					});
                }
            })


        }),jQuery(".comm-dropdown-a").live("click", function() {
            jQuery(this).unbind("click"), jQuery.ajax({
                type: "POST",
                url: ajaxurl,
                data: "action=bus_post_comm_toggle&nonce=" + ajaxnonce + "&post-id=" + jQuery(this).data("id") + "&comm-stat=" + jQuery(this).data("comm"),
                success: function(a) {
                    location.reload()
                }
            })
        }),jQuery(".desc-see-more").each(function() {
        	if ( business.isBusiness )
	        	jQuery(this).find('.read-more').click(function(){
	        		jQuery('#about-btn').trigger('click');
	        	});
        }), business) {
        if (business.havePosts) {
            var f = 1;
            jQuery("#loadmoreajaxloader").bind("inview", function(a, c) {
                b(a, c)
            }), jQuery("#loadmoreajaxloader").isAboveScreen() && b(event, !0),
            jQuery(document).on("click", ".like-btn-no-log", function() {
            	if(h){DropDn.slideUp("fast")}
                event.preventDefault(), h = true, DropDn = LoginDropdown.getDropdown(35, jQuery(this).position().top + ( jQuery(this).find('.fa-heart-o').length ? jQuery(this).parent().position().top + 20 : jQuery(this).parent().parent().position().top ) + 40, false), jQuery("#postswrapper").append(DropDn), 
            	DropDn = jQuery("#postswrapper").find(".login-dropdown"), DropDn.slideDown("slow")
            });
        }

        jQuery('.page-map-right-content .map-info-gallery li .gal-link').css('line-height',jQuery('.page-map-right-content .map-info-gallery').width()/4+'px');

        jQuery(".com-view-more").live({
			click: function (event) {
				event.preventDefault();
				jQuery(this).addClass('fade-loading');
				var This = jQuery(this);
				var offset = parseInt( This.data('offset') );
				jQuery.ajax({
					type: "POST",
					url: ajaxurl,
					data: "action=bus_load_comments&nonce=" + ajaxnonce + "&post-id=" + This.data('id') + '&offset=' + offset,
					success: function(result) {
						This.removeClass('fade-loading');
						if ( result ) {
							This.closest('.the-comment').before(result);
							This.closest('.the-comment').css({"display":"none"});
						} else {
							This.closest('.the-comment').css({"display":"none"});
						}
					}
				});
			}
		});

        wyz_align_gallery_images();

		jQuery(".map-share-btn").on('click', function (e) {
				e.preventDefault();
				jQuery(this).parent().nextAll(".business-post-share-cont").first().toggle();
		});

        jQuery("body").click(function() {
            h && DropDn!=undefined&&(DropDn.slideUp("fast"), DropDn.remove(), h = !1);
        });

        if (business.haveRatings) {
            var page = 1;

			//infinite scroll
			jQuery('#loadmoreratingsajaxloader').bind('inview', function(event, visible){
				ajax_loadmore_ratings(event, visible);
			});

			//case page reload and loadmoreajaxloader is above screen
			if(jQuery('#loadmoreratingsajaxloader').isAboveScreen())
				ajax_loadmore_ratings(event, true);
        }

        var i, j = !1;
        jQuery("#business-post-image").click(function(a) {
            return a.preventDefault(), j ? (j = !1, jQuery("#business_post_image").attr("imgid", ""), jQuery("#business_post_image").val(""), jQuery("#business-post-image").removeClass("has-image"), void jQuery("#tc").hide()) : (i = wp.media({
                title: "Upload Image",
                multiple: !1
            }), i.on("open", function() {
                var a = i.state().get("selection"),
                    b = jQuery("#business_post_image").attr("imgid");
                if ("" !== b) {
                    var c = wp.media.attachment(b);
                    c.fetch(), a.add(c ? [c] : [])
                }
            }), i.on("select", function() {
                var a = i.state().get("selection");
                a.map(function(a) {
                    a = a.toJSON(), jQuery("#business_post_image").val(a.id), jQuery("#business_post_image").attr("imgid", a.id), jQuery("#business-post-image").addClass("has-image"), jQuery("#tc").show(), j = !0
                })
            }), void i.open())
        }), jQuery("#busi_post_submit").on("click", function() {
            var a = encodeURIComponent(jQuery("#post-txt").val()),
                b = jQuery("#business_post_image").val(),
                c = jQuery("#wyz_business_post_nonce").val();
            if ("" === a && "" === b) toastr.warning(business.emptyPost);
            else {
                jQuery(this).prop("disabled", !0), jQuery(this).addClass("busi_post_submit-dis wyz-primary-color-txt"), jQuery(this).removeClass("busi_post_submit"), jQuery(this).prop("value", business.posting + "...");
                var d = jQuery(this);
                jQuery.ajax({
                    type: "POST",
                    url: ajaxurl,
                    data: "action=upbuspost&id=" + business.businessId + "&nonce=" + c + "&post-txt=" + a + "&img=" + b,
                    success: function(a) {
                    	if(-1 == a)
                    		toastr.warning(business.insufPoints);
                    	else
                        	business.havePosts || jQuery("#no-business-posts").remove(), jQuery("#postswrapper").prepend(a), jQuery("#post-txt").val(""), jQuery("#business_post_image").val(""), jQuery("#business_post_image").attr("imgid", ""), jQuery("#io").hide(), jQuery("#io").unbind("click"), j = !1, jQuery("#business-post-image").removeClass("has-image"), jQuery("#tc").hide();
                    	d.prop("disabled", !1), d.addClass("busi_post_submit"), d.removeClass("busi_post_submit-dis wyz-primary-color-txt"), d.prop("value", business.post);
                    }
                })
            }
        });
		var currentTarget, currentCommInput;
		jQuery(".post_footer_comment_btn, .post_footer_comment_btn").live("click", function(event) {
			if('false'==(business.loggedInUser)){
				if(jQuery(this).hasClass('action-btn')){
					DropDn = LoginDropdown.getDropdown(70, 
	                	jQuery(this).position().top + 53, true
	                );
				} else {
	                DropDn = LoginDropdown.getDropdown(35, 
	                	jQuery(this).position().top + jQuery(this).parent().parent().position().top + 40, true
	                );
	            }
                jQuery("#postswrapper").append(DropDn);
                DropDn = jQuery("#postswrapper").find(".login-dropdown"), DropDn.slideDown("slow");
                h=1;
	            return;
	        }
			currentCommInput = jQuery(this).prev();
			var inputContent = currentCommInput.val(),
			non_c = jQuery(this).next().val();
			if(inputContent==''){
				toastr.warning(business.emptyComment);
			} else{
				var id = jQuery(this).data('id'),
				currentTarget = jQuery(this);
				currentTarget.prop("disabled", !0);
				currentTarget.addClass("busi_post_submit-dis wyz-primary-color-txt");
				var label = currentTarget.text();
				currentTarget.text(business.posting+"...");
				jQuery.ajax({
					type: "POST",
					url: ajaxurl,
					data: "action=bus_post_comm&nonce=" + non_c + "&id=" + id + "&comment=" + inputContent,
					success: function(msg) {
						if(!msg){
							toastr.error(business.commentFailed);
						}
						else {
							currentTarget.closest('.post-footer-comments').find('.the-post-comments').prepend(msg);
							currentTarget.prop("disabled", 0);
							currentTarget.removeClass("busi_post_submit-dis wyz-primary-color-txt");
							currentCommInput.val('');
							currentTarget.text(label);
						}
					}
				});
			}
		}),
		jQuery(document).on("click", ".rate-btn-no-log", function(event) {
            	if(h){DropDn.slideUp("fast")}
                event.preventDefault(), h = true, DropDn = LoginDropdown.getDropdown(35, jQuery(this).position().top + jQuery(this).parent().position().top + 40, true), jQuery("#create-busi-rate").append(DropDn), 
            	DropDn = jQuery("#create-busi-rate").find(".login-dropdown"), DropDn.slideDown("slow")
            });
             jQuery("#busi_rate_submit").on("click", function() {

            	var a = jQuery("#rate-txt").val(),
                c = jQuery("#wyz_business_rate_nonce").val(),
                rf = jQuery("#business-rate-form input[name=rating]:checked");
                r = rf.val();
				rc = jQuery('#rating_category').val();
				if (r == 0 || r == '' || r == undefined )
					toastr.info(business.chooseRating);
				else if( r < 3 && a == "")
					toastr.info("");
				else if(''==rc || undefined == rc)
					toastr.info(business.ratingReason);
            	else {
					jQuery(this).prop("disabled", !0);
					jQuery(this).unbind("click");
					rf.attr('disabled', true);
					jQuery('#rating_category').prop("disabled", !0);
					jQuery(this).addClass("busi_post_submit-dis wyz-primary-color-txt wyz-prim-color-txt");
					jQuery('#business-rate-form label').removeClass('star-hov');
					jQuery(this).removeClass("busi_post_submit"), jQuery(this).prop("value", business.posting + "...");
					jQuery.ajax({
						type: "POST",
						url: ajaxurl,
						data: "action=bus_rate&nonce=" + ajaxnonce + "&bus-id=" + business.businessId +
						"&rate_txt=" + a + "&rate_cat=" + rc + "&rate=" + r,
						success: function(msg) {
							if(!msg){
								toastr.error(business.ratingError);
							}
							else {
								business.haveRatings || jQuery("#no-business-ratings").remove(),
								jQuery("#ratingswrapper").prepend(msg),
								jQuery('#create-busi-rate').fadeOut('slow', function() {
									jQuery('#create-busi-rate').remove();
								});
							}
						}
					});
				}
        })
    }
    if (business) {
        var i;
        jQuery("#upload-button").click(function(a) {
            a.preventDefault(), i = wp.media({
                title: "Upload Image",
                multiple: 'toggle'
            }), i.on("open", function() {
                var a = i.state().get("selection"),
                    b = business.attachments;
                if (b.constructor === Array || b.constructor === Object) b.forEach(function(b) {
                    var c = wp.media.attachment(b);
                    c.fetch(), a.add(c ? [c] : [])
                });
                else {
                    var c = wp.media.attachment(b);
                    c.fetch(), a.add(c ? [c] : [])
                }
            }), i.on("select", function() {
                var a = i.state().get("selection"),
                    b = [];
                a.map(function(a) {
                    a = a.toJSON(), null !== a.id && "" !== a.id && b.push(a.id)
                }), jQuery.ajax({
                    type: "POST",
                    url: ajaxurl,
                    data: "action=upattachments&nonce=" + ajaxnonce + "&imgs_ids=" + b + "&bus_id=" + business.businessId,
                    success: function(a) {
                    	location.reload();
                    }
                })
            }), i.open()
        })
    }

    var i;
    if ( jQuery("#business-header-no-image-btn").length) {
	    jQuery("#business-header-no-image-btn").click(function(a) {
	        a.preventDefault(), i = wp.media({
	            title: "Upload Image",
	        }), i.on("open", function() {
	            var a = i.state().get("selection"),
	                b = business.attachments;
	            if (b.constructor === Array || b.constructor === Object) b.forEach(function(b) {
	                var c = wp.media.attachment(b);
	                c.fetch(), a.add(c ? [c] : [])
	            });
	            else {
	                var c = wp.media.attachment(b);
	                c.fetch(), a.add(c ? [c] : [])
	            }
	        }), i.on("select", function() {
	            var a = i.state().get("selection"),
	                b = [];
	            a.map(function(a) {
	                a = a.toJSON(), null !== a.id && "" !== a.id && b.push(a.id)
	            }), jQuery.ajax({
	                type: "POST",
	                url: ajaxurl,
	                data: "action=up_business_cover_photo&nonce=" + ajaxnonce + "&imgs_ids=" + b + "&bus_id=" + business.businessId,
	                success: function(a) {
	                	location.reload();
	                }
	            })
	        }), i.open()
	    });
	}
});
var DeleteDropdown = {
    getDropdown: function(a, b, c, d) {
        return this.ID = a, this.x = b, this.y = c, this.comm = d, this.dropdown = '<div class="delete-dropdown list-group" style="display:none; position: absolute; left: ' + (b - 45) + "px; top: " + c + 'px;" ><a class="list-group-item wyz-prim-color-txt-hover edit-dropdown-a" data-id="' + this.ID + '" >'+business.editPost+'</a><a class="list-group-item wyz-prim-color-txt-hover delete-dropdown-a" data-id="' + this.ID + '" >'+business.deletePost+'</a><a class="list-group-item wyz-prim-color-txt-hover comm-dropdown-a" data-comm="' + (this.comm ? "closed" : "open") + '" data-id="' + this.ID + '" >' + (this.comm ? business.disableComments : business.enableComments) + "</a></div>", jQuery.parseHTML(this.dropdown)
    }
},
LoginDropdown = {
    getDropdown: function(a, b, c) {
        return this.x = a, this.y = b, this.dropdown = '<div class="login-dropdown list-group'+(c?' post-comm-dropdown':'')+'" style="display:block; position: absolute; '+(c?'right':'left')+': ' + a + "px; top: " + b + 'px;" >' + (!c?'<span>'+business.likePostq+'</span>':'')+'<p>'+business.loginLike+'</p><a class="wyz-button wyz-primary-color wyz-prim-color icon action-btn btn-bg-blue btn-rounded" href="' + business.loginPermalink + '" title="'+business.signin+'"> '+business.signin+'<i class="fa fa-angle-right" aria-hidden="true"></i></a></div>', jQuery.parseHTML(this.dropdown);
    }
};

function wyz_align_gallery_images() {
	var i =1;
	var conWidth = jQuery('.busi-photos-wrapper .sin-photo').width();
	var conHeight = jQuery('.busi-photos-wrapper .sin-photo').height();
	jQuery('.busi-photos-wrapper .sin-photo').each(function(){
		var img = jQuery(this).find('img');
		var w = img.width();
		var h = img.height();
		if(w>h) {
			img.css({"height":"100%","width":"auto","right":(conWidth-w)/2+"px","top":"0px"});
		} else {
			img.css({"width":"100%","height":"auto","top":(conHeight-h)/2+"px", "right":"0px"});
		}
	});
}