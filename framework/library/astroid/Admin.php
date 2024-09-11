<?php

/**
 * @package   Astroid Framework
 * @author    Astroid Framework Team https://astroidframe.work
 * @copyright Copyright (C) 2023 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

namespace Astroid;
use Astroid\Element\Layout;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\Filesystem\Folder;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Uri\Uri;
use Astroid\Component\Includer;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Filter\OutputFilter;
use Astroid\Component\Utility;

defined('_JEXEC') or die;

class Admin extends Helper\Client
{
    public function onAfterRender()
    {
        $this->addTemplateLabels();
    }

    public function onBeforeRender()
    {
        Utility::showFreeTemplate();
    }

    protected function save()
    {
        $this->checkAuth();
        $app = Factory::getApplication();
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
            $preset_name = uniqid(OutputFilter::stringURLSafe($preset['title']).'-');
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
        $this->checkAuth();
        $action = Factory::getApplication()->input->get('action', '', 'RAW');
        $func = Helper::classify($action);
        if (!method_exists(Helper\Media::class, $func)) {
            throw new \Exception("`{$func}` function not found in Astroid\\Helper\\Media");
        }
        $this->response(Helper\Media::$func());
    }

    protected function getLayouts()
    {
        $app = Factory::getApplication();
        $template_name  = $app->input->get('template', NULL, 'RAW');
        $type           = $app->input->get('type', 'layouts', 'RAW');
        $this->response(Layout::getDatalayouts($template_name, $type));
    }

    protected function getLayout()
    {
        try {
            // Check for request forgeries.
            $this->checkAuth();
            $app            = Factory::getApplication();
            $template_name  = $app->input->get('template', NULL, 'RAW');
            $filename       = $app->input->get('name', NULL, 'RAW');
            $type           = $app->input->get('type', 'layouts', 'RAW');

            $this->response(Layout::getDataLayout($filename, $template_name, $type));
        } catch (\Exception $e) {
            $this->errorResponse($e);
        }
        return true;
    }

    protected function saveLayout() {
        try {
            // Check for request forgeries.
            $this->checkAuth();
            $app            = Factory::getApplication();
            $template_name  = $app->input->get('template', NULL, 'RAW');
            $filename       = $app->input->get('name', NULL, 'RAW');
            $type           = $app->input->get('type', 'layouts', 'RAW');
            $layout_path    = JPATH_SITE . "/media/templates/site/$template_name/astroid/$type/";

            $layout = [
                'title' => $app->input->post->get('title', '', 'RAW'),
                'desc' => $app->input->post->get('desc', '', 'RAW'),
                'thumbnail' => $app->input->post->get('thumbnail_old', '', 'RAW'),
                'data' => $app->input->post->get('data', '{"sections":[]}', 'RAW'),
            ];
            $default = $app->input->post->get('default', '', 'RAW');

            if ($default === 'true') {
                $layout_name = 'default';
            } elseif (!$filename) {
                $layout_name = uniqid(OutputFilter::stringURLSafe($layout['title']).'-');
            } else {
                $layout_name = $filename;
            }

            $thumbnail_file =   $app->input->files->get('thumbnail', NULL, 'raw');

            if (\is_array($thumbnail_file)) {
                // Make sure that file uploads are enabled in php.
                if (!(bool) \ini_get('file_uploads')) {
                    throw new \Exception('File upload is not enabled in PHP', 400);
                }
                // Is the PHP tmp directory missing?
                if ($thumbnail_file['error'] && ($thumbnail_file['error'] == UPLOAD_ERR_NO_TMP_DIR)) {
                    throw new \Exception('There was an error uploading this thumbnail to the server.', 400);
                }
                $pathinfo = pathinfo($thumbnail_file['name']);
                $uploadedFileExtension = $pathinfo['extension'];
                $uploadedFileExtension = strtolower($uploadedFileExtension);
                $validExts  =   ['jpg', 'jpeg', 'png', 'bmp'];
                if (!in_array($uploadedFileExtension, $validExts)) {
                    throw new \Exception(Text::_('INVALID EXTENSION'));
                }

                $fileTemp       = $thumbnail_file['tmp_name'];
                $thumbnail      = file_get_contents($fileTemp);
                if ($layout['thumbnail'] != '' && file_exists(JPATH_SITE . "/media/templates/site/$template_name/images/$type/".$layout['thumbnail'])) {
                    unlink(JPATH_SITE . "/media/templates/site/$template_name/images/$type/".$layout['thumbnail']);
                }
                $layout['thumbnail'] = $layout_name.'.'.$uploadedFileExtension;

                Helper::putContents(JPATH_SITE . "/media/templates/site/$template_name/images/$type/".$layout['thumbnail'], $thumbnail);
                unlink($fileTemp);
            }
            Helper::putContents($layout_path . $layout_name . '.json', \json_encode($layout));
            $this->response($layout_name);
        } catch (\Exception $e) {
            $this->errorResponse($e);
        }
        return true;
    }

    protected function deleteLayouts()
    {
        try {
            // Check for request forgeries.
            $this->checkAuth();
            $app            = Factory::getApplication();
            $template_name  = $app->input->get('template', NULL, 'RAW');
            $layouts        = $app->input->get('layouts', array(), 'RAW');
            $type           = $app->input->get('type', 'layouts', 'RAW');

            $this->response(Layout::deleteDatalayouts($layouts, $template_name, $type));
        } catch (\Exception $e) {
            $this->errorResponse($e);
        }
        return true;
    }

    protected function getcategories()
    {
        $this->response(Utility::getCategories());
    }

    protected function search()
    {
        $search = Factory::getApplication()->input->get('search', '', 'RAW');
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

    protected function icons() : void
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
        $form->loadOptions(JPATH_SITE . '/templates/' . $template->template . '/astroid/options');
        $form->loadOptions(JPATH_SITE . '/media/templates/site/' . $template->template . '/astroid/options');
        Helper::triggerEvent('onAfterAstroidTemplateFormLoad', [&$template, &$form]);
        Framework::getDebugger()->log('Loading Forms');

        $this->checkAndRedirect(); // Auth
        $form->loadParams($template->getParams());

        Framework::getDebugger()->log('Loading Languages');
        Helper::loadLanguage('tpl_' . ASTROID_TEMPLATE_NAME);
        Helper::loadLanguage(ASTROID_TEMPLATE_NAME);
        Helper::loadLanguage('mod_menu');
        Framework::getDebugger()->log('Loading Languages');
        $document->addScript('media/system/js/core.min.js');
        $document->addScript('media/system/js/keepalive.min.js');
        $document->addScript('vendor/bootstrap/js/bootstrap.bundle.min.js');
        $document->addScript('vendor/manager/dist/index.js', 'body', [], [], 'module');
        $pluginParams   =   Helper::getPluginParams();
        $plg_color_mode =   $pluginParams->get('astroid_color_mode_enable', 0);

        $config = Helper\Constants::manager_configs();
        $document->addScriptOptions('astroid_lib', $config);

        // Get Language
        $lang = array();
        foreach (Helper\Constants::$translationStrings as $string) {
            $lang[strtoupper($string)] = Factory::getApplication()->getLanguage()->_($string);
        }
        $document->addScriptOptions('astroid_lang', $lang);

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
                    $fieldsArr[] = $field;
                    $orders[$field->name] = $order++;
                } else {
                    if (isset($orders[$ordering])) {
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

            $groups = [];
            foreach ($fieldsArr as $key => $field) {
                if ($field->type == 'astroidgroup') {
                    if (!$plg_color_mode && $field->fieldname == 'colormode') {
                        continue;
                    }
                    $groups[$field->fieldname] = ['title' => Text::_($field->getAttribute('title', '')), 'icon' => $field->getAttribute('icon', ''), 'description' => Text::_($field->getAttribute('description', '')), 'fields' => [], 'help' => $field->getAttribute('help', ''), 'preset' => $field->getAttribute('preset', ''), 'option-type' => $field->getAttribute('option-type', '')];
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
                if (!$plg_color_mode && $field_group == 'colormode') {
                    continue;
                }

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
        }
        $document->addScriptOptions('astroid_content', $form_content);

        // styles
        $stylesheets = ['vendor/manager/dist/index.css', 'media/astroid/assets/vendor/fontawesome/css/all.min.css', 'media/astroid/assets/vendor/linearicons/font.min.css'];
        $document->addStyleSheet($stylesheets);
        $document->addStyleSheet('https://fonts.gstatic.com', ['rel' => 'preconnect']);

        Helper::triggerEvent('onBeforeAstroidAdminRender', [&$template]);

        Framework::getDebugger()->log('Getting Manager');
        $layout = new FileLayout('manager.index', ASTROID_LAYOUTS);
        $html = $layout->render();
        $html = Includer::run($html);
        Framework::getDebugger()->log('Getting Manager');
        $this->response($html);
    }

    protected function getArticleFormTemplate()
    {
        try {
            $app            = Factory::getApplication();
            $template_id    = $app->input->get('id', NULL, 'RAW');
            $this->response(Helper::getFormTemplate('article', $template_id));
        } catch (\Exception $e) {
            $this->errorResponse($e);
        }
        return true;
    }

    protected function clearCache()
    {
	    $app = Factory::getApplication();
	    $tpl = $app->input->get('template', '');
		if (empty($tpl)) {
			$tpl_folders    =   Folder::folders(JPATH_ROOT.DIRECTORY_SEPARATOR.'templates');
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
        $this->response(['message' => Text::_('TPL_ASTROID_SYSTEM_MESSAGES_CACHE')]);
    }

    protected function clearJoomlaCache()
    {
        Helper::clearJoomlaCache();
        $this->response(['message' => Text::_('TPL_ASTROID_SYSTEM_MESSAGES_JCACHE')]);
    }

    public function addTemplateLabels()
    {
        $app = Factory::getApplication();
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
                $uri = new Uri($matches[2]);
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
            $this->checkAuth();
            $app = Factory::getApplication();
            $template_name  = $app->input->get('template', NULL, 'RAW');
            $presets_path   = JPATH_SITE . "/media/templates/site/$template_name/astroid/presets/";
            $preset = [
                'title' => $app->input->post->get('title', '', 'RAW'),
                'desc' => $app->input->post->get('desc', '', 'RAW'),
                'thumbnail' => '', 'demo' => '',
                'preset' => ''
            ];
            $preset_name = uniqid(OutputFilter::stringURLSafe($preset['title']).'-');

            $file =   $app->input->files->get('file', NULL, 'raw');

            $fileError = $file['error'];
            if ($fileError > 0) {
                switch ($fileError) {
                    case 1:
                        throw new \Exception(Text::_('ASTROID_ERROR_LARGE_FILE'));
                        break;

                    case 2:
                        throw new \Exception(Text::_('ASTROID_ERROR_FILE_HTML_ALLOW'));
                        break;

                    case 3:
                        throw new \Exception(Text::_('ASTROID_ERROR_FILE_PARTIAL_ALLOW'));
                        break;

                    case 4:
                        throw new \Exception(Text::_('ASTROID_ERROR_NO_FILE'));
                        break;
                }
            }

            $pathinfo = pathinfo($file['name']);
            $uploadedFileExtension = $pathinfo['extension'];
            $uploadedFileExtension = strtolower($uploadedFileExtension);
            if ($uploadedFileExtension != 'json') {
                throw new \Exception(Text::_('INVALID EXTENSION'));
            }

            $fileTemp = $file['tmp_name'];
            $json           = file_get_contents($fileTemp);
            $config         = json_decode($json, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                if (!isset($config['preset'])) {
                    $preset['preset'] = $json;
                } else {
                    $preset['preset'] = $config['preset'];
                }
            } else {
                throw new \Exception(Text::_('INVALID FILETYPE'));
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
            $this->checkAuth();
            $app = Factory::getApplication();
            $template_name  = $app->input->get('template', NULL, 'RAW');
            $presets_path   = JPATH_SITE . "/media/templates/site/$template_name/astroid/presets/";
            $file           = $app->input->post->get('name', '', 'RAW');
            $json           = file_get_contents($presets_path.$file.'.json');
            if (!$json) {
                throw new \Exception(Text::_('JOLLYANY_LOAD_PRESET_FILE_ERROR').': '.$presets_path.$file.'.json');
            }
            $data = \json_decode($json, true);
            if (!isset($data['preset']) || empty($data['preset'])) {
                throw new \Exception(Text::_('JOLLYANY_PRESET_EMPTY_ERROR'));
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
            $this->checkAuth();
            $app = Factory::getApplication();
            $template_name  = $app->input->get('template', NULL, 'RAW');
            $presets_path   = JPATH_SITE . "/media/templates/site/$template_name/astroid/presets/";
            $file           = $app->input->post->get('name', '', 'RAW');
            $file_name      = $presets_path.$file.'.json';
            jimport('joomla.filesystem.file');
            if (File::exists($file_name)) {
                File::delete($file_name);
            }
            $this->response('Preset Removed!');
        } catch (\Exception $e) {
            $this->errorResponse($e);
        }
        return true;
    }

    public function getFreeTemplates()
    {
        try {
            // Check for request forgeries.
            $json_file   = JPATH_SITE . "/media/astroid/assets/json/templates.json";
            if (file_exists($json_file)) {
                $json = file_get_contents($json_file);
                $this->response(\json_decode($json, true));
            } else {
                throw new \Exception(Text::_('Template file is not available'));
            }
        } catch (\Exception $e) {
            $this->errorResponse($e);
        }
        return true;
    }

    public function installTemplate() {
        try {
            // Check for request forgeries.
            $this->checkAuth();
            $app = Factory::getApplication();
            $template_url  = $app->input->get('url', '', 'RAW');
            $result = Helper\Install::InstallUrl($template_url);
            if (!is_array($result)) {
                throw new \Exception(Text::_('ASTROID_UNABLE_TO_FIND_INSTALL_PACKAGE'));
            }

            if ($result['type'] == 'error') {
                throw new \Exception($result['msg']);
            }
            $app->enqueueMessage($result['msg'], 'error');
            $this->response($result['msg']);
        } catch (\Exception $e) {
            $this->errorResponse($e);
        }
        return true;
    }
}
