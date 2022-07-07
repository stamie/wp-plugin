(function($) {

  'use strict';

  $(document).ready(function() {

    'use strict';

    $('#execute-task').click(function(event) {
      event.preventDefault();
      $('#dialog-confirm').dialog('open');
    });

  });

  /**
   * Original Version (not compatible with pre-ES5 browser)
   */
  // $(function() {
  //   $('#dialog-confirm').dialog({
  //     autoOpen: false,
  //     resizable: false,
  //     height: 'auto',
  //     width: 340,
  //     modal: true,
  //     buttons: {
  //       [window.objectL10n.deleteText]: function() {
  //         $('#form-maintenance').submit();
  //       },
  //       [window.objectL10n.cancelText]: function() {
  //         $(this).dialog('close');
  //       },
  //     },
  //   });
  // });

  /**
   *
   * Compiled Version (compatible with pre-ES5 browser and ES5 browsers)
   *
   * Version compiled with Babel (https://babeljs.io).
   *
   * The reason is that dynamic property names (in this case [objectL10n.deleteText] and [objectL10n.cancelText] are not
   * supported with pre-ES5 JavaScript engines.
   */
  function _defineProperty(obj, key, value) {
    if (key in obj) {
      Object.defineProperty(obj, key, {value: value, enumerable: true, configurable: true, writable: true});
    } else {
      obj[key] = value;
    }
    return obj;
  }

  $(function() {
    var _buttons;

    $('#dialog-confirm').dialog({
      autoOpen: false,
      resizable: false,
      height: 'auto',
      width: 340,
      modal: true,
      buttons: (_buttons = {}, _defineProperty(_buttons, window.objectL10n.deleteText, function() {
        $('#form-maintenance').submit();
      }), _defineProperty(_buttons, window.objectL10n.cancelText, function() {
        $(this).dialog('close');
      }), _buttons),
    });
  });

}(window.jQuery));

