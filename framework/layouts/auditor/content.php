<?php
defined('JPATH_BASE') or die;
extract($displayData);
$app = JFactory::getApplication();
$atm = $app->input->get('atm', 0, 'INT');

if ($atm) {
    $joomla_url = \JRoute::_('index.php?option=com_advancedtemplates');
} else {
    $joomla_url = \JRoute::_('index.php?option=com_templates');
}

$templates = Astroid\Helper\Template::getAstroidTemplates(true);
$templateGroups = [];
foreach ($templates as $template) {
    if (!isset($templateGroups[$template->template])) {
        $templateGroups[$template->template] = [];
    }
    $templateGroups[$template->template][] = $template;
}
?>
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
                                            <code><?php echo $template->title; ?></code><br />
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
                        <div onclick="Admin.refreshScroll();" data-toggle="collapse" data-target="#mergableFiles" aria-expanded="true" aria-controls="mergableFiles" class="card-header cursor-pointer" id="mergableFilesHeading">
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
                        <div onclick="Admin.refreshScroll();" data-toggle="collapse" data-target="#unmergableFiles" aria-expanded="true" aria-controls="unmergableFiles" class="card-header cursor-pointer" id="unmergableFilesHeading">
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
                        <div onclick="Admin.refreshScroll();" data-toggle="collapse" data-target="#notfoundFiles" aria-expanded="true" aria-controls="notfoundFiles" class="card-header cursor-pointer" id="notfoundFilesHeading">
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