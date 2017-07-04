(function ($, window, document) {

  'use strict';

  var _defaults;
  _defaults = {
    animate: 'TweenLite',
    speed: 0.6,
    trailType: 'breadcrumb',
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
    this.menu = $(this.settings.selector.menu, element).addClass('animation-type-' + this.settings.animation);
    this.trail = $(this.settings.selector.trail, element).addClass('trail-type-' + this.settings.trail);
    this.links = $(this.settings.selector.links, element);
    this.trail_links = [];

    /*
     * Initialize offcanvas and gather all the data
     */
    this.initialize = (function (_this) {
      return function () {
        _this.set_animator();
        _this.element.on('opening', function () {
          if (!_this.initialized) {
            _this.bind_links();
            _this.show_level();
            _this.initial_level = _this.level_get();
            _this.initial_trail = $(_this.menu).find('.active-trail');
          }
          _this.visible = true;
          _this.initialized = true;
        });
        _this.element.on('closed', function () {
          _this.level_revert();
          _this.visible = false;
        });
        _this.log('UX Offcanvas Menu has been initialized.');
      };
    })(this);

    /*
     * Get current level.
     */
    this.level_get = (function (_this) {
      return function () {
        var level = _this.menu.attr('data-depth');
        return level > 0 ? level : 0;
      };
    })(this);

    /*
     * Get current level.
     */
    this.level_set = (function (_this) {
      return function (level) {
        level = level > 0 ? level : 0;
        _this.menu.attr('data-depth', level);
      };
    })(this);

    /**
     * Revert to a level.
     */
    this.level_revert = (function (_this) {
      return function () {
        var level = _this.initial_level;
        var width = _this.menu.width() * level;
        setTimeout(function () {
          _this.animate.to(_this.menu, 0, {x: width * -1});
          _this.initial_trail.addClass('active-trail');
          _this.show_level(level);
        }, _this.settings.speed);
      };
    })(this);

    /**
     * Show level.
     */
    this.show_level = (function (_this) {
      return function (level) {
        level = level || _this.level_get();
        var width = _this.menu.width() * level;
        var speed = _this.visible ? _this.settings.speed : 0.1;
        _this.element.find('.current').removeClass('current');

        var afterAnimation = function () {
          $('ul[data-level="' + level + '"] .active-trail', _this.menu).removeClass('active-trail');

          if (parseInt(level) === 0) {
            _this.menu.addClass('current');
          }
          else {
            _this.menu.find('.active-trail').last().addClass('current');
          }
        };

        _this.level_set(level);
        _this.trail_build(level);
        if (this.settings.animation === 'fade') {
          _this.animate.fromTo(_this.menu, speed / 2, {opacity: 1}, {opacity: 0}).eventCallback('onComplete', function () {
            _this.animate.to(_this.menu, 0, {x: width * -1});
            _this.animate.fromTo(_this.menu, speed / 2, {opacity: 0}, {opacity: 1});
            afterAnimation();
          });
        }
        else {
          _this.animate.to(_this.menu, speed, {x: width * -1}).eventCallback('onComplete', afterAnimation);
        }
      };
    })(this);

    /**
     * Bind active trail.
     */
    this.trail_build = (function (_this) {
      return function (level) {
        var i;
        var link;
        var links = [];
        level = level || _this.level_get();

        var trail_click = function (e) {
          e.preventDefault();
          _this.show_level($(this).attr('data-level'));
        };

        // Simple back trail.
        if (_this.settings.trail === 'back') {
          _this.trail.html('');
          link = $('<a>Back</a>');
          if (level > 0) {
            link.attr('data-level', level - 1);
            link.on('click', trail_click);
            if (_this.visible && _this.trail_links.length === 0) {
              _this.animate.fromTo(link, _this.settings.speed, {opacity: 0}, {opacity: 1, clearProps: 'All'});
            }
            links.push(1);
          }
          else {
            if (_this.trail_links.length === 0) {
              link.css({opacity: 0});
            }
            else {
              _this.animate.to(link, _this.settings.speed, {opacity: 0});
            }
          }
          link.appendTo(_this.trail);
        }

        // Breadcrump trail.
        else {
          links.push($('<a data-level="0">All</a>'));
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
        }

        _this.trail_links = links;
      };
    })(this);

    /**
     * Bind trigger click handler.
     */
    this.bind_links = (function (_this) {
      return function () {
        var windowPath = window.location.pathname;

        _this.menu.find('a').each(function () {
          var path = $(this).attr('href');
          // If we are set to show at the base level BUT we have a URL match,
          // we want to set the active trail.
          if (!_this.level_get() && path === windowPath) {
            $(this).parents('.children').addClass('active-trail');
            var closestLevel = $(this).siblings('[data-level]').attr('data-level');
            if (!closestLevel) {
              // If we do not have a child menu, use the parent level.
              closestLevel = $(this).closest('[data-level]').attr('data-level');
            }
            if (closestLevel) {
              _this.level_set(closestLevel);
            }
          }
          // If parent items are actual links, we want to append them to the
          // children so they are still accessible.
          if (path) {
            var child = $(this).siblings('[data-level]');
            if (child.length) {
              $(this).clone().prependTo(child).wrap('<li class="parent"></li>');
            }
          }
        });

        _this.links.on('click', function (e) {
          var child = $(this).siblings('[data-level]');
          if (child.length) {
            _this.log('Child Found:', child);
            e.preventDefault();
            var level = child.attr('data-level');
            $(this).parent().addClass('active-trail');
            _this.show_level(level);
          }
          else {
            _this.log('No Child Found');
          }
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
      return function (item, object) {
        if (!_this.debug) {
          return;
        }
        if (typeof item === 'object') {
          console.log('[UX Offcanvas Menu]', item); // eslint-disable-line no-console
        }
        else {
          if (object) {
            console.log('[UX Offcanvas Menu] ' + item, object); // eslint-disable-line no-console
          }
          else {
            console.log('[UX Offcanvas Menu] ' + item); // eslint-disable-line no-console
          }
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
      if (!$.data(element, 'ux_offcanvas_menu')) {
        return $.data(element, 'ux_offcanvas_menu', new $.ux_offcanvas_menu(element, opts));
      }
    });
  };
  return $.fn.ux_offcanvas_menu;

})(jQuery, window, document);
