<?php

/**
 * @package   Astroid Framework
 * @author    Astroid Framework https://astroidframe.work
 * @copyright Copyright (C) 2024 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
/** @var Joomla\CMS\Document\HtmlDocument $this */
$app = Factory::getApplication();
$document = Astroid\Framework::getDocument(); // Astroid Document
$wa = $app->getDocument()->getWebAssetManager();
$wa->useScript('bootstrap.alert');
?>
<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <jdoc:include type="head" />
    <astroid:include type="head-styles" /> <!-- head styles -->
</head>
<body class="astroid-grid">
    <div class="d-flex justify-content-center">
        <div class="as-width-large my-5 px-3">
            <jdoc:include type="message" />
            <div id="frame" class="card card-body">
                <?php if ($app->get('offline_image') && file_exists($app->get('offline_image'))) : ?>
                    <img src="<?php echo $app->get('offline_image'); ?>" alt="<?php echo htmlspecialchars($app->get('sitename'), ENT_COMPAT, 'UTF-8'); ?>" />
                <?php endif; ?>
                <h1>
                    <?php echo htmlspecialchars($app->get('sitename'), ENT_COMPAT, 'UTF-8'); ?>
                </h1>
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
                </form>
            </div>
        </div>
    </div>
</body>
</html>