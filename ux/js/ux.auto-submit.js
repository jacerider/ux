"use strict";!function(a){Drupal.behaviors.uxAutoSubmit={attach:function(t){function i(t){a(this).find("[data-ux-auto-submit-click]").first().click()}a("form[data-ux-auto-submit-full-form]",t).add("[data-ux-auto-submit]",t).filter("form, select, input:not(:text, :submit)").once("ux-auto-submit").change(function(t){a(t.target).is(":not(:text, :submit, [data-ux-auto-submit-exclude])")&&i.call(t.target.form)});var o=[16,17,18,20,33,34,35,36,37,38,39,40,9,13,27];a("[data-ux-auto-submit-full-form] input:text, input:text[data-ux-auto-submit]",t).filter(":not([data-ux-auto-submit-exclude])").once("ux-auto-submit",function(){var u=0;a(this).bind("keydown keyup",function(t){-1===a.inArray(t.keyCode,o)&&u&&clearTimeout(u)}).keyup(function(t){-1===a.inArray(t.keyCode,o)&&(u=setTimeout(a.proxy(i,this.form),500))}).bind("change",function(t){-1===a.inArray(t.keyCode,o)&&(u=setTimeout(a.proxy(i,this.form),500))})})}}}(jQuery);