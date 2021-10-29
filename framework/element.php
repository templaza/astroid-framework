<?php

/**
 * @package   Astroid Framework
 * @author    JoomDev https://www.joomdev.com
 * @copyright Copyright (C) 2009 - 2020 JoomDev.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */
defined('_JEXEC') or die;
jimport('astroid.framework.helper');
jimport('astroid.framework.astroid');

class AstroidElement
{

   public $id = null;
   protected $app = null;
   public $type = '';
   public $title = '';
   public $icon = '';
   public $multiple = true;
   public $classname = '';
   public $description = '';
   protected $xml_file = null;
   protected $default_xml_file = null;
   protected $layout = null;
   public $params = [];
   public $data = [];
   protected $template = null;
   protected $xml = null;
   protected $form = null;

   public function __construct($type = '', $data = [], $template = null)
   {
      $this->type = $type;
      if (!empty($data)) {
         $this->id = $data['id'];
         $this->data = isset($data['params']) ? $data['params'] : [];
         $this->raw_data = $data;
      }
      $this->app = JFactory::getApplication();

      if ($template === null) {
         $this->template = Astroid\Framework::getTemplate();
      } else {
         $this->template = $template;
      }

      $this->setClassName();
      $this->template->setLog("Initiated Element : " . $type, "success");
      $library_elements_directory = JPATH_LIBRARIES . '/' . 'astroid' . '/' . 'framework' . '/' . 'elements' . '/';
      $template_elements_directory = JPATH_SITE . '/' . 'templates' . '/' . $this->template->template . '/' . 'astroid' . '/' . 'elements' . '/';

      switch ($this->type) {
         case 'section':
            $this->default_xml_file = $library_elements_directory . 'section-default.xml';
            break;
         case 'column':
            $this->default_xml_file = $library_elements_directory . 'column-default.xml';
            break;
         case 'row':
            $this->default_xml_file = $library_elements_directory . 'row-default.xml';
            break;
         default:
            $this->default_xml_file = $library_elements_directory . 'default.xml';
            break;
      }

      if (file_exists($template_elements_directory . $this->type . '/' . $this->type . '.xml')) {
         $this->xml_file = $template_elements_directory . $this->type . '/' . $this->type . '.xml';
         $this->layout = $template_elements_directory . $this->type . '/' . $this->type . '.php';
      } else if (file_exists($library_elements_directory . $this->type . '/' . $this->type . '.xml')) {
         $this->xml_file = $library_elements_directory . $this->type . '/' . $this->type . '.xml';
         $this->layout = $library_elements_directory . $this->type . '/' . $this->type . '.php';
      }
      if (!defined('ASTROID_FRONTEND')) {
         if ($this->xml_file !== null) {
            $this->loadXML();
         }
         $this->loadForm();
      }
   }

   protected function setClassName()
   {
      $type = $this->type;
      $type = str_replace('-', ' ', $type);
      $type = str_replace('_', ' ', $type);
      $type = ucwords(strtolower($type));
      $classname = 'AstroidElement' . str_replace(' ', '', $type);
      $this->classname = $classname;
   }

   protected function loadXML()
   {
      $xml = simplexml_load_file($this->xml_file);
      $this->xml = $xml;
      $title = (string) @$xml->title;
      $icon = (string) @$xml->icon;
      $description = (string) @$xml->description;
      $color = (string) @$xml->color;
      $multiple = (string) @$xml->multiple;

      $this->title = $title;
      $this->icon = $icon;
      $this->description = $description;
      $this->color = $color;
      $this->multiple = $multiple == "false" ? false : true;
   }

   public function loadForm()
   {
      $this->form = new JForm($this->type);
      if (!empty($this->xml_file)) {
         $xml = $this->xml->form;
         $this->form->load($xml, false);
      }
      $defaultXml = simplexml_load_file($this->default_xml_file);
      $this->form->load($defaultXml->form, false);

      $formData = [];

      $fieldsets = $this->form->getFieldsets();
      foreach ($fieldsets as $key => $fieldset) {
         $fields = $this->form->getFieldset($key);
         foreach ($fields as $field) {
            $formData[] = ['name' => $field->name, 'value' => $field->value];
         }
      }

      $this->params = $formData;
   }

