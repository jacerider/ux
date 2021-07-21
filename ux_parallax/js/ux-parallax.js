/**
 * @file
 * Global ux_offcanvas javascript.
 */

(function ($, Drupal, drupalSettings, displace) {

  'use strict';

  function UxParallax($parallax, options) {
    this.settings = $.extend(true, {}, this._defaults, options);
    this.element = $parallax;
    this.layers = $(this.settings.selector.layer, this.element);
    this.fit = $(this.settings.selector.fit, this.element);
    this.background = $(this.settings.selector.background, this.element);
    if (!this.background.length) {
      this.background = $(this.settings.selector.background_inside, this.element).find('img:first').addClass('ux-parallax-background');
    }
    this.source = this.settings.source ? this.settings.source : this.element;
    this.parent = this.settings.parent ? this.settings.parent : this.element.parent();
    this.window = this.settings.scroller;
    if (this.background.length > 0 && !this.settings.animateElement) {
      this.settings.animateElement = false;
    }
    else {
      this.settings.animateElement = true;
    }
    this.debug = false;
    this.size = {
      window: {},
      source: {},
      parent: {},
      element: {},
      background: {},
      layer: []
    };
    this.position = {
      window: {},
      source: {},
      element: {},
      parent: {}
    };
    this.loaded = false;
    this.initialize();
  }

  /**
   * Global offcanvas object.
   */
  $.extend(UxParallax, /** @lends Drupal.UxParallax */{
    /**
     * Holds references to instantiated UxParallax objects.
     *
     * @type {Array.<Drupal.UxParallax>}
     */
    instances: {},

    initialize: function () {
      // var _this = this;
      $('body').addClass('has-ux-parallax');
      // Drupal.Ux.addScrollCallback($.proxy(function () {
      //   _this.scroll();
      // }, _this));
    }

    // scroll: function () {
    //   var _this = this;
    //   for (var id in _this.instances) {
    //     if (_this.instances[id]) {
    //       _this.instances[id].scroll();
    //     }
    //   }
    // }

  });

  /**
   * Individual offcanvas items.
   */
  $.extend(UxParallax.prototype, /** @lends Drupal.UxParallax# */{
    _defaults: {
      animation: 'y',
      animate: 'TweenLite',
      optimize: true,
      initialAnimationDuration: 1,
      orientation: 'vertical',
      factor: 0.5,
      screen: {
        sm: 0,
        md: 640,
        lg: 1024
      },
      perspective: 800,
      perspectiveOrigin: '50% 50%',
      preload: true,
      preloader: '<div class="ux-parallax-loader"><div class="ux-parallax-loader-inner"></div></div>',
      normalizeTop: true,
      overflow: false,
      anchor: 'center',
      size: 'auto',
      scroller: $(window),
      selector: {
        layer: '.ux-parallax-layer',
        background: '.ux-parallax-background',
        background_inside: '.ux-parallax-background-inside',
        fit: '.ux-parallax-fit'
      }
    },

    initialize: function () {
      var _this = this;
      _this.set_element_id();
      _this.set_animator();
      _this.set_orientation(_this.settings.orientation);
      _this.set_layer_index();
      _this.init_animus();
      _this.set_data();
      _this.set_classes();
      _this.load(function () {
        _this.set_size();
        _this.set_position();
        _this.set_responsive_context();
        _this.bind_resize();
        _this.bind_scroll();
        _this.scroll();
        _this.loaded = true;
        _this.element.triggerHandler('ux_parallax.load', [_this.element]);
        _this.source.addClass('ux-parallax-built');
        _this.log('UxParallax has been initialized.');
      });
    },

    /*
    Parallax images and content

    @param position [Fixnum] Current scrolling position
     */
    parallax: function () {
      var _this = this;
      var delta;
      var delta_difference;
      var delta_progress;
      var from_middle;
      var progress;
      var progress_difference;
      var to_middle;
      delta = _this.position.window[_this.param.middle] - _this.position.source[_this.param.middle];
      progress = (_this.position.window[_this.param.end] - _this.position.source[_this.param.start]) / _this.size.source.spanning;
      delta_progress = delta / _this.size.source.spanning;
      if (_this.position.source.beginning) {
        delta_difference = _this.size.window[_this.param.half] - _this.position.source[_this.param.middle];
        progress_difference = delta_difference / _this.size.source.spanning;
        delta -= delta_difference;
        progress -= progress_difference;
        delta_progress -= progress_difference;
      }
      to_middle = delta <= 0;
      from_middle = delta >= 0;
      if (!_this.loaded) {
        to_middle = false;
        from_middle = false;
        if (!_this.in_view) {
          delta = 0;
          progress = 0;
          delta_progress = 0;
        }
      }
      $.each(_this.animation, function (index, animatable) {
        var animation;
        var animation_data;
        var animation_target;
        animation = {};
        animation_target = animatable[0];
        animation_data = animatable[1][_this.current_responsive_size];
        $.each(animation_data.state, function (key, value) {
          var current_base;
          var current_peak;
          var delta_normalized;
          var delta_progress_normalized;
          var difference_normalized;
          var middle_normalization;
          var mode;
          var normalization;
          var progress_inner;
          var progress_normalized;
          var range_progress_normalized;
          var total_range;
          if (animation_data.state[key][0] !== '*') {
            normalization = animation_data.state[key][0];
            mode = animation_data.state[key][1];
            delta_normalized = delta * normalization;
            progress_normalized = progress * normalization;
            delta_progress_normalized = delta_progress * normalization;
            if ((mode === 'to-middle' && from_middle || mode === 'from-middle' && to_middle) && key !== 'opacity') {
              return;
            }
            switch (key) {
              case 'opacity':
                if (mode === 'from-middle') {
                  animation[key] = 1 - delta_progress_normalized * 2;
                  if (animation[key] > 1) {
                    animation[key] = 1;
                  }
                }
                else if (mode === 'to-middle') {
                  animation[key] = 1 + delta_progress_normalized * 2;
                  if (animation[key] > 1) {
                    animation[key] = 1;
                  }
                }
                else {
                  animation[key] = 1 - Math.abs(delta_progress_normalized * 2);
                }
                animation[key] = animation[key].toFixed(2);
                break;
              case 'scale':
              case 'scaleX':
              case 'scaleY':
              case 'scaleZ':
                if (mode === 'up') {
                  animation[key] = 1 + progress_normalized;
                }
                else if (mode === 'down') {
                  animation[key] = 1 + 0.5 * normalization - progress_normalized;
                }
                else {
                  animation[key] = 1 + delta_progress_normalized;
                }
                break;
              case 'rotation':
              case 'rotationX':
              case 'rotationY':
              case 'rotationZ':
                animation[key] = (180 * (progress - 0.5)) * normalization;
                break;
              case 'skewX':
              case 'skewY':
              case 'skewZ':
                animation[key] = (180 * (progress - 0.5)) * normalization;
                break;
              case 'x':
              case 'y':
                if (_this.background.length > 0) {
                  if (!_this.settings.overflow && _this.size.source.same[_this.param.size] && key === _this.param.axis) {
                    progress_inner = {};
                    progress_inner[_this.param.start] = (_this.position.window[_this.param.start] - _this.position.source[_this.param.start]) / _this.size.source[_this.param.size];
                    progress_inner[_this.param.end] = (_this.position.window[_this.param.end] - _this.position.source[_this.param.end]) / _this.size.source[_this.param.size];
                    progress_inner.value = 0;
                    if (progress_inner[_this.param.start] < 0) {
                      progress_inner[_this.param.start] = 0;
                    }
                    else {
                      progress_inner.value = progress_inner[_this.param.start];
                    }
                    if (progress_inner[_this.param.end] > 0) {
                      progress_inner[_this.param.end] = 0;
                    }
                    else {
                      progress_inner.value = progress_inner[_this.param.end];
                    }
                    animation[key] = progress_inner.value * _this.size.source[_this.param.size] * normalization;
                  }
                  else {
                    difference_normalized = _this.size.source.difference * (1 + normalization);
                    current_base = _this.position.element[_this.param.start] - delta_normalized - difference_normalized;
                    current_peak = current_base + _this.size.element[_this.param.size];
                    if (!(current_base > _this.position.source[_this.param.start] && current_peak < _this.position.source[_this.param.end])) {
                      animation[key] = delta_normalized;
                    }
                  }
                }
                else {
                  animation[key] = delta_normalized;
                }
                animation[key] = animation[key].toFixed();
                break;
              default:
                animation[key] = delta_normalized;
            }
          }
          else {
            mode = animation_data.state[key][3];
            if (mode === 'to-middle' || mode === 'from-middle') {
              middle_normalization = 2;
            }
            else {
              middle_normalization = 1;
            }
            total_range = Math.abs(value[1]) + Math.abs(value[2]);
            if (mode === 'to-middle' && from_middle) {
              return;
            }
            else if (mode === 'from-middle') {
              if (to_middle) {
                animation[key] = value[1];
              }
              else {
                animation[key] = value[1] - total_range * (progress - 0.5) * middle_normalization;
              }
            }
            else {
              range_progress_normalized = total_range * progress * middle_normalization;
              if (value[1] > value[2]) {
                animation[key] = value[1] - range_progress_normalized;
              }
              else {
                animation[key] = value[1] + range_progress_normalized;
              }
            }
            return;
          }
        });
        // animation.onComplete = _this.stop_ticking;
        animation.onComplete = function () {
          _this.stop_ticking();
        };
        if (_this.loaded) {
          _this.animate.set(animation_target, animation);
        }
        else {
          _this.animate.to(animation_target, _this.settings.initialAnimationDuration, animation);
        }
      });
      _this.element.triggerHandler('ux_parallax.parallax', [progress, delta, delta_progress]);
    },

    /*
    Binds the window resize event to cache current window
    width and height and to set the layout up
     */
    bind_resize: function () {
      var _this = this;
      Drupal.Ux.addResizeCallback($.proxy(function () {
        _this.set_size();
        _this.set_position();
        _this.set_responsive_context();
        _this.parallax();
      }, _this));
    },

    /*
    Bind the window scroll event to fade content on scroll down
     */
    bind_scroll: function () {
      var _this = this;
      Drupal.Ux.addScrollCallback($.proxy(function () {
        _this.scroll();
      }, _this));
    },

    /*
    Load element
     */
    load: function (callback) {
      var _this = this;
      var check_loaded;
      var loadables;
      var loaded;
      var preloader;
      loadables = _this.background.add(_this.layers.filter('img'));
      if (loadables.length > 0) {
        loaded = 0;
        if (_this.settings.preloader) {
          preloader = $(_this.settings.preloader);
          _this.background.before(preloader);
        }
        check_loaded = function () {
          loaded += 1;
          if (loaded === loadables.length) {
            if (_this.settings.preloader) {
              _this.animate.to(preloader, 1, {
                scale: 2,
                opacity: 0,
                onComplete: function () {
                  preloader.remove();
                }
              });
            }
            callback.call();
          }
        };
        loadables.each(function (index, loadable) {
          var image;
          var parent;
          var isResponsive;
          var image_loader;
          var src;
          image = $(loadable);
          parent = image.parent();
          isResponsive = false;
          if (image.attr('data-ux-parallax-src') != null) {
            src = image.attr('data-ux-parallax-src');
          }
          else {
            src = image.attr('src');
          }
          image_loader = $('<img>');
          if (parent.is('picture')) {
            parent = parent.clone();
            parent.find('img').remove().end();
            parent.append(image_loader);
            isResponsive = true;
          }
          if (isResponsive && !window.HTMLPictureElement) {
            if (window.respimage) {
              window.respimage({elements: [image_loader[0]]});
            }
            else if (window.picturefill) {
              window.picturefill({elements: [image_loader[0]]});
            }
            else if (src) {
              image_loader.attr('src', src);
            }
          }
          else {
            image_loader.attr('src', src);
          }
          image_loader.on('load', function () {
            image.attr('src', src);
            check_loaded();
          }).on('error', function () {
            _this.error('Image with src=\"" + src + "\" failed to load.');
            check_loaded();
          });
        });
      }
      else {
        callback.call();
      }
    },

    /*
    Set the element id if it doesn't have one or get the existing one.
     */
    set_element_id: function () {
      var _this = this;
      if (_this.element.attr('id') != null) {
        _this.id = _this.element.attr('id');
      }
      else {
        _this.id = _this.get_random_id('ux-parallax');
        _this.element.attr('id', _this.id);
      }
      _this.log('Element id has been set to ' + _this.id + '.');
    },

    /*
    Get a random id by concatenating input string with a random number.
     */
    get_random_id: function (string) {
      return string + '-' + Math.floor((Math.random() * 100000) + 1);
    },

    /*
    Setup Animation Platform
     */
    set_animator: function () {
      var _this = this;
      _this.animate = window[_this.settings.animate];
      _this.log('Animating using the ' + _this.settings.animate + ' platform.');
    },

    /*
    Sets parameters based on orientation settings
     */
    set_orientation: function (orientation) {
      var _this = this;
      _this.param = {};
      if (orientation === 'vertical') {
        _this.param.size = 'height';
        _this.param.middle = 'vmiddle';
        _this.param.start = 'top';
        _this.param.end = 'bottom';
        _this.param.axis = 'y';
        _this.param.scroll = 'scrollTop';
        _this.param.half = 'halfHeight';
      }
      else {
        _this.param.size = 'width';
        _this.param.middle = 'hmiddle';
        _this.param.start = 'left';
        _this.param.end = 'right';
        _this.param.axis = 'x';
        _this.param.scroll = 'scrollLeft';
        _this.param.half = 'halfWidth';
      }
    },

    /*
    Set layer z-index for overlapping
     */
    set_layer_index: function () {
      var _this = this;
      _this.layers.each(function (index, layer) {
        $(layer).css({
          'z-index': _this.layers.length - index
        });
      });
    },

    /*
    Set default animation parameters for UxParallax animation objects
    and create animus model
     */
    init_animus: function () {
      var _this = this;
      var override;
      override = {
        duration: _this.settings.animation.duration,
        easing: _this.settings.animation.easing
      };
      _this.animus = new $.animus(override);
      _this.log('Initialized animus parser.');
    },

    /*
    Get individual element animation data
     */
    set_data: function () {
      var _this = this;
      _this.animation = [];
      if (_this.settings.animateElement) {
        _this.animation.push([_this.element, _this.get_animation_data('element', _this.element)]);
      }
      if (_this.background.length > 0) {
        _this.animation.push([_this.background, _this.get_animation_data('background', _this.background)]);
        _this.size['background'] = _this.get_size_data('background', _this.background);
      }
      if (_this.layers.length > 0) {
        _this.animation['layer'] = [];
        _this.layers.each(function (index, layer) {
          layer = $(layer);
          _this.animation.push([layer, _this.get_animation_data('layer', layer, index)]);
          _this.size['layer'].push(_this.get_size_data('layer', layer, index));
        });
      }
      _this.log('UxParallax data has been processed.');
      _this.log(_this.animation);
    },

    /*
    Position background according to anchor settings
     */
    set_canvas_position: function () {
      var _this = this;
      var height_ratio;
      var margin_left;
      var margin_top;
      var width_ratio;
      switch (_this.settings.anchor) {
        case 'center':
          margin_left = -(_this.size.element.width - _this.size.source.width) / 2;
          margin_top = -(_this.size.element.height - _this.size.source.height) / 2;
          break;
        case 'top':
          margin_left = -(_this.size.element.width - _this.size.source.width) / 2;
          margin_top = 0;
          break;
        case 'bottom':
          margin_left = -(_this.size.element.width - _this.size.source.width) / 2;
          margin_top = -(_this.size.element.height - _this.size.source.height);
          break;
        case 'left':
          margin_left = 0;
          margin_top = -(_this.size.element.height - _this.size.source.height);
          break;
        case 'right':
          margin_left = -(_this.size.element.width - _this.size.source.width);
          margin_top = -(_this.size.element.height - _this.size.source.height) / 2;
          break;
        case 'top-left':
          margin_left = 0;
          margin_top = 0;
          break;
        case 'bottom-left':
          margin_left = 0;
          margin_top = -(_this.size.element.height - _this.size.source.height);
          break;
        case 'top-right':
          margin_left = -(_this.size.element.width - _this.size.source.width);
          margin_top = 0;
          break;
        case 'bottom-right':
          margin_left = -(_this.size.element.width - _this.size.source.width);
          margin_top = -(_this.size.element.height - _this.size.source.height);
      }
      if (margin_left > 0) {
        margin_left = 0;
      }
      if (margin_top > 0) {
        margin_top = 0;
      }
      if (_this.loaded) {
        _this.animate.set(_this.background, {
          'margin-top': margin_top,
          'margin-left': margin_left
        });
      }
      else {
        _this.animate.to(_this.background, _this.settings.initialAnimationDuration, {
          'margin-top': margin_top
        });
      }
      width_ratio = _this.size.element.width / _this.size.background.width;
      height_ratio = _this.size.element.height / _this.size.background.height;
      _this.layers.each(function (index, layer) {
        var layer_css;
        layer = $(layer);
        layer_css = {};
        if ('top' in _this.size.layer[index].position) {
          layer_css.top = height_ratio * _this.size.layer[index].position.top + margin_top;
        }
        else if ('bottom' in _this.size.layer[index].position) {
          layer_css.bottom = height_ratio * _this.size.layer[index].position.bottom + margin_top;
        }
        if ('left' in _this.size.layer[index].position) {
          layer_css.left = width_ratio * _this.size.layer[index].position.left + margin_left;
        }
        else if ('right' in _this.size.layer[index].position) {
          layer_css.right = width_ratio * _this.size.layer[index].position.right + margin_left;
        }
        if ('width' in _this.size.layer[index]) {
          layer_css.width = width_ratio * _this.size.layer[index].width;
        }
        if ('height' in _this.size.layer[index]) {
          layer_css.height = height_ratio * _this.size.layer[index].height;
        }
        if (_this.loaded) {
          _this.animate.set(layer, layer_css);
        }
        else {
          _this.animate.to(layer, _this.settings.initialAnimationDuration, layer_css);
        }
      });
    },

    /*
    Initialize element size data

    canvas: {
      background: {
        width: 1100,
        height: 500
      },
      layer: [
        {
          width: 500,
          height: 600,
          top: 500,
          left: 500
        }
      ]
    }
     */
    get_size_data: function (context, object, index) {
      var _this = this;
      var data;
      var element_class;
      var element_id;
      var js_data;
      data = {};
      if (object.attr('id') != null) {
        element_id = '#' + object.attr('id');
      }
      else {
        element_id = '';
      }
      if (object.attr('class') != null) {
        element_class = object.attr('class').split(' ').map(function (item) {
          return '.' + item;
        });
      }
      else {
        element_class = [];
      }
      js_data = false;
      if ((_this.settings.canvas != null) && (_this.settings.canvas[context] != null)) {
        if (index != null) {
          if (_this.settings.canvas[context][index] != null) {
            js_data = _this.settings.canvas[context][index];
          }
          else if (_this.settings.canvas[context][element_id] != null) {
            js_data = _this.settings.canvas[context][element_id];
          }
          else {
            $.each(element_class, function (index, _class) {
              if (_this.settings.canvas[context][_class] != null) {
                js_data = _this.settings.canvas[context][_class];
              }
            });
          }
        }
        else {
          js_data = _this.settings.canvas[context];
        }
      }
      if (object.attr('data-imagine-width') != null) {
        data.width = parseFloat(object.attr('data-imagine-width'));
      }
      else if (js_data && (js_data.width != null)) {
        data.width = parseFloat(_this.delete_property(js_data, 'width'));
      }
      if (object.attr('data-imagine-height') != null) {
        data.height = parseFloat(object.attr('data-imagine-height'));
      }
      else if (js_data && (js_data.height != null)) {
        data.height = parseFloat(_this.delete_property(js_data, 'height'));
      }
      if (context === 'layer') {
        data.position = {};
        if (object.attr('data-imagine-top') != null) {
          data.position.top = parseFloat(object.attr('data-imagine-top'));
        }
        else if (js_data && (js_data.top != null)) {
          data.position.top = parseFloat(_this.delete_property(js_data, 'top'));
        }
        else if (object.attr('data-imagine-bottom') != null) {
          data.position.bottom = parseFloat(object.attr('data-imagine-bottom'));
        }
        else if (js_data && (js_data.bottom != null)) {
          data.position.bottom = parseFloat(_this.delete_property(js_data, 'bottom'));
        }
        else {
          data.position.top = 0;
        }
        if (object.attr('data-imagine-left') != null) {
          data.position.left = parseFloat(object.attr('data-imagine-left'));
        }
        else if (js_data && (js_data.left != null)) {
          data.position.left = parseFloat(_this.delete_property(js_data, 'left'));
        }
        else if (object.attr('data-imagine-right') != null) {
          data.position.right = parseFloat(object.attr('data-imagine-right'));
        }
        else if (js_data && (js_data.right != null)) {
          data.position.right = parseFloat(_this.delete_property(js_data, 'right'));
        }
        else {
          data.position.left = 0;
        }
      }
      return data;
    },

    /*
    Get the size of an image element
     */
    get_image_size: function (image) {
      var size;
      size = {};
      size.width = 'auto';
      if (image[0].naturalWidth != null) {
        size.width = image[0].naturalWidth;
      }
      else if (image[0].width != null) {
        size.width = image[0].width;
      }
      else if (image.width != null) {
        size.width = image.width();
      }
      size.height = 'auto';
      if (image[0].naturalHeight != null) {
        size.height = image[0].naturalHeight;
      }
      else if (image[0].height != null) {
        size.height = image[0].height;
      }
      else if (image.height != null) {
        size.height = image.height();
      }
      return size;
    },

    /*
    Initialize element animation data
     */
    get_animation_data: function (context, element, index) {
      var _this = this;
      var animation;
      var animation_default;
      var animation_object;
      var element_class;
      var element_id;
      animation = {};
      animation_default = '';
      animation_object = null;
      if (element.attr('id') != null) {
        element_id = '#' + element.attr('id');
      }
      else {
        element_id = '';
      }
      if (element.attr('class') != null) {
        element_class = element.attr('class').split(' ').map(function (item) {
          return '.' + item;
        });
      }
      else {
        element_class = [];
      }
      if (element.attr('data-ux-parallax') != null) {
        animation_default = element.attr('data-ux-parallax');
      }
      else if (typeof _this.settings.animation === 'string') {
        animation_default = _this.settings.animation;
      }
      else if (typeof _this.settings.animation === 'object') {
        if (typeof _this.settings.animation[context] === 'string') {
          animation_default = _this.settings.animation[context];
        }
        else if ($.isArray(_this.settings.animation[context])) {
          if (typeof _this.settings.animation[context][index] === 'string') {
            animation_default = _this.settings.animation[context][index];
          }
          else if (typeof _this.settings.animation[context][index] === 'object') {
            animation_object = _this.settings.animation[context][index];
            if (typeof _this.settings.animation[context][index]['*'] === 'string') {
              animation_default = _this.settings.animation[context]['*'];
            }
          }
        }
        else if (typeof _this.settings.animation[context] === 'object') {
          animation_object = _this.settings.animation[context];
          if (typeof _this.settings.animation[context]['*'] === 'string') {
            animation_default = _this.settings.animation[context]['*'];
          }
          else if (typeof _this.settings.animation[context][element_id] === 'string') {
            animation_default = _this.settings.animation[context][element_id];
          }
          else if (typeof _this.settings.animation[context][element_id] === 'object') {
            animation_object = _this.settings.animation[context][element_id];
          }
          else {
            $.each(element_class, function (index, _class) {
              if (typeof _this.settings.animation[context][_class] === 'string') {
                animation_default = _this.settings.animation[context][_class];
              }
              if (typeof _this.settings.animation[context][_class] === 'object') {
                animation_object = _this.settings.animation[context][_class];
              }
            });
          }
        }
        else if (typeof _this.settings.animation['*'] === 'string') {
          animation_default = _this.settings.animation['*'];
        }
        else {
          animation_default = '';
        }
      }
      $.each(Object.keys(_this.settings.screen), function (responsive_mode_index, responsive_mode) {
        var ref;
        var responsive_animation;
        var scroll_transform;
        var transform;
        var value;
        animation[responsive_mode] = {};
        responsive_animation = {};
        if (animation_object) {
          if (typeof animation_object[responsive_mode] === 'string') {
            responsive_animation = animation_object[responsive_mode];
          }
          else {
            responsive_animation = animation_default;
          }
        }
        else {
          responsive_animation = animation_default;
        }
        animation[responsive_mode] = _this.animus.get(responsive_animation);
        ref = animation[responsive_mode].state;
        for (transform in ref) {
          if (Object.prototype.hasOwnProperty.call(ref, transform)) {
            value = ref[transform];
            value = '' + value;
            if (value.indexOf('..') === -1) {
              scroll_transform = value.split(/\s+/);
              if (scroll_transform.length === 0) {
                scroll_transform.push(_this.settings.factor);
              }
              else if (scroll_transform.length === 1) {
                if (scroll_transform[0] === '') {
                  scroll_transform[1] = 'default';
                  scroll_transform[0] = _this.settings.factor;
                }
                if (isNaN(scroll_transform[0])) {
                  scroll_transform[1] = scroll_transform[0];
                  scroll_transform[0] = _this.settings.factor;
                }
                else {
                  scroll_transform[1] = 'default';
                  scroll_transform[0] = parseFloat(scroll_transform[0]);
                }
              }
              animation[responsive_mode].state[transform] = scroll_transform;
            }
            else {
              scroll_transform = ['*'];
              value = value.split(/[\. ]+/);
              scroll_transform[1] = parseFloat(value[0]);
              scroll_transform[2] = parseFloat(value[1]);
              if (value.length === 2) {
                scroll_transform[3] = 'default';
              }
              else {
                scroll_transform[3] = value[2];
              }
              animation[responsive_mode].state[transform] = scroll_transform;
            }
          }
        }
      });
      return animation;
    },

    /*
    Add dynamic element classes and
     */
    set_classes: function () {
      var _this = this;
      var ux_parallax_style;
      var style;
      if ($('#ux-parallax-style').length === 0) {
        ux_parallax_style = '.ux-parallax-parent, .ux-parallax-background-inside { perspective: ' + _this.settings.perspective + 'px; -moz-perspective: ' + _this.settings.perspective + 'px; -webkit-perspective: ' + _this.settings.perspective + 'px; perspective-origin: ' + _this.settings.perspectiveOrigin + '; -moz-perspective-origin: ' + _this.settings.perspectiveOrigin + '; -webkit-perspective-origin: ' + _this.settings.perspectiveOrigin + '; backface-visibility: hidden; -moz-backface-visibility: hidden; -webkit-backface-visibility: hidden; }';
        style = document.createElement('style');
        style.id = 'ux-parallax-style';
        style.type = 'text/css';
        style.innerHTML = ux_parallax_style;
        $('head')[0].appendChild(style);
      }
      if (_this.settings.animateElement) {
        _this.parent.addClass('ux-parallax-parent');
      }
      else {
        _this.element.addClass('ux-parallax-parent');
      }
      if (_this.background.length > 0) {
        _this.element.addClass('ux-parallax-canvas');
      }
      _this.element.addClass('ux-parallax-animated');
      _this.background.addClass('ux-parallax-animated');
      _this.layers.addClass('ux-parallax-animated');
    },

    /*
    Set element and parent sizes
     */
    set_size: function () {
      var _this = this;
      _this.size.window.width = _this.window.width();
      _this.size.window.height = _this.window.height();
      _this.size.window.halfWidth = _this.size.window.width / 2;
      _this.size.window.halfHeight = _this.size.window.height / 2;
      if (_this.fit.length) {
        _this.fit.removeClass('ux-parallax-content');
        _this.source.height(_this.fit.outerHeight(true));
        _this.fit.addClass('ux-parallax-content');
      }
      else if (_this.settings.size === 'fullscreen') {
        _this.source.css({
          'width': _this.size.window.width,
          'height': _this.size.window.height,
          'max-width': _this.size.window.width,
          'max-height': _this.size.window.height
        });
      }
      else if (_this.settings.size === 'screenHeight') {
        _this.source.height(_this.size.window.height);
      }
      else if (_this.settings.size === 'screenWidth') {
        _this.source.width(_this.size.window.width);
      }
      _this.size.parent.width = _this.parent.outerWidth(true);
      _this.size.parent.height = _this.parent.outerHeight(true);
      _this.size.source.width = _this.source.outerWidth(true);
      _this.size.source.height = _this.source.outerHeight(true);

      /*
      Set the size for the element, based on whether it is used as background or
      as a normal element on the site.
       */
      if (_this.background.length > 0) {
        _this.size.background = $.extend(_this.get_image_size(_this.background), _this.size.background);
        _this.size.element.width = _this.size.source.width;
        _this.size.element.height = _this.size.source.width / _this.size.background.width * _this.size.background.height;
        if (_this.size.source.height > _this.size.element.height) {
          _this.size.element.width = _this.size.source.height / _this.size.background.height * _this.size.background.width;
          _this.size.element.height = _this.size.source.height;
        }

        /**
         * Adjust dimentions of element if source proprotions are different.
         */
        var offset = ((_this.size.window.height + _this.size.source.height) / 2) - _this.size.element.height;
        if (offset > 0) {
          _this.size.element.width += offset;
          _this.size.element.height += offset;
        }
        if (_this.loaded) {
          _this.animate.set(_this.background, {
            height: _this.size.element.height,
            width: _this.size.element.width
          });
        }
        else {
          _this.animate.to(_this.background, _this.settings.initialAnimationDuration, {
            height: _this.size.element.height,
            width: _this.size.element.width
          });
        }
      }
      else {
        _this.size.element.width = _this.element.outerWidth(true);
        _this.size.element.height = _this.element.outerHeight(true);
      }
      if (_this.layers.length > 0) {
        _this.layers.each(function (index, layer) {
          _this.size.layer[index] = $.extend(_this.get_image_size($(layer)), _this.size.layer[index]);
        });
      }
      _this.size.source.same = {
        width: _this.size.source.width === _this.size.element.width,
        height: _this.size.source.height === _this.size.element.height
      };
      _this.size.source.ratio = _this.size.window[_this.param.size] / _this.size.source[_this.param.size];
      _this.size.source.difference = _this.size.window[_this.param.size] - _this.size.source[_this.param.size];
      _this.size.source.spanning = _this.size.window[_this.param.size] + _this.size.source[_this.param.size];
      _this.log('Element sizes have been set.');
    },

    /*
    Set element top and bottom positioning on the page
     */
    set_position: function () {
      var _this = this;
      _this.position.source.top = _this.source.offset().top;
      _this.position.source.bottom = _this.position.source.top + _this.size.source.height;
      _this.position.source.vmiddle = (_this.position.source.top + _this.position.source.bottom) / 2;
      _this.position.source.left = _this.source.offset().left;
      _this.position.source.right = _this.position.source.left + _this.size.source.width;
      _this.position.source.hmiddle = (_this.position.source.left + _this.position.source.right) / 2;
      _this.position.element.top = _this.element.offset().top;
      _this.position.element.bottom = _this.position.element.top + _this.size.element.height;
      _this.position.element.left = _this.element.offset().left;
      _this.position.element.right = _this.position.element.left + _this.size.element.width;
      if (_this.background.length > 0) {
        _this.position.background = _this.position.element;
      }
      _this.position.parent.top = _this.parent.offset().top;
      _this.position.parent.bottom = _this.position.parent.top + _this.size.parent.height;
      _this.position.parent.left = _this.parent.offset().left;
      _this.position.parent.right = _this.position.parent.left + _this.size.parent.width;
      if (_this.settings.normalizeTop && _this.position.source[_this.param.start] < _this.size.window[_this.param.size] / 2 && _this.size.source[_this.param.size] < _this.size.window[_this.param.size]) {
        _this.position.source.beginning = true;
      }
      else {
        _this.position.source.beginning = false;
      }
      if (_this.background.length > 0 || _this.layers.length > 0) {
        _this.set_canvas_position();
      }
      _this.log('Element positions have been set.');
    },

    /*
    Set current responsive range parameter as xs, sm, md, lg or xl
     */
    set_responsive_context: function () {
      var _this = this;
      if (_this.size.window.width >= _this.settings.screen.xl) {
        _this.current_responsive_size = 'xl';
      }
      else if (_this.size.window.width >= _this.settings.screen.lg) {
        _this.current_responsive_size = 'lg';
      }
      else if (_this.size.window.width >= _this.settings.screen.md) {
        _this.current_responsive_size = 'md';
      }
      else if (_this.size.window.width >= _this.settings.screen.sm) {
        _this.current_responsive_size = 'sm';
      }
      else {
        _this.current_responsive_size = 'xs';
      }
      _this.log('Responsive context is ' + _this.current_responsive_size + '.');
    },

    /*
    Rendering performance optimization ticking for debouncing purposes
     */
    ticking: false,
    stop_ticking: function () {
      var _this = this;
      _this.ticking = false;
    },
    start_ticking: function () {
      var _this = this;
      _this.ticking = true;
    },

    /*
    Scroll animation handler
     */
    in_view_eariler: false,
    scroll: function () {
      var _this = this;
      var in_view_tolerance;
      var translation;
      _this.position.window[_this.param.start] = _this.window[_this.param.scroll]();
      _this.position.window[_this.param.end] = _this.position.window[_this.param.start] + _this.size.window[_this.param.size];
      _this.position.window[_this.param.middle] = _this.position.window[_this.param.start] + _this.size.window[_this.param.half];
      if (_this.element[0]._gsTransform && _this.element[0]._gsTransform[_this.param.axis]) {
        translation = _this.element[0]._gsTransform[_this.param.axis];
      }
      else {
        translation = 0;
      }
      in_view_tolerance = _this.size.window[_this.param.half] - translation;
      _this.in_view = !(_this.position.window[_this.param.end] - _this.position.source[_this.param.start] + in_view_tolerance < 0 || _this.position.window[_this.param.start] - _this.position.source[_this.param.end] - in_view_tolerance > 0);
      if (_this.in_view) {
        if (!_this.in_view_eariler) {
          _this.in_view_eariler = true;
          _this.element.addClass('ux-parallax-visible');
          _this.element.removeClass('ux-parallax-hidden');
          _this.element.triggerHandler('ux_parallax.visible', [_this.in_view]);
        }
      }
      else {
        if (_this.in_view_eariler) {
          _this.in_view_eariler = false;
          _this.element.removeClass('ux-parallax-visible');
          _this.element.addClass('ux-parallax-hidden');
          _this.element.triggerHandler('ux_parallax.visible', [_this.in_view]);
        }
        if (_this.loaded) {
          return;
        }
      }
      if (!_this.ticking) {
        _this.start_ticking();
        _this.parallax();
      }
    },

    /*
    Logger snippet within UxParallax
     */
    log: function (item) {
      var _this = this;
      if (!_this.debug) {
        return;
      }
      if (typeof item === 'object') {
        console.log('[UxParallax ' + _this.id + ']', item); // eslint-disable-line no-console
      }
      else {
        console.log('[UxParallax ' + _this.id + '] ' + item); // eslint-disable-line no-console
      }
    },

    /*
    Error logger snippet within UxParallax
     */
    error: function (item) {
      var _this = this;
      if (typeof item === 'object') {
        console.error('[UxParallax ' + _this.id + ']', item); // eslint-disable-line no-console
      }
      else {
        console.error('[UxParallax ' + _this.id + '] ' + item); // eslint-disable-line no-console
      }
    }

  });

  Drupal.behaviors.uxParallax = {
    attachCount: 0,

    attach: function (context, settings) {
      var $elements = $('.ux-parallax', context);
      if (settings.ux && settings.ux.parallax && settings.ux.parallax.items) {
        for (var id in settings.ux.parallax.items) {
          if (settings.ux.parallax.items[id]) {
            var $parallax = $('.ux-parallax-' + id, context).once('ux-parallax');
            if ($parallax.length) {
              UxParallax.instances[id] = new UxParallax($parallax, settings.ux.parallax.items[id]);
            }
          }
        }
      }
      this.parallaxAttach($elements);
      UxParallax.initialize();
    },

    parallaxAttach: function ($elements) {
      var _this = this;
      var $parallax = $elements.first().data('ux-parallax', true);
      if ($parallax.length) {
        $parallax.on('ux_parallax.load', function () {
          $elements = $elements.filter(function () {
            return $(this).data('ux-parallax') !== true;
          });
          _this.parallaxAttach($elements);
        });
        var id = 'attach-' + _this.attachCount;
        _this.attachCount++;
        UxParallax.instances[id] = new UxParallax($parallax);
      }
    }
  };

  // Expose constructor in the public space.
  Drupal.UxParallax = UxParallax;

})(jQuery, Drupal, drupalSettings, Drupal.displace);
