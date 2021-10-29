<?php

/**
 * @package   Astroid Framework
 * @author    JoomDev https://www.joomdev.com
 * @copyright Copyright (C) 2009 - 2020 JoomDev.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */
defined('_JEXEC') or die;
jimport('astroid.framework.helper');
jimport('astroid.framework.element');

class AstroidFrameworkTemplate
{

   public $template;
   public $params;
   public $language;
   public $title = "";
   public $version = "";
   public $astroidVersion = "";
   public $direction;
   protected $logs;
   protected $debug = false;
   public $cssFile = true;
   public $_styles = ['desktop' => [], 'tablet' => [], 'mobile' => []];
   public $_js = [];
   public $mods = array();
   public $modules = array();
   public static $astroidTemplatePath = JPATH_LIBRARIES . '/astroid/framework/template';

   public function __construct($template)
   {
      Astroid\Framework::getReporter('Auditor')->backtrace(debug_backtrace());
      if (!defined('ASTROID_TEMPLATE_NAME')) {
         define('ASTROID_TEMPLATE_NAME', $template->template);
      }
      $this->template = $template->template;
      if (isset($template->title)) {
         $this->title = $template->title;
      }
      $this->presets = $this->getPresets();
      if (isset($template->id)) {
         $this->params = $this->getTemplateParams($template->id);
      } else {
         $this->params = $this->getTemplateParams();
      }
      $language = JFactory::getApplication()->getLanguage();
      $this->language = $language->getTag();
      $this->direction = $language->isRtl() ? 'rtl' : 'ltr';
      $this->version = $this->templateVersion();
      $this->astroidVersion = AstroidFrameworkHelper::frameworkVersion();
      $this->initAgent();
      $this->addMeta();
      $this->inspect();
   }

   public function templateVersion()
   {
      $xml = JFactory::getXML(JPATH_SITE . "/templates/{$this->template}/templateDetails.xml");
      $version = (string) $xml->version;
      return $version;
   }

   public function addMeta()
   {

      $app = JFactory::getApplication();
      $itemid = $app->input->get('Itemid', '', 'INT');

      $menu = $app->getMenu();
      $item = $menu->getItem($itemid);

      if (empty($item)) {
         return;
      }
      $params = new JRegistry();
      $params->loadString($item->params);

      $enabled = $params->get('astroid_opengraph_menuitem', 0);
      if (empty($enabled)) {
         return;
      }

      $fb_id = $this->params->get('article_opengraph_facebook', '');
      $tw_id = $this->params->get('article_opengraph_twitter', '');

      $config = JFactory::getConfig();
      $og_title = $item->title;
      if (!empty($params->get('astroid_og_title_menuitem', ''))) {
         $og_title = $params->get('astroid_og_title_menuitem', '');
      }
      $og_description = '';
      if (!empty($params->get('astroid_og_desc_menuitem', ''))) {
         $og_description = $params->get('astroid_og_desc_menuitem', '');
      }
      $og_image = '';
      if (!empty($params->get('astroid_og_image_menuitem', ''))) {
         $og_image = JURI::base() . $params->get('astroid_og_image_menuitem', '');
      }

      $og_sitename = $config->get('sitename');
      $og_siteurl = JURI::getInstance();

      $meta = [];
      $meta[] = '<meta name="twitter:card" content="summary_large_image" />';

      if ($item->type == 'component' && isset($item->query) && $item->query['option'] == 'com_content' && $item->query['view'] == 'article') {
         $meta[] = '<meta property="og:type" content="article">';
      }
      if (!empty($og_title)) {
         $meta[] = '<meta property="og:title" content="' . $og_title . '">';
      }
      if (!empty($og_sitename)) {
         $meta[] = '<meta property="og:site_name" content="' . $og_sitename . '">';
      }
      if (!empty($og_siteurl)) {
         $meta[] = '<meta property="og:url" content="' . $og_siteurl . '">';
      }
      if (!empty($og_description)) {
         $meta[] = '<meta property="og:description" content="' . substr($og_description, 0, 200) . '">';
      }
      if (!empty($fb_id)) {
         $meta[] = '<meta property="fb:app_id" content="' . $fb_id . '" />';
      }
      if (!empty($tw_id)) {
         $meta[] = '<meta name="twitter:creator" content="@' . $tw_id . '" />';
      }
      if (!empty($og_image)) {
         $meta[] = '<meta name="og:image" content="' . $og_image . '" />';
      }
      $meta = implode('', $meta);
      if (!empty($meta)) {
         $document = JFactory::getDocument();
         $document->addCustomTag($meta);
      }
   }

   protected function getTemplateParams($id = null)
   {
      if (empty($id)) {
         $template = JFactory::getApplication()->getTemplate(true);
         if (isset($template->id) && $template->id === 0) {
            $template->id = $template->params->get('astroid', 0);
         }
         if (isset($template->id)) {
            $id = $template->id;
         } else {
            $astroid_id = $template->params->get('astroid', 0);
            if (empty($astroid_id)) {
               $params = new JRegistry();
               return $params;
            }
            $id = $astroid_id;
         }
      }

      $params_path = JPATH_SITE . "/templates/{$this->template}/params/" . $id . '.json';
      $json = file_get_contents($params_path);
      $params = new JRegistry();
      $params->loadString($json, 'JSON');

      $issetPreset = JFactory::getApplication()->input->get('preset', '');
      if (!empty($issetPreset)) {
         $preset = null;
         foreach ($this->presets as $set) {
            if ($set['name'] === $issetPreset) {
               $preset = $set;
               break;
            }
         }
         if ($preset !== null) {
            foreach ($preset['preset'] as $attr => $val) {
               if (is_array($val)) {
                  $obj = $params->get($attr);
                  foreach ($val as $subattr => $subval) {
                     $obj->{$subattr} = $subval;
                  }
                  $params->set($attr, $obj);
               } else {
                  $params->set($attr, $val);
               }
            }
         }
      }

      return $params;
   }

