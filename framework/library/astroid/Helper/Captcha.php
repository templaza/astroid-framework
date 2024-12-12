<?php

/**
 * @package   Astroid Framework
 * @author    Astroid Framework Team https://astroidframe.work
 * @copyright Copyright (C) 2023 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

namespace Astroid\Helper;

use Astroid\Helper;
use Joomla\CMS\Factory;
use Joomla\CMS\Captcha\Google\HttpBridgePostRequestMethod;
use ReCaptcha\ReCaptcha;
use ReCaptcha\RequestMethod;
use Joomla\Utilities\IpHelper;

defined('_JEXEC') or die;

class Captcha {
    public static function loadCaptcha($context = '') {
        if (empty($context)) {
            return '';
        }
        $app    =   Factory::getApplication();
        $value1 =   rand(1,100);
        $value2 =   rand(1,100);
        $app->setUserState( $context.'.value1', $value1 );
        $app->setUserState( $context.'.value2', $value2 );
        return '<div class="'.$context.'">'.($value1 . ' + ' . $value2 .' = ?').'</div><div class="'.$context.'-result"><input type="text" name="'.$context.'" class="form-control required" placeholder="'.($value1 . ' + ' . $value2 .' = ?').'" required></div>';
    }

    public static function getCaptcha($context = '') {
        $app    =   Factory::getApplication();
        $value1 =   intval($app->getUserState( $context.'.value1' ));
        $value2 =   intval($app->getUserState( $context.'.value2' ));
        $value_result = intval($app->input->get($context, 0, 'ALNUM'));
        return ( $value1 + $value2 == $value_result );
    }

    public static function verifyGoogleCaptcha($gRecaptchaResponse, $secretKey = '', RequestMethod $requestMethod = new HttpBridgePostRequestMethod()) {
        $app    =   Factory::getApplication();
        if (empty($secretKey)) {
            $pluginParams   =   Helper::getPluginParams('captcha', 'astroidcaptcha');
            $secretKey      =   $pluginParams->get('g_secret_key', '');
        }
        if (empty($secretKey)) {
            throw new \RuntimeException($app->getLanguage()->_('ASTROID_GOOGLE_RECAPTCHA_ERROR_NO_PRIVATE_KEY'));
        }
        $remoteip   = IpHelper::getIp();
        // Check for IP
        if (empty($remoteip)) {
            throw new \RuntimeException($app->getLanguage()->_('ASTROID_GOOGLE_RECAPTCHA_ERROR_NO_IP'));
        }
        if (empty($gRecaptchaResponse)) {
            throw new \RuntimeException($app->getLanguage()->_('ASTROID_GOOGLE_RECAPTCHA_ERROR_EMPTY_SOLUTION'));
        }
        $reCaptcha = new ReCaptcha($secretKey, $requestMethod);
        $response  = $reCaptcha->verify($gRecaptchaResponse, $remoteip);
        if (!$response->isSuccess()) {
            foreach ($response->getErrorCodes() as $error) {
                throw new \RuntimeException($error);
            }
            return false;
        }
        return true;
    }
}