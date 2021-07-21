(function ($, window, document) {

  'use strict';

  var _defaults;
  _defaults = {
    animate: 'TweenLite',
    speed: 0.6,
    selector: {
      trail: '.ux-offcanvas-menu-trail',
      menu: '.ux-offcanvas-menu',
      links: '.children > a'
    }
  };

  $.ux_offcanvas_menu = function (element, options) {
    this.debug = false;
    this.initialized = false;
    this.visible = false;
    this._defaults = _defaults;
    this.settings = $.extend(true, {}, this._defaults, options);
    this.element = $(element);
    this.offcanvas = $(element).closest('.ux-offcanvas-item');
    this.trail = $(this.settings.selector.trail, element);
    this.menu = $(this.settings.selector.menu, element);
    this.links = $(this.settings.selector.links, element);
    this.trail_links = [];

    /*
     * Initialize offcanvas and gather all the data
     */
    this.initialize = (function (_this) {
      return function () {
        _this.set_animator();
        _this.offcanvas.on('ux_offcanvas_item.open', function () {
          if (!_this.initialized) {
            _this.bind_links();
            _this.show_level();
          }
          _this.visible = true;
          _this.initialized = true;
        });
        _this.offcanvas.on('ux_offcanvas_item.close', function () {
          _this.visible = false;
        });
        _this.log('UX Offcanvas Menu has been initialized.');
      };
    })(this);

    /*
     * Get current level.
     */
    this.get_level = (function (_this) {
      return function () {
        var level = _this.menu.attr('data-depth');
        return level > 0 ? level : 0;
      };
    })(this);

    /*
     * Get current level.
     */
    this.set_level = (function (_this) {
      return function (level) {
        level = level > 0 ? level : 0;
        _this.menu.attr('data-depth', level);
      };
    })(this);

    /**
     * Show level.
     */
    this.show_level = (function (_this) {
      return function (level) {
        level = level || _this.get_level();
        var width = _this.menu.width() * level;
        var speed = _this.visible ? _this.settings.speed : 0.1;
        _this.set_level(level);
        _this.trail_build(level);
        _this.animate.to(_this.menu, speed, {x: width * -1}).eventCallback('onComplete', function () {
          $('ul[data-level="' + level + '"] li.active-trail', _this.menu).removeClass('active-trail');
        });

      };
    })(this);

    /**
     * Bind active trail.
     */
    this.trail_build = (function (_this) {
      return function (level) {
        level = level || _this.get_level();
        var i;
        var link;
        var links = [$('<a data-level="0">All</a>')];
        var layer;
        var layers = $('.active-trail' + this.settings.selector.links, _this.menu);
        for (i = 0; i < level; i++) {
          if (_this.trail_links[i + 1]) {
            links.push(_this.trail_links[i + 1]);
          }
          else {
            layer = layers.eq(i);
            links.push($('<a data-level="' + (i + 1) + '">' + $(layer).text() + '</a>'));
          }
        }

        var trail_click = function (e) {
          e.preventDefault();
          _this.show_level($(this).attr('data-level'));
        };

        var trail_remove = function (link) {
          $(link).remove();
        };

        // Add new links.
        if (links.length > _this.trail_links.length) {
          for (i = _this.trail_links.length; i < links.length; i++) {
            link = links[i];
            link.appendTo(_this.trail).on('click', trail_click);
            if (_this.visible) {
              _this.animate.fromTo(link, _this.settings.speed, {opacity: 0}, {opacity: 1, clearProps: 'All'});
            }
          }
        }
        // Remove old links.
        else if (links.length < _this.trail_links.length) {
          for (i = links.length; i < _this.trail_links.length; i++) {
            link = _this.trail_links[i];
            if (_this.visible) {
              _this.animate.to(link, _this.settings.speed, {opacity: 0}).eventCallback('onComplete', trail_remove, link);
            }
            else {
              link.remove();
            }
          }
        }

        _this.trail_links = links;
      };
    })(this);

    /**
     * Bind trigger click handler.
     */
    this.bind_links = (function (_this) {
      return function () {
        _this.links.on('click', function (e) {
          e.preventDefault();
          var level = $(this).next('ul').attr('data-level');
          $(this).parent('li').addClass('active-trail');
          _this.show_level(level);
        });
      };
    })(this);

    /**
     * Revert to a level.
     */
    this.level_revert = (function (_this) {
      return function (level) {
        var width = _this.menu.width() * level;
        _this.set_level(level);
        _this.animate.to(_this.menu, _this.settings.speed, {x: width * -1}).eventCallback('onComplete', function () {
          // $('ul[data-level="' + level + '"] li.active-trail', _this.menu).removeClass('active-trail');
          // _this.trail_build();
        });
      };
    })(this);

    /*
     * Setup Animation Platform
     */
    this.set_animator = (function (_this) {
      return function () {
        _this.animate = window[_this.settings.animate];
        _this.log('Animating using the ' + _this.settings.animate + ' platform.');
      };
    })(this);

    /*
     * Logger snippet within UX Offcanvas
     */
    this.log = (function (_this) {
      return function (item) {
        if (!_this.debug) {
          return;
        }
        if (typeof item === 'object') {
          console.log('[UX Offcanvas Menu]', item); // eslint-disable-line no-console
        }
        else {
          console.log('[UX Offcanvas Menu] ' + item); // eslint-disable-line no-console
        }
      };
    })(this);

    /*
     * Error logger snippet within UX Offcanvas
     */
    this.error = (function (_this) {
      return function (item) {
        if (typeof item === 'object') {
          console.error('[UX Parallax]', item); // eslint-disable-line no-console
        }
        else {
          console.error('[UX Parallax] ' + item); // eslint-disable-line no-console
        }
      };
    })(this);

    this.initialize();
  };

  $.fn.ux_offcanvas_menu = function (opts) {
    return this.each(function (index, element) {
      if (!$.data(element, 'ux_offcanvas')) {
        return $.data(element, 'ux_offcanvas_menu', new $.ux_offcanvas_menu(element, opts));
      }
    });
  };
  return $.fn.ux_offcanvas_menu;

})(window.jQuery, window, document);
