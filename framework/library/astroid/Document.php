<?php

/**
 * @package   Astroid Framework
 * @author    Astroid Framework Team https://astroidframe.work
 * @copyright Copyright (C) 2023 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

namespace Astroid;

defined('_JEXEC') or die;

require_once __DIR__ . "/../scssphp/scss.inc.php";

use Astroid\Helper\Constants;
use Joomla\Filesystem\File;
use ScssPhp\ScssPhp\Compiler;
use MatthiasMullie\Minify;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\Layout\FileLayout;

class Document
{
    protected $_metas = [], $_links = [];
    protected $_javascripts = ['head' => [], 'body' => []];
    protected $_stylesheets = [];
    protected $_scripts = ['head' => [], 'body' => []];
    protected $_styles = ['desktop' => [], 'tablet' => [], 'mobile' => []];
    protected $_customtags = ['head' => [], 'body' => []];
    protected $_dev = null;
    protected $minify_css = false;
    protected $minify_js = false;
    protected $minify_html = false;
    protected static $_fontawesome = false;
    protected static $_fancybox = false;
    protected static $_layout_paths = [];
    protected $type = null;
    protected $modules = null;

    protected $scriptOptions = [];

    public function __construct()
    {
        $params = Framework::getTemplate()->getParams();
        $this->minify_css = $params->get('minify_css', false);
        $this->minify_js = $params->get('minify_js', false);
        $this->minify_html = $params->get('minify_html', false);

        $doc = Factory::getDocument();
        $this->type = $doc->getType();

        $template = Framework::getTemplate();
        $this->addLayoutPath(JPATH_SITE . '/templates/' . $template->template . '/html/frontend/');
    }

    public function getType()
    {
        return $this->type;
    }

    public function addLayoutPath($path)
    {
        self::$_layout_paths[] = $path;
    }

    public function include($section, $data = [], $return = false)
    {
        $path = null;
        $name = str_replace('.', '/', $section);
        if (Framework::isAdmin() && file_exists(JPATH_LIBRARIES . '/astroid/framework/layouts/' . $name . '.php')) {
            $path = JPATH_LIBRARIES . '/astroid/framework/layouts';
        } else {
            $layout_paths = self::$_layout_paths;
            $layout_paths[] = JPATH_LIBRARIES . '/astroid/framework/frontend/';
            foreach ($layout_paths as $layout_path) {
                $layout_path = substr($layout_path, -1) == '/' ? $layout_path : $layout_path . '/';
                if (file_exists($layout_path . $name . '.php')) {
                    $path = $layout_path;
                    break;
                }
            }
        }

        if ($path === null) {
            return '';
        }

        $layout = new FileLayout($section, $path);
        $content = $layout->render($data);

        /* if (Framework::isAdmin()) {
            $layout = new FileLayout($section, $path);
            $content = $layout->render($data);
        } else {
            $hash = Helper::getFileHash($path . '/' . $name . '.php');
            $hash = md5($hash . $template->hash . serialize($data));
            $file = ASTROID_CACHE . '/includes/' . $name . '/' . $hash . '.html';
            if (file_exists($file)) {
                $content = file_get_contents($file);
            } else {
                $beforeHash = $this->_assetsHash();;

                $layout = new FileLayout($section, $path);
                $content = $layout->render($data);

                if ($beforeHash === $this->_assetsHash()) {
                    Helper::putContents($file, $content);
                }
            }
        } */
        if ($return) {
            return trim($content);
        }
        echo trim($content);
    }

    public function compress()
    {
        $app = Factory::getApplication();
        $body = $app->getBody();

        // Stop Minification for RSSFeeds and other doc types.
        if ($this->type == 'feed') $this->minify_css = $this->minify_js = $this->minify_html = false;

        if ($this->minify_css) $body = $this->minifyCSS($body);

        if ($this->minify_js && !$this->isFrontendEditing()) $body = $this->minifyJS($body);

        if ($this->minify_html) $body = $this->minifyHTML($body);

        $app->setBody($body);
    }

    public function isFrontendEditing()
    {
        if (!Framework::isSite()) {
            return false;
        }

        $app = Factory::getApplication();
        $option = $app->input->get('option', '', 'STRING');
        $view = $app->input->get('view', '', 'STRING');
        $layout = $app->input->get('layout', 'default', 'STRING');
        $task = $app->input->get('task', '', 'STRING');
        $tmpl = $app->input->get('tmpl', '', 'STRING');

        if ($option == 'com_content' && $view == 'form' && $layout == 'edit') {
            return true;
        }

        if ($option == 'com_media' && !empty($view) && $tmpl == 'component') {
            return true;
        }

        return false;
    }

    public function _cssPath($file)
    {
        $site_path = parse_url(Uri::root(), PHP_URL_PATH);

        if (Helper::startsWith($file, $site_path)) {
            $file = preg_replace('/' . preg_quote($site_path, '/') . '/', '/', $file, 1);
        }

        $file_info = parse_url($file);
        if (isset($file_info['host'])) {
            if ($file_info['host'] == parse_url(Uri::root(), PHP_URL_HOST)) {
                $file = strtok($file, '?');
                $file = str_replace(Uri::root(), '', $file);
                return $file;
            } else {
                return '@import "' . $file . '"';
            }
        } else {
            if (substr($file, 0, 1) == '/') {
                $file = strtok($file, '?');
                $file = substr($file, 1);
            }
            return $file;
        }
    }

    public function _jsPath($file)
    {
        $site_path = parse_url(Uri::root(), PHP_URL_PATH);

        if (Helper::startsWith($file, $site_path)) {
            $file = preg_replace('/' . preg_quote($site_path, '/') . '/', '/', $file, 1);
        }

        $file_info = parse_url($file);
        if (isset($file_info['host'])) {
            if ($file_info['host'] == parse_url(Uri::root(), PHP_URL_HOST)) {
                $file = strtok($file, '?');
                $file = str_replace(Uri::root(), '', $file);
                return $file;
            } else {
                return file_get_contents($this->addProtocol($file));
            }
        } else {
            if (substr($file, 0, 1) == '/') {
                $file = strtok($file, '?');
                $file = substr($file, 1);
            }
            return $file;
        }
    }


    function addProtocol($url)
    {
        if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
            $url = "https:" . (substr($url, 0, 2) == '//' ? $url : '//' . $url);
        }
        return $url;
    }


    public function minifyCSS($html)
    {
        Framework::getDebugger()->log('Minifying CSS');
        $stylesheets = [];
        $stylesheetLinks = [];
        $stylesheetsUrls = [];

        $html = preg_replace_callback('/(<link\s[^>]*href=")([^"]*)("[^>][^>]*rel=")([^"]*)("[^>]*\/>)/siU', function ($matches) use (&$stylesheetLinks, &$stylesheetsUrls) {
            if (isset($matches[4]) && $matches[4] === 'stylesheet') {
                if (strpos($matches[2], 'fonts.googleapis.com') > 0 || strpos($matches[2], 'use.fontawesome.com') > 0) {
                    return $matches[0];
                }
                $url = $this->_cssPath($matches[2]);
                $ext = pathinfo($url, PATHINFO_EXTENSION);
                if ($ext !== 'css' && !Helper::startsWith($url, '@import')) return $matches[0];
                $stylesheetLinks[] = $url;
                $stylesheetsUrls[] = $this->beutifyURL($matches[2]);
                return '';
            }
            return $matches[0];
        }, $html);

        $html = preg_replace_callback('/(<style>)(.*)(<\/style>)|(<style\s[^>]*type=")([^"]*)("[^>]*>)(.*)(<\/style>)/siU', function ($matches) use (&$stylesheets) {
            if (isset($matches[3]) && $matches[3] == '</style>' && !empty($matches[2])) {
                $stylesheets[] = $matches[2];
                return '';
            }
            if (isset($matches[8]) && $matches[8] == '</style>' && !empty($matches[7])) {
                $stylesheets[] = $matches[7];
                return '';
            }
            return $matches[0];
        }, $html);

        $version = md5(serialize($stylesheets).serialize($stylesheetLinks));

        $cssFile = ASTROID_CACHE . '/css/' . $version . '.css';
        if (!file_exists($cssFile)) {
            Framework::getReporter('Logs')->add('Minify &amp; Combine Files <code>' . implode('</code>, <code>', $stylesheetsUrls) . '</code>.');
            Helper::putContents($cssFile, '');
            $minifier = new Minify\CSS($cssFile);
            if (count($stylesheetLinks)) {
                foreach ($stylesheetLinks as $stylesheet) {
                    if (file_exists(JPATH_SITE . '/' . $stylesheet)) {
                        $minifier->add(JPATH_SITE . '/' . $stylesheet);
                    }
                }
            }
            if (count($stylesheets)) {
                foreach ($stylesheets as $stylesheet) {
                    $minifier->add($stylesheet);
                }
            }
            $minifier->minify($cssFile);
        } else {
            Framework::getReporter('Logs')->add('Getting Minified CSS <code>' . (str_replace(JPATH_SITE . '/', '', $cssFile)) . '</code> from cache.');
        }

        $html = str_replace('</head>', '<link rel="stylesheet" href="' . 'cache/astroid/css/' . $version . '.css?' . Helper::joomlaMediaVersion() . '" /></head>', $html);
        Framework::getDebugger()->log('Minifying CSS');
        return $html;
    }

    public function minifyJS($html)
    {
        $juri = Uri::getInstance();
        $base_path = str_replace($juri->getScheme() . '://' . $juri->getHost() . '/', '', $juri->root());

        Framework::getDebugger()->log('Minifying JS');
        $excludes = Framework::getTemplate()->getParams()->get('minify_js_excludes', '');
        $javascripts = [];
        $javascriptFiles = [];
        $html = preg_replace_callback('/(<script\s[^>]*src=")([^"]*)("[^>]*>)(.*)(<\/script>)|(<script>)(.*)(<\/script>)|(<script\s[^>]*type=")([^"]*)("[^>]*>)(.*)(<\/script>)/siU', function ($matches) use (&$javascripts, &$javascriptFiles, $base_path, $excludes) {
            // print_r($matches);
            $script = [];
            if (isset($matches[5]) && $matches[5] == '</script>' && !empty($matches[2])) {
                if (strpos($matches[0], 'type="module"') > 0 || strpos($matches[0], 'media/system/js/joomla-hidden-mail-es5.min.js') > 0 || strpos($matches[0], 'webcomponents-bundle.min.js') > 0 || strpos($matches[0], 'media/system/js/core') > 0 || strpos($matches[0], 'www.googletagmanager.com') > 0) {
                    return $matches[0];
                }

                // Excludes scripts
                $scriptName = $this->beutifyURL($matches[2]);
                $file_path = strtok($scriptName, '?');
                if (substr($file_path, 0, strlen($base_path)) === $base_path) {
                    $file_path = preg_replace('/' . preg_quote($base_path, '/') . '/', '', $file_path, 1);
                }

                $file = basename($file_path);
                if (Helper::matchFilename($file, \explode(',', $excludes))) {
                    return $matches[0];
                }
                $script = ['content' => $scriptName, 'type' => 'url'];
                $javascriptFiles[] = $scriptName;
            } else if (isset($matches[8]) && $matches[8] == '</script>' && !empty($matches[7])) {
                $script = ['content' => $matches[7], 'type' => 'script'];
            } else if (isset($matches[13]) && $matches[13] == '</script>' && !empty($matches[10]) && ($matches[10] == 'text/javascript') && !empty($matches[12])) {
                $script = ['content' => $matches[12], 'type' => 'script'];
            }
            if (!empty($script)) {
                $javascripts[] = $script;
                return '';
            }
            return $matches[0];
        }, $html);

        // print_r($javascripts);

        $version = md5(json_encode($javascripts));
        $jsFile = ASTROID_CACHE . '/js/' . $version . '.js';
        if (!file_exists($jsFile)) {
            Framework::getReporter('Logs')->add('Minifying JS <code>' . implode('</code>, <code>', $javascriptFiles) . '</code>.');

            Helper::putContents($jsFile, '');
            foreach ($javascripts as $javascript) {

                $minifier = new Minify\JS();
                if ($javascript['type'] == 'url') {
                    $file_path = strtok($javascript['content'], '?');

                    if (substr($file_path, 0, strlen($base_path)) === $base_path) {
                        $file_path = preg_replace('/' . preg_quote($base_path, '/') . '/', '', $file_path, 1);
                    }

                    $file = basename($file_path);

                    if (!Helper::matchFilename($file, \explode(',', $excludes))) {
                        $content = "/* `{$file_path}` minified by Astroid */";
                        if (file_exists(JPATH_SITE . '/' . $file_path)) {
                            $minifier->add(JPATH_SITE . '/' . $file_path);
                        } else {
                            $minifier->add(file_get_contents($this->addProtocol($javascript['content'])));
                        }
                        $content .= $minifier->minify();
                    } else {
                        $content = "/* `{$file_path}` minification skipped by Astroid */";

                        if (file_exists(JPATH_SITE . '/' . $file_path)) {
                            $content .= file_get_contents(JPATH_SITE . '/' . $file_path);
                        } else {
                            $content .= file_get_contents($this->addProtocol($javascript['content']));
                        }
                    }
                } else {
                    $minifier->add($javascript['content']);
                    $content = $minifier->minify();
                }
                if (!Helper::endsWith($content, ';')) {
                    $content .= ';';
                }

                Helper::putContents($jsFile, $content, true);
                Helper::putContents($jsFile, "\n", true);
            }
        } else {
            Framework::getReporter('Logs')->add('Getting Minified JS <code>' . (str_replace(JPATH_SITE . '/', '', $jsFile)) . '</code>.');
        }

        $html = Helper::str_lreplace('</head>', '<script src="' . 'cache/astroid/js/' . $version . '.js?' . Helper::joomlaMediaVersion() . '"></script></head>', $html);
        Framework::getDebugger()->log('Minifying JS');
        return $html;
    }

    public function minifyHTML($html)
    {
        Framework::getDebugger()->log('Minifying HTML');
        $level = Framework::getTemplate()->getParams()->get('minify_html_level', 'basic');

        $x  = '<!--(?>-?[^-]*+)*?--!?>';
        $s1 = '"(?>(?:\\\\.)?[^\\\\"]*+)+?(?:"|(?=$))';
        $s2 = "'(?>(?:\\\\.)?[^\\\\']*+)+?(?:'|(?=$))";
        $a = '[^\s/"\'=<>]*+(?:\s*=(?>\s*+"[^">]*+"|\s*+\'[^\'>]*+\'|[^\s>]*+[\s>]))?';
        $pr = "<pre\b[^>]*+>(?><?[^<]*+)*?</pre\s*+>";
        $sc = "<script\b[^>]*+>(?><?[^<]*+)*?</script\s*+>";
        $st = "<style\b[^>]*+>(?><?[^<]*+)*?</style\s*+>";
        $tx = "<textarea\b[^>]*+>(?><?[^<]*+)*?</textarea\s*+>";

        //Remove comments (not containing IE conditional comments)
        $rx = "#(?><?[^<]*+(?>$pr|$sc|$st|$tx|<!--\[(?><?[^<]*+)*?"
            . "<!\s*\[(?>-?[^-]*+)*?--!?>|<!DOCTYPE[^>]++>)?)*?\K(?:$x|$)#i";
        $html = $this->_replace($rx, '', $html);

        //Reduce runs of whitespace outside all elements to one
        $rx = "#(?>[^<]*+(?:$pr|$sc|$st|$tx|$x|<(?>[^>=]*+(?:=\s*+(?:$s1|$s2|['\"])?|(?=>)))*?>)?)*?\K"
            . '(?:[\t\f ]++(?=[\r\n]\s*+<)|(?>\r?\n|\r)\K\s++(?=<)|[\t\f]++(?=[ ]\s*+<)|[\t\f]\K\s*+(?=<)|[ ]\K\s*+(?=<)|$)#i';
        $html = $this->_replace($rx, '', $html);


        //Minify scripts
        //invalid scripts
        $nsc = "<script\b(?=(?>\s*+$a)*?\s*+type\s*+=\s*+(?![\"']?(?:text|application)/(?:javascript|[^'\"\s>]*?json)))[^<>]*+>(?><?[^<]*+)*?</\s*+script\s*+>";
        //invalid styles
        $nst = "<style\b(?=(?>\s*+$a)*?\s*+type\s*+=\s*+(?![\"']?(?:text|(?:css|stylesheet))))[^<>]*+>(?><?[^<]*+)*?</\s*+style\s*>";
        $rx          = "#(?><?[^<]*+(?:$x|$nsc|$nst)?)*?\K"
            . "(?:(<script\b(?!(?>\s*+$a)*?\s*+type\s*+=\s*+(?![\"']?(?:text|application)/(?:javascript|[^'\"\s>]*?json)))[^<>]*+>)((?><?[^<]*+)*?)(</\s*+script\s*+>)|"
            . "(<style\b(?!(?>\s*+$a)*?\s*+type\s*+=\s*+(?![\"']?text/(?:css|stylesheet)))[^<>]*+>)((?><?[^<]*+)*?)(</\s*+style\s*+>)|$)#i";
        $html = $this->_replace($rx, '', $html, array($this, '_minifyAssets'));

        //Replace line feed with space (legacy)
        $rx = "#(?>[^<]*+(?:$pr|$sc|$st|$tx|$x|<(?>[^>=]*+(?:=\s*+(?:$s1|$s2|['\"])?|(?=>)))*?>)?)*?\K"
            . '(?:[\r\n\t\f]++(?=<)|$)#i';
        $html = $this->_replace($rx, ' ', $html);

        // remove ws around block elements preserving space around inline elements
        //block/undisplayed elements
        $b = 'address|article|aside|audio|body|blockquote|canvas|dd|div|dl'
            . '|fieldset|figcaption|figure|footer|form|h[1-6]|head|header|hgroup|html|noscript|ol|output|p'
            . '|pre|section|style|table|title|tfoot|ul|video';

        //self closing block/undisplayed elements
        $b2 = 'base|meta|link|hr';

        //inline elements
        $i = 'b|big|i|small|tt'
            . '|abbr|acronym|cite|code|dfn|em|kbd|strong|samp|var'
            . '|a|bdo|br|map|object|q|script|span|sub|sup'
            . '|button|label|select|textarea';

        //self closing inline elements
        $i2 = 'img|input';

        $rx = "#(?>\s*+(?:$pr|$sc|$st|$tx|$x|<(?:(?>$i)\b[^>]*+>|(?:/(?>$i)\b>|(?>$i2)\b[^>]*+>)\s*+)|<[^>]*+>)|[^<]++)*?\K"
            . "(?:\s++(?=<(?>$b|$b2)\b)|(?:</(?>$b)\b>|<(?>$b2)\b[^>]*+>)\K\s++(?!<(?>$i|$i2)\b)|$)#i";
        $html = $this->_replace($rx, '', $html);

        if ($level == 'basic') {
            Framework::getDebugger()->log('Minifying HTML');
            return $html;
        }

        //Replace runs of whitespace inside elements with single space escaping pre, textarea, scripts and style elements
        //elements to escape
        $e = 'pre|script|style|textarea';

        $rx = "#(?>[^<]*+(?:$pr|$sc|$st|$tx|$x|<[^>]++>[^<]*+))*?(?:(?:<(?!$e|!)[^>]*+>)?(?>\s?[^\s<]*+)*?\K\s{2,}|\K$)#i";
        $html = $this->_replace($rx, ' ', $html);

        //remove redundant attributes
        $nsc = "<script\b(?=(?>\s*+$a)*?\s*+type\s*+=\s*+(?![\"']?(?:text|application)/(?:javascript|[^'\"\s>]*?json)))[^<>]*+>(?><?[^<]*+)*?</\s*+script\s*+>";
        $nst = "<style\b(?=(?>\s*+$a)*?\s*+type\s*+=\s*+(?![\"']?(?:text|(?:css|stylesheet))))[^<>]*+>(?><?[^<]*+)*?</\s*+style\s*>";

        //Remove quotes from selected attributes
        $ns1 = '"[^"\'`=<>\s]*+(?:[\'`=<>\s]|(?<=\\\\)")(?>(?:(?<=\\\\)")?[^"]*+)*?(?<!\\\\)"';
        $ns2 = "'[^'\"`=<>\s]*+(?:[\"`=<>\s]|(?<=\\\\)')(?>(?:(?<=\\\\)')?[^']*+)*?(?<!\\\\)'";

        $j = '<input type="hidden" name="[0-9a-f]{32}" value="1" />';
        $rx = "#(?:(?=[^>]*+>)|<[a-z0-9]++ )"
            . "(?>[=]?[^=><]*+(?:=(?:$ns1|$ns2)|>(?>[^<]*+(?:$j|$x|$nsc|$nst|<(?![a-z0-9]++ ))?)*?(?:<[a-z0-9]++ |$))?)*?"
            . "(?:=\K([\"'])([^\"'`=<>\s]++)\g{1}[ ]?|\K$)#i";
//        $html = $this->_replace($rx, '$2 ', $html);

        //Remove last whitespace in open tag
        $rx = "#(?>[^<]*+(?:$j|$x|$nsc|$nst|<(?![a-z0-9]++))?)*?(?:<[a-z0-9]++(?>\s*+[^\s>]++)*?\K" . "(?:\s*+(?=>)|(?<=[\"'])\s++(?=/>))|$\K)#i";
        $html = $this->_replace($rx, '', $html);
        Framework::getDebugger()->log('Minifying HTML');
        return trim($html);
    }

    public function _replace($rx, $replacement, $code, $callback = null)
    {
        if ($callback === null) {
            return preg_replace($rx, $replacement, $code);
        } else {
            return preg_replace_callback($rx, $callback, $code);
        }
    }

    public function _minifyAssets($m)
    {
        if ($m[0] == '') {
            return $m[0];
        }

        $openTag  = isset($m[1]) && $m[1] != '' ? $m[1] : (isset($m[4]) && $m[4] != '' ? $m[4] : '');
        $content  = isset($m[2]) && $m[2] != '' ? $m[2] : (isset($m[5]) && $m[5] != '' ? $m[5] : '');
        $closeTag = isset($m[3]) && $m[3] != '' ? $m[3] : (isset($m[6]) && $m[6] != '' ? $m[6] : '');

        if (trim($content) == '') {
            return $m[0];
        }

        $type = stripos($openTag, 'script') == 1 ? (stripos($openTag, 'json') !== false ? 'json' : 'js') : 'css';

        if ($type == 'css') {
            $minifier = new Minify\CSS();
            $minifier->add($content);
            return "{$openTag}{$minifier->minify()}{$closeTag}";
        }
        if ($type == 'js') {
            $minifier = new Minify\JS();
            $minifier->add($content);
            return "{$openTag}{$minifier->minify()}{$closeTag}";
        }

        return $m[0];
    }

    private function _assetsHash()
    {
        $assets = [];
        $assets[] = \json_encode($this->_styles);
        $assets[] = \json_encode($this->_stylesheets);
        $assets[] = \json_encode($this->_javascripts);
        $assets[] = \json_encode($this->_scripts);
        $assets[] = \json_encode($this->_customtags);
        $assets[] = \json_encode($this->_metas);
        $assets[] = self::$_fontawesome;
        $assets[] = Factory::getDocument()->getHeadData();
        return md5(serialize($assets));
    }

    public function position($position, $style = 'none')
    {
        if (empty($position)) {
            return '';
        }
        $return = '';
        $beforeContent = $this->_positionContent($position, 'before');
        if (!empty($beforeContent)) {
            $return .= $beforeContent;
        }
        $return .= $this->_position($position, $style);
        $afterContent = $this->_positionContent($position, 'after');
        if (!empty($afterContent)) {
            $return .= $afterContent;
        }
        return $return;
    }

    public function hasModule($position, $module)
    {
        return in_array($module, array_column(ModuleHelper::getModules($position), 'module'));
    }

    public function loadModule($content)
    {
        // Expression to search for(module Position)
        $regex = '/{loadposition\s(.*?)}/i';
        preg_match_all($regex, $content, $matches, PREG_SET_ORDER);
        if ($matches) {
            foreach ($matches as $match) {
                $matcheslist = explode(',', $match[1]);
                $position = trim($matcheslist[0]);
                $output = $this->_modulePosition($position);
                // We should replace only first occurrence in order to allow positions with the same name to regenerate their content:
                $content = preg_replace("|$match[0]|", $output, $content, 1);
            }
        }
        // Expression to search for(id)
        $regexmodid = '/{loadmoduleid\s([1-9][0-9]*)}/i';
        preg_match_all($regexmodid, $content, $matchesmodid, PREG_SET_ORDER);
        // If no matches, skip this
        if ($matchesmodid) {
            foreach ($matchesmodid as $match) {
                $id = trim($match[1]);
                $output = $this->_moduleId($id);

                // We should replace only first occurrence in order to allow positions with the same name to regenerate their content:
                $content = preg_replace("|$match[0]|", $output, $content, 1);
            }
        }
        return $content;
    }

    private function _modulePosition($position)
    {
        $this->modules[$position] = '';
        $document = Factory::getDocument();
        $renderer = $document->loadRenderer('module');
        $modules = ModuleHelper::getModules($position);
        ob_start();

        foreach ($modules as $module) {
            echo $renderer->render($module);
        }

        $this->modules[$position] = ob_get_clean();

        return $this->modules[$position];
    }

    private function _moduleId($id)
    {
        $this->modules[$id] = '';
        $document = Factory::getDocument();
        $renderer = $document->loadRenderer('module');
        $modules = ModuleHelper::getModuleById($id);
        ob_start();

        if ($modules->id > 0) {
            echo $renderer->render($modules);
        }

        $this->modules[$id] = ob_get_clean();

        return $this->modules[$id];
    }

    private function _position($position, $style)
    {
        if (empty($position)) {
            return '';
        }
        $return = '';
        $modules = ModuleHelper::getModules($position);
        if (count($modules)) {
            $return .= '<jdoc:include type="modules" name="' . $position . '" style="' . $style . '" />';
        }
        return $return;
    }

    public function _positionContent($position, $load = 'after')
    {
        $contents = $this->_positionLayouts();
        $return = '';
        if (isset($contents[$position]) && !empty($contents[$position])) {
            foreach ($contents[$position] as $layout) {
                $layout = explode(':', $layout);
                if ($layout[1] == $load) {
                    $return .= $this->include($layout[0], [], true);
                }
            }
        }
        return $return;
    }

    private function _positionLayouts()
    {
        $params = Framework::getTemplate()->getParams();
        $astroidcontentlayouts = $params->get('astroidcontentlayouts', 'social:astroid-top-social:after,contactinfo:astroid-top-contact:after');
        $return = [];
        if (!empty($astroidcontentlayouts)) {
            $astroidcontentlayouts = explode(',', $astroidcontentlayouts);
            foreach ($astroidcontentlayouts as $astroidcontentlayout) {
                $astroidcontentlayout = explode(':', $astroidcontentlayout);
                if (isset($return[$astroidcontentlayout[1]])) {
                    $return[$astroidcontentlayout[1]][] = $astroidcontentlayout[0] . ':' . $astroidcontentlayout[2];
                } else {
                    $return[$astroidcontentlayout[1]] = [];
                    $return[$astroidcontentlayout[1]][] = $astroidcontentlayout[0] . ':' . $astroidcontentlayout[2];
                }
            }
        }
        return $return;
    }

    protected function checkDev()
    {
        $params = Framework::getTemplate()->getParams();
        if ($params->exists('developemnt_mode')) {
            $dev = $params->get('developemnt_mode', 0);
        } else {
            $dev = $params->get('development_mode', 0);
        }
        $this->_dev = ($dev ? true : false);
    }

    public function isDev()
    {
        if ($this->_dev === null) {
            $this->checkDev();
        }
        return $this->_dev;
    }

    public function addMeta($name, $content, $attribs = [])
    {
        $this->_metas[$name] = [
            'name' => $name,
            'content' => $content,
            'attribs' => $attribs
        ];
    }

    public function addLink($href = '', $rel = 'stylesheet', $attribs = ['type' => 'text/css'])
    {
        $this->_links[md5($href)] = [
            'href' => $href,
            'rel' => $rel,
            'attribs' => $attribs
        ];
    }

    public function renderMeta()
    {
        $html = '';
        foreach ($this->_metas as $meta) {
            $html .= '<meta';
            if (!empty($meta['name'])) {
                $html .= ' name="' . $meta['name'] . '"';
            }
            foreach ($meta['attribs'] as $attribProp => $attribVal) {
                $html .= ' ' . $attribProp . '="' . $attribVal . '"';
            }
            if (!empty($meta['content'])) {
                $html .= ' content="' . $meta['content'] . '"';
            }
            $html .= ' />';
        }
        return $html;
    }

    public function renderLinks()
    {
        $html = '';
        foreach ($this->_links as $link) {
            $html .= '<link';
            if (!empty($link['href'])) {
                $html .= ' href="' . $this->_systemUrl($link['href']) . '"';
            }
            if (!empty($link['rel'])) {
                $html .= ' rel="' . $link['rel'] . '"';
            }
            foreach ($link['attribs'] as $attribProp => $attribVal) {
                $html .= ' ' . $attribProp . '="' . $attribVal . '"';
            }
            $html .= ' />';
        }
        return $html;
    }

    public function beutifyURL($url)
    {
        $url = str_replace('?' . Helper::joomlaMediaVersion(), '', $url);
        $url = str_replace(Uri::root(), '', $url);
        $url = str_replace(JPATH_SITE . '/', '', $url);
        if (substr($url, 0, 1) == '/' && substr($url, 0, 2) != '//') {
            $url = substr($url, 1);
        }
        if (substr($url, 0, 7) == '@import') {
            $url = substr($url, 8);
            $url = substr($url, -1);
        }
        return $url;
    }

    public function addScript($url, $position = 'head', $options = [], $attribs = [], $type = '')
    {
        if (!is_array($url)) {
            $url = [$url];
        }
        foreach ($url as $u) {
            if (!empty(trim($u))) {
                $script = [];
                $script['url'] = $u;
                $script['attribs'] = $attribs;
                $script['options'] = $options;
                $script['type'] = $type;
                $this->_javascripts[$position][md5(serialize($script))] = $script;
            }
        }
    }

    public function getScripts($position = 'head')
    {
        $html = '';
        foreach ($this->_javascripts[$position] as $javascript) {
            $html .= '<script src="' . $this->_systemUrl($javascript['url']) . '"'.(isset($javascript['type']) && $javascript['type'] ? ' type="'.$javascript['type'].'"' : '').'></script>';
        }
        foreach ($this->_scripts[$position] as $script) {
            $html .= '<script type="' . $script['type'] . '">' . $script['content'] . '</script>';
        }
        return $html;
    }

    public function getCustomTags($position = 'head')
    {
        $content = '';
        foreach ($this->_customtags[$position] as $tag) {
            $content .= $tag;
        }
        return $content;
    }

    public function addScriptOptions($key, $options, $merge = true)
    {
        if (empty($this->scriptOptions[$key])) {
            $this->scriptOptions[$key] = [];
        }

        if ($merge && \is_array($options)) {
            $this->scriptOptions[$key] = array_replace_recursive($this->scriptOptions[$key], $options);
        } else {
            $this->scriptOptions[$key] = $options;
        }

        return $this;
    }

    public function getScriptOptions($key = null)
    {
        if ($key) {
            return (empty($this->scriptOptions[$key])) ? [] : $this->scriptOptions[$key];
        }

        return $this->scriptOptions;
    }

    protected function _systemUrl($url, $version = true)
    {
        $config = Factory::getApplication()->getConfig();
        $params = Helper::getPluginParams();
        if ($config->get('debug', 0) || $params->get('astroid_debug', 0)) {
            $postfix = $version ? '?' . Helper::joomlaMediaVersion() : '';
        } else {
            $postfix = $version ? '?v=' . Helper::frameworkVersion() : '';
        }

        if (Framework::isAdmin()) {
            $root = Uri::root();
        } else {
            $root = Uri::root(true). '/';
            $postfix = '';
        }

        if (file_exists(ASTROID_MEDIA . '/' . $url)) {
            $url = $root . 'media/astroid/assets/' . $url;
        } elseif (Framework::isSite() && file_exists(ASTROID_MEDIA_TEMPLATE_PATH . '/' . $url)) {
            $url = $root . 'media/templates/site/' . Framework::getTemplate()->template . '/' . $url;
        } elseif (Framework::isSite() && file_exists(ASTROID_TEMPLATE_PATH . '/' . $url)) {
            $url = $root . 'templates/' . Framework::getTemplate()->template . '/' . $url;
        } else if (file_exists(JPATH_SITE . '/' . $url)) {
            $url = $root . $url;
        } else {
            $postfix = '';
        }
        return $url.$postfix ;
    }

    public function addScriptDeclaration($content, $position = 'head', $type = 'text/javascript')
    {
        if (empty($content)) {
            return;
        }
        $script = [];
        $script['content'] = $content;
        $script['type'] = $type;
        $this->_scripts[$position][] = $script;
    }

    public function addStyleDeclaration($content, $device = 'desktop')
    {
        if (empty($content)) {
            return;
        }
        $this->_styles[$device][] = trim($content);
    }

    public function addStyleSheet($url, $attribs = ['rel' => 'stylesheet', 'type' => 'text/css'], $shifted = 0)
    {
        if (!is_array($url)) {
            $url = [$url];
        }
        if (!isset($attribs['rel'])) {
            $attribs['rel'] = 'stylesheet';
        }
        if (!isset($attribs['type'])) {
            $attribs['type'] = 'text/css';
        }
        foreach ($url as $u) {
            if (!empty(trim($u))) {
                $stylesheet = ['url' => $u, 'attribs' => $attribs, 'shifted' => $shifted];
                $this->_stylesheets[md5($u)] = $stylesheet;
            }
        }
    }

    public function addCustomTag($content, $position = 'head')
    {
        if (empty($content)) {
            return;
        }
        $content = trim($content);
        if (!in_array($content, $this->_customtags[$position])) {
            $this->_customtags[$position][] = trim($content);
        }
    }

    public function loadFontAwesome()
    {
        if (self::$_fontawesome) {
            return;
        }
        Helper\Font::loadFontAwesome();
    }

    public function loadFancyBox()
    {
        if (self::$_fancybox) {
            return;
        }
        $wa = Factory::getApplication()->getDocument()->getWebAssetManager();
        $wa->registerAndUseStyle('fancybox', "https://cdn.jsdelivr.net/npm/@fancyapps/ui@".Constants::$fancybox_version."/dist/fancybox/fancybox.css");
        $wa->registerAndUseScript('fancybox', 'https://cdn.jsdelivr.net/npm/@fancyapps/ui@'.Constants::$fancybox_version.'/dist/fancybox/fancybox.umd.js');
        self::$_fancybox = true;
    }

    public function moveFile(&$array, $a, $b)
    {
        $out = array_splice($array, $a, 1);
        array_splice($array, $b, 0, $out);
    }

    public function getStylesheets()
    {
        $keys = array_keys($this->_stylesheets);
        foreach ($keys as $index => $key) {
            if ($this->_stylesheets[$key]['shifted']) {
                $newindex = $index + (int) $this->_stylesheets[$key]['shifted'];
                $this->moveFile($keys, $index, $newindex);
            }
        }
        $content = '';
        foreach ($keys as $key) {
            $stylesheet = $this->_stylesheets[$key];
            $content .= '<link href="' . $this->_systemUrl($stylesheet['url']) . '"';
            foreach ($stylesheet['attribs'] as $prop => $value) {
                $content .= ' ' . $prop . '="' . $value . '"';
            }
            $content .= ' />' . "\n";
        }
        return $content;
    }

    public function renderScss($path)
    {
        ini_set('memory_limit', '1024M');
        Framework::getDebugger()->log('Rendering Scss');
        $template = Framework::getTemplate();

        Helper::clearCache($template->template, ['compiled-scss']);
        $isChildTemplate        =   Helper::isChildTemplate($template->template);

        $parentTemplateMediaPath        =   '';
        $parentTemplateMediaScssPath    =   '';
        $isChild                        =   $isChildTemplate && isset($isChildTemplate['isChild']) && $isChildTemplate['isChild'];
        if ($isChild) {
            $parentTemplateMediaPath        =   './../../../../../media/templates/site/' . $isChildTemplate['parent'];
            $parentTemplateMediaScssPath    =   $parentTemplateMediaPath . '/scss';
        }

        $templatePath           =   './../../../../../templates/' . $template->template;
        $templateScssPath       =   $templatePath . '/scss';
        $templateMediaPath      =   './../../../../../media/templates/site/' . $template->template;
        $templateMediaScssPath  =   $templateMediaPath . '/scss';

        $scss = new Compiler();
        if (file_exists(__DIR__ . '/' . $templateMediaPath . '/scss')) {
            $scss->setImportPaths(__DIR__ . '/' . $templateMediaPath . '/scss');
            $mediaPath              = '../../../../astroid/assets';
        } else {
            $scss->setImportPaths(__DIR__ . '/' . $templatePath . '/scss');
            $mediaPath              = '../../../media/astroid/assets';
        }

        $bootstrapPath = $mediaPath . '/vendor/bootstrap/scss';

        if (file_exists(__DIR__ . '/' . $templateMediaScssPath . '/vendor/bootstrap') || file_exists(__DIR__ . '/' . $templateScssPath . '/vendor/bootstrap')) {
            $bootstrapPath = 'vendor/bootstrap';
        }
        elseif ($isChild && file_exists(__DIR__ . '/' . $parentTemplateMediaScssPath . '/vendor/bootstrap'))
        {
            $bootstrapPath = '../../'.$isChildTemplate['parent'].'/scss/vendor/bootstrap';
        }
        $content = '';
        $functionsIncluded = false;
        if (file_exists(__DIR__ . '/' . $templateMediaScssPath . '/custom/variable_overrides.scss') || file_exists(__DIR__ . '/' . $templateScssPath . '/custom/variable_overrides.scss')) {
            $functionsIncluded = true;
            $content .= '@import "' . $bootstrapPath . '/functions";';
            $content .= '@import "custom/variable_overrides";';
        }
        elseif ($isChild && file_exists(__DIR__ . '/' . $parentTemplateMediaScssPath . '/custom/variable_overrides.scss'))
        {
            $functionsIncluded = true;
            $content .= '@import "' . $bootstrapPath . '/functions";';
            $content .= '@import "../../' . $isChildTemplate['parent'] . '/scss/custom/variable_overrides";';
        }

        if (file_exists(__DIR__ . '/' . $templateMediaScssPath . '/variable_overrides.scss') || file_exists(__DIR__ . '/' . $templateScssPath . '/variable_overrides.scss')) {
            if (!$functionsIncluded) {
                $content .= '@import "' . $bootstrapPath . '/functions";';
            }
            $content .= '@import "variable_overrides";';
        }
        elseif ($isChild && file_exists(__DIR__ . '/' . $parentTemplateMediaScssPath . '/variable_overrides.scss'))
        {
            if (!$functionsIncluded) {
                $content .= '@import "' . $bootstrapPath . '/functions";';
            }
            $content .= '@import "../../' . $isChildTemplate['parent'] . '/scss/variable_overrides";';
        }
        $content .= '@import "' . $bootstrapPath . '/bootstrap";';

        $content .= '@import "' . $mediaPath . '/vendor/astroid/scss/astroid";';

        if (file_exists(__DIR__ . '/' . $templateMediaScssPath . '/style.scss') || file_exists(__DIR__ . '/' . $templateScssPath . '/style.scss')) {
            $content .= '@import "style";';
        }
        elseif ($isChild && file_exists(__DIR__ . '/' . $parentTemplateMediaScssPath . '/style.scss'))
        {
            $content .= '@import "../../' . $isChildTemplate['parent'] . '/scss/style";';
        }

        if (file_exists(__DIR__ . '/' . $templateMediaScssPath . '/custom/custom.scss') || file_exists(__DIR__ . '/' . $templateScssPath . '/custom/custom.scss')) {
            $content .= '@import "custom/custom";';
        }
        elseif ($isChild && file_exists(__DIR__ . '/' . $parentTemplateMediaScssPath . '/custom/custom.scss'))
        {
            $content .= '@import "../../' . $isChildTemplate['parent'] . '/scss/custom/custom";';
        }

        $variables = $template->getThemeVariables();
        if (!empty($variables)) {
            $plugin_params  =   Helper::getPluginParams();
            $color_mode     =   $plugin_params->get('astroid_color_mode_enable', 0);
            $color_mode_light   =   '';
            $color_mode_dark    =   '';
            foreach ($variables as $key => $variable) {
                $result = json_decode($variable);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $value_light    =   $result->light;
                    $value_dark     =   $result->dark;
                    if (!empty($value_light)) {
                        $variables[$key]    =  $value_light;
                    } else {
                        unset($variables[$key]);
                    }
                    if (!empty($value_dark)) {
                        $color_mode_dark    .=  '--bs-'.$key.':'.$value_dark.';';
                    }
                } else {
                    if (strpos($key, '[light]')) {
                        $color_mode_light   .=  substr($key, 0, strpos($key, '[light]')). ':' . $variable . ';';
                    } elseif (strpos($key, '[dark]')) {
                        $color_mode_dark    .=  substr($key, 0, strpos($key, '[dark]')). ':' . $variable . ';';
                    }
                }
            }
            $scss->addVariables($variables);
            if ($color_mode && $color_mode_light) {
                $content    .=  '@include color-mode(light) {'. $color_mode_light .'}';
            }
            if ($color_mode && $color_mode_dark) {
                $content    .=  '@include color-mode(dark) {'. $color_mode_dark .'}';
            }
        }

        $css = $scss->compileString($content);
        Framework::getDebugger()->log('Rendering Scss');
        Helper::putContents($path, $css->getCss());
    }

    public function renderCss()
    {
        /* if (Framework::isSite()) {
            $template = Framework::getTemplate();
            Helper::clearCache($template->template, ['compiled-css']);
        } */
        $cssScript = '';
        foreach ($this->_styles as $device => $css) {
            if ($device == 'mobile') {
                $cssScript .= '@media (max-width: 767.98px) {' . implode('', $this->_styles[$device]) . '}';
            } elseif ($device == 'tablet') {
                $cssScript .= '@media (max-width: 991.98px) {' . implode('', $this->_styles[$device]) . '}';
            } else {
                $cssScript .= implode('', $this->_styles[$device]);
            }
        }
        return $cssScript;
    }

    public function getBodyClass($extra_class = '')
    {
        $template = Framework::getTemplate();

        $params = $template->getParams();
        $app = Factory::getApplication();
        $menu = $app->getMenu()->getActive();

        $class = [];
        $class[] = "site";
        $class[] = "astroid-framework";

        $option = $app->input->get('option', '', 'STRING');
        $view = $app->input->get('view', '', 'STRING');
        $layout = $app->input->get('layout', 'default', 'ALNUM');
        $task = $app->input->get('task', '', 'ALNUM');
        $header = $params->get('header', TRUE);
        $headerMode = $params->get('header_mode', 'horizontal', 'STRING');
        $Itemid = $app->input->get('Itemid', '', 'INT');

        if (!empty($option)) {
            $class[] = htmlspecialchars(str_replace('_', '-', $option));
        }
        if (!empty($view)) {
            $class[] = 'view-' . $view;
        }
        if (!empty($layout)) {
            $class[] = 'layout-' . $layout;
        }
        if (!empty($task)) {
            $class[] = 'task-' . $task;
        }
        if (!empty($Itemid)) {
            $class[] = 'itemid-' . $Itemid;
        }

        if ($header && !empty($headerMode) && $headerMode == 'sidebar') {
            $sidebarDirection = $params->get('header_sidebar_menu_mode', 'left');
            $class[] = "header-sidebar-" . $sidebarDirection;
        }

        if (isset($menu) && $menu) {
            $menu_params = $menu->getParams();
            if ($menu_params->get('pageclass_sfx')) {
                $class[] = $menu_params->get('pageclass_sfx');
            }
            if ($menu->alias) {
                $class[] = $menu->alias . '-alias';
            }
        }
        if (!empty($template->id)) {
            $class[] = 'tp-style-' . $template->id;
        }

        $class[] = $template->language;
        $class[] = $template->direction;

        if (!empty($extra_class) && !is_array($extra_class)) {
            $extra_class = [$extra_class];
        }

        if (!empty($extra_class)) {
            foreach ($extra_class as $ext_class) {
                $class[] = $ext_class;
            }
        }

        return implode(' ', $class);
    }

    public function isBuilder()
    {
        $jinput = Factory::getApplication()->input;
        $option = $jinput->get('option', '');
        $view = $jinput->get('view', '');
        return (($option == "com_sppagebuilder" && $view == "page") || ($option == "com_quix" && $view == "page") || ($option == "com_jdbuilder" && $view == "page"));
    }

    public static function getDir($dir, $extension = null, &$results = array())
    {
        if (!file_exists($dir)) return $results;
        $files = scandir($dir);

        foreach ($files as $key => $value) {
            $path = realpath($dir . DIRECTORY_SEPARATOR . $value);
            if (!is_dir($path)) {
                $pathinfo = pathinfo($path);
                if ($extension !== null && $pathinfo['extension'] == $extension) {
                    $include_path = str_replace(JPATH_THEMES, '', $path);
                    $component_name = str_replace('.min', '', $pathinfo['filename']);
                    $results[$component_name] = ['component_name' => $component_name, 'name' => $pathinfo['basename'], 'path' => $include_path, 'basepath' => $path];
                } elseif ($extension === null) {
                    $include_path = str_replace(JPATH_THEMES, '', $path);
                    $results[] = ['name' => $pathinfo['basename'], 'path' => $include_path];
                }
            } else if ($value != "." && $value != "..") {
                self::getDir($path, $extension, $results);
            }
        }

        return $results;
    }

    public static function scssHash()
    {
        $params = Helper::getPluginParams();
        $debug = $params->get('astroid_debug', 0);
        if (!$debug) {
            return '';
        }
        Framework::getDebugger()->log('Checking Scss');
        $template = Framework::getTemplate();
        if ( file_exists(JPATH_SITE . '/media/templates/site/' . $template->template . '/scss') ) {
            $scss_dir   =   JPATH_SITE . '/media/templates/site/' . $template->template . '/scss';
        } elseif ( file_exists(JPATH_SITE . '/templates/' . $template->template . '/scss')) {
            $scss_dir   =   JPATH_SITE . '/templates/' . $template->template . '/scss';
        } else {
            return '';
        }
        $scss_files = self::getDir($scss_dir, 'scss');
        if (!is_array($scss_files) || !count($scss_files)) return '';

        $name = '';
        foreach ($scss_files as $scss) {
            $name .= md5_file($scss['basepath']);
        }
        Framework::getDebugger()->log('Checking Scss');
        return md5($name);
    }

    public function astroidCSS()
    {
        $getPluginParams = Helper::getPluginParams();
        $wa = Factory::getApplication()->getDocument()->getWebAssetManager();
        // Scss
        if (Framework::isSite()) {
            $template = Framework::getTemplate();

            // scss compile version
            $scssVersion = md5(serialize($template->getThemeVariables())  . self::scssHash());

            // css file to be generated in template folder
            $cssFile = ASTROID_MEDIA_TEMPLATE_PATH . '/css/compiled-' .  $scssVersion . '.css';

            // $scssFile = ASTROID_CACHE . '/compiled/' . $template->id . '-' . $scssVersion . '.css';

            if (!file_exists($cssFile)) {
                // rendering scss
                Framework::getReporter('Logs')->add('Rendering Scss');
                // clearing previous versions
                Helper::clearCache($template->template, ['compiled-' . $scssVersion]);
                // adding compiled scss in css file
                $this->renderScss($cssFile);
                if ($template->isDefault()) {
                    Helper::clearCache($template->template, ['template']);
                    File::copy($cssFile, ASTROID_MEDIA_TEMPLATE_PATH . '/css/template.css');
                }
            } else {
                // logging compiled scss
                Framework::getReporter('Logs')->add('Getting SCSS Compiled CSS <code>' . str_replace(JPATH_SITE . '/', '', $cssFile) . '</code> from cache.');
            }
            // adding compiled scss

//            $wa->registerAndUseStyle('astroid.template.'.$template->template, 'media/templates/site/'.$template->template.'/css/compiled-' . $scssVersion . '.css');
            $this->addStyleSheet('css/compiled-' . $scssVersion . '.css');
        }

        if ($getPluginParams->get('astroid_debug', 0)) {
            $wa->registerAndUseStyle('astroid.debug', 'astroid/debug.css');
        }
    }
    public function astroidCustomCSS() {
        // css on page
        $getPluginParams = Helper::getPluginParams();
        $astroid_inline_css    =   $getPluginParams->get('astroid_inline_css', 0);

        if (Framework::isSite()) {
            $css = $this->renderCss();
            if (!$astroid_inline_css) {
                // page css
                $pageCSSHash = md5($css);
                $pageCSS = ASTROID_MEDIA_TEMPLATE_PATH . '/css/compiled-' . $pageCSSHash . '.css';
                if (!file_exists($pageCSS)) {
                    Helper::putContents($pageCSS, $css);
                }
                $this->addStyleSheet('css/compiled-' . $pageCSSHash . '.css');
                // custom css
                if (file_exists(ASTROID_MEDIA_TEMPLATE_PATH . '/css/custom.css') || file_exists(ASTROID_TEMPLATE_PATH . '/css/custom.css')) {
                    $this->addStyleSheet('css/custom.css');
                }
                return '';
            } else {
                // custom css
                if (file_exists(ASTROID_MEDIA_TEMPLATE_PATH . '/css/custom.css') || file_exists(ASTROID_TEMPLATE_PATH . '/css/custom.css')) {
                    $this->addStyleSheet('css/custom.css');
                }
                return '<style>'.$css.'</style>';
            }
        }
    }

    public function astroidInlineCSS() {
        // css on page
        $getPluginParams = Helper::getPluginParams();
        $astroid_inline_css    =   $getPluginParams->get('astroid_inline_css', 0);
        if (Framework::isSite() && $astroid_inline_css) {
            $css = $this->renderCss();
            return '<style>'.$css.'</style>';
        }
        return '';
    }
}
