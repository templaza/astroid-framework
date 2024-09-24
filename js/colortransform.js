(function ($) {
    function colorTransform () {
        if ((window.innerHeight + window.scrollY) >= document.body.scrollHeight * (ASTROID_COLOR_TRANSFORM.offset/100)) {
            if ($('html').attr('data-bs-theme') === ASTROID_COLOR_TRANSFORM.from) {
                $('html').attr('data-bs-theme', ASTROID_COLOR_TRANSFORM.to);
            }
        } else {
            if ($('html').attr('data-bs-theme') === ASTROID_COLOR_TRANSFORM.to) {
                $('html').attr('data-bs-theme', ASTROID_COLOR_TRANSFORM.from);
            }
        }
    }
    $(document).ready(function (){
        $('body').addClass('as-transition-body');
        colorTransform();
    })
    $(window).on('load', function () {
        window.onscroll = function(ev) {
            colorTransform();
        };
    })
}(jQuery));