"use strict";!function(u){Drupal.behaviors.uxButton={attach:function(t){u(".ux-button-trigger",t).once("ux-button").on("click",function(t){t.preventDefault(),u(this).closest(".ux-button").find('input[type="submit"]').trigger("mousedown").trigger("mouseup").trigger("click")})}}}(jQuery);