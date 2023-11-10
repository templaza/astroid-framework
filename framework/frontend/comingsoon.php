<?php

/**
 * @package   Astroid Framework
 * @author    Astroid Framework Team https://astroidframe.work
 * @copyright Copyright (C) 2023 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 * 	DO NOT MODIFY THIS FILE DIRECTLY AS IT WILL BE OVERWRITTEN IN THE NEXT UPDATE
 *  You can easily override all files under /frontend/ folder.
 *	Just copy the file to ROOT/templates/YOURTEMPLATE/html/frontend/ folder to create and override
 */
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Astroid\Helper\Style;
// No direct access.
defined('_JEXEC') or die;
extract($displayData);

$app = Factory::getApplication();
$document = Astroid\Framework::getDocument();
$params = Astroid\Framework::getTemplate()->getParams();
$wa = $app->getDocument()->getWebAssetManager();

$wa->registerAndUseScript('astroid.coundown.moment', 'astroid/moment/moment.min.js', ['relative' => true, 'version' => 'auto'], [], ['jquery']);
$wa->registerAndUseScript('astroid.coundown.timezone', 'astroid/moment/moment-timezone.min.js', ['relative' => true, 'version' => 'auto'], [], ['jquery']);
$wa->registerAndUseScript('astroid.coundown.timezone.10years', 'astroid/moment/moment-timezone-with-data-10-year-range.min.js', ['relative' => true, 'version' => 'auto'], [], ['jquery']);
$document->addScript('vendor/astroid/js/countdown.min.js', 'body');

// Background Image

$background_setting = $params->get('background_setting');
$comming_soon_style         =   new Style('.comingsoon-wrap');
$comming_soon_style_dark    =   new Style('.comingsoon-wrap', 'dark');

$styles = [];
$video = [];
if ($background_setting) {
   if ($background_setting == "color") {
      $background_color = Style::getColor($params->get('background_color', ''));
      $comming_soon_style->addCss('background-color', $background_color['light']);
      $comming_soon_style_dark->addCss('background-color', $background_color['dark']);
   }
   if ($background_setting == "image") {

      $img_background_color = Style::getColor($params->get('img_background_color', ''));
       $comming_soon_style->addCss('background-color', $img_background_color['light']);
       $comming_soon_style_dark->addCss('background-color', $img_background_color['dark']);

      $background_image = $params->get('background_image', '');
      if (!empty($background_image)) {
          $comming_soon_style->addCss('background-image', 'url(' . Uri::root() . Astroid\Helper\Media::getPath() . '/' . $background_image . ')');
          $background_repeat = $params->get('background_repeat', '');
          $background_repeat = empty($background_repeat) ? 'inherit' : $background_repeat;
          $comming_soon_style->addCss('background_repeat', $background_repeat);

          $background_size = $params->get('background_size', '');
          $background_size = empty($background_size) ? 'inherit' : $background_size;
          $comming_soon_style->addCss('background-size', $background_size);

          $background_attchment = $params->get('background_attchment', '');
          $background_attchment = empty($background_attchment) ? 'inherit' : $background_attchment;
          $comming_soon_style->addCss('background-attachment', $background_attchment);

          $background_position = $params->get('background_position', '');
          $background_position = empty($background_position) ? 'inherit' : $background_position;
          $comming_soon_style->addCss('background-position', $background_position);
      }
   }

   if ($background_setting == "gradient") {
      $background_gradient = $params->get('background_gradient', '');
      if (!empty($background_gradient)) {
          $comming_soon_style->addCss('background-image', $background_gradient->gradient_type . '-gradient(' . $background_gradient->start_color . ',' . $background_gradient->stop_color);
      }
   }

   if ($background_setting == "video") {
      $attributes = [];
      $background_video = $params->get('background_video', '');
      if (!empty($background_video)) {
         $attributes['data-jd-video-bg'] = Uri::root() . Astroid\Helper\Media::getPath() . '/' . $background_video;
          $wa->registerAndUseScript('astroid.videobg', 'astroid/videobg.min.js', ['relative' => true, 'version' => 'auto'], [], ['jquery']);
      }

      $return = [];
      foreach ($attributes as $key => $value) {
         $return[] = $key . '="' . $value . '"';
      }
      $video =  $return;
   }
}