   public function head()
   {
   }

   public function initAgent()
   {
      //      $agent = new Mobile_Detect;
      //      if ($agent->isMobile() || $agent->isTablet()) {
      //         $agent_environment = 'wap';
      //         if ($agent->isTablet()) {
      //            $agent_device = 'tablet';
      //         } else {
      //            $agent_device = 'mobile';
      //         }
      //         $agent_name = $agent->device();
      //         $agent_browser = $agent->browser();
      //      } else if ($agent->isDesktop()) {
      //         $agent_environment = 'web';
      //         $agent_device = 'desktop';
      //         $agent_name = $agent->device();
      //         $agent_browser = $agent->browser();
      //      } else if ($agent->isRobot()) {
      //         config(['agent.environment' => 'robot']);
      //         $agent_device = strtolower($agent->robot());
      //         $agent_name = $agent->robot();
      //      } else {
      //         $agent_environment = 'desktop';
      //         $agent_device = 'undefined';
      //         $agent_name = $agent->device();
      //         $agent_browser = $agent->browser();
      //      }

      /*
        var ASTROID_TEMPLATE = [];
        ASTROID_TEMPLATE.DEVICE_ENV = "<?php echo config('agent.environment'); ?>";
        ASTROID_TEMPLATE.DEVICE_TYPE = "<?php echo config('agent.device'); ?>";
        ASTROID_TEMPLATE.DEVICE_NAME = "<?php echo config('agent.name'); ?>";
        ASTROID_TEMPLATE.DEVICE_BROWSER = "<?php echo config('agent.browser'); ?>";
        ASTROID_TEMPLATE.BASE_URL = "<?php echo url('/'); ?>";
       */
   }

   public function body()
   {
      $this->loadLayout('custom');
      if ($this->debug) {
         $this->renderLogs();
      }
   }

