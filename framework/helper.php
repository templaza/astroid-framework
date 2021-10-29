<?php

/**
 * @package   Astroid Framework
 * @author    JoomDev https://www.joomdev.com
 * @copyright Copyright (C) 2009 - 2020 JoomDev.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */
defined('_JEXEC') or die;
jimport('astroid.framework.constants');
jimport('astroid.framework.template');
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.element');

use ScssPhp\ScssPhp\Compiler;

\JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_cache/models', 'CacheModel');

class AstroidFrameworkHelper
{

   public static function getAstroidElements()
   {

      $elements_dir = JPATH_LIBRARIES . '/' . 'astroid' . '/' . 'framework' . '/' . 'elements' . '/';
      $template_elements_dir = JPATH_SITE . '/' . 'templates' . '/' . ASTROID_TEMPLATE_NAME . '/' . 'astroid' . '/' . 'elements' . '/';
      $elements = array_filter(glob($elements_dir . '*'), 'is_dir');
      $template_elements = array_filter(glob($template_elements_dir . '*'), 'is_dir');
      $elements = array_merge($elements, $template_elements);
      $return = array();

      $defaultxml = JPATH_LIBRARIES . '/' . 'astroid' . '/' . 'framework' . '/' . 'elements' . '/' . 'default.xml';

      $xml = simplexml_load_file($defaultxml);
      $default = self::getElementConfig($xml, "");

      foreach ($elements as $element_dir) {
         $xmlfile = $element_dir . '/' . (str_replace($template_elements_dir, '', str_replace($elements_dir, '', $element_dir))) . '.xml';
         if (file_exists($xmlfile)) {
            $xml = simplexml_load_file($xmlfile);
            $type = str_replace($template_elements_dir, '', str_replace($elements_dir, '', $element_dir));
            $element = self::getElementConfig($xml, $type, $default['icon'], $default['description'], $default['color'], $default['multiple'], $default['options']);
            $return[] = $element;
         }
      }
      //exit;
      return $return;
   }

   public static function getJoomlaVersion()
   {
      $version = new \JVersion;
      $version = $version->getShortVersion();
      $version = substr($version, 0, 1);
      return $version;
   }

   public static function getAllAstroidElements()
   {

      // Template Directories
      $elements_dir = JPATH_LIBRARIES . '/' . 'astroid' . '/' . 'framework' . '/' . 'elements' . '/';
      $template_elements_dir = JPATH_SITE . '/' . 'templates' . '/' . ASTROID_TEMPLATE_NAME . '/' . 'astroid' . '/' . 'elements' . '/';

      // Getting Elements from Template Directories
      $elements = array_filter(glob($elements_dir . '*'), 'is_dir');
      $template_elements = array_filter(glob($template_elements_dir . '*'), 'is_dir');

      // Merging Elements
      $elements = array_merge($elements, $template_elements);

      $return = array();

      foreach ($elements as $element_dir) {
         $xmlfile = $element_dir . '/' . (str_replace($template_elements_dir, '', str_replace($elements_dir, '', $element_dir))) . '.xml';
         if (file_exists($xmlfile)) {
            $type = str_replace($template_elements_dir, '', str_replace($elements_dir, '', $element_dir));

            $template = new \stdClass();
            $template->template = ASTROID_TEMPLATE_NAME;
            $template->params = new \stdClass();
            $template = new AstroidFrameworkTemplate($template);
            $element = new AstroidElement($type, [], $template);
            $return[] = $element;
         }
      }
      //exit;
      return $return;
   }

   public static function getElementClassName($type)
   {
      $type = str_replace('-', ' ', $type);
      $type = str_replace('_', ' ', $type);
      $type = ucwords(strtolower($type));
      return 'AstroidElement' . str_replace(' ', '', $type);
   }

   public static function getTemplatePostions()
   {
      $db = JFactory::getDbo();
      $query = "SELECT * FROM `#__modules` GROUP BY `position`";

      $query = $db->getQuery(true)
         ->select('DISTINCT(position)')
         ->from('#__modules')
         ->where($db->quoteName('client_id') . ' = ' . 0)
         ->order('position');

      $db->setQuery($query);
      $results = $db->loadObjectList();
      $return = array();
      $return[] = array('name' => "None", 'value' => "");


      $exists = [];
      for ($i = 1; $i <= 10; $i++) {
         $return[] = array('name' => "position-" . $i, 'value' => "position-" . $i);
         $exists[] = "position-" . $i;
      }

      foreach ($results as $result) {
         if (empty($result->position) || in_array($result->position, $exists)) {
            continue;
         }
         $return[] = array('name' => $result->position, 'value' => $result->position);
      }

      return $return;
   }

