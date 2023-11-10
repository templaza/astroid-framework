<?php
/**
 * @package   Astroid Framework
 * @author    Astroid Framework Team https://astroidframe.work
 * @copyright Copyright (C) 2023 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 * 	DO NOT MODIFY THIS FILE DIRECTLY AS IT WILL BE OVERWRITTEN IN THE NEXT UPDATE
 *  You can easily override all files under /frontend/ folder.
 *	Just copy the file to ROOT/templates/YOURTEMPLATE/html/frontend/ folder to create and override
 */
use Joomla\CMS\Factory;
// No direct access.
defined('_JEXEC') or die;
extract($displayData);

$params = Astroid\Framework::getTemplate()->getParams();
$document = Astroid\Framework::getDocument();

$enable_footer = $params->get('footer', 0);
if ($enable_footer) {
   $footer_copyright = $params->get('footer_copyright');
   // values to find & replace	
   $year = Factory::getDate()->format('Y');
   $sitename = Factory::getApplication()->get('sitename');
   $find = array('{year}', '{sitename}');
   $replace = array($year, $sitename);
   $footertext = str_replace($find, $replace, $footer_copyright);
   $html = '<div id="astroid-footer" class="astroid-footer">' . $footertext . '</div>';
   echo $html;
}
