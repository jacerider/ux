!function(a,n,u){"use strict";n.behaviors.uxOffcanvasMenu={attach:function(n,u){if(u.ux&&u.ux.offcanvasMenu&&u.ux.offcanvasMenu.items)for(var f in u.ux.offcanvasMenu.items)if(u.ux.offcanvasMenu.items[f]){var e=a("#ux-offcanvas-"+f);e.length&&e.ux_offcanvas_menu(u.ux.offcanvasMenu.items[f])}}}}(jQuery,Drupal,drupalSettings,document);