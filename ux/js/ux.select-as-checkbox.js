!function(e,c){"use strict";c.behaviors.uxSelectAsCheckbox={attach:function(t,s){if(s.ux&&s.ux.theme&&s.ux.theme.select_as_checkbox)for(var a in s.ux.theme.select_as_checkbox)s.ux.theme.select_as_checkbox[a]&&e("#"+a+"-checkbox",t).once("ux-select-as-checkbox").data("ux-select-as-checkbox",a).change(function(){var t=e(this).data("ux-select-as-checkbox"),s=e("#"+t+"-select");e(this).is(":checked")?s.val(1):s.val("All"),"undefined"!=typeof c.UxForm&&c.UxForm.updateFields()})}}}(jQuery,Drupal);