<?php

/**
 * @package   Astroid Framework
 * @author    JoomDev https://www.joomdev.com
 * @copyright Copyright (C) 2009 - 2020 JoomDev.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

if (ASTROID_JOOMLA_VERSION > 3) {
    \JLoader::registerAlias('ContentHelperRoute', 'Joomla\Component\Content\Site\Helper\RouteHelper');
} else {
    include_once(JPATH_COMPONENT . '/helpers/route.php');
}

?>
<div class="items-more">
    <h3><?php echo Text::_('COM_CONTENT_MORE_ARTICLES'); ?></h3>
    <ul class="list-group">
        <?php foreach ($this->link_items as &$item) : ?>
            <li class="list-group-item">
                <a href="<?php echo Route::_(ContentHelperRoute::getArticleRoute($item->slug, $item->catid, $item->language)); ?>"><?php echo $item->title; ?></a>
            </li>
        <?php endforeach; ?>
    </ul>
</div>