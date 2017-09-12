(function ($, window, document) {

  'use strict';

  $.uxMenuTree = function (element, options) {

    var plugin = this;
    var $element = $(element);
    var defaults = {
      multiexpand: true,
      animation: false
    };

    var expand = function (el) {
      el.addClass('ux-menu-tree-expanded');
    };
    var collapse = function (el) {
      el.removeClass('ux-menu-tree-expanded');
    };
    var click = function (e) {
      if (e.target.nodeName !== 'A') {
        if ($(this).hasClass('ux-menu-tree-expanded')) {
          collapse($(this));
        }
        else {
          if (!plugin.settings.multiexpand) {
            $element.find('.ux-menu-tree-expanded').not($(this).parents()).trigger('click');
          }
          expand($(this));
        }
      }
    };

    plugin.settings = {};

    plugin.init = function () {
      plugin.settings = $.extend({}, defaults, options);

      if (plugin.settings.animation) {
        expand = function (el) {
          el.children('ul, ol').slideDown(function () {
            el.addClass('ux-menu-tree-expanded');
          });
        };

        collapse = function (el) {
          el.children('ul, ol').slideUp(function () {
            el.removeClass('ux-menu-tree-expanded');
          });
        };
      }

      $element.addClass('ux-menu-tree-list');
      $element.children('li').addClass('ux-menu-tree-top-level');
      $element.find('li').each(function () {
        var $this = $(this);
        var childrenTrees = $this.children('ul, ol');

        if (childrenTrees.index() !== -1) {
          $this.addClass('ux-menu-tree-item');
          $this.bind('click.ux-menu-tree', click);
          childrenTrees.bind('click.ux-menu-tree', function (e) {
            if (e.target.nodeName !== 'A') {
              return false;
            }
          });
          if ($this.hasClass('active-trail')) {
            expand($this);
          }
        }
      });
    };

    plugin.expand = function () {
      expand($element.find('.ux-menu-tree-item'));
    };

    plugin.collapse = function () {
      collapse($element.find('.ux-menu-tree-expanded'));
    };

    plugin.destroy = function () {
      $element.removeClass('ux-menu-tree-list');
      $element.children('li').removeClass('ux-menu-tree-top-level');
      $element.find('li').each(function () {
        var $this = $(this);
        var childrenTrees = $this.children('ul, ol');

        if (childrenTrees.index() !== -1) {
          $this.removeClass('ux-menu-tree-item');
          $this.unbind('.ux-menu-tree');
          childrenTrees.removeAttr('style');
          childrenTrees.unbind('.ux-menu-tree');
        }
      });
      $element.find('.ux-menu-tree-expanded').removeClass('ux-menu-tree-expanded');
      $element.data('ux-menu-tree', null);
    };

    plugin.init();
  };

  $.fn.uxMenuTree = function (options) {
    return this.each(function () {
      var uxMenuTree = $(this).data('uxMenuTree');
      if (typeof uxMenuTree === 'undefined' || uxMenuTree === null) {
        var plugin = new $.uxMenuTree(this, options);
        $(this).data('uxMenuTree', plugin);
      }
    });
  };

})(jQuery, window, document);
