/**
 * @file
 * Library ux_parallax javascript.
 */

/* eslint-disable */

(function($, window, document) {
  "use strict";
  var _defaults;
  _defaults = {
    animation: "y",
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
    perspectiveOrigin: "50% 50%",
    preload: true,
    preloader: '<div class="ux-parallax-loader"><div class="ux-parallax-loader-inner"></div></div>',
    normalizeTop: true,
    overflow: false,
    anchor: 'center',
    size: 'auto',
    selector: {
      layer: '.ux-parallax-layer',
      background: '.ux-parallax-background',
      background_inside: '.ux-parallax-background-inside',
      fit: '.ux-parallax-fit'
    }
  };
  $.ux_parallax = function(element, options) {
    this._defaults = _defaults;
    this.settings = $.extend(true, {}, this._defaults, options);
    this.element = $(element);
    this.layers = $(this.settings.selector.layer, this.element);
    this.fit = $(this.settings.selector.fit, this.element);
    this.background = $(this.settings.selector.background, this.element);
    if (!this.background.length) {
      this.background = $(this.settings.selector.background_inside, this.element).find('img:first').addClass('ux-parallax-background');
    }
    this.source = this.settings.source ? this.settings.source : this.element;
    this.parent = this.settings.parent ? this.settings.parent : this.element.parent();
    this.window = $(window);
    this.wrapper = $('#ux-push');
    this.document = $('#ux-document');
    this.wrapper_width = this.wrapper.width();
    if (this.background.length > 0 && !this.settings.animateElement) {
      this.settings.animateElement = false;
    } else {
      this.settings.animateElement = true;
    }
    this.debug = false;
    this.size = {
      wrapper: {},
      source: {},
      parent: {},
      element: {},
      background: {},
      layer: []
    };
    this.position = {
      wrapper: {},
      source: {},
      element: {},
      parent: {}
    };
    this.loaded = false;

    /*
    Initialize ux_parallax and gather all the data
     */
    this.initialize = (function(_this) {
      return function() {
        _this.set_element_id();
        _this.set_animator();
        _this.set_orientation(_this.settings.orientation);
        _this.set_layer_index();
        _this.init_animus();
        _this.set_data();
        _this.set_classes();
        _this.load(function() {
          _this.set_size();
          _this.set_position();
          _this.set_responsive_context();
          _this.bind_resize();
          _this.bind_scroll();
          // Timeout to make sure things have loaded. Caused issue with delta
          // in this.parallax not being set yet.
          // setTimeout(function(){
            _this.scroll();
          // }, 10);
          _this.loaded = true;
          _this.element.triggerHandler('ux_parallax.load', [_this.element]);
          _this.source.addClass('ux-parallax-built');
          _this.log("UX Parallax has been initialized.");
        });
      };
    })(this);

    /*
    Parallax images and content

    @param position [Fixnum] Current scrolling position
     */
    this.parallax = (function(_this) {
      return function() {
        var delta, delta_difference, delta_progress, from_middle, progress, progress_difference, to_middle;
        delta = $.ux_parallax.wrapper[_this.param.middle] - _this.position.source[_this.param.middle];
        progress = ($.ux_parallax.wrapper[_this.param.end] - _this.position.source[_this.param.start]) / _this.size.source.spanning;
        delta_progress = delta / _this.size.source.spanning;
        if (_this.position.source.beginning) {
          delta_difference = _this.size.wrapper.half - _this.position.source[_this.param.middle];
          progress_difference = delta_difference / _this.size.source.spanning;
          // Commented out as it caused weird y positioning.
          // delta -= delta_difference;
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
        $.each(_this.animation, function(index, animatable) {
          var animation, animation_data, animation_target;
          animation = {};
          animation_target = animatable[0];
          animation_data = animatable[1][_this.current_responsive_size];
          $.each(animation_data.state, function(key, value) {
            var current_base, current_peak, delta_normalized, delta_progress_normalized, difference_normalized, middle_normalization, mode, normalization, progress_inner, progress_normalized, range_progress_normalized, total_range;
            if (animation_data.state[key][0] !== '*') {
              normalization = animation_data.state[key][0];
              mode = animation_data.state[key][1];
              delta_normalized = delta * normalization;
              progress_normalized = progress * normalization;
              delta_progress_normalized = delta_progress * normalization;
              if ((mode === "to-middle" && from_middle || mode === "from-middle" && to_middle) && key !== 'opacity') {
                return;
              }
              switch (key) {
                case 'opacity':
                  if (mode === "from-middle") {
                    animation[key] = 1 - delta_progress_normalized * 2;
                    if (animation[key] > 1) {
                      animation[key] = 1;
                    }
                  } else if (mode === "to-middle") {
                    animation[key] = 1 + delta_progress_normalized * 2;
                    if (animation[key] > 1) {
                      animation[key] = 1;
                    }
                  } else {
                    animation[key] = 1 - Math.abs(delta_progress_normalized * 2);
                  }
                  break;
                case 'scale':
                case 'scaleX':
                case 'scaleY':
                case 'scaleZ':
                  if (animation_data.state[key][2]) {
                    mode = animation_data.state[key][2];
                  }
                  if (mode === "up") {
                    animation[key] = 1 + progress_normalized;
                  } else if (mode === "down") {
                    animation[key] = 1 + 0.5 * normalization - progress_normalized;
                  } else {
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
                    if (_this.size.source.same[_this.param.size] && key === _this.param.axis) {
                      progress_inner = {};
                      progress_inner[_this.param.start] = ($.ux_parallax.wrapper[_this.param.start] - _this.position.source[_this.param.start]) / _this.size.source[_this.param.size];
                      progress_inner[_this.param.end] = ($.ux_parallax.wrapper[_this.param.end] - _this.position.source[_this.param.end]) / _this.size.source[_this.param.size];
                      progress_inner.value = 0;
                      if (progress_inner[_this.param.start] < 0) {
                        progress_inner[_this.param.start] = 0;
                      } else {
                        progress_inner.value = progress_inner[_this.param.start];
                      }
                      if (progress_inner[_this.param.end] > 0) {
                        progress_inner[_this.param.end] = 0;
                      } else {
                        progress_inner.value = progress_inner[_this.param.end];
                      }
                      animation[key] = progress_inner.value * _this.size.source[_this.param.size] * normalization;
                    } else {
                      difference_normalized = _this.size.source.difference * (1 + normalization);
                      difference_normalized = _this.size.source.difference / 2;
                      current_base = _this.position.element[_this.param.start] - delta_normalized - difference_normalized;
                      current_peak = current_base + _this.size.element[_this.param.size];
                      if (!(current_base > _this.position.source[_this.param.start] && current_peak < _this.position.source[_this.param.end])) {
                        animation[key] = delta_normalized;
                      }
                    }
                  } else {
                    animation[key] = delta_normalized;
                  }
                  break;
                default:
                  animation[key] = delta_normalized;
              }
            } else {
              mode = animation_data.state[key][3];
              if (mode === 'to-middle' || mode === 'from-middle') {
                middle_normalization = 2;
              } else {
                middle_normalization = 1;
              }
              total_range = Math.abs(value[1]) + Math.abs(value[2]);
              if (mode === "to-middle" && from_middle) {
                return;
              } else if (mode === "from-middle") {
                if (to_middle) {
                  animation[key] = value[1];
                } else {
                  animation[key] = value[1] - total_range * (progress - 0.5) * middle_normalization;
                }
              } else {
                range_progress_normalized = total_range * progress * middle_normalization;
                if (value[1] > value[2]) {
                  animation[key] = value[1] - range_progress_normalized;
                } else {
                  animation[key] = value[1] + range_progress_normalized;
                }
              }
              return;
            }
          });
          animation.onComplete = _this.stop_ticking;
          if (_this.loaded) {
            _this.animate.set(animation_target, animation);
          } else {
            _this.animate.to(animation_target, _this.settings.initialAnimationDuration, animation);
          }
        });
        _this.element.triggerHandler('ux_parallax.parallax', [progress, delta, delta_progress]);
      };
    })(this);

    /*
    Load element
     */
    this.load = (function(_this) {
      return function(callback) {
        var loadables, loaded, preloader;
        loadables = _this.background.add(_this.layers.filter('img'));
        if (loadables.length > 0) {
          loaded = 0;
          if (_this.settings.preloader) {
            preloader = $(_this.settings.preloader);
            _this.background.before(preloader);
          }
          loadables.each(function(index, loadable) {
            var image, parent, isResponsive, image_loader, src;
            image = $(loadable);
            parent = image.parent();
            isResponsive = false;
            if (image.attr('data-ux-parallax-src') != null) {
              src = image.attr('data-ux-parallax-src');
            } else {
              src = image.attr('src');
            }
            image_loader = $("<img>");
            if(parent.is('picture')){
              parent = parent.clone();
              parent.find('img').remove().end();
              parent.append(image_loader);
              isResponsive = true;
            }
            image_loader.attr('src', src);
            if(isResponsive && !window.HTMLPictureElement){
              if(window.respimage){
                window.respimage({elements: [$img[0]]});
              } else if(window.picturefill){
                window.picturefill({elements: [$img[0]]});
              } else if(src){
                $img.attr('src', src);
              }
            }
            image_loader.on('load', function() {
              image.attr('src', src);
              loaded += 1;
              if (loaded === loadables.length) {
                if (_this.settings.preloader) {
                  _this.animate.to(preloader, 1, {
                    scale: 2,
                    opacity: 0,
                    onComplete: function() {
                      preloader.remove();
                    }
                  });
                }
                callback.call();
              }
            }).on('error', function() {
              _this.error("Image with src=\"" + src + "\" failed to load.");
            });
          });
        } else {
          callback.call();
        }
      };
    })(this);

    /*
    Set default animation parameters for UX Parallax animation objects
    and create animus model
     */
    this.init_animus = (function(_this) {
      return function() {
        var override;
        override = {
          duration: _this.settings.animation.duration,
          easing: _this.settings.animation.easing
        };
        _this.animus = new $.animus(override);
        _this.log("Initialized animus parser.");
      };
    })(this);

    /*
    Get individual element animation data
     */
    this.set_data = (function(_this) {
      return function() {
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
          _this.layers.each(function(index, layer) {
            layer = $(layer);
            _this.animation.push([layer, _this.get_animation_data('layer', layer, index)]);
            _this.size['layer'].push(_this.get_size_data('layer', layer, index));
          });
        }
        _this.log("UX Parallax data has been processed.");
        _this.log(_this.animation);
      };
    })(this);

    /*
    Initialize element animation data
     */
    this.get_animation_data = (function(_this) {
      return function(context, element, index) {
        var animation, animation_default, animation_object, element_class, element_id;
        animation = {};
        animation_default = "";
        animation_object = null;
        if (element.attr('id') != null) {
          element_id = '#' + element.attr('id');
        } else {
          element_id = "";
        }
        if (element.attr('class') != null) {
          element_class = element.attr('class').split(' ').map(function(item) {
            return "." + item;
          });
        } else {
          element_class = [];
        }
        if (element.attr('data-ux-parallax') != null) {
          animation_default = element.attr('data-ux-parallax');
        } else if (typeof _this.settings.animation === 'string') {
          animation_default = _this.settings.animation;
        } else if (typeof _this.settings.animation === 'object') {
          if (typeof _this.settings.animation[context] === 'string') {
            animation_default = _this.settings.animation[context];
          } else if ($.isArray(_this.settings.animation[context])) {
            if (typeof _this.settings.animation[context][index] === 'string') {
              animation_default = _this.settings.animation[context][index];
            } else if (typeof _this.settings.animation[context][index] === 'object') {
              animation_object = _this.settings.animation[context][index];
              if (typeof _this.settings.animation[context][index]['*'] === 'string') {
                animation_default = _this.settings.animation[context]['*'];
              }
            }
          } else if (typeof _this.settings.animation[context] === 'object') {
            animation_object = _this.settings.animation[context];
            if (typeof _this.settings.animation[context]['*'] === 'string') {
              animation_default = _this.settings.animation[context]['*'];
            } else if (typeof _this.settings.animation[context][element_id] === 'string') {
              animation_default = _this.settings.animation[context][element_id];
            } else if (typeof _this.settings.animation[context][element_id] === 'object') {
              animation_object = _this.settings.animation[context][element_id];
            } else {
              $.each(element_class, function(index, _class) {
                if (typeof _this.settings.animation[context][_class] === 'string') {
                  animation_default = _this.settings.animation[context][_class];
                }
                if (typeof _this.settings.animation[context][_class] === 'object') {
                  animation_object = _this.settings.animation[context][_class];
                }
              });
            }
          } else if (typeof _this.settings.animation['*'] === 'string') {
            animation_default = _this.settings.animation['*'];
          } else {
            animation_default = "";
          }
        }
        $.each(Object.keys(_this.settings.screen), function(responsive_mode_index, responsive_mode) {
          var ref, responsive_animation, scroll_transform, transform, value;
          animation[responsive_mode] = {};
          responsive_animation = {};
          if (animation_object) {
            if (typeof animation_object[responsive_mode] === 'string') {
              responsive_animation = animation_object[responsive_mode];
            } else {
              responsive_animation = animation_default;
            }
          } else {
            responsive_animation = animation_default;
          }
          animation[responsive_mode] = _this.animus.get(responsive_animation);
          ref = animation[responsive_mode].state;
          for (transform in ref) {
            value = ref[transform];
            value = "" + value;
            if (value.indexOf("..") === -1) {
              scroll_transform = value.split(/\s+/);
              if (scroll_transform.length === 0) {
                scroll_transform.push(_this.settings.factor);
              } else if (scroll_transform.length === 1) {
                if (scroll_transform[0] === "") {
                  scroll_transform[1] = "default";
                  scroll_transform[0] = _this.settings.factor;
                }
                if (isNaN(scroll_transform[0])) {
                  scroll_transform[1] = scroll_transform[0];
                  scroll_transform[0] = _this.settings.factor;
                } else {
                  scroll_transform[1] = "default";
                  scroll_transform[0] = parseFloat(scroll_transform[0]);
                }
              }
              animation[responsive_mode].state[transform] = scroll_transform;
            } else {
              scroll_transform = ['*'];
              value = value.split(/[\. ]+/);
              scroll_transform[1] = parseFloat(value[0]);
              scroll_transform[2] = parseFloat(value[1]);
              if (value.length === 2) {
                scroll_transform[3] = "default";
              } else {
                scroll_transform[3] = value[2];
              }
              animation[responsive_mode].state[transform] = scroll_transform;
            }
          }
        });
        return animation;
      };
    })(this);

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
    this.get_size_data = (function(_this) {
      return function(context, object, index) {
        var data, element_class, element_id, js_data;
        data = {};
        if (object.attr('id') != null) {
          element_id = '#' + object.attr('id');
        } else {
          element_id = "";
        }
        if (object.attr('class') != null) {
          element_class = object.attr('class').split(' ').map(function(item) {
            return "." + item;
          });
        } else {
          element_class = [];
        }
        js_data = false;
        if ((_this.settings.canvas != null) && (_this.settings.canvas[context] != null)) {
          if (index != null) {
            if (_this.settings.canvas[context][index] != null) {
              js_data = _this.settings.canvas[context][index];
            } else if (_this.settings.canvas[context][id] != null) {
              js_data = _this.settings.canvas[context][id];
            } else {
              $.each(element_class, function(index, _class) {
                if (_this.settings.canvas[context][_class] != null) {
                  js_data = _this.settings.canvas[context][_class];
                }
              });
            }
          } else {
            js_data = _this.settings.canvas[context];
          }
        }
        if (object.attr('data-ux-parallax-width') != null) {
          data.width = parseFloat(object.attr('data-ux-parallax-width'));
        } else if (js_data && (js_data.width != null)) {
          data.width = parseFloat(_this.delete_property(js_data, 'width'));
        }
        if (object.attr('data-ux-parallax-height') != null) {
          data.height = parseFloat(object.attr('data-ux-parallax-height'));
        } else if (js_data && (js_data.height != null)) {
          data.height = parseFloat(_this.delete_property(js_data, 'height'));
        }
        if (context === 'layer') {
          data.position = {};
          if (object.attr('data-ux-parallax-top') != null) {
            data.position.top = parseFloat(object.attr('data-ux-parallax-top'));
          } else if (js_data && (js_data.top != null)) {
            data.position.top = parseFloat(_this.delete_property(js_data, 'top'));
          } else if (object.attr('data-ux-parallax-bottom') != null) {
            data.position.bottom = parseFloat(object.attr('data-ux-parallax-bottom'));
          } else if (js_data && (js_data.bottom != null)) {
            data.position.bottom = parseFloat(_this.delete_property(js_data, 'bottom'));
          } else {
            data.position.top = 0;
          }
          if (object.attr('data-ux-parallax-left') != null) {
            data.position.left = parseFloat(object.attr('data-ux-parallax-left'));
          } else if (js_data && (js_data.left != null)) {
            data.position.left = parseFloat(_this.delete_property(js_data, 'left'));
          } else if (object.attr('data-ux-parallax-right') != null) {
            data.position.right = parseFloat(object.attr('data-ux-parallax-right'));
          } else if (js_data && (js_data.right != null)) {
            data.position.right = parseFloat(_this.delete_property(js_data, 'right'));
          } else {
            data.position.left = 0;
          }
        }
        return data;
      };
    })(this);

    /*
    Set element and parent sizes
     */
    this.set_size = (function(_this) {
      return function() {
        _this.size.wrapper.width = _this.wrapper.width();
        _this.size.wrapper.height = _this.wrapper.height();
        _this.size.wrapper.half = _this.size.wrapper[_this.param.size] / 2;
        if (_this.fit.length) {
          _this.fit.removeClass('ux-parallax-content');
          _this.source.height(_this.fit.outerHeight(true));
          _this.fit.addClass('ux-parallax-content');
        }
        else if (_this.settings.size === 'fullscreen') {
          _this.source.css({
            'width': _this.size.wrapper.width,
            'height': _this.size.wrapper.height,
            'max-width': _this.size.wrapper.width,
            'max-height': _this.size.wrapper.height
          });
        } else if (_this.settings.size === 'screenHeight') {
          _this.source.height(_this.size.wrapper.height);
        } else if (_this.settings.size === 'screenWidth') {
          _this.source.width(_this.size.wrapper.width);
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
          var offset = ((_this.size.wrapper.height + _this.size.source.height) / 2) - _this.size.element.height;
          if (offset > 0) {
            _this.size.element.width += offset;
            _this.size.element.height += offset;
          }
          if (_this.loaded) {
            _this.animate.set(_this.background, {
              height: _this.size.element.height,
              width: _this.size.element.width
            });
          } else {
            _this.animate.to(_this.background, _this.settings.initialAnimationDuration, {
              height: _this.size.element.height,
              width: _this.size.element.width
            });
          }
        } else {
          _this.size.element.width = _this.element.outerWidth(true);
          _this.size.element.height = _this.element.outerHeight(true);
        }
        if (_this.layers.length > 0) {
          _this.layers.each(function(index, layer) {
            _this.size.layer[index] = $.extend(_this.get_image_size($(layer)), _this.size.layer[index]);
          });
        }
        _this.size.source.same = {
          width: _this.size.source.width === _this.size.element.width,
          height: _this.size.source.height === _this.size.element.height
        };
        _this.size.source.ratio = _this.size.wrapper[_this.param.size] / _this.size.source[_this.param.size];
        _this.size.source.difference = _this.size.wrapper[_this.param.size] - _this.size.source[_this.param.size];
        _this.size.source.spanning = _this.size.wrapper[_this.param.size] + _this.size.source[_this.param.size];
        _this.log("Element sizes have been set.");
      };
    })(this);
    this.setSize = this.set_size;

    /*
    Set element top and bottom positioning on the page
     */
    this.set_position = (function(_this) {
      return function() {
        _this.position.source.top = _this.source.offset().top - _this.document.offset().top;
        _this.position.source.bottom = _this.position.source.top + _this.size.source.height;
        _this.position.source.vmiddle = (_this.position.source.top + _this.position.source.bottom) / 2;
        _this.position.source.left = _this.source.offset().left - _this.document.offset().left;
        _this.position.source.right = _this.position.source.left + _this.size.source.width;
        _this.position.source.hmiddle = (_this.position.source.left + _this.position.source.right) / 2;
        _this.position.element.top = _this.element.offset().top - _this.document.offset().top;
        _this.position.element.bottom = _this.position.element.top + _this.size.element.height;
        _this.position.element.left = _this.element.offset().left - _this.document.offset().left;
        _this.position.element.right = _this.position.element.left + _this.size.element.width;
        if (_this.background.length > 0) {
          _this.position.background = _this.position.element;
        }
        _this.position.parent.top = _this.parent.offset().top - _this.document.offset().top;
        _this.position.parent.bottom = _this.position.parent.top + _this.size.parent.height;
        _this.position.parent.left = _this.parent.offset().left - _this.document.offset().left;
        _this.position.parent.right = _this.position.parent.left + _this.size.parent.width;
        if (_this.settings.normalizeTop && _this.position.source[_this.param.start] < _this.size.wrapper[_this.param.size] / 2 && _this.size.source[_this.param.size] < _this.size.wrapper[_this.param.size]) {
          _this.position.source.beginning = true;
        } else {
          _this.position.source.beginning = false;
        }
        if (_this.background.length > 0 || _this.layers.length > 0) {
          _this.set_canvas_position();
        }
        _this.log("Element positions have been set.");
      };
    })(this);
    this.setPosition = this.set_position;

    /*
    Position background according to anchor settings
     */
    this.set_canvas_position = (function(_this) {
      return function() {
        var height_ratio, margin_left, margin_top, width_ratio;
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
        } else {
          _this.animate.to(_this.background, _this.settings.initialAnimationDuration, {
            'margin-top': margin_top,
            'margin-left': margin_left
          });
        }
        width_ratio = _this.size.element.width / _this.size.background.width;
        height_ratio = _this.size.element.height / _this.size.background.height;
        _this.layers.each(function(index, layer) {
          var layer_css;
          layer = $(layer);
          layer_css = {};
          if ('top' in _this.size.layer[index].position) {
            layer_css.top = height_ratio * _this.size.layer[index].position.top + margin_top;
          } else if ('bottom' in _this.size.layer[index].position) {
            layer_css.bottom = height_ratio * _this.size.layer[index].position.bottom + margin_top;
          }
          if ('left' in _this.size.layer[index].position) {
            layer_css.left = width_ratio * _this.size.layer[index].position.left + margin_left;
          } else if ('right' in _this.size.layer[index].position) {
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
          } else {
            _this.animate.to(layer, _this.settings.initialAnimationDuration, layer_css);
          }
        });
      };
    })(this);

    /*
    Set layer z-index for overlapping
     */
    this.set_layer_index = (function(_this) {
      return function() {
        _this.layers.each(function(index, layer) {
          $(layer).css({
            'z-index': _this.layers.length - index
          });
        });
      };
    })(this);

    /*
    Sets parameters based on orientation settings
     */
    this.set_orientation = (function(_this) {
      return function(orientation) {
        _this.param = {};
        if (orientation === 'vertical') {
          _this.param.size = 'height';
          _this.param.middle = 'vmiddle';
          _this.param.start = 'top';
          _this.param.end = 'bottom';
          _this.param.axis = 'y';
          _this.param.scroll = 'scrollTop';
          _this.param.half = 'halfHeight';
        } else {
          _this.param.size = 'width';
          _this.param.middle = 'hmiddle';
          _this.param.start = 'left';
          _this.param.end = 'right';
          _this.param.axis = 'x';
          _this.param.scroll = 'scrollLeft';
          _this.param.half = 'halfWidth';
        }
      };
    })(this);

    /*
    Set current responsive range parameter as xs, sm, md, lg or xl
     */
    this.set_responsive_context = (function(_this) {
      return function() {
        var current_responsive_size = _this.current_responsive_size;
        if (_this.size.wrapper.width >= _this.settings.screen.xl) {
          _this.current_responsive_size = 'xl';
        } else if (_this.size.wrapper.width >= _this.settings.screen.lg) {
          _this.current_responsive_size = 'lg';
        } else if (_this.size.wrapper.width >= _this.settings.screen.md) {
          _this.current_responsive_size = 'md';
        } else if (_this.size.wrapper.width >= _this.settings.screen.sm) {
          _this.current_responsive_size = 'sm';
        } else {
          _this.current_responsive_size = 'xs';
        }
        if (current_responsive_size && current_responsive_size !== _this.current_responsive_size) {
          _this.log("Responsive context is reset to " + _this.current_responsive_size + ".");
          // Reset image sizes to support responsive images.
          // _this.set_size();
          // _this.set_position();
        }
        else {
          _this.log("Responsive context is " + _this.current_responsive_size + ".");
        }
      };
    })(this);

    /*
    Add dynamic element classes and
     */
    this.set_classes = (function(_this) {
      return function() {
        var ux_parallax_style, style;
        if ($('#ux_parallax-style').length === 0) {
          ux_parallax_style = ".ux-parallax-parent { perspective: " + _this.settings.perspective + "px; -moz-perspective: " + _this.settings.perspective + "px; -webkit-perspective: " + _this.settings.perspective + "px; perspective-origin: " + _this.settings.perspectiveOrigin + "; -moz-perspective-origin: " + _this.settings.perspectiveOrigin + "; -webkit-perspective-origin: " + _this.settings.perspectiveOrigin + "; backface-visibility: hidden; -moz-backface-visibility: hidden; -webkit-backface-visibility: hidden; }";
          style = document.createElement('style');
          style.id = 'ux-parallax-style';
          style.type = 'text/css';
          style.innerHTML = ux_parallax_style;
          $('head')[0].appendChild(style);
        }
        if (_this.settings.animateElement) {
          _this.parent.addClass('ux-parallax-parent');
        } else {
          _this.element.addClass('ux-parallax-parent');
        }
        if (_this.background.length > 0) {
          _this.element.addClass('ux-parallax-canvas');
        }
        _this.element.addClass('ux-parallax-animated');
        _this.background.addClass('ux-parallax-animated');
        _this.layers.addClass('ux-parallax-animated');
      };
    })(this);

    /*
    Binds the wrapper resize event to cache current wrapper
    width and height and to set the layout up
     */
    this.bind_resize = (function(_this) {
      return function() {
        _this.window.resize(function() {
          _this.set_size();
          _this.set_position();
          _this.set_responsive_context();
          _this.parallax();
        });
      };
    })(this);

    /*
    Rendering performance optimization ticking for debouncing purposes
     */
    this.ticking = false;
    this.stop_ticking = (function(_this) {
      return function() {
        _this.ticking = false;
      };
    })(this);
    if (this.settings.optimize) {
      this.start_ticking = (function(_this) {
        return function() {
          _this.ticking = true;
        };
      })(this);
    } else {
      this.start_ticking = (function(_this) {
        return function() {
          _this.ticking = false;
        };
      })(this);
    }

    /*
    Scroll animation handler
     */
    this.in_view_eariler = false;
    this.scroll = (function(_this) {
      return function() {
        var in_view_tolerance, translation;
        if (_this.element[0]._gsTransform && _this.element[0]._gsTransform[_this.param.axis]) {
          translation = _this.element[0]._gsTransform[_this.param.axis];
        } else {
          translation = 0;
        }
        in_view_tolerance = $.ux_parallax.wrapper[_this.param.half] - translation;
        _this.in_view = !($.ux_parallax.wrapper[_this.param.end] - _this.position.source[_this.param.start] + in_view_tolerance < 0 || $.ux_parallax.wrapper[_this.param.start] - _this.position.source[_this.param.end] - in_view_tolerance > 0);
        if (_this.in_view) {
          if (!_this.in_view_eariler) {
            _this.in_view_eariler = true;
            _this.element.addClass('ux-parallax-visible');
            _this.element.removeClass('ux-parallax-hidden');
            _this.element.triggerHandler('ux_parallax.visible', [_this.in_view]);
          }
        } else {
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
      };
    })(this);

    /*
    Bind the wrapper scroll event to fade content on scroll down
     */
    this.bind_scroll = (function(_this) {
      return function() {
        _this.wrapper.on('ux_parallax.scroll', _this.scroll);
      };
    })(this);

    /*
    Set the element id if it doesn't have one or
    get the existing one
     */
    this.set_element_id = (function(_this) {
      return function() {
        if (_this.element.attr('id') != null) {
          _this.id = _this.element.attr('id');
        } else {
          _this.id = _this.get_random_id('ux-parallax');
          _this.element.attr('id', _this.id);
        }
        _this.log("Element id has been set to " + _this.id + ".");
      };
    })(this);

    /*
    Setup Animation Platform
     */
    this.set_animator = (function(_this) {
      return function() {
        _this.animate = window[_this.settings.animate];
        _this.log("Animating using the " + _this.settings.animate + " platform.");
      };
    })(this);

    /*
    Get a random id by concatenating input string
    with a random number
     */
    this.get_random_id = function(string) {
      return string + '-' + Math.floor((Math.random() * 100000) + 1);
    };

    /*
    Get the size of an image element
     */
    this.get_image_size = (function(_this) {
      return function(image) {
        var size;
        size = {};
        size.width = image[0].naturalWidth != null ? image[0].naturalWidth : image[0].width != null ? image[0].width : image.width != null ? image.width() : 'auto';
        size.height = image[0].naturalHeight != null ? image[0].naturalHeight : image[0].height != null ? image[0].height : image.height != null ? image.height() : 'auto';
        return size;
      };
    })(this);

    /*
    Debounce helper to make resize happen every n milliseconds
     */
    this.debounce = function(func, wait, immediate) {
      var timeout;
      timeout = void 0;
      return function() {
        var args, callNow, context, later;
        context = this;
        args = arguments;
        later = function() {
          timeout = null;
          if (!immediate) {
            func.apply(context, args);
          }
        };
        callNow = immediate && !timeout;
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
        if (callNow) {
          func.apply(context, args);
        }
      };
    };

    /*
    Delete an object property and return its value
     */
    this.delete_property = function(object, property) {
      var temporary;
      temporary = object[property];
      delete object[property];
      return temporary;
    };

    /*
    Extend given default settings with user input
     */
    this.extend_settings = (function(_this) {
      return function(id, defaults) {
        if (_this.settings[id] != null) {
          return _this.settings[id] = $.extend({}, defaults, _this.settings[id]);
        } else {
          return _this.settings[id] = defaults;
        }
      };
    })(this);

    /*
    Logger snippet within UX Parallax
     */
    this.log = (function(_this) {
      return function(item) {
        if (!_this.debug) {
          return;
        }
        if (typeof item === 'object') {
          console.log("[UX Parallax " + _this.id + "]", item);
        } else {
          console.log("[UX Parallax " + _this.id + "] " + item);
        }
      };
    })(this);

    /*
    Error logger snippet within UX Parallax
     */
    this.error = (function(_this) {
      return function(item) {
        if (typeof item === 'object') {
          console.error("[UX Parallax " + _this.id + "]", item);
        } else {
          console.error("[UX Parallax " + _this.id + "] " + item);
        }
      };
    })(this);
    this.initialize();
  };
  $.ux_parallax.wrapper = {};
  $.ux_parallax.resize = function() {
    var _wrapper;
    _wrapper = $('#ux-push');
    $.ux_parallax.wrapper.height = _wrapper.height();
    $.ux_parallax.wrapper.width = _wrapper.width();
    $.ux_parallax.wrapper.halfHeight = $.ux_parallax.wrapper.height / 2;
    $.ux_parallax.wrapper.halfWidth = $.ux_parallax.wrapper.width / 2;
  };
  $(window).on('resize', (function(_this) {
    return function() {
      $.ux_parallax.resize();
    };
  })(this));
  $.ux_parallax.resize();
  $.ux_parallax.scroll = function() {
    var _wrapper;
    _wrapper = $('#ux-push');
    $.ux_parallax.wrapper.top = _wrapper.scrollTop();
    $.ux_parallax.wrapper.left = _wrapper.scrollLeft();
    $.ux_parallax.wrapper.bottom = $.ux_parallax.wrapper.top + $.ux_parallax.wrapper.height;
    $.ux_parallax.wrapper.right = $.ux_parallax.wrapper.left + $.ux_parallax.wrapper.width;
    $.ux_parallax.wrapper.vmiddle = $.ux_parallax.wrapper.top + $.ux_parallax.wrapper.halfHeight;
    $.ux_parallax.wrapper.hmiddle = $.ux_parallax.wrapper.left + $.ux_parallax.wrapper.halfWidth;
  };
  $('#ux-push').on('scroll', (function(_this) {
    return function() {
      $.ux_parallax.scroll();
      $('#ux-push').triggerHandler('ux_parallax.scroll');
    };
  })(this));
  $.ux_parallax.scroll();
  return $.fn.ux_parallax = function(opts) {
    return this.each(function(index, element) {
      if (!$.data(element, "ux_parallax")) {
        return $.data(element, "ux_parallax", new $.ux_parallax(element, opts));
      }
    });
  };
})(window.jQuery, window, document);

//# sourceMappingURL=../../maps/ux_parallax/ux_parallax.js.map