   public function getInfo()
   {
      return [
         'type' => $this->type,
         'title' => JText::_($this->title),
         'icon' => $this->icon,
         'description' => JText::_($this->description),
         'color' => $this->color,
         'multiple' => $this->multiple,
         'params' => $this->params,
      ];
   }

   public function renderForm()
   {
      $layout = new JLayoutFile('form', JPATH_LIBRARIES . '/' . 'astroid' . '/' . 'framework' . '/' . 'layouts' . '/' . 'framework');
      $html = $layout->render(['element' => $this]);
      $form = str_replace(array("\n", "\r", "\t"), '', $html);
      $replacer = [
         'ng-show="' => 'ng-show="elementParams.',
         'ng-hide="' => 'ng-hide="elementParams.',
         'ng-model="' => 'ng-model="elementParams.',
         'ng-value="' => 'ng-value="elementParams.',
         'ng-radio-init="' => 'ng-init="elementParams.',
         'ng-media-class' => 'ng-class',
      ];

      $form = preg_replace_callback('/(\s*ng-class="{)([^"]*)(}"[^>]*>)(.*)/siU', function ($matches) {
         $replaced = str_replace(':', ':elementParams.', $matches[2]);
         return str_replace($matches[2], $replaced, $matches[0]);
      }, $form);

      foreach ($replacer as $find => $replace) {
         $form = str_replace($find, $replace, $form);
      }

      return $form;
   }

   public function getForm()
   {
      return $this->form;
   }

   protected function getParams()
   {
      $formData = [];
      $return = [];
      foreach ($this->data as $data) {
         $data = (array) $data;
         $formData[$data['name']] = $data['value'];
      }
      /* $params = [];
      foreach ($this->params as $param) {
         $param = (array) $param;
         if (isset($formData[$param['name']])) {
            $params[$param['name']] = $formData[$param['name']];
         } else {
            $params[$param['name']] = $param['value'];
         }
      } */

      return $AstroidParams = new AstroidParams($formData);
   }

   public function render()
   {
      if ($this->layout === null || !file_exists($this->layout)) {
         $this->template->setLog('Layout Not Found - Element : ' . $this->type);
         return '';
      }
      $pathinfo = pathinfo($this->layout);
      $layout = new JLayoutFile($pathinfo['filename'], $pathinfo['dirname']);

      $html = $layout->render(['params' => $this->getParams(), 'template' => $this->template, 'element' => $this]);

      $return = '';
      if (!empty($html)) {

         $elHtml = [];
         $elHtml[] = '<div';
         if (!empty($this->getClass())) {
            $elHtml[] = 'class="' . $this->getClass() . '"';
         }
         if (!empty($this->getID())) {
            $elHtml[] = 'id="' . $this->getID() . '"';
         }
         if (!empty($this->getStyles())) {
            $elHtml[] = 'style="' . $this->getStyles() . '"';
         }
         if (!empty($this->getAnimation())) {
            $elHtml[] = 'data-animation="' . $this->getAnimation() . '"';
            $elHtml[] = 'data-animation-delay="' . $this->getAnimationDelay() . '"';
            $elHtml[] = 'data-animation-duration="' . $this->getAnimationDuration() . '"';
         }
         if (!empty($this->getAttributes())) {
            $elHtml[] = $this->getAttributes();
         }
         $elHtml[] = '>' . $html . '</div>';

         $return .= implode(' ', $elHtml);
      }
      return $return;
   }

   public function renderWireframe()
   {
      $layout = new JLayoutFile('wireframe', JPATH_LIBRARIES . '/' . 'astroid' . '/' . 'framework' . '/' . 'layouts' . '/' . 'framework');
      $html = $layout->render(['params' => $this->getParams(), 'template' => $this->template, 'element' => $this]);
      return $html;
   }

   public function getID()
   {
      $params = $this->getParams();
      $customid = $params->get('customid', '');
      if (!empty($customid)) {
         return $customid;
      } else {
         $prefix = !empty($params->get('title')) ? $params->get('title') : 'astroid-' . $this->type;
         return $this->template->slugify($prefix . '-' . $this->id);
      }
   }

