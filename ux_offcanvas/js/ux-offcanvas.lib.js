(function ($, window, document) {

  'use strict';

  var _defaults;
  _defaults = {
    animate: 'TweenLite',
    items: {},
    speed: 0.6
  };

  $.ux_offcanvas = function (offcanvas, options) {
    this.debug = false;
    this._defaults = _defaults;
    this.settings = $.extend(true, {}, this._defaults, options);
    this.body = $('body');
    this.overlay = $('#ux-overlay');
    this.offcanvas = $(offcanvas);
    this.items = {};
    this.active_id = '';

    /*
     * Initialize offcanvas and gather all the data
     */
    this.initialize = (function (_this) {
      return function () {
        _this.set_animator();
        _this.bind_items();
        _this.bind_offcanvas();
        _this.bind_keys();
        _this.listen_open();
        _this.listen_close();
        _this.log('UX Offcanvas has been initialized.');
      };
    })(this);

    /**
     * Create offcanvas items.
     */
    this.bind_items = (function (_this) {
      return function () {
        var config;
        for (var id in _this.settings.items) {
          if (_this.settings.items[id]) {
            config = _this.settings.items[id];
            config.speed = _this.settings.speed;
            _this.items[config.id] = $('#ux-offcanvas-' + config.id).ux_offcanvas_item(offcanvas, config);
          }
        }
      };
    })(this);

    /**
     * Create offcanvas.
     */
    this.bind_offcanvas = (function (_this) {
      return function () {
        _this.overlay.on('click', function (event) {
          if (!$(event.target).closest('.ux-offcanvas-item').length) {
            _this.close(_this.active_id);
          }
        });
      };
    })(this);

    /**
     * Create key commands.
     */
    this.bind_keys = (function (_this) {
      return function () {
        $(document).keyup(function (e) {
          if (e.keyCode === 27) {
            _this.close(_this.active_id);
          }
        });
      };
    })(this);

    /**
     * Listen for an offcanvas open event.
     */
    this.listen_open = (function (_this) {
      return function () {
        _this.offcanvas.on('ux_offcanvas.open', function (event, id) {
          var active_id = _this.active_id;
          if (active_id) {
            _this.toggle(active_id, id);
          }
          else if (active_id !== id) {
            _this.open(id);
          }
        });
      };
    })(this);

    /**
     * Listen for an offcanvas close event.
     */
    this.listen_close = (function (_this) {
      return function () {
        _this.offcanvas.on('ux_offcanvas.close', function (event, id) {
          if (_this.active_id === id) {
            _this.close(id);
          }
        });
      };
    })(this);

    /**
     * Open offcanvas offcanvas.
     */
    this.toggle = (function (_this) {
      return function (close_id, open_id) {
        _this.active_id = open_id;
        _this.items[close_id].triggerHandler('ux_offcanvas_item.close', function () {
          _this.items[open_id].triggerHandler('ux_offcanvas_item.open');
        });
      };
    })(this);

    /**
     * Open offcanvas offcanvas.
     */
    this.open = (function (_this) {
      return function (id) {
        _this.active_id = id;
        _this.body.addClass('ux-offcanvas-active');
        // Animate overlay.
        _this.animate.to(_this.overlay, _this.settings.speed, {opacity: 0.4});
        _this.items[id].triggerHandler('ux_offcanvas_item.open');
      };
    })(this);

    /**
     * Open offcanvas offcanvas.
     */
    this.close = (function (_this) {
      return function (id) {
        _this.active_id = '';
        // Animate overlay.
        _this.animate.to(_this.overlay, _this.settings.speed, {opacity: 0, clearProps: 'all'});
        _this.items[id].triggerHandler('ux_offcanvas_item.close', function () {
          _this.body.removeClass('ux-offcanvas-active');
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
          console.log('[UX Offcanvas]', item); // eslint-disable-line no-console
        }
        else {
          console.log('[UX Offcanvas] ' + item); // eslint-disable-line no-console
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

  $.fn.ux_offcanvas = function (opts) {
    return this.each(function (index, offcanvas) {
      if (opts.items && !$.data(offcanvas, 'ux_offcanvas')) {
        return $.data(offcanvas, 'ux_offcanvas', new $.ux_offcanvas(offcanvas, opts));
      }
    });
  };
  return $.fn.ux_offcanvas;

})(window.jQuery, window, document);
