<html>
<body>




<script src="https://www.booking-manager.com/down/3155_wbm.js"></script>


<script type="text/javascript">
!function(){"use strict";function a(a,b,c){"addEventListener"in window?a.addEventListener(b,c,!1):"attachEvent"in window&&a.attachEvent("on"+b,c)}function b(){var a,b=["moz","webkit","o","ms"];for(a=0;a<b.length&&!window.requestAnimationFrame;a+=1)window.requestAnimationFrame=window[b[a]+"RequestAnimationFrame"];window.requestAnimationFrame||(c(" RequestAnimationFrame not supported"),window.requestAnimationFrame=function(a){a()})}function c(a){k.log&&"console"in window&&console.log(i+"[Host page]"+a)}function d(a){function b(){function a(a){window.requestAnimationFrame(function(){n.iframe.style[a]=n[a]+"px",c(" IFrame ("+n.iframe.id+") "+a+" set to "+n[a]+"px")})}k.sizeHeight&&a("height"),k.sizeWidth&&a("width")}function d(a){c(" iFrame "+a.id+" removed."),a.parentNode.removeChild(a)}function e(){var a=m.substr(j).split(":");return{iframe:document.getElementById(a[0]),height:a[1],width:a[2],type:a[3]}}function f(){"close"===n.type?d(n.iframe):b(),k.resizedCallback(n)}function g(){var b=a.origin,d=n.iframe.src.split("/").slice(0,3).join("/");if(k.checkOrigin&&(c(" Checking conection is from: "+d),""+b!="null"&&b!==d))throw new Error("Unexpected message received from: "+b+" for "+n.iframe.id+". Message was: "+a.data);return!0}function h(){return i===(""+m).substr(0,j)}function l(){var a=m.substr(m.lastIndexOf(":")+1);c(' Received message "'+a+'" from '+n.iframe.id),k.messageCallback({iframe:n.iframe,message:a})}var m=a.data,n={};h()&&(n=e(),g()&&("message"!==n.type?f():l()))}function e(){function b(a){return""===a&&(l.id=a="iFrameResizer"+h++,c(" Added missing iframe ID: "+a)),a}function d(){c(" IFrame scrolling "+(k.scrolling?"enabled":"disabled")+" for "+m),l.style.overflow=!1===k.scrolling?"hidden":"auto",l.scrolling=!1===k.scrolling?"no":"yes"}function e(){("number"==typeof k.bodyMargin||"0"===k.bodyMargin)&&(k.bodyMarginV1=k.bodyMargin,k.bodyMargin=""+k.bodyMargin+"px")}function f(){return m+":"+k.bodyMarginV1+":"+k.sizeWidth+":"+k.log+":"+k.interval+":"+k.enablePublicMethods+":"+k.autoResize+":"+k.bodyMargin+":"+k.heightCalculationMethod+":"+k.bodyBackground+":"+k.bodyPadding}function g(a,b){c("["+a+"] Sending init msg to iframe ("+b+")"),l.contentWindow.postMessage(i+b,"*")}function j(b){a(l,"load",function(){g("iFrame.onload",b)}),g("init",b)}var l=this,m=b(l.id);d(),e(),j(f())}function f(){function a(a){if("IFRAME"!==a.tagName)throw new TypeError("Expected <IFRAME> tag, found <"+a.tagName+">.");e.call(a)}function b(a){if(a=a||{},"object"!=typeof a)throw new TypeError("Options is not an object.");for(var b in l)l.hasOwnProperty(b)&&(k[b]=a.hasOwnProperty(b)?a[b]:l[b])}window.iFrameResize=function(c,d){b(c),Array.prototype.forEach.call(document.querySelectorAll(d||"iframe"),a)}}function g(a){a.fn.iFrameResize=function(b){return k=a.extend({},l,b),this.filter("iframe").each(e).end()}}var h=0,i="[iFrameSizer]",j=i.length,k={},l={autoResize:!0,bodyBackground:null,bodyMargin:null,bodyMarginV1:8,bodyPadding:null,checkOrigin:!0,enablePublicMethods:!1,heightCalculationMethod:"offset",interval:32,log:!1,messageCallback:function(){},resizedCallback:function(){},scrolling:!1,sizeHeight:!0,sizeWidth:!1};b(),a(window,"message",d),f(),"jQuery"in window&&g(jQuery)}();
//# sourceMappingURL=../src/iframeResizer.map
</script>

<script type="text/javascript">

 iFrameResize({
  log                   : true,                  // Enable console logging
  enablePublicMethods   : true,                  // Enable methods within iframe hosted page
  sizeHeight  			: true,
  resizedCallback       : function(messageData){ // Callback fn when message is received
      $('p#callback').html(
    ' <b>Frame ID:</b> '	+ messageData.iframe.id +
    ' <b>Height:</b> '		+ messageData.height +
    ' <b>Width:</b> '		+ messageData.width + 
    ' <b>Event type:</b> '	+ messageData.type
      );
  }
 });

</script>

</body>
</html>