   public static function getAnimationStyles()
   {
      $groups = AstroidFrameworkConstants::$animations;
      $return = array();
      foreach ($groups as $group => $animations) {
         foreach ($animations as $key => $value) {
            $return[] = array('name' => $value, 'value' => $key, 'group' => $group);
         }
      }
      return $return;
   }

   public static function getTemplateById($id = null)
   {
      $db = JFactory::getDbo();
      $query = "SELECT * FROM `#__template_styles` WHERE `id`='$id'";
      $db->setQuery($query);
      $result = $db->loadObject();
      if (!empty($result)) {
         $template = new AstroidFrameworkTemplate($result);
         return $template;
      } else {
         return null;
      }
   }

   public static function compileSass($sass_path, $css_path, $sass, $css, $variables = array())
   {
      try {
         require_once JPATH_LIBRARIES . '/' . 'astroid' . '/' . 'framework' . '/' . 'library' . '/' . 'scssphp' . '/' . 'scss.inc.php';
         $scss = new Compiler();
         $scss->setImportPaths($sass_path);
         $scss->setFormatter('ScssPhp\ScssPhp\Formatter\Compressed');
         if (!empty($variables)) {
            $scss->setVariables($variables);
         }
         $content = $scss->compile('@import "' . $sass . '";');
         file_put_contents($css_path . '/' . $css, $content);
      } catch (\Exception $e) {
         print_r($e);
         exit;
         echo '<h1>' . $e->getMessage() . '</h1>';
         echo '<h3>' . $e->getFile() . ' in ' . $e->getLine() . '</h3>';
         exit;
      }
   }

   public static function AstroidMedia($action)
   {
      $data = null;
      switch ($action) {
         case "library":
            $params = JComponentHelper::getParams('com_media');
            $input = JFactory::getApplication()->input;
            $folder = $input->get('folder', '', 'RAW');
            $data = self::getMediaLibrary();
            $data['current_folder'] = $params->get('image_path', 'images') . (empty($folder) ? '' : '/' . $folder);
            break;
         case "upload":
            $data = self::uploadMedia();
            break;
         case "create-folder":
            $data = self::createFolder();
            break;
         default:
            throw new \Exception("Bad Request", 400);
            break;
      }
      return $data;
   }

   public static function getMediaLibrary()
   {
      $input = JFactory::getApplication()->input;
      $user = JFactory::getUser();
      $asset = $input->get('asset');
      $author = $input->get('author');

      if (!$user->authorise('core.manage', 'com_media') && (!$asset || (!$user->authorise('core.edit', $asset) && !$user->authorise('core.create', $asset) && count($user->getAuthorisedCategories($asset, 'core.create')) == 0) && !($user->id == $author && $user->authorise('core.edit.own', $asset)))) {
         throw new JAccessExceptionNotallowed(JText::_('JERROR_ALERTNOAUTHOR'), 403);
      }

      $folder = $input->get('folder', '', 'RAW');
      return self::getMediaList($folder);
   }

   public static function getMediaLibraryOld()
   {
      $input = JFactory::getApplication()->input;
      $user = JFactory::getUser();
      $asset = $input->get('asset');
      $author = $input->get('author');

      if (!$user->authorise('core.manage', 'com_media') && (!$asset || (!$user->authorise('core.edit', $asset) && !$user->authorise('core.create', $asset) && count($user->getAuthorisedCategories($asset, 'core.create')) == 0) && !($user->id == $author && $user->authorise('core.edit.own', $asset)))) {
         throw new JAccessExceptionNotallowed(JText::_('JERROR_ALERTNOAUTHOR'), 403);
      }

      $params = JComponentHelper::getParams('com_media');

      JLoader::register('MediaHelper', JPATH_ADMINISTRATOR . '/components/com_media/helpers/media.php');


      // Set the path definitions
      $popup_upload = $input->get('pop_up', null);
      $path = 'file_path';
      $view = $input->get('view');

      if (substr(strtolower($view), 0, 6) == 'images' || $popup_upload == 1) {
         $path = 'image_path';
      }

      define('COM_MEDIA_BASE', JPATH_ROOT . '/' . $params->get($path, 'images'));
      define('COM_MEDIA_BASEURL', JUri::root() . $params->get($path, 'images'));

      $mediamodelPath = JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_media' . DIRECTORY_SEPARATOR . 'models';
      JModelLegacy::addIncludePath($mediamodelPath);
      $model = JModelLegacy::getInstance('List', 'MediaModel');
      return $model->getList();
   }

