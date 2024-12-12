<?php

/**
 * @package   Astroid Framework
 * @author    Astroid Framework Team https://astroidframe.work
 * @copyright Copyright (C) 2023 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

namespace Joomla\Plugin\Captcha\AstroidCaptcha\Extension;

use Joomla\CMS\Plugin\CMSPlugin;
use Astroid\Framework;
use Astroid\Helper;
use Joomla\Event\DispatcherInterface;
use ReCaptcha\RequestMethod;
use Joomla\CMS\Language\Text;

final class AstroidCaptcha extends CMSPlugin
{
    private $requestMethod;
    public function __construct(DispatcherInterface $dispatcher, array $config, RequestMethod $requestMethod)
    {
        parent::__construct($dispatcher, $config);

        $this->requestMethod = $requestMethod;
    }
    public function onInit($id = null)
    {
        $app = $this->getApplication();
        $captcha_type   =   $this->params->get('captcha_type', 'default');
        if ($captcha_type == 'recaptcha' || $captcha_type == 'recaptcha_invisible') {
            if ($this->params->get('g_site_key', '') === '') {
                throw new \RuntimeException($app->getLanguage()->_('ASTROID_GOOGLE_RECAPTCHA_ERROR_NO_PUBLIC_KEY'));
            }
            if ($captcha_type == 'recaptcha_invisible') {
                $onload = ['url' => 'astroid/recaptcha_invisible.min.js', 'function' => 'AstroidinitReCaptchaInvisible'];
                $render = 'explicit';
            } else {
                $onload = [];
                $render = '';
            }
            Framework::getDocument()->loadGoogleReCaptcha($onload, $render);
        }
        return true;
    }
    public function onDisplay($name = null, $id = 'astroid-recaptcha', $class = '')
    {
        $captcha_type = $this->params->get('captcha_type', 'default');
        $html = '<div class="mt-2">';
        if ($captcha_type == 'recaptcha' || $captcha_type == 'recaptcha_invisible') {
            $dom = new \DOMDocument('1.0', 'UTF-8');
            $ele = $dom->createElement('div');
            $ele->setAttribute('id', $id);
            $ele->setAttribute('class', ((trim($class) == '') ? 'g-recaptcha' : ($class . ' g-recaptcha')));
            $ele->setAttribute('data-sitekey', $this->params->get('g_site_key', ''));
            if ($captcha_type == 'recaptcha_invisible') {
                $ele->setAttribute('data-badge', $this->params->get('badge', 'bottomright'));
                $ele->setAttribute('data-size', 'invisible');
            }
            $ele->setAttribute('data-tabindex', $this->params->get('tabindex', '0'));
            $callback = $this->params->get('callback', '');
            if ($callback != '') {
                $ele->setAttribute('data-callback', $callback);
            }
            $expired_callback = $this->params->get('expired_callback', '');
            if ($expired_callback != '') {
                $ele->setAttribute('data-expired-callback', $expired_callback);
            }
            $error_callback = $this->params->get('error_callback', '');
            if ($error_callback != '') {
                $ele->setAttribute('data-error-callback', $error_callback);
            }
            $dom->appendChild($ele);
            $html .= $dom->saveHTML($ele);
        } else {
            $html .= Helper\Captcha::loadCaptcha('as-joomla-captcha');
        }
        $html .= '</div>';
        return $html;
    }
    public function onCheckAnswer($code = null)
    {
        $captcha_type = $this->params->get('captcha_type', 'default');
        if ($captcha_type == 'recaptcha' || $captcha_type == 'recaptcha_invisible') {
            $input      = $this->getApplication()->getInput();
            $privatekey = $this->params->get('g_secret_key', '');
            $gcaptcha  = $input->post->get('g-recaptcha-response', '', 'string');
            return Helper\Captcha::verifyGoogleCaptcha($gcaptcha, $privatekey, $this->requestMethod);
        } elseif (!Helper\Captcha::getCaptcha('as-joomla-captcha')) {
            throw new \RuntimeException(Text::_('ASTROID_AJAX_ERROR_INVALID_CAPTCHA'));
        } else {
            return true;
        }
    }
}