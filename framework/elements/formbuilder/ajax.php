<?php

/**
 * @package   Astroid Framework
 * @author    Astroid Framework Team https://astroidframe.work
 * @copyright Copyright (C) 2024 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 * DO NOT MODIFY THIS FILE DIRECTLY AS IT WILL BE OVERWRITTEN IN THE NEXT UPDATE
 * You can easily override all files under /astroid/ folder.
 * Just copy the file to JROOT/templates/YOUR_ASTROID_TEMPLATE/astroid/elements/module_position/module_position.php folder to create and override
 */

// No direct access.
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Mail\MailerFactoryInterface;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Language\Text;
use Astroid\Helper;

$mainframe      =   Factory::getApplication();
$asformbuilder  =   $mainframe->input->get('as-form-builder-', array(), 'RAW');
$unqid          =   $mainframe->input->get('form_id', '', 'ALNUM');
$element        =   Helper::getElement($unqid, $template);
if (!empty($asformbuilder) && !empty($element)) {
    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: *');
    $return = array();
    try {
        $params         =   $element['params'];
        $mail           =   Factory::getContainer()->get(MailerFactoryInterface::class)->createMailer();;
        $message        =   $params->get('email_body', '');
        $email_headers  =   $params->get('email_headers', '');
        $gcaptcha       =   '';

        foreach ($asformbuilder as $field => $value) {
            $message        =   str_replace('{{'.$field.'}}', $value, $message);
            $email_headers  =   str_replace('{{'.$field.'}}', $value, $email_headers);
            if ($field == 'g-recaptcha-response') {
                $gcaptcha = $value;
            }
        }

        $replyToMail = $replyToName = '';

        if (intval($params->get('enable_captcha', 0))) {
            $captcha_type   =   $params->get('captcha_type', 'default');
            if ($captcha_type == 'recaptcha' || $captcha_type == 'recaptcha_invisible') {
                if($gcaptcha == ''){
                    throw new \Exception(Text::_('ASTROID_AJAX_ERROR_INVALID_CAPTCHA'));
                } else {
                    if($captcha_type == 'recaptcha_invisible') {
                        PluginHelper::importPlugin('captcha', 'recaptcha_invisible');
                    } else {
                        PluginHelper::importPlugin('captcha', 'recaptcha');
                    }
                    $dispatcher = Factory::getApplication()->getDispatcher();
                    $event = new Joomla\Event\Event('onCheckAnswer', [$gcaptcha]);
                    $res = $dispatcher->dispatch('onCheckAnswer', $event);

                    if (!$res[0]) {
                        throw new \Exception(Text::_('ASTROID_AJAX_ERROR_INVALID_CAPTCHA'));
                    }
                }
            } else {
                if (!Helper::getCaptcha('as-formbuilder-captcha')) {
                    throw new \Exception(Text::_('ASTROID_AJAX_ERROR_INVALID_CAPTCHA'));
                }
            }
        }

        //get sender UP
        $senderip       = isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
        // Subject Structure
        $site_name 	    = isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : '';
        $mail_subject   = $params->get('email_subject', '') . ' - ' . $site_name;
        $mail_subject = preg_replace_callback('/\{\{(\S+?)\}\}/siU', function ($matches) use (&$asformbuilder, &$site_name) {
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

        $config = Factory::getConfig();

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

        $message_success    =   $params->get('success_message', Text::_('ASTROID_FORMBUILDER_SENT_SUCCESSFULLY'));
        $message_failed     =   $params->get('failed_message', Text::_('ASTROID_FORMBUILDER_SENT_MAIL_FAILED'));

        if ($mail->Send()) {
            $return["status"]   =   'success';
            $return["message"]  =   $message_success;
            $return["code"]     =   200;
        } else {
            throw new \Exception($message_failed);
        }
    } catch (\Exception $e) {
        $return["status"] = "error";
        $return["code"] = $e->getCode();
        $return["message"] = $e->getMessage();
    }
    echo \json_encode($return);
}