   public static function uploadMedia()
   {
      $input = JFactory::getApplication()->input;
      $dir = $input->get('dir', '', 'RAW');
      $media = $input->get('media', '', 'images');

      if (empty($dir)) {
         $dir = JPATH_SITE . '/' . 'images' . '/' . date('Y');
         if (!file_exists($dir)) {
            mkdir($dir, 0777);
         }
         if (!file_exists($dir . '/' . date('m'))) {
            mkdir($dir . '/' . date('m'), 0777);
         }
         if (!file_exists($dir . '/' . date('m') . '/' . date('d'))) {
            mkdir($dir . '/' . date('m') . '/' . date('d'), 0777);
         }

         $uploadDir = $dir . '/' . date('m') . '/' . date('d');
         $uploadFolder = date('Y') . '/' . date('m') . '/' . date('d');
      } else {
         $uploadDir = JPATH_SITE . '/' . $dir;
         if ($dir == 'images') {
            $uploadFolder = '';
         } else {
            $uploadFolder = preg_replace('/images\//', '', $dir, 1);
         }
      }


      $fieldName = 'file';

      $fileError = $_FILES[$fieldName]['error'];
      if ($fileError > 0) {
         switch ($fileError) {
            case 1:
               throw new \Exception(JText::_('ASTROID_ERROR_LARGE_FILE'));
               return;

            case 2:
               throw new \Exception(JText::_('ASTROID_ERROR_FILE_HTML_ALLOW'));
               return;

            case 3:
               throw new \Exception(JText::_('ASTROID_ERROR_FILE_PARTIAL_ALLOW'));
               return;

            case 4:
               throw new \Exception(JText::_('ASTROID_ERROR_NO_FILE'));
               return;
         }
      }


      $pathinfo = pathinfo($_FILES[$fieldName]['name']);
      $uploadedFileExtension = $pathinfo['extension'];
      $uploadedFileExtension = strtolower($uploadedFileExtension);
      if ($media == 'images') {
         $validFileExts = explode(',', 'jpeg,jpg,png,gif');
      } else {
         $validFileExts = explode(',', 'mp4,mpeg,mpg');
      }
      if (!in_array($uploadedFileExtension, $validFileExts)) {
         throw new \Exception(JText::_('INVALID EXTENSION'));
      }

      $fileTemp = $_FILES[$fieldName]['tmp_name'];

      if ($media == 'images') {
         $imageinfo = getimagesize($fileTemp);
         $okMIMETypes = 'image/jpeg,image/pjpeg,image/png,image/x-png,image/gif';
         $validFileTypes = explode(",", $okMIMETypes);

         if (!is_int($imageinfo[0]) || !is_int($imageinfo[1]) || !in_array($imageinfo['mime'], $validFileTypes)) {
            throw new \Exception(JText::_('INVALID FILETYPE'));
         }
      }

      $fileName = preg_replace("/[^A-Za-z0-9]/i", "-", $pathinfo['filename']) . '.' . $pathinfo['extension'];

      $uploadPath = $uploadDir . '/' . $fileName;

      if (file_exists($uploadPath)) {
         $fileName = preg_replace("/[^A-Za-z0-9]/i", "-", $pathinfo['filename']) . '-' . time() . '.' . $pathinfo['extension'];

         $uploadPath = $uploadDir . '/' . $fileName;
      }

      if (!JFile::upload($fileTemp, $uploadPath)) {
         throw new \Exception(JText::_('ERROR MOVING FILE'));
      } else {
         return ['status' => 'success', 'filepath' => $uploadPath, 'filename' => $fileName, 'uploadpath' => $uploadDir, 'folder' => $uploadFolder];
      }
   }

