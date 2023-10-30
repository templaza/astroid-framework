<?php

/**
 * @package   Astroid Framework
 * @author    Astroid Framework Team https://astroidframe.work
 * @copyright Copyright (C) 2023 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

namespace Astroid\Helper;

use Astroid\Framework;
use Astroid\Helper;
use Joomla\CMS\Uri\Uri;
use Joomla\Registry\Registry;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Version;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;

class Client
{
    protected $format = 'json';

    public function __construct()
    {
        $this->format = Factory::getApplication()->input->get('format', 'json', 'RAW');
    }

    protected function responseMime()
    {
        $mime_types = array(
            'txt' => 'text/plain',
            'htm' => 'text/html',
            'html' => 'text/html',
            'php' => 'text/html',
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'xml' => 'application/xml',
            'swf' => 'application/x-shockwave-flash',
            'flv' => 'video/x-flv',

            // images
            'png' => 'image/png',
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'ico' => 'image/vnd.microsoft.icon',
            'tiff' => 'image/tiff',
            'tif' => 'image/tiff',
            'svg' => 'image/svg+xml',
            'svgz' => 'image/svg+xml',

            // archives
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed',
            'exe' => 'application/x-msdownload',
            'msi' => 'application/x-msdownload',
            'cab' => 'application/vnd.ms-cab-compressed',

            // audio/video
            'mp3' => 'audio/mpeg',
            'qt' => 'video/quicktime',
            'mov' => 'video/quicktime',

            // adobe
            'pdf' => 'application/pdf',
            'psd' => 'image/vnd.adobe.photoshop',
            'ai' => 'application/postscript',
            'eps' => 'application/postscript',
            'ps' => 'application/postscript',

            // ms office
            'doc' => 'application/msword',
            'rtf' => 'application/rtf',
            'xls' => 'application/vnd.ms-excel',
            'ppt' => 'application/vnd.ms-powerpoint',

            // open office
            'odt' => 'application/vnd.oasis.opendocument.text',
            'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
        );

        $type = isset($mime_types[$this->format]) ? $mime_types[$this->format] : $mime_types['json'];

        header('Content-Type: ' . $type);
    }

    protected function response($data, $raw = false)
    {
        $this->responseMime();
        switch ($this->format) {
            case 'json':
                if (!$raw) {
                    $return = [];
                    $return['status'] = 'success';
                    $return['code'] = 200;
                    $return['data'] = $data;
                } else {
                    $return = $data;
                }
                $data = \json_encode($return);
                break;
        }
        echo $data;
        exit();
    }

    protected function errorResponse(\Exception $e)
    {
        $this->responseMime();
        switch ($this->format) {
            case 'json':
                $return = [];
                $return['status'] = 'error';
                $return['code'] = $e->getCode();
                $return['message'] = $e->getMessage();
                $data = \json_encode($return);
                break;
            default:
                $data = $e->getCode() . ' : ' . $e->getMessage();
                break;
        }
        echo $data;
        exit();
    }

    public function execute($task)
    {
        try {
            $func = Helper::classify($task);
            if (!method_exists($this, $func)) {
                return;
            }
            Helper::loadLanguage('astroid');
            $this->$func();
        } catch (\Exception $e) {
            $this->errorResponse($e);
        }
    }

    protected function checkAuth()
    {
        if (!Session::checkToken()) {
            throw new \Exception(Text::_('ASTROID_AJAX_ERROR'));
        }
    }

    protected function checkAndRedirect()
    {
        $user = Factory::getApplication()->getIdentity();
        if (!$user->id) {
            $uri = Uri::getInstance();
            $return = $uri->toString();
            Factory::getApplication()->redirect(Route::_('index.php?ast=' . urlencode(base64_encode($return))));
        }
    }

    public function onContentPrepareForm($form, &$data)
    {
        $pluginParams = Helper::getPluginParams();
        $astroid_dir = 'libraries/astroid';
        Helper::loadLanguage('astroid');
        $frontendVisibility = $pluginParams->get('frontend_tabs_visibility', 1);

        Form::addFormPath(JPATH_SITE . '/' . $astroid_dir . '/framework/forms');

        $loaded = false;

        if ($form->getName() == 'com_menus.item' && Framework::isAdmin()) {
            $form->loadFile('menu', false);
            $form->loadFile('banner', false);
            $form->loadFile('og', false);
            $form->loadFile('custom', false);
            $loaded = true;
        }

        if ($form->getName() == 'com_content.article' && ((Framework::isSite() && $frontendVisibility) || Framework::isAdmin())) {
            if (Framework::isSite() && isset($data->attribs) && isset($data->params)) {
                $data->attribs = $data->params;
            }
            $form->loadFile('article', false);
            $form->loadFile('blog', false);
            $form->loadFile('opengraph', false);
            $loaded = true;
        }

        if ($form->getName() == 'com_categories.categorycom_content' && ((Framework::isSite() && $frontendVisibility) || Framework::isAdmin())) {
            $form->loadFile('category_blog', false);
            $loaded = true;
        }

        if ($form->getName() == 'com_menus.item' && (isset($data->request['option']) && $data->request['option'] == 'com_content') && (isset($data->request['view']) && $data->request['view'] == 'category') && (isset($data->request['layout']) && $data->request['layout'] == 'blog') && ((Framework::isSite() && $frontendVisibility) || Framework::isAdmin())) {
            $form->loadFile('menu_blog', false);
            $loaded = true;
        }
        if ($form->getName() == 'com_menus.item' && (isset($data->request['option']) && $data->request['option'] == 'com_content') && (isset($data->request['view']) && $data->request['view'] == 'featured') && ((Framework::isSite() && $frontendVisibility) || Framework::isAdmin())) {
            $form->loadFile('menu_blog', false);
            $loaded = true;
        }

        if ($form->getName() == 'com_users.user' || $form->getName() == 'com_admin.profile' && ((Framework::isSite() && $frontendVisibility) || Framework::isAdmin())) {
            $form->loadFile('author', false);
            $loaded = true;
        }

        $version = new Version;
        $version = $version->getShortVersion();
        $version = substr($version, 0, 1);
        if ($version == 4 && $loaded) {
            Factory::getDocument()->addScriptDeclaration('
            (function(){
                var fixed = false;
                var fixTabs = function () {
                    var items = document.querySelectorAll("[href^=\'#attrib-\']");
                    if (items.length) {
                        items.forEach(function (item) {
                            item.innerHTML = item.innerText;
                        });
                        fixed = true;
                    } else {
                        setTimeout(fixTabs, 50);
                    }
                }

                var body = document.body;
                if(body!==null){body.classList.add("astroidOnJ4");}

                window.addEventListener(\'DOMContentLoaded\', function(){
                    setTimeout(fixTabs, 50);
                });
            })();
            ');
        }
    }

    public function onContentBeforeSave($context, $table, $isNew, $data)
    {
        if ($context === 'com_content.form' && Framework::isSite() && !$isNew) {
            $tblClone   = clone($table);

            $tblClone -> reset();
            $tblClone -> load($table -> get($table -> getKeyName()));

            $params = new Registry($tblClone -> get('attribs'));

            $my_attribs = new Registry($data['attribs']);
            $params -> merge($my_attribs);

            $table -> set('attribs', $params -> toString());
        }
        return true;
    }

    public function onInstallerAfterInstaller($package)
    {
        if (!file_exists($package['dir'])) return false;
        $astroidTemplate    =   Helper\Template::isAstroidTemplate($package['dir'] . '/templateDetails.xml');
        if ($astroidTemplate !== false) {
            $xml    =   Helper::getXml($package['dir'] . '/templateDetails.xml');
            if (!$xml || !isset($xml->inheritable)) return false;
            $inheritable = (int) $xml->inheritable;
            if (!$inheritable) {
                $template_name      =   (string) $xml->name;
                $template_parent    =   (string) $xml->parent;
                Helper\Template::prepareChildTemplateDefaults($template_parent, $template_name);
                Helper\Template::uploadTemplateDefaults($template_name);
            }
        }
        return true;
    }
}