$comingsoon_logo = "";
$hascs_logo = 0;
if ($cs_logo = $params->get('coming_soon_logo')) {
   $comingsoon_logo = Uri::root() . Astroid\Helper\Media::getPath() . '/' . $cs_logo;
   $hascs_logo = 1;
}
$comingsoon_date = $params->get("coming_soon_countdown_date");
if(isset($comingsoon_date)){
   $date = new DateTime($comingsoon_date, new DateTimeZone($app->getConfig()->get('offset')));
   $comingsoon_date = $date->format('Y/m/d H:i:s');
}
$comming_soon_style->render();
$comming_soon_style_dark->render();
?>

<div class="comingsoon-wrap" <?php echo implode(' ', $video); ?>>
   <div class="container">
      <div class="text-center">
         <div id="comingsoon">
            <div class="comingsoon-page-logo">
               <?php if ($hascs_logo) { ?>
                  <img class="comingsoon-logo m-auto" alt="logo" src="<?php echo $comingsoon_logo; ?>" />
               <?php } ?>
            </div>

            <?php if ($params->get('coming_soon_content')) { ?>
               <div class="comingsoon-content">
                  <?php echo $params->get('coming_soon_content'); ?>
               </div>
            <?php } ?>

            <?php if ($comingsoon_date) { ?>
               <div id="astroid-countdown" class="comingsoon-date text-center">
                  <div class="days mx-4">
                     <span class="count">-</span>
                     <span class="label"><?php echo Text::_('ASTROID_DAYS'); ?></span>
                  </div>
                  <div class="hours mx-4">
                     <span class="count">-</span>
                     <span class="label"><?php echo Text::_('ASTROID_HOURS'); ?></span>
                  </div>
                  <div class="minutes mx-4">
                     <span class="count">-</span>
                     <span class="label"><?php echo Text::_('ASTROID_MINUTES'); ?></span>
                  </div>
                  <div class="seconds mx-4">
                     <span class="count">-</span>
                     <span class="label"><?php echo Text::_('ASTROID_SECONDS'); ?></span>
                  </div>
               </div>
            <?php } ?>
            <?php
            if ($params->get('coming_soon_social', 1)) {
                echo '<div class="coming_soon_social d-flex justify-content-center">';
                $document->include('social', ['class' => 'd-inline-block']);
                echo '</div>';
            }
            ?>
         </div>
      </div>
   </div>
</div>

<?php if (!empty($comingsoon_date)) {
   $document->addScriptDeclaration("(function($) {
   $(function() {
      moment.tz.setDefault('" . $app->getConfig()->get('offset') . "');
      var _timer = moment('" . $comingsoon_date . "', 'YYYY/MM/DD HH:mm:ss').tz('" . $app->getConfig()->get('offset') . "');
      var _timezone = moment.tz.guess();
      var _countdown = _timer.clone().tz(_timezone).format('YYYY/MM/DD HH:mm:ss');
      $('#astroid-countdown').countdown(_countdown).on('update.countdown', function(event) {
         $(this).children('.days').children('.count').html(event.strftime('%D'));
         $(this).children('.hours').children('.count').html(event.strftime('%H'));
         $(this).children('.minutes').children('.count').html(event.strftime('%M'));
         $(this).children('.seconds').children('.count').html(event.strftime('%S'));
      }).on('finish.countdown', function(event) {
         $(this).children('.days').children('.count').html('0');
         $(this).children('.hours').children('.count').html('0');
         $(this).children('.minutes').children('.count').html('0');
         $(this).children('.seconds').children('.count').html('0');
      });
   });
})(jQuery);", "body");
?>
<?php } ?>