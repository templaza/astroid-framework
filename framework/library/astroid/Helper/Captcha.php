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
        return '<div class="'.$context.'">'.($value1 . ' + ' . $value2 .' = ?').'</div><div class="'.$context.'-result"><input type="text" name="'.$context.'" class="form-control" placeholder="'.($value1 . ' + ' . $value2 .' = ?').'"></div>';
    }

    public static function getCaptcha($context = '') {
        $app    =   Factory::getApplication();
        $value1 =   intval($app->getUserState( $context.'.value1' ));
        $value2 =   intval($app->getUserState( $context.'.value2' ));
        $value_result = intval($app->input->get($context, 0, 'ALNUM'));
        return ( $value1 + $value2 == $value_result );
    }

    public static function verifyGoogleCaptcha($gRecaptchaResponse, $secretKey = '') {
        if (empty($gRecaptchaResponse)) {
            return false;
        }
        if (empty($secretKey)) {
            $pluginParams   =   Helper::getPluginParams();
            $secretKey      =   $pluginParams->get('g_secret_key', '');
        }
        if (empty($secretKey)) {
            return false;
        }
        $url = 'https://www.google.com/recaptcha/api/siteverify';
        $data = [
            'secret' => $secretKey,
            'response' => $gRecaptchaResponse
        ];

        $options = [
            'http' => [
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($data),
            ],
        ];

        $context  = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        $response = json_decode($result, true);

        return isset($response['success']) && $response['success'] === true;
    }
}