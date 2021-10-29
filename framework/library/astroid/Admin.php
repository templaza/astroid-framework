<?php

/**
 * @package   Astroid Framework
 * @author    JoomDev https://www.joomdev.com
 * @copyright Copyright (C) 2009 - 2020 JoomDev.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

namespace Astroid;

use Astroid\Component\Includer;

defined('_JEXEC') or die;

class Admin extends Helper\Client
{
    public function onAfterRender()
    {
        $this->addTemplateLabels();
    }

    protected function save()
    {
        $this->checkAuth();
        $app = \JFactory::getApplication();

        $params = $app->input->post->get('params', array(), 'RAW');
        $export_settings = $app->input->post->get('export_settings', 0, 'INT');

        if ($export_settings) {
            $this->response($params);
        }

        $template = Framework::getTemplate();

        $params = \json_encode($params);
        Helper::putContents(JPATH_SITE . "/templates/{$template->template}/params" . '/' . $template->id . '.json', $params);

        Helper::refreshVersion();

        $this->response("saved");
    }

    protected function media()
    {
        $action = \JFactory::getApplication()->input->get('action', '', 'RAW');
        $func = Helper::classify($action);
        if (!method_exists(Helper\Media::class, $func)) {
            throw new \Exception("`{$func}` function not found in Astroid\\Helper\\Media");
        }
        $this->response(Helper\Media::$func());
    }

    protected function search()
    {
        $search = \JFactory::getApplication()->input->get('search', '', 'RAW');
        switch ($search) {
            case 'icon':
                $this->response(self::icons());
                break;
            default:
                throw new \Exception('Bad Request', 400);
                break;
        }
    }

    protected function googleFonts()
    {
        $this->format = 'html'; // Response Format
        $this->response(Helper\Font::getAllFonts());
    }

    protected function icons()
    {
        if ($this->format == 'html') {
            $this->format = 'json';
            $return = ['success' => true];
            $return['results'] = Helper\Font::fontAwesomeIcons(true);
            $this->response($return, true);
        } else {
            $this->response(Helper\Font::fontAwesomeIcons());
        }
    }

    protected function manager()
    {
        $this->format = 'html'; // Response Format
        $document = Framework::getDocument();

        Framework::getDebugger()->log('Loading Forms');
        $form = Framework::getForm();
        $form->loadOptions(JPATH_LIBRARIES . '/astroid/framework/options');
        $form->loadOptions(ASTROID_TEMPLATE_PATH . '/astroid/options');
        Framework::getDebugger()->log('Loading Forms');

        $this->checkAndRedirect(); // Auth

        $template = Framework::getTemplate();
        $form->loadParams($template->getParams());

        Framework::getDebugger()->log('Loading Languages');
        Helper::loadLanguage('tpl_' . ASTROID_TEMPLATE_NAME);
        Helper::loadLanguage(ASTROID_TEMPLATE_NAME);
        Helper::loadLanguage('mod_menu');
        Framework::getDebugger()->log('Loading Languages');

        // scripts
        $scripts = ['vendor/jquery/jquery-3.5.1.min.js', 'vendor/jquery/jquery.cookie.js', 'vendor/bootstrap/js/popper.min.js', 'vendor/bootstrap/js/bootstrap.min.old.js', 'vendor/lodash/lodash.min.js', 'vendor/spectrum/spectrum.js', 'vendor/ace/1.3.3/ace.js', 'vendor/dropzone/dropzone.min.js', 'vendor/moment/moment.min.js', 'vendor/moment/moment-timezone.min.js', 'vendor/moment/moment-timezone-with-data-2012-2022.min.js', 'vendor/bootstrap/js/bootstrap-datetimepicker.min.js', 'vendor/bootstrap-slider/js/bootstrap-slider.min.js', 'vendor/angular/angular.min.js', 'vendor/angular/angular-animate.js', 'vendor/angular/sortable.min.js', 'vendor/angular/angular-legacy-sortable.js', 'js/parsley.min.js', 'js/notify.min.js', 'js/jquery.hotkeys.js', 'js/jquery.nicescroll.min.js', 'vendor/semantic-ui/components/transition.min.js', 'vendor/semantic-ui/components/api.min.js', 'vendor/semantic-ui/components/dropdown.min.js', 'js/astroid.min.js'];
        $document->addScript($scripts, 'body');
        $document->addScriptDeclaration('moment.tz.setDefault(\'' . \JFactory::getConfig()->get('offset') . '\');', 'body');

        // styles
        $stylesheets = ['https://fonts.googleapis.com/css?family=Nunito:300,400,600', 'css/astroid-framework.css', 'css/admin.css', 'css/animate.min.css', 'vendor/semantic-ui/components/icon.min.css', 'vendor/semantic-ui/components/transition.min.css', 'vendor/semantic-ui/components/dropdown.min.css'];
        $document->addStyleSheet($stylesheets);

        Helper::triggerEvent('onBeforeAstroidAdminRender', [&$template]);

        Framework::getDebugger()->log('Getting Manager');
        $layout = new \JLayoutFile('manager.index', ASTROID_LAYOUTS);
        $html = $layout->render();
        $html = Includer::run($html);
        Framework::getDebugger()->log('Getting Manager');
        $this->response($html);
    }

    protected function auditor()
    {
        $this->format = 'html'; // Response Format
        $document = Framework::getDocument();

        $this->checkAndRedirect(); // Auth

        // scripts
        $scripts = ['vendor/jquery/jquery-3.2.1.min.js', 'vendor/jquery/jquery.cookie.js', 'vendor/bootstrap/js/popper.min.js', 'vendor/bootstrap/js/bootstrap.min.old.js', 'vendor/lodash/lodash.min.js', 'vendor/angular/angular.min.js', 'vendor/angular/angular-animate.js', 'vendor/angular/sortable.min.js', 'vendor/angular/angular-legacy-sortable.js', 'js/notify.min.js', 'js/jquery.hotkeys.js', 'js/jquery.nicescroll.min.js', 'js/astroid.min.js'];
        $document->addScript($scripts, 'body');

        // styles
        $stylesheets = ['https://fonts.googleapis.com/css?family=Nunito:300,400,600', 'css/astroid-framework.css', 'css/admin.css'];
        $document->addStyleSheet($stylesheets);

        Framework::getDebugger()->log('Getting Auditor');
        $layout = new \JLayoutFile('auditor.index', ASTROID_LAYOUTS);
        $html = $layout->render();
        $html = Includer::run($html);
        Framework::getDebugger()->log('Getting Auditor');
        $this->response($html);
    }

    protected function audit()
    {
        $template = \JFactory::getApplication()->input->post->get('template', '', 'RAW');
        $this->response(Auditor::audit($template));
    }

    protected function migrate()
    {
        $template = \JFactory::getApplication()->input->get->get('template', '', 'RAW');
        $this->response(Auditor::migrate($template));
    }

    protected function clearCache()
    {
        $template = Framework::getTemplate()->template;
        Helper::clearCacheByTemplate($template);
        $this->response(['message' => \JText::_('TPL_ASTROID_SYSTEM_MESSAGES_CACHE')]);
    }

    protected function clearJoomlaCache()
    {
        Helper::clearJoomlaCache();
        $this->response(['message' => \JText::_('TPL_ASTROID_SYSTEM_MESSAGES_JCACHE')]);
    }

    public function addTemplateLabels()
    {
        $app = \JFactory::getApplication();
        $option = $app->input->get('option', '');
        $view = $app->input->get('view', '');
        if (!($option == 'com_templates' && ($view == 'styles' || empty($view)))) {
            return;
        }

        $body = $app->getBody();
        $astroid_templates = Helper\Template::getAstroidTemplates();
        $body = preg_replace_callback('/(<a\s[^>]*href=")([^"]*)("[^>]*>)(.*)(<\/a>)/siU', function ($matches) use ($astroid_templates) {
            $html = $matches[0];
            if (strpos($matches[2], 'task=style.edit')) {
                $uri = new \JUri($matches[2]);
                $id = (int) $uri->getVar('id');
                if ($id && in_array($uri->getVar('option'), array('com_templates')) && (in_array($id, $astroid_templates))) {
                    $html = $matches[1] . $uri . $matches[3] . $matches[4] . $matches[5];
                    $html .= ' <span class="label" style="background: rgba(0, 0, 0, 0) linear-gradient(to right, #8E2DE2, #4A00E0) repeat scroll 0 0; color:#fff;padding-left: 10px;padding-right: 10px;margin-left: 5px;border-radius: 30px;box-shadow: 0 0 20px rgba(0, 0, 0, 0.20);display: inline-block;">Astroid</span>';
                }
            }
            return $html;
        }, $body);
        $app->setBody($body);
    }
}