   public static function createFolder()
   {
      $input = JFactory::getApplication()->input;
      $directory = $input->get('dir', '', 'RAW');
      $name = $input->get('name', '', 'RAW');

      $dir = JPATH_SITE . '/' . $directory . '/' . $name;
      if (file_exists($dir)) {
         throw new \Exception("Folder `$name` already exists", 0);
      }
      mkdir($dir, 0777);

      $folder = $name;
      if ($directory != 'images') {
         $directory = preg_replace('/images\//', '', $directory, 1);
         $folder = $directory . '/' . $name;
      }

      return ['message' => "Folder `$name` successfully created.", 'folder' => $folder];
   }

   public static function getJSONData($name)
   {
      $fontsJSON = file_get_contents(JPATH_SITE . '/' . 'media' . '/' . 'astroid' . '/' . 'assets' . '/' . 'json' . '/' . $name . '.json');
      return \json_decode($fontsJSON, true);
   }

   public static function getGoogleFonts()
   {
      $fonts = self::getJSONData('webfonts');
      return $fonts['items'];
   }

   public static function getFAIcons($html = false)
   {
      if ($html) {
         $icons = self::getJSONData('fa-icons');
         $array = [];
         $array[] = ['value' => '', 'name' => 'None'];
         foreach ($icons as $icon) {
            $array[] = ['value' => $icon['value'], 'name' => '<i class="' . $icon['value'] . '"></i> ' . $icon['name']];
         }
         $icons = $array;
      } else {
         $icons = self::getJSONData('fa-icons');
      }
      return $icons;
   }

   public static function clearCache($template = '', $prefix = 'style')
   {
      $template_dir = JPATH_SITE . '/' . 'templates' . '/' . $template . '/' . 'css';
      $version = new \JVersion;
      $version->refreshMediaVersion();
      if (!file_exists($template_dir)) {
         throw new \Exception("Template not found.", 404);
      }

      if (is_array($prefix)) {
         foreach ($prefix as $pre) {
            $styles = preg_grep('~^' . $pre . '-.*\.(css)$~', scandir($template_dir));
            foreach ($styles as $style) {
               unlink($template_dir . '/' . $style);
            }
         }
      } else {
         $styles = preg_grep('~^' . $prefix . '-.*\.(css)$~', scandir($template_dir));
         foreach ($styles as $style) {
            unlink($template_dir . '/' . $style);
         }
      }
      self::clearJoomlaCache();
      return true;
   }

   public static function clearJoomlaCache()
   {
      $app = \JFactory::getApplication();
      $model = \JModelLegacy::getInstance('Cache', 'CacheModel', array('ignore_request' => true));
      $clients    = array(1, 0);
      foreach ($clients as $client) {
         $mCache = $model->getCache($client);
         foreach ($mCache->getAll() as $cache) {
            $mCache->clean($cache->group);
         }
      }
      $app->triggerEvent('onAfterPurge', array());
   }

   public static function getAstroidFieldsets($form)
   {
      $astroidfieldsets = $form->getFieldsets();
      usort($astroidfieldsets, "self::fieldsetOrding");

      $fieldsets = [];

      foreach ($astroidfieldsets as $af) {
         $fieldsets[$af->name] = $af;
      }

      return $fieldsets;
   }

   public static function replaceRelationshipOperators($str)
   {
      $str = str_replace(" AND ", " && ", $str);
      $str = str_replace(" OR ", " || ", $str);
      return $str;
   }

   public static function fieldsetOrding($a, $b)
   {
      if ($a->order == $b->order) {
         return 0;
      }

      if ($a->order == '' || $b->order == '') {
         return 1;
      }

      return ($a->order < $b->order) ? -1 : 1;
   }

   public static function getModules()
   {
      $db = JFactory::getDbo();
      $query = "SELECT #__modules.*, #__usergroups.title as access_title FROM #__modules JOIN #__usergroups ON #__usergroups.id=#__modules.access WHERE #__modules.client_id=0";

      $db->setQuery($query);
      $results = $db->loadObjectList();

      $return = [];
      foreach ($results as $result) {
         $return[] = ['id' => $result->id, 'title' => $result->title, 'module' => $result->module, 'type' => 'module', 'published' => $result->published, 'access_title' => $result->access_title, 'position' => $result->position, 'showtitle' => $result->showtitle];
      }

      return $return;
   }

