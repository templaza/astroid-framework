<?php

/**
 * @package   Astroid Framework
 * @author    Astroid Framework https://astroidframe.work
 * @copyright Copyright (C) 2023 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

defined('_JEXEC') or die;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\Component\Tags\Site\Helper\RouteHelper;
?>
<div class="mod-tagspopular tagspopular">
    <?php if (!count($list)) : ?>
        <div class="alert alert-info">
            <span class="icon-info-circle" aria-hidden="true"></span><span class="visually-hidden"><?php echo Text::_('INFO'); ?></span>
            <?php echo Text::_('MOD_TAGS_POPULAR_NO_ITEMS_FOUND'); ?>
        </div>
    <?php else : ?>
        <div class="row row-cols-auto g-2">
            <?php foreach ($list as $item) : ?>
                <div>
                    <a class="btn btn-sm btn-primary" href="<?php echo Route::_(RouteHelper::getComponentTagRoute($item->tag_id . ':' . $item->alias, $item->language)); ?>">
                        <?php echo htmlspecialchars($item->title, ENT_COMPAT, 'UTF-8'); ?>
                    <?php if ($display_count) : ?>
                        <span class="tag-count badge text-bg-secondary"><?php echo $item->count; ?></span>
                    <?php endif; ?></a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>