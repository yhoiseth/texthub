class FormHelper {
  constructor($body) {
    this.$body = $body;

    this.timeoutId = undefined;

    this.searchRequest = null;

    this.$searchForm = this.$body
      .find('#js-search-form');

    this.$searchInput = this.$searchForm
      .find('input[type="text"]');

    let textInputChangeEventTypes = 'input propertychange change selectionchange';

    this.$searchInput
      .on(
        textInputChangeEventTypes,
        this.updateTextList.bind(this)
      );

    this.$editTextForm = this.$body
      .find('#js-edit-text-form');

    this.$editTextForm
      .on(
        textInputChangeEventTypes,
        this.saveTextBodyDraft.bind(this)
      );

    this.$saveTextBodyButton = this.$body
      .find('#js-save-text-body-button');

    this.$saveTextBodyButton
      .on(
        'click',
        this.saveText.bind(this)
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

  updateTextList() {
    if (this.searchRequest !== null) {
      this.searchRequest.abort();
      this.searchRequest = null;
    }

    this.searchRequest = $.ajax({
      type: this.$searchForm.attr('method'),
      url: this.$searchForm.attr('action'),
      data: this.$searchInput.val()
    }).done(function(response) {
      console.log(response);
    });
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

  saveText() {
    const $saveTextBodyButton = this.$saveTextBodyButton;

    $saveTextBodyButton.prop('disabled', true);

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

  static updateTextStatus($statusTextContainer, status) {
    $statusTextContainer.html(status);
  }
}

module.exports = FormHelper;
