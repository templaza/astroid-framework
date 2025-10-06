(function ($) {
   $.fn.ASVideoBG = function () {
      return this.each(function () {
         var _url = $(this).data('as-video-bg'),
             _poster = $(this).data('as-video-poster'),
             _position = $(this).data('as-video-position');
         if (typeof _position === "undefined") {
            _position = 'absolute'
         }
         $(this).addClass('position-relative');
         $(this).children().addClass('position-relative z-1');

         var _container = $('<div/>');
         _container.addClass('astroid-element-overlay');
         _container.addClass('position-' + _position);
         _container.addClass('top-0 start-0 w-100 h-100 overflow-hidden z-0 pe-none');

         var _video = $('<video />', {
            playsinline: true,
            loop: true,
            src: _url,
         });

         if (typeof _poster !== 'undefined' && _poster) {
            _container.css('background', 'url('+_poster+')');
            _container.css('background-size', 'cover');
            _container.css('background-position', 'center center');
         }
         _video.addClass('position-absolute top-50 start-50 translate-middle w-auto h-auto');
         _video.css('min-width', '100%');
         _video.css('min-height', '100%');
         _video.css('z-index', '-100');
         _video.css('max-width', 'inherit');

         _container.append(_video);
         $(this).prepend(_container);

         _video.prop('muted', true);
         _video.trigger('play');
      });
   };
   $(document).ready(function () {
      $('[data-as-video-bg]').ASVideoBG();
   });
}(jQuery));

