"use strict";!function(t,n){t(window).on({"dialog:beforecreate":function(o,e,a,i){a.is("#ux-dialog")&&(a=t("#ux-content"),t("body").addClass("has-ux-dialog"),i.dialogClass="ux-dialog",i.show=!1,i.hide={effect:"none",delay:600},i.position={my:"center",at:"center",of:a},i.beforeClose=function(){n.Ux.focusContent(),n.Ux.hideShadow(),t("body").removeClass("ux-dialog-animate"),setTimeout(function(){t("body").removeClass("ux-dialog-active")},400)})},"dialog:aftercreate":function(o,e,a,i){a.is("#ux-dialog")&&(setTimeout(function(){t("body").addClass("ux-dialog-active")},0),setTimeout(function(){n.Ux.blurContent(),n.Ux.showShadow(function(o){a.dialog("close")}),t("body").addClass("ux-dialog-animate")},100))},"dialog:beforeclose":function(o,e,a){a.is("#ux-dialog")&&t("body").removeClass("has-ux-dialog")}}),n.AjaxCommands.prototype.closeUxDialog=function(o,e,a){e=t(e.selector);e.length&&e.dialog("close"),e.off("dialogButtonsChange")}}(jQuery,Drupal,(Drupal.debounce,Drupal.displace));