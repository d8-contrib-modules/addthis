// AddThis requires a global variable

var addthis_config,
    addthis_share;

(function ($, Drupal, window, document, undefined) {
    Drupal.behaviors.addThisWidget = {

        //@TODO Need to support domready and async loading. See http://support.addthis.com/customer/portal/articles/1338006-url-parameters

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
                if (typeof settings.addThisWidget.config !== 'undefined') {
                    addthis_config = settings.addThisWidget.config;
                }
                if (typeof settings.addThisWidget.share !== 'undefined') {
                    addthis_share = settings.addThisWidget.share;
                }
            }
        }
    };

})(jQuery, Drupal, this, this.document);
