<?php

/**
 * @package   Astroid Framework
 * @author    JoomDev https://www.joomdev.com
 * @copyright Copyright (C) 2009 - 2020 JoomDev.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */
defined('JPATH_BASE') or die;
extract($displayData);
?>
<!-- Column's Responsive Settings Template -->
<?php
$screen_sizes = [
    'xs' => [
        'label' => 'Extra small',
        'info' => '<576px'
    ],
    'sm' => [
        'label' => 'Small',
        'info' => '&#8805;576px'
    ],
    'md' => [
        'label' => 'Medium',
        'info' => '&#8805;768px'
    ],
    'lg' => [
        'label' => 'Large',
        'info' => '&#8805;992px'
    ],
    'xl' => [
        'label' => 'Extra large',
        'info' => '&#8805;1200px'
    ],
];
$column_sizes = ['inherit', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12'];
?>
<script type="text/ng-template" id="column-responsive-field-template">
    <table class="table table-bordered"><tr><th width="30%"></th><th class="" width="30%"><?php echo JText::_('ASTROID_COLUMN_SIZE_LABEL'); ?></th><th class="" width="30%"><?php echo JText::_('TPL_ASTROID_VISIBILITY_LABEL'); ?></th></tr><?php foreach ($screen_sizes as $key => $screen_size) { ?><tr><td class=""><p class="mb-0 h4 font-weight-normal"><strong><?php echo $screen_size['label']; ?></strong></p><p class="text-muted mb-0"><code><?php echo $screen_size['info']; ?></code></p></td><td class="align-middle"><select <?php echo ($key == "lg" ? 'readonly disabled' : ''); ?> data-name="size_<?php echo $key; ?>" class="responsive-field form-control"><?php foreach ($column_sizes as $column_size) { ?><option<?php echo $column_size == 'inherit' ? ' selected' : ''; ?> value="<?php echo $column_size; ?>"><?php echo (($column_size == 'inherit' || $column_size == 'col') ? '' : 'col-' . $key . '-') . $column_size; ?></option><?php } ?></select></td><td class="align-middle"><div class="jd-ui"><div class="d-inline-block"><input checked type="checkbox" data-name="hide_<?php echo $key; ?>" id="visible-<?php echo $key; ?>" class="responsive-field jd-switch" /><label class="jd-switch-btn m-0" for="visible-<?php echo $key; ?>"></label></div></div></td></tr><?php } ?></table>
</script>