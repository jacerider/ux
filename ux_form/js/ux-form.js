!function(t,e){"use strict";e.behaviors.uxForm={attach:function(t){this.setLastElement(t)},setLastElement:function(t){}},t(document).on("state:visible",function(e){e.trigger&&t(e.target).closest(".ux-form-element-js").toggle(e.value)})}(jQuery,Drupal);