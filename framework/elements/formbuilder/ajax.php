<?php

/**
 * @package   Astroid Framework
 * @author    Astroid Framework Team https://astroidframe.work
 * @copyright Copyright (C) 2024 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 * DO NOT MODIFY THIS FILE DIRECTLY AS IT WILL BE OVERWRITTEN IN THE NEXT UPDATE
 * You can easily override all files under /astroid/ folder.
 * Just copy the file to JROOT/media/templates/site/{YOUR_TEMPLATE_NAME}/astroid/elements/module_position/module_position.php folder to create and override
 */

// No direct access.
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Mail\MailerFactoryInterface;
use Joomla\CMS\Language\Text;
use Joomla\Utilities\IpHelper;
use Astroid\Helper;
use Astroid\Framework;
$mainframe      =   Factory::getApplication();
$asformbuilder  =   $mainframe->input->post->get('as-form-builder-', array(), 'RAW');
$unqid          =   $mainframe->input->post->get('form_id', '', 'string');
$source         =   $mainframe->input->post->get('source', '', 'string');
$template_id    =   $mainframe->input->post->get('template', '', 'ALNUM');
$template       =   Framework::getTemplate(intval($template_id));
$layout_type    =   $mainframe->input->post->get('layout_type', '', 'string');
$article_id     =   0;
if ($layout_type == 'article_layouts') {
    $article_id     =   $mainframe->input->post->get('id', 0, 'int');
}
$element        =   Helper::getElement($unqid, '', ['source' => $source, 'template' => $template->template, 'layout_type' => $layout_type, 'article_id' => $article_id]);
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
$return = array();
try {
    if (empty($asformbuilder)) {
        throw new \Exception(Text::_('ASTROID_AJAX_ERROR_INVALID_FORM_DATA'));
    }
    if (empty($element)) {
        throw new \Exception(Text::_('ASTROID_AJAX_ERROR_INVALID_FORM_ID'));
    }
    $params         =   $element['params'];
    $mail           =   Factory::getContainer()->get(MailerFactoryInterface::class)->createMailer();
    $message        =   $params->get('email_body', '');
    $email_headers  =   $params->get('email_headers', '');

    $pluginParams   =   Helper::getPluginParams('captcha', 'astroidcaptcha');

    foreach ($asformbuilder as $field => $value) {
        if (is_array($value)) {
            $value = implode(', ', $value);
        }
        $message        =   str_replace('{{'.$field.'}}', $value, $message);
        $email_headers  =   str_replace('{{'.$field.'}}', $value, $email_headers);
    }
    $replyToMail = $replyToName = '';

    if (intval($params->get('enable_captcha', 0))) {
        $captcha_type   =   $pluginParams->get('captcha_type', 'default');
        $invalidCaptchaMessage = Text::_('ASTROID_AJAX_ERROR_INVALID_CAPTCHA');
        if ($captcha_type == 'recaptcha') {
            $gcaptcha   =   $mainframe->input->post->get('g-recaptcha-response');
            if (empty($gcaptcha) || !Helper\Captcha::verifyGoogleCaptcha($gcaptcha)) {
                throw new \Exception($invalidCaptchaMessage);
            }
        } elseif ($captcha_type == 'turnstile') {
            $token      =   $mainframe->input->post->get('cf-turnstile-response');
            if (empty($token) || !Helper\Captcha::verifyCloudFlareTurnstile($token)) {
                throw new \Exception($invalidCaptchaMessage);
            }
        } elseif (!Helper\Captcha::getCaptcha('as-formbuilder-captcha')) {
            throw new \Exception($invalidCaptchaMessage);
        }
    }
    //get sender UP
    $senderip       = IpHelper::getIp();
    // Subject Structure
    $config         = Factory::getApplication()->getConfig();
    $site_name 	    = $config->get( 'sitename', '' );
    $mail_subject   = $params->get('email_subject', '');
    $mail_subject   = preg_replace_callback('/\{\{(\S+?)\}\}/siU', function ($matches) use (&$asformbuilder, &$site_name) {
        if (isset($asformbuilder[$matches[1]])) {
            return $asformbuilder[$matches[1]];
        } elseif ($matches[1] == 'site-name') {
            return $site_name;
        }
        return $matches[0];
    }, $mail_subject);
    // Message structure
    $mail_body =  $message;
    $mail_body .= '<p><strong>' . Text::_('ASTROID_FORMBUILDER_SENDER_IP'). '</strong>: ' . $senderip .'</p>';

    $sender = array( $config->get( 'mailfrom' ), $config->get( 'fromname' ) );
    $recipient = $config->get( 'mailfrom' );

    // $sender = array( $email, $name );

    $from_email =   $params->get('from_email', '');
    if (!empty($from_email)) {
        $sender = array($from_email, $from_email);
        $mail->addReplyTo($from_email, $from_email);
    }

    $recipient =   $params->get('recipient', '');


    if (!empty($email_headers)) {
        $additional_header_ajax = explode("\n", $email_headers);
        foreach ($additional_header_ajax as $_header)
        {
            $_header = explode(':', $_header);
            if (count($_header) > 0)
            {
                if (strtolower($_header[0]) == 'reply-to')
                {
                    $replyToMail =  isset($_header[1]) ?  trim($_header[1]) : '';
                }
                if (strtolower($_header[0])  == 'reply-name')
                {
                    $replyToName =  isset($_header[1]) ?  trim($_header[1]) : '';
                }
                if (strtolower($_header[0]) == 'cc' && isset($_header[1]))
                {
                    $mail->addCc(trim($_header[1]));
                }
                if (strtolower($_header[0]) == 'bcc' && isset($_header[1]))
                {
                    $mail->addCc(trim($_header[1]));
                }
            }
        }
        if (!empty($replyToMail)) {
            if (!empty($replyToName)) {
                $mail->addReplyTo($replyToMail, $replyToName);
            } else {
                $mail->addReplyTo($replyToMail);
            }
        }
    }

    $mail->setSender($sender);
    $mail->addRecipient($recipient);
    $mail->setSubject($mail_subject);
    $mail->isHTML(true);
    $mail->Encoding = 'base64';
    $mail->setBody($mail_body);

    $message_success    =   $params->get('form_submit_success', Text::_('ASTROID_FORMBUILDER_SENT_SUCCESSFULLY'));
    $message_failed     =   $params->get('form_submit_failed', Text::_('ASTROID_FORMBUILDER_SENT_MAIL_FAILED'));

    if ($mail->Send()) {
        $return["status"]   =   'success';
        $return["message"]  =   $message_success;
        $return["code"]     =   200;
        if ($params->get('enable_redirect', 0) && !empty($params->get('redirect_url', ''))) {
            $return["redirect"] = $params->get('redirect_url', '');
        }
    } else {
        throw new \Exception($message_failed);
    }
} catch (\Exception $e) {
    $return["status"] = "error";
    $return["code"] = $e->getCode();
    $return["message"] = $e->getMessage();
}
echo \json_encode($return);