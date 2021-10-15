!function(t,i,e){"use strict";var s;return s={animate:"TweenLite",position:"left",size:320,speed:.5},t.ux_offcanvas_item=function(e,n,a,o){this.debug=!1,this._defaults=s,this.settings=t.extend(!0,{},this._defaults,o),this.id=this.settings.id,this.offcanvas=t(e),this.trigger=t(a),this.push=t("#ux-document"),this.item=t(n),this.closeLink=t(".ux-offcanvas-close",this.item),this.active=!1,this.param={},this.size=this.settings.size,this.initialize=function(t){return function(){t.set_animator(),t.bind_trigger(),t.bind_close_link(),t.listen_open(),t.listen_close(),t.log("UX Offcanvas has been initialized.")}}(this),this.set_orientation=function(t){return function(){switch(t.param.pushFrom=0,t.param.itemTo=0,this.settings.position){case"left":t.size=t.offcanvas.width()<this.settings.size?t.offcanvas.width()-20:this.settings.size,t.param.axis="x",t.param.size="width",t.param.pushTo=1*t.settings.size,t.param.itemFrom=t.settings.size*-1;break;case"right":t.size=t.offcanvas.width()<this.settings.size?t.offcanvas.width()-20:this.settings.size,t.param.axis="x",t.param.size="width",t.param.pushTo=t.size*-1,t.param.itemFrom=1*t.size;break;case"top":t.size=t.offcanvas.height()<this.settings.size?t.offcanvas.height()-20:this.settings.size,t.param.axis="y",t.param.size="height",t.param.pushTo=1*t.size,t.param.itemFrom=t.size*-1;break;case"bottom":t.size=t.offcanvas.height()<this.settings.size?t.offcanvas.height()-20:this.settings.size,t.param.axis="y",t.param.size="height",t.param.pushTo=t.size*-1,t.param.itemFrom=1*t.size}}}(this),this.set_size=function(t){return function(){t.item[t.param.size](this.size)}}(this),this.open=function(t){return function(i){var e;t.set_orientation(),t.set_size(),t.active=!0,t.trigger.addClass("active"),t.item.addClass("active"),t.log("UX Offcanvas open."),e=t.animation("push"),t.animate.fromTo(t.push,t.settings.speed,e.from,e.to),e=t.animation("item"),t.animate.fromTo(t.item,t.settings.speed,e.from,e.to).eventCallback("onComplete",function(){i&&i.call()})}}(this),this.close=function(t){return function(i){var e;t.active=!1,t.log("UX Offcanvas close."),e=t.animation("push"),e.from.clearProps="transform",t.animate.fromTo(t.push,t.settings.speed,e.to,e.from),e=t.animation("item"),e.from.clearProps="transform",t.animate.fromTo(t.item,t.settings.speed,e.to,e.from).eventCallback("onComplete",function(){t.trigger.removeClass("active"),t.item.removeClass("active"),i&&i.call()})}}(this),this.animation=function(t){return function(i){var e={from:{},to:{}};return e.from[t.param.axis]=t.param[i+"From"],e.to[t.param.axis]=t.param[i+"To"],e}}(this),this.listen_open=function(t){return function(){t.item.on("ux_offcanvas_item.open",function(i,e){t.open(e)})}}(this),this.listen_close=function(t){return function(){t.item.on("ux_offcanvas_item.close",function(i,e){t.close(e)})}}(this),this.bind_trigger=function(t){return function(){t.trigger.on("click",function(i){i.preventDefault(),t.offcanvas.triggerHandler("ux_offcanvas.open",[t.id])})}}(this),this.bind_close_link=function(t){return function(){t.closeLink.on("click",function(i){i.preventDefault(),t.offcanvas.triggerHandler("ux_offcanvas.close",[t.id])})}}(this),this.set_animator=function(t){return function(){t.animate=i[t.settings.animate],t.log("Animating using the "+t.settings.animate+" platform.")}}(this),this.log=function(t){return function(i){t.debug&&("object"==typeof i?console.log("[UX Offcanvas "+t.id+"]",i):console.log("[UX Offcanvas "+t.id+"] "+i))}}(this),this.error=function(t){return function(i){"object"==typeof i?console.error("[UX Parallax "+t.id+"]",i):console.error("[UX Parallax "+t.id+"] "+i)}}(this),this.initialize()},t.fn.ux_offcanvas_item=function(i,s){return this.each(function(n,a){if(s.id&&!t.data(a,"ux_offcanvas_item")){var o=e.getElementById("ux-offcanvas-trigger-"+s.id);if(o)return t.data(o,"ux_offcanvas_item",new t.ux_offcanvas_item(i,a,o,s))}})},t.fn.ux_offcanvas_item}(window.jQuery,window,document);