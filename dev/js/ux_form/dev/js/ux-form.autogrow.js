"use strict";!function(n){Drupal.behaviors.uxFormAutogrow={attach:function(t,i){n.each(n("textarea[data-autogrow]",t).once(),function(){function t(t){var i=n(t),e=i.data("autogrow-max"),o=t.scrollHeight+a;e&&e<o?i.css({overflow:"auto",resize:"vertical"}):i.css({minHeight:"auto",overflow:"hidden",resize:"none"}).css("minHeight",t.scrollHeight+a)}var i=n(this),a=this.offsetHeight-this.clientHeight;t(this),i.on("keyup input",function(){t(this)})})}}}(jQuery);