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

// $(document).ready(function() {
//   var $wrapper = $('#js-editor');
//   var $form = $wrapper.find('form');
//   var $statusText = $('#js-status-text-container');
//   var $textarea = $wrapper.find('textarea');
//   var timeoutId;
//
//   $textarea
//     .on(
//       'input propertychange change selectionchange',
//       function() {
//         $statusText.html('Saving draft…');
//
//         clearTimeout(timeoutId);
//         timeoutId = setTimeout(function() {
//
//           $.ajax({
//             type: $form.attr('method'),
//             url: $form.attr('action'),
//             data: $form.serialize()
//           })
//             .done(function(response) {
//               $statusText.html('Draft saved');
//             })
//           ;
//         }, 1000);
//       }
//     )
//   ;
//
//   var $saveTextBodyButton = $('#js-save-text-body-button');
//
//   $saveTextBodyButton
//     .on(
//       'click',
//       function() {
//         $(this).prop('disabled',true);
//
//         $.ajax({
//           type: 'POST',
//           url: window.location.href,
//           data: {
//             'save': true
//           }
//         })
//           .done(function(response) {
//             if (response === 'text successfully saved') {
//               window.location.href = $saveTextBodyButton.data('url');
//             }
//           })
//         ;
//
//       }
//     )
// });

