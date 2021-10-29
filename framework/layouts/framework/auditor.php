<?php

/**
 * @package   Astroid Framework
 * @author    JoomDev https://www.joomdev.com
 * @copyright Copyright (C) 2009 - 2020 JoomDev.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */
defined('JPATH_BASE') or die;
extract($displayData);
jimport('astroid.framework.template');
jimport('astroid.framework.element');

$application = JFactory::getApplication();
$document = JFactory::getDocument();
$config = JFactory::getConfig();

$atm = $application->input->get('atm', 0, 'INT');

if ($atm) {
   $joomla_url = JRoute::_('index.php?option=com_advancedtemplates');
} else {
   $joomla_url = JRoute::_('index.php?option=com_templates');
}

$lang = JFactory::getLanguage();
$langdir = $lang->get('rtl') ? 'rtl' : 'ltr';
$assets = JURI::root() . 'media' . '/' . 'astroid' . '/' . 'assets' . '/';

// adding styles
$stylesheets = [];
$stylesheets[] = 'https://fonts.googleapis.com/css?family=Nunito:300,400,600';
$stylesheets[] = $assets . '/vendor/fontawesome/css/font-awesome.css';

$stylesheets[] = $assets . 'css' . '/' . 'astroid-framework.css?v=' . $document->getMediaVersion();
$stylesheets[] = $assets . 'css' . '/' . 'admin.css?v=' . $document->getMediaVersion();
?>
<!DOCTYPE html>
<html lang="<?php echo strtolower($lang->getTag()); ?>" dir="<?php echo $langdir; ?>">

<head>
   <meta charset="utf-8" />
   <meta name="generator" content="Astroid Framework | Template Manager" />
   <title>Astroid Auditor</title>
   <link href="<?php echo $assets . 'images' . '/' . 'favicon.png'; ?>" rel="shortcut icon" type="image/vnd.microsoft.icon" />
   <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
   <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
      <![endif]-->
   <!--[if IE]><script src="<?php echo JURI::root(); ?>media/system/js/html5fallback.js?<?php echo $document->getMediaVersion(); ?>"></script><![endif]-->
   <!--[if IE]><script src="<?php echo JURI::root(); ?>media/system/js/polyfill.filter.js?<?php echo $document->getMediaVersion(); ?>"></script><![endif]-->
   <!--[if IE]><script src="<?php echo JURI::root(); ?>media/jui/js/html5.js?<?php echo $document->getMediaVersion(); ?>"></script><![endif]-->
   <script>
      var SITE_URL = '<?php echo JURI::root(); ?>';
      var BASE_URL = '<?php echo JURI::root(); ?>administrator/';
      var ASTROID_IS_MOBILE = false;
   </script>
   <style>
      <?php foreach (AstroidFramework::$styles as $style) {
         echo $style;
      } ?>
   </style>
   <?php
   foreach ($stylesheets as $stylesheet) {
      echo '<link href="' . $stylesheet . '?' . $document->getMediaVersion() . '" type="text/css" rel="stylesheet" />';
   }
   foreach (AstroidFramework::$stylesheets as $stylesheet) {
      echo '<link href="' . $stylesheet . '?' . $document->getMediaVersion() . '" type="text/css" rel="stylesheet" />';
   }
   ?>
   <?php
   foreach (AstroidFramework::$javascripts['head'] as $script) {
      echo '<script src="' . $script . '?' . $document->getMediaVersion() . '"></script>';
   }
   ?>
   <script>
      <?php
      foreach (AstroidFramework::$scripts['head'] as $js) {
         echo $js;
      }
      ?>
      var astroid_shortcut_enable = false;
      var TEMPLATE_NAME = '';
      var IS_MANAGER = false;
   </script>
   <style>
      #astroid-wrapper #astroid-sidebar-wrapper {
         background: #e9f2ee;
      }

      .astroid-manager-navbar {
         background: #e9f2ee;
         box-shadow: 0 0 10px #b0e2cc;
      }

      .astroid-logo {
         box-shadow: 0 0 10px #b0e2cc;
      }

      body {
         background-color: #f9fdfb;
      }

      #astroid-migration-loading {
         background: #fff;
         position: fixed;
         top: 0;
         left: 0;
         width: 100%;
         height: 100%;
         z-index: 9999;
         display: flex;
         justify-content: center;
         align-items: center;
      }

      #astroid-migration-loading.loaded span {
         color: #4CAF50;
         transition: 1s linear color;
      }

      #astroid-migration-loading span {
         display: block;
         text-align: right;
         font-size: 30px;
         color: #fff;
         transition: 1s linear color;
      }

      #astroid-migration-loading>div {
         max-width: 400px;
      }

      #astroid-migrating-loading {
         background: rgba(255, 255, 255, 0.9);
         position: fixed;
         top: 0;
         left: 0;
         width: 100%;
         height: 100%;
         z-index: 9999;
         display: flex;
         justify-content: center;
         align-items: center;
      }

      #astroid-migrating-loading>div>div {
         display: block;
         text-align: right;
         font-size: 18px;
         color: #4CAF50;
      }

      #astroid-migrating-loading>div {
         max-width: 300px;
      }
   </style>
   <?php
   $templates = Astroid\Helper\Template::getAstroidTemplates();
   $templateGroups = [];
   foreach ($templates as $template) {
      if (!isset($templateGroups[$template['name']])) {
         $templateGroups[$template['name']] = [];
      }
      $templateGroups[$template['name']][] = $template;
   }
   ?>
