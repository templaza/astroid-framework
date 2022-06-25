<?php

/**
 * @package   Astroid Framework
 * @author    JoomDev https://www.joomdev.com
 * @copyright Copyright (C) 2009 - 2020 JoomDev.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

namespace Astroid\Component;

use Astroid\Framework;
use Astroid\Helper;
use Astroid\Helper\Style;

defined('_JEXEC') or die;

class Utility
{
    public static function meta()
    {
        $app = \JFactory::getApplication();
        $document = Framework::getDocument();
        $itemid = $app->input->get('Itemid', '', 'INT');
        $menu = $app->getMenu();
        $item = $menu->getItem($itemid);

        $template_params = Framework::getTemplate()->getParams();
        $config = \JFactory::getConfig();

        if (empty($item)) {
            return;
        }

        $params = $item->getParams();

        $enabled = $params->get('astroid_opengraph_menuitem', 0);
        if (empty($enabled)) {
            return;
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
            $og_image = \JURI::root() . $params->get('astroid_og_image_menuitem', '');
        }

        $og_sitename = $config->get('sitename');
        $og_siteurl = \JURI::getInstance();

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
            $document->addMeta('og:image', $og_image);
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
            $style->addCss('background-image', 'url(' . \JURI::root() . Helper\Media::getPath() . '/' . $layout_background_image . ')');
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
        $document = Framework::getDocument();
        $enable_smooth_scroll = $params->get('enable_smooth_scroll', '');
        if ($enable_smooth_scroll == '1') {
            $speed = $params->get('smooth_scroll_speed', '');
            $document->addScript('vendor/astroid/js/smooth-scroll.polyfills.min.js', 'body');
            $header = $params->get('header', TRUE);
            $mode = $params->get('header_mode', 'horizontal');
            $sidebar = ($header && $mode == 'sidebar');

            $script = '
			var scroll = new SmoothScroll(\'a[href*="#"]\', {
            speed: ' . $speed . '
            ' . ($sidebar ? '' : ', header: ".astroid-header"') . '
			});';
            $document->addScriptDeclaration($script, 'body');
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
                      background-image: url("' . \JURI::root() . Helper\Media::getPath() . '/' . $params->get('basic_background_image') . '");
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

    public static function typography()
    {
        $params = Framework::getTemplate()->getParams();
        $customselector = $params->get('custom_typography_selectors', '');

        $types = array('body' => 'body, .body', 'h1' => 'h1, .h1', 'h2' => 'h2, .h2', 'h3' => 'h3, .h3', 'h4' => 'h4, .h4', 'h5' => 'h5, .h5', 'h6' => 'h6, .h6', 'logo' => '.astroid-logo-text, .astroid-logo-text > a.site-title', 'logo_tag_line' => '.astroid-logo-text > p.site-tagline', 'menu' => '.astroid-nav > li > a, .astroid-sidebar-menu > li > a', 'submenu' => '.nav-submenu-container .nav-submenu > li, .jddrop-content .megamenu-item .megamenu-menu li, .nav-submenu', 'custom' => $customselector);

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
        $body = new Style('body');
        $body->addCss('background-color', $params->get('body_background_color', ''));
        $body->addCss('color', $params->get('body_text_color', ''));
        $body->link()->addCss('color', $params->get('body_link_color', ''));
        $body->link()->hover()->addCss('color', $params->get('body_link_hover_color', ''));
        $body->render();  // render body colors

        // Header
        Style::addCssBySelector('header', 'color', $params->get('header_text_color', ''));
        Style::addCssBySelector('.astroid-header-section, .astroid-sidebar-header', 'background-color', $params->get('header_bg', ''));

        $textLogo = new Style('.astroid-logo-text');
        $textLogo->child('.site-title')->addCss('color', $params->get('header_logo_text_color', ''));
        $textLogo->child('.site-tagline')->addCss('color', $params->get('header_logo_text_tagline_color', ''));
        $textLogo->render();  // render text logo colors

        // Sticky Header
        $stickyHeader = new Style('#astroid-sticky-header');
        $stickyHeader->addCss('background-color', $params->get('stick_header_bg_color', ''));
        $stickyHeaderLink = $stickyHeader->child('.astroid-nav .nav-link');
        $stickyHeaderLink->addCss('color', $params->get('stick_header_menu_link_color', ''));
        $stickyHeaderLink->hover()->addCss('color', $params->get('stick_header_menu_link_hover_color', ''));
        $stickyHeaderLink->active('.active')->addCss('color', $params->get('stick_header_menu_link_active_color', ''));
        $stickyHeader->render();  // render sticky header

        // Menu
        $navLink = new Style('.astroid-nav .nav-link, .astroid-sidebar-menu .nav-link');
        $navLink->addCss('color', $params->get('main_menu_link_color', ''));
        $navLink->hover()->addCss('color', $params->get('main_menu_link_hover_color', ''));
        $navLink->focus()->addCss('color', $params->get('main_menu_link_hover_color', ''));
        $navLink->active('.active')->addCss('color', $params->get('main_menu_link_active_color', ''));
        $navLink->render(); // render navlink

        // Dropdown Menu
        $dropdown = Style::addCssBySelector('.megamenu-container', 'background-color', $params->get('dropdown_bg_color', ''));

        $submenuDropdown = Style::addCssBySelector('.megamenu-container .nav-submenu .nav-submenu', 'background-color', $params->get('dropdown_bg_color', ''));

        Style::addCssBySelector('.has-megamenu.open .arrow', 'border-bottom-color', $params->get('dropdown_bg_color', ''));

        $link = $dropdown->child('li.nav-item-submenu > a');
        $link->addCss('color', $params->get('dropdown_link_color', ''));
        $link->hover()->addCss('color', $params->get('dropdown_menu_link_hover_color', ''))->addCss('background-color', $params->get('dropdown_menu_hover_bg_color', ''));
        $link->active('.active')->addCss('color', $params->get('dropdown_menu_active_link_color', ''))->addCss('background-color', $params->get('dropdown_menu_active_bg_color', ''));
        $dropdown->render(); // render dropdown

        // Offcanvas Menu
        $mobile_background_color = $params->get('mobile_backgroundcolor', '');
        $mobile_link_color = $params->get('mobile_menu_link_color', '');
        $mobile_menu_text_color = $params->get('mobile_menu_text_color', '');
        $mobile_hover_background_color = $params->get('mobile_hover_background_color', '');
        $mobile_active_link_color = $params->get('mobile_menu_active_link_color', '');
        $mobile_active_background_color = $params->get('mobile_menu_active_bg_color', '');
        $mobile_menu_icon_color = $params->get('mobile_menu_icon_color', '');
        $mobile_menu_active_icon_color = $params->get('mobile_menu_active_icon_color', '');

        $mobilemenu_styles = [];
        if (!empty($mobile_background_color)) {
            $mobilemenu_styles[] = '.astroid-offcanvas, .astroid-offcanvas .burger-menu-button, .astroid-offcanvas .astroid-mobilemenu-container .astroid-mobilemenu-inner .dropdown-menus{ background-color: ' . $mobile_background_color . ' !important;}';
        }
        if (!empty($mobile_menu_text_color)) {
            $mobilemenu_styles[] = '.astroid-offcanvas { color: ' . $mobile_menu_text_color . ' !important;}';
        }
        if (!empty($mobile_link_color)) {
            $mobilemenu_styles[] = '.astroid-offcanvas .astroid-mobilemenu-container .astroid-mobilemenu-inner .menu-item a, .astroid-offcanvas .menu-indicator{ color: ' . $mobile_link_color . ' !important;}';
        }
        if (!empty($mobile_hover_background_color)) {
            $mobilemenu_styles[] = '.astroid-offcanvas .astroid-mobilemenu-container .astroid-mobilemenu-inner .menu-item a:hover{ background-color: ' . $mobile_hover_background_color . ' !important;}';
        }
        if (!empty($mobile_active_link_color)) {
            $mobilemenu_styles[] = '.astroid-offcanvas .astroid-mobilemenu-container .astroid-mobilemenu-inner .menu-item.active > a, .astroid-offcanvas .astroid-mobilemenu-container .astroid-mobilemenu-inner .menu-item.active > .nav-header, .astroid-offcanvas .astroid-mobilemenu-container .astroid-mobilemenu-inner .menu-item.nav-item-active > a, .astroid-offcanvas .astroid-offcanvas .astroid-mobilemenu-container .astroid-mobilemenu-inner .menu-item.nav-item-active > a + .menu-indicator{ color: ' . $mobile_active_link_color . ' !important;}';
        }
        if (!empty($mobile_active_background_color)) {
            $mobilemenu_styles[] = '.astroid-offcanvas .astroid-mobilemenu-container .astroid-mobilemenu-inner .menu-item.active, .astroid-offcanvas .astroid-mobilemenu-container .astroid-mobilemenu-inner .menu-item.nav-item-active { background-color: ' . $mobile_active_background_color . ' !important;}';
        }
        if (!empty($mobile_menu_icon_color)) {
            $mobilemenu_styles[] = '.header-offcanvas-trigger.burger-menu-button .inner, .header-offcanvas-trigger.burger-menu-button .inner::before, .header-offcanvas-trigger.burger-menu-button .inner::after{background-color: ' . $mobile_menu_icon_color . ';}';
        }
        if (!empty($mobile_menu_active_icon_color)) {
            $mobilemenu_styles[] = '.astroid-offcanvas .burger-menu-button .inner, .astroid-offcanvas .burger-menu-button .inner::before, .astroid-offcanvas .burger-menu-button .inner::after{background-color: ' . $mobile_menu_active_icon_color . ';}';
        }

        Framework::getDocument()->addStyleDeclaration(implode('', $mobilemenu_styles));

        // Mobile Menu
        $mobilemenu_background_color = $params->get('mobilemenu_backgroundcolor', '');
        $mobilemenu_link_color = $params->get('mobilemenu_menu_link_color', '');
        $mobilemenu_menu_text_color = $params->get('mobilemenu_menu_text_color', '');
        $mobilemenu_hover_background_color = $params->get('mobilemenu_hover_background_color', '');
        $mobilemenu_active_link_color = $params->get('mobilemenu_menu_active_link_color', '');
        $mobilemenu_active_background_color = $params->get('mobilemenu_menu_active_bg_color', '');
        $mobilemenu_menu_icon_color = $params->get('mobilemenu_menu_icon_color', '');
        $mobilemenu_menu_active_icon_color = $params->get('mobilemenu_menu_active_icon_color', '');

        $mobilemenu_styles = [];
        if (!empty($mobilemenu_background_color)) {
            $mobilemenu_styles[] = '.astroid-mobilemenu, .astroid-mobilemenu-container .astroid-mobilemenu-inner .dropdown-menus{ background-color: ' . $mobilemenu_background_color . ' !important;}';
        }
        if (!empty($mobilemenu_menu_text_color)) {
            $mobilemenu_styles[] = '.astroid-mobilemenu { color: ' . $mobilemenu_menu_text_color . ' !important;}';
        }
        if (!empty($mobilemenu_link_color)) {
            $mobilemenu_styles[] = '.astroid-mobilemenu .astroid-mobilemenu-container .astroid-mobilemenu-inner .menu-item a, .astroid-mobilemenu .menu-indicator{ color: ' . $mobilemenu_link_color . ' !important;}';
        }
        if (!empty($mobilemenu_hover_background_color)) {
            $mobilemenu_styles[] = '.astroid-mobilemenu .astroid-mobilemenu-container .astroid-mobilemenu-inner .menu-item a:hover{ background-color: ' . $mobilemenu_hover_background_color . ' !important;}';
        }
        if (!empty($mobilemenu_active_link_color)) {
            $mobilemenu_styles[] = '.astroid-mobilemenu .astroid-mobilemenu-container .astroid-mobilemenu-inner .menu-item.active > a, .astroid-mobilemenu .astroid-mobilemenu-container .astroid-mobilemenu-inner .menu-item.active > .nav-header, .astroid-mobilemenu-container .astroid-mobilemenu-inner .menu-item.nav-item-active > a, .astroid-mobilemenu-container .astroid-mobilemenu-inner .menu-item.nav-item-active > a + .menu-indicator{ color: ' . $mobilemenu_active_link_color . ' !important;}';
        }
        if (!empty($mobilemenu_active_background_color)) {
            $mobilemenu_styles[] = '.astroid-mobilemenu .astroid-mobilemenu-container .astroid-mobilemenu-inner .menu-item.active, .astroid-mobilemenu-container .astroid-mobilemenu-inner .menu-item.nav-item-active { background-color: ' . $mobilemenu_active_background_color . ' !important;}';
        }
        if (!empty($mobilemenu_menu_icon_color)) {
            $mobilemenu_styles[] = '.header-mobilemenu-trigger.burger-menu-button .inner, .header-mobilemenu-trigger.burger-menu-button .inner::before, .header-mobilemenu-trigger.burger-menu-button .inner::after{background-color: ' . $mobilemenu_menu_icon_color . ';}';
        }
        if (!empty($mobilemenu_menu_active_icon_color)) {
            $mobilemenu_styles[] = '.astroid-mobilemenu-open .burger-menu-button .inner, .astroid-mobilemenu-open .burger-menu-button .inner::before, .astroid-mobilemenu-open .burger-menu-button .inner::after{background-color: ' . $mobilemenu_menu_active_icon_color . ';}';
        }

        Framework::getDocument()->addStyleDeclaration(implode('', $mobilemenu_styles));

        // Contact Icon
        Style::addCssBySelector('.astroid-contact-info i[class*="fa-"]', 'color', $params->get('icon_color', ''));
    }

    public static function article() {
        $params = Framework::getTemplate()->getParams();
        // Article listing
        $lead_heading_fontsize  =   $params->get('article_listing_lead_heading_fontsize', '');
        $intro_heading_fontsize =   $params->get('article_listing_intro_heading_fontsize', '');
        if (!empty($lead_heading_fontsize)) {
            $article    =   new Style('.items-leading .article-title .page-header [itemprop="name"]');
            $article->addCss('font-size', $lead_heading_fontsize.'px');
            $article->render();
        }

        if (!empty($intro_heading_fontsize)) {
            $article    =   new Style('.items-row .article-title .page-header [itemprop="name"]');
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
        $app = \JFactory::getApplication();
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
        $background_setting_404 = $params->get('background_setting_404');
        if ($background_setting_404) {
            switch ($background_setting_404) {
                case 'color':
                    $bodyStyle->addCss('background-color', $params->get('background_color_404', ''));
                    break;
                case 'image':
                    $bodyStyle->addCss('background-color', $params->get('img_background_color_404', ''));

                    $background_image = $params->get('background_image_404', '');
                    if (!empty($background_image)) {
                        $bodyStyle->addCss('background-image', 'url(' . \JURI::root() . Helper\Media::getPath() . '/' . $background_image . ')');
                        $bodyStyle->addCss('background-repeat', $params->get('background_repeat_404', ''));
                        $bodyStyle->addCss('background-size', $params->get('background_size_404', ''));
                        $bodyStyle->addCss('background-attachment', $params->get('background_attchment_404', ''));
                        $bodyStyle->addCss('background-position', $params->get('background_position_404', ''));
                    }
                    break;
            }
        }
        $bodyStyle->render();
    }
}