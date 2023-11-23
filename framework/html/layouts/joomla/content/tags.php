<?php

/**
 * @package   Astroid Framework
 * @author    Astroid Framework https://astroidframe.work
 * @copyright Copyright (C) 2023 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\Component\Tags\Site\Helper\RouteHelper;
use Joomla\Registry\Registry;

$authorised = Factory::getUser()->getAuthorisedViewLevels();

?>
<?php if (!empty($displayData)) : ?>
    <ul class="tags list-inline">
        <?php foreach ($displayData as $i => $tag) : ?>
            <?php if (in_array($tag->access, $authorised)) : ?>
                <?php $tagParams = new Registry($tag->params); ?>
                <?php $link_class = $tagParams->get('tag_link_class', 'btn-outline-primary'); ?>
                <li class="list-inline-item tag-<?php echo $tag->tag_id; ?> tag-list<?php echo $i; ?>">
                    <a href="<?php echo Route::_(RouteHelper::getComponentTagRoute($tag->tag_id . ':' . $tag->alias, $tag->language)); ?>" class="btn rounded-pill btn-sm <?php echo $link_class; ?>">
                        <?php echo $this->escape($tag->title); ?>
                    </a>
                </li>
            <?php endif; ?>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>
