
(function ($, Drupal, window, document) {

  'use strict';

  var pluginName = 'uxFormSelect';

  function Plugin(element, options) {
    this.element = element;
    this._name = pluginName;
    this._defaults = $.fn.uxFormSelect.defaults;
    this.options = $.extend({}, this._defaults, options);
    this.uniqueId = Drupal.Ux.guid();
    this.init();
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
      this.$field = this.$element.find('select');
      this.multiple = this.$field.attr('multiple') ? true : false;
      this.$wrapper = $('<div class="ux-form-select-wrapper"></div>');
      this.$dropdownIcon = $('<span class="ux-form-select-caret">&#9660;</span>');
      this.$options = $('<ul id="ux-form-select-options-' + this.uniqueId + '" class="ux-form-dropdown ' + (this.multiple ? 'ux-form-checkboxes' : '') + '"></ul>');
      this.selectChildren = this.$field.children('option, optgroup');
      this.valuesSelected = [];
      this.optionsHover = false;
      this.label = this.$field.find('option:selected').html() || '';
      if (!this.label && !this.multiple) {
        this.label = this.$field.find('option:first').html() || '';
      }
      this.sanitizedLabelHtml = this.label.replace(/"/g, '&quot;');
      this.$newSelect = $('<input type="text" class="ux-form-select-input" readonly="true" ' + ((this.$field.is(':disabled')) ? 'disabled' : '') + ' data-activates="ux-form-select-options-' + this.uniqueId + '" value="' + this.sanitizedLabelHtml + '"/>');
      this.filterQuery = [];
      this.filterTimeout = null;
    },

    /*
    Process fields.
     */
    buildElement: function () {
      var _this = this;
      this.$field.data('select-id', this.uniqueId);
      this.$wrapper.addClass(this.$field.attr('class'));

      this.buildOptions();


      this.$field.wrap(this.$wrapper);

      if (this.$field.is(':disabled')) {
        this.$dropdownIcon.addClass('disabled');
      }

      this.$field.before(this.$newSelect);
      this.$newSelect.before(this.$dropdownIcon);
      this.$newSelect.after(this.$options);

      // Use Ux Form Dropdown.
      if (!this.$field.is(':disabled')) {
        this.$newSelect.uxFormDropdown({hover: false, closeOnClick: false});
      }

      // Copy tabindex
      if (this.$field.attr('tabindex')) {
        $(this.$newSelect[0]).attr('tabindex', this.$field.attr('tabindex'));
      }

      // Add initial multiple selections.
      if (this.multiple) {
        this.$field.find('option:selected:not(:disabled)').each(function () {
          var index = $(this).index();
          _this.toggleEntryFromArray(_this.valuesSelected, index, _this.$field);
          _this.$options.find('li').eq(index).find(':checkbox').prop('checked', true);
        });
      }

      this.$element.addClass('ux-form-input').uxFormInput();
      Drupal.attachBehaviors(this.$element[0]);

      setTimeout(function () {
        _this.$element.addClass('ready');
      });
    },

    buildOptions: function () {
      var _this = this;
      if (this.selectChildren.length) {
        this.selectChildren.each(function () {
          if ($(this).is('option')) {
            // Direct descendant option.
            if (_this.multiple) {
              _this.appendOption($(this), 'multiple');

            }
            else {
              _this.appendOption($(this));
            }
          }
          else if ($(this).is('optgroup')) {
            // Optgroup.
            var selectOptions = $(this).children('option');
            _this.$options.append($('<li class="optgroup"><span>' + $(this).attr('label') + '</span></li>'));
            selectOptions.each(function () {
              _this.appendOption($(this), 'optgroup-option');
            });
          }
        });
      }
    },

    appendOption: function (option, type) {
      var disabledClass = (option.is(':disabled')) ? 'disabled ' : '';
      var optgroupClass = (type === 'optgroup-option') ? 'optgroup-option ' : '';

      if (type === 'multiple') {
        this.$options.append($('<li class="' + disabledClass + 'ux-form-checkbox"><span><input type="checkbox"' + disabledClass + '/><label>' + option.html() + '</label></span></li>'));
      }
      else {
        this.$options.append($('<li class="' + disabledClass + optgroupClass + '"><span>' + option.html() + '</span></li>'));
      }
    },

    /*
    Bind events that trigger methods.
    */
    bindEvents: function () {
      var _this = this;
      this.$options.find('li:not(.optgroup)').each(function (i) {
        $(this).on('click' + '.' + _this._name, function (e) {
          _this.onOptionClick.call(_this, e, i);
        });
      });
      _this.$newSelect.on('click' + '.' + _this._name, function (e) {
        _this.onNewSelectClick.call(_this, e);
      }).on('focus' + '.' + _this._name, function (e) {
        _this.onNewSelectFocus.call(_this, e);
      }).on('blur' + '.' + _this._name, function (e) {
        _this.onNewSelectBlur.call(_this, e);
      }).on('keydown' + '.' + _this._name, function (e) {
        _this.onNewSelectKeydown.call(_this, e);
      });
      _this.$options.on('mouseenter' + '.' + _this._name, function () {
        _this.optionsHover = true;
      });
      _this.$options.on('mouseleave' + '.' + _this._name, function () {
        _this.optionsHover = false;
      });
      $(window).on('click' + '.' + _this._name, function () {
        if (_this.multiple && !_this.optionsHover && _this.$options.is(':visible')) {
          _this.$newSelect.trigger('close');
        }
      });
    },

    /*
    Unbind events that trigger methods.
    */
    unbindEvents: function () {
      this.$options.find('li:not(.optgroup)').off('.' + this._name);
      this.$newSelect.off('.' + this._name);
      this.$options.off('.' + this._name);
      $(window).off('.' + this._name);
    },

    /*
    On option click event callback.
     */
    onOptionClick: function (e, i) {
      var option = e.currentTarget;
      var $option = $(option);
      if (!$option.hasClass('disabled') && !$option.hasClass('optgroup')) {
        var selected = true;

        if (this.multiple) {
          $('input[type="checkbox"]', option).prop('checked', function (i, v) { return !v; });
          selected = this.toggleEntryFromArray(this.valuesSelected, i, this.$field);
          this.$newSelect.trigger('focus');
        }
        else {
          this.$options.find('li').removeClass('active');
          $option.toggleClass('active');
          this.$newSelect.val($option.text());
        }

        this.activateOption(this.$options, $option);
        this.$field.find('option').eq(i).prop('selected', selected);
        this.$field.trigger('change');
      }
    },

    onNewSelectClick: function (e) {
      e.stopPropagation();
    },

    onNewSelectFocus: function (e) {
      $('ul.ux-form-dropdown:visible').not(this.$options[0]).each(function () {
        $(this).siblings('input.ux-form-select-input').trigger('close');
      });
      if (!this.$options.is(':visible')) {
        this.$newSelect.trigger('open', ['focus']);
        var label = this.$newSelect.val();
        var selectedOption = this.$options.find('li').filter(function () {
          return $(this).text().toLowerCase() === label.toLowerCase();
        })[0];
        this.activateOption(this.$options, selectedOption);
      }
    },

    onNewSelectBlur: function (e) {
      if (!this.multiple) {
        this.$newSelect.trigger('close');
      }
      this.$options.find('li.selected').removeClass('selected');
    },

    onNewSelectKeydown: function (e) {
      var _this = this;
      var newOption;

      // TAB - switch to another input
      if (e.which === 9) {
        this.$newSelect.trigger('close');
        return;
      }

      // ARROW DOWN WHEN SELECT IS CLOSED - open select this.$options
      if (e.which === 40 && !this.$options.is(':visible')) {
        this.$newSelect.trigger('open');
        return;
      }

      // ENTER WHEN SELECT IS CLOSED - submit form
      if (e.which === 13 && !this.$options.is(':visible')) {
        return;
      }

      e.preventDefault();

      // ESC - close this.$options
      if (e.which === 27) {
        this.$newSelect.trigger('close');
      }

      // When user types letters.
      var letter = String.fromCharCode(e.which).toLowerCase();
      var nonLetters = [9, 13, 27, 38, 40];
      if (letter && (nonLetters.indexOf(e.which) === -1)) {
        this.filterQuery.push(letter);
        var string = this.filterQuery.join('');
        newOption = this.$options.find('li').filter(function () {
          return $(this).text().toLowerCase().indexOf(string) === 0;
        })[0];

        if (newOption) {
          this.activateOption(this.$options, newOption);
        }
      }

      // ENTER - select option and close when select this.$options are opened
      if (e.which === 13) {
        var activeOption = this.$options.find('li.selected:not(.disabled)')[0];
        if (activeOption) {
          this.optionsHover = true;
          $(activeOption).trigger('click');
          if (!this.multiple) {
            this.$newSelect.trigger('close');
          }
          this.optionsHover = false;
        }
      }

      // ARROW DOWN - move to next not disabled option
      if (e.which === 40) {
        if (this.$options.find('li.selected').length) {
          newOption = this.$options.find('li.selected').next('li:not(.disabled)')[0];
        }
        else {
          newOption = this.$options.find('li:not(.disabled)')[0];
        }
        this.activateOption(this.$options, newOption);
      }

      // ARROW UP - move to previous not disabled option
      if (e.which === 38) {
        newOption = this.$options.find('li.selected').prev('li:not(.disabled)')[0];
        if (newOption) {
          this.activateOption(this.$options, newOption);
        }
      }

      clearTimeout(this.filterTimeout);
      this.filterTimeout = setTimeout(function () {
        _this.filterQuery = [];
      }, 1000);
    },

    activateOption: function (collection, newOption) {
      if (newOption) {
        collection.find('li.selected').removeClass('selected');
        var option = $(newOption);
        option.addClass('selected');
        this.$options.scrollTo(option);
      }
    },

    toggleEntryFromArray: function (entriesArray, entryIndex, select) {
      var index = entriesArray.indexOf(entryIndex);
      var notAdded = index === -1;

      if (notAdded) {
        entriesArray.push(entryIndex);
      }
      else {
        entriesArray.splice(index, 1);
      }

      select.siblings('ul.ux-form-dropdown').find('li').eq(entryIndex).toggleClass('active');

      // use notAdded instead of true (to detect if the option is selected or not)
      select.find('option').eq(entryIndex).prop('selected', notAdded);
      this.setValueToInput(entriesArray, select);

      return notAdded;
    },

    setValueToInput: function (entriesArray, select) {
      var value = '';
      for (var i = 0, count = entriesArray.length; i < count; i++) {
        var text = select.find('option').eq(entriesArray[i]).text();
        if (i === 0) {
          value += text;
        }
        else {
          value += ', ' + text;
        }
      }
      if (value === '') {
        value = select.find('option:disabled').eq(0).text();
      }
      select.siblings('input.ux-form-select-input').val(value).trigger('change');
    }

  });

  $.fn.uxFormSelect = function (options) {
    this.each(function () {
      if (!$.data(this, pluginName)) {
        $.data(this, pluginName, new Plugin(this, options));
      }
    });
    return this;
  };

  $.fn.uxFormSelect.defaults = {};

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
