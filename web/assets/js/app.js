const $ = require('jquery');

require('bootstrap-sass');

const FormHelper = require('./FormHelper');

$(document).ready(function() {
  new FormHelper();

  const $editTextForm = $('#js-edit-text-form');
  const $statusText = $('#js-status-text-container');
  const $textarea = $editTextForm.find('textarea');
  let timeoutId;

  $textarea
    .on(
      'input propertychange change selectionchange',
      function() {
        $statusText.html('Saving draft…');

        clearTimeout(timeoutId);
        timeoutId = setTimeout(function() {

          $.ajax({
            type: $editTextForm.attr('method'),
            url: $editTextForm.attr('action'),
            data: $editTextForm.serialize()
          })
            .done(function(response) {
              $statusText.html('Draft saved');
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

