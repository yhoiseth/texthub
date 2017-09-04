const $ = require('jquery');

require('bootstrap-sass');

const FormHelper = require('./FormHelper');

$(document).ready(function() {
  new FormHelper(
    $('body')
  );
});

