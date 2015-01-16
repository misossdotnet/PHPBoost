// This contains all the HTML forms contained in the page
function HTMLForms(){}
HTMLForms.forms = new Array();

HTMLForms.add = function(form) {
	return HTMLForms.forms.push(form);
};
HTMLForms.get = function(id) {
	var form = null;
	jQuery.each(HTMLForms.forms, function(index, aForm) {
		if (aForm.getId() == id) {
			form = aForm;
			return false;
		}
	});
	return form;
};
HTMLForms.has = function(id) {
	return HTMLForms.get(id) != null;
};
HTMLForms.getFieldset = function(id) {
	var fieldset = null;
	jQuery.each(HTMLForms.forms, function(index, form) {
		var aFieldset = form.getFieldset(id);
		if (aFieldset != null) {
			fieldset = aFieldset;
			return false;
		}
	});
	return fieldset;
};
HTMLForms.getField = function(id) {
	var field = null;
	jQuery.each(HTMLForms.forms, function(index, form) {
		var aField = form.getField(id);
		if (aField != null) {
			field = aField;
			return false;
		}
	});
	return field;
};

// Shortcuts
var $HF = HTMLForms.get;
var $FFS = HTMLForms.getFieldset;
var $FF = HTMLForms.getField;

// This represents a HTML form.
var HTMLForm = function(id){
	this.id = id;
	this.fieldsets = new Array();
};

HTMLForm.prototype = {
	getId : function() {
		return this.id;
	},
	addFieldset : function(fieldset) {
		this.fieldsets.push(fieldset);
		fieldset.setFormId(this.id);
	},
	getFieldset : function(id) {
		var fieldset = null;
		jQuery.each(this.fieldsets, function(index, aFieldset) {
			if (aFieldset.getId() == id) {
				fieldset = aFieldset;
				return false;
			}
		});
		return fieldset;
	},
	getFieldsets : function() {
		return this.fieldsets;
	},
	hasFieldset : function(id) {
		var hasFieldset = false;
		jQuery.each(this.fieldsets, function(index, aFieldset) {
			if (aFieldset.getId() == id) {
				hasFieldset = true;
				return false;
			}
		});
		return hasFieldset;
	},
	getFields : function() {
		var fields = new Array();
		jQuery.each(this.fieldsets, function(index, fieldset) {
			jQuery.each(fieldset.getFields(), function(index, field) {
				fields.push(field);
			});
		});
		return fields;
	},
	getField : function(id) {
		var field = null;
		jQuery.each(this.getFields(), function(index, aField) {
			if (aField.getId() == id) {
				field = aField;
				return false;
			}
		});
		return field;
	},
	validate : function() {
		var validated = true;
		var validation = '';
		var form = this;
		jQuery.each(this.getFields(), function(index, field) {
			var field_validation = field.validate();
			
			if (field_validation != "") {
				validation = validation + '\n\n' + field_validation;
				validated = false;
			}
		});
		
		if (validated == false) {
			form.displayValidationError(validation);
			jQuery('html, body').animate({scrollTop:jQuery('#' + this.id).offset().top}, 'slow');
		}
		
		this.registerDisabledFields();
		return validated;
	},
	displayValidationError : function(message) {
		message = message.replace(/&quot;/g, '"');
		message = message.replace(/&amp;/g, '&');
		alert(message);
	},
	registerDisabledFields : function() {
		var disabledFields = "";
		jQuery.each(this.getFields(), function(index, field) {
			if (field.isDisabled()) {
				disabledFields += "|" + field.getId();
			}
		});
		jQuery('#' + this.id + '_disabled_fields').value = disabledFields;

		var disabledFieldsets = "";
		jQuery.each(this.getFieldsets(), function(index, fieldset) {
			if (fieldset.isDisabled()) {
				disabledFieldsets += "|" + fieldset.getId();
			}
		});
		jQuery('#' + this.id + '_disabled_fieldsets').value = disabledFieldsets;
	}
};


// This represents a fieldset
var FormFieldset = function(id){
	this.id = id;
	this.fields = new Array();
	this.disabled = false;
	this.formId = "";
};

FormFieldset.prototype = {
	getId : function() {
		return this.id;
	},
	getHTMLId : function() {
		return this.formId + '_' + this.id;
	},
	setFormId : function(formId) {
		this.formId = formId;
	},
	addField : function(field) {
		this.fields.push(field);
		field.setFormId(this.formId);
	},
	getField : function(id) {
		var field = null;
		jQuery.each(this.fields, function(index, aField) {
			if (aField.getId() == id) {
				field = aField;
				return false;
			}
		});
		return field;
	},
	getFields : function() {
		return this.fields;
	},
	hasField : function(id) {
		var hasField = false;
		jQuery.each(this.fields, function(index, field) {
			if (field.getId() == id) {
				hasField = true;
				return false;
			}
		});
		return hasField;
	},
	enable : function() {
		this.disabled = false;
		Effect.Appear(this.getHTMLId());
		jQuery.each(this.fields, function(index, field) {
			field.enable();
		});
	},
	disable : function() {
		this.disabled = true;
		Effect.Fade(this.getHTMLId());
		jQuery.each(this.fields, function(index, field) {
			field.disable();
		});
	},
	isDisabled : function() {
		return this.disabled;
	}
};