   public static function getPositions()
   {
      $db = \JFactory::getDbo();

      $query = $db->getQuery(true);
      $query->select(array('*'));
      $query->from($db->quoteName('#__template_styles'));
      $query->where($db->quoteName('client_id') . ' = 0');
      $query->where($db->quoteName('id') . ' = ' . $db->quote(\JFactory::getApplication()->input->get('id', 0, 'INT')));
      $db->setQuery($query);

      $template_style = $db->loadObject();

      $templateXML = \JPATH_SITE . '/templates/' . $template_style->template . '/templateDetails.xml';
      $template = simplexml_load_file($templateXML);
      $positions = [];
      foreach ($template->positions[0] as $position) {
         $p = (string) $position;
         $positions[$p] = $p;
      }
      return $positions;
   }

   public static function getTemplatePartials($template)
   {
      $template_dir = JPATH_SITE . '/' . 'templates' . '/' . $template . '/' . 'frontend' . '/partials/';
      if (file_exists($template_dir)) {
         $partials = self::getPartials($template_dir, $template_dir);
         return $partials;
      } else {
         return $partials;
      }
   }

   public static function getPartials($dir, $templatedir)
   {
      $files = glob($dir . '*');
      $partials = [];
      foreach ($files as $file) {
         if (!is_dir($file)) {
            $extension = pathinfo($file, PATHINFO_EXTENSION);
            if ($extension == 'php') {
               $prefix = str_replace(DIRECTORY_SEPARATOR, '.', str_replace($templatedir, '', $dir));
               $partials[] = $prefix . pathinfo($file, PATHINFO_FILENAME);
            }
         } else if ($file != "." && $file != ".." && is_dir($file)) {
            $sub_partials = self::getPartials($file . '/', $templatedir);
            $partials = array_merge($partials, $sub_partials);
         }
      }
      return $partials;
   }

   public static function isSystemFont($font)
   {
      return isset(AstroidFrameworkConstants::$system_fonts[$font]);
   }

   public static function setTemplateDefaults($template, $id, $parent_id = 0)
   {
      $params_path = JPATH_SITE . "/templates/{$template}/params/{$id}.json";
      if (!file_exists($params_path)) {
         if (!empty($parent_id) && file_exists(JPATH_SITE . "/templates/{$template}/params/" . $parent_id . '.json')) {
            $params = file_get_contents(JPATH_SITE . "/templates/{$template}/params" . '/' . $parent_id . '.json');
            file_put_contents(JPATH_SITE . "/templates/{$template}/params" . '/' . $id . '.json', $params);
         } else if (file_exists(JPATH_SITE . '/templates/' . $template . '/astroid/default.json')) {
            $params = file_get_contents(JPATH_SITE . '/templates/' . $template . '/astroid/default.json');
            if (!file_exists(JPATH_SITE . "/templates/{$template}/params")) {
               mkdir(JPATH_SITE . "/templates/{$template}/params");
            }
            $params = str_replace('TEMPLATE_NAME', $template, $params);
            file_put_contents(JPATH_SITE . "/templates/{$template}/params" . '/' . $id . '.json', $params);
         } else {
            if (!file_exists(JPATH_SITE . "/templates/{$template}/params")) {
               mkdir(JPATH_SITE . "/templates/{$template}/params");
            }
            file_put_contents(JPATH_SITE . "/templates/{$template}/params" . '/' . $id . '.json', '');
         }
         $db = JFactory::getDbo();
         $object = new stdClass();
         $object->id = $id;
         $object->params = \json_encode(["astroid" => $id]);
         $db->updateObject('#__template_styles', $object, 'id');
         self::uploadTemplateDefaults($template, $id);
      }
   }

   public static function setTemplateTypography($template, $id)
   {
      $params_path = JPATH_SITE . "/templates/{$template}/params/{$id}.json";
      if (file_exists($params_path)) {
         $params = json_decode(file_get_contents($params_path));
         $typographys = array('body_typography', 'menus_typography', 'submenus_typography', 'h1_typography', 'h2_typography', 'h3_typography', 'h4_typography', 'h5_typography', 'h6_typography');
         foreach ($typographys as $typography) {
            if (isset($params->$typography) && $params->$typography == 'custom') {
               $key = $typography . '_options';
               $units = array('font_size_unit', 'font_size', 'letter_spacing_unit', 'letter_spacing', 'line_height_unit', 'line_height');
               foreach ($units as $unit) {
                  if (isset($params->$key->$unit) && !is_object($params->$key->$unit)) {
                     $val = $params->$key->$unit;
                     $params->$key->$unit =  new stdClass;
                     $params->$key->$unit->desktop = $val;
                     $params->$key->$unit->tablet = $val;
                     $params->$key->$unit->mobile = $val;
                  }
               }
            }
         }
         file_put_contents(JPATH_SITE . "/templates/{$template}/params" . '/' . $id . '.json', json_encode($params));
      }
   }