   public function renderLayout()
   {
      define('ASTROID_FRONTEND', 1);
      $params = $this->params;
      $layout = $params->get("layout", NULL);
      if ($layout === NULL) {
         $value = file_get_contents(JPATH_SITE . '/' . 'media' . '/' . 'astroid' . '/' . 'assets' . '/' . 'json' . '/' . 'layouts' . '/' . 'default.json');
         $layout = \json_decode($value, true);
      } else {
         $layout = \json_decode($layout, true);
      }
      $this->setLog("Rending Layout");
      $template_layout = $this->params->get('template_layout', 'wide');
      $sppb = $this->isPageBuilder();
      echo '<div class="astroid-container">';
      $header_mode = $this->params->get('header_mode', 'horizontal');
      $header = $this->params->get('header', TRUE);
      if ($header && !empty($header_mode) && $header_mode == 'sidebar') {
         $this->loadLayout('header.sidebar');
      } else {
         $this->loadLayout('offcanvas');
      }
      $this->loadLayout('mobilemenu');

      $content_classes = [];

      if ($header && !empty($header_mode) && $header_mode == 'sidebar') {
         $sidebar_dir = $this->params->get('header_sidebar_menu_mode', 'left');
         $content_classes[] = 'has-sidebar';
         $content_classes[] = 'sidebar-dir-' . $sidebar_dir;
      }

      echo '<div class="astroid-content' . (!empty($content_classes) ? ' ' . implode(' ', $content_classes) : '') . '">';
      echo '<div style="' . $this->getLayoutStyles() . '" class="astroid-layout astroid-layout-' . $template_layout . '">';
      echo '<div class="astroid-wrapper">';
      foreach ($layout['sections'] as $section) {
         $sectionObject = new AstroidElement($section['type'], $section, $this);
         $sectionHTML = '';
         $this->setLog("Rending Section : " . $sectionObject->getValue('title'));
         $rowHTML = '';
         $layout_type = $sectionObject->getValue('layout_type', '');
         $section_layout_type = $sectionObject->getSectionLayoutType();
         foreach ($section['rows'] as $rowIndex => $row) {
            $columnHTML = '';

            $columnSizes = [];
            $bufferSize = 0;
            $hasComponent = FALSE;
            $componentIndex = 0;

            foreach ($row['cols'] as $colIndex => $col) {
               foreach ($col['elements'] as $element) {
                  if ($element['type'] == 'component') {
                     $hasComponent = true;
                     $componentIndex = $colIndex;
                     break;
                  }
               }
            }

            $prevColIndex = null;
            foreach ($row['cols'] as $colIndex => $col) {
               $renderedHTML = '';
               foreach ($col['elements'] as $element) {
                  $el = new AstroidElement($element['type'], $element, $this);
                  $renderedHTML .= $el->render();
               }
               if (empty($renderedHTML)) {
                  $bufferSize += $col['size'];
                  unset($row['cols'][$colIndex]);
               } else {
                  if ($hasComponent) {
                     $row['cols'][$componentIndex]['size'] = $row['cols'][$componentIndex]['size'] + $bufferSize;
                     $bufferSize = 0;
                  } else {
                     if (isset($row['cols'][$prevColIndex])) {
                        $row['cols'][$prevColIndex]['size'] = $row['cols'][$prevColIndex]['size'] + $bufferSize;
                     } else {
                        $row['cols'][$colIndex]['size'] = $row['cols'][$colIndex]['size'] + $bufferSize;
                     }
                     $bufferSize = 0;
                  }
                  $prevColIndex = $colIndex;
               }
            }
            if (!empty($row['cols'])) {
               if ($bufferSize) {
                  if ($hasComponent) {
                     $row['cols'][$componentIndex]['size'] = $row['cols'][$componentIndex]['size'] + $bufferSize;
                  } else {
                     if ($prevColIndex !== null) {
                        $row['cols'][$prevColIndex]['size'] = $row['cols'][$prevColIndex]['size'] + $bufferSize;
                     }
                  }
               }
               foreach ($row['cols'] as $col) {
                  $renderedHTML = '';
                  foreach ($col['elements'] as $element) {
                     $el = new AstroidElement($element['type'], $element, $this);
                     $this->setLog("Rending Element : " . $el->getValue('title'));
                     $template_positions_display = JComponentHelper::getParams('com_templates')->get('template_positions_display');
                     if (@$_GET['wf'] == 1 && $template_positions_display) {
                        $renderedHTML .= $el->renderWireframe();
                     } else {
                        $renderedHTML .= $el->render();
                     }
                  }
                  if (!empty($renderedHTML)) {
                     $columnObject = new AstroidElement("column", $col, $this);
                     $col_stylesEnable = $columnObject->getStyles() ? true : false;
                     $col_animationEnable = $columnObject->getAnimation() ? true : false;
                     $col_animationDelay = $columnObject->getAnimationDelay() ? true : false;
                     $col_animationDuration = $columnObject->getAnimationDuration() ? true : false;
                     $columnHTML .= '<div id="' . $columnObject->getID() . '" class="' . $columnObject->getClass() . (($col_stylesEnable) ? '" style="' . $columnObject->getStyles() : '') . (($col_animationEnable) ? '" data-animation= "' . $columnObject->getAnimation() : '') . (($col_animationDelay && $col_animationEnable) ? '" data-animation-delay="' . $columnObject->getAnimationDelay() : '') . (($col_animationDuration && $col_animationEnable) ? '" data-animation-duration="' . $columnObject->getAnimationDuration() : '') . '" ' . $columnObject->getAttributes() . '>';
                     $columnHTML .= $renderedHTML;
                     $columnHTML .= '</div>';
                  }
               }
            }
            if (!empty($columnHTML)) {
               $layout_type = ($sppb && $hasComponent) ? 'no-container' : $layout_type;
               $no_gutter = false;
               switch ($layout_type) {
                  case 'no-container':
                  case 'custom-container':
                  case 'container-with-no-gutters':
                  case 'container-fluid-with-no-gutters':
                     $no_gutter = true;
                     break;
               }

               $rowObject = new AstroidElement("row", $row, $this);
               $row_stylesEnable = $rowObject->getStyles() ? true : false;
               $row_animationEnable = $rowObject->getAnimation() ? true : false;
               $row_animationDelay = $rowObject->getAnimationDelay() ? true : false;
               $row_animationDuration = $rowObject->getAnimationDuration() ? true : false;
               $rowHTML .= '<div  id="' . $rowObject->getID() . '" class="row' . ($no_gutter ? ' no-gutters' : '') . (!empty($rowObject->getClass()) ? ' ' . $rowObject->getClass() : '') . (($row_stylesEnable) ? '" style="' . $rowObject->getStyles() : '') . (($row_animationEnable) ? '" data-animation= "' . $rowObject->getAnimation() : '') . (($row_animationDelay && $row_animationEnable) ? '" data-animation-delay="' . $rowObject->getAnimationDelay() : '') . (($row_animationDuration && $row_animationEnable) ? '" data-animation-duration="' . $rowObject->getAnimationDuration() : '') . '" ' . $rowObject->getAttributes() . '>';
               $rowHTML .= $columnHTML;
               $rowHTML .= '</div>';
            }
         }
         if (!empty($rowHTML)) {
            $stylesEnable = $sectionObject->getStyles() ? true : false;
            $animationEnable = $sectionObject->getAnimation() ? true : false;
            $sectionHTML .= '<section  id="' . $sectionObject->getID() . '" class="' . $sectionObject->getClass() . (($stylesEnable) ? '" style="' . $sectionObject->getStyles() : '') . (($animationEnable) ? '" data-animation= "' . $sectionObject->getAnimation() : '') . ((!empty($sectionObject->getAnimationDelay()) && $animationEnable) ? '" data-animation-delay="' . $sectionObject->getAnimationDelay() : '')  . ((!empty($sectionObject->getAnimationDuration()) && $animationEnable) ? '" data-animation-duration="' . $sectionObject->getAnimationDuration() : '') . '" ' . $sectionObject->getAttributes() . '>';

            $section_layout_type = ($sppb && $hasComponent) ? '' : $section_layout_type;
            if (!empty($section_layout_type)) {
               $sectionHTML .= "<div class='" . $section_layout_type . "'>";
            }
            $sectionHTML .= $rowHTML;
            if (!empty($section_layout_type)) {
               $sectionHTML .= '</div>';
            }
            $sectionHTML .= '</section>';
         }
         echo $sectionHTML;
      }
      echo '</div>';
      echo '</div>';
      echo '</div>';
      echo '</div>';
      $this->setLog("Rending Complete!", "success");
   }

