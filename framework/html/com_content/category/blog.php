<?php

/**
 * @package   Astroid Framework
 * @author    Astroid Framework https://astroidframe.work
 * @copyright Copyright (C) 2023 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Layout\LayoutHelper;
use Astroid\Framework;
use Astroid\Helper\Style;
use Astroid\Helper;

$app = Factory::getApplication();

$this->category->text = $this->category->description;
$app->triggerEvent('onContentPrepare', [$this->category->extension . '.categories', &$this->category, &$this->params, 0]);
$this->category->description = $this->category->text;

$results = $app->triggerEvent('onContentAfterTitle', [$this->category->extension . '.categories', &$this->category, &$this->params, 0]);
$afterDisplayTitle = trim(implode("\n", $results));

$results = $app->triggerEvent('onContentBeforeDisplay', [$this->category->extension . '.categories', &$this->category, &$this->params, 0]);
$beforeDisplayContent = trim(implode("\n", $results));

$results = $app->triggerEvent('onContentAfterDisplay', [$this->category->extension . '.categories', &$this->category, &$this->params, 0]);
$afterDisplayContent = trim(implode("\n", $results));

$htag    = $this->params->get('show_page_heading') ? 'h2' : 'h1';

$use_masonry = $this->params->get('use_masonry', 0);

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
                $param_column       =   $this->params->get($type . '_' . $key . '_column', '');
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
                $column_default             =   $type === 'intro' ? $this->params->get('num_columns') : 1;
                $param_column               =   $this->params->get($type . '_column', $column_default);
                if (!empty($param_column)) {
                    ${$type . '_row_cls'}[]     =   'row-cols-' . $param_column;
                }
            }
        }
    }
} else {
    if ((int) $this->params->get('num_columns') > 1) {
        $intro_row_cls[] = 'row-cols-' . $this->params->get('num_columns');
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
<div class="blog<?php echo $this->pageclass_sfx; ?>" itemscope itemtype="https://schema.org/Blog">
    <?php if ($this->params->get('show_page_heading')) : ?>
        <div class="page-header">
            <h1> <?php echo $this->escape($this->params->get('page_heading')); ?> </h1>
        </div>
    <?php endif; ?>

    <?php if ($this->params->get('show_category_title', 1)) : ?>
        <<?php echo $htag; ?>>
            <?php echo $this->category->title; ?>
        </<?php echo $htag; ?>>
    <?php endif; ?>
    <?php echo $afterDisplayTitle; ?>

    <?php if ($this->params->get('show_cat_tags', 1) && !empty($this->category->tags->itemTags)) : ?>
        <?php $this->category->tagLayout = new FileLayout('joomla.content.tags'); ?>
        <?php echo $this->category->tagLayout->render($this->category->tags->itemTags); ?>
    <?php endif; ?>

    <?php if ($beforeDisplayContent || $afterDisplayContent || $this->params->get('show_description', 1) || $this->params->def('show_description_image', 1)) : ?>
    <div class="category-desc clearfix">
        <?php if ($this->params->get('show_description_image') && $this->category->getParams()->get('image')) : ?>
            <?php echo LayoutHelper::render(
                'joomla.html.image',
                [
                    'src' => $this->category->getParams()->get('image'),
                    'alt' => empty($this->category->getParams()->get('image_alt')) && empty($this->category->getParams()->get('image_alt_empty')) ? false : $this->category->getParams()->get('image_alt'),
                ]
            ); ?>
        <?php endif; ?>
        <?php echo $beforeDisplayContent; ?>
        <?php if ($this->params->get('show_description') && $this->category->description) : ?>
            <?php echo HTMLHelper::_('content.prepare', $this->category->description, '', 'com_content.category'); ?>
        <?php endif; ?>
        <?php echo $afterDisplayContent; ?>
    </div>
    <?php endif; ?>

<?php if ($this->maxLevel != 0 && !empty($this->children[$this->category->id])) : ?>
    <div class="com-content-category-blog__children cat-children">
        <?php if ($this->params->get('show_category_heading_title_text', 1) == 1) : ?>
            <h3> <?php echo Text::_('JGLOBAL_SUBCATEGORIES'); ?> </h3>
        <?php endif; ?>
        <?php echo $this->loadTemplate('children'); ?>
    </div>
<?php endif; ?>

    <?php if (empty($this->lead_items) && empty($this->link_items) && empty($this->intro_items)) : ?>
    <?php if ($this->params->get('show_no_articles', 1)) : ?>
        <div class="alert alert-info">
            <span class="icon-info-circle" aria-hidden="true"></span><span class="visually-hidden"><?php echo Text::_('INFO'); ?></span>
            <?php echo Text::_('COM_CONTENT_NO_ARTICLES'); ?>
        </div>
    <?php endif; ?>
    <?php endif; ?>

    <?php if (!empty($this->lead_items)) : ?>
    <div class="com-content-category-blog__items blog-items items-leading">
        <div class="<?php echo implode(' ', $lead_row_cls); ?>">
        <?php foreach ($this->lead_items as $key => &$item) : ?>
            <div class="com-content-category-blog__item blog-item">
                <?php
                $item->is_leaditem = true;
                $item->key_idx = $key;
                $this->item = &$item;
                echo $this->loadTemplate('item');
                ?>
            </div>
        <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <?php if (!empty($this->intro_items)) : ?>
    <div class="com-content-category-blog__items blog-items items-row">
        <div class="<?php echo implode(' ', $intro_row_cls); ?>">
            <?php foreach ($this->intro_items as $key => &$item) : ?>
                <div class="com-content-category-blog__item blog-item">
                    <?php
                    $item->is_introitem = true;
                    $item->key_idx = $key;
                    $this->item = &$item;
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