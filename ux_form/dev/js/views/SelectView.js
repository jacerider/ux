/**
 * @file
 * A Backbone view for the body element.
 */

(function ($, Drupal, Backbone) {

  'use strict';

  Drupal.UxForm.SelectView = Backbone.View.extend(/** @lends Drupal.UxForm.SelectView# */{

    $select: null,
    $options: null,
    $newSelect: null,

    events: {
      'click li:not(.optgroup)': 'onOptionClick',
      'focus input.select-dropdown': 'onInputFocus',
      'click input.select-dropdown': 'onInputClick',
      'blur input.select-dropdown': 'onInputBlur'
    },

    initialize: function () {
      var _this = this;
      var multiple = _this.model.get('multiple');
      var uniqueID = _this.model.cid;

      _this.$select = _this.$el.find('select');
      _this.$options = $('<ul id="select-options-' + uniqueID +'" class="dropdown-content select-dropdown ' + (multiple ? 'multiple-select-dropdown' : '') + '"></ul>');

      /* Create dropdown structure. */
      var $selectChildren = _this.model.get('$selectChildren');
      if ($selectChildren.length) {
        $selectChildren.each(function() {
          if ($(this).is('option')) {
            // Direct descendant option.
            if (multiple) {
              _this.$options.append(_this.formatOption($(this), 'multiple'));

            } else {
              _this.$options.append(_this.formatOption($(this)));
            }
          }
          else if ($(this).is('optgroup')) {
            var selectOptions = $(this).children('option');
            _this.$options.append($('<li class="optgroup"><span>' + $(this).attr('label') + '</span></li>'));
            selectOptions.each(function() {
              _this.$options.append(_this.formatOption($(this), 'optgroup-option'));
            });
          }
        });
      }

      _this.$newSelect = $('<input type="text" class="select-dropdown" readonly="true" ' + (_this.model.get('disabled') ? 'disabled' : '') + ' data-activates="select-options-' + uniqueID +'" value="'+ _this.model.get('sanitizedLabelHtml') +'"/>');
      var dropdownIcon = $('<span class="caret">&#9660;</span>');
      if (_this.model.get('disabled')) {
        dropdownIcon.addClass('disabled');
      }
      _this.$select.before(_this.$newSelect);
      _this.$newSelect.before(dropdownIcon);
      _this.$newSelect.after(_this.$options);

      // Check if section element is disabled
      if (!_this.$select.is(':disabled')) {
        _this.$newSelect.uxFormDropdown({'hover': false, 'closeOnClick': false});
      }

      // Copy tabindex
      if (_this.$select.attr('tabindex')) {
        $(_this.$newSelect[0]).attr('tabindex', _this.$select.attr('tabindex'));
      }

      _this.$select.addClass('initialized');

      $(window).on('click.' + _this.cid, function (e) {
        _this.onWindowClick(e);
      });

      _this.render();
    },

    onWindowClick: function (e) {
      console.log('hit', e);
    },

    formatOption: function ($option, type) {
      var $element;
      var disabledClass = ($option.is(':disabled')) ? 'disabled ' : '';
      var optgroupClass = (type === 'optgroup-option') ? 'optgroup-option ' : '';
      // Check for multiple type.
      if (type === 'multiple') {
        $element = $('<li class="' + disabledClass + '"><span><input type="checkbox"' + disabledClass + '/><label></label>' + $option.html() + '</span></li>');
      } else {
        $element = $('<li class="' + disabledClass + optgroupClass + '"><span>' + $option.html() + '</span></li>');
      }
      return $element;
    },

    onOptionClick: function (e) {
      var _this = this;
      var $element = $(e.currentTarget);
      if (!$element.hasClass('disabled') && !$element.hasClass('optgroup')) {
        var selected = true;

        if (_this.model.get('multiple')) {
          $('input[type="checkbox"]', this).prop('checked', function(i, v) { return !v; });
          selected = _this.toggleEntryFromArray(_this.model.get('valuesSelected'), $element.index(), _this.$select);
          _this.$newSelect.trigger('focus');
        }
        else {
          _this.$options.find('li').removeClass('active');
          $element.toggleClass('active');
          _this.$newSelect.val($element.text());
        }

        _this.activateOption(_this.$options, $element);
        _this.$select.find('option').eq(0).prop('selected', selected);
        // Trigger onchange() event
        _this.$select.trigger('change');
      }
      e.stopPropagation();
    },

    onInputFocus: function (e) {
      var _this = this;
      var $element = $(e.currentTarget);
      if ($('ul.select-dropdown').not(_this.$options[0]).is(':visible')) {
        $('input.select-dropdown').trigger('close');
      }
      if (!_this.$options.is(':visible')) {
        $element.trigger('open', ['focus']);
        var label = $element.val();
        var selectedOption = _this.$options.find('li').filter(function() {
          return $(this).text().toLowerCase() === label.toLowerCase();
        })[0];
        _this.activateOption(_this.$options, selectedOption);
      }
    },

    onInputClick: function (e) {
      e.stopPropagation();
    },

    onInputBlur: function (e) {
      var _this = this;
      var $element = $(e.currentTarget);
      if (!_this.model.get('multiple')) {
        $element.trigger('close');
      }
      _this.$options.find('li.selected').removeClass('selected');
    },

    activateOption: function(collection, newOption) {
      var _this = this;
      if (newOption) {
        collection.find('li.selected').removeClass('selected');
        var option = $(newOption);
        option.addClass('selected');
        _this.$options.scrollTo(option);
      }
    },

    toggleEntryFromArray: function (entriesArray, entryIndex, select) {
      var _this = this;
      var index = entriesArray.indexOf(entryIndex),
          notAdded = index === -1;

      if (notAdded) {
        entriesArray.push(entryIndex);
      } else {
        entriesArray.splice(index, 1);
      }

      select.siblings('ul.dropdown-content').find('li').eq(entryIndex).toggleClass('active');

      // use notAdded instead of true (to detect if the option is selected or not)
      select.find('option').eq(entryIndex).prop('selected', notAdded);
      _this.setValueToInput(entriesArray, select);

      return notAdded;
    },

    setValueToInput: function (entriesArray, select) {
      var value = '';
      for (var i = 0, count = entriesArray.length; i < count; i++) {
        var text = select.find('option').eq(entriesArray[i]).text();
        i === 0 ? value += text : value += ', ' + text;
      }
      if (value === '') {
        value = select.find('option:disabled').eq(0).text();
      }
      select.siblings('input.select-dropdown').val(value);
    },

    render: function () {
      var _this = this;
      return _this;
    },

    remove: function () {
      var _this = this;
      $(window).off('click.' + _this.cid);
    }
  });

  // Drupal.UxForm.views.inputView = new Drupal.UxForm.SelectView({
  //   tagName: '.ux-form-input',
  //   model: Drupal.UxForm.models.elementModel
  // });

}(jQuery, Drupal, Backbone));
