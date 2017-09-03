var $ = require('jquery');

require('bootstrap-sass');

var TexthubApp = {
  initialize: function($wrapper) {
    this.$wrapper = $wrapper;

    this.$wrapper
      .find('#new-text-modal')
      .on(
        'shown.bs.modal',
        this.handleNewTextModalOpen
      );

    this.$wrapper
      .find('#edit-text-title-modal')
      .on(
        'shown.bs.modal',
        this.handleEditTextTitleModalOpen
      );
  },

  handleEditTextTitleModalOpen: function() {
    $(this)
      .find('#appbundle_text_title')
      .select();
  },

  handleNewTextModalOpen: function() {
    $(this)
      .find('#appbundle_text_new_title')
      .select();
  }
};

$(document).ready(function() {
  var $body = $('body');
  TexthubApp.initialize($body);
});
