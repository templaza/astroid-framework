<?php

/**
 * @package   Astroid Framework
 * @author    JoomDev https://www.joomdev.com
 * @copyright Copyright (C) 2009 - 2020 JoomDev.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 * 	DO NOT MODIFY THIS FILE DIRECTLY AS IT WILL BE OVERWRITTEN IN THE NEXT UPDATE
 *  You can easily override all files under /frontend/ folder.
 * 	Just copy the file to ROOT/templates/YOURTEMPLATE/html/frontend/header/ folder to create and override
 */
// No direct access.
defined('_JEXEC') or die;
extract($displayData);
?>
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
                        Admin.refreshScroll();
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