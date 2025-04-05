<?php

/**
 * @package   Astroid Framework
 * @author    Astroid Framework https://astroidframe.work
 * @copyright Copyright (C) 2024 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

\defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use \Astroid\Framework;
use \Astroid\Helper;
/** @var Joomla\CMS\Document\HtmlDocument $this */
$document = Framework::getDocument(); // Astroid Document
$app = $document->getApp();
$wa = $document->getWA();
$wa->useScript('bootstrap.alert');
$attributes = ['class' => 'col-lg-7 comingsoon-wrap'];
$params = Framework::getTemplate()->getParams();
Helper::coming_soon();
$comingsoon_date = $params->get("coming_soon_countdown_date");
$coming_soon_logo = $params->get("coming_soon_logo");
if(isset($comingsoon_date)){
    $date = new \DateTime($comingsoon_date, new \DateTimeZone($app->getConfig()->get('offset')));
    $comingsoon_date = $date->format('Y/m/d H:i:s');
}
$background_setting = $params->get('background_setting');
if ($background_setting == "video") {
    $background_video = $params->get('background_video', '');
    if (!empty($background_video)) {
        $attributes['data-as-video-bg'] = Uri::root() . Astroid\Helper\Media::getPath() . '/' . $background_video;
        $wa->registerAndUseScript('astroid.videobg', 'astroid/videobg.min.js', ['relative' => true, 'version' => 'auto'], [], ['jquery']);
    }
}
$wrap_attrs = [];
foreach ($attributes as $key => $value) {
    $wrap_attrs[] = $key . '="' . $value . '"';
}
?>
<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <jdoc:include type="head" />
    <astroid:include type="head-styles" /> <!-- head styles -->
</head>
<body>
    <div class="offline-page row g-0">
        <div class="col-lg-5 p-lg-5 p-4 bg-body-tertiary">

            <div id="frame" class="d-flex flex-column justify-content-between h-100">
                <?php if ($app->get('offline_image')) : ?>
                    <img src="<?php echo $app->get('offline_image'); ?>" alt="<?php echo htmlspecialchars($app->get('sitename'), ENT_COMPAT, 'UTF-8'); ?>" class="offline-image-logo" />
                <?php endif; ?>
                <div class="offline-form-login">
                    <jdoc:include type="message" />
                    <h1><?php echo htmlspecialchars($app->get('sitename'), ENT_COMPAT, 'UTF-8'); ?></h1>
                    <?php if ($app->get('display_offline_message', 1) == 1 && str_replace(' ', '', $app->get('offline_message')) !== '') : ?>
                        <p>
                            <?php echo $app->get('offline_message'); ?>
                        </p>
                    <?php elseif ($app->get('display_offline_message', 1) == 2 && str_replace(' ', '', Text::_('JOFFLINE_MESSAGE')) !== '') : ?>
                        <p>
                            <?php echo Text::_('JOFFLINE_MESSAGE'); ?>
                        </p>
                    <?php endif; ?>
                    <form action="<?php echo Route::_('index.php', true); ?>" method="post" id="form-login">
                        <fieldset class="input">
                            <p id="form-login-username" class="mb-3">
                                <label for="username" class="form-label"><?php echo Text::_('JGLOBAL_USERNAME'); ?></label>
                                <input name="username" id="username" type="text" class="inputbox form-control" alt="<?php echo Text::_('JGLOBAL_USERNAME'); ?>" autocomplete="off" autocapitalize="none" />
                            </p>
                            <p id="form-login-password" class="mb-3">
                                <label for="passwd" class="form-label"><?php echo Text::_('JGLOBAL_PASSWORD'); ?></label>
                                <input type="password" name="password" class="inputbox form-control" alt="<?php echo Text::_('JGLOBAL_PASSWORD'); ?>" id="passwd" />
                            </p>
                            <p id="submit-button" class="mt-4">
                                <button type="submit" name="Submit" class="btn btn-primary btn-lg w-100"><?php echo Text::_('JLOGIN'); ?></button>
                            </p>
                            <input type="hidden" name="option" value="com_users" />
                            <input type="hidden" name="task" value="user.login" />
                            <input type="hidden" name="return" value="<?php echo base64_encode(Uri::base()); ?>" />
                            <?php echo HTMLHelper::_('form.token'); ?>
                        </fieldset>
                        <?php
                        if ($params->get('coming_soon_social', 1)) {
                            echo '<div class="coming_soon_social d-flex mt-4">';
                            $document->include('social', ['class' => 'd-inline-block']);
                            echo '</div>';
                        } ?>
                    </form>
                </div>
                <?php
                echo '<div class="coming_soon_footer mt-4">';
                $document->include('footer', ['class' => 'd-block']);
                echo '</div>';
                ?>
            </div>
        </div>
        <div <?php echo implode(' ', $wrap_attrs); ?>>
            <div class="px-lg-5 px-4 d-flex flex-column justify-content-center align-items-center h-100">
                <?php
                if ($coming_soon_logo) {
                    echo '<img src="' . Astroid\Helper\Media::getPath() . '/' . $coming_soon_logo . '" alt="Coming Soon" class="comingsoon-image my-4">';
                } else {
                    echo '<img src="media/astroid/assets/images/comingsoon.png" alt="Coming Soon" class="comingsoon-image my-4">';
                }
                if ($comingsoon_date) {
                    echo '<div id="comingsoon" class="w-100 my-4">';
                    echo '<div class="as-countdown comingsoon-date text-center row row-cols-4" data-date="' . $comingsoon_date . '" data-offset="' . $app->getConfig()->get('offset') . '" data-expired="' . $params->get('coming_soon_countdown_finish_text', '') . '">';
                    echo '<div class="days"><div class="counter-wrap bg-body-tertiary d-flex align-items-center justify-content-center flex-column"><span class="count">-</span><span class="label">' . Text::_('ASTROID_DAYS') . '</span></div></div>';
                    echo '<div class="hours"><div class="counter-wrap bg-body-tertiary d-flex align-items-center justify-content-center flex-column"><span class="count">-</span><span class="label">' . Text::_('ASTROID_HOURS') . '</span></div></div>';
                    echo '<div class="minutes"><div class="counter-wrap bg-body-tertiary d-flex align-items-center justify-content-center flex-column"><span class="count">-</span><span class="label">' . Text::_('ASTROID_MINUTES') . '</span></div></div>';
                    echo '<div class="seconds"><div class="counter-wrap bg-body-tertiary d-flex align-items-center justify-content-center flex-column"><span class="count">-</span><span class="label">' . Text::_('ASTROID_SECONDS') . '</span></div></div>';
                    echo '</div>';
                    echo '</div>';
                } ?>
            </div>
        </div>
    </div>
    <?php
    echo $document->getScripts('body');
    echo $document->getCustomTags('body');
    ?>
</body>
</html>