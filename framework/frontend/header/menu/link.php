<?php

/**
 * @package   Astroid Framework
 * @author    Astroid Framework Team https://astroidframe.work
 * @copyright Copyright (C) 2023 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */
use Joomla\CMS\Uri\Uri;
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

$attributes = [];
if (isset($props)) {
   foreach ($props as $prop => $val) {
      $attributes[$prop] = $val;
   }
}
if ($options->icononly) {
   $attributes['title'] = !empty($item->anchor_title) ? $item->anchor_title : $item->title;
}

$attributes['class'] = 'as-menu-item';
if ($item->anchor_css) {
   $attributes['class'] .= ' ' . $item->anchor_css;
}

if (isset($item->id)) {
    $attributes['class'] .= ' nav-link-item-id-'.$item->id;
    if ($options->badge) {
        $style      =   '--as-nav-item-badge-background: '.$options->badge_bgcolor.';';
        $style      .=  '--as-nav-item-badge-color: '.$options->badge_color.';';
        $style      .=  'background-color: var(--as-nav-item-badge-background);';
        $style      .=  'color: var(--as-nav-item-badge-color);';
        $document->addStyledeclaration('.nav-link-item-id-'.$item->id.' .nav-title .menu-item-badge{'.$style.'}');
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
$attributes['data-drop-action'] = $params->get('dropdown_trigger', 'hover');
if (($options->megamenu || ($item->parent && $item->deeper == 1)) && !$is_mobile_menu) {
   $attributes['class'] .= " megamenu-item-link";
}

$attributes['class'] .= " item-link-" . $item->type;
$attributes['class'] .= " item-level-" . $item->level;

if ($item->type == "heading" || $item->type == 'separator') {
    $item->flink = '#';
}

if (!empty($item->menu_icon) && empty($options->icon)) {
   $options->icon = $item->menu_icon;
}

$attr = [];
foreach ($attributes as $key => $attribute) {
   $attr[] = $key . '="' . $attribute . '"';
}

// One Page Coding Starts
// Valid conditions
// Must start with #
// Length must be more than 1
if ($item->type == 'url') {
   // Let's search for #
   $validonepagelink = strpos($item->link, "#");
   if ($validonepagelink === 0 && (strlen($item->link) > 1)) {
      // Default we assume that you only want the one page for the homepage. If you want one page to work on other pages, please go ahead and hard code the full page URL i.e. https://yoursite.com/pageurl#onepageblockid
      // $item->link = Uri::root().$item->link;
      $item->link = Uri::getInstance() . $item->link;
   }
}
$has_media = !empty($options->icon);
echo '<!--menu link starts-->';
echo '<a href="' . $item->flink . '" ' . implode(' ', $attr) . '>';
echo $has_media ? '<div class="as-gutter-x-md d-flex">' : '';

if (!empty($options->icon)) {
   echo '<div class="nav-icon"><i class="' . $options->icon . '"></i></div>';
}

echo $has_media ? '<div class="w-100 d-flex flex-wrap flex-column justify-content-center">' : '';
echo '<div class="nav-title">';
if (!$options->icononly) {
    if (!empty($item->menu_image)) {
        echo '<img src="' . Uri::root() . $item->menu_image . '" alt="' . $item->title . '" ' . (!empty($item->menu_image_css) ? "class='" . $item->menu_image_css . "'" : "") . '>';
    }
    if ($item->getParams()->get('menu_text', 1)) {
        echo '<span class="nav-title-text">' . $item->title . '</span>';
    }
}

if ($options->badge && ($header != 'sticky' || $enable_sticky_badge)) {
   if ($item->level == 1) {
      echo '<sup><span class="menu-item-badge">' . $options->badge_text . '</span></sup>';
   } else {
      echo '<span class="menu-item-badge">' . $options->badge_text . '</span>';
   }
}
if ((!$is_mobile_menu && $item->level == 1 && (($item->parent && $item->deeper == 1) || $options->megamenu)) && ($item->level != $header_endLevel) && !$slidemenu) {
   if ($params->get('dropdown_arrow', 0)) {
      echo '<i class="fas fa-chevron-down nav-item-caret"></i>';
   }
} elseif ((!$is_mobile_menu && $item->parent) && $item->level != $header_endLevel && !$slidemenu) {
   echo '<i class="fas fa-chevron-right nav-item-caret"></i>';
}
echo '</div>';
if (!$is_mobile_menu && !empty($options->subtitle)) {
   echo '<small class="nav-subtitle">' . $options->subtitle . '</small>';
}
echo $has_media ? '</div>' : '';
echo $has_media ? '</div>' : '';
echo '</a>';
if ($slidemenu && ($item->parent && $item->deeper == 1)) {
   echo '<i class="fas fa-plus nav-item-caret' . ($active ? ' open' : '') . '"></i>';
}
echo '<!--menu link ends-->';