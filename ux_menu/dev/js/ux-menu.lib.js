
(function ($, window, document) {

  'use strict';

  var pluginName = 'uxMenu';

  function Plugin(element, options) {
    this.element = element;
    this._name = pluginName;
    this._defaults = $.fn.uxMenu.defaults;
    this.options = $.extend({}, this._defaults, options);
    this.animationEvent = whichAnimationEvent();
    this.init();
  }

  function whichAnimationEvent() {
    var t;
    var el = document.createElement('fakeelement');
    var animations = {
      animation: 'animationend',
      OAnimation: 'oAnimationEnd',
      MozAnimation: 'animationend',
      WebkitAnimation: 'webkitAnimationEnd'
    };
    for (t in animations) {
      if (el.style[t] !== 'undefined') {
        return animations[t];
      }
    }
  }

  // Avoid Plugin.prototype conflicts
  $.extend(Plugin.prototype, {

    /*
    Initialize plugin instance.
     */
    init: function () {
      this.buildCache();
      this.buildElement();
      this.bindEvents();
    },

    /*
    Remove plugin instance complete.
     */
    destroy: function () {
      this.unbindEvents();
      this.$element.removeData();
    },

    /*
    Cache DOM nodes for performance.
     */
    buildCache: function () {
      this.$element = $(this.element);
      this.$menu = this.$element.find('.' + pluginName + '-menu');
      this.$wrap = this.$menu.find('.' + pluginName + '-wrap');
      this.$menus = this.$menu.find('.' + pluginName + '-level');
      this.menusArr = [];
      this.breadCrumbs = false;

      if (this.options.breadcrumbNav || this.options.backNav) {
        this.$breadcrumbWrapper = this.$element.find('.' + pluginName + '-top');
      }

      if (this.options.backNav) {
        this.$backWrapper = this.$element.find('.' + pluginName + '-top');
      }

      /* Determine what current menu actually is */
      var current_menu = 0;
      this.$menus.each(function (pos) {
        if ($(this).find('.' + pluginName + '-link.is-active').length) {
          current_menu = pos;
        }
      });

      this.current_menu = current_menu;
    },

    /*
    Process fields.
     */
    buildElement: function () {
      var _this = this;
      var submenus = [];
      var $wrapper = null;

      /* Loops over root level menu items */
      this.$menus.each(function (pos) {
        var $menuEl = $(this);

        var menu = {
          $menuEl: $menuEl,
          $menuItems: $menuEl.find('.' + pluginName + '-item')
        };
        _this.menusArr.push(menu);

        // set current menu class
        if (pos === _this.current_menu) {
          $menuEl.addClass(pluginName + '-level--current');
          // set height of nav based on children
          _this.setWrapHeight($menuEl);
        }

        $menuEl.find('.' + pluginName + '-link[data-submenu]').each(function () {
          var $linkEl = $(this);
          if (_this.options.itemIcon) {
            $linkEl.append(_this.options.itemIcon);
          }
          var submenu = $linkEl.attr('data-submenu');
          var pushMe = {
            menu: submenu,
            name: $linkEl.html()
          };
          if (submenus[pos]) {
            submenus[pos].push(pushMe);
          }
          else {
            submenus[pos] = [];
            submenus[pos].push(pushMe);
          }
        });
      });

      /* For each MENU, find their parent MENU */
      this.$menus.each(function (pos) {
        var $menuEl = $(this);
        var menu_x = $menuEl.attr('data-menu');
        submenus.forEach(function (subMenuEl, menu_root) {
          subMenuEl.forEach(function (subMenuItem, subPos) {
            if (subMenuItem.menu === menu_x) {
              _this.menusArr[pos].backIdx = menu_root;
              _this.menusArr[pos].name = subMenuItem.name;
            }
          });
        });
      });

      // create back button
      if (_this.options.backNav) {
        $wrapper = _this.fetchSelector(_this.options.backSelector);
        if ($wrapper) {
          $wrapper.find('.' + pluginName + '-back').remove();
          _this.$backNav = $('<a class="' + pluginName + '-back" aria-label="' + _this.options.backText + '" href="#"></a>').prependTo($wrapper);
          var html = _this.options.backText;
          if (_this.options.backIcon) {
            html = _this.options.backIcon + ' ' + html;
          }
          _this.$backNav.html(html);

          if (_this.current_menu !== 0) {
            _this.$backNav.addClass('animate-fadeIn');
          }
        }
      }

      // create breadcrumbs
      if (_this.options.breadcrumbNav) {
        $wrapper = _this.fetchSelector(_this.options.breadcrumbSelector);
        if ($wrapper) {
          $wrapper.find('.' + pluginName + '-breadcrumbs').remove();
          _this.$breadcrumbNav = $('<nav class="' + pluginName + '-breadcrumbs" aria-label="You are here"></nav>').prependTo($wrapper);
          _this.addBreadcrumb(0);

          // Need to add breadcrumbs for all parents of current submenu
          if (_this.menusArr[_this.current_menu].backIdx !== 0 && _this.current_menu !== 0) {
            _this.crawlCrumbs(_this.menusArr[_this.current_menu].backIdx, _this.menusArr);
            _this.breadCrumbs = true;
          }

          // Create current submenu breadcrumb
          if (_this.current_menu !== 0) {
            _this.addBreadcrumb(_this.current_menu);
            _this.breadCrumbs = true;
          }
        }
      }

    },

    /*
    Given a selector, locate the closest.
     */
    fetchSelector: function (selector) {
      var $wrapper = this.$element.find(selector);
      if (!$wrapper.length) {
        $wrapper = $(selector).first();
      }
      return $wrapper.length ? $wrapper : null;
    },

    /*
    Bind events that trigger methods.
    */
    bindEvents: function () {
      var _this = this;
      for (var i = 0, len = this.menusArr.length; i < len; ++i) {
        this.menusArr[i].$menuItems.each(function (pos) {
          $(this).find('.' + pluginName + '-link[data-submenu]').on('click', function (e) {
            var $linkEl = $(this);
            var itemName = $linkEl.html();
            var submenu = $linkEl.attr('data-submenu');
            var $subMenuEl = _this.$menu.find('.' + pluginName + '-level[data-menu="' + submenu + '"]');
            if ($subMenuEl.length) {
              e.preventDefault();
              _this.openSubMenu($subMenuEl, pos, itemName);
            }
            else {
              _this.$menu.find('.' + pluginName + '-link--current').removeClass(pluginName + '-link--current');
              $linkEl.addClass(pluginName + '-link--current');
            }
          });
        });
      }

      // back navigation
      if (this.options.backNav) {
        this.$backNav.on('click', function (e) {
          e.preventDefault();
          _this.back();
        });
      }
    },

    /*
    Add a breadcrumb
     */
    addBreadcrumb: function (idx) {
      if (!this.options.breadcrumbNav) {
        return false;
      }

      var _this = this;
      var title = idx ? this.menusArr[idx].name.replace(/<([^>]+?)([^>]*?)>(.*?)<\/\1>/ig, '') : this.options.breadcrumbText;
      var $bc = $('<span class="uxMenu-breadcrumb">');
      if (!idx && _this.options.breadcrumbIcon) {
        title = _this.options.breadcrumbIcon + title;
      }
      if (idx && _this.options.breadcrumbSeparatorIcon) {
        $bc.html(_this.options.breadcrumbSeparatorIcon);
      }
      var $link = $('<a href="#"></a>').html(title).appendTo($bc);

      $link.on('click', function (e) {
        e.preventDefault();
        // do nothing if this breadcrumb is the last one in the list of breadcrumbs
        if (!$bc.next().length || _this.isAnimating) {
          return false;
        }

        _this.isAnimating = true;
        // current menu slides out
        _this.menuOut();
        // next menu slides in
        var $nextMenu = _this.menusArr[idx].$menuEl;
        _this.menuIn($nextMenu);

        // remove breadcrumbs that are ahead
        // $bc.nextAll().remove();
        var $remaining = $bc.nextAll();
        $remaining.one(_this.animationEvent, function (e) {
          $remaining.remove();
        }).addClass('animate-fadeOut');
      });

      this.$breadcrumbNav.append($bc);
      $bc.addClass('animate-fadeIn');
    },

    crawlCrumbs: function (currentMenu, menuArray) {
      this.log('Crawl Crumbs');
      if (menuArray[currentMenu].backIdx !== 0) {
        this.crawlCrumbs(menuArray[currentMenu].backIdx, menuArray);
      }
      // create breadcrumb
      this.addBreadcrumb(currentMenu);
    },

    openSubMenu: function ($subMenuEl, clickPosition, subMenuName) {
      if (this.isAnimating) {
        return false;
      }
      var menuIdx = this.$menus.index($subMenuEl);
      this.isAnimating = true;

      // save "parent" menu index for back navigation
      this.menusArr[menuIdx].backIdx = this.current_menu;
      // save "parent" menu´s name
      this.menusArr[menuIdx].name = subMenuName;
      // current menu slides out
      this.menuOut(clickPosition);
      // next menu (submenu) slides in
      this.menuIn($subMenuEl, clickPosition);
    },

    back: function () {
      if (this.isAnimating) {
        return false;
      }
      this.isAnimating = true;

      // current menu slides out
      this.menuOut();
      // next menu (previous menu) slides in
      var $backMenu = this.menusArr[this.menusArr[this.current_menu].backIdx].$menuEl;
      this.menuIn($backMenu);

      // remove last breadcrumb
      if (this.options.breadcrumbNav) {
        this.$breadcrumbNav.children().last().remove();
      }
    },

    menuOut: function (clickPosition) {
      var _this = this;
      var $currentMenu = this.menusArr[this.current_menu].$menuEl;
      var isBackNavigation = typeof clickPosition == 'undefined' ? true : false;

      // slide out current menu items - first, set the delays for the items
      this.menusArr[this.current_menu].$menuItems.each(function (pos) {
        this.style.WebkitAnimationDelay = this.style.animationDelay = isBackNavigation ? parseInt(pos * _this.options.itemDelayInterval) + 'ms' : parseInt(Math.abs(clickPosition - pos) * _this.options.itemDelayInterval) + 'ms';
      });
      // animation class
      if (this.options.direction === 'r2l') {
        $currentMenu.addClass(!isBackNavigation ? 'animate-outToLeft' : 'animate-outToRight');
      }
      else {
        $currentMenu.addClass(isBackNavigation ? 'animate-outToLeft' : 'animate-outToRight');
      }
    },

    /*
    Set wrap height based on element content.
     */
    setWrapHeight: function ($menuEl) {
      var _this = this;
      $menuEl = $menuEl || this.$menus.filter('.' + pluginName + '-level--current');
      var currentHeight = this.$wrap.height();
      var height = 0;
      $menuEl.children().each(function () {
        height += $(this).outerHeight();
      });

      // _this.$element.offsetParent().animate({scrollTop: 0}, 1000);
      // this.$wrap.height(height);
      _this.$element.offsetParent().animate({scrollTop: 0}, 1000);
      if (currentHeight <= height) {
        this.$wrap.height(height);
      }
      else {
        $menuEl.one(this.animationEvent, function (e) {
          _this.$wrap.height(height);
        });
      }
    },

    menuIn: function ($nextMenuEl, clickPosition) {
      var _this = this;
      var $currentMenu = this.menusArr[this.current_menu].$menuEl;
      var isBackNavigation = typeof clickPosition == 'undefined' ? true : false;
      var nextMenuIdx = this.$menus.index($nextMenuEl);
      var nextMenu = this.menusArr[nextMenuIdx];
      // var $nextMenuEl = nextMenu.$menuEl;
      var $nextMenuItems = nextMenu.$menuItems;
      var nextMenuItemsTotal = $nextMenuItems.length;

      // set height of nav based on children
      _this.setWrapHeight($nextMenuEl);

      // control back button and breadcrumbs navigation elements
      if (!isBackNavigation) {
        // show back button
        if (_this.options.backNav) {
          _this.$backNav.removeClass('animate-fadeOut').addClass('animate-fadeIn');
        }
        // add breadcrumb
        _this.addBreadcrumb(nextMenuIdx);
      }
      else if (nextMenuIdx === 0 && _this.options.backNav) {
        // hide back button
        _this.$backNav.removeClass('animate-fadeIn').addClass('animate-fadeOut');
      }

      $nextMenuItems.each(function (pos) {
        this.style.WebkitAnimationDelay = this.style.animationDelay = isBackNavigation ? parseInt(pos * _this.options.itemDelayInterval) + 'ms' : parseInt(Math.abs(clickPosition - pos) * _this.options.itemDelayInterval) + 'ms';
        var farthestIdx = clickPosition <= nextMenuItemsTotal / 2 || isBackNavigation ? nextMenuItemsTotal - 1 : 0;

        if (pos === farthestIdx) {
          $(this).one(_this.animationEvent, function (e) {
            // reset classes
            if (_this.options.direction === 'r2l') {
              $currentMenu.removeClass(!isBackNavigation ? 'animate-outToLeft' : 'animate-outToRight');
              $nextMenuEl.removeClass(!isBackNavigation ? 'animate-inFromRight' : 'animate-inFromLeft');
            }
            else {
              $currentMenu.removeClass(isBackNavigation ? 'animate-outToLeft' : 'animate-outToRight');
              $nextMenuEl.removeClass(isBackNavigation ? 'animate-inFromRight' : 'animate-inFromLeft');
            }
            $currentMenu.removeClass(pluginName + '-level--current');
            $nextMenuEl.addClass(pluginName + '-level--current');

            _this.current_menu = nextMenuIdx;

            // we can navigate again.
            _this.isAnimating = false;

            // focus retention
            $nextMenuEl.focus();
          });
        }
      });

      // animation class
      if (this.options.direction === 'r2l') {
        $nextMenuEl.addClass(!isBackNavigation ? 'animate-inFromRight' : 'animate-inFromLeft');
      }
      else {
        $nextMenuEl.addClass(isBackNavigation ? 'animate-inFromRight' : 'animate-inFromLeft');
      }
    },

    /*
    Unbind events that trigger methods.
    */
    unbindEvents: function () {
      this.$element.off('.' + this._name);
    },

    log: function (item) {
      if (!this.options.debug) {
        return;
      }
      if (typeof item === 'object') {
        console.log('[' + this._name + ']', item); // eslint-disable-line no-console
      }
      else {
        console.log('[' + this._name + '] ' + item); // eslint-disable-line no-console
      }
    },

    error: function (item) {
      if (typeof item === 'object') {
        console.error('[' + this._name + ']', item); // eslint-disable-line no-console
      }
      else {
        console.error('[' + this._name + '] ' + item); // eslint-disable-line no-console
      }
    }

  });

  $.fn.uxMenu = function (options) {
    var args = arguments;
    if (options === 'undefined' || typeof options === 'object') {
      return this.each(function () {
        if (!$.data(this, pluginName)) {
          $.data(this, pluginName, new Plugin(this, options));
        }
      });
    }
    else if (typeof options === 'string' && options[0] !== '_' && options !== 'init') {
      var returns;
      this.each(function () {
        var instance = $.data(this, pluginName);

        if (instance instanceof Plugin && typeof instance[options] === 'function') {
          returns = instance[options].apply(instance, Array.prototype.slice.call(args, 1));
        }

        // Allow instances to be destroyed via the 'destroy' method
        if (options === 'destroy') {
          $.data(this, 'plugin_' + pluginName, null);
        }
      });

      return returns !== 'undefined' ? returns : this;
    }
  };

  $.fn.uxMenu.defaults = {
    debug: false,
    // show back button
    backNav: false,
    // back nav text
    backText: 'Back',
    // back icon
    backIcon: '',
    // show breadcrumbs
    breadcrumbNav: true,
    // the location of this element within the dom
    backSelector: '.' + pluginName + '-top',
    // initial breadcrumb text
    breadcrumbText: 'All',
    // icon used in initial breadcrumb
    breadcrumbIcon: '',
    // breadcrumb icon that separates each item
    breadcrumbSeparatorIcon: '',
    // a selector that allows for placing the breadcrumb somehwere else in the dom
    breadcrumbSelector: '.' + pluginName + '-top',
    // icon used to signify menu items that will open a submenu
    itemIcon: '',
    // delay between each menu item sliding animation
    itemDelayInterval: 60,
    // direction
    direction: 'r2l',
    // theme
    theme: '',
    relocateNav: '',
    // callback: item that doesn´t have a submenu gets clicked -
    // onItemClick([event], [inner HTML of the clicked item])
    onItemClick: null
  };

})(jQuery, window, document);
