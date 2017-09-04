const $ = require('jquery');

require('bootstrap-sass');

const FormHelper = require('./FormHelper');

$(document).ready(function() {
  new FormHelper();

  const $editTextForm = $('#js-edit-text-form');
  const $statusTextContainer = $('#js-status-text-container');
  const $bodyInput = $editTextForm.find('textarea');
  let timeoutId;

  $bodyInput
    .on(
      'input propertychange change selectionchange',
      function() {
        $statusTextContainer.html('Saving draft…');

        clearTimeout(timeoutId);
        timeoutId = setTimeout(function() {

          $.ajax({
            type: $editTextForm.attr('method'),
            url: $editTextForm.attr('action'),
            data: $editTextForm.serialize()
          })
            .done(function(response) {
              $statusTextContainer.html('Draft saved');
            })
          ;
        }, 1000);
      }
    )
  ;

  const $saveTextBodyButton = $('#js-save-text-body-button');

  $saveTextBodyButton
    .on(
      'click',
      function() {
        $(this).prop('disabled', true);

        $.ajax({
          type: 'POST',
          url: window.location.href,
          data: {
            'save': true
          }
        })
          .done(function(response) {
            if (response === 'text successfully saved') {
              window.location.href = $saveTextBodyButton.data('url');
            }
          })
        ;

      }
    );
});