   public function getClass()
   {
      $params = $this->getParams();
      $classes = [];
      $classes[] = $this->template->slugify('astroid-' . $this->type);
      if ($this->type == 'section') {
         $section_classes = $this->getSectionClasses();
         if (!empty($section_classes)) {
            $classes[] = $section_classes;
         }
      }
      if ($this->type == 'column') {
         $column_classes = $this->getColumnClasses();
         if (!empty($column_classes)) {
            $classes[] = $column_classes;
         }
      }
      $customclass = $params->get('customclass', '');
      if (!empty($customclass)) {
         $classes[] = $customclass;
      }

      $hideonxs = $params->get('hideonxs', 0);
      if ($hideonxs) {
         $classes[] = 'hideonxs';
      }
      $hideonsm = $params->get('hideonsm', 0);
      if ($hideonsm) {
         $classes[] = 'hideonsm';
      }
      $hideonmd = $params->get('hideonmd', 0);
      if ($hideonmd) {
         $classes[] = 'hideonmd';
      }
      $hideonlg = $params->get('hideonlg', 0);
      if ($hideonlg) {
         $classes[] = 'hideonlg';
      }
      $hideonxl = $params->get('hideonxl', 0);
      if ($hideonxl) {
         $classes[] = 'hideonxl';
      }

      return implode(' ', $classes);
   }

   public function getSectionClasses()
   {
      $data = $this->raw_data;
      $classes = [];

      $header_module_position = $this->template->params->get('header_module_position', '');
      $footer_module_position = $this->template->params->get('footer_module_position', '');

      // Check Section has component
      foreach ($data['rows'] as $row) {
         foreach ($row['cols'] as $colIndex => $col) {
            foreach ($col['elements'] as $element) {
               if ($element['type'] == 'component') {
                  $classes[] = 'astroid-component-section';
                  break;
               }
            }
         }
      }

      // Check Section has header
      if (!empty($header_module_position)) {
         foreach ($data['rows'] as $row) {
            foreach ($row['cols'] as $colIndex => $col) {
               foreach ($col['elements'] as $element) {
                  if ($element['type'] == 'module_position') {
                     $el = new AstroidElement('module_position', $element, $this->template);
                     $position = $el->getValue('position', '');
                     if ($position == $header_module_position) {
                        $classes[] = 'astroid-header-section';
                     }
                  }
                  break;
               }
            }
         }
      }

      // Check Section has footer
      if (!empty($header_module_position)) {
         foreach ($data['rows'] as $row) {
            foreach ($row['cols'] as $colIndex => $col) {
               foreach ($col['elements'] as $element) {
                  if ($element['type'] == 'module_position') {
                     $el = new AstroidElement('module_position', $element, $this->template);
                     $position = $el->getValue('position', '');
                     if ($position == $footer_module_position) {
                        $classes[] = 'astroid-footer-section';
                     }
                  }
                  break;
               }
            }
         }
      }
      return implode(' ', $classes);
   }

   public function getColumnClasses()
   {
      $data = $this->raw_data;
      $class = [];
      $params = $this->getParams();
      $responsive = $params->get('responsive', '');
      if (!empty($responsive)) {
         $responsive = \json_decode($responsive, true);
      } else {
         $responsive = [];
      }
      $responsive_utilities = [];
      foreach ($responsive as $responsive_utility) {
         if (array_key_exists('name', $responsive_utility)) {
            $responsive_utilities[$responsive_utility['name']] = $responsive_utility['value'];
         }
      }
      $sizes = ['xs', 'sm', 'md', 'lg', 'xl'];
      foreach ($sizes as $size) {
         if ($size == 'lg') {
            $class[] = 'col-' . $size . '-' . $data['size'];
            if (isset($responsive_utilities['hide_' . $size]) && $responsive_utilities['hide_' . $size] != 1) {
               $class[] = 'hideon' . $size;
            }
         } else {
            if (isset($responsive_utilities['size_' . $size]) && $responsive_utilities['size_' . $size] != 'inherit') {
               $class[] = $size == 'xs' ? 'col-' . $responsive_utilities['size_' . $size] : 'col-' . $size . '-' . $responsive_utilities['size_' . $size];
            }
            if (isset($responsive_utilities['hide_' . $size]) && $responsive_utilities['hide_' . $size] != 1) {
               $class[] = 'hideon' . $size;
            }
         }
      }
      return implode(' ', $class);
   }