// This represents a field. It can be overloaded to fit to different fields
// types
var FormField = function(id){
	this.id = id;
	this.validationMessageEnabled = false;
	this.hasConstraints = false;
	this.formId = "";
};

FormField.prototype = {
	getId : function() {
		return this.id;
	},
	getHTMLId : function() {
		return this.formId + "_" + this.id;
	},
	setFormId : function(formId) {
		this.formId = formId;
	},
	HTMLFieldExists : function() {
		return jQuery('#' + this.getHTMLId()) != null;
	},
	enable : function() {
		if (this.HTMLFieldExists()) {
			jQuery('#' + this.getHTMLId()).prop('disabled', false);
		}
		jQuery("#" + this.getHTMLId() + "_field").fadeIn(300);
		this.liveValidate();
	},
	disable : function() {
		if (this.HTMLFieldExists()) {
			jQuery('#' + this.getHTMLId()).prop('disabled', true);
		}
		jQuery("#" + this.getHTMLId() + "_field").fadeOut(300);
		this.clearErrorMessage();
	},
	isDisabled : function() {
		if (this.HTMLFieldExists()) {
			var element = jQuery('#' + this.getHTMLId());
			var disabled = element.prop('disabled');
			if (disabled == false) {
				var field = jQuery('#' + this.getHTMLId() + '_field');
				if (field) {
					var display = field.css('display');
					if (display != null) {
						return display == "none";
					} else {
						return false;
					}
				} else {
					var display = element.css('display');
					if (display != null) {
						return display == "none";
					} else {
						return false;
					}
				}
			}
			return true;
		}
		return false;
	},
	getValue : function() {
		var field = jQuery('#' + this.getHTMLId());
		if (field.is(":checkbox")){
			return field.prop("checked");
		}
		else {
			return field.val();
		}
	},
	setValue : function(value) {
		var field = jQuery('#' + this.getHTMLId());
		if (field.is(":checkbox")){
			return field.prop("checked", value);
		}
		else {
			return field.val(value);
		}
	},
	enableValidationMessage : function() {
		this.validationMessageEnabled = true;
	},
	displayErrorMessage : function(message) {
		if (!this.validationMessageEnabled) {
			return;
		}
		
		if (jQuery('#' + this.getHTMLId() + '_field') && jQuery('#onblurContainerResponse' + this.getHTMLId())) {
			
			jQuery('#' + this.getHTMLId() + '_field').removeClass('constraint-status-right').addClass('constraint-status-error');
			jQuery('#onblurMessageResponse' + this.getHTMLId()).text(message);
			
			jQuery("#onblurMessageResponse" + this.getHTMLId()).fadeIn(500);
		}
	},
	displaySuccessMessage : function() {
		if (!this.validationMessageEnabled) {
			return;
		}
		
		if (jQuery('#' + this.getHTMLId() + '_field') && jQuery('#onblurContainerResponse' + this.getHTMLId())) {
			
			jQuery('#' + this.getHTMLId() + '_field').removeClass('constraint-status-error').addClass('constraint-status-right');
			jQuery("#onblurMessageResponse" + this.getHTMLId()).fadeOut(200);
		}
	},
	clearErrorMessage : function() {
		if (jQuery('#' + this.getHTMLId() + '_field') && jQuery('#onblurContainerResponse' + this.getHTMLId())) {

			jQuery('#' + this.getHTMLId() + '_field').removeClass('constraint-status-right').addClass('constraint-status-error');
			jQuery('#onblurMessageResponse' + this.getHTMLId()).text('');
			jQuery("#onblurMessageResponse" + this.getHTMLId()).fadeOut(200);
		}
	},
	liveValidate : function() {
		if (!this.isDisabled() && this.hasConstraints) {
			var errorMessage = this.doValidate();
			if (errorMessage != "") {
				this.displayErrorMessage(errorMessage);
			} else {
				this.displaySuccessMessage();
			}
		}
	},
	validate : function() {
		if (!this.isDisabled() && this.hasConstraints) {
			var errorMessage = this.doValidate();
			if (errorMessage != "") {
				this.enableValidationMessage();
				this.displayErrorMessage(errorMessage);
			} 
			return errorMessage;
		}
		return "";
	},
	doValidate : function() {
		return '';
	}
};