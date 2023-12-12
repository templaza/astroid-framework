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
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\Component\Content\Site\Helper\RouteHelper;
// No direct access.
defined('_JEXEC') or die;
extract($displayData);

$params = Astroid\Framework::getTemplate()->getParams();
$type = $params->get('article_socialshare_type', 'none');
if ($type == 'none') {
   return;
}

// Share button default
if ($type == 'default') {
    $astroid_article_share_buttons = json_decode($params->get('astroid_article_share_buttons', '[]'), true);
    if (is_array($astroid_article_share_buttons) && count($astroid_article_share_buttons)) {
        $url = Route::_(RouteHelper::getArticleRoute($article->slug, $article->catid, $article->language), true, 0, true);
        echo '<div class="astroid-socialshare mb-4">';
        echo '<div class="row row-cols-auto g-3">';
        echo '<div class="ui-social-share-text">'.Text::_('ASTROID_SOCIAL_SHARE_TEXT').'</div>';
        foreach ($astroid_article_share_buttons as $article_share_button) {
            switch ($article_share_button['value']) {
                case 'facebook':
                    echo '<div><a class="facebook" onClick="window.open(\'https://www.facebook.com/sharer.php?u='.$url.'\',\'Facebook\',\'width=600,height=300,left=\'+(screen.availWidth/2-300)+\',top=\'+(screen.availHeight/2-150)+\'\'); return false;" href="https://www.facebook.com/sharer.php?u='.$url.'" title="Facebook"><i class="fa-brands fa-facebook"></i></a></div>';
                    break;
                case 'x':
                    echo '<div><a class="facebook" onClick="window.open(\'https://x.com/share?url='.$url.'&amp;text='.str_replace(" ", "%20", $article->title).'\',\'X\',\'width=600,height=300,left=\'+(screen.availWidth/2-300)+\',top=\'+(screen.availHeight/2-150)+\'\'); return false;" href="https://www.x.com/sharer.php?u='.$url.'&amp;text='.str_replace(" ", "%20", $article->title).'" title="X"><i class="fa-brands fa-x-twitter"></i></a></div>';
                    break;
                case 'linkedin':
                    echo '<div><a class="linkedin" onClick="window.open(\'https://www.linkedin.com/shareArticle?mini=true&url='.$url.'\',\'LinkedIn\',\'width=585,height=666,left=\'+(screen.availWidth/2-292)+\',top=\'+(screen.availHeight/2-333)+\'\'); return false;" href="https://www.linkedin.com/shareArticle?mini=true&url='.$url.'" title="LinkedIn"><i class="fa-brands fa-linkedin"></i></a></div>';
                    break;
                case 'pinterest':
                    echo '<div><a class="pinterest" onClick="window.open(\'http://pinterest.com/pin/create/button/?url='.$url.'&description='.$article->title.'\',\'Pinterest\',\'width=600,height=300,left=\'+(screen.availWidth/2-300)+\',top=\'+(screen.availHeight/2-150)+\'\'); return false;" href="http://pinterest.com/pin/create/button/?url='.$url.'&description='.$article->title.'" title="Pinterest"><i class="fa-brands fa-pinterest"></i></a></div>';
                    break;
            }
        }
        echo '</div>';
        echo '</div>';
    }
}
// Sharethis Social Share Start 
if ($type == 'sharethis') {
	$article_socialshare_sharethis = $params->get('article_socialshare_sharethis', ''); ?>
	<?php if(!empty($article_socialshare_sharethis)){?>
		<?php $doc = Factory::getDocument(); $doc->addScript('//platform-api.sharethis.com/js/sharethis.js#property='.$article_socialshare_sharethis.'&product=inline-share-buttons'); ?>
			<div class="astroid-socialshare">
                <div class="ui-social-share-text"><?php echo Text::_('ASTROID_SOCIAL_SHARE_TEXT') ?></div>
				<div class="sharethis-inline-share-buttons"></div>
			</div>
        <a href="http://pinterest.com/pin/create/button/?url={URI-encoded URL of the page to pin}&media={URI-encoded URL of the image to pin}&description={optional URI-encoded description}" class="pin-it-button" count-layout="horizontal">
            <img border="0" src="//assets.pinterest.com/images/PinExt.png" title="Pin It" />
        </a>
	<?php } ?>
<?php } ?>