   public function getAnimation()
   {
      $params = $this->getParams();
      $animation = $params->get('animation', '');
      return $animation;
   }

   public function getAnimationDuration()
   {
      $params = $this->getParams();
      $animation_duration = $params->get('animation_duration', 0);
      return $animation_duration;
   }

   public function getAnimationDelay()
   {
      $params = $this->getParams();
      $animation_delay = $params->get('animation_delay', 0);
      return $animation_delay;
   }

   public function getStyles()
   {
      $params = $this->getParams();
      $styles = [];
      $background = $params->get('background', 0);
      $custom_colors = $params->get('custom_colors', 0);
      if ($background && $this->type != 'section') {
         $background_color = $params->get('background_color', '');
         if (!empty($background_color)) {
            $styles[] = 'background-color:' . $background_color;
         }
         $background_image = $params->get('background_image', '');
         if (!empty($background_image)) {
            $styles[] = 'background-image: url(' . JURI::root() . $this->SeletedMedia() . '/' . $background_image . ')';
            $background_repeat = $params->get('background_repeat', '');
            $background_repeat = empty($background_repeat) ? 'inherit' : $background_repeat;
            $styles[] = 'background-repeat:' . $background_repeat;

            $background_size = $params->get('background_size', '');
            $background_size = empty($background_size) ? 'inherit' : $background_size;
            $styles[] = 'background-size:' . $background_size;

            $background_attchment = $params->get('background_attchment', '');
            $background_attchment = empty($background_attchment) ? 'inherit' : $background_attchment;
            $styles[] = 'background-attachment:' . $background_attchment;

            $background_position = $params->get('background_position', '');
            $background_position = empty($background_position) ? 'inherit' : $background_position;
            $styles[] = 'background-position:' . $background_position;
         }
      }

      $styles = $this->Style();
      $this->MarginPadding();

      if ($this->type == 'column') {
         $styles = $this->Style();
      }
      if ($this->type != 'column' && $this->type != 'section') {
         $styles = $this->Style();
      }

      if ($custom_colors) {
         $text_color = $params->get('text_color', '');
         $link_color = $params->get('link_color', '');
         $link_hover_color = $params->get('link_hover_color', '');
         $color_styles = [];
         if (!empty($text_color)) {
            $color_styles[] = '#' . $this->getID() . '{color:' . $text_color . ' !important; }';
         }
         if (!empty($link_color)) {
            $color_styles[] = '#' . $this->getID() . ' a{color:' . $link_color . ' !important; }';
         }
         if (!empty($link_hover_color)) {
            $color_styles[] = '#' . $this->getID() . ' a:hover{color:' . $link_hover_color . ' !important; }';
         }
         if (!empty($color_styles)) {
            $document = JFactory::getDocument();
            $document->addStyleDeclaration(implode('', $color_styles));
         }
      }
      if (!empty($this->getAnimation())) {
         $styles[] = 'visibility: hidden';
      }
      return implode(';', $styles);
   }

   public function MarginPadding()
   {
      $template = AstroidFramework::getTemplate();
      $params = $this->getParams();
      $margin = $params->get('margin', '');
      $padding = $params->get('padding', '');

      if (!empty($margin)) {
         $margin = \json_decode($margin, false);
         foreach ($margin as $device => $props) {
            $style = AstroidFrameworkHelper::spacingValue($props, "margin");
            if (!empty($style)) {
               $style = '#' . $this->getID() . '{' . $style . '}';
               $template->addStyleDeclaration($style, $device);
            }
         }
      }

      if (!empty($padding)) {
         $padding = \json_decode($padding, false);
         foreach ($padding as $device => $props) {
            $style = AstroidFrameworkHelper::spacingValue($props, "padding");
            if (!empty($style)) {
               $style = '#' . $this->getID() . '{' . $style . '}';
               $template->addStyleDeclaration($style, $device);
            }
         }
      }
   }

