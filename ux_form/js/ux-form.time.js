"use strict";!function(m,t){t.behaviors.uxFormTime={configDefaults:{mode:"button",container:"#ux-content",format:"HH:i:00",formatSubmit:"HH:i:00",formatLabel:"h:i A"},attach:function(t,i){if(i.ux&&i.ux.time&&i.ux.time.items)for(var e in i.ux.time.items)if(i.ux.time.items[e])for(var n=m("#"+e,t).once("ux-form-time"),o=0;o<n.length;o++)this.init(n[o],i.ux.time.items[e])},init:function(t,i){var t=m(t),e=t.find(".form-time"),i=m.extend(!0,{},this.configDefaults,i),n=i.mode;e.data("value",e.val()),e.pickatime(i),"button"===n&&(i=t.find(".ux-form-time-button"),e.attr("type","time"),i.on("click",function(t){t.preventDefault(),t.stopPropagation(),e.pickatime("picker").open()}))}}}(jQuery,Drupal,drupalSettings);