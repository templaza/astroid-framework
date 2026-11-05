<?php

/**
 * @package   Astroid Framework
 * @author    Astroid Framework Team https://astroidframe.work
 * @copyright Copyright (C) 2024 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

namespace Astroid\Helper;

use Astroid\Framework;
use Astroid\Helper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Joomla\Filesystem\Path;

defined('_JEXEC') or die;

class Font
{
    public static $system_fonts = [
        "" => 'Default',
        "Arial, Helvetica, sans-serif" => 'Arial, Helvetica',
        "Arial Black, Gadget, sans-serif" => 'Arial Black, Gadget',
        "Bookman Old Style, serif" => 'Bookman Old Style',
        "Comic Sans MS, cursive" => 'Comic Sans MS',
        "Courier, monospace" => 'Courier',
        "Garamond, serif" => 'Garamond',
        "Georgia, serif" => 'Georgia',
        "Impact, Charcoal, sans-serif" => 'Impact, Charcoal',
        "Lucida Console, Monaco, monospace" => 'Lucida Console, Monaco',
        "Lucida Sans Unicode, sans-serif" => 'Lucida Sans Unicode',
        "MS Sans Serif, Geneva, sans-serif" => 'MS Sans Serif, Geneva',
        "MS Serif, New York, sans-serif" => 'MS Serif, New York',
        "Palatino Linotype, Book Antiqua, Palatino, serif" => 'Palatino Linotype, Book Antiqua, Palatino',
        "Tahoma, Geneva, sans-serif" => 'Tahoma, Geneva',
        "Times New Roman, Times, serif" => 'Times New Roman, Times',
        "Trebuchet MS, Helvetica, sans-serif" => 'Trebuchet MS, Helvetica',
        "Verdana, Geneva, sans-serif" => 'Verdana, Geneva'
    ];

    public static function googleFonts()
    {
        $fonts = Helper::getJSONData('webfonts');
        $options = [];
        if (!isset($fonts['items'])) {
            return $options;
        }

        foreach ($fonts['items'] as $font) {
            $variants = [];
            if (count($font['variants']) > 1) {
                foreach ($font['variants'] as $v) {
                    if ($v == 'regular') {
                        $variants[] = '400';
                    } else if ($v == 'italic') {
                        $variants[] = '400i';
                    } else {
                        $variants[] = str_replace('talic', '', $v);
                    }
                }
            }
            $value = str_replace(' ', '+', $font['family']);
            if (!empty($variants)) {
                $value .= ':' . implode(',', $variants);
            }
            $options[$font['category']][$value] = $font['family'];
        }
        return $options;
    }

    public static function getAllFonts()
    {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        $googleFonts = self::googleFonts();
        $rt_fonts   =   array(
            'system' => array([
                'value' => '__default',
                'text'  => Text::_('TPL_ASTROID_OPTIONS_DEFAULT')
            ]),
            'google' => array([
                'value' => '__default',
                'text'  => Text::_('TPL_ASTROID_OPTIONS_DEFAULT')
            ]),
            'local'  => array([
                'value' => '__default',
                'text'  => Text::_('TPL_ASTROID_OPTIONS_DEFAULT')
            ])
        );

        foreach (self::$system_fonts as $name => $system_font) {
            $rt_fonts['system'][]     =   [
                'value' => $name,
                'text'  => $system_font
            ];
        }

        $template = Framework::getTemplate()->template;
        $uploadedFonts = self::getUploadedFonts($template);

        if (!empty($uploadedFonts)) {
            foreach ($uploadedFonts as $uploaded_font) {
                $rt_fonts['local'][]     =   [
                    'value' => $uploaded_font['id'],
                    'text'  => $uploaded_font['name']
                ];
            }

            // Method is only used in admin, so we use the un-cached function 'getUploadedFonts'
            // above, and check here if we need to update the fontcache.
            $cachedFonts  = self::getCachedLocalFonts($template);
            $current_hash = md5(json_encode($uploadedFonts));
            $cached_hash  = md5(json_encode($cachedFonts));
            if ($current_hash !== $cached_hash) {
                self::updateCachedLocalFonts($template, $uploadedFonts);
            }
        }

        foreach ($googleFonts as $group => $fonts) {
            foreach ($fonts as $fontValue => $font) {
                $rt_fonts['google'][]     =   [
                    'value' => $fontValue,
                    'text'  => $font
                ];
            }
        }
        return $rt_fonts;
    }

    /**
     * Loads the cached font-info for local fonts from `/media/templates/site/$template/fonts.cache.json`.
     *
     * @param string $template template name
     * @return null|array font-info
     */
    public static function getCachedLocalFonts($template)
    {
        if (empty($template)) {
            return null;
        }

        $template_font_cache = JPATH_SITE . '/media/templates/site/' . $template . '/fonts.cache.json';
        $ret = json_decode(@file_get_contents($template_font_cache), true);
        return $ret;
    }

    /**
     * Updates the cached font-info for local fonts in `/media/templates/site/$template/fonts.cache.json`.
     *
     * @param string $template template name
     * @param array $fonts font-info
     */
    public static function updateCachedLocalFonts($template, $fonts) {
        if (empty($template) || empty($fonts)) {
            return;
        }
        $template_font_cache = JPATH_SITE . '/media/templates/site/' . $template . '/fonts.cache.json';
        file_put_contents($template_font_cache, json_encode($fonts, JSON_THROW_ON_ERROR));
    }

    public static function getUploadedFonts($template)
    {
        Framework::getDebugger()->start('local-fonts');
        if (empty($template)) {
            return [];
        }

        $template_fonts_path        =   JPATH_SITE . "/templates/{$template}/fonts";
        $template_media_fonts_path  =   JPATH_SITE . "/media/templates/site/{$template}/fonts";
        $template_custom_fonts_path =   JPATH_SITE . "/images/{$template}/fonts";
        if (!file_exists($template_fonts_path) && !file_exists($template_media_fonts_path) && !file_exists($template_custom_fonts_path)) {
            return [];
        }
        $fonts = [];
        if (file_exists($template_media_fonts_path) || file_exists($template_fonts_path)) {
            if (file_exists($template_media_fonts_path)) $template_fonts_path = $template_media_fonts_path;
            $fonts  =   self::getLocalFonts($template_fonts_path);
        }

        if (file_exists($template_custom_fonts_path)) {
            $fonts  =   array_merge($fonts, self::getLocalFonts($template_custom_fonts_path));
        }
        Framework::getDebugger()->stop('local-fonts');
        return $fonts;
    }

    public static function getLocalFonts(string $template_fonts_path): array
    {
        $fonts           = [];
        $lib_extensions  = ['otf', 'ttf', 'woff', 'eot'];
        $woff2_extension = 'woff2';

        foreach (scandir($template_fonts_path) as $font_path) {
            $full_path = $template_fonts_path . '/' . $font_path;
            if (!is_file($full_path)) {
                continue;
            }

            $pathinfo  = pathinfo($full_path);
            $extension = strtolower($pathinfo['extension'] ?? '');

            if ($extension === $woff2_extension) {
                $fontname = self::getFontFamilyName($full_path);
            } elseif (in_array($extension, $lib_extensions, true)) {
                $font     = \FontLib\Font::load($full_path);
                $font->parse();
                $fontname = $font->getFontFullName();
            } else {
                continue;
            }

            if (empty($fontname)) {
                continue;
            }

            $fontid = 'library-font-' . Helper::slugify($fontname);
            if (!isset($fonts[$fontid])) {
                $fonts[$fontid] = [
                    'id'    => $fontid,
                    'name'  => $fontname,
                    'files' => [],
                ];
            }
            $basename     = pathinfo($full_path, PATHINFO_FILENAME);
            $sidecar_file = pathinfo($full_path, PATHINFO_DIRNAME) . '/' . $basename . '.json';
            $sidecar      = [];
            if (is_readable($sidecar_file)) {
                $meta = json_decode(@file_get_contents($sidecar_file), true);
                if (is_array($meta)) {
                    $sidecar = $meta;
                }
            }
            $descriptors = self::getFontDescriptors($font_path, $sidecar);
            $fonts[$fontid]['files'][] = [
                'file'   => $font_path,
                'weight' => $descriptors['weight'],
                'style'  => $descriptors['style'],
            ];
        }

        return $fonts;
    }

    // php-font-lib does not support woff2 — derive name from filename.
    // A {basename}.json sidecar with {"family":"Roboto Slab"} overrides the derived name,
    // keeping typography selections stable when the font file is replaced.
    // RobotoSlab-Regular.woff2 → Roboto Slab Regular
    private static function getFontFamilyName(string $filepath): string
    {
        if (!is_readable($filepath)) {
            return '';
        }

        $basename    = pathinfo($filepath, PATHINFO_FILENAME);
        $sidecar     = pathinfo($filepath, PATHINFO_DIRNAME) . '/' . $basename . '.json';
        if (is_readable($sidecar)) {
            $meta = json_decode(@file_get_contents($sidecar), true);
            if (!empty($meta['family']) && is_string($meta['family'])) {
                $family = htmlspecialchars(strip_tags(trim($meta['family'])), ENT_QUOTES, 'UTF-8');
                if (strlen($family) <= 100 && preg_match('/^[\p{L}\p{N} \-]+$/u', $family)) {
                    return $family;
                }
            }
        }

        $name = str_replace(['-', '_'], ' ', $basename);
        $name = preg_replace('/([a-z])([A-Z])/', '$1 $2', $name);
        $name = trim(preg_replace('/\s+/', ' ', $name));

        return $name !== '' ? $name : $basename;
    }

    private static function getFontDescriptors(string $filename, array $sidecar = []): array
    {
        // Order matters: longest/most-specific tokens first to avoid partial matches.
        $weight_map = [
            'Thin'       => '100',
            'ExtraLight' => '200',
            'UltraLight' => '200',
            'Light'      => '300',
            'Medium'     => '500',
            'SemiBold'   => '600',
            'DemiBold'   => '600',
            'ExtraBold'  => '800',
            'UltraBold'  => '800',
            'Black'      => '900',
            'Heavy'      => '900',
            'Bold'       => '700',
        ];

        // Infer from filename first.
        $weight = '400';
        foreach ($weight_map as $token => $value) {
            if (stripos($filename, $token) !== false) {
                $weight = $value;
                break;
            }
        }

        $style = 'normal';
        if (stripos($filename, 'Italic') !== false) {
            $style = 'italic';
        } elseif (stripos($filename, 'Oblique') !== false) {
            $style = 'oblique';
        }

        // Overlay sidecar fields individually — explicit sidecar values win, unspecified fields keep filename inference.
        if (!empty($sidecar['weight'])) {
            $weight = (string) $sidecar['weight'];
        }
        if (!empty($sidecar['style'])) {
            $style = $sidecar['style'];
        }

        return ['weight' => $weight, 'style' => $style];
    }

    public static function fontAstroidIcons() {
        $icons = self::_getASIcons();
        $array = [];
        $array[] = ['value' => '', 'name' => 'None'];
        foreach ($icons as $icon) {
            $array[] = ['value' => $icon['value'], 'name' => '<i class="' . $icon['value'] . '"></i> ' . $icon['name']];
        }
        $icons = $array;
        return $icons;
    }

    public static function _getASIcons()
    {
        $cache  =   Path::clean(JPATH_ROOT . '/cache/astroid/asicon/asicon.json');
        if (file_exists($cache)) {
            return json_decode(file_get_contents($cache), true);
        }
        $json = file_get_contents(ASTROID_MEDIA . '/vendor/linearicons/Linearicons.json');
        $json = \json_decode($json, true);
        $icons = [];
        foreach ($json['selection'] as $icon) {
            $icons[] = ['value' => 'as-icon as-icon-' . $icon['name'], 'name' => $icon['name'], 'type' => 'as-icon'];
        }
        Helper::putContents($cache, json_encode($icons));
        return $icons;
    }

    public static function fontAwesomeIcons($html = false)
    {
        $icons = self::_getFAIcons();
        if ($html) {
            $array = [];
            $array[] = ['value' => '', 'name' => 'None'];
            foreach ($icons as $icon) {
                $array[] = ['value' => $icon['value'], 'name' => '<i class="' . $icon['value'] . '"></i> ' . $icon['name']];
            }
            $icons = $array;
        }
        return $icons;
    }

    public static function _getFAIcons()
    {

        $version = Helper\Constants::$fontawesome_version;
        if (file_exists(JPATH_ROOT . '/cache/astroid/fontawesome/free-' . $version . '-.json')) {
            return json_decode(file_get_contents(JPATH_ROOT . '/cache/astroid/fontawesome/free-' . $version . '-.json'), true);
        }

        $json = file_get_contents(ASTROID_MEDIA . '/vendor/fontawesome/metadata/icons.json');
        $json = \json_decode($json, true);

        $icons = [];
        foreach ($json as $icon => $info) {
            foreach ($info['styles'] as $style) {
                $icons[] = ['value' => 'fa' . substr($style, 0, 1) . ' fa-' . $icon, 'name' => $info['label'], 'type' => $style];
            }
        }
        Helper::putContents(JPATH_ROOT . '/cache/astroid/fontawesome/free-' . $version . '-.json', json_encode($icons));
        return $icons;
    }

    public static function getFontType($value)
    {
        $type = 'google';
        if (Helper::startsWith($value, 'library-font-')) {
            $type = 'local';
        }
        if (isset(self::$system_fonts[$value])) {
            $type = 'system';
        }
        return $type;
    }

    public static function getFontFamily($value)
    {
        $type = self::getFontType($value);
        switch ($type) {
            case 'google':
                $value = '"'.self::loadGoogleFont($value).'"';
                break;
            case 'local':
                $value = '"'.self::loadLocalFont($value).'"';
                break;
            case 'system':
                return $value;
                break;
        }
        return $value;
    }

    public static function loadGoogleFont($value)
    {
        $document = Factory::getApplication()->getDocument();
        $wa = $document->getWebAssetManager();

        $value = str_replace(',', ';', $value);
        $value = str_replace(':', ':ital,wght@', $value);

        $wght = substr($value, strpos($value, "@") + 1);

        $_value = explode(';', $wght);
        foreach ($_value as &$_v) {
            $_v = explode('i', $_v);
            if (count($_v) == 2) {
                $_v = '1,' . $_v[0];
            } else {
                $_v = '0,' . $_v[0];
            }
        }
        sort($_value);

        if (strpos($value, "@") > 0) {
            $value = str_replace($wght, implode(';', $_value), $value);
        }

        if ($value) {
            $wa->registerAndUseStyle('astroid.googlefont', 'https://fonts.gstatic.com', ['version' => 'auto'], ['rel' => 'preconnect']);
            $wa->registerAndUseStyle('astroid.googlefont.'.$value, 'https://fonts.googleapis.com/css2?family=' . $value . '&display=swap');
        } else {
            return '';
        }

        @list($font, $variants) = explode(":", $value);

        return str_replace('+', ' ', $font);
    }

    public static function loadLocalFont($value)
    {
        $template = Framework::getTemplate();
        $document = Factory::getApplication()->getDocument();
        $wa = $document->getWebAssetManager();
        $uploaded_fonts = $template->getFonts();
        $template_media_fonts_path  = JPATH_SITE . "/media/templates/site/{$template->template}/fonts";
        $template_custom_fonts_path = JPATH_SITE . "/images/{$template->template}/fonts";
        $font_custom_path           = Uri::root() . "images/{$template->template}/fonts/";
        if (file_exists($template_media_fonts_path)) {
            $font_path      =       Uri::root() . "media/templates/site/{$template->template}/fonts/";
        } else {
            $font_path      =       Uri::root() . "templates/{$template->template}/fonts/";
        }
        if (isset($uploaded_fonts[$value])) {
            $files = $uploaded_fonts[$value]['files'];
            $value = $uploaded_fonts[$value]['name'];
            foreach ($files as $file) {
                $filename = is_array($file) ? $file['file']   : $file;
                $weight   = is_array($file) ? (int) $file['weight'] : 400;
                $style    = is_array($file) ? $file['style']  : 'normal';
                if (file_exists($template_custom_fonts_path . '/' . $filename)) {
                    $src = $font_custom_path . $filename;
                } else {
                    $src = $font_path . $filename;
                }
                $wa->addInlineStyle(
                    '@font-face { font-family: "' . $value . '"; src: url("' . $src . '");' .
                    ' font-weight: ' . $weight . '; font-style: ' . $style . '; }'
                );
            }
        }
        return $value;
    }

    public static function loadFontAwesome(): void
    {
        $params = Helper::getPluginParams();
        $source = $params->get('astroid_load_fontawesome', "cdn");
        $wa = Factory::getApplication()->getDocument()->getWebAssetManager();
        switch ($source) {
            case 'cdn':
                $wa->registerAndUseStyle('fontawesome', "https://use.fontawesome.com/releases/v" . Helper\Constants::$fontawesome_version . "/css/all.css");
                break;
            case 'local':
                $wa->registerAndUseStyle('fontawesome', 'media/astroid/assets/vendor/fontawesome/css/all.min.css');
                break;
            default:
                if (Framework::isAdmin()) {
                    $wa->registerAndUseStyle('fontawesome', 'media/astroid/assets/vendor/fontawesome/css/all.min.css');
                }
                break;
        }
    }

    public static function loadASIcon(): void
    {
        $wa = Factory::getApplication()->getDocument()->getWebAssetManager();
        $wa->registerAndUseStyle('linearicons', 'media/astroid/assets/vendor/linearicons/font.min.css');
    }
}
