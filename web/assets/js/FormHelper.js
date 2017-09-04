class FormHelper {
  constructor($body) {
    this.$body = $body;

    this.timeoutId = undefined;

    

    this.$body
      .find('#js-edit-text-form')
      .on(
        'input propertychange change selectionchange',
        this.saveTextBodyDraft.bind(this)
      );

    this.$body
      .find('#new-text-modal')
      .on(
        'shown.bs.modal',
        this.handleNewTextModalOpen
      );

    this.$body
      .find('#edit-text-title-modal')
      .on(
        'shown.bs.modal',
        this.handleEditTextTitleModalOpen
      );
  }

  handleEditTextTitleModalOpen() {
    $(this)
      .find('#appbundle_text_title')
      .select();
  }

  handleNewTextModalOpen() {
    $(this)
      .find('#appbundle_text_new_title')
      .select();
  }

  saveTextBodyDraft() {
    let $editTextForm = this.$body.find('#js-edit-text-form');
    let $statusTextContainer = this.$body.find('#js-status-text-container');

    $statusTextContainer
      .html('Saving draft…');

    clearTimeout(this.timeoutId);

    this.timeoutId = setTimeout(function() {
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
}

module.exports = FormHelper;
