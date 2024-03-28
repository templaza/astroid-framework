<?php

/**
 * @package   Astroid Framework
 * @author    Astroid Framework Team https://astroidframe.work
 * @copyright Copyright (C) 2023 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

namespace Astroid\Component;

use Astroid\Framework;
use Astroid\Helper;
use Astroid\Helper\Style;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\Database\DatabaseInterface;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;

class Utility
{
    public static function meta()
    {
        $app = Factory::getApplication();
        $document = Framework::getDocument();
        $itemid = $app->input->get('Itemid', '', 'INT');
        $menu = $app->getMenu();
        $item = $menu->getItem($itemid);

        $template_params = Framework::getTemplate()->getParams();
        $config = Factory::getConfig();

        if (empty($item)) {
            return;
        }

        $params = $item->getParams();

        $enabled = $params->get('astroid_opengraph_menuitem', 0);
        if (empty($enabled)) {
            return;
        }
        $astroid_og_visibility = $params->get('astroid_og_visibility', "currentPage");
        if ($astroid_og_visibility == "currentPage") {
            if ((isset($item->query['option']) && $item->query['option'] != $app->input->get('option', '')) || (isset($item->query['view']) && $item->query['view'] != $app->input->get('view', '')) || (isset($item->query['layout']) && $item->query['layout'] != $app->input->get('layout', ''))) {
                return;
            }
        }

        $fb_id = $template_params->get('article_opengraph_facebook', '');
        $tw_id = $template_params->get('article_opengraph_twitter', '');

        $og_title = $item->title;
        if (!empty($params->get('astroid_og_title_menuitem', ''))) {
            $og_title = $params->get('astroid_og_title_menuitem', '');
        }
        $og_description = '';
        if (!empty($params->get('astroid_og_desc_menuitem', ''))) {
            $og_description = $params->get('astroid_og_desc_menuitem', '');
        }
        $og_image = '';
        if (!empty($params->get('astroid_og_image_menuitem', ''))) {
            $og_image = Uri::root() . $params->get('astroid_og_image_menuitem', '');
        }

        $og_sitename = $config->get('sitename');
        $og_siteurl = Uri::getInstance();

        $meta = [];

        $document->addMeta('twitter:card', 'summary_large_image');

        if ($item->type == 'component' && isset($item->query) && $item->query['option'] == 'com_content' && $item->query['view'] == 'article') {
            $document->addMeta('', 'article', ['property' => 'og:type']);
        }
        if (!empty($og_title)) {
            $document->addMeta('og:title', $og_title, ['property' => 'og:title']);
        }
        if (!empty($og_sitename)) {
            $document->addMeta('og:site_name', $og_sitename, ['property' => 'og:site_name']);
        }
        if (!empty($og_siteurl)) {
            $document->addMeta('og:url', $og_siteurl, ['property' => 'og:url']);
        }
        if (!empty($og_description)) {
            $document->addMeta('og:description', substr($og_description, 0, 200), ['property' => 'og:description']);
        }
        if (!empty($fb_id)) {
            $document->addMeta('fb:app_id', $fb_id, ['property' => 'fb:app_id']);
        }
        if (!empty($tw_id)) {
            $document->addMeta('twitter:creator', '@' . $tw_id);
        }
        if (!empty($og_image)) {
            $document->addMeta('og:image', $og_image, ['property' => 'og:image']);
        }
    }

    public static function layout()
    {
        $params = Framework::getTemplate()->getParams();
        $document = Framework::getDocument();

        $theme_width    =   $params->get('theme_width', '');
        if (!empty($theme_width)) {
            $document->addStyleDeclaration('.container, .container-sm, .container-md, .container-lg, .container-xl, .astroid-layout.astroid-layout-boxed .astroid-wrapper {max-width: '.$theme_width.';}');
        }

        $template_layout = $params->get('template_layout', 'wide');
        if ($template_layout != 'boxed') {
            return false;
        }

        $layout_background_image = $params->get('layout_background_image', '');
        if (!empty($layout_background_image)) {
            $style = new Style('body');
            $style->addCss('background-image', 'url(' . Uri::root() . Helper\Media::getPath() . '/' . $layout_background_image . ')');
            $style->addCss('background-repeat', $params->get('layout_background_repeat', 'inherit'));
            $style->addCss('background-size', $params->get('layout_background_size', 'inherit'));
            $style->addCss('background-position', $params->get('layout_background_position', 'inherit'));
            $style->addCss('background-attachment', $params->get('layout_background_attachment', 'inherit'));
            $style->render();
        }
    }

    public static function smoothScroll()
    {
        $params = Framework::getTemplate()->getParams();
        $wa = Factory::getApplication()->getDocument()->getWebAssetManager();
        $enable_smooth_scroll = $params->get('enable_smooth_scroll', '');
        if ($enable_smooth_scroll == '1') {
            $speed = $params->get('smooth_scroll_speed', '');
            $wa->registerAndUseScript('astroid.smooth-scroll', 'astroid/smooth-scroll.polyfills.min.js', ['relative' => true, 'version' => 'auto'], [], ['jquery']);
            $header = $params->get('header', TRUE);
            $mode = $params->get('header_mode', 'horizontal');
            $sidebar = ($header && $mode == 'sidebar');

            $script = '
			var scroll = new SmoothScroll(\'a[href*="#"]\', {
            speed: ' . $speed . '
            ' . ($sidebar ? '' : ', header: ".astroid-header"') . '
			});';
            $wa->addInlineScript($script);
        }
    }

    public static function background()
    {
        $params = Framework::getTemplate()->getParams();
        $document = Framework::getDocument();
        if ($params->get('template_layout') == 'boxed') {
            $styles = '';
            // Background color
            if ($params->get('color_body_background_color')) {
                $styles .= 'background-color: ' . $params->get('color_body_background_color') . ';';
            }
            // Let's add the image styles only if an image is selected.
            if ($params->get('basic_background_image')) {
                $styles .= '
                      background-image: url("' . Uri::root() . Helper\Media::getPath() . '/' . $params->get('basic_background_image') . '");
                      background-repeat: ' . $params->get('basic_background_repeat') . ';
                      background-size: ' . $params->get('basic_background_size') . ';
                      background-position: ' . str_replace('_', ' ', $params->get('basic_background_position')) . ';
                      background-attachment: ' . $params->get('basic_background_attachment') . ';
                  ';
            }

            $bodystyle = 'body {' . $styles . '}';
            $document->addStyleDeclaration($bodystyle);
        }
    }

    public static function getCategories()
    {
        $db    = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true)
            ->select('DISTINCT a.id, a.title, a.level, a.published, a.lft');
        $subQuery = $db->getQuery(true)
            ->select('id,title,level,published,parent_id,extension,lft,rgt')
            ->from('#__categories')
            ->where($db->quoteName('published') . ' = ' . $db->quote(1))
            ->where($db->quoteName('extension') . ' = ' . $db->quote('com_content'));

        $query->from('(' . $subQuery->__toString() . ') AS a')
            ->join('LEFT', $db->quoteName('#__categories') . ' AS b ON a.lft > b.lft AND a.rgt < b.rgt');
        $query->order('a.lft ASC');

        $db->setQuery($query);
        $categories = $db->loadObjectList();

        $article_cats = array( 0 => array('value' => '', 'label' => Text::_('ASTROID_WIDGET_ALL_CATEGORIES') ) );

        $j = 1;

        if (count((array) $categories))
        {
            foreach ($categories as $category)
            {
                $article_cats[$j]['value'] = $category->id;
                $article_cats[$j]['label'] = str_repeat('- ', ($category->level - 1)) . $category->title;

                $j = $j + 1;
            }
        }
        return $article_cats;
    }

    public static function typography()
    {
        $params = Framework::getTemplate()->getParams();
        $customselector = $params->get('custom_typography_selectors', '');

        $types = array('body' => 'body, .body', 'h1' => 'h1, .h1', 'h2' => 'h2, .h2', 'h3' => 'h3, .h3', 'h4' => 'h4, .h4', 'h5' => 'h5, .h5', 'h6' => 'h6, .h6', 'logo' => ['.astroid-logo-text', '.astroid-logo-text > a.site-title'], 'logo_tag_line' => '.astroid-logo-text > p.site-tagline', 'menu' => '.astroid-nav > li > .as-menu-item, .astroid-sidebar-menu > li > .as-menu-item, .astroid-mobile-menu > .nav-item > .as-menu-item', 'submenu' => '.nav-submenu-container .nav-submenu > li, .jddrop-content .megamenu-item .megamenu-menu li, .nav-submenu, .astroid-mobile-menu .nav-child .menu-go-back, .astroid-mobile-menu .nav-child .nav-item-submenu > .as-menu-item', 'custom' => $customselector);

        $bodyTypography = null;
        foreach ($types as $type => $selector) {
            if (empty($selector)) {
                continue;
            }

            if ($params->exists($type . '_typography')) {
                $status = $params->get($type . '_typography');
            } else {
                $status = $params->get($type . 's_typography');
            }
            if (!empty($status) && trim($status) !== 'custom') {
                continue;
            }
            $typography = $params->get($type . '_typography_options', null);
            if (empty($typography)) {
                continue;
            }
            if ($type == 'body') {
                $bodyTypography = $typography;
            }
            Helper\Style::renderTypography($selector, $typography, $bodyTypography);
        }
    }

    public static function colors()
    {
        $params = Framework::getTemplate()->getParams();
        // Body
        $body_background_color  =   Style::getColor($params->get('body_background_color', ''));
        $body_text_color        =   Style::getColor($params->get('body_text_color', ''));
        $body_link_color        =   Style::getColor($params->get('body_link_color', ''));
        $body_link_hover_color  =   Style::getColor($params->get('body_link_hover_color', ''));
        $body_heading_color     =   Style::getColor($params->get('body_heading_color', ''));
        $template_layout        =   $params->get('template_layout', 'wide');
        Style::addCssBySelector('html', 'background-color', $body_background_color['light']);
        Style::addCssBySelector('[data-bs-theme=dark]', 'background-color', $body_background_color['dark']);

        if ($template_layout == 'boxed') {
            Style::addCssBySelector('.astroid-layout.astroid-layout-boxed .astroid-wrapper', 'background-color', $body_background_color['light']);
            Style::addCssBySelector('[data-bs-theme=dark] .astroid-layout.astroid-layout-boxed .astroid-wrapper', 'background-color', $body_background_color['dark']);
        }
        $body = new Style('body');
        $body_dark = new Style('body', 'dark');
        $body->addCss('--bs-body-bg', $body_background_color['light']);
        $body->addCss('--bs-body-color', $body_text_color['light']);
        $body->link()->addCss('color', 'var(--as-link-color)');
        $body->link()->addCss('--as-link-color', $body_link_color['light']);
        $body->link()->hover()->addCss('color', 'var(--as-link-hover-color)');
        $body->link()->hover()->addCss('--as-link-hover-color', $body_link_hover_color['light']);
        $body->render();  // render body colors

        $body_dark->addCss('--bs-body-bg', $body_background_color['dark']);
        $body_dark->addCss('--bs-body-color', $body_text_color['dark']);
        $body_dark->link()->addCss('--as-link-color', $body_link_color['dark']);
        $body_dark->link()->hover()->addCss('--as-link-hover-color', $body_link_hover_color['dark']);
        $body_dark->render();  // render body colors

        $body = new Style(['h1','h2','h3','h4','h5','h6']);
        $body->addCss('--bs-heading-color', $body_heading_color['light']);
        $body->render();
        $body = new Style(['h1','h2','h3','h4','h5','h6'], 'dark');
        $body->addCss('--bs-heading-color', $body_heading_color['dark']);
        $body->render();

        // Header
        $header_text_color      =   Style::getColor($params->get('header_text_color', ''));
        $header_bg              =   Style::getColor($params->get('header_bg', ''));
        $header_heading_color   =   Style::getColor($params->get('header_heading_color', ''));
        $header_link_color      =   Style::getColor($params->get('header_link_color', ''));
        $header_link_hover_color=   Style::getColor($params->get('header_link_hover_color', ''));

        $header = new Style('header');
        $header->addCss('color', $header_text_color['light']);
        $header->child('h1,h2,h3,h4,h5,h6,.megamenu-container .item-link-heading')->addCss('color', $header_heading_color['light']);
        $header->link()->addCss('color', $header_link_color['light']);
        $header->link()->hover()->addCss('color', $header_link_hover_color['light']);
        $header->render();

        $header = new Style('header', 'dark');
        $header->addCss('color', $header_text_color['dark']);
        $header->child('h1,h2,h3,h4,h5,h6,.megamenu-container .item-link-heading')->addCss('color', $header_heading_color['dark']);
        $header->link()->addCss('color', $header_link_color['dark']);
        $header->link()->hover()->addCss('color', $header_link_hover_color['dark']);
        $header->render();

        Style::addCssBySelector('.astroid-header-section, .astroid-sidebar-header', 'background-color', $header_bg['light']);
        Style::addCssBySelector('[data-bs-theme=dark] .astroid-header-section, [data-bs-theme=dark] .astroid-sidebar-header', 'background-color', $header_bg['dark']);

        // Sticky Header
        $stick_header_bg_color              =   Style::getColor($params->get('stick_header_bg_color', ''));
        $stick_header_menu_link_color       =   Style::getColor($params->get('stick_header_menu_link_color', ''));
        $stick_header_menu_link_hover_color =   Style::getColor($params->get('stick_header_menu_link_hover_color', ''));
        $stick_header_menu_link_active_color=   Style::getColor($params->get('stick_header_menu_link_active_color', ''));
        $stickyHeader = new Style('#astroid-sticky-header');
        $stickyHeader->addCss('background-color', $stick_header_bg_color['light']);
        $stickyHeaderLink = $stickyHeader->child('.astroid-nav .nav-link');
        $stickyHeaderLink->addCss('color', $stick_header_menu_link_color['light']);
        $stickyHeaderLink->hover()->addCss('color', $stick_header_menu_link_hover_color['light']);
        $stickyHeaderLink->active('.active')->addCss('color', $stick_header_menu_link_active_color['light']);
        $stickyHeader->render();  // render sticky header

        $stickyHeader = new Style('#astroid-sticky-header', 'dark');
        $stickyHeader->addCss('background-color', $stick_header_bg_color['dark']);
        $stickyHeaderLink = $stickyHeader->child('.astroid-nav .nav-link');
        $stickyHeaderLink->addCss('color', $stick_header_menu_link_color['dark']);
        $stickyHeaderLink->hover()->addCss('color', $stick_header_menu_link_hover_color['dark']);
        $stickyHeaderLink->active('.active')->addCss('color', $stick_header_menu_link_active_color['dark']);
        $stickyHeader->render();  // render sticky header

        // Menu
        $main_menu_link_color           =   Style::getColor($params->get('main_menu_link_color', ''));
        $main_menu_link_hover_color     =   Style::getColor($params->get('main_menu_link_hover_color', ''));
        $main_menu_link_active_color    =   Style::getColor($params->get('main_menu_link_active_color', ''));
        $main_menu_active_background    =   Style::getColor($params->get('main_menu_active_background', ''));
        $main_menu_hover_background     =   Style::getColor($params->get('main_menu_hover_background', ''));
        $navLink = new Style(['.astroid-nav .nav-link', '.astroid-sidebar-menu .nav-link']);
        $navLink->addCss('color', $main_menu_link_color['light']);
        $navLink->hover()->addCss('color', $main_menu_link_hover_color['light']);
        $navLink->hover()->addCss('background-color', $main_menu_hover_background['light']);
        $navLink->focus()->addCss('color', $main_menu_link_hover_color['light']);
        $navLink->active('.active')->addCss('color', $main_menu_link_active_color['light']);
        $navLink->active('.active')->addCss('background-color', $main_menu_active_background['light']);
        $navLink->render(); // render navlink

        $navLink = new Style(['.astroid-nav .nav-link', '.astroid-sidebar-menu .nav-link'], 'dark');
        $navLink->addCss('color', $main_menu_link_color['dark']);
        $navLink->hover()->addCss('color', $main_menu_link_hover_color['dark']);
        $navLink->hover()->addCss('background-color', $main_menu_hover_background['dark']);
        $navLink->focus()->addCss('color', $main_menu_link_hover_color['dark']);
        $navLink->active('.active')->addCss('color', $main_menu_link_active_color['dark']);
        $navLink->active('.active')->addCss('background-color', $main_menu_active_background['dark']);
        $navLink->render(); // render navlink

        // Dropdown Menu
        $dropdown_bg_color  =   Style::getColor($params->get('dropdown_bg_color', ''));
        $dropdown_link_color            =   Style::getColor($params->get('dropdown_link_color', ''));
        $dropdown_menu_link_hover_color =   Style::getColor($params->get('dropdown_menu_link_hover_color', ''));
        $dropdown_menu_hover_bg_color   =   Style::getColor($params->get('dropdown_menu_hover_bg_color', ''));
        $dropdown_menu_active_bg_color  =   Style::getColor($params->get('dropdown_menu_active_bg_color', ''));
        $dropdown_menu_active_link_color=   Style::getColor($params->get('dropdown_menu_active_link_color', ''));

        $dropdown           = Style::addCssBySelector('.megamenu-container', 'background-color', $dropdown_bg_color['light']);
        $submenuDropdown    = Style::addCssBySelector('.megamenu-container .nav-submenu .nav-submenu', 'background-color', $dropdown_bg_color['light']);

        Style::addCssBySelector('.has-megamenu.open .arrow', 'border-bottom-color', $dropdown_bg_color['light']);

        $link = $dropdown->child('li.nav-item-submenu > a');
        $link->addCss('color', $dropdown_link_color['light']);
        $link->hover()->addCss('color', $dropdown_menu_link_hover_color['light'])->addCss('background-color', $dropdown_menu_hover_bg_color['light']);
        $link->active('.active')->addCss('color', $dropdown_menu_active_link_color['light'])->addCss('background-color', $dropdown_menu_active_bg_color['light']);
        $dropdown->render(); // render dropdown

        $dropdown           = Style::addCssBySelector('[data-bs-theme=dark] .megamenu-container', 'background-color', $dropdown_bg_color['dark']);
        $submenuDropdown    = Style::addCssBySelector('[data-bs-theme=dark] .megamenu-container .nav-submenu .nav-submenu', 'background-color', $dropdown_bg_color['dark']);

        Style::addCssBySelector('[data-bs-theme=dark] .has-megamenu.open .arrow', 'border-bottom-color', $dropdown_bg_color['dark']);

        $link = $dropdown->child('li.nav-item-submenu > a');
        $link->addCss('color', $dropdown_link_color['dark']);
        $link->hover()->addCss('color', $dropdown_menu_link_hover_color['dark'])->addCss('background-color', $dropdown_menu_hover_bg_color['dark']);
        $link->active('.active')->addCss('color', $dropdown_menu_active_link_color['dark'])->addCss('background-color', $dropdown_menu_active_bg_color['dark']);
        $dropdown->render(); // render dropdown

        // Sticky Menu
        $stick_header_mobile_menu_icon_color = Style::getColor($params->get('stick_header_mobile_menu_icon_color', ''));
        $stick_header_mobile_menu_active_icon_color = Style::getColor($params->get('stick_header_mobile_menu_active_icon_color', ''));
        $sticky_menu_styles = new Style('#astroid-sticky-header');
        $header_mobilemenu_trigger  =   $sticky_menu_styles->child('.header-mobilemenu-trigger.burger-menu-button .inner, .header-mobilemenu-trigger.burger-menu-button .inner::before, .header-mobilemenu-trigger.burger-menu-button .inner::after');
        $header_mobilemenu_trigger->addCss('background-color', $stick_header_mobile_menu_icon_color['light']);
        $astroid_mobilemenu_open    =   $sticky_menu_styles->child('.astroid-mobilemenu-open .burger-menu-button .inner, .astroid-mobilemenu-open .burger-menu-button .inner::before, .astroid-mobilemenu-open .burger-menu-button .inner::after');
        $astroid_mobilemenu_open->addCss('background-color', $stick_header_mobile_menu_active_icon_color['light']);
        $sticky_menu_styles->render();

        $sticky_menu_styles = new Style('#astroid-sticky-header', 'dark');
        $header_mobilemenu_trigger  =   $sticky_menu_styles->child('.header-mobilemenu-trigger.burger-menu-button .inner, .header-mobilemenu-trigger.burger-menu-button .inner::before, .header-mobilemenu-trigger.burger-menu-button .inner::after');
        $header_mobilemenu_trigger->addCss('background-color', $stick_header_mobile_menu_icon_color['dark']);
        $astroid_mobilemenu_open    =   $sticky_menu_styles->child('.astroid-mobilemenu-open .burger-menu-button .inner, .astroid-mobilemenu-open .burger-menu-button .inner::before, .astroid-mobilemenu-open .burger-menu-button .inner::after');
        $astroid_mobilemenu_open->addCss('background-color', $stick_header_mobile_menu_active_icon_color['dark']);
        $sticky_menu_styles->render();

        // Offcanvas Menu
        $mobile_background_color = Style::getColor($params->get('mobile_backgroundcolor', ''));
        $mobile_link_color = Style::getColor($params->get('mobile_menu_link_color', ''));
        $mobile_menu_text_color = Style::getColor($params->get('mobile_menu_text_color', ''));
        $mobile_hover_background_color = Style::getColor($params->get('mobile_hover_background_color', ''));
        $mobile_active_link_color = Style::getColor($params->get('mobile_menu_active_link_color', ''));
        $mobile_active_background_color = Style::getColor($params->get('mobile_menu_active_bg_color', ''));
        $mobile_menu_icon_color = Style::getColor($params->get('mobile_menu_icon_color', ''));
        $mobile_menu_active_icon_color = Style::getColor($params->get('mobile_menu_active_icon_color', ''));

        $mobilemenu_styles = new Style('.astroid-offcanvas');
        $mobilemenu_styles->addCss('color', $mobile_menu_text_color['light'] . ' !important');
        $mobilemenu_styles->addCss('background-color', $mobile_background_color['light'] . ' !important');
        $mobilemenu_styles->child('.burger-menu-button, .astroid-mobilemenu-container .astroid-mobilemenu-inner .dropdown-menus')->addCss('background-color', $mobile_background_color['light'] . ' !important');
        $mobilemenu_styles->child('.menu-indicator')->addCss('color', $mobile_link_color['light'] . ' !important');
        $astroid_mobilemenu_inner   =   $mobilemenu_styles->child('.astroid-mobilemenu-container .astroid-mobilemenu-inner');
        $astroid_mobilemenu_inner->child('.menu-item a')->addCss('color', $mobile_link_color['light'] . ' !important');
        $astroid_mobilemenu_inner->child('.menu-item a:hover')->addCss('background-color', $mobile_hover_background_color['light'] . ' !important');
        $astroid_mobilemenu_inner->child('.menu-item.active > a,  .menu-item.active > .nav-header, .menu-item.nav-item-active > a, .menu-item.nav-item-active > a + .menu-indicator')->addCss('color', $mobile_active_link_color['light'] . ' !important');
        $astroid_mobilemenu_inner->child('.menu-item.active, .menu-item.nav-item-active')->addCss('background-color', $mobile_active_background_color['light'] . ' !important');
        $mobilemenu_styles->child('.burger-menu-button .inner, .burger-menu-button .inner::before, .burger-menu-button .inner::after')->addCss('background-color', $mobile_menu_active_icon_color['light']);
        $mobilemenu_styles->render();

        $mobilemenu_styles = new Style('.astroid-offcanvas', 'dark');
        $mobilemenu_styles->addCss('color', $mobile_menu_text_color['dark'] . ' !important');
        $mobilemenu_styles->addCss('background-color', $mobile_background_color['dark'] . ' !important');
        $mobilemenu_styles->child('.burger-menu-button, .astroid-mobilemenu-container .astroid-mobilemenu-inner .dropdown-menus')->addCss('background-color', $mobile_background_color['dark'] . ' !important');
        $mobilemenu_styles->child('.menu-indicator')->addCss('color', $mobile_link_color['dark'] . ' !important');
        $astroid_mobilemenu_inner   =   $mobilemenu_styles->child('.astroid-mobilemenu-container .astroid-mobilemenu-inner');
        $astroid_mobilemenu_inner->child('.menu-item a')->addCss('color', $mobile_link_color['dark'] . ' !important');
        $astroid_mobilemenu_inner->child('.menu-item a:hover')->addCss('background-color', $mobile_hover_background_color['dark'] . ' !important');
        $astroid_mobilemenu_inner->child('.menu-item.active > a,  .menu-item.active > .nav-header, .menu-item.nav-item-active > a, .menu-item.nav-item-active > a + .menu-indicator')->addCss('color', $mobile_active_link_color['dark'] . ' !important');
        $astroid_mobilemenu_inner->child('.menu-item.active, .menu-item.nav-item-active')->addCss('background-color', $mobile_active_background_color['dark'] . ' !important');
        $mobilemenu_styles->child('.burger-menu-button .inner, .burger-menu-button .inner::before, .burger-menu-button .inner::after')->addCss('background-color', $mobile_menu_active_icon_color['dark']);
        $mobilemenu_styles->render();

        $mobilemenu_styles = new Style('.header-offcanvas-trigger.burger-menu-button');
        $mobilemenu_styles->child('.inner, .inner::before, .inner::after')->addCss('background-color', $mobile_menu_icon_color['light']);
        $mobilemenu_styles->render();

        $mobilemenu_styles = new Style('.header-offcanvas-trigger.burger-menu-button', 'dark');
        $mobilemenu_styles->child('.inner, .inner::before, .inner::after')->addCss('background-color', $mobile_menu_icon_color['dark']);
        $mobilemenu_styles->render();

        // Mobile Menu
        $mobilemenu_background_color = Style::getColor($params->get('mobilemenu_backgroundcolor', ''));
        $mobilemenu_link_color = Style::getColor($params->get('mobilemenu_menu_link_color', ''));
        $mobilemenu_menu_text_color = Style::getColor($params->get('mobilemenu_menu_text_color', ''));
        $mobilemenu_hover_background_color = Style::getColor($params->get('mobilemenu_hover_background_color', ''));
        $mobilemenu_active_link_color = Style::getColor($params->get('mobilemenu_menu_active_link_color', ''));
        $mobilemenu_active_background_color = Style::getColor($params->get('mobilemenu_menu_active_bg_color', ''));
        $mobilemenu_menu_icon_color = Style::getColor($params->get('mobilemenu_menu_icon_color', ''));
        $mobilemenu_menu_active_icon_color = Style::getColor($params->get('mobilemenu_menu_active_icon_color', ''));

        $mobilemenu_styles = new Style('.astroid-mobilemenu');
        $mobilemenu_styles->addCss('background-color', $mobilemenu_background_color['light'] . ' !important');
        $mobilemenu_styles->addCss('color', $mobilemenu_menu_text_color['light'] . ' !important');
        $astroid_mobilemenu_inner   =   $mobilemenu_styles->child('.astroid-mobilemenu-container .astroid-mobilemenu-inner');
        $astroid_mobilemenu_inner->child('.dropdown-menus')->addCss('background-color', $mobilemenu_background_color['light'] . ' !important');
        $mobilemenu_styles->child('.menu-indicator')->addCss('color', $mobilemenu_link_color['light'] . ' !important');
        $astroid_mobilemenu_inner->child('.menu-item a')->addCss('color', $mobilemenu_link_color['light'] . ' !important');
        $astroid_mobilemenu_inner->child('.menu-item a:hover')->addCss('background-color', $mobilemenu_hover_background_color['light'] . ' !important');
        $astroid_mobilemenu_inner->child('.menu-item.active > a, .menu-item.active > .nav-header, .menu-item.nav-item-active > a, .menu-item.nav-item-active > a + .menu-indicator')->addCss('color', $mobilemenu_active_link_color['light'] . ' !important');
        $astroid_mobilemenu_inner->child('.menu-item.active, .menu-item.nav-item-active')->addCss('background-color', $mobilemenu_active_background_color['light'] . ' !important');
        $mobilemenu_styles->render();

        $mobilemenu_styles = new Style('.astroid-mobilemenu', 'dark');
        $mobilemenu_styles->addCss('background-color', $mobilemenu_background_color['dark'] . ' !important');
        $mobilemenu_styles->addCss('color', $mobilemenu_menu_text_color['dark'] . ' !important');
        $astroid_mobilemenu_inner   =   $mobilemenu_styles->child('.astroid-mobilemenu-container .astroid-mobilemenu-inner');
        $astroid_mobilemenu_inner->child('.dropdown-menus')->addCss('background-color', $mobilemenu_background_color['dark'] . ' !important');
        $mobilemenu_styles->child('.menu-indicator')->addCss('color', $mobilemenu_link_color['dark'] . ' !important');
        $astroid_mobilemenu_inner->child('.menu-item a')->addCss('color', $mobilemenu_link_color['dark'] . ' !important');
        $astroid_mobilemenu_inner->child('.menu-item a:hover')->addCss('background-color', $mobilemenu_hover_background_color['dark'] . ' !important');
        $astroid_mobilemenu_inner->child('.menu-item.active > a, .menu-item.active > .nav-header, .menu-item.nav-item-active > a, .menu-item.nav-item-active > a + .menu-indicator')->addCss('color', $mobilemenu_active_link_color['dark'] . ' !important');
        $astroid_mobilemenu_inner->child('.menu-item.active, .menu-item.nav-item-active')->addCss('background-color', $mobilemenu_active_background_color['dark'] . ' !important');
        $mobilemenu_styles->render();

        $mobilemenu_styles = new Style('.header-mobilemenu-trigger.burger-menu-button');
        $mobilemenu_styles->child('.inner, .inner::before, .inner::after')->addCss('background-color', $mobilemenu_menu_icon_color['light']);
        $mobilemenu_styles->render();

        $mobilemenu_styles = new Style('.header-mobilemenu-trigger.burger-menu-button', 'dark');
        $mobilemenu_styles->child('.inner, .inner::before, .inner::after')->addCss('background-color', $mobilemenu_menu_icon_color['dark']);
        $mobilemenu_styles->render();

        $mobilemenu_styles = new Style('.astroid-mobilemenu-open .burger-menu-button');
        $mobilemenu_styles->child('.inner, .inner::before, .inner::after')->addCss('background-color', $mobilemenu_menu_active_icon_color['light']);
        $mobilemenu_styles->render();

        $mobilemenu_styles = new Style('.astroid-mobilemenu-open .burger-menu-button', 'dark');
        $mobilemenu_styles->child('.inner, .inner::before, .inner::after')->addCss('background-color', $mobilemenu_menu_active_icon_color['dark']);
        $mobilemenu_styles->render();

        // Contact Icon
        $contact_icon_color     =   Style::getColor($params->get('icon_color', ''));
        Style::addCssBySelector('.astroid-contact-info i[class*="fa-"]', 'color', $contact_icon_color['light']);
        Style::addCssBySelector('[data-bs-theme=dark] .astroid-contact-info i[class*="fa-"]', 'color', $contact_icon_color['dark']);
    }

    public static function article() {
        $params = Framework::getTemplate()->getParams();
        // Article listing
        $lead_heading_fontsize  =   $params->get('article_listing_lead_heading_fontsize', '');
        $intro_heading_fontsize =   $params->get('article_listing_intro_heading_fontsize', '');
        if (!empty($lead_heading_fontsize)) {
            $article    =   new Style('.items-leading .article-title .page-header h2');
            $article->addCss('font-size', $lead_heading_fontsize.'px');
            $article->render();
        }

        if (!empty($intro_heading_fontsize)) {
            $article    =   new Style('.items-row .article-title .page-header h2');
            $article->addCss('font-size', $intro_heading_fontsize.'px');
            $article->render();
        }
    }

    public static function custom()
    {
        $params = Framework::getTemplate()->getParams();
        $document = Framework::getDocument();

        $document->addCustomTag($params->get('trackingcode', ''));
        $document->addStyleDeclaration($params->get('customcss', ''));

        $paramcustomcssfiles = $params->get('customcssfiles');
        if (isset($paramcustomcssfiles) && $paramcustomcssfiles) {
            $customcssfiles = explode("\n", $paramcustomcssfiles);
        }
        else {
            $customcssfiles = array();
        }

        foreach ($customcssfiles as $customcssfile) {
            @list($file, $shift) = \explode('|', $customcssfile);
            $shift = $shift ? $shift : 0;
            $document->addStyleSheet($file, ['rel' => 'stylesheet', 'type' => 'text/css'], $shift);
        }

        $document->addScriptdeclaration($params->get('customjs', ''));
        $document->addScript(explode("\n", $params->get('customjsfiles', '')));

        $document->addCustomTag($params->get('beforehead', ''));
        $document->addCustomTag($params->get('beforebody', ''), 'body');

        // Page level custom code
        $app = Factory::getApplication();
        $itemid = $app->input->get('Itemid', '', 'INT');
        if (empty($itemid)) return false;

        $menu = $app->getMenu();
        $item = $menu->getItem($itemid);
        $params = !empty($item)?$item->getParams(): (new \Joomla\Registry\Registry());

        $document->addCustomTag($params->get('astroid_trackingcode', ''));
        $document->addStyleDeclaration($params->get('astroid_customcss', ''));

        $paramastroidcustomcssfiles = $params->get('astroid_customcssfiles');
        if (isset($paramastroidcustomcssfiles) && $paramastroidcustomcssfiles) {
            $customcssfiles = explode("\n", $paramastroidcustomcssfiles);
        }
        else {
            $customcssfiles = array();
        }

        foreach ($customcssfiles as $customcssfile) {
            @list($file, $shift) = \explode('|', $customcssfile);
            $shift = $shift ? $shift : 0;
            $document->addStyleSheet($file, ['rel' => 'stylesheet', 'type' => 'text/css'], $shift);
        }

        $document->addScriptdeclaration($params->get('astroid_customjs', ''));
        $document->addScript(explode("\n", $params->get('astroid_customjsfiles', '')));

        $document->addCustomTag($params->get('astroid_beforehead', ''));
        $document->addCustomTag($params->get('astroid_beforebody', ''), 'body');
    }

    public static function error()
    {
        $params = Framework::getTemplate()->getParams();
        $document = Framework::getDocument();

        $bodyStyle = new Style('body');
        $bodyStyle_dark = new Style('body', 'dark');
        $background_setting_404 = $params->get('background_setting_404');
        if ($background_setting_404) {
            switch ($background_setting_404) {
                case 'color':
                    $background_color   =   Style::getColor($params->get('background_color_404', ''));
                    $bodyStyle->addCss('background-color', $background_color['light']);
                    $bodyStyle_dark->addCss('background-color', $background_color['dark']);
                    break;
                case 'image':
                    $background_color   =   Style::getColor($params->get('img_background_color_404', ''));
                    $bodyStyle->addCss('background-color', $background_color['light']);
                    $bodyStyle_dark->addCss('background-color', $background_color['dark']);

                    $background_image = $params->get('background_image_404', '');
                    if (!empty($background_image)) {
                        $bodyStyle->addCss('background-image', 'url(' . Uri::root() . Helper\Media::getPath() . '/' . $background_image . ')');
                        $bodyStyle->addCss('background-repeat', $params->get('background_repeat_404', ''));
                        $bodyStyle->addCss('background-size', $params->get('background_size_404', ''));
                        $bodyStyle->addCss('background-attachment', $params->get('background_attchment_404', ''));
                        $bodyStyle->addCss('background-position', $params->get('background_position_404', ''));
                    }
                    break;
            }
        }
        $bodyStyle->render();
        $bodyStyle_dark->render();
    }
}