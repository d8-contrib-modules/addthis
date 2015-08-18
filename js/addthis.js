// AddThis requires a global variable. :(
var addthis_config;
var addthis_share;

(function ($) {
  Drupal.behaviors.addThisWidget = {
    attach: function (context, settings) {
      // Because we cannot dynamically add JS, we do it here. :(
      $('body').once('LoadAddThisWidget').each(function () {
        //if (typeof settings.addThisWidget.widgetScript !== 'undefined') {
        //  $.getScript(settings.addThisWidget.widgetScript);
        //}

        if (typeof settings.addThisWidget.config !== 'undefined') {
          addthis_config = settings.addThisWidget.config;
        }

        if (typeof settings.addThisWidget.share !== 'undefined') {
          addthis_share = settings.addThisWidget.share;
        }
      });
    }
  };
})(jQuery);
