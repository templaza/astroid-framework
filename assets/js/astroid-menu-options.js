var spectrumConfig = {
   showInput: true,
   showInitial: false,
   allowEmpty: true,
   showAlpha: true,
   disabled: false,
   showPalette: true,
   showPaletteOnly: false,
   showSelectionPalette: true,
   showButtons: false,
   localStorageKey: "astroid.colors",
   palette: [
      ["#fff", "#f8f9fa", "#dee2e6", "#adb5bd", "#495057", "#343a40", "#212529", "#000"],
      ["#007bff", "#8445f7", "#ff4169", "#c4183c", "#fb7906", "#ffb400", "#17c671", "#00b8d8"]
   ],
};

(function ($) {
   $(function () {
      $(window).on('load', function () {
         Astroid.initMenuOptions();
      });
      $('.astroid-icon-selector').dropdown({
         placeholder: false,
         apiSettings: {
            url: astroidSearchUrl + '&search=icon&query={query}'
         },
         filterRemoteData: true
      });
      $('.astroid-select-ui').dropdown({placeholder: false, fullTextSearch: true});
      $('.astroid-color-picker').each(function () {
         if ($(this).hasClass('color-picker-lg')) {
            var spectrumConfigExtend = spectrumConfig;
            spectrumConfigExtend.replacerClassName = 'color-picker-lg';
            $(this).spectrum(spectrumConfigExtend);
         } else {
            $(this).spectrum(spectrumConfig);
         }
      });
      if ($('#jform_params_astroid_menu_options_megamenu').is(':checked')) {
         $('#astroid-megamenu-options').show();
      } else {
         $('#astroid-megamenu-options').hide();
      }
      $('#jform_params_astroid_menu_options_megamenu').change(function () {
         if ($(this).is(':checked')) {
            $('#astroid-megamenu-options').slideDown(200);
         } else {
            $('#astroid-megamenu-options').slideUp(200);
         }
      });
   });
})(jQuery);