   public function getLayoutStyles()
   {
      $styles = [];
      $template_layout = $this->params->get('template_layout', 'wide');
      if ($template_layout != 'boxed') {
         return false;
      }
      $layout_background_image = $this->params->get('layout_background_image', '');
      if (!empty($layout_background_image)) {
         $styles[] = 'background-image:url(' . JURI::root() . $this->SeletedMedia() . '/' . $layout_background_image . ')';
         $styles[] = 'background-repeat:' . $this->params->get('layout_background_repeat', 'inherit');
         $styles[] = 'background-size:' . $this->params->get('layout_background_size', 'inherit');
         $styles[] = 'background-position:' . $this->params->get('layout_background_position', 'inherit');
         $styles[] = 'background-attachment:' . $this->params->get('layout_background_attachment', 'inherit');
      }
      return implode(';', $styles);
   }

   public function renderErrorLayout()
   {
      $params = $this->params;
      $template_layout = $this->params->get('template_layout', 'wide');
      echo '<div style="' . $this->getLayoutStyles() . '" class="astroid-layout astroid-layout-' . $template_layout . '">';
      echo '<div class="astroid-wrapper">';
      $this->loadLayout('error');
      echo '</div>';
      echo '</div>';
      $this->setLog("Rending Complete!", "success");
   }

   static public function slugify($text)
   {
      // replace non letter or digits by -
      $text = preg_replace('~[^\pL\d]+~u', '-', $text);
      // transliterate
      $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
      // remove unwanted characters
      $text = preg_replace('~[^-\w]+~', '', $text);
      // trim
      $text = trim($text, '-');
      // remove duplicate -
      $text = preg_replace('~-+~', '-', $text);
      // lowercase
      $text = strtolower($text);
      if (empty($text)) {
         return 'n-a';
      }
      return $text;
   }

   public function modulePosition($position = '', $style = 'none')
   {
      if (empty($position)) {
         return '';
      }
      $this->setLog("Rending Module Position : " . $position);
      $return = '';

      $modules = JModuleHelper::getModules($position);
      if (count($modules)) {
         $return .= '<jdoc:include type="modules" name="' . $position . '" style="' . $style . '" />';
      }
      return $return;
   }

   public function renderModulePosition($position, $style = 'none')
   {
      if (empty($position)) {
         return '';
      }
      $return = '';
      $beforeContent = $this->getAstroidContent($position, 'before');
      if (!empty($beforeContent)) {
         $return .= $beforeContent;
      }
      if (!empty(JModuleHelper::getModules($position))) {
         $return .= $this->modulePosition($position, $style);
      }
      $afterContent = $this->getAstroidContent($position, 'after');
      if (!empty($afterContent)) {
         $return .= $afterContent;
      }
      return $return;
   }

   public function getAstroidContent($position, $load = 'after')
   {
      $contents = $this->getAstroidPositionLayouts();
      $return = '';
      if (isset($contents[$position]) && !empty($contents[$position])) {
         foreach ($contents[$position] as $layout) {
            $layout = explode(':', $layout);
            if ($layout[1] == $load) {
               $return .= $this->loadLayout($layout[0], false);
            }
         }
      }
      return $return;
   }

   public function getAstroidPositionLayouts()
   {
      $astroidcontentlayouts = $this->params->get('astroidcontentlayouts', 'social:astroid-top-social:after,contactinfo:astroid-top-contact:after');
      $return = [];
      if (!empty($astroidcontentlayouts)) {
         $astroidcontentlayouts = explode(',', $astroidcontentlayouts);
         foreach ($astroidcontentlayouts as $astroidcontentlayout) {
            $astroidcontentlayout = explode(':', $astroidcontentlayout);
            if (isset($return[$astroidcontentlayout[1]])) {
               $return[$astroidcontentlayout[1]][] = $astroidcontentlayout[0] . ':' . $astroidcontentlayout[2];
            } else {
               $return[$astroidcontentlayout[1]] = [];
               $return[$astroidcontentlayout[1]][] = $astroidcontentlayout[0] . ':' . $astroidcontentlayout[2];
            }
         }
      }
      return $return;
   }

   public function getStyleName($template_directory, $custom = false)
   {
      if (!$custom) {
         $scss_files = $this->getDir($template_directory . 'scss', 'scss');
         $name = '';
         foreach ($scss_files as $scss) {
            $name .= md5_file($scss['basepath']);
         }


         $variables = $this->getThemeVariables();
         $name .= serialize($variables);

         $cssname = 'style-' . md5($name);

         if (!file_exists($template_directory . 'css/' . $cssname . '.css')) {
            //ini_set('xdebug.max_nesting_level', 3000);
            AstroidFrameworkHelper::clearCache($this->template);
            AstroidFrameworkHelper::compileSass($template_directory . 'scss', $template_directory . 'css', 'style.scss', $cssname . '.css', $variables);
         }
         return $cssname . '.css';
      } else {

         if (!file_exists($template_directory . 'scss/custom') || !file_exists($template_directory . 'scss/custom/custom.scss')) {
            return '';
         }

         $scss_files = $this->getDir($template_directory . 'scss/custom', 'scss');
         $name = '';
         foreach ($scss_files as $scss) {
            $name .= md5_file($scss['basepath']);
         }
         $cssname = 'custom-' . md5($name);
         if (!file_exists($template_directory . 'css/' . $cssname . '.css')) {
            AstroidFrameworkHelper::clearCache($this->template, 'custom');
            AstroidFrameworkHelper::compileSass($template_directory . 'scss/custom', $template_directory . 'css', 'custom.scss', $cssname . '.css');
         }
         return $cssname . '.css';
      }
   }