   public function Style()
   {
      $params = $this->getParams();
      $background_setting = $params->get('background_setting', 0);
      $Style = [];
      if ($background_setting) {
         if ($background_setting == "color") {
            $background_color = $params->get('background_color', '');
            if (!empty($background_color)) {
               $Style[] = 'background-color:' . $background_color;
            }
         }
         if ($background_setting == "image") {
            $background_image = $params->get('background_image', '');

            $img_background_color = $params->get('img_background_color', '');
            $img_background_color = empty($img_background_color) ? 'inherit' : $img_background_color;
            $Style[] = 'background-color:' . $img_background_color;

            if (!empty($background_image)) {
               $Style[] = 'background-image: url(' . JURI::root() . $this->SeletedMedia() . '/' . $background_image . ')';
               $background_repeat = $params->get('background_repeat', '');
               $background_repeat = empty($background_repeat) ? 'inherit' : $background_repeat;
               $Style[] = 'background-repeat:' . $background_repeat;

               $background_size = $params->get('background_size', '');
               $background_size = empty($background_size) ? 'inherit' : $background_size;
               $Style[] = 'background-size:' . $background_size;

               $background_attchment = $params->get('background_attchment', '');
               $background_attchment = empty($background_attchment) ? 'inherit' : $background_attchment;
               $Style[] = 'background-attachment:' . $background_attchment;

               $background_position = $params->get('background_position', '');
               $background_position = empty($background_position) ? 'inherit' : $background_position;
               $Style[] = 'background-position:' . $background_position;
            }
         }

         if ($background_setting == "gradient") {
            $background_gradient = $params->get('background_gradient', '');
            $background_gradient = json_decode($background_gradient);
            if (!empty($background_gradient)) {
               $Style[] = 'background-image: ' . $background_gradient->type . '-gradient(' . $background_gradient->start . ',' . $background_gradient->stop . ')';
            }
         }
      }
      return $Style;
   }

   public function getSectionLayoutType()
   {
      $params = $this->getParams();
      $container = $params->get('layout_type', '');
      $custom_class = $params->get('custom_container_class', '');
      switch ($container) {
         case '':
            $container = 'container';
            break;
         case 'no-container':
            $container = '';
            break;
         case 'container-with-no-gutters':
            $container = 'container';
            break;
         case 'container-fluid-with-no-gutters':
            $container = 'container-fluid';
            break;
      }
      if (!empty($container) && !empty($custom_class)) {
         $container .= ' ' . $custom_class;
      }
      return $container;
   }

   public function getAttributes()
   {
      $params = $this->getParams();
      $attributes = [];

      $background = $params->get('background', 0);
      if ($background && $this->type != 'section') {
         $background_video = $params->get('background_video', '');
         if (!empty($background_video)) {
            $attributes['data-jd-video-bg'] = JURI::root() . $this->SeletedMedia() . '/' . $background_video;
         }
      }

      $background_setting = $params->get('background_setting', 0);
      if ($background_setting && $background_setting == "video") {
         $background_video = $params->get('background_video', '');
         if (!empty($background_video)) {
            $attributes['data-jd-video-bg'] = JURI::root() . $this->SeletedMedia() . '/' . $background_video;
            $template = AstroidFramework::getTemplate();
            $videobgjs = 'vendor/jquery.jdvideobg.js';
            if (!isset($template->_js[$videobgjs])) {
               $template->addScript($videobgjs);
            }
         }
      }

      $return = [];
      foreach ($attributes as $key => $value) {
         $return[] = $key . '="' . $value . '"';
      }
      return implode(' ', $return);
   }

   public function SeletedMedia()
   {
      $params = JComponentHelper::getParams('com_media');
      return $params->get('image_path', 'images');
   }

   public function getValue($key, $default = '')
   {
      $params = $this->getParams();
      return $params->get($key, $default);
   }
}

class AstroidParams
{

   public $params = [];

   function __construct($params)
   {
      $this->params = $params;
   }

   public function get($key, $default = null)
   {
      if (isset($this->params[$key])) {
         return $this->params[$key];
      } else {
         return $default;
      }
   }
}
