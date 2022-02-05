<?php

/**
 * @package   Astroid Framework
 * @author    JoomDev https://www.joomdev.com
 * @copyright Copyright (C) 2009 - 2020 JoomDev.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */
// No direct access.
defined('_JEXEC') or die;

extract($displayData);

$params     =   Astroid\Framework::getTemplate()->getParams();
$document   =   Astroid\Framework::getDocument();

/**
 * Layout variables
 * -----------------
 * @var   string   $item            Item Object.
 * @var   string   $options         Astroid Menu Options.
 */
$header_endLevel = $params->get('header_endLevel', 0);
$enable_sticky_badge = $params->get('enable_sticky_badge', 0);
$header = @$header;
$is_mobile_menu = $mobilemenu;
$slidemenu = @$slidemenu;
$slidemenu = ($slidemenu == 1 ? true : false);

if ($item->type == "heading") {
   $item->flink = 'javascript:void(0);';
}

$attributes = [];
if (isset($props)) {
   foreach ($props as $prop => $val) {
      $attributes[$prop] = $val;
   }
}
if ($item->anchor_title) {
   $attributes['title'] = $item->anchor_title;
} else {
   $attributes['title'] = $item->title;
}

if ($item->anchor_css) {
   $attributes['class'] = $item->anchor_css;
} else {
   $attributes['class'] = '';
}

if (isset($item->id)) {
    $attributes['class'] .= ' nav-link-item-id-'.$item->id;
    if ($options->badge) {
        $style      =   '--as-nav-item-badge-background: '.$options->badge_bgcolor.';';
        $style      .=  '--as-nav-item-badge-color: '.$options->badge_color.';';
        $style      .=  'background-color: var(--as-nav-item-badge-background);';
        $style      .=  'color: var(--as-nav-item-badge-color);';
        $document->addStyledeclaration('.nav-link-item-id-'.$item->id.' > .nav-title .menu-item-badge{'.$style.'}');
    }
}

if ($item->level == 1 || $is_mobile_menu) {
   $attributes['class'] .= ' nav-link';
}

if ($active) {
   $attributes['class'] .= ' active';
}

if ($item->anchor_rel) {
   $attributes['rel'] = $item->anchor_rel;
}

if ($item->browserNav == 1) {
   $attributes['target'] = '_blank';
   $attributes['rel'] = 'noopener noreferrer';
} elseif ($item->browserNav == 2) {
   $iframe_options = 'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes';
   $attributes['onclick'] = "window.open(this.href, 'targetWindow', '" . $iframe_options . "'); return false;";
}
$attributes['data-drop-action'] = 'hover';
if (($options->megamenu || ($item->parent && $item->deeper == 1)) && !$is_mobile_menu) {
   $attributes['class'] .= " megamenu-item-link";
}

$attributes['class'] .= " item-link-" . $item->type;
$attributes['class'] .= " item-level-" . $item->level;

$attr = [];
foreach ($attributes as $key => $attribute) {
   $attr[] = $key . '="' . $attribute . '"';
}
?>
<!--menu link starts-->
<?php
// One Page Coding Starts
// Valid conditions
// Must start with #
// Length must be more than 1
if ($item->type == 'url') {
   // Let's search for #
   $validonepagelink = strpos($item->link, "#");
   if ($validonepagelink === 0 && (strlen($item->link) > 1)) {
      // Default we assume that you only want the one page for the homepage. If you want one page to work on other pages, please go ahead and hard code the full page URL i.e. https://yoursite.com/pageurl#onepageblockid
      // $item->link = JURI::root().$item->link;
      $item->link = JUri::getInstance() . $item->link;
   }
}
?>
<a href="<?php echo $item->flink; ?>" <?php echo implode(' ', $attr); ?>>
   <span class="nav-title">
      <?php if (!empty($options->icon)) { ?>
         <i class="<?php echo $options->icon; ?>"></i>
      <?php } ?>
      <?php if (!$options->icononly) { ?>
         <?php if (!empty($item->menu_image)) { ?>
            <img src="<?php echo JURI::root() . $item->menu_image; ?>" alt="<?php echo $item->title; ?>" <?php echo !empty($item->menu_image_css) ? "class='$item->menu_image_css'" : "";?> />
         <?php } ?>
         <?php if (!empty($item->menu_image) && $item->getParams()->get('menu_text', 1)) { ?>
            <?php echo $item->title; ?>
         <?php } else if (!empty($item->menu_image) && !$item->getParams()->get('menu_text', 1)) { ?>

         <?php } else { ?>
            <?php echo $item->title; ?>
         <?php } ?>
      <?php } ?>
      <?php if ($options->badge && ($header != 'sticky' || $enable_sticky_badge)) { ?>
         <?php if ($item->level == 1) { ?>
            <sup>
               <span class="menu-item-badge">
                  <?php echo $options->badge_text; ?>
               </span>
            </sup>
         <?php } else { ?>
            <span class="menu-item-badge">
               <?php echo $options->badge_text; ?>
            </span>
         <?php } ?>
      <?php } ?>
      <?php if ((!$is_mobile_menu && $item->level == 1 && (($item->parent && $item->deeper == 1) || $options->megamenu)) && ($item->level != $header_endLevel) && !$slidemenu) { ?>
         <?php if ($params->get('dropdown_arrow', 0)) {  ?>
            <i class="fas fa-chevron-down nav-item-caret"></i>
         <?php } ?>
      <?php } elseif ((!$is_mobile_menu && $item->parent && !($item->type == "heading")) && $item->level != $header_endLevel && !$slidemenu) { ?>
         <i class="fas fa-chevron-right nav-item-caret"></i>
      <?php } ?>
   </span>
   <?php if (!$is_mobile_menu && !empty($options->subtitle)) { ?>
      <small class="nav-subtitle"><?php echo $options->subtitle ?></small>
   <?php } ?>
</a>
<?php if ($slidemenu && ($item->parent && $item->deeper == 1)) { ?>
   <i class="fas fa-plus nav-item-caret<?php echo $active ? ' open' : ''; ?>"></i>
<?php } ?>
<!--menu link ends-->