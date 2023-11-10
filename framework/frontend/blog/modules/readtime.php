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
use Joomla\CMS\Language\Text;
// No direct access.
defined('_JEXEC') or die;
extract($displayData);

$params = $article->params;
?>
<dd class="readtime">
   <i class="far fa-clock"></i>
   <span><?php echo Text::sprintf('ASTROID_ARTICLE_BLOG_READTIME', $article->readtime); ?></span>
</dd>