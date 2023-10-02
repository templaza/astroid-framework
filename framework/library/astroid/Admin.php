<?php

/**
 * @package   Astroid Framework
 * @author    Astroid Framework Team https://astroidframe.work
 * @copyright Copyright (C) 2023 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

namespace Astroid;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
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

        $astroid_preset = $app->input->post->get('astroid-preset', 0, 'INT');
        if ($astroid_preset) {
            $preset = [
                'title' => $app->input->post->get('astroid-preset-name', '', 'RAW'),
                'desc' => $app->input->post->get('astroid-preset-desc', '', 'RAW'),
                'thumbnail' => '', 'demo' => '',
                'preset' => $params
            ];
            $preset_name = uniqid(\JFilterOutput::stringURLSafe($preset['title']).'-');
            Helper::putContents(JPATH_SITE . "/media/templates/site/{$template->template}/astroid/presets/" . $preset_name . '.json', \json_encode($preset));
            $this->response($preset_name);
        } else {
            Helper::putContents(JPATH_SITE . "/media/templates/site/{$template->template}/params" . '/' . $template->id . '.json', $params);
            Helper::refreshVersion();
            $this->response("saved");
        }
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
        $template = Framework::getTemplate();
 
        Framework::getDebugger()->log('Loading Forms');
        $form = Framework::getForm();
        Helper::triggerEvent('onBeforeAstroidFormLoad', [&$template, &$form]);
        $form->loadOptions(JPATH_LIBRARIES . '/astroid/framework/options');
        Helper::triggerEvent('onBeforeAstroidTemplateFormLoad', [&$template, &$form]);
        $form->loadOptions(ASTROID_TEMPLATE_PATH . '/astroid/options');
        $form->loadOptions(ASTROID_MEDIA_TEMPLATE_PATH . '/astroid/options');
        Helper::triggerEvent('onAfterAstroidTemplateFormLoad', [&$template, &$form]);
        Framework::getDebugger()->log('Loading Forms');

        $this->checkAndRedirect(); // Auth
        $form->loadParams($template->getParams());

        Framework::getDebugger()->log('Loading Languages');
        Helper::loadLanguage('tpl_' . ASTROID_TEMPLATE_NAME);
        Helper::loadLanguage(ASTROID_TEMPLATE_NAME);
        Helper::loadLanguage('mod_menu');
        Framework::getDebugger()->log('Loading Languages');
        $document->addScript('vendor/manager/dist/index.js', 'body', [], [], 'module');
        $doc = Factory::getDocument();
        $config = [
            'site_url'              =>  \JURI::root(),
            'base_url'              =>  \JURI::base(true),
            'astroid_media_url'     => ASTROID_MEDIA_URL,
            'template_name'         => $template->template.'-'.$template->id,
            'tpl_template_name'     => $template->template,
            'template_title'        => $template->title,
            'astroid_version'       => Helper\Constants::$astroid_version,
            'astroid_link'          => Helper\Constants::$astroid_link,
            'document_link'         => Helper\Constants::$documentation_link,
            'video_tutorial'        => Helper\Constants::$video_tutorial_link,
            'github_link'           => Helper\Constants::$github_link,
            'jtemplate_link'        => Helper::getJoomlaUrl(),
            'astroid_admin_token'   => \JSession::getFormToken(),
            'astroid_action'        => Helper::getAstroidUrl('save', ['template' => $template->template . '-' . $template->id])
        ];
        $doc->addScriptOptions('astroid_lib', $config);

        // Get Language
        $lang = array();
        foreach (Helper\Constants::$translationStrings as $string) {
            $lang[strtoupper($string)] = Factory::getLanguage()->_($string);
        }
        $doc->addScriptOptions('astroid_lang', $lang);

        // Prepare content
        $form_content = array();
        $form = Framework::getForm();
        foreach ($form->getFieldsets() as $key => $fieldset) {
            $fields = $form->getFields($key);

            // Ordering
            $fieldsArr = [];
            $order = 1;
            $orders = [];
            $reorders = [];

            foreach ($fields as $key => $field) {
                // Ordering
                $ordering = $field->getAttribute('after', '');
                if (empty($ordering)) {
//                    $field->ordering = $order++;
                    $fieldsArr[] = $field;
                    $orders[$field->name] = $order++;
                } else {
                    if (isset($orders[$ordering])) {
//                        $field->ordering = $orders[$ordering];
                        $fieldsArr[] = $field;
                        $orders[$field->name] = $orders[$ordering];

                    } else {
                        $reorders[] = $field;

                    }
                }
            }
            // Reorder group
            foreach ($reorders as &$reorder) {
                $ordering = $reorder->getAttribute('after', '');
                $reorder->ordering = $orders[$ordering];
                $fieldsArr[] = $reorder;
            }

//            usort($fieldsArr, 'Astroid\Helper::orderingFields');

            $groups = [];
            foreach ($fieldsArr as $key => $field) {
                if ($field->type == 'astroidgroup') {
                    $groups[$field->fieldname] = ['title' => Text::_($field->getAttribute('title', '')), 'icon' => $field->getAttribute('icon', ''), 'description' => Text::_($field->getAttribute('description', '')), 'fields' => [], 'help' => $field->getAttribute('help', ''), 'preset' => $field->getAttribute('preset', '')];
                }
            }

            $groups['none'] = ['fields' => []];

            foreach ($fieldsArr as $key => $field) {
                if ($field->type == 'astroidgroup') {
                    continue;
                }

                if (empty($field->getAttribute('name'))) {
                    continue;
                }
                $input = $field->input ? trim(str_replace('ng-media-class', 'ng-class', $field->input)) : $field->input;
                $js_input   =   json_decode($input);
                $field_group = $field->getAttribute('astroidgroup', 'none');
                $field_tmp  =   [
                    'id'            =>  $field->id,
                    'name'          =>  $field->fieldname,
                    'value'         =>  $field->value,
                    'label'         =>  Text::_($field->getAttribute('label')),
                    'description'   =>  Text::_($field->getAttribute('description')),
                    'input'         =>  $input,
                    'type'          =>  'string',
                    'group'         =>  $fieldset->name,
                    'ngShow'        =>  Helper::replaceRelationshipOperators($field->getAttribute('ngShow')),
                ];

                if (json_last_error() === JSON_ERROR_NONE) {
                    $field_tmp['input']     =   $js_input;
                    $field_tmp['type']      =   'json';
                }
                $groups[$field_group]['fields'][] = $field_tmp;

            }
            // Get sidebar data
            $fieldset->label    = Text::_($fieldset->label);
            $fieldset->childs   = $groups;
            $form_content[] = $fieldset;

            $presets    =   Helper::getPresets();
        }
        $doc->addScriptOptions('astroid_content', $form_content);

        // styles
        $stylesheets = ['vendor/manager/dist/index.css'];
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
        $scripts = ['vendor/jquery/jquery-3.2.1.min.js', 'vendor/jquery/jquery.cookie.js', 'vendor/bootstrap/js/popper.min.js', 'vendor/bootstrap/js/bootstrap.min.old.js', 'vendor/lodash/lodash.min.js', 'vendor/angular/angular.min.js', 'vendor/angular/angular-animate.min.js', 'vendor/angular/sortable.min.js', 'vendor/angular/angular-legacy-sortable.js', 'js/notify.min.js', 'js/jquery.hotkeys.js', 'js/jquery.nicescroll.min.js', 'js/astroid.min.js'];
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
	    $app = \JFactory::getApplication();
	    $tpl = $app->input->get('template', '');
		if (empty($tpl)) {
			\JLoader::import('joomla.filesystem.folder');
			$tpl_folders    =   \JFolder::folders(JPATH_ROOT.DIRECTORY_SEPARATOR.'templates');
			if ($tpl_folders && count($tpl_folders)) {
				foreach ($tpl_folders as $tpl_item) {
					if (file_exists(JPATH_ROOT.DIRECTORY_SEPARATOR.'media/templates/site'.DIRECTORY_SEPARATOR.$tpl_item.DIRECTORY_SEPARATOR.'astroid'.DIRECTORY_SEPARATOR.'default.json') || file_exists(JPATH_ROOT.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.$tpl_item.DIRECTORY_SEPARATOR.'astroid'.DIRECTORY_SEPARATOR.'default.json')) {
						Helper::clearCacheByTemplate($tpl_item);
					}
				}
			}
		} else {
			$template = Framework::getTemplate()->template;
			Helper::clearCacheByTemplate($template);
		}
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

    public function importpreset() {
        try {
            // Check for request forgeries.
            if (!\JSession::checkToken()) {
                throw new \Exception(\JText::_('JINVALID_TOKEN'));
            }
            $app = \JFactory::getApplication();
            $template_name  = $app->input->get('template', NULL, 'RAW');
            $presets_path   = JPATH_SITE . "/media/templates/site/$template_name/astroid/presets/";
            $preset = [
                'title' => $app->input->post->get('title', '', 'RAW'),
                'desc' => $app->input->post->get('desc', '', 'RAW'),
                'thumbnail' => '', 'demo' => '',
                'preset' => ''
            ];
            $preset_name = uniqid(\JFilterOutput::stringURLSafe($preset['title']).'-');
            $fieldName = 'file';

            $fileError = $_FILES[$fieldName]['error'];
            if ($fileError > 0) {
                switch ($fileError) {
                    case 1:
                        throw new \Exception(\JText::_('ASTROID_ERROR_LARGE_FILE'));
                        break;

                    case 2:
                        throw new \Exception(\JText::_('ASTROID_ERROR_FILE_HTML_ALLOW'));
                        break;

                    case 3:
                        throw new \Exception(\JText::_('ASTROID_ERROR_FILE_PARTIAL_ALLOW'));
                        break;

                    case 4:
                        throw new \Exception(\JText::_('ASTROID_ERROR_NO_FILE'));
                        break;
                }
            }

            $pathinfo = pathinfo($_FILES[$fieldName]['name']);
            $uploadedFileExtension = $pathinfo['extension'];
            $uploadedFileExtension = strtolower($uploadedFileExtension);
            if ($uploadedFileExtension != 'json') {
                throw new \Exception(\JText::_('INVALID EXTENSION'));
            }

            $fileTemp = $_FILES[$fieldName]['tmp_name'];
            $json           = file_get_contents($fileTemp);
            $config         = json_decode($json, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                if (!isset($config['preset'])) {
                    $preset['preset'] = $json;
                } else {
                    $preset['preset'] = $config['preset'];
                }
            } else {
                throw new \Exception(\JText::_('INVALID FILETYPE'));
            }

            $uploadPath = $presets_path . $preset_name . '.json';

            Helper::putContents($uploadPath, \json_encode($preset));
            unlink($fileTemp);
            $this->response($preset_name);
        } catch (\Exception $e) {
            $this->errorResponse($e);
        }
        return true;
    }

    public function loadpreset() {
        try {
            // Check for request forgeries.
            if (!\JSession::checkToken()) {
                throw new \Exception(\JText::_('JINVALID_TOKEN'));
            }
            $app = \JFactory::getApplication();
            $template_name  = $app->input->get('template', NULL, 'RAW');
            $presets_path   = JPATH_SITE . "/media/templates/site/$template_name/astroid/presets/";
            $file           = $app->input->post->get('name', '', 'RAW');
            $json           = file_get_contents($presets_path.$file.'.json');
            if (!$json) {
                throw new \Exception(\JText::_('JOLLYANY_LOAD_PRESET_FILE_ERROR').': '.$presets_path.$file.'.json');
            }
            $data = \json_decode($json, true);
            if (!isset($data['preset']) || empty($data['preset'])) {
                throw new \Exception(\JText::_('JOLLYANY_PRESET_EMPTY_ERROR'));
            }
            $this->response($data['preset']);
        } catch (\Exception $e) {
            $this->errorResponse($e);
        }
        return true;
    }

    public function removepreset() {
        try {
            // Check for request forgeries.
            if (!\JSession::checkToken()) {
                throw new \Exception(\JText::_('JOLLYANY_AJAX_ERROR'));
            }
            $app = \JFactory::getApplication();
            $template_name  = $app->input->get('template', NULL, 'RAW');
            $presets_path   = JPATH_SITE . "/media/templates/site/$template_name/astroid/presets/";
            $file           = $app->input->post->get('name', '', 'RAW');
            $file_name      = $presets_path.$file.'.json';
            jimport('joomla.filesystem.file');
            if (\JFile::exists($file_name)) {
                \JFile::delete($file_name);
            }
            $this->response('Preset Removed!');
        } catch (\Exception $e) {
            $this->errorResponse($e);
        }
        return true;
    }
}
