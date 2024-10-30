jQuery(document).ready(function ($) {
    var timer;

    function tryParse(event) {
        var out;
        try {
            out = JSON.parse(event.data);
        } catch (e) {
            out = '';
        }
        return out;
    }

    timer = setTimeout(function () {
        $('#momently_blocked').css('display', 'block');
    }, 500);


    $(window).on('message', receiveMessage);

    function receiveMessage(e) {
        var event = e.originalEvent;
        if (event.origin === 'https://momently.com') {
            var d = tryParse(event);
            if (d && d.siteId) {
                var data = {
                    action: 'momently_ajax_set_set_id',
                    siteId: d.siteId,
                    siteScript: d.siteScript,
                    nonce: momently_js_vars.nonce
                };

                jQuery.post(momently_js_vars.ajaxurl, data, function (response) {
                    if (response.success) {
                        window.location.href = momently_js_vars.baseUrl;
                    }
                });
            }
            clearTimeout(timer);
        }
    }
});