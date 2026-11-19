<?php
/**
 * @package   Astroid Framework
 * @author    Astroid Framework Team https://astroidframe.work
 * @copyright Copyright (C) 2026 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3 or Later
 * DO NOT MODIFY THIS FILE DIRECTLY AS IT WILL BE OVERWRITTEN IN THE NEXT UPDATE
 * You can easily override all files under /astroid/ folder.
 * Just copy the file to JROOT/media/templates/site/{YOUR_TEMPLATE_NAME}/astroid/elements/svg/svg.php folder to create and override
 */

use Astroid\Helper\Media;
use Astroid\Helper\Style;
use enshrined\svgSanitize\Sanitizer;
use Joomla\CMS\Factory;

defined('_JEXEC') or die;
extract($displayData);
$svg = $params->get('svg', '');

if ($svg === '') {
    return;
}

$mediaPath = realpath(JPATH_ROOT . '/' . Media::getPath());
$svgPath = $mediaPath !== false && is_string($svg)
    ? realpath($mediaPath . '/' . $svg)
    : false;

if ($svgPath === false
    || strpos($svgPath, $mediaPath . DIRECTORY_SEPARATOR) !== 0
    || strtolower(pathinfo($svgPath, PATHINFO_EXTENSION)) !== 'svg'
    || !is_file($svgPath)
    || !is_readable($svgPath)
) {
    Factory::getApplication()->enqueueMessage('Astroid SVG: select a readable SVG file inside the media directory.', 'warning');
    return;
}

$content = file_get_contents($svgPath);

if ($content === false) {
    Factory::getApplication()->enqueueMessage('Astroid SVG: unable to read the selected file.', 'warning');
    return;
}

$sanitizer = new Sanitizer();
$sanitizer->removeRemoteReferences(true);
$sanitizer->removeXMLTag(true);
try {
    $content = $sanitizer->sanitize($content);
} catch (\LogicException $exception) {
    Factory::getApplication()->enqueueMessage('Astroid SVG: the selected file does not contain a valid SVG.', 'warning');
    return;
}

if ($content === false || !preg_match('/^\s*<svg(?:\s|>)/i', $content)) {
    Factory::getApplication()->enqueueMessage('Astroid SVG: the selected file does not contain a valid SVG.', 'warning');
    return;
}

echo '<div class="as-svg-wrapper">' . $content . '</div>';

$svg_style = $element->style->child('.as-svg-wrapper > svg');
$svg_width = $params->get('svg_width', '160');
$svg_width_data = \json_decode($svg_width, true);
$svg_style->addResponsiveCSS('width', $svg_width, $svg_width_data['postfix'] ?? 'px');

$svg_height = $params->get('svg_height', '160');
$svg_height_data = \json_decode($svg_height, true);
$svg_style->addResponsiveCSS('height', $svg_height, $svg_height_data['postfix'] ?? 'px');

$svg_color = Style::getColor($params->get('svg_color', ''));
foreach (['light' => $element->style, 'dark' => $element->style_dark] as $mode => $style) {
    if ($svg_color[$mode] === '') {
        continue;
    }

    $style->child('.as-svg-wrapper > svg')->addCss('color', $svg_color[$mode]);
    $style->child('.as-svg-wrapper > svg:not([fill="none"]), .as-svg-wrapper > svg [fill]:not([fill="none"])')
        ->addCss('fill', $svg_color[$mode] . ' !important');
    $style->child('.as-svg-wrapper > svg[stroke]:not([stroke="none"]), .as-svg-wrapper > svg [stroke]:not([stroke="none"])')
        ->addCss('stroke', $svg_color[$mode] . ' !important');
}
