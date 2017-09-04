const $ = require('jquery');

require('bootstrap-sass');

const FormHelper = require('./FormHelper');

$(document).ready(function() {
  new FormHelper($('body'));

  // $saveTextBodyButton
  //   .on(
  //     'click',
  //     function() {
        // $(this).prop('disabled', true);
        //
        // $.ajax({
        //   type: 'POST',
        //   url: window.location.href,
        //   data: {
        //     'save': true
        //   }
        // })
        //   .done(function(response) {
        //     if (response === 'text successfully saved') {
        //       window.location.href = $saveTextBodyButton.data('url');
        //     }
        //   })
        // ;

      // }
    // );
});