</head>

<body ng-app="astroid-framework" id="astroid-framework" ng-controller="astroidController">
   <div id="astroid-migration-loading">
      <div>
         <img src="http://joomla.local/media/astroid/assets/images/logo-dark-wide.png" width="100%" />
         <span>auditor</span>
      </div>
   </div>
   <div ng-show="migrating" class="d-none" id="astroid-migrating-loading">
      <div>
         <img src="http://joomla.local/media/astroid/assets/images/logo-dark-wide.png" width="100%" />
         <div class="mt-2"><span class="fas fa-circle-notch fa-spin"></span> Migrating Please Wait...</div>
      </div>
   </div>
   <input type="hidden" id="astroid-admin-token" value="<?php echo JSession::getFormToken(); ?>" />
   <div id="astroid-wrapper" class="container pt-5">
      <div id="astroid-manager-disabled"></div>
      <div class="row position-relative pt-5">
         <div id="astroid-content-wrapper w-100" class="col">
            <div class="container p-5">
               <h3 class="astroid-group-title">Introduction</h3>
               <div class="astroid-tab-pane">
                  <div class="astroid-form-fieldset-section mb-5" style="font-size: 16px;">
                     <h3 class="d-inline">Astroid Auditor</h3> is a tool to migrate your existing Astroid based templates so that you don't have to process template migration manually, It helps in figuring out the template's compatibility with current (latest) framework to make sure all framework features should will work properly within the template.
                  </div>
               </div>
               <h3 class="astroid-group-title">Installed Astroid Templates</h3>
               <div class="astroid-tab-pane">
                  <div class="astroid-form-fieldset-section mb-5">
                     <div class="row">
                        <?php $index = 1; ?>
                        <?php foreach ($templateGroups as $groupname => $group) { ?>
                           <div class="col-12 mb-3">
                              <div class="row">
                                 <div class="col-8">
                                    <h3><?php echo '#' . ($index++) . '. ' . $groupname; ?> <button ng-disabled="auditing" ng-click="auditTemplate('<?php echo $groupname; ?>')" type="button" class="btn btn-success btn-round btn-wide"><span ng-show="auditingTemplate != '<?php echo $groupname; ?>'">Audit Now</span><span ng-show="auditingTemplate == '<?php echo $groupname; ?>'">Auditing <span class="fas fa-circle-notch fa-spin"></span></span></button></h3>
                                    <div>
                                       <?php foreach ($group as $template) { ?>
                                          <code><?php echo $template['title']; ?></code><br />
                                       <?php } ?>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        <?php } ?>
                     </div>
                  </div>
               </div>
               <h3 ng-if="report != null" class="astroid-group-title">{{ report.data.mergable.length+report.data.unmergable.length }} Files<span class="text-secondary"> audited in </span>{{ report.template }}</h3>
               <div ng-if="report != null" class="astroid-tab-pane">
                  <div class="astroid-form-fieldset-section mb-5">
                     <div id="reportAccordion">
                        <div ng-if="report.data.mergable.length" class="card mb-4">
                           <div data-toggle="collapse" data-target="#mergableFiles" aria-expanded="true" aria-controls="mergableFiles" class="card-header cursor-pointer" id="mergableFilesHeading">
                              <h5 class="my-2">{{ report.data.mergable.length }} Files <span class="text-success"><span class="fas fa-check-circle"></span> Able to migrate.</span> <span class="text-secondary">These files can be automatically migrate.</span></h5>
                           </div>

                           <div id="mergableFiles" class="collapse" aria-labelledby="mergableFilesHeading" data-parent="#reportAccordion">
                              <div class="card-body p-0">
                                 <table class="table">
                                    <tr>
                                       <th class="align-middle">
                                          #
                                       </th>
                                       <th class="align-middle">
                                          File
                                       </th>
                                       <th width="100">
                                          <button type="button" class="btn btn-success btn-round btn-wide" ng-click="doMigrate()">Migrate</button>
                                       </th>
                                    </tr>
                                    <tr ng-repeat="item in report.data.mergable | orderBy">
                                       <td>
                                          {{ $index + 1 }}
                                       </td>
                                       <td>
                                          <code>{{ item }}</code>
                                       </td>
                                       <td></td>
                                    </tr>
                                 </table>
                              </div>
                           </div>
                        </div>
                        <div ng-if="report.data.unmergable.length" class="card mb-4">
                           <div data-toggle="collapse" data-target="#unmergableFiles" aria-expanded="true" aria-controls="unmergableFiles" class="card-header cursor-pointer" id="unmergableFilesHeading">
                              <h5 class="my-2">{{ report.data.unmergable.length }} Files <span class="text-danger"><span class="fas fa-times-circle"></span> Can’t automatically migrate.</span> <span class="text-secondary">Don't worry, you can still migrate manually.</span></h5>
                           </div>

                           <div id="unmergableFiles" class="collapse" aria-labelledby="unmergableFilesHeading" data-parent="#reportAccordion">
                              <div class="card-body">
                                 <table class="table">
                                    <tr ng-repeat="item in report.data.unmergable">
                                       <td>
                                          <code>{{ item }}</code>
                                       </td>
                                    </tr>
                                 </table>
                              </div>
                           </div>
                        </div>
                        <div ng-if="0" class="card">
                           <div data-toggle="collapse" data-target="#notfoundFiles" aria-expanded="true" aria-controls="notfoundFiles" class="card-header cursor-pointer" id="notfoundFilesHeading">
                              <h5 class="my-2">{{ report.data.notfound.length }} Files <span class="text-warning"><span class="fas fa-exclamation-triangle"></span> Not found.</span> <span class="text-secondary">Don't worry, you do not need these files.</span></h5>
                           </div>

                           <div id="notfoundFiles" class="collapse" aria-labelledby="notfoundFilesHeading" data-parent="#reportAccordion">
                              <div class="card-body">
                                 <table class="table">
                                    <tr ng-repeat="item in report.data.notfound">
                                       <td>
                                          <code>{{ item }}</code>
                                       </td>
                                    </tr>
                                 </table>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <div class="astroid-manager-navbar w-100 fixed-top m-0 row">
            <div class="d-flex justify-content-center align-items-center">
               <div>
                  <img style="vertical-align: baseline;" width="150" src="<?php echo $assets . 'images' . '/' . 'logo-dark-wide.png'; ?>" /> <span style="color: #009688;">auditor</span>
               </div>
            </div>
            <ul class="list-inline ml-auto mb-0 p-0 text-right">
               <li class="d-inline-block"><a id="close-editor" title="<?php echo JText::_('TPL_ASTROID_BACK_TO_JOOMLA'); ?>" href="<?php echo $joomla_url; ?>" class="astroid-sidebar-btn astroid-back-btn d-flex align-items-center">
                     <div><i class="fas fa-times"></i><span><?php echo JText::_('ASTROID_TEMPLATE_CLOSE'); ?></span></div>
                  </a></li>
            </ul>
         </div>
      </div>
   </div>
   <?php
   $scripts = [];
   $scripts[] = $assets . 'vendor' . '/' . 'jquery' . '/' . 'jquery-3.2.1.min.js?v=' . $document->getMediaVersion();
   $scripts[] = $assets . 'vendor' . '/' . 'jquery' . '/' . 'jquery.cookie.js?v=' . $document->getMediaVersion();
   $scripts[] = $assets . 'vendor' . '/' . 'bootstrap' . '/' . 'js/popper.min.js?v=' . $document->getMediaVersion();
   $scripts[] = $assets . 'vendor' . '/' . 'bootstrap' . '/' . 'js/bootstrap.min.js?v=' . $document->getMediaVersion();
   $scripts[] = $assets . 'vendor' . '/' . 'dropzone' . '/' . 'dropzone.min.js?v=' . $document->getMediaVersion();
   $scripts[] = $assets . 'vendor' . '/' . '/moment/moment.min.js?v=' . $document->getMediaVersion();
   $scripts[] = $assets . 'vendor' . '/' . 'moment/moment-timezone.min.js?v=' . $document->getMediaVersion();
   $scripts[] = $assets . 'vendor' . '/' . 'moment/moment-timezone-with-data-2012-2022.min.js?v=' . $document->getMediaVersion();
   $scripts[] = $assets . 'vendor' . '/' . 'bootstrap/js/bootstrap-datetimepicker.min.js?v=' . $document->getMediaVersion();
   $scripts[] = $assets . 'vendor' . '/' . 'bootstrap-slider' . '/' . 'js' . '/' . 'bootstrap-slider.min.js?v=' . $document->getMediaVersion();
   $scripts[] = $assets . 'vendor' . '/' . 'angular' . '/' . 'angular.min.js?v=' . $document->getMediaVersion();
   $scripts[] = $assets . 'vendor' . '/' . 'angular' . '/' . 'angular-animate.js?v=' . $document->getMediaVersion();
   $scripts[] = $assets . 'vendor' . '/' . 'angular' . '/' . 'sortable.min.js?v=' . $document->getMediaVersion();
   $scripts[] = $assets . 'vendor' . '/' . 'angular' . '/' . 'angular-legacy-sortable.js?v=' . $document->getMediaVersion();
   foreach ($scripts as $script) {
      echo "<script src='" . $script . "'></script>";
   }
   ?>
   <script>
      var TIMEZONE = '<?php echo $config = JFactory::getConfig()->get('offset'); ?>';
      moment.tz.setDefault('<?php echo $config = JFactory::getConfig()->get('offset'); ?>');
   </script>
   <script src="<?php echo $assets . 'js' . '/' . 'parsley.min.js?v=' . $document->getMediaVersion(); ?>"></script>
   <script src="<?php echo $assets . 'js' . '/' . 'notify.min.js?v=' . $document->getMediaVersion(); ?>"></script>
   <script src="<?php echo $assets . 'js' . '/' . 'jquery.hotkeys.js?v=' . $document->getMediaVersion(); ?>"></script>
   <script src="<?php echo $assets . 'js' . '/' . 'jquery.nicescroll.min.js?v=' . $document->getMediaVersion(); ?>"></script>
   <script src="<?php echo $assets . 'js' . '/' . 'astroid.min.js?v=' . $document->getMediaVersion(); ?>"></script>

   <script type="text/javascript">
      astroidFramework.controller('astroidController', function($scope, $http) {
         $scope.report = null;
         $scope.auditing = false;
         $scope.auditingTemplate = '';
         $scope.migrating = false;

         $scope.startAuditing = function(_template) {
            $scope.auditingTemplate = _template;
            $scope.auditing = true;
         }

         $scope.stopAuditing = function(_template) {
            $scope.auditing = false;
            $scope.auditingTemplate = '';
         }

         $scope.auditTemplate = function(_template) {
            $scope.startAuditing(_template);

            var transform = function(data) {
               return $.param(data);
            }


            $http.post("<?php echo JURI::base(); ?>index.php?option=com_ajax&astroid=audit", {
                  template: _template
               }, {
                  headers: {
                     'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
                  },
                  transformRequest: transform
               })
               .then(function(response) {
                  $scope.stopAuditing();
                  if (response.data.status == 'error') {
                     Admin.notify(response.data.message, 'error');
                     return;
                  }

                  $scope.report = {
                     template: _template,
                     data: response.data.data
                  };

                  setTimeout(function() {
                     $('body').animate({
                        scrollTop: $('#reportAccordion').offset().top
                     }, 100);
                  });
               });
         }

         $scope.doMigrate = function() {
            var transform = function(data) {
               return $.param(data);
            }
            $scope.migrating = true;
            var _template = $scope.report.template;
            $http.post("<?php echo JURI::base(); ?>index.php?option=com_ajax&astroid=migrate", {
                  template: _template
               }, {
                  headers: {
                     'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
                  },
                  transformRequest: transform
               })
               .then(function(response) {
                  if (response.data.status == 'error') {
                     Admin.notify(response.data.message, 'error');
                  } else {
                     Admin.notify(response.data.data, 'success');
                  }
                  setTimeout(function() {
                     window.location = '<?php echo JURI::base(); ?>index.php?option=com_ajax&astroid=auditor';
                  }, 3000);
               });
         }
      });
   </script>
   <?php
   foreach (AstroidFramework::$javascripts['body'] as $script) {
      echo '<script src="' . $script . '?' . $document->getMediaVersion() . '"></script>';
   }
   ?>
   <script>
      <?php
      foreach (AstroidFramework::$scripts['body'] as $js) {
         echo $js;
      }
      ?>

         (function($) {
            $(window).on('load', function() {
               $('#astroid-migration-loading').addClass('loaded');
               $('#astroid-migrating-loading').removeClass('d-none');

               setTimeout(function() {
                  $('#astroid-migration-loading').fadeOut();
               }, 1200);
            });
         })(jQuery);
   </script>
</body>

</html>