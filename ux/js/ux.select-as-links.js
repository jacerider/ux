!function(e,i){"use strict";i.behaviors.uxSelectAsLinks={attach:function(i){e(".ux-select-as-links",i).once("ux-select-as-links").each(function(){var i=e(this).find("select").hide(),t=e(this).find("a"),s=i.closest("form").find(".form-submit:visible").first();t.on("click",function(a){a.preventDefault();var c=e(this).data("ux-value");i.val(c),i.trigger("change"),t.removeClass("active"),e(this).addClass("active"),s.length&&s.trigger("click")})})}}}(jQuery,Drupal);