   public static function uploadTemplateDefaults($template, $id)
   {
      $source = JPATH_SITE . '/templates/' . $template . '/images/default';
      $destination = JPATH_SITE . '/images/' . $template;
      $files = JFolder::files($source);
      JFolder::copy($source, $destination, '', true);
   }

   public static function getUploadedFonts($template)
   {
      require_once JPATH_LIBRARIES . '/' . 'astroid' . '/' . 'framework' . '/' . 'library' . '/' . 'FontLib' . '/' . 'Autoloader.php';
      $template_fonts_path = JPATH_SITE . "/templates/{$template}/fonts";
      if (!file_exists($template_fonts_path)) {
         return [];
      }
      $fonts = [];
      $font_extensions = ['otf', 'ttf', 'woff'];
      foreach (scandir($template_fonts_path) as $font_path) {
         if (is_file($template_fonts_path . '/' . $font_path)) {
            $pathinfo = pathinfo($template_fonts_path . '/' . $font_path);
            if (in_array($pathinfo['extension'], $font_extensions)) {
               $font = \FontLib\Font::load($template_fonts_path . '/' . $font_path);
               $font->parse();
               $fontname = $font->getFontFullName();
               $fontid = 'library-font-' . JFilterOutput::stringURLSafe($fontname);
               if (!isset($fonts[$fontid])) {
                  $fonts[$fontid] = [];
                  $fonts[$fontid]['id'] = $fontid;
                  $fonts[$fontid]['name'] = $fontname;
                  $fonts[$fontid]['files'] = [];
               }
               $fonts[$fontid]['files'][] = './../fonts/' . $font_path;
            }
         }
      }
      return $fonts;
   }

   public static function loadLibraryFont($font, $template)
   {
      if (empty($font)) {
         return;
      }
      $style = '';
      foreach ($font['files'] as $file) {
         $style .= '@font-face { font-family: "' . $font['name'] . '"; src: url("' . JURI::root() . "templates/{$template->template}/css/" . $file . '");}';
      }
      $template->addStyleDeclaration($style);
   }

