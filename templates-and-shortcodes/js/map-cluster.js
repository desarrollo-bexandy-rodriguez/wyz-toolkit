
var wyz_map_loaded = false;

document.addEventListener('DOMContentLoaded', function() {
	wyz_init_load_map();
}, false);

function wyz_init_load_map() {
	if(wyz_map_loaded)return;
	if (typeof google === 'object' && typeof google.maps === 'object') {
		wyz_map_loaded = true;
		wyz_load_map();
	}
}

function wyz_load_map(){

	/*
	 * marker cluster
	 */
	 function MarkerClusterer(t,e,r){this.extend(MarkerClusterer,google.maps.OverlayView),this.map_=t,this.markers_=[],this.clusters_=[],this.sizes=[53,56,66,78,90],this.styles_=[],this.ready_=!1;var s=r||{};this.gridSize_=s.gridSize||60,this.minClusterSize_=s.minimumClusterSize||2,this.maxZoom_=s.maxZoom||null,this.styles_=s.styles||[],this.imagePath_=s.imagePath||this.MARKER_CLUSTER_IMAGE_PATH_,this.imageExtension_=s.imageExtension||this.MARKER_CLUSTER_IMAGE_EXTENSION_,this.zoomOnClick_=!0,void 0!=s.zoomOnClick&&(this.zoomOnClick_=s.zoomOnClick),this.averageCenter_=!1,void 0!=s.averageCenter&&(this.averageCenter_=s.averageCenter),this.setupStyles_(),this.setMap(t),this.prevZoom_=this.map_.getZoom();var o=this;google.maps.event.addListener(this.map_,"zoom_changed",function(){var t=o.map_.getZoom(),e=o.map_.minZoom||0,r=Math.min(o.map_.maxZoom||100,o.map_.mapTypes[o.map_.getMapTypeId()].maxZoom);t=Math.min(Math.max(t,e),r),o.prevZoom_!=t&&(o.prevZoom_=t,o.resetViewport())}),google.maps.event.addListener(this.map_,"idle",function(){o.redraw()}),e&&(e.length||Object.keys(e).length)&&this.addMarkers(e,!1)}function Cluster(t){this.markerClusterer_=t,this.map_=t.getMap(),this.gridSize_=t.getGridSize(),this.minClusterSize_=t.getMinClusterSize(),this.averageCenter_=t.isAverageCenter(),this.center_=null,this.markers_=[],this.bounds_=null,this.clusterIcon_=new ClusterIcon(this,t.getStyles(),t.getGridSize())}function ClusterIcon(t,e,r){t.getMarkerClusterer().extend(ClusterIcon,google.maps.OverlayView),this.styles_=e,this.padding_=r||0,this.cluster_=t,this.center_=null,this.map_=t.getMap(),this.div_=null,this.sums_=null,this.visible_=!1,this.setMap(this.map_)}MarkerClusterer.prototype.MARKER_CLUSTER_IMAGE_PATH_="http://google-maps-utility-library-v3.googlecode.com/svn/trunk/markerclusterer/images/m",MarkerClusterer.prototype.MARKER_CLUSTER_IMAGE_EXTENSION_="png",MarkerClusterer.prototype.extend=function(t,e){return function(t){for(var e in t.prototype)this.prototype[e]=t.prototype[e];return this}.apply(t,[e])},MarkerClusterer.prototype.onAdd=function(){this.setReady_(!0)},MarkerClusterer.prototype.draw=function(){},MarkerClusterer.prototype.setupStyles_=function(){if(!this.styles_.length)for(var t,e=0;t=this.sizes[e];e++)this.styles_.push({url:this.imagePath_+(e+1)+"."+this.imageExtension_,height:t,width:t})},MarkerClusterer.prototype.fitMapToMarkers=function(){for(var t,e=this.getMarkers(),r=new google.maps.LatLngBounds,s=0;t=e[s];s++)r.extend(t.getPosition());this.map_.fitBounds(r)},MarkerClusterer.prototype.setStyles=function(t){this.styles_=t},MarkerClusterer.prototype.getStyles=function(){return this.styles_},MarkerClusterer.prototype.isZoomOnClick=function(){return this.zoomOnClick_},MarkerClusterer.prototype.isAverageCenter=function(){return this.averageCenter_},MarkerClusterer.prototype.getMarkers=function(){return this.markers_},MarkerClusterer.prototype.getTotalMarkers=function(){return this.markers_.length},MarkerClusterer.prototype.setMaxZoom=function(t){this.maxZoom_=t},MarkerClusterer.prototype.getMaxZoom=function(){return this.maxZoom_},MarkerClusterer.prototype.calculator_=function(t,e){for(var r=0,s=t.length,o=s;0!==o;)o=parseInt(o/10,10),r++;return r=Math.min(r,e),{text:s,index:r}},MarkerClusterer.prototype.setCalculator=function(t){this.calculator_=t},MarkerClusterer.prototype.getCalculator=function(){return this.calculator_},MarkerClusterer.prototype.addMarkers=function(t,e){if(t.length)for(var r,s=0;r=t[s];s++)this.pushMarkerTo_(r);else if(Object.keys(t).length)for(var r in t)this.pushMarkerTo_(t[r]);e||this.redraw()},MarkerClusterer.prototype.pushMarkerTo_=function(t){if(t.isAdded=!1,t.draggable){var e=this;google.maps.event.addListener(t,"dragend",function(){t.isAdded=!1,e.repaint()})}this.markers_.push(t)},MarkerClusterer.prototype.addMarker=function(t,e){this.pushMarkerTo_(t),e||this.redraw()},MarkerClusterer.prototype.removeMarker_=function(t){var e=-1;if(this.markers_.indexOf)e=this.markers_.indexOf(t);else for(var r,s=0;r=this.markers_[s];s++)if(r==t){e=s;break}return-1==e?!1:(t.setMap(null),this.markers_.splice(e,1),!0)},MarkerClusterer.prototype.removeMarker=function(t,e){var r=this.removeMarker_(t);return!e&&r?(this.resetViewport(),this.redraw(),!0):!1},MarkerClusterer.prototype.removeMarkers=function(t,e){for(var r,s=!1,o=0;r=t[o];o++){var i=this.removeMarker_(r);s=s||i}return!e&&s?(this.resetViewport(),this.redraw(),!0):void 0},MarkerClusterer.prototype.setReady_=function(t){this.ready_||(this.ready_=t,this.createClusters_())},MarkerClusterer.prototype.getTotalClusters=function(){return this.clusters_.length},MarkerClusterer.prototype.getMap=function(){return this.map_},MarkerClusterer.prototype.setMap=function(t){this.map_=t},MarkerClusterer.prototype.getGridSize=function(){return this.gridSize_},MarkerClusterer.prototype.setGridSize=function(t){this.gridSize_=t},MarkerClusterer.prototype.getMinClusterSize=function(){return this.minClusterSize_},MarkerClusterer.prototype.setMinClusterSize=function(t){this.minClusterSize_=t},MarkerClusterer.prototype.getExtendedBounds=function(t){var e=this.getProjection(),r=new google.maps.LatLng(t.getNorthEast().lat(),t.getNorthEast().lng()),s=new google.maps.LatLng(t.getSouthWest().lat(),t.getSouthWest().lng()),o=e.fromLatLngToDivPixel(r);o.x+=this.gridSize_,o.y-=this.gridSize_;var i=e.fromLatLngToDivPixel(s);i.x-=this.gridSize_,i.y+=this.gridSize_;var a=e.fromDivPixelToLatLng(o),n=e.fromDivPixelToLatLng(i);return t.extend(a),t.extend(n),t},MarkerClusterer.prototype.isMarkerInBounds_=function(t,e){return e.contains(t.getPosition())},MarkerClusterer.prototype.clearMarkers=function(){this.resetViewport(!0),this.markers_=[]},MarkerClusterer.prototype.resetViewport=function(t){for(var e,r=0;e=this.clusters_[r];r++)e.remove();for(var s,r=0;s=this.markers_[r];r++)s.isAdded=!1,t&&s.setMap(null);this.clusters_=[]},MarkerClusterer.prototype.repaint=function(){var t=this.clusters_.slice();this.clusters_.length=0,this.resetViewport(),this.redraw(),window.setTimeout(function(){for(var e,r=0;e=t[r];r++)e.remove()},0)},MarkerClusterer.prototype.redraw=function(){this.createClusters_()},MarkerClusterer.prototype.distanceBetweenPoints_=function(t,e){if(!t||!e)return 0;var r=6371,s=(e.lat()-t.lat())*Math.PI/180,o=(e.lng()-t.lng())*Math.PI/180,i=Math.sin(s/2)*Math.sin(s/2)+Math.cos(t.lat()*Math.PI/180)*Math.cos(e.lat()*Math.PI/180)*Math.sin(o/2)*Math.sin(o/2),a=2*Math.atan2(Math.sqrt(i),Math.sqrt(1-i)),n=r*a;return n},MarkerClusterer.prototype.addToClosestCluster_=function(t){for(var e,r=4e4,s=null,o=(t.getPosition(),0);e=this.clusters_[o];o++){var i=e.getCenter();if(i){var a=this.distanceBetweenPoints_(i,t.getPosition());r>a&&(r=a,s=e)}}if(s&&s.isMarkerInClusterBounds(t))s.addMarker(t);else{var e=new Cluster(this);e.addMarker(t),this.clusters_.push(e)}},MarkerClusterer.prototype.createClusters_=function(){if(this.ready_)for(var t,e=new google.maps.LatLngBounds(this.map_.getBounds().getSouthWest(),this.map_.getBounds().getNorthEast()),r=this.getExtendedBounds(e),s=0;t=this.markers_[s];s++)!t.isAdded&&this.isMarkerInBounds_(t,r)&&this.addToClosestCluster_(t)},Cluster.prototype.isMarkerAlreadyAdded=function(t){if(this.markers_.indexOf)return-1!=this.markers_.indexOf(t);for(var e,r=0;e=this.markers_[r];r++)if(e==t)return!0;return!1},Cluster.prototype.addMarker=function(t){if(this.isMarkerAlreadyAdded(t))return!1;if(this.center_){if(this.averageCenter_){var e=this.markers_.length+1,r=(this.center_.lat()*(e-1)+t.getPosition().lat())/e,s=(this.center_.lng()*(e-1)+t.getPosition().lng())/e;this.center_=new google.maps.LatLng(r,s),this.calculateBounds_()}}else this.center_=t.getPosition(),this.calculateBounds_();t.isAdded=!0,this.markers_.push(t);var o=this.markers_.length;if(o<this.minClusterSize_&&t.getMap()!=this.map_&&t.setMap(this.map_),o==this.minClusterSize_)for(var i=0;o>i;i++)this.markers_[i].setMap(null);return o>=this.minClusterSize_&&t.setMap(null),this.updateIcon(),!0},Cluster.prototype.getMarkerClusterer=function(){return this.markerClusterer_},Cluster.prototype.getBounds=function(){for(var t,e=new google.maps.LatLngBounds(this.center_,this.center_),r=this.getMarkers(),s=0;t=r[s];s++)e.extend(t.getPosition());return e},Cluster.prototype.remove=function(){this.clusterIcon_.remove(),this.markers_.length=0,delete this.markers_},Cluster.prototype.getSize=function(){return this.markers_.length},Cluster.prototype.getMarkers=function(){return this.markers_},Cluster.prototype.getCenter=function(){return this.center_},Cluster.prototype.calculateBounds_=function(){var t=new google.maps.LatLngBounds(this.center_,this.center_);this.bounds_=this.markerClusterer_.getExtendedBounds(t)},Cluster.prototype.isMarkerInClusterBounds=function(t){return this.bounds_.contains(t.getPosition())},Cluster.prototype.getMap=function(){return this.map_},Cluster.prototype.updateIcon=function(){var t=this.map_.getZoom(),e=this.markerClusterer_.getMaxZoom();if(e&&t>e)for(var r,s=0;r=this.markers_[s];s++)r.setMap(this.map_);else{if(this.markers_.length<this.minClusterSize_)return void this.clusterIcon_.hide();var o=this.markerClusterer_.getStyles().length,i=this.markerClusterer_.getCalculator()(this.markers_,o);this.clusterIcon_.setCenter(this.center_),this.clusterIcon_.setSums(i),this.clusterIcon_.show()}},ClusterIcon.prototype.triggerClusterClick=function(){var t=this.cluster_.getMarkerClusterer();google.maps.event.trigger(t,"clusterclick",this.cluster_),t.isZoomOnClick()&&this.map_.fitBounds(this.cluster_.getBounds())},ClusterIcon.prototype.onAdd=function(){if(this.div_=document.createElement("DIV"),this.visible_){var t=this.getPosFromLatLng_(this.center_);this.div_.style.cssText=this.createCss(t),this.div_.innerHTML=this.sums_.text}var e=this.getPanes();e.overlayMouseTarget.appendChild(this.div_);var r=this;google.maps.event.addDomListener(this.div_,"click",function(){r.triggerClusterClick()})},ClusterIcon.prototype.getPosFromLatLng_=function(t){var e=this.getProjection().fromLatLngToDivPixel(t);return e.x-=parseInt(this.width_/2,10),e.y-=parseInt(this.height_/2,10),e},ClusterIcon.prototype.draw=function(){if(this.visible_){var t=this.getPosFromLatLng_(this.center_);this.div_.style.top=t.y+"px",this.div_.style.left=t.x+"px"}},ClusterIcon.prototype.hide=function(){this.div_&&(this.div_.style.display="none"),this.visible_=!1},ClusterIcon.prototype.show=function(){if(this.div_){var t=this.getPosFromLatLng_(this.center_);this.div_.style.cssText=this.createCss(t),this.div_.style.display=""}this.visible_=!0},ClusterIcon.prototype.remove=function(){this.setMap(null)},ClusterIcon.prototype.onRemove=function(){this.div_&&this.div_.parentNode&&(this.hide(),this.div_.parentNode.removeChild(this.div_),this.div_=null)},ClusterIcon.prototype.setSums=function(t){this.sums_=t,this.text_=t.text,this.index_=t.index,this.div_&&(this.div_.innerHTML=t.text),this.useStyle()},ClusterIcon.prototype.useStyle=function(){var t=Math.max(0,this.sums_.index-1);t=Math.min(this.styles_.length-1,t);var e=this.styles_[t];this.url_=e.url,this.height_=e.height,this.width_=e.width,this.textColor_=e.textColor,this.anchor_=e.anchor,this.textSize_=e.textSize,this.backgroundPosition_=e.backgroundPosition},ClusterIcon.prototype.setCenter=function(t){this.center_=t},ClusterIcon.prototype.createCss=function(t){var e=[];e.push("background-image:url("+this.url_+");");var r=this.backgroundPosition_?this.backgroundPosition_:"0 0";e.push("background-position:"+r+";"),e.push("background-size: contain;"),"object"==typeof this.anchor_?("number"==typeof this.anchor_[0]&&this.anchor_[0]>0&&this.anchor_[0]<this.height_?e.push("height:"+(this.height_-this.anchor_[0])+"px; padding-top:"+this.anchor_[0]+"px;"):e.push("height:"+this.height_+"px; line-height:"+this.height_+"px;"),"number"==typeof this.anchor_[1]&&this.anchor_[1]>0&&this.anchor_[1]<this.width_?e.push("width:"+(this.width_-this.anchor_[1])+"px; padding-left:"+this.anchor_[1]+"px;"):e.push("width:"+this.width_+"px; text-align:center;")):e.push("height:"+this.height_+"px; line-height:"+this.height_+"px; width:"+this.width_+"px; text-align:center;");var s=this.textColor_?this.textColor_:"black",o=this.textSize_?this.textSize_:11;return e.push("cursor:pointer; top:"+t.y+"px; left:"+t.x+"px; color:"+s+"; position:absolute; font-size:"+o+"px; font-family:Arial,sans-serif; font-weight:bold"),e.join("")},window.MarkerClusterer=MarkerClusterer,MarkerClusterer.prototype.addMarker=MarkerClusterer.prototype.addMarker,MarkerClusterer.prototype.addMarkers=MarkerClusterer.prototype.addMarkers,MarkerClusterer.prototype.clearMarkers=MarkerClusterer.prototype.clearMarkers,MarkerClusterer.prototype.fitMapToMarkers=MarkerClusterer.prototype.fitMapToMarkers,MarkerClusterer.prototype.getCalculator=MarkerClusterer.prototype.getCalculator,MarkerClusterer.prototype.getGridSize=MarkerClusterer.prototype.getGridSize,MarkerClusterer.prototype.getExtendedBounds=MarkerClusterer.prototype.getExtendedBounds,MarkerClusterer.prototype.getMap=MarkerClusterer.prototype.getMap,MarkerClusterer.prototype.getMarkers=MarkerClusterer.prototype.getMarkers,MarkerClusterer.prototype.getMaxZoom=MarkerClusterer.prototype.getMaxZoom,MarkerClusterer.prototype.getStyles=MarkerClusterer.prototype.getStyles,MarkerClusterer.prototype.getTotalClusters=MarkerClusterer.prototype.getTotalClusters,MarkerClusterer.prototype.getTotalMarkers=MarkerClusterer.prototype.getTotalMarkers,MarkerClusterer.prototype.redraw=MarkerClusterer.prototype.redraw,MarkerClusterer.prototype.removeMarker=MarkerClusterer.prototype.removeMarker,MarkerClusterer.prototype.removeMarkers=MarkerClusterer.prototype.removeMarkers,MarkerClusterer.prototype.resetViewport=MarkerClusterer.prototype.resetViewport,MarkerClusterer.prototype.repaint=MarkerClusterer.prototype.repaint,MarkerClusterer.prototype.setCalculator=MarkerClusterer.prototype.setCalculator,MarkerClusterer.prototype.setGridSize=MarkerClusterer.prototype.setGridSize,MarkerClusterer.prototype.setMaxZoom=MarkerClusterer.prototype.setMaxZoom,MarkerClusterer.prototype.onAdd=MarkerClusterer.prototype.onAdd,MarkerClusterer.prototype.draw=MarkerClusterer.prototype.draw,Cluster.prototype.getCenter=Cluster.prototype.getCenter,Cluster.prototype.getSize=Cluster.prototype.getSize,Cluster.prototype.getMarkers=Cluster.prototype.getMarkers,ClusterIcon.prototype.onAdd=ClusterIcon.prototype.onAdd,ClusterIcon.prototype.draw=ClusterIcon.prototype.draw,ClusterIcon.prototype.onRemove=ClusterIcon.prototype.onRemove,Object.keys=Object.keys||function(t){var e=[];for(var r in t)t.hasOwnProperty(r)&&e.push(r);return e};

	/*
	 * Range slider.
	 */
	!function(e){if("object"==typeof exports&&"undefined"!=typeof module)module.exports=e();else if("function"==typeof define&&define.amd)define([],e);else{var t;t="undefined"!=typeof window?window:"undefined"!=typeof global?global:"undefined"!=typeof self?self:this,t.rangesliderJs=e()}}(function(){return function e(t,n,i){function s(o,a){if(!n[o]){if(!t[o]){var l="function"==typeof require&&require;if(!a&&l)return l(o,!0);if(r)return r(o,!0);var h=new Error("Cannot find module '"+o+"'");throw h.code="MODULE_NOT_FOUND",h}var u=n[o]={exports:{}};t[o][0].call(u.exports,function(e){var n=t[o][1][e];return s(n?n:e)},u,u.exports,e,t,n,i)}return n[o].exports}for(var r="function"==typeof require&&require,o=0;o<i.length;o++)s(i[o]);return s}({1:[function(e,t,n){function i(e,t,n){return n>t?t>e?t:e>n?n:e:n>e?n:e>t?t:e}t.exports=i},{}],2:[function(e,t,n){"use strict";function i(e,t,n){var i=e.getElementById(t);if(i)n(i);else{var s=e.getElementsByTagName("head")[0];i=e.createElement("style"),null!=t&&(i.id=t),n(i),s.appendChild(i)}return i}t.exports=function(e,t,n){var s=t||document;if(s.createStyleSheet){var r=s.createStyleSheet();return r.cssText=e,r.ownerNode}return i(s,n,function(t){t.styleSheet?t.styleSheet.cssText=e:t.innerHTML=e})},t.exports.byUrl=function(e){if(document.createStyleSheet)return document.createStyleSheet(e).ownerNode;var t=document.getElementsByTagName("head")[0],n=document.createElement("link");return n.rel="stylesheet",n.href=e,t.appendChild(n),n}},{}],3:[function(e,t,n){(function(e){function n(){try{var e=new i("cat",{detail:{foo:"bar"}});return"cat"===e.type&&"bar"===e.detail.foo}catch(e){}return!1}var i=e.CustomEvent;t.exports=n()?i:"function"==typeof document.createEvent?function(e,t){var n=document.createEvent("CustomEvent");return t?n.initCustomEvent(e,t.bubbles,t.cancelable,t.detail):n.initCustomEvent(e,!1,!1,void 0),n}:function(e,t){var n=document.createEventObject();return n.type=e,t?(n.bubbles=Boolean(t.bubbles),n.cancelable=Boolean(t.cancelable),n.detail=t.detail):(n.bubbles=!1,n.cancelable=!1,n.detail=void 0),n}}).call(this,"undefined"!=typeof global?global:"undefined"!=typeof self?self:"undefined"!=typeof window?window:{})},{}],4:[function(e,t,n){var i=e("date-now");t.exports=function(e,t,n){function s(){var u=i()-l;t>u&&u>0?r=setTimeout(s,t-u):(r=null,n||(h=e.apply(a,o),r||(a=o=null)))}var r,o,a,l,h;return null==t&&(t=100),function(){a=this,o=arguments,l=i();var u=n&&!r;return r||(r=setTimeout(s,t)),u&&(h=e.apply(a,o),a=o=null),h}}},{"date-now":5}],5:[function(e,t,n){function i(){return(new Date).getTime()}t.exports=Date.now||i},{}],6:[function(e,t,n){"use strict";var i=function(e){return"number"==typeof e&&!isNaN(e)},s=function(e,t){t=t||t.currentTarget;var n=t.getBoundingClientRect(),s=e.originalEvent||e,r=e.touches&&e.touches.length,o=0,a=0;return r?i(e.touches[0].pageX)&&i(e.touches[0].pageY)?(o=e.touches[0].pageX,a=e.touches[0].pageY):i(e.touches[0].clientX)&&i(e.touches[0].clientY)&&(o=s.touches[0].clientX,a=s.touches[0].clientY):i(e.pageX)&&i(e.pageY)?(o=e.pageX,a=e.pageY):e.currentPoint&&i(e.currentPoint.x)&&i(e.currentPoint.y)&&(o=e.currentPoint.x,a=e.currentPoint.y),{x:o-n.left,y:a-n.top}};t.exports=s},{}],7:[function(e,t,n){"use strict";var i=e("number-is-nan");t.exports=Number.isFinite||function(e){return!("number"!=typeof e||i(e)||e===1/0||e===-(1/0))}},{"number-is-nan":8}],8:[function(e,t,n){"use strict";t.exports=Number.isNaN||function(e){return e!==e}},{}],9:[function(e,t,n){function i(e,t){t=t||{},this.element=e,this.options=t,this.onSlideEventsCount=-1,this.isInteracting=!1,this.needTriggerEvents=!1,this.identifier="js-"+l.PLUGIN_NAME+"-"+h++,this.min=a.getFirstNumberLike(t.min,parseFloat(e.getAttribute("min")),0),this.max=a.getFirstNumberLike(t.max,parseFloat(e.getAttribute("max")),l.MAX_SET_BY_DEFAULT),this.value=a.getFirstNumberLike(t.value,parseFloat(e.value),this.min+(this.max-this.min)/2),this.step=a.getFirstNumberLike(t.step,parseFloat(e.getAttribute("step")),l.STEP_SET_BY_DEFAULT),this.percent=null,this._updatePercentFromValue(),this.toFixed=d(this.step),this.range=u(l.RANGE_CLASS),this.range.id=this.identifier,this.fillBg=u(l.FILL_BG_CLASS),this.fill=u(l.FILL_CLASS),this.handle=u(l.HANDLE_CLASS),["fillBg","fill","handle"].forEach(function(e){this.range.appendChild(this[e])},this),["min","max","step"].forEach(function(t){e.setAttribute(t,""+this[t])},this),this._setValue(this.value),a.insertAfter(e,this.range),e.style.position="absolute",e.style.width="1px",e.style.height="1px",e.style.overflow="hidden",e.style.opacity="0",["_update","_handleDown","_handleMove","_handleEnd","_startEventListener","_changeEventListener"].forEach(function(e){this[e]=this[e].bind(this)},this),this._init(),window.addEventListener("resize",r(this._update,l.HANDLE_RESIZE_DEBOUNCE)),l.START_EVENTS.forEach(function(e){this.range.addEventListener(e,this._startEventListener)},this),e.addEventListener("change",this._changeEventListener)}e("./styles/base.css");var s=e("clamp"),r=e("debounce"),o=e("ev-pos"),a=e("./utils"),l={MAX_SET_BY_DEFAULT:100,HANDLE_RESIZE_DEBOUNCE:100,RANGE_CLASS:"rangeslider",FILL_CLASS:"range_fill",FILL_BG_CLASS:"range_fill_bg",HANDLE_CLASS:"range_handle",DISABLED_CLASS:"range-disabled",STEP_SET_BY_DEFAULT:1,START_EVENTS:["mousedown","touchstart","pointerdown"],MOVE_EVENTS:["mousemove","touchmove","pointermove"],END_EVENTS:["mouseup","touchend","pointerup"],PLUGIN_NAME:"rangeslider-js"},h=0,u=function(e){var t=document.createElement("div");return t.classList.add(e),t},d=function(e){return(e+"").replace(".","").length-1};i.prototype.constructor=i,i.prototype._init=function(){this.options.onInit&&this.options.onInit(),this._update()},i.prototype._updatePercentFromValue=function(){this.percent=(this.value-this.min)/(this.max-this.min)},i.prototype._startEventListener=function(e,t){var n=e.target,i=!1,s=this.identifier;a.forEachAncestorsAndSelf(n,function(e){return i=e.id===s&&!e.classList.contains(l.DISABLED_CLASS)}),i&&this._handleDown(e,t)},i.prototype._changeEventListener=function(e,t){t&&t.origin===this.identifier||this._setPosition(this._getPositionFromValue(e.target.value))},i.prototype._update=function(){this.handleWidth=a.getDimension(this.handle,"offsetWidth"),this.rangeWidth=a.getDimension(this.range,"offsetWidth"),this.maxHandleX=this.rangeWidth-this.handleWidth,this.grabX=this.handleWidth/2,this.position=this._getPositionFromValue(this.value),this.range.classList[this.element.disabled?"add":"remove"](l.DISABLED_CLASS),this._setPosition(this.position),this._updatePercentFromValue(),a.emit(this.element,"change")},i.prototype._listen=function(e){var t=(e?"add":"remove")+"EventListener";l.MOVE_EVENTS.forEach(function(e){document[t](e,this._handleMove)},this),l.END_EVENTS.forEach(function(e){document[t](e,this._handleEnd),this.range[t](e,this._handleEnd)},this)},i.prototype._handleDown=function(e){if(e.preventDefault(),this.isInteracting=!0,this._listen(!0),!e.target.classList.contains(l.HANDLE_CLASS)){var t=o(e,this.range).x,n=this.range.getBoundingClientRect().left,i=this.handle.getBoundingClientRect().left-n;this._setPosition(t-this.grabX),t>=i&&t<i+this.handleWidth&&(this.grabX=t-i),this._updatePercentFromValue()}},i.prototype._handleMove=function(e){this.isInteracting=!0,e.preventDefault();var t=o(e,this.range).x;this._setPosition(t-this.grabX)},i.prototype._handleEnd=function(e){e.preventDefault(),this._listen(!1),a.emit(this.element,"change",{origin:this.identifier}),(this.isInteracting||this.needTriggerEvents)&&this.options.onSlideEnd&&this.options.onSlideEnd(this.value,this.percent,this.position),this.onSlideEventsCount=0,this.isInteracting=!1},i.prototype._setPosition=function(e){var t=this._getValueFromPosition(s(e,0,this.maxHandleX)),n=this._getPositionFromValue(t);this.fill.style.width=n+this.grabX+"px",this.handle.style.webkitTransform=this.handle.style.transform="translate("+n+"px, 0px)",this._setValue(t),this.position=n,this.value=t,this._updatePercentFromValue(),(this.isInteracting||this.needTriggerEvents)&&(this.options.onSlideStart&&0===this.onSlideEventsCount&&this.options.onSlideStart(this.value,this.percent,this.position),this.options.onSlide&&this.options.onSlide(this.value,this.percent,this.position)),this.onSlideEventsCount++},i.prototype._getPositionFromValue=function(e){var t=(e-this.min)/(this.max-this.min);return t*this.maxHandleX},i.prototype._getValueFromPosition=function(e){var t=e/(this.maxHandleX||1),n=this.step*Math.round(t*(this.max-this.min)/this.step)+this.min;return Number(n.toFixed(this.toFixed))},i.prototype._setValue=function(e){e===this.value&&e===this.element.value||(this.value=this.element.value=e,a.emit(this.element,"input",{origin:this.identifier}))},i.prototype.update=function(e,t){return e=e||{},this.needTriggerEvents=!!t,a.isFiniteNumber(e.min)&&(this.element.setAttribute("min",""+e.min),this.min=e.min),a.isFiniteNumber(e.max)&&(this.element.setAttribute("max",""+e.max),this.max=e.max),a.isFiniteNumber(e.step)&&(this.element.setAttribute("step",""+e.step),this.step=e.step,this.toFixed=d(e.step)),a.isFiniteNumber(e.value)&&this._setValue(e.value),this._update(),this.onSlideEventsCount=0,this.needTriggerEvents=!1,this},i.prototype.destroy=function(){window.removeEventListener("resize",this._update,!1),l.START_EVENTS.forEach(function(e){this.range.removeEventListener(e,this._startEventListener)},this),this.element.removeEventListener("change",this._changeEventListener),this.element.style.cssText="",delete this.element[l.PLUGIN_NAME],this.range.parentNode.removeChild(this.range)},i.create=function(e,t){function n(e){e[l.PLUGIN_NAME]=e[l.PLUGIN_NAME]||new i(e,t)}e.length?Array.prototype.slice.call(e).forEach(function(e){n(e)}):n(e)},t.exports=i},{"./styles/base.css":10,"./utils":11,clamp:1,debounce:4,"ev-pos":6}],10:[function(e,t,n){var i=e("./../../node_modules/cssify"),s=".rangeslider {\n    position: relative;\n    cursor: pointer;\n    height: 30px;\n    width: 100%;\n}\n.rangeslider,\n.rangeslider__fill,\n.rangeslider__fill__bg {\n    display: block;\n}\n.rangeslider__fill,\n.rangeslider__fill__bg,\n.rangeslider__handle {\n    position: absolute;\n}\n.rangeslider__fill,\n.rangeslider__fill__bg {\n    top: calc(50% - 6px);\n    height: 12px;\n    z-index: 2;\n    background: #29e;\n    border-radius: 10px;\n    will-change: width;\n}\n.rangeslider__handle {\n    display: inline-block;\n    top: calc(50% - 15px);\n    background: #29e;\n    width: 30px;\n    height: 30px;\n    z-index: 3;\n    cursor: pointer;\n    border: solid 2px #ffffff;\n    border-radius: 50%;\n}\n.rangeslider__handle:active {\n    background: #107ecd;\n}\n.rangeslider__fill__bg {\n    background: #ccc;\n    width: 100%;\n}\n.rangeslider--disabled {\n    opacity: 0.4;\n}\n.rangeslider--slim .rangeslider {\n    height: 25px;\n}\n.rangeslider--slim .rangeslider:active .rangeslider__handle {\n    width: 21px;\n    height: 21px;\n    top: calc(50% - 10px);\n    background: #29e;\n}\n.rangeslider--slim .rangeslider__fill,\n.rangeslider--slim .rangeslider__fill__bg {\n    top: calc(50% - 1px);\n    height: 2px;\n}\n.rangeslider--slim .rangeslider__handle {\n    will-change: width, height, top;\n    -webkit-transition: width 0.1s ease-in-out, height 0.1s ease-in-out, top 0.1s ease-in-out;\n    transition: width 0.1s ease-in-out, height 0.1s ease-in-out, top 0.1s ease-in-out;\n    width: 14px;\n    height: 14px;\n    top: calc(50% - 7px);\n}\n";i(s,void 0,"_1fcddbb"),t.exports=s},{"./../../node_modules/cssify":2}],11:[function(e,t,n){function i(e){return!(0!==e.offsetWidth&&0!==e.offsetHeight&&e.open!==!1)}function s(e){return d(parseFloat(e))||d(e)}function r(){if(!arguments.length)return null;for(var e=0,t=arguments.length;t>e;e++)if(s(arguments[e]))return arguments[e]}function o(e){for(var t=[],n=e.parentNode;n&&i(n);)t.push(n),n=n.parentNode;return t}function a(e,t){function n(e){"undefined"!=typeof e.open&&(e.open=!e.open)}var i,s=o(e),r=s.length,a=e[t],l=[],h=0;if(r){for(h=0;r>h;h++)i=s[h].style,l[h]=i.display,i.display="block",i.height="0",i.overflow="hidden",i.visibility="hidden",n(s[h]);for(a=e[t],h=0;r>h;h++)i=s[h].style,n(s[h]),i.display=l[h],i.height="",i.overflow="",i.visibility=""}return a}function l(e,t){for(t(e);e.parentNode&&!t(e);)e=e.parentNode;return e}function h(e,t){e.parentNode.insertBefore(t,e.nextSibling)}var u=e("custom-event"),d=e("is-finite");t.exports={emit:function(e,t,n){e.dispatchEvent(new u(t,n))},isFiniteNumber:d,getFirstNumberLike:r,getDimension:a,insertAfter:h,forEachAncestorsAndSelf:l}},{"custom-event":3,"is-finite":7}]},{},[9])(9)});


	var latLng;
	var map;
	var searching = false;
	var geoEnabled = false;
	var myLat = 0;
	var myLon = 0;
	var myTrueLat = 0;
	var myTrueLon = 0;
	var radVal = 0;
	var offset = 1;
	var append = '';
	var appendTop = '';
	var appendBottom = '';
	var locationFirstRun = true;
	var mapFirstLoad = true;
	var pageFirstLoad = true;
	var sidebarWidth =0;

	var mapCntr = 0;
	var markers = [];
	var infowindow;
	var bounds;
	var content;
	var gpsLen = globalMap.GPSLocations.length;
	var lastIndex = 0;
	var searchMarker = globalMap.myLocationMarker;
	var markerAnchorX;
	var markerAnchorY;
	var markerWidthX;
	var markerWidthY;
	var myoverlay;

	var page = 0;

	var path = wyz_plg_ref + "templates-and-shortcodes\/images\/";
	var clusterStyles = [{
		textColor: 'grey',
		url: path + "mrkr-clstr-sml.png",
		height: 50,
		width: 50
	}, {
		textColor: 'grey',
		url: path + "mrkr-clstr-mdm.png",
		height: 50,
		width: 50
	}, {
		textColor: 'grey',
		url: path + "mrkr-clstr-lrg.png",
		height: 50,
		width: 50
	}];
	var markerCluster;

	function initMap() {
		if(searching && jQuery.isEmptyObject(globalMap.GPSLocations)){
			toastr.info( globalMap.translations.noBusinessesFound );
		}

		// Hide Business list under map
		jQuery('#business-list').hide();

		if (!globalMap.defCoor || globalMap.defCoor.latitude === '' || undefined === globalMap.defCoor.latitude){
			latLng = new google.maps.LatLng(0, 0);
			globalMap.defCoor = new Object;
			globalMap.defCoor.latitude = 0;
			globalMap.defCoor.longitude = 0;
			globalMap.defCoor.zoom = 11;
		}
		else latLng = new google.maps.LatLng(parseFloat(globalMap.defCoor.latitude), parseFloat(globalMap.defCoor.longitude));
		var scrollwheel = 'on' == mapScrollZoom ? true : false;
		var options = {
			zoom: parseInt(globalMap.defCoor.zoom),
			scrollwheel : scrollwheel,
			center: latLng,
			mapTypeId: google.maps.MapTypeId.ROADMAP,
		};
		map = new google.maps.Map(document.getElementById('home-map'), options);

		if ( '' != globalMap.mapSkin ) {
			map.setOptions({styles: globalMap.mapSkin});
		}

		myoverlay = new google.maps.OverlayView();
	    myoverlay.draw = function () {
	        this.getPanes().markerLayer.id='markerLayer';
	    };
		myoverlay.setMap(map);


		mapCntr = 0;
		markers = [];
		infowindow = new google.maps.InfoWindow();
		bounds = new google.maps.LatLngBounds();
		
		gpsLen = globalMap.GPSLocations.length;
		lastIndex = 0;

		markerCluster = new MarkerClusterer(map, markers, { styles: clusterStyles });
		if (!geoEnabled || !searching) {

		}
	}

	var slided = false;

	function updateMap(){
		var marker;
		gpsLen = globalMap.GPSLocations.length;
		for (var ii = lastIndex; ii<gpsLen; ii++){
			if(''!=globalMap.GPSLocations[ii].latitude&&''!=globalMap.GPSLocations[ii].longitude){
				var latlng = new google.maps.LatLng(parseFloat(globalMap.GPSLocations[ii].latitude), parseFloat(globalMap.GPSLocations[ii].longitude));

				content = '<div id="content">'+
					'<div style="display:none;">' + globalMap.businessNames[ii] + '</div>' +
					'<div id="siteNotice">'+
					'</div>'+
					'<div id="mapBodyContent">'+
					('' != globalMap.businessLogoes[ii] ? globalMap.businessLogoes[ii] : '<img class="business-logo-marker wp-post-image" src="'+globalMap.defLogo+'"/>' )
					+
					'<h4>'+globalMap.businessNames[ii]+'</h4>'+	
					( null != globalMap.afterBusinessNames[ii] ? ( '<div><p>' + globalMap.afterBusinessNames[ii] + '</p></div>' ) : '' ) +
					'<a href="'+globalMap.businessPermalinks[ii]+'"' + ( 2 == globalMap.templateType ? '' : ' class="wyz-button" style="background-color:' + globalMap.businessCategoriesColors[ii] + ';"' ) + '>'+globalMap.translations.viewDetails+'</a>'+		
					'</div>'+
					'</div>';

				if ('' !== globalMap.markersWithIcons[ii]) {
					marker = new google.maps.Marker({
						position: latlng,
						icon: {
							url: globalMap.markersWithIcons[ii],
							size: new google.maps.Size(markerWidthX,markerWidthY),
							origin: new google.maps.Point(0, 0),
							anchor: new google.maps.Point(markerAnchorX, markerAnchorY),
						},
						info: content,
						shadow: globalMap.myLocationMarker,
						optimized: false,
						category: parseInt(globalMap.businessCategories[ii]),
						busName: globalMap.businessNames[ii],
						busId: globalMap.businessIds[ii],
						busPermalink:globalMap.businessPermalinks[ii],
						favorite:globalMap.favorites[ii],
						galleryLoaded: false,
						gallery: [],
					});

				} else{
					marker = new google.maps.Marker({
						busName: globalMap.businessNames[ii],
						info: content,
						busId: globalMap.businessIds[ii],
						position: latlng,
						galleryLoaded: false,
						favorite:globalMap.favorites[ii],
						gallery: [],
					});
				}
				if(2 != globalMap.templateType ){
					marker.setAnimation(google.maps.Animation.DROP);
				}

				if(searching || 'on' == mapAutoZoom) {
					bounds.extend(marker.position);
					map.fitBounds(bounds);
				}
				
				var galleryContainer = jQuery('.page-map-right-content .map-info-gallery');


				google.maps.event.addListener(marker, 'click', function() {
					if ( globalMap.templateType == 1){
						infowindow.setContent(this.info);
						infowindow.open(map, this);
					}
					
					this.setAnimation(google.maps.Animation.oo);
					jQuery('.map-company-info .company-logo').attr( 'href',this.busPermalink );
					jQuery('.map-company-info #map-company-info-name>a').attr( 'href',this.busPermalink ).html(this.busName);
					jQuery('.page-map-right-content #rate-bus').attr('href',this.busPermalink +'#'+globalMap.tabs['rating'] );

					jQuery('.map-company-info #map-company-info-slogan').html('');
					jQuery('.page-map-right-content .map-company-info .company-logo img').attr('src','');
					jQuery('.map-company-info #map-company-info-rating').html('');
					if(jQuery('.map-company-info #map-company-info-name .verified-icon').length)
						jQuery('.map-company-info #map-company-info-name .verified-icon').remove();

					if(globalMap.favEnabled){
						var favBus = jQuery('.page-map-right-content .fav-bus');
						favBus.data("busid",this.busId );

						if ( this.favorite){
							favBus.find('i').removeClass('fa-heart-o');
							favBus.find('i').addClass('fa-heart');
							favBus.data('fav',1 );

						} else {
							favBus.find('i').removeClass('fa-heart');
							favBus.find('i').addClass('fa-heart-o');
							favBus.data('fav',0 );
						}
					}

					if(!slided){
						jQuery('#slidable-map-sidebar').animate({right:'0'}, {queue: false, duration: 500});
						slided = true;
					}

					galleryContainer.html('');


					if(!this.galleryLoaded){
						var This = this;
						
						jQuery('.page-map-right-content .search-wrapper #map-sidebar-loading').addClass('loading-spinner');
						jQuery('.page-map-right-content .search-wrapper').css('background-image','');

						jQuery.ajax({
							type: "POST",
							url: ajaxurl,
							data: "action=business_map_sidebar_data&nonce=" + ajaxnonce + "&bus_id=" + this.busId ,
							success: function(result) {

								result = JSON.parse(result);

								This.galleryLoaded = true;
								This.gallery = result;

								jQuery('.map-company-info #map-company-info-slogan').html(result.slogan);

								jQuery('.map-company-info #map-company-info-name>a').before(result.verified);

								jQuery('.page-map-right-content .map-company-info .company-logo img').attr('src',result.logo);

								jQuery('.page-map-right-content .search-wrapper #map-sidebar-loading').removeClass('loading-spinner');

								for(var i=0;i<result.gallery.length;i++){
									galleryContainer.append( '<li><img src="'+result.gallery.thumb[i]+'" alt=""></li>' );
								}
								if ( result.gallery.length > 0)
									jQuery('.page-map-right-content .map-info-gallery li:last-child').append('<a class="gal-link" href="'+This.busPermalink+'#'+globalMap.tabs['photo']+'">'+globalMap.translations.viewAll+'</a>');
								jQuery('.map-company-info #map-company-info-desc').html(result.slogan );
								jQuery('.map-company-info #map-company-info-rating').html(result.ratings );
								jQuery('.page-map-right-content .search-wrapper').css('background-image','url('+result.banner_image+')');
								jQuery('.map-info-links').append(result.share);
								if ( result.canBooking) {
									jQuery('.page-map-right-content #book-bus').attr('href',This.busPermalink +'#'+globalMap.tabs['booking'] );
									jQuery('.page-map-right-content #book-bus').parent().css('display','block');
									jQuery('.page-map-right-content .map-info-links li').each(function(){
										jQuery(this).removeClass('three-way-width');
									});
								} else {
									jQuery('.page-map-right-content #book-bus').attr('href','');
									jQuery('.page-map-right-content #book-bus').parent().css('display','none');
									jQuery('.page-map-right-content .map-info-links li').each(function(){
										jQuery(this).addClass('three-way-width');
									});
								}
								jQuery('.page-map-right-content .map-info-gallery li .gal-link').css('line-height',jQuery('.page-map-right-content .map-info-gallery').width()/4+'px');
							}
						});
					} else {
						jQuery('.page-map-right-content .search-wrapper #map-sidebar-loading').removeClass('loading-spinner');
						for(var i=0;i<this.gallery.gallery.length;i++){
							galleryContainer.append( '<li><img src="'+this.gallery.gallery.thumb[i]+'" alt=""></li>' );
						}

						jQuery('.page-map-right-content .map-company-info .company-logo img').attr('src',this.gallery.logo);
						jQuery('.map-company-info #map-company-info-slogan').html(this.gallery.slogan);
						jQuery('.map-company-info #map-company-info-name>a').before(this.gallery.verified);

						if(this.gallery.gallery.length)
							jQuery('.page-map-right-content .map-info-gallery li:last-child').append('<a class="gal-link" href="'+this.busPermalink+'#'+globalMap.tabs['photo']+'">'+globalMap.translations.viewAll+'</a>');
						jQuery('.map-company-info #map-company-info-desc').html(this.gallery.slogan );
						jQuery('.map-company-info #map-company-info-rating').html(this.gallery.ratings );
						jQuery('.page-map-right-content .search-wrapper').css('background-image','url('+this.gallery.banner_image+')');
						jQuery('.map-info-links').append(this.gallery.share);
						if ( this.gallery.canBooking) {
							jQuery('.page-map-right-content #book-bus').attr('href',this.busPermalink +'#'+globalMap.tabs['booking'] );
							jQuery('.page-map-right-content #book-bus').parent().css('display','block');
							jQuery('.page-map-right-content .map-info-links li').each(function(){
								jQuery(this).css('width','25%');
							});
						} else {
							jQuery('.page-map-right-content #book-bus').attr('href','');
							jQuery('.page-map-right-content #book-bus').parent().css('display','none');
							jQuery('.page-map-right-content .map-info-links li').each(function(){
								jQuery(this).css('width','33%');
							});
						}
						jQuery('.page-map-right-content .map-info-gallery li .gal-link').css('line-height',jQuery('.page-map-right-content .map-info-gallery').width()/4+'px');
					}
				});

				

				markers.push(marker);
				if( 0 >= radVal && ( searching || 'on' == mapAutoZoom )&& marker != undefined ) {
					bounds.extend(marker.position);
					map.fitBounds(bounds);
				}
			}
			
			mapCntr++;
		}
		if( pageFirstLoad && globalMap.onLoadLocReq &&globalMap.geolocation && navigator.geolocation && 1>globalMap.defRad) {
			jQuery('#map-mask').fadeIn('"slow"');
			var la,lo;
			navigator.geolocation.getCurrentPosition(function(position) {
				la = position.coords.latitude;
				lo = position.coords.longitude;

				jQuery('#map-mask').fadeOut('"fast"');
				marker = new google.maps.Marker({
					position: { lat: parseFloat(la), lng: parseFloat(lo) },
					icon: {
						url: searchMarker,
						size: new google.maps.Size(40,55),
						origin: new google.maps.Point(0, 0),
						anchor: new google.maps.Point(20, 55),
					},
					map: map
				});
				if(2 != globalMap.templateType )
					marker.setAnimation(google.maps.Animation.DROP);
				markers.push(marker);
				map.setCenter({lat:la, lng:lo});
			}, function() {
				handleLocationError(1);
			});
		} else {
			if (globalMap.geolocation && pageFirstLoad && globalMap.onLoadLocReq && 1>globalMap.defRad){
				handleLocationError(3);
			}
		}

		pageFirstLoad=false;
		if ((geoEnabled||'dropdown' != globalMap.filterType) && (searching || (globalMap.defRad>0 && globalMap.onLoadLocReq)) && (0!=myLat||0!=myLon)) {

			marker = new google.maps.Marker({
				position: { lat: parseFloat(myLat), lng: parseFloat(myLon) },
				icon: {
					url: searchMarker,
					size: new google.maps.Size(40,55),
					origin: new google.maps.Point(0, 0),
					anchor: new google.maps.Point(20, 55),
				},
				map: map
			});
			if(2 != globalMap.templateType )
				marker.setAnimation(google.maps.Animation.DROP);

			//setup radius multiplier in miles or km
			var radMult = ('km'==globalMap.radiusUnit ? 1000 : 1609.34);
			// Add circle overlay and bind to marker
			var circle = new google.maps.Circle({
				map: map,
				radius: radVal * radMult,
				fillColor: '#42c2ff',
				strokeColor: '#00aeff',
				strokeWeight: 1
			});
			circle.bindTo('center', marker, 'position');

			bounds.extend(marker.position);
				
			map.fitBounds(bounds);

			var sz = 0;
			sz = (radVal < 101 ? 8 : (radVal < 201 ? 7 : (radVal < 401 ? 6 : radVal < 501 ? 5 : 0)));
			if (0 !== sz)
				map.setZoom(sz);
		} else {
			var sz = map.getZoom();
			if(!isNaN(sz)){
				if(sz>3)
					sz--;
				map.setZoom(sz);
			}
		}

		// all markers set and added to map, update marker cluster

		markerCluster = new MarkerClusterer(map, markers, { styles: clusterStyles });
		lastIndex = gpsLen;
	}

	function initMapFirst(){
		initMap();
		if(globalMap.isListingPage)
			updateBusinessList();
	}


	function paginateBusinessList(){
		append = appendTop = appendBottom = '';
		if('' != globalMap.businessList){
			if(globalMap.hasBefore || globalMap.hasAfter){
				if(globalMap.hasBefore)
					append += '<li class="prev-page float-left">' + 
						'<button class="wyz-primary-color wyz-prim-color btn-square list-paginate" data-offset="-1"><i class="fa fa-angle-left"> </i> ' + globalMap.translations.prev + '</button></li>';
				if(globalMap.hasAfter){
					append += '<li class="next-page float-right">'+
						'<button class="wyz-primary-color wyz-prim-color btn-square list-paginate" data-offset="1">' + globalMap.translations.nxt + ' <i class="fa fa-angle-right"></i></button></li>';
				}
				if('' != append){
					appendTop = '<div class="blog-pagination fix" style="margin-bottom:20px;margin-top:0;"><ul>' + append + '</ul></div>';
					appendBottom = '<div class="blog-pagination fix" style="margin-bottom:30px;"><ul>' + append + '</ul></div>';
				}
			}
		}
	}

	// Display Businesses list under the map
	function updateBusinessList(){
		if('' != globalMap.businessList){
			paginateBusinessList();
			//jQuery('#business-list').hide();
			if(globalMap.ess_grid_shortcode == '') {
			jQuery('#business-list').html(appendTop + '<div class="bus-list-container">' + globalMap.businessList + '</div>' + appendBottom );
			 }
			else {
			jQuery('#business-list').html(appendTop + '<div class="bus-list-container">' + globalMap.ess_grid_shortcode + '</div>' + appendBottom);
			}
			setTimeout(function(){ jQuery('#business-list').show(); jQuery('#business-list').resize();}, 100);
		}
	}


	var active;
	function mapSearch() {
		if ('dropdown' != globalMap.filterType){
			var tmpMapLocSearch = jQuery('#wyz-loc-filter-txt').val();
			if ( '' == tmpMapLocSearch){
				jQuery("#loc-filter-lat").val('');
				jQuery("#loc-filter-lon").val('');
				jQuery("#wyz-loc-filter").val('');
			}
		}


		geoEnabled = ( globalMap.geolocation && navigator.geolocation && 0 < radVal && 500>= radVal )?true:false;

		if ( geoEnabled && (isNaN(radVal) || 0 > radVal || 500 < radVal) )
			toastr.warning( globalMap.translations.notValidRad);
		else {
			var catId = jQuery("#wyz-cat-filter").val();
			if ( mapFirstLoad && undefined != globalMap.defCat && null != globalMap.defCat )
				catId = globalMap.defCat;
			var busName = jQuery("#map-names").val();

			jQuery('#map-mask').fadeIn('"slow"');
			jQuery('#map-loading').fadeIn('"fast"');
			
			var locData = jQuery("#wyz-loc-filter").val();
			if ( mapFirstLoad && undefined != globalMap.defLoc && null != globalMap.defLoc )
				locData = globalMap.defLoc;
			var locId = '';

			if ( 'dropdown' == globalMap.filterType ) {

				if( -1 != locData && '' != locData){
					locData = JSON.parse(locData);
					myLat = locData.lat;
					myLon = locData.lon;
					searchMarker = globalMap.locLocationMarker;
				}else if(geoEnabled){
					myLat = myTrueLat;
					myLon = myTrueLon;
				}else{
					searchMarker = globalMap.myLocationMarker;
				}
				
				locId = locData.id;

				if( undefined == locId )
					locId = '';
			} else {

				if ( '' != jQuery("#loc-filter-lat").val()){
					myLat = jQuery("#loc-filter-lat").val();
					myLon = jQuery("#loc-filter-lon").val();
					locId = '';
					if ( radVal<1)radVal=500;
				} else if(radVal>0) {
					myLat = myTrueLat;
					myLon = myTrueLon;
				}
				searchMarker = globalMap.myLocationMarker;
			}
			page = 0;

			if(jQuery.active>0&&undefined!=active)
				active.abort();

			if(slided){
				jQuery('#slidable-map-sidebar').animate({right:-sidebarWidth}, {queue: false, duration: 500});
				slided = false;
			}

			if (-1 == catId && '' === busName && "" == locId && 'text' != globalMap.filterType )
				ajax_map_search('', '', '', geoEnabled);
			else
				ajax_map_search(catId, busName, locId, geoEnabled);

			if(mapFirstLoad)
				mapFirstLoad = false;
			else
				searching = true;
		}
	}

	var input_interval;

	function intialize() {
		infowindow = new google.maps.InfoWindow();
		bounds = new google.maps.LatLngBounds();
		input_interval = setTimeout(input_autocomplete, 500);
	}

	function input_autocomplete() {
		if (!(typeof google.maps.places === 'object') )
			return;
		clearInterval(input_interval);
		input = document.getElementById('wyz-loc-filter-txt');
		autocomplete = new google.maps.places.Autocomplete(input);
		google.maps.event.addListener(autocomplete, 'place_changed', function () {
			var place = autocomplete.getPlace();
			document.getElementById('loc-filter-txt').value = place.name;
			document.getElementById('loc-filter-lat').value = place.geometry.location.lat();
			document.getElementById('loc-filter-lon').value = place.geometry.location.lng();

		});
	}

	if('text'==globalMap.filterType){
		google.maps.event.addDomListener(window, 'load', intialize);
	}

	function ajax_map_search(catId, busName, locId, geoEnabled) {
		active = jQuery.ajax({
			type: "POST",
			url: ajaxurl,
			data: "action=global_map_search&nonce=" + ajaxnonce + "&page=" + page + "&bus-name=" + busName + "&loc-id=" + locId + "&is-listing=" + globalMap.isListingPage + "&is-grid=" + globalMap.isGrid + "&cat-id=" + catId + ( ( geoEnabled || 'text' == globalMap.filterType ) ? "&rad=" +  radVal + "&lat=" + myLat + "&lon=" + myLon : '') + '&posts-per-page=' +(globalMap.isListingPage ? globalMap.postsPerPage : '-1'),
			success: function(result) {


				result = JSON.parse(result);

				if(0==page){
					resetGlobalData(result);
					initMap();
					jQuery('#map-mask').fadeOut('fast');
				}
				else
					updateGlobalData(result);


				if(0==parseInt(result.postsCount)){
					searching=false;
					jQuery('#map-loading').fadeOut('"fast"');
					return;
				}
				
				updateMap();

				if(globalMap.isListingPage)
					updateBusinessList();
				
				page+=parseInt(result.postsCount);
				ajax_map_search(catId, busName, locId, geoEnabled);
			}
		});
	}

	function resetGlobalData(result){
		var tempMark = globalMap.myLocationMarker;
		var tempLocMark = globalMap.locLocationMarker;
		var tempGeolocation = globalMap.geolocation;
		var defCoor = globalMap.defCoor;
		var defLogo = globalMap.defLogo;
		var radUnit = globalMap.radiusUnit;
		var grid = globalMap.isGrid;
		var translations = globalMap.translations;
		var tmpFilterType = globalMap.filterType;
		var tmpTemplateType = globalMap.templateType;
		var tmpTabs = globalMap.tabs;
		var tmpFavEn = globalMap.favEnabled;
		var tmpSkin = globalMap.mapSkin;
		var onLoadLocReq = globalMap.onLoadLocReq;
		var defRad = globalMap.defRad;

		globalMap = null;
		google.maps.event.trigger(map, 'resize');
		globalMap = result;


		globalMap.myLocationMarker = tempMark;
		globalMap.locLocationMarker = tempLocMark;
		globalMap.geolocation = tempGeolocation;
		globalMap.defCoor = defCoor;
		globalMap.defLogo = defLogo;
		globalMap.radiusUnit = radUnit;
		globalMap.isGrid = grid;
		globalMap.businessList = result.businessList;
		globalMap.isListingPage = result.isListingPage;
		globalMap.postsPerPage = result.postsPerPage;
		globalMap.businessIds = result.businessIds;
		globalMap.hasAfter = result.hasAfter;
		globalMap.hasBefore = result.hasBefore;
		globalMap.filterType = tmpFilterType;
		globalMap.templateType = tmpTemplateType;
		globalMap.translations = translations;
		globalMap.tabs = tmpTabs;
		globalMap.mapSkin = tmpSkin;
		globalMap.onLoadLocReq = onLoadLocReq;
		globalMap.defRad = defRad;
		globalMap.favEnabled = tmpFavEn;
	}

	function updateGlobalData(result){
		for(var i=0;i<result.postsCount;i++){
			globalMap.GPSLocations.push(result.GPSLocations[i]);

			globalMap.markersWithIcons.push(result.markersWithIcons[i]);
			globalMap.businessNames.push(result.businessNames[i]);

			globalMap.businessLogoes.push(result.businessLogoes[i]);
			globalMap.businessPermalinks.push(result.businessPermalinks[i]);
			globalMap.businessCategories.push(result.businessCategories[i]);
			globalMap.businessCategoriesColors.push(result.businessCategoriesColors[i]);
			
		}
		globalMap.postsCount = result.postsCount;
		
	}



	function ajax_business_list(ofst){
		if(ofst != 1 && ofst != -1)
			return;
		if((ofst == -1 && offset == 0) || (ofst == 1 && ! globalMap.hasAfter))
			return;
		if(ofst == 1)
			offset++;
		else
			offset--;
		jQuery.ajax({
			type: "POST",
			url: ajaxurl,
			data: "action=business_listing_paginate&nonce=" + ajaxnonce + "&business_ids=" + JSON.stringify(globalMap.businessIds) + "&is-grid=" + globalMap.isGrid + "&offset=" + offset + '&posts-per-page=' + globalMap.postsPerPage,
			success: function(result) {
				result = JSON.parse(result);
				if(null != result){
					globalMap.businessList = result.businessList;
					globalMap.hasBefore = result.hasBefore;
					globalMap.hasAfter = result.hasAfter;
					globalMap.ess_grid_shortcode = result.ess_grid_shortcode;
					updateBusinessList();
				}
			}
		});
	}

	function handleLocationError(browserHasGeolocation) {
		switch (browserHasGeolocation) {
			case 1:
				toastr.error(globalMap.translations.geoFail);
				break;
			case 2:
				break;
			case 3:
				toastr.warning(globalMap.geolocation.geoBrowserFail);
		}
	}

	jQuery(document).ready(function() {

		sidebarWidth = jQuery(window).width();

		jQuery('#slidable-map-sidebar').css({'right':-sidebarWidth*2});

		jQuery(".map-share-btn").live({
			click: function (e) {
				e.preventDefault();
				jQuery(this).parent().nextAll(".business-post-share-cont").first().toggle();
			}
		});


		jQuery('.search-wrapper .close-button').click(function(event){
			event.preventDefault();
			if(slided){
				jQuery('#slidable-map-sidebar').animate({right:-sidebarWidth}, {queue: false, duration: 500});
				slided = false;
			}
		});

		globalMap.templateType = parseInt(globalMap.templateType);

		switch ( globalMap.templateType ) {
			case 1:
				markerAnchorX = 20;
				markerAnchorY = 55;
				markerWidthX = 40;
				markerWidthY = 55;
			break;
			case 2:
				markerAnchorX = 0;
				markerAnchorY = 60;
				markerWidthX = 60;
				markerWidthY = 60;
			break;
		}

		var useDimmer = 1 == wyz_template_type;

		//pretty select box
		jQuery('#wyz-cat-filter').selectator({
			labels: {
				search: globalMap.translations.searchText
			},
			useDimmer: useDimmer
		});

		jQuery('#wyz-loc-filter').selectator({
			labels: {
				search: globalMap.translations.searchText
			},
			useDimmer: useDimmer
		});

		//add km or miles to the map radius slider
		if(globalMap.radiusUnit=='mile')
			jQuery('.location-search .input-range p span').addClass('distance-miles');
		else
			jQuery('.location-search .input-range p span').addClass('distance-km');

		var range = jQuery('#loc-radius');
		var radius = jQuery('#loc-radius').attr('value')
		jQuery('#loc-radius').siblings('p').find('span').html( radius );

		rangesliderJs.create(range,{
			onSlideEnd: function(pos, value) {

				if (locationFirstRun) {
					locationFirstRun = false;

					//geolocation activation
					if (globalMap.geolocation && navigator.geolocation) {
						jQuery('#map-mask').fadeIn('"slow"') ;
						navigator.geolocation.getCurrentPosition(function(position) {
							jQuery('#map-mask').fadeOut('"fast"') ;
							myTrueLat = position.coords.latitude;
							myTrueLon = position.coords.longitude;

						}, function() {
							jQuery('#map-mask').fadeOut('"fast"') ;
							handleLocationError(1);
						});
					} else {
						if (globalMap.geolocation)
							handleLocationError(3);
						else
							handleLocationError(2);
					}
				} 
			}
		});

		jQuery('.fav-bus').click(favoriteBus);

		if ( 2 == globalMap.templateType){
	        jQuery('.range_handle').append('<span></span>');
	    
			var radiusLength = jQuery('.range_handle span');
			range.on('input', function() {
				radiusLength.html( jQuery(this).val() + ' ' + globalMap.radiusUnit );
				radVal = jQuery(this).val();
			});

			var locRadius = jQuery('input[type="range"]').attr('value');
			var radiusLength = jQuery('.range_handle span');
			radiusLength.html( locRadius + ' ' + globalMap.radiusUnit );
		} else{
			range.on('input', function() {
				jQuery(this).siblings('p').find('span').html( jQuery(this).val() );
				radVal = jQuery(this).val();
			});
		}


		jQuery('#map-names').keypress(function(e) {
			if(e.which == 13) {
				jQuery('#map-search-submit').trigger('click');
			}
		});

		jQuery('#map-search-submit').on('click', mapSearch);

		google.maps.event.addDomListener(window, 'load', function(){
			initMap();
			mapSearch();
			if( globalMap.onLoadLocReq &&globalMap.geolocation && navigator.geolocation && 0<globalMap.defRad) {
				jQuery('#loc-radius').trigger('input');
				navigator.geolocation.getCurrentPosition(function(position) {
					myTrueLat = position.coords.latitude;
					myTrueLon = position.coords.longitude;
					mapSearch();
				}, function() {
					handleLocationError(1);
				});
			}
			
		});


		jQuery(".list-paginate").live('click',function(){
			jQuery(".list-paginate").prop('disabled', true).css('background-color','#68819b'); 
			ajax_business_list(parseInt(jQuery(this).data('offset')));
		});

	});

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
				var i;
				for(i=0;i<globalMap.length;i++){
					if(globalMap.businessIds == bus_id)
						break;
				}
				if(favType=='fav'){
					if(i<globalMap.length)
						globalMap.favorites[i]=true;
					target.find('i').removeClass('fa-heart-o');
					target.find('i').addClass('fa-heart');
					target.data('fav',1 );
				} else {
					if(i<globalMap.length)
						globalMap.favorites[i]=false;
					target.find('i').removeClass('fa-heart');
					target.find('i').addClass('fa-heart-o');
					target.data('fav',0 );
				}
			}
		});
	}
}