class FormHelper {
  constructor($body) {
    this.$body = $body;

    this.timeoutId = undefined;

    this.$editTextForm = this.$body
      .find('#js-edit-text-form');

    this.$editTextForm
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
    const $editTextForm = this.$editTextForm;
    const updateTextStatus = FormHelper.updateTextStatus;
    const $statusTextContainer = this.$body
      .find('#js-status-text-container');

    FormHelper.updateTextStatus(
      $statusTextContainer,
      'Saving draft…'
    );

    clearTimeout(this.timeoutId);

    this.timeoutId = setTimeout(function() {
      $.ajax({
        type: $editTextForm.attr('method'),
        url: $editTextForm.attr('action'),
        data: $editTextForm.serialize()
      })
        .done(
          FormHelper.updateTextStatus(
            $statusTextContainer,
            'Draft saved'
          )
        )
      ;
    }, 1000);
  }

  static updateTextStatus($statusTextContainer, status) {
    $statusTextContainer.html(status);
  }
}

module.exports = FormHelper;
