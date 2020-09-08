"use strict";

jQuery.fn.extend({
  clear: function () {
    $(this).attr("readonly", false);
    $(this).val(null);
    $(this).data("id", null);

    return this;
  },

  lock: function (disable = false) {
    if ($(this).is("form")) {
      $(this).findReadonly();
      $(this).find("input, textarea, select").attr("readonly", true);
      $(this).find("button").attr("disabled", true);
    } else {
      if(disable) $(this).attr("disabled", true);
      else $(this).attr("readonly", true);
    }

    return this;
  },

  unlock: function (disable = false) {
    if ($(this).is("form")) {
      $(this)
        .find("input, textarea, select")
        .not($(this).data("readonly_elements"))
        .attr("readonly", false);
      $(this).find("button").attr("disabled", false);
    } else {
      if(disable) $(this).attr("disabled", false);
      else $(this).attr("readonly", false);
    }

    return this;
  },

  findReadonly: function () {
    let elements = $(this).find(
      'input[readonly="readonly"], input[readonly="true"], textarea[readonly="readonly"], textarea[readonly="true"]'
    );
    $(this).data("readonly_elements", elements);
    return elements;
  },

  scrollToBottom: function () {
    this.scrollTop(this.prop('scrollHeight'));
  },
  
  scrollSmoothToBottom: function () {
    this.animate({
       scrollTop: this.prop('scrollHeight')
    }, 800);
  },
  
  complete: function (val, id) {
    this.val(val);
    this.data('id', id);
    this.attr('readonly', true);
    return this;
  }
});
