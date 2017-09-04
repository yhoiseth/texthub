class FormHelper {
  constructor() {
    this.$body = $('body');

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
}

module.exports = FormHelper;
