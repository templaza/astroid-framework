<?php

/**
 * @package   Astroid Framework
 * @author    Astroid Framework https://astroidframe.work
 * @copyright Copyright (C) 2023 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */
defined('_JEXEC') or die;

if (ASTROID_JOOMLA_VERSION < 4) {
  JHtml::_('behavior.caption');
}

?>

<div class="blog-featured<?php echo $this->pageclass_sfx; ?>" itemscope itemtype="https://schema.org/Blog">
  <?php if ($this->params->get('show_page_heading') != 0) : ?>
    <div class="item-title">
      <h1>
        <?php echo $this->escape($this->params->get('page_heading')); ?>
      </h1>
    </div>
  <?php endif; ?>
  <?php if ($this->params->get('page_subheading')) : ?>
    <h2>
      <?php echo $this->escape($this->params->get('page_subheading')); ?>
    </h2>
  <?php endif; ?>

  <?php $leadingcount = 0; ?>
  <?php if (!empty($this->lead_items)) : ?>
    <div class="items-leading clearfix">
      <?php foreach ($this->lead_items as &$item) : ?>
        <div class="article-wraper">
          <div class="article-wraper-inner">
            <article class="item leading-<?php echo $leadingcount; ?><?php echo $item->state == 0 ? ' system-unpublished' : null; ?><?php echo $item->featured ? ' item-featured' : ''; ?>" itemprop="blogPost" itemscope itemtype="https://schema.org/BlogPosting">
              <?php
              $this->item = &$item;
              echo $this->loadTemplate('item');
              ?>
            </article>
          </div>
        </div>
        <?php $leadingcount++; ?>
      <?php endforeach; ?>
    </div><!-- end items-leading -->
  <?php endif; ?>

  <?php

  $introcount = (count($this->intro_items));
  $counter = 0;
  $columns = ASTROID_JOOMLA_VERSION > 3 ? $this->params->get('num_columns', 1) : $this->columns;

  ?>

  <?php if (!empty($this->intro_items)) : ?>
    <?php $row = $counter / $columns; ?>
    <div class="items-row <?php echo 'row-' . $row; ?> row clearfix">
      <?php foreach ($this->intro_items as $key => &$item) : ?>
        <?php $rowcount = ((int) $key % (int) $columns) + 1; ?>
        <div class="col-lg-<?php echo round((12 / $columns)); ?>">
          <div class="article-wraper">
            <div class="article-wraper-inner">
              <article class="item column-<?php echo $rowcount; ?><?php echo $item->state == 0 ? ' system-unpublished' : null; ?><?php echo $item->featured ? ' item-featured' : ''; ?>" itemprop="blogPost" itemscope itemtype="https://schema.org/BlogPosting">
                <?php
                $this->item = &$item;
                echo $this->loadTemplate('item');
                ?>
              </article>
              <?php $counter++; ?>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>




  <?php if (!empty($this->link_items)) : ?>
    <?php echo $this->loadTemplate('links'); ?>
  <?php endif; ?>

  <?php if ($this->params->def('show_pagination', 2) == 1  || ($this->params->get('show_pagination') == 2 && $this->pagination->pagesTotal > 1)) : ?>
    <div class="pagination-wrapper">
      <?php echo $this->pagination->getPagesLinks(); ?>
      <?php if ($this->params->def('show_pagination_results', 1)) : ?>
        <p class="counter"> <?php echo $this->pagination->getPagesCounter(); ?> </p>
      <?php endif; ?>
    </div>
  <?php endif; ?>
</div>