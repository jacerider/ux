(function ($, window, document) {

  'use strict';

  var _defaults;
  _defaults = {
    animate: 'TweenLite',
    position: 'left',
    size: 320,
    speed: 0.5
  };

  $.ux_offcanvas_item = function (offcanvas, item, trigger, options) {
    this.debug = false;
    this._defaults = _defaults;
    this.settings = $.extend(true, {}, this._defaults, options);
    this.id = this.settings.id;
    this.offcanvas = $(offcanvas);
    this.trigger = $(trigger);
    this.push = $('#ux-document');
    this.item = $(item);
    this.closeLink = $('.ux-offcanvas-close', this.item);
    this.active = false;
    this.param = {};
    this.size = this.settings.size;

    /*
     * Initialize ux_offcanvas_item and gather all the data
     */
    this.initialize = (function (_this) {
      return function () {
        _this.set_animator();
        _this.bind_trigger();
        _this.bind_close_link();
        _this.listen_open();
        _this.listen_close();
        _this.log('UX Offcanvas has been initialized.');
      };
    })(this);

    /*
     * Determine orientation.
     */
    this.set_orientation = (function (_this) {
      return function () {
        _this.param.pushFrom = 0;
        _this.param.itemTo = 0;
        switch (this.settings.position) {
          case 'left':
            _this.size = _this.offcanvas.width() < this.settings.size ? _this.offcanvas.width() - 20 : this.settings.size;
            _this.param.axis = 'x';
            _this.param.size = 'width';
            _this.param.pushTo = _this.settings.size * 1;
            _this.param.itemFrom = _this.settings.size * -1;
            break;
          case 'right':
            _this.size = _this.offcanvas.width() < this.settings.size ? _this.offcanvas.width() - 20 : this.settings.size;
            _this.param.axis = 'x';
            _this.param.size = 'width';
            _this.param.pushTo = _this.size * -1;
            _this.param.itemFrom = _this.size * 1;
            break;
          case 'top':
            _this.size = _this.offcanvas.height() < this.settings.size ? _this.offcanvas.height() - 20 : this.settings.size;
            _this.param.axis = 'y';
            _this.param.size = 'height';
            _this.param.pushTo = _this.size * 1;
            _this.param.itemFrom = _this.size * -1;
            break;
          case 'bottom':
            _this.size = _this.offcanvas.height() < this.settings.size ? _this.offcanvas.height() - 20 : this.settings.size;
            _this.param.axis = 'y';
            _this.param.size = 'height';
            _this.param.pushTo = _this.size * -1;
            _this.param.itemFrom = _this.size * 1;
            break;
        }
      };
    })(this);

    /**
     * Set the size of the offcanvas item.
     */
    this.set_size = (function (_this) {
      return function () {
        _this.item[_this.param.size](this.size);
      };
    })(this);

    /**
     * Open offcanvas element.
     */
    this.open = (function (_this) {
      return function (callback) {
        var animation;

        _this.set_orientation();
        _this.set_size();

        _this.active = true;
        _this.trigger.addClass('active');
        _this.item.addClass('active');
        _this.log('UX Offcanvas open.');

        // Push animation.
        animation = _this.animation('push');
        _this.animate.fromTo(_this.push, _this.settings.speed, animation.from, animation.to);

        // Item animation.
        animation = _this.animation('item');
        _this.animate.fromTo(_this.item, _this.settings.speed, animation.from, animation.to).eventCallback('onComplete', function () {
          if (callback) {
            callback.call();
          }
        });
      };
    })(this);

    /**
     * Close offcanvas element.
     */
    this.close = (function (_this) {
      return function (callback) {
        var animation;

        _this.active = false;
        _this.log('UX Offcanvas close.');

        // Push animation.
        animation = _this.animation('push');
        animation.from.clearProps = 'transform';
        _this.animate.fromTo(_this.push, _this.settings.speed, animation.to, animation.from);

        // Item animation.
        animation = _this.animation('item');
        animation.from.clearProps = 'transform';
        _this.animate.fromTo(_this.item, _this.settings.speed, animation.to, animation.from).eventCallback('onComplete', function () {
          _this.trigger.removeClass('active');
          _this.item.removeClass('active');
          if (callback) {
            callback.call();
          }
        });
      };
    })(this);

    /**
     * Build animation object.
     */
    this.animation = (function (_this) {
      return function (type) {
        var animation = {
          from: {},
          to: {}
        };
        animation.from[_this.param.axis] = _this.param[type + 'From'];
        animation.to[_this.param.axis] = _this.param[type + 'To'];

        return animation;
      };
    })(this);

    /**
     * Listen for open offcanvas event.
     */
    this.listen_open = (function (_this) {
      return function () {
        _this.item.on('ux_offcanvas_item.open', function (event, callback) {
          _this.open(callback);
        });
      };
    })(this);

    /**
     * Open offcanvas element.
     */
    this.listen_close = (function (_this) {
      return function () {
        _this.item.on('ux_offcanvas_item.close', function (event, callback) {
          _this.close(callback);
        });
      };
    })(this);

    /**
     * Bind trigger click handler.
     */
    this.bind_trigger = (function (_this) {
      return function () {
        _this.trigger.on('click', function (e) {
          e.preventDefault();
          _this.offcanvas.triggerHandler('ux_offcanvas.open', [_this.id]);
        });
      };
    })(this);

    /**
     * Bind trigger click handler.
     */
    this.bind_close_link = (function (_this) {
      return function () {
        _this.closeLink.on('click', function (e) {
          e.preventDefault();
          _this.offcanvas.triggerHandler('ux_offcanvas.close', [_this.id]);
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
          console.log('[UX Offcanvas ' + _this.id + ']', item); // eslint-disable-line no-console
        }
        else {
          console.log('[UX Offcanvas ' + _this.id + '] ' + item); // eslint-disable-line no-console
        }
      };
    })(this);

    /*
     * Error logger snippet within UX Offcanvas
     */
    this.error = (function (_this) {
      return function (item) {
        if (typeof item === 'object') {
          console.error('[UX Parallax ' + _this.id + ']', item); // eslint-disable-line no-console
        }
        else {
          console.error('[UX Parallax ' + _this.id + '] ' + item); // eslint-disable-line no-console
        }
      };
    })(this);

    this.initialize();
  };

  $.fn.ux_offcanvas_item = function (offcanvas, opts) {
    return this.each(function (index, item) {
      if (opts.id && !$.data(item, 'ux_offcanvas_item')) {
        var trigger = document.getElementById('ux-offcanvas-trigger-' + opts.id);
        if (trigger) {
          return $.data(trigger, 'ux_offcanvas_item', new $.ux_offcanvas_item(offcanvas, item, trigger, opts));
        }
      }
    });
  };
  return $.fn.ux_offcanvas_item;

})(window.jQuery, window, document);
