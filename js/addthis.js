// AddThis requires a global variable

var addthis_config,
  addthis_share;

(function ($, Drupal, window, document, undefined) {
  Drupal.behaviors.addThisWidget = {
    attach: function (context, settings) {
      if (context === document) { // only fires on document load

        // Because we cannot dynamically add JS
        if (typeof settings.addThisWidget.widgetScript !== 'undefined') {
          $.getScript(settings.addThisWidget.widgetScript)
            .done(function (script, textStatus) {
            }).fail(function (jqxhr, settings, exception) {
              // TODO: check for fail msg
            });
        }

      }
    }
  };

})(jQuery, Drupal, this, this.document);
