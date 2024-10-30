jQuery(document).ready(function ($) {
    $('#momently_select_another').on('click', function (event) {
        var data = {
            action: 'momently_ajax_reset',
            nonce: momently_js_vars.nonce
        };
        jQuery.post(momently_js_vars.ajaxurl, data, function (response) {
            if (response.success) {
                window.location.href = momently_js_vars.baseUrl;
            }
        });
    });
});