   public static function getMediaList($folder)
   {
      $params = JComponentHelper::getParams('com_media');

      define('COM_MEDIA_BASE', JPATH_ROOT . '/' . $params->get('image_path', 'images'));
      define('COM_MEDIA_BASEURL', JUri::root() . $params->get('image_path', 'images'));

      $current = $folder;
      $basePath = COM_MEDIA_BASE . ((strlen($current) > 0) ? '/' . $current : '');
      $mediaBase = str_replace(DIRECTORY_SEPARATOR, '/', COM_MEDIA_BASE . '/');

      $images = array();
      $folders = array();
      $docs = array();
      $videos = array();

      $fileList = false;
      $folderList = false;

      if (file_exists($basePath)) {
         // Get the list of files and folders from the given folder
         $fileList = JFolder::files($basePath);
         $folderList = JFolder::folders($basePath);
      }

      // Iterate over the files if they exist
      if ($fileList !== false) {
         $tmpBaseObject = new JObject;

         foreach ($fileList as $file) {
            if (is_file($basePath . '/' . $file) && substr($file, 0, 1) != '.' && strtolower($file) !== 'index.html') {
               $tmp = clone $tmpBaseObject;
               $tmp->name = $file;
               $tmp->title = $file;
               $tmp->path = str_replace(DIRECTORY_SEPARATOR, '/', JPath::clean($basePath . '/' . $file));
               $tmp->path_relative = str_replace($mediaBase, '', $tmp->path);
               $tmp->size = filesize($tmp->path);

               $ext = strtolower(JFile::getExt($file));

               switch ($ext) {
                     // Image
                  case 'jpg':
                  case 'png':
                  case 'gif':
                  case 'xcf':
                  case 'odg':
                  case 'bmp':
                  case 'jpeg':
                  case 'svg':
                  case 'webp':
                  case 'ico':
                  case 'tiff':
                     $info = @getimagesize($tmp->path);
                     $tmp->width = @$info[0];
                     $tmp->height = @$info[1];
                     $tmp->type = @$info[2];
                     $tmp->mime = @$info['mime'];

                     if (($info[0] > 60) || ($info[1] > 60)) {
                        $dimensions = self::imageResize($info[0], $info[1], 60);
                        $tmp->width_60 = $dimensions[0];
                        $tmp->height_60 = $dimensions[1];
                     } else {
                        $tmp->width_60 = $tmp->width;
                        $tmp->height_60 = $tmp->height;
                     }

                     if (($info[0] > 16) || ($info[1] > 16)) {
                        $dimensions = self::imageResize($info[0], $info[1], 16);
                        $tmp->width_16 = $dimensions[0];
                        $tmp->height_16 = $dimensions[1];
                     } else {
                        $tmp->width_16 = $tmp->width;
                        $tmp->height_16 = $tmp->height;
                     }

                     $images[] = $tmp;
                     break;

                     // Video
                  case 'mp4':
                  case 'webm':
                  case 'ogg':
                     $tmp->icon_32 = 'media/mime-icon-32/' . $ext . '.png';
                     $tmp->icon_16 = 'media/mime-icon-16/' . $ext . '.png';
                     $videos[] = $tmp;
                     break;

                     // Non-image document
                  default:
                     $tmp->icon_32 = 'media/mime-icon-32/' . $ext . '.png';
                     $tmp->icon_16 = 'media/mime-icon-16/' . $ext . '.png';
                     $docs[] = $tmp;
                     break;
               }
            }
         }
      }

      // Iterate over the folders if they exist
      if ($folderList !== false) {
         $tmpBaseObject = new JObject;

         foreach ($folderList as $folder) {
            $tmp = clone $tmpBaseObject;
            $tmp->name = basename($folder);
            $tmp->path = str_replace(DIRECTORY_SEPARATOR, '/', JPath::clean($basePath . '/' . $folder));
            $tmp->path_relative = str_replace($mediaBase, '', $tmp->path);
            $count = self::countFiles($tmp->path);
            $tmp->files = $count[0];
            $tmp->folders = $count[1];

            $folders[] = $tmp;
         }
      }

      $list = array('folders' => $folders, 'docs' => $docs, 'images' => $images, 'videos' => $videos);

      return $list;
   }

   public static function imageResize($width, $height, $target)
   {
      /*
       * Takes the larger size of the width and height and applies the
       * formula accordingly. This is so this script will work
       * dynamically with any size image
       */
      if ($width > $height) {
         $percentage = ($target / $width);
      } else {
         $percentage = ($target / $height);
      }

      // Gets the new value and applies the percentage, then rounds the value
      $width = round($width * $percentage);
      $height = round($height * $percentage);

      return array($width, $height);
   }

   public static function countFiles($dir)
   {
      $total_file = 0;
      $total_dir = 0;

      if (is_dir($dir)) {
         $d = dir($dir);

         while (($entry = $d->read()) !== false) {
            if ($entry[0] !== '.' && strpos($entry, '.html') === false && strpos($entry, '.php') === false && is_file($dir . DIRECTORY_SEPARATOR . $entry)) {
               $total_file++;
            }

            if ($entry[0] !== '.' && is_dir($dir . DIRECTORY_SEPARATOR . $entry)) {
               $total_dir++;
            }
         }

         $d->close();
      }

      return array($total_file, $total_dir);
   }

