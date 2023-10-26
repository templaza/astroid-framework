(function ($) {
    $(function () {
        var getCookie = function (cname) {
            var name = cname + "=";
            var decodedCookie = decodeURIComponent(document.cookie);
            var ca = decodedCookie.split(';');
            for (var i = 0; i < ca.length; i++) {
                var c = ca[i];
                while (c.charAt(0) == ' ') {
                    c = c.substring(1);
                }
                if (c.indexOf(name) == 0) {
                    return c.substring(name.length, c.length);
                }
            }
            return "";
        }

        var setCookie = function (cname, cvalue, exdays) {
            var d = new Date();
            d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
            var expires = "expires=" + d.toUTCString();
            document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
        }

        $('.astroid-reporter-heading').click(function () {
            if ($('#astroid-reporter').hasClass('active')) {
                setCookie('astroid-reporter-panel', '0');
            } else {
                setCookie('astroid-reporter-panel', '1');
            }
            $('#astroid-reporter').toggleClass('active');
        });

        var isActive = getCookie('astroid-reporter-panel');
        if (isActive == '0') {
            $('#astroid-reporter').removeClass('active');
        } else {
            $('#astroid-reporter').addClass('active');
        }

    });
})(jQuery);