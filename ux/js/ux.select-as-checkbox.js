"use strict";!function(s,a){a.behaviors.uxSelectAsCheckbox={attach:function(e,c){if(c.ux&&c.ux.theme&&c.ux.theme.select_as_checkbox)for(var t in c.ux.theme.select_as_checkbox)c.ux.theme.select_as_checkbox[t]&&s("#"+t+"-checkbox",e).once("ux-select-as-checkbox").data("ux-select-as-checkbox",t).change(function(){var e=s(this).data("ux-select-as-checkbox"),e=s("#"+e+"-select");s(this).is(":checked")?e.val(1):e.val("All"),void 0!==a.UxForm&&a.UxForm.updateFields()})}}}(jQuery,Drupal);