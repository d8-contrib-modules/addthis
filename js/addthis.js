// AddThis requires a global variable. :(
var addthis_config,
  addthis_share;

(function ($, Drupal, window, document, undefined) {
  Drupal.behaviors.addThisWidget = {
    attach: function (context, settings) {
      if (context === document) { // only fires on document load

               console.log(settings.addThisWidget);

        console.log('loaded ');

        // Because we cannot dynamically add JS, we do it here. :(
        if (typeof settings.addThisWidget.widgetScript !== 'undefined') {
          $.getScript(settings.addThisWidget.widgetScript)
            .done(function (script, textStatus) {

              // console.log(settings.addThisWidget.widgetScript);

              console.log('Success');

            }).fail(function (jqxhr, settings, exception) {

              // TODO: check for fail msg
              console.log('Fail');

            });
        }

        /*
         console.log(addthis_share);
         console.log('- - - - - - - - - - - - ');
         console.log(settings.addThisWidget);
         console.log('- - - - - - - - - - - - ');
         */

      }
    }
  };

})(jQuery, Drupal, this, this.document);