   public function getThemeVariables()
   {
      $variables = [];
      $variables['blue'] = $this->params->get('theme_blue', '#007bff');
      $variables['indigo'] = $this->params->get('theme_indigo', '#6610f2');
      $variables['purple'] = $this->params->get('theme_purple', '#6f42c1');
      $variables['pink'] = $this->params->get('theme_pink', '#e83e8c');
      $variables['red'] = $this->params->get('theme_red', '#dc3545');
      $variables['orange'] = $this->params->get('theme_orange', '#fd7e14');
      $variables['yellow'] = $this->params->get('theme_yellow', '#ffc107');
      $variables['green'] = $this->params->get('theme_green', '#28a745');
      $variables['teal'] = $this->params->get('theme_teal', '#20c997');
      $variables['cyan'] = $this->params->get('theme_cyan', '#17a2b8');
      $variables['white'] = $this->params->get('theme_white', '#fff');
      $variables['gray100'] = $this->params->get('theme_gray100', '#f8f9fa');
      $variables['gray600'] = $this->params->get('theme_gray600', '#6c757d');
      $variables['gray800'] = $this->params->get('theme_gray800', '#343a40');
      $primary = $this->params->get('theme_primary', 'blue');
      $variables['primary'] = $variables[$primary];
      $secondary = $this->params->get('theme_secondary', 'gray600');
      $variables['secondary'] = $variables[$secondary];
      $success = $this->params->get('theme_success', 'green');
      $variables['success'] = $variables[$success];
      $info = $this->params->get('theme_info', 'cyan');
      $variables['info'] = $variables[$info];
      $warning = $this->params->get('theme_warning', 'yellow');
      $variables['warning'] = $variables[$warning];
      $danger = $this->params->get('theme_danger', 'red');
      $variables['danger'] = $variables[$danger];
      $light = $this->params->get('theme_light', 'gray100');
      $variables['light'] = $variables[$light];
      $dark = $this->params->get('theme_dark', 'gray800');
      $variables['dark'] = $variables[$dark];
      /*
        $link_color = $this->params->get('theme_link_color', '#007bff');
        $variables['link-color'] = $link_color;
        $link_hover_color = $this->params->get('theme_link_hover_color', '#0056b3');
        $variables['link-hover-color'] = $link_hover_color;
       */

      $variables = $this->getVariableOverrides($variables);

      return $variables;
   }

   public function getVariableOverrides($variables)
   {
      $sass_overrides = $this->params->get('sass_overrides');
      $sass_overrides = \json_decode($sass_overrides, true);
      if (empty($sass_overrides)) {
         return $variables;
      }

      foreach ($sass_overrides as $sass_override) {
         $variable = $sass_override['variable'];
         if (!empty($variable) && !empty($sass_override['value'])) {
            if (substr($variable, 0, 1) === "$") {
               $variable = ltrim($variable, '$');
            }
            $variables[$variable] = $sass_override['value'];
         }
      }
      return $variables;
   }

   public function getColors()
   {
      $colors = [];
      $variables = $this->params->get('sass_variables', []);
      foreach ($variables as $key => $variable) {
         $colors[$key] = $this->params->get($variable, '');
      }
      return $colors;
   }

   public function getDir($dir, $extension = null, &$results = array())
   {
      $files = scandir($dir);

      foreach ($files as $key => $value) {
         $path = realpath($dir . DIRECTORY_SEPARATOR . $value);
         if (!is_dir($path)) {
            $pathinfo = pathinfo($path);
            if ($extension !== null && $pathinfo['extension'] == $extension) {
               $include_path = str_replace(JPATH_THEMES, '', $path);
               $component_name = str_replace('.min', '', $pathinfo['filename']);
               $results[$component_name] = ['component_name' => $component_name, 'name' => $pathinfo['basename'], 'path' => $include_path, 'basepath' => $path];
            } elseif ($extension === null) {
               $include_path = str_replace(JPATH_THEMES, '', $path);
               $results[] = ['name' => $pathinfo['basename'], 'path' => $include_path];
            }
         } else if ($value != "." && $value != "..") {
            $this->getDir($path, $extension, $results);
         }
      }

      return $results;
   }

