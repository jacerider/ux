"use strict";!function(i,e){var n="uxFormCheckbox";function t(e,t){this.element=e,this._name=n,this._defaults=i.fn.uxFormCheckbox.defaults,this.options=i.extend({},this._defaults,t),this.init()}i.extend(t.prototype,{init:function(){this.buildCache(),this.bindEvents(),this.buildElement()},destroy:function(){this.unbindEvents(),this.$element.removeData()},buildElement:function(){var e,t=this;this.$element.hasClass("form-no-label")&&((e=this.$element.find("label")).removeClass("visually-hidden"),e.html('<span class="visually-hidden">'+e.html()+"</span>")),this.$field.is(":checked")&&this.$element.addClass("active"),setTimeout(function(){t.$element.addClass("ready")})},buildCache:function(){this.$element=i(this.element),this.$field=this.$element.find("input.form-checkbox")},bindEvents:function(){var e=this;e.$field.on("change."+e._name,function(){e.onChange.call(e),e.validate()}).on("focus."+e._name,function(){e.$element.addClass("focused"),e.validate()}).on("blur."+e._name,function(){e.$element.removeClass("focused")})},unbindEvents:function(){this.$field.off("."+this._name)},onChange:function(){this.$field.is(":checked")?this.$element.addClass("active"):this.$element.removeClass("active")},validate:function(){this.$element.removeClass("valid invalid").removeAttr("data-error"),this.isValid()||this.$element.addClass("invalid").attr("data-error",this.$field[0].validationMessage)},isValid:function(){return!0===this.$field[0].validity.valid}}),i.fn.uxFormCheckbox=function(e){return this.each(function(){i.data(this,n)||i.data(this,n,new t(this,e))}),this},i.fn.uxFormCheckbox.defaults={},e.behaviors.uxFormCheckbox={attach:function(e){i(e).find(".ux-form-checkbox").once("ux-form-checkbox").uxFormCheckbox()}}}(jQuery,Drupal,(window,document));