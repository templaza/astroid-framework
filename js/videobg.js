(function ($) {
   $.fn.ASVideoBG = function () {
      return this.each(function () {
         var _url = $(this).data('as-video-bg'),
             _poster = $(this).data('as-video-poster');
         $(this).css('position', 'relative');
         $(this).children().css('position', 'relative');
         $(this).children().css('z-index', '1');

         var _container = $('<div/>');
         _container.addClass('astroid-element-overlay')
         _container.css('position', 'absolute');
         _container.css('top', '0');
         _container.css('left', '0');
         _container.css('width', '100%');
         _container.css('height', '100%');
         _container.css('overflow', 'hidden');
         _container.css('z-index', '0');

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
         _video.css('position', 'absolute');
         _video.css('top', '50%');
         _video.css('left', '50%');
         _video.css('min-width', '100%');
         _video.css('min-height', '100%');
         _video.css('width', 'auto');
         _video.css('height', 'auto');
         _video.css('z-index', '-100');
         _video.css('transform', 'translate(-50%, -50%)');
         _video.css('max-width', 'inherit');
         _video.prop('muted', true);

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

