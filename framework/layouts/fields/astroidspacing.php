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

<div astroidspacing ng-model="<?php echo $fieldname; ?>">
    <div class="mb-3">
        <ul class="nav tabmedia">
            <li></li>
            <li>
                <a data-device="desktop" ng-click="setDevice('desktop')" class="active" href="javascript:void(0);">
                    <i class="fas fa-desktop"></i>
                </a>
            </li>
            <li>
                <a data-device="tablet" ng-click="setDevice('tablet')" href="javascript:void(0);">
                    <i class="fas fa-tablet-alt"></i>
                </a>
            </li>
            <li>
                <a data-device="mobile" ng-click="setDevice('mobile')" href="javascript:void(0);">
                    <i class="fas fa-mobile-alt"></i>
                </a>
            </li>
        </ul>
    </div>
    <?php foreach (['desktop', 'tablet', 'mobile'] as $device) { ?>
        <div data-device="<?php echo $device; ?>" class="astroid-spacing-field">
            <?php foreach (['top', 'right', 'bottom', 'left'] as $position) { ?>
                <div data-label="<?php echo $position; ?>">
                    <input data-device="<?php echo $device; ?>" data-attr="<?php echo $position; ?>" type="number" class="form-control text-center" />
                </div>
            <?php } ?>
            <div class="text-center">
                <button data-device="<?php echo $device; ?>" ng-click="switchLock('<?php echo $device; ?>')" type="button" class="btn-lock btn btn-light">
                    <span class="fas fa-lock"></span>
                    <span class="fas fa-unlock"></span>
                </button>
            </div>
            <div>
                <select data-device="<?php echo $device; ?>" data-attr="unit" class="form-control text-center">
                    <option value="px">PX</option>
                    <option value="em">EM</option>
                    <option value="%">%</option>
                </select>
            </div>
        </div>
    <?php } ?>
    <textarea class="d-none" name="<?php echo $name; ?>"></textarea>
</div>