   public function loadTemplateCSS($components = '', $error = false)
   {
      $this->setLog("Loading Stylesheets");
      $components = explode(',', $components);
      $template_directory = JPATH_THEMES . "/" . $this->template . "/css/";
      $custom_compiled_css = $this->getStyleName(JPATH_THEMES . "/" . $this->template . '/', true);
      if (!empty($custom_compiled_css)) {
         array_unshift($components, $custom_compiled_css);
      }
      $compiled_css = $this->getStyleName(JPATH_THEMES . "/" . $this->template . '/');
      array_unshift($components, $compiled_css);
      $document = JFactory::getDocument();
      foreach ($components as $component) {
         if (file_exists($template_directory . $component)) {
            if ($error) {
               echo '<link href="' . JURI::root() . 'templates/' . $this->template . "/css/" . $component . '?' . $document->getMediaVersion() . '" rel="stylesheet" />';
            } else {
               $document->addStyleSheet(JURI::root() . 'templates/' . $this->template . "/css/" . $component . '?' . $document->getMediaVersion());
            }
         }
      }
      if ($error) {
         echo '<link href="' . JURI::root() . 'media/astroid/assets/css/animate.min.css?' . $document->getMediaVersion() . '" rel="stylesheet" />';
      } else {
         $document->addStyleSheet(JURI::root() . 'media/astroid/assets/css/animate.min.css?' . $document->getMediaVersion());
      }
      $this->setLog("Stylesheet Loaded!", "success");
   }

   public function loadTemplateJS($components = '')
   {
      $this->setLog("Loading Javascripts");
      $components = explode(',', $components);
      $template_directory = JPATH_THEMES . "/" . $this->template . "/js/";
      $document = JFactory::getDocument();
      foreach ($components as $component) {
         if (file_exists($template_directory . $component)) {
            JHtml::_('script', JURI::root() . 'templates/' . $this->template . "/js/" . $component, array('version' => $document->getMediaVersion(), 'relative' => true));
         }
      }
      $this->setLog("Javascripts Loaded!", "success");
   }

   /*
    * 	Function to return classes imploded in the body tag on the website.
    */

   public function bodyClass($body_class, $language = '', $direction = '')
   {
      $template = JFactory::getApplication()->getTemplate(true);
      $class = [];
      $app = JFactory::getApplication();
      $menu = $app->getMenu()->getActive();
      $class[] = "site";
      $class[] = "astroid-framework";

      $option = $app->input->get('option', '', 'STRING');
      $view = $app->input->get('view', '', 'STRING');
      $layout = $app->input->get('layout', 'default', 'STRING');
      $task = $app->input->get('task', '', 'STRING');
      $header = $this->params->get('header', TRUE);
      $headerMode = $this->params->get('header_mode', 'horizontal', 'STRING');
      $Itemid = $app->input->get('Itemid', '', 'INT');

      if (!empty($option)) {
         $class[] = htmlspecialchars(str_replace('_', '-', $option));
      }
      if (!empty($view)) {
         $class[] = 'view-' . $view;
      }
      if (!empty($layout)) {
         $class[] = 'layout-' . $layout;
      }
      if (!empty($task)) {
         $class[] = 'task-' . $task;
      }
      if (!empty($Itemid)) {
         $class[] = 'itemid-' . $Itemid;
      }

      if ($header && !empty($headerMode) && $headerMode == 'sidebar') {
         $sidebarDirection = $this->params->get('header_sidebar_menu_mode', 'left');
         $class[] = "header-sidebar-" . $sidebarDirection;
      }

      if (isset($menu) && $menu) {
         if ($menu->params->get('pageclass_sfx')) {
            $class[] = $menu->params->get('pageclass_sfx');
         }
         if ($menu->get('alias')) {
            // menu alias without -alias appended will be removed in the next version.
            $class[] = $menu->get('alias');
            $class[] = $menu->get('alias') . '-alias';
         }
      }
      if (!empty($template->id)) {
         $class[] = 'tp-style-' . $template->id;
      }
      if (!empty($language)) {
         $class[] = $language;
      }

      if (!empty($direction)) {
         $class[] = $direction;
      }

      if (!empty($body_class)) {
         $class[] = $body_class;
      }

      return implode(' ', $class);
   }

   public function loadLayout($partial = '', $display = true, $params = null)
   {
      $this->setLog("Rending template partial : " . $partial);

      if (file_exists(JPATH_SITE . '/templates/' . $this->template . '/html/frontend/' . str_replace('.', '/', $partial) . '.php')) {
         $layout = new JLayoutFile($partial, JPATH_SITE . '/templates/' . $this->template . '/html/frontend');
      } else if (file_exists(self::$astroidTemplatePath . '/frontend/' . str_replace('.', '/', $partial) . '.php')) {
         $layout = new JLayoutFile($partial, self::$astroidTemplatePath . '/frontend');
      } else {
         return;
      }
      $data = [];
      $data['template'] = $this;
      if (!empty($params)) {
         $data['params'] = $params;
      }
      if ($display) {
         echo $layout->render($data);
      } else {
         return $layout->render($data);
      }
      $this->setLog("Template partial rendered!: " . $partial, 'success');
   }

   public function setLog($message, $type = 'info', $data = [])
   {
      $this->logs[] = new AstroidLog($type, $message, $data);
   }

   public function renderLogs()
   {
      echo '<div id="astroid-debug" class="p-4 border" style="position: fixed;left: 0;bottom: 0;height: 50vh;width: 300px;background: #fff;overflow-y: auto;">';
      foreach ($this->logs as $log) {
         echo $log->render();
      }
      echo '</div>';
   }

   /*
    * 	Checks to see if the Page Builder is used.
    * 	If true, then removing the container so page builder can have full control
    * 	Current supported page builders Quix, JD Builder, Sp Page Builder
    */

