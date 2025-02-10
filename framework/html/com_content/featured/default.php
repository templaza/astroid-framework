<?php

/**
 * @package   Astroid Framework
 * @author    Astroid Framework https://astroidframe.work
 * @copyright Copyright (C) 2023 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */
defined('_JEXEC') or die;

use Astroid\Framework;
use Astroid\Helper\Style;
use Astroid\Helper;
$use_masonry = $this->params->get('use_masonry', 0);
$num_columns = $this->params->get('num_columns', 1);
$blog_style = new Style('.blog');

// Blog Responsive Columns
$lead_row_cls       =   $intro_row_cls      =   ['row'];
if (Helper::isPro()) {
    $responsive_key     =   ['xxl', 'xl', 'lg', 'md', 'sm', 'xs'];
    foreach ($responsive_key as $key) {
        foreach (['lead', 'intro'] as $type) {
            if ($key !== 'xs') {
                // Lead options
                $row_gutter         =   $this->params->get($type . '_row_gutter_' . $key, '');
                $column_gutter      =   $this->params->get($type . '_column_gutter_' . $key, '');

                if (!empty($row_gutter)) {
                    ${$type . '_row_cls'}[]     =  'gy-' . $key . '-' . $row_gutter;
                }
                if (!empty($column_gutter)) {
                    ${$type . '_row_cls'}[]     =  'gx-' . $key . '-' . $column_gutter;
                }
                $param_column       =   $this->params->get($type . '_' . $key . '_column', ($key == 'lg' && $type == 'intro' ? $num_columns : ''));
                if (!empty($param_column)) {
                    ${$type . '_row_cls'}[]      =  'row-cols-' . $key . '-' . $param_column;
                }

            } else {
                $row_gutter         =   $this->params->get($type . '_row_gutter', 5);
                $column_gutter      =   $this->params->get($type . '_column_gutter', 3);
                if (!empty($row_gutter)) {
                    ${$type . '_row_cls'}[]    =  'gy-' . $row_gutter;
                }
                if (!empty($column_gutter)) {
                    ${$type . '_row_cls'}[]    =  'gx-' . $column_gutter;
                }
                $param_column               =   $this->params->get($type . '_column', 1);
                if (!empty($param_column)) {
                    ${$type . '_row_cls'}[]     =   'row-cols-' . $param_column;
                }
            }
        }
    }
} else {
    if ((int) $this->params->get('num_columns') > 1) {
        $intro_row_cls[] = 'row-cols-lg-' . $num_columns;
    }
    $intro_row_cls[] = 'gy-5';
}

$blog_class_leading = $this->params->get('blog_class_leading', '');
if (!empty($blog_class_leading)) {
    $lead_row_cls[] = $blog_class_leading;
}
$blog_class = $this->params->get('blog_class', '');
if (!empty($blog_class)) {
    $intro_row_cls[] = $blog_class;
}

if (!empty($use_masonry)) {
    $document = Framework::getDocument();
    $document->loadMasonry('.as-masonry');
    $lead_row_cls[] = 'as-masonry';
    $intro_row_cls[] = 'as-masonry';
}

$blog_layout = $this->params->get('as_blog_layout', '');
// Overlay Color
if ($blog_layout == 'overlay') {
    $as_overlay_color_type = $this->params->get('as_overlay_color_type', '');

    if ($as_overlay_color_type == 'color') {
        $as_overlay_color = $this->params->get('as_overlay_color', '');
        $blog_style->child('.as-blog-overlay > .item-image:after')->addCss('background-color', $as_overlay_color);
    } elseif ($as_overlay_color_type == 'gradient') {
        $gradient = [];
        $gradient['start'] = $this->params->get('as_overlay_color_start', '');
        $gradient['stop'] = $this->params->get('as_overlay_color_end', '');
        $gradient['start_pos'] = $this->params->get('as_overlay_start', '0');
        $gradient['stop_pos'] = $this->params->get('as_overlay_end', '100');
        $gradient['type'] = $this->params->get('as_gradient_type', 'linear');
        $gradient['angle'] = $this->params->get('as_gradient_angle', '0');
        $gradient['position'] = $this->params->get('as_gradient_position', 'center center');
        $blog_style->child('.as-blog-overlay > .item-image:after')->addCss('background-image', Style::getGradientValue(json_encode($gradient)));
    }
}
$blog_style->render();
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
        <div class="blog-items items-leading">
            <div class="<?php echo implode(' ', $lead_row_cls); ?>">
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
        </div>
    <?php endif; ?>

    <?php if (!empty($this->intro_items)) : ?>
        <div class="blog-items items-row">
            <div class="<?php echo implode(' ', $intro_row_cls); ?>">
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