   public static function spacingValue($value = null, $property = "padding", $default = [])
   {
      $return = [];
      $values = [];
      if (!empty($value) && isset($value->unit)) {
         $unit = $value->unit;
         if ($value->lock && is_numeric($value->top)) {
            foreach (['top', 'right', 'bottom', 'left'] as $position) {
               $return[$position] = self::getPropertySubset($property, $position) . ":{$value->top}{$unit}";
               $values[$position] = "{$value->top}{$unit}";
            }
         } else {
            foreach (['top', 'right', 'bottom', 'left'] as $position) {
               $pvalue = $value->{$position};
               if (is_numeric($pvalue)) {
                  $return[$position] = self::getPropertySubset($property, $position) . ":{$pvalue}{$unit}";
                  $values[$position] = "{$pvalue}{$unit}";
               }
            }
         }
      }

      if (!isset($default['unit'])) {
         $default['unit'] = 'px';
      }

      foreach (array_keys($default) as $position) {
         if ($position == "unit") {
            continue;
         }
         if (!isset($return[$position])) {
            $return[$position] = self::getPropertySubset($property, $position) . ":{$default[$position]}{$default['unit']}";
            $values[$position] = "{$default[$position]}{$default['unit']}";
         }
      }


      if (count(array_keys($values)) === 4) {
         $return = [];
         $return[] = self::getPropertySet($property) . ':' . implode(' ', $values);
      }

      return implode(";", $return);
   }

   public static function getPropertySubset($property, $position)
   {
      switch ($property) {
         case "radius":
            switch ($position) {
               case "top":
                  return 'border-top-left-radius';
                  break;
               case "left":
                  return 'border-bottom-left-radius';
                  break;
               case "right":
                  return 'border-top-right-radius';
                  break;
               case "bottom":
                  return 'border-bottom-right-radius';
                  break;
            }
            break;
         case "border":
            return $property . '-' . $position . '-width';
            break;
         default:
            return $property . '-' . $position;
            break;
      }
   }

   public static function getPropertySet($property)
   {
      switch ($property) {
         case "radius":
            return "border-radius";
            break;
         case "border":
            return "border-width";
            break;
         default:
            return $property;
            break;
      }
   }

   public static function frameworkVersion()
   {
      $xml = JFactory::getXML(JPATH_ADMINISTRATOR . '/manifests/libraries/astroid.xml');
      $version = (string) $xml->version;
      return $version;
   }


   public static function selectedImages(&$matches, $images = '', $toggle = '')
   {
      $images = array_map('trim', explode("\n", $images));
      $matchesTemp = array();

      foreach ($images as $image) {
         $count = 0;

         foreach ($matches[1] as $match) {
            if (preg_match('@' . preg_quote($image) . '@', $match)) {
               if ($toggle == 'exclude') {
                  unset($matches[0][$count]);
               } else {
                  $matchesTemp[] = $matches[0][$count];
               }
            }

            $count++;
         }
      }

      if ($toggle == 'include') {
         unset($matches[0]);
         $matches[0] = $matchesTemp;
      }
   }

   public static function selectedComponents($components = '', $toggle = '')
   {
      $option = JFactory::getApplication()->input->getWord('option');
      $components = array_map('trim', explode("\n", $components));
      $hit = false;
      $return = true;
      foreach ($components as $component) {
         if ($option === $component) {
            $hit = true;
            break;
         }
      }

      if ($toggle == 'include') {
         if ($hit === false) {
            $return = false;
         }
         return $return;
      }

      if ($hit === true) {
         $return = false;
      }

      return $return;
   }

   public static function selectedURLs($surls = '', $toggle = '')
   {
      $url = JUri::getInstance()->toString();
      $surls = array_map('trim', explode("\n", $surls));
      $hit = false;
      $return = true;

      foreach ($surls as $surl) {
         if ($url === $surl) {
            $hit = true;
            break;
         }
      }

      if ($toggle == 'include') {
         if ($hit === false) {
            $return = false;
         }

         return $return;
      }

      if ($hit === true) {
         $return = false;
      }

      return $return;
   }

   public static function exclidedViews($views = '')
   {
      $view = JFactory::getApplication()->input->getWord('tmpl', '');
      $views = array_map('trim', explode(",", $views));
      $return = true;

      if (in_array($view, $views)) {
         $return = false;
      }

      return $return;
   }

   public static function selectedClasses(&$matches, $classes = '', $toggle = '')
   {
      $classes = array_map('trim', explode("\n", $classes));

      foreach ($matches[0] as $key => $match) {
         foreach ($classes as $classname) {
            $classExists = preg_match('@class=[\"\'].*' . $classname . '.*[\"\']@Ui', $match);

            if ($toggle == 'include') {
               if (empty($classExists)) {
                  unset($matches[0][$key]);
               }

               continue;
            }

            if (!empty($classExists)) {
               unset($matches[0][$key]);
            }
         }
      }
   }
}
