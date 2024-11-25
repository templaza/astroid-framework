<?php

/**
 * @package   Astroid Framework
 * @author    Astroid Framework https://astroidframe.work
 * @copyright Copyright (C) 2023 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */
defined('_JEXEC') or die;

use Astroid\Framework;
$use_masonry = $this->params->get('use_masonry', 0);

if ($use_masonry) {
    $document = Framework::getDocument();
    $document->loadMasonry('.as-masonry');
}
?>
<div class="blog blog-featured<?php echo $this->pageclass_sfx; ?>">
    <?php if ($this->params->get('show_page_heading') != 0) : ?>
        <div class="page-header">
            <h1>
                <?php echo $this->escape($this->params->get('page_heading')); ?>
            </h1>
        </div>
    <?php endif; ?>
    <?php if (!empty($this->lead_items)) : ?>
        <div class="blog-items items-leading <?php echo $this->params->get('blog_class_leading'); ?>">
            <?php foreach ($this->lead_items as $key => &$item) : ?>
                <div class="blog-item">
                    <?php
                    $item->is_leaditem = true;
                    $item->key_idx = $key;
                    $this->item = & $item;
                    echo $this->loadTemplate('item');
                    ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($this->intro_items)) : ?>
        <?php $blogClass = $this->params->get('blog_class', ''); ?>
        <?php if ((int) $this->params->get('num_columns') > 1) : ?>
            <?php $blogClass .= ' gx-xl-5 gy-5 row-cols-lg-'.$this->params->get('num_columns'); ?>
        <?php endif; ?>
        <div class="blog-items items-row">
            <div class="row gx-xl-5 gy-5 <?php echo $blogClass; ?>">
                <?php foreach ($this->intro_items as $key => &$item) : ?>
                    <div class="blog-item">
                        <?php
                        $item->is_introitem = true;
                        $item->key_idx = $key;
                        $this->item = & $item;
                        echo $this->loadTemplate('item');
                        ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <?php if (!empty($this->link_items)) : ?>
        <div class="items-more">
            <?php echo $this->loadTemplate('links'); ?>
        </div>
    <?php endif; ?>

    <?php if (($this->params->def('show_pagination', 1) == 1 || ($this->params->get('show_pagination') == 2)) && ($this->pagination->pagesTotal > 1)) : ?>
        <div class="com-content-category-blog__navigation w-100">
            <?php if ($this->params->def('show_pagination_results', 1)) : ?>
                <p class="com-content-category-blog__counter counter pt-3 pe-2">
                    <?php echo $this->pagination->getPagesCounter(); ?>
                </p>
            <?php endif; ?>
            <div class="com-content-category-blog__pagination">
                <?php echo $this->pagination->getPagesLinks(); ?>
            </div>
        </div>
    <?php endif; ?>
</div>