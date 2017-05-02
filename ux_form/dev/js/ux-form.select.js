
(function ($, Drupal, window, document) {

  'use strict';

  var pluginName = 'uxFormSelect';

  function Plugin(element, options) {
    this.element = element;
    this.$element = $(this.element);

    if (this.isSupported()) {
      this._name = pluginName;
      this._defaults = $.fn.uxFormSelect.defaults;
      this.options = $.extend({}, this._defaults, options);
      this.uniqueId = Drupal.Ux.guid();
      this.init();
    }
    else {
      this.$element.addClass('invalid');
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
      this.$field = this.$element.find('select');
      this.$wrapper = $('<div class="ux-form-select-wrapper ux-form-input"></div>');
      this.$caret = $('<span class="ux-form-select-caret">&#9660;</span>');
      this.$trigger = $('<input class="ux-form-input-item" readonly="true"></input>');
      this.$hidden = $('<input class="ux-form-select-hidden"></input>');
      this.$dropdown = $('<ul class="ux-form-select-dropdown"></ul>');
      this.multiple = (this.$field.attr('multiple')) ? true : false;
      this.placeholder = this.$field.attr('placeholder') || (this.multiple ? 'Select Multiple' : 'Select One');

      this.$trigger.addClass('ux-form-select-trigger');
      this.$trigger.attr('placeholder', this.placeholder);
      this.$wrapper.insertAfter(this.$field);
      this.$wrapper.append(this.$caret).append(this.$hidden).append(this.$trigger).append(this.$dropdown).append(this.$field);
      this.$dropdown.addClass((this.multiple ? 'is-multiple' : 'is-single'));
    },

    /*
    Process fields.
     */
    buildElement: function () {
      var _this = this;
      this.loadOptionsFromSelect();
      this.updateTrigger();
      Drupal.attachBehaviors(this.$element[0]);

      if (this.options.debug) {
        this.$field.show();
        setTimeout(function () {
          _this.$trigger.trigger('tap');
        }, 500);
      }

      // Copy tabindex
      if (this.$field.attr('tabindex')) {
        this.$trigger.attr('tabindex', this.$field.attr('tabindex'));
      }

      setTimeout(function () {
        _this.$element.addClass('ready');
      });
    },

    /*
    Bind events that trigger methods.
    */
    bindEvents: function () {
      var _this = this;
      // _this.$trigger.on('tap' + '.' + _this._name, function (e) {
      //   _this.populateDropdown.call(_this);
      //   _this.showDropdown.call(_this);
      // });
      // _this.$trigger.on('tapstart' + '.' + _this._name, function (e) {
      //   // e.preventDefault();
      //   // e.stopPropagation();
      //   // _this.$trigger.blur();
      // }).on('tap' + '.' + _this._name, function (e, touch) {
      //   e.preventDefault();
      //   e.stopPropagation();
      //   // $(document).trigger('tap');
      //   // _this.$trigger.blur();
      //   // _this.$trigger.focus();
      //   _this.populateDropdown.call(_this);
      //   _this.showDropdown.call(_this);
      //   // _this.$trigger.focus();
      // }).on('focus' + '.' + _this._name, function (e) {
      //   clearTimeout(_this.timeout);
      //   console.log('focus');
      // }).on('blur' + '.' + _this._name, function (e) {
      //   console.log('blur');
      //   _this.timeout = setTimeout(function() {
      //     console.log('blur trigger');
      //   }, 101);
      // });
      // _this.$dropdown.on('tap' + '.' + _this._name, '.selector', function (e) {
      //   _this.onItemTap.call(_this, e);
      //   setTimeout(function () {

      //     // clearTimeout(_this.timeout);
      //     _this.$trigger.focus();
      //   }, 10);
      // });
      // _this.$dropdown.on('blur' + '.' + _this._name, function (e) {
      //   console.log('BLUR DROPDOWN');
      // });
      // _this.$dropdown.on('keydown' + '.' + _this._name, function (e) {
      //   console.log('ugh', e.which);

      //   // TAB - switch to another input
      //   if (e.which === 9) {
      //     _this.closeDropdown.call(_this, e);
      //     return;
      //   }
      // });
      // _this.$dropdown.on('keyup' + '.' + _this._name, '.search-input', function (e) {
      //   _this.onSearch.call(_this, e);
      // });
      // _this.$dropdown.on('tap' + '.' + _this._name, '.close', function (e) {
      //   _this.closeDropdown.call(_this, e);
      // });
      // _this.$hidden.on('focus' + '.' + _this._name, function (e) {
      //   e.preventDefault();
      //   e.stopPropagation();
      //   // _this.$trigger.trigger('tap');
      //   _this.$trigger.focus();
      //   _this.populateDropdown.call(_this);
      //   _this.showDropdown.call(_this);
      //   // _this.populateDropdown.call(_this);
      //   // _this.showDropdown.call(_this);
      // });

      _this.$trigger.on('tap', function (e) {
        _this.populateDropdown.call(_this);
        _this.showDropdown.call(_this);
      });
      _this.$dropdown.on('tap' + '.' + _this._name, '.selector', function (e) {
        _this.onItemTap.call(_this, e);
        _this.$trigger.focus();
      });
      _this.$dropdown.on('tap' + '.' + _this._name, '.close', function (e) {
        _this.closeDropdown.call(_this, e);
      });
      _this.$hidden.on('focus', function (e) {
        _this.populateDropdown.call(_this);
        _this.showDropdown.call(_this);
        _this.$trigger.focus();
      });
      _this.$element.on('focusout', function (e) {
        // if ($(e.target).closest(_this.$dropdown).length) {
        //   _this.closeDropdown.call(_this);
        // }
        // var target = $(e.target);
        // console.log('focusout', target);
        // console.log('find', _this.$element.find(e.target).length);
        // if ($(e.target, _this.$element).length === 0) {
        //   _this.closeDropdown.call(_this);
        // }
      });


      // this.$field.on('change input',function(e,internal){
      //   console.log('WTF');
      //   // if (internal) return;
      //   // return load(function(){
      //   //   return this.$dropdown.find('.search-input').this.$trigger('keyup');
      //   // });
      // });
    },

    /*
    Unbind events that trigger methods.
    */
    unbindEvents: function () {
      this.$element.off('.' + this._name);
      this.$dropdown.off('.' + this._name);
      $(document).off('.' + this._name);
    },

    /*
    Click event of inidividual dropdown item.
     */
    onItemTap: function (e) {
      var $item = $(e.currentTarget);
      var option = $item.data('option');
      var action;

      if (!this.multiple) {
        this.changeSelected(option, 'add');
        return this.closeDropdown();
      }

      this.$dropdown.find('.selector.selected').removeClass('selected');
      if ($item.is('.active')) {
        action = 'remove';
        $item.removeClass('active');
        $item.find('input').prop('checked', false).trigger('change');
      }
      else {
        action = 'add';
        $item.addClass('active');
        $item.find('input').prop('checked', true).trigger('change');
        $item.addClass('selected');
      }
      return this.changeSelected(option, action);
    },

    /*
    Click event of inidividual dropdown item.
     */
    onSearch: function (e) {
      var $item = $(e.currentTarget);
      var search = $item.val().toLowerCase();
      if (search) {
        this.$dropdown.find('.selector').each(function () {
          var text = $(this).data('option').text.toLowerCase();
          if (text.indexOf(search) >= 0) {
            $(this).show();
          }
          else {
            $(this).hide();
          }
        });
      }
      else {
        this.$dropdown.find('.selector').show();
      }
    },

    /*
    Reset and repopulate all dropdown options.
     */
    populateDropdown: function () {
      this.$dropdown.find('li').remove();

      if (this.$dropdown.children().length === 0) {
        this.$dropdown
          .append('<li class="close">&times;</li>')
          .append('<li class="search"><input type="text" class="ux-form-input-item simple search-input" tabindex="-1"></input></li>')
          .find('.search-input').attr('placeholder', this.placeholder);
      }
      if (this.$trigger.val()) {
        this.$dropdown.find('.search-input').attr('placeholder', this.$trigger.val());
      }
      else {
        this.$dropdown.find('.search-input').attr('placeholder', this.placeholder);
      }
      var options = this.getAllOptions();
      for (var i = 0; i < options.length; i++) {
        var option = options[i];

        var li = $('<li></li>');

        if (option.group) {
          li.addClass('optgroup');
          li.html('<span>' + option.text + '</span>');
        }
        else if (this.multiple) {
          li.addClass('selector ux-select-checkbox ready');
          li.html('<span><input type="checkbox" class="form-checkbox"><label class="option">' + option.text + '</label></span>');
        }
        else {
          li.addClass('selector');
          li.html('<span>' + option.text + '</span>');
        }

        if (option.selected) {
          li.addClass('active');
          li.find('input').prop('checked', true);
        }

        li.data('option', option);
        this.$dropdown.append(li);
      }

      this.$dropdown.find('.selector.active:eq(0)').addClass('selected');
      Drupal.attachBehaviors(this.$dropdown[0]);
    },

    /*
    Store all items of a select.
     */
    loadOptionsFromSelect: function () {
      var _this = this;
      this.selected = [];
      this.$field.find('option, optgroup').each(function () {
        var values = {
          value: '',
          text: '',
          selected: false,
          group: false
        };
        if ($(this).is('optgroup')) {
          values.text = $(this).attr('label');
          values.group = true;
        }
        else {
          values.value = $(this).attr('value');
          values.text = $(this).html();
          values.selected = $(this).is(':selected');
        }
        _this.selected.push(values);
      });
    },

    getAllOptions: function (field) {
      if (!field) {
        return this.selected;
      }
      var vals = [];
      for (var i = 0; i < this.selected.length; i++) {
        vals.push(this.selected[i][field]);
      }
      return vals;
    },

    updateTrigger: function () {
      this.$trigger.val(this.htmlDecode(this.getSelectedOptions('text').join(', ')));
    },

    updateSearch: function () {
      this.$dropdown.find('.search-input').attr('placeholder', this.getSelectedOptions('text').join(', '));
    },

    getSelectedOptions: function (field) {
      var vals = [];
      for (var i = 0; i < this.selected.length; i++) {
        if (this.selected[i].selected) {
          if (field) {
            vals.push(this.selected[i][field]);
          }
          else {
            vals.push(this.selected[i]);
          }
        }
      }
      return vals;
    },

    changeSelected: function (option, action) {
      var found = false;
      for (var i = 0; i < this.selected.length; i++) {
        if (!this.multiple) {
          this.selected[i].selected = false;
        }
        if (this.selected[i].value === option.value) {
          found = true;
          if (action === 'add') {
            this.selected[i].selected = true;
          }
          else if (action === 'remove') {
            this.selected[i].selected = false;
          }
        }
      }

      this.updateTrigger();
      if (this.multiple) {
        this.updateSearch();
      }
      this.updateSelect((!found) ? option : null);
    },

    updateSelect: function (newOption) {
      if (newOption) {
        var option = $('<option></option>')
          .attr('value', newOption.value)
          .html(newOption.text);
        this.$field.append(option);
      }

      this.$field.val(this.getSelectedOptions('value'));
      this.$field.trigger('change', [true]);
      this.$field.trigger('input', [true]);
    },

    showDropdown: function () {
      var _this = this;
      $(document).trigger('tap');
      if (this.open) {
        return this.closeDropdown();
      }
      this.open = true;

      this.$element.addClass('active');
      setTimeout(function () {
        _this.$element.addClass('animate');
        _this.$dropdown.focus();
      }, 50);
      _this.$hidden.attr('readonly', true);
      this.windowHideDropdown();
    },

    windowHideDropdown: function () {
      var _this = this;
      $(document).on('tap' + '.' + _this.uniqueId, function (e) {
        if (!_this.open) {
          return;
        }
        if ($(e.target).closest(_this.$dropdown).length) {
          return;
        }
        _this.closeDropdown();
      });
    },

    closeDropdown: function () {
      var _this = this;
      this.open = false;
      this.$dropdown.find('.search-input').val('');
      this.$element.removeClass('animate');
      $(document).off('.' + _this.uniqueId);
      setTimeout(function () {
        if (_this.open === false) {
          _this.$element.removeClass('active');
        }
      }, 350);
      _this.$hidden.attr('readonly', false);
    },

    isSupported: function () {
      if (window.navigator.appName === 'Microsoft Internet Explorer') {
        return document.documentMode >= 8;
      }
      if (/iP(od|hone)/i.test(window.navigator.userAgent) || /IEMobile/i.test(window.navigator.userAgent) || /Windows Phone/i.test(window.navigator.userAgent) || /BlackBerry/i.test(window.navigator.userAgent) || /BB10/i.test(window.navigator.userAgent) || /Android.*Mobile/i.test(window.navigator.userAgent)) {
        return false;
      }
      return true;
    },

    htmlDecode: function (value) {
      return $('<div/>').html(value).text();
    }

  });

  $.fn.uxFormSelect = function (options) {
    this.each(function () {
      var $element = $(this);
      if ($element.hasClass('browser-default') || $element.find('select').hasClass('browser-default')) {
        return; // Continue to next (return false breaks out of entire loop)
      }
      if (!$.data(this, pluginName)) {
        $.data(this, pluginName, new Plugin(this, options));
      }
    });
    return this;
  };

  $.fn.uxFormSelect.defaults = {
    debug: false
  };

  Drupal.behaviors.uxFormSelect = {
    attach: function (context) {
      var $context = $(context);
      $context.find('.ux-form-select').once('ux-form-select').uxFormSelect();
    },
    detach: function (context) {
      $(context).find('.ux-form-select').each(function () {
        var plugin = $(this).data('uxFormSelect');
        if (plugin) {
          plugin.destroy();
        }
      });
    }
  };

})(jQuery, Drupal, window, document);
