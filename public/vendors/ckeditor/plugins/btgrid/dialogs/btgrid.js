CKEDITOR.dialog.add( 'btgrid', function( editor ) {
  var lang = editor.lang.btgrid;
  var commonLang = editor.lang.common;

  // Whole-positive-integer validator.
  function validatorNum(msg) {
    return function() {
      return (this.getValue() == '') ? false: true;
      
      var value = this.getValue(),
        pass = !!(CKEDITOR.dialog.validate.integer()(value) && value > 0);

      if (!pass) {
        alert(msg); // jshint ignore:line
      }

      return pass;
    };
  }
  return {
    title: lang.editBtGrid,
    minWidth: 200,
    minHeight: 100,
    onShow: function() {
      // Detect if there's a selected table.
      var selection = editor.getSelection(),
        ranges = selection.getRanges();
      var command = this.getName();

      var rowsInput = this.getContentElement('info', 'rowCount'),
        colsInput = this.getContentElement('info', 'colCount');
      if (command == 'btgrid') {
        var grid = selection.getSelectedElement();
        // Enable or disable row and cols.
        if (grid) {
          this.setupContent(grid);
          rowsInput && rowsInput.disable();
          colsInput && colsInput.disable();
        }
      }
    },
    contents: [
      {
        id: 'info',
        label: lang.infoTab,
        accessKey: 'I',
        elements: [
          {
            id: 'colCount',
            type: 'select',
            required: true,
            label: lang.selNumCols,
            items: [
              [ '2-10','2-10'],
              [ '4-8','4-8'],
              [ '6-6','6-6'],
              [ '8-4','8-4'],
              [ '10-2','10-2'],
            ],
            validate: validatorNum(lang.numColsError),
            setup: function( widget ) {
              this.setValue(widget.data.colCount);
            },
            // When committing (saving) this field, set its value to the widget data.
            commit: function( widget ) {
              widget.setData( 'colCount', this.getValue());
            }
          },
          {
            id: 'rowCount',
            type: 'text',
            width: '50px',
            required: true,
            label: lang.genNrRows,
            validate: validatorNum(lang.numRowsError),
            setup: function( widget ) {
              this.setValue( widget.data.rowCount );
            },
            commit: function( widget ) {
              widget.setData( 'rowCount', this.getValue());
            }
          }
        ]
      }
    ],
  };
});
