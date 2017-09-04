const $ = require('jquery');

require('bootstrap-sass');

const FormHelper = require('./FormHelper');

$(document).ready(function() {
  new FormHelper();

  const $wrapper = $('#js-editor');
  const $form = $wrapper.find('form');
  const $statusText = $('#js-status-text-container');
  const $textarea = $wrapper.find('textarea');
  let timeoutId;

  $textarea
    .on(
      'input propertychange change selectionchange',
      function() {
        $statusText.html('Saving draft…');

        clearTimeout(timeoutId);
        timeoutId = setTimeout(function() {

          $.ajax({
            type: $form.attr('method'),
            url: $form.attr('action'),
            data: $form.serialize()
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

