<?php

/**
 * @package   Astroid Framework
 * @author    Astroid Framework Team https://astroidframe.work
 * @copyright Copyright (C) 2023 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 * 	DO NOT MODIFY THIS FILE DIRECTLY AS IT WILL BE OVERWRITTEN IN THE NEXT UPDATE
 *  You can easily override all files under /frontend/ folder.
 *	Just copy the file to ROOT/templates/YOURTEMPLATE/html/frontend/blog/ folder to create and override
 */
// No direct access.
defined('_JEXEC') or die;
use Joomla\CMS\Router\Route;
use Joomla\Component\Content\Site\Helper\RouteHelper;
extract($displayData);
$params = $item->params;

?>
<?php if ($params->get('link_titles') && $params->get('access-view')) : ?>
    <a title="<?php echo @$title; ?>" href="<?php echo Route::_(RouteHelper::getArticleRoute($item->slug, $item->catid, $item->language)); ?>">
        <img class="card-img-top" src="<?php echo @$image; ?>" alt="<?php echo @$title; ?>" />
    </a>
<?php else : ?>
    <img class="card-img-top" src="<?php echo @$image; ?>" alt="<?php echo @$title; ?>" title="<?php echo @$title; ?>" />
<?php endif; ?>