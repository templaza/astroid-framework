<?php

/**
 * @package     Joomla.Site
 * @subpackage  com_content
 *
 * @copyright   (C) 2010 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\Component\Content\Site\Helper\RouteHelper;

/** @var \Joomla\Component\Content\Site\View\Category\HtmlView $this */
$lang   = $this->getLanguage();
$user   = $this->getCurrentUser();
$groups = $user->getAuthorisedViewLevels();

if ($this->maxLevel != 0 && count($this->children[$this->category->id]) > 0) : ?>
<div class="com-content-category-blog__children-container row gx-xl-5 gy-5 row-cols-lg-<?php echo $this->params->get('num_columns'); ?>">
    <?php foreach ($this->children[$this->category->id] as $id => $child) : ?>
        <?php // Check whether category access level allows access to subcategories. ?>
        <?php if (in_array($child->access, $groups)) : ?>
            <?php if ($this->params->get('show_empty_categories') || $child->numitems || count($child->getChildren())) : ?>
            <div><div class="com-content-category-blog__child card card-body p-4">
                <?php if ($lang->isRtl()) : ?>
                <h3 class="page-header item-title">
                    <?php if ($this->params->get('show_cat_num_articles', 1)) : ?>
                        <span class="badge bg-info tip">
                            <?php echo $child->getNumItems(true); ?>
                        </span>
                    <?php endif; ?>
                    <a href="<?php echo Route::_(RouteHelper::getCategoryRoute($child->id, $child->language)); ?>">
                    <?php echo $this->escape($child->title); ?></a>

                    <?php if ($this->maxLevel > 1 && count($child->getChildren()) > 0) : ?>
                        <a href="#category-<?php echo $child->id; ?>" data-bs-toggle="collapse" class="btn btn-sm float-end" aria-label="<?php echo Text::_('JGLOBAL_EXPAND_CATEGORIES'); ?>"><span class="icon-plus" aria-hidden="true"></span></a>
                    <?php endif; ?>
                </h3>
                <?php else : ?>
                <h3 class="page-header item-title d-flex align-items-center flex-wrap as-gutter-md"><a href="<?php echo Route::_(RouteHelper::getCategoryRoute($child->id, $child->language)); ?>">
                    <?php echo $this->escape($child->title); ?></a>
                    <?php if ($this->params->get('show_cat_num_articles', 1)) : ?>
                        <span class="badge bg-info">
                            <?php echo Text::_('COM_CONTENT_NUM_ITEMS'); ?>&nbsp;
                            <?php echo $child->getNumItems(true); ?>
                        </span>
                    <?php endif; ?>

                    <?php if ($this->maxLevel > 1 && count($child->getChildren()) > 0) : ?>
                        <a href="#category-<?php echo $child->id; ?>" data-bs-toggle="collapse" class="btn btn-sm float-end" aria-label="<?php echo Text::_('JGLOBAL_EXPAND_CATEGORIES'); ?>"><span class="icon-plus" aria-hidden="true"></span></a>
                    <?php endif; ?>
                </h3>
                <?php endif; ?>

                <?php if ($this->params->get('show_subcat_desc') == 1) : ?>
                    <?php if ($child->description) : ?>
                    <div class="com-content-category-blog__description category-desc">
                        <?php echo HTMLHelper::_('content.prepare', $child->description, '', 'com_content.category'); ?>
                    </div>
                    <?php endif; ?>
                <?php endif; ?>

                <?php if ($this->maxLevel > 1 && count($child->getChildren()) > 0) : ?>
                <div class="com-content-category-blog__children collapse fade" id="category-<?php echo $child->id; ?>">
                    <?php
                    $this->children[$child->id] = $child->getChildren();
                    $this->category = $child;
                    $this->maxLevel--;
                    echo $this->loadTemplate('children');
                    $this->category = $child->getParent();
                    $this->maxLevel++;
                    ?>
                </div>
                <?php endif; ?>
            </div></div>
            <?php endif; ?>
        <?php endif; ?>
    <?php endforeach; ?>
</div>
<?php endif;