   public function isPageBuilder()
   {
      $jinput = JFactory::getApplication()->input;
      $option = $jinput->get('option', '');
      $view = $jinput->get('view', '');
      if (($option == "com_sppagebuilder" && $view == "page") || ($option == "com_quix" && $view == "page") || ($option == "com_jdbuilder" && $view == "page")) {
         return TRUE;
      } else {
         return FALSE;
      }
   }

   public function addStyledeclaration($styles, $device = 'desktop')
   {
      if ($this->cssFile) {
         $this->_styles[$device][] = $styles;
      } else {
         $document = JFactory::getDocument();
         $document->addStyledeclaration($styles);
      }
   }

   public function addScriptDeclaration($script)
   {
      $document = JFactory::getDocument();
      $document->addScriptDeclaration($script);
   }

   public function addScript($js)
   {
      $template_directory = JPATH_THEMES . "/" . $this->template . "/js/";
      if (file_exists($template_directory . $js)) {
         $this->_js[$js] = JURI::root() . 'templates/' . $this->template . "/js/" . $js;
      } else {
         $this->_js[$js] = $js;
      }
   }

   public function buildAstroidCSS($version, $css = '')
   {
      $prefix = 'astroid-';
      if ($this->cssFile) {
         $issetPreset = JFactory::getApplication()->input->get('preset', '');
         if (!empty($issetPreset)) {
            $prefix = 'preset-';
         }

         $template_dir = JPATH_SITE . '/templates/' . $this->template . '/css';
         if (!file_exists($template_dir . '/' . $prefix . $version . '.css')) {
            if (empty($issetPreset)) {
               AstroidFrameworkHelper::clearCache($this->template, 'astroid');
            }
            $styles = preg_grep('~^' . $prefix . '.*\.(css)$~', scandir($template_dir));
            foreach ($styles as $style) {
               unlink($template_dir . '/' . $style);
            }
            file_put_contents($template_dir . '/' . $prefix . $version . '.css', $css);
         }
      }
      $document = JFactory::getDocument();
      $document->addStyleSheet(JURI::root() . 'templates/' . $this->template . '/css/' . $prefix . $version . '.css');
   }

   public function loadCSSFile()
   {
      if ($this->cssFile) {
         $styles = [];
         foreach (['desktop', 'tablet', 'mobile'] as $device) {
            if ($device == 'mobile') {
               $styles[] = '@media (max-width: 767.98px) {' . implode('', $this->_styles[$device]) . '}';
            } elseif ($device == 'tablet') {
               $styles[] = '@media (max-width: 991.98px) {' . implode('', $this->_styles[$device]) . '}';
            } else {
               $styles[] = implode('', $this->_styles[$device]);
            }
         }
         $styles = implode('', $styles);
         $document = JFactory::getDocument();
         $mediaVersion = $document->getMediaVersion();
         $version = md5($styles);
         $this->buildAstroidCSS($version, $styles);
      }
   }

   public function getStyleDeclaration()
   {
      $styles = [];
      foreach (['desktop', 'tablet', 'mobile'] as $device) {
         if ($device == 'mobile') {
            $styles[] = '@media (max-width: 767.98px) {' . implode('', $this->_styles[$device]) . '}';
         } elseif ($device == 'tablet') {
            $styles[] = '@media (max-width: 991.98px) {' . implode('', $this->_styles[$device]) . '}';
         } else {
            $styles[] = implode('', $this->_styles[$device]);
         }
      }
      $styles = implode('', $styles);
      return $styles;
   }

   public function loadJS()
   {
      $document = JFactory::getDocument();
      foreach ($this->_js as $key => $js) {
         if ($key == 'custom.js') {
            $template_directory = JPATH_THEMES . "/" . $this->template . "/js/";
            if (!file_exists($template_directory . $key)) {
               continue;
            }
         }
         $document->addScript($js, ['version' => $document->getMediaVersion()]);
      }
   }

   public function _loadModule($errorContent)
   {

      // Expression to search for(module Position)
      $regex = '/{loadposition\s(.*?)}/i';

      preg_match_all($regex, $errorContent, $matches, PREG_SET_ORDER);

      if ($matches) {
         foreach ($matches as $match) {
            $matcheslist = explode(',', $match[1]);
            $position = trim($matcheslist[0]);
            $output = $this->_load($position);
            // We should replace only first occurrence in order to allow positions with the same name to regenerate their content:
            $errorContent = preg_replace("|$match[0]|", $output, $errorContent, 1);
         }
      }

      // Expression to search for(id)
      $regexmodid = '/{loadmoduleid\s([1-9][0-9]*)}/i';

      preg_match_all($regexmodid, $errorContent, $matchesmodid, PREG_SET_ORDER);

      // If no matches, skip this
      if ($matchesmodid) {
         foreach ($matchesmodid as $match) {
            $id = trim($match[1]);
            $output = $this->_loadid($id);

            // We should replace only first occurrence in order to allow positions with the same name to regenerate their content:
            $errorContent = preg_replace("|$match[0]|", $output, $errorContent, 1);
         }
      }

      return $errorContent;
   }

   public function _load($position)
   {
      $this->modules[$position] = '';
      $document = JFactory::getDocument();
      $renderer = $document->loadRenderer('module');
      $modules = JModuleHelper::getModules($position);
      ob_start();

      foreach ($modules as $module) {
         echo $renderer->render($module);
      }

      $this->modules[$position] = ob_get_clean();

      return $this->modules[$position];
   }

