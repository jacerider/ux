"use strict";!function(n,o,t,a){o.behaviors.uxAside={_defaults:{overlay:"#ux-asides",appendTo:!1},openCount:0,attach:function(t,e){var o,a,s=this;if(e.ux&&e.ux.aside&&e.ux.aside.items)for(var i in n(document).once("ux-aside").on("drupalViewportOffsetChange.ux-aside",s.resize),s.resize(),e.ux.aside.items)e.ux.aside.items[i]&&((a=n("#ux-aside-"+i).once("ux-aside")).length&&(o=s.getOptions(e.ux.aside,i),a.uxAside(o)),(a=n('[data-ux-aside="'+i+'"]').once("ux-aside")).length)&&a.on("click",function(t){t.preventDefault();t=n("#ux-aside-"+n(this).data("ux-aside"));t.length&&t.uxAside("open")})},detach:function(t,e,o){"unload"===o&&n(document).removeOnce("ux-aside").off("drupalViewportOffsetChange.ux-aside")},resize:function(t,e){n("#ux-asides").css({marginTop:a.offsets.top,marginLeft:a.offsets.left,marginRight:a.offsets.right,marginBottom:a.offsets.bottom})},getOptions:function(t,e){var o=this,t=n.extend({},o._defaults,t.options,t.items[e]);return t.onOpening=o.onOpening,t.onClosed=o.onClosed,t.attachTop&&null!==t.attachTop&&!1!==t.attachTop&&(t.top=0),t.attachBottom&&null!==t.attachBottom&&!1!==t.attachBottom&&(t.bottom=0),t.attachLeft&&null!==t.attachLeft&&!1!==t.attachLeft&&(t.left=0,t.openTall=!0),t.attachRight&&null!==t.attachRight&&!1!==t.attachRight&&(t.right=0,t.openTall=!0),t.offsets=a.offsets,t},onOpening:function(e){e.$element.once("ux-aside-moved").each(function(){var t=n("#ux-asides");e.$element.appendTo(t)}),o.behaviors.uxAside.openCount++,n("#ux-asides").addClass("active"),e.options.top===a.offsets.top&&e.$element.css({borderTopRightRadius:0,borderTopLeftRadius:0}),e.options.bottom===a.offsets.bottom&&e.$element.css({borderBottomRightRadius:0,borderBottomLeftRadius:0}),e.options.restoreDefaultContent&&setTimeout(function(){o.attachBehaviors(e.$element.get(0),t)},10),e.initialized=!0},onClosed:function(t){o.behaviors.uxAside.openCount--,0===o.behaviors.uxAside.openCount&&n("#ux-asides").removeClass("active")}}}(jQuery,Drupal,drupalSettings,Drupal.displace);