   public function _loadid($id)
   {
      $this->modules[$id] = '';
      $document = JFactory::getDocument();
      $renderer = $document->loadRenderer('module');
      $modules = JModuleHelper::getModuleById($id);
      ob_start();

      if ($modules->id > 0) {
         echo $renderer->render($modules);
      }

      $this->modules[$id] = ob_get_clean();

      return $this->modules[$id];
   }

   public function SeletedMedia()
   {
      $params = JComponentHelper::getParams('com_media');
      return $params->get('image_path', 'images');
   }

   public function _loadFontAwesome()
   {
      $plugin = JPluginHelper::getPlugin('system', 'astroid');
      $assets = JURI::root() . 'media' . '/' . 'astroid' . '/' . 'assets' . '/' . 'fontawesome';
      $plugin_params = new JRegistry($plugin->params);
      $astroid_load_fontawesome = $plugin_params->get('astroid_load_fontawesome', "cdn");
      $document = JFactory::getDocument();
      if ($astroid_load_fontawesome == "local") {
         $document->addStyleSheet($assets . '/css/font-awesome.css');
         $document->addStyleSheet($assets . '/webfonts');
      } elseif ($astroid_load_fontawesome == "cdn") {
         $document->addStyleSheet("https://use.fontawesome.com/releases/v" . AstroidFrameworkConstants::$fontawesome_version . "/css/all.css");
      }
   }

   public function getPresets()
   {
      $presets_path = JPATH_SITE . "/templates/{$this->template}/astroid/presets/";
      if (!file_exists($presets_path)) {
         return [];
      }
      $files = array_filter(glob($presets_path . '/' . '*.json'), 'is_file');
      $presets = [];
      foreach ($files as $file) {
         $json = file_get_contents($file);
         $data = \json_decode($json, true);
         $preset = ['title' => pathinfo($file)['filename'], 'colors' => [], 'preset' => [], 'thumbnail' => '', 'name' => pathinfo($file)['filename']];
         if (isset($data['title']) && !empty($data['title'])) {
            $preset['title'] = \JText::_($data['title']);
         }
         if (isset($data['thumbnail']) && !empty($data['thumbnail'])) {
            $preset['thumbnail'] = \JURI::root() . 'templates/' . $this->template . '/' . $data['thumbnail'];
         }
         if (isset($data['colors'])) {
            $colors = [];
            $properties = [];
            foreach ($data['colors'] as $prop => $color) {
               if (is_array($color)) {
                  foreach ($color as $subprop => $color2) {
                     if (!empty($color2)) {
                        $properties[$prop][$subprop] = $color2;
                        $colors[] = $color2;
                     }
                  }
               } else {
                  if (!empty($color)) {
                     $properties[$prop] = $color;
                     $colors[] = $color;
                  }
               }
            }
            $colors = array_keys(array_count_values($colors));
            $preset['colors'] = array_unique($colors);
            $preset['preset'] = $properties;
         }
         $presets[] = $preset;
      }
      return $presets;
   }

   public function inspect()
   {
      // fix for typography
      $extension = \JTable::getInstance('extension');
      $id = $extension->find(array('element' => 'astroid', 'type' => 'library'));
      $extension->load($id);
      $frameworkInfo = json_decode($extension->manifest_cache, true);
      if ($frameworkInfo['version'] < 2.3) {
         foreach (['body', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6'] as $typo) {
            $typoType = $this->params->get($typo . '_typography');
            if (trim($typoType) == 'custom') {
               $typoOption = $typo . '_typography_options';
               $typoParams = $this->params->get($typoOption);
               foreach (['font_size', 'font_size_unit', 'letter_spacing', 'letter_spacing_unit', 'line_height', 'line_height_unit'] as $prop) {
                  if (!is_string($typoParams->{$prop})) {
                     $typoParams->{$prop} = $typoParams->{$prop}->desktop;
                  }
               }
               $this->params->set($typoOption, $typoParams);
            }
         }

         $menuType = $this->params->get('menus_typography');
         if (trim($menuType) == 'custom') {
            $menu_font = $this->params->get('menu_typography_options');
            foreach (['font_size', 'font_size_unit', 'letter_spacing', 'letter_spacing_unit', 'line_height', 'line_height_unit'] as $prop) {
               if (!is_string($menu_font->{$prop})) {
                  $menu_font->{$prop} = $menu_font->{$prop}->desktop;
               }
            }
            $this->params->set('menu_typography_options', $menu_font);
         }

         $submenuType = $this->params->get('submenus_typography');
         if (trim($submenuType) == 'custom') {
            $submenu_font = $this->params->get('submenu_typography_options');
            foreach (['font_size', 'font_size_unit', 'letter_spacing', 'letter_spacing_unit', 'line_height', 'line_height_unit'] as $prop) {
               if (!is_string($submenu_font->{$prop})) {
                  $submenu_font->{$prop} = $submenu_font->{$prop}->desktop;
               }
            }
            $this->params->set('submenu_typography_options', $submenu_font);
         }
      }
   }
}

class AstroidLog
{

   protected $type;
   protected $message;
   protected $data;

   public function __construct($type, $message, $data)
   {
      $this->type = $type;
      $this->message = $message;
      $this->data = $data;
      $this->created = time();
   }

   public function render()
   {
      $class = $this->type == 'error' ? 'danger' : $this->type;
      echo '<p class="text-' . $class . '">' . $this->message . '</p>';
   }
}
