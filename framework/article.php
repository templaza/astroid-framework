<?php

/**
 * @package   Astroid Framework
 * @author    JoomDev https://www.joomdev.com
 * @copyright Copyright (C) 2009 - 2020 JoomDev.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

use Astroid\Framework;

defined('_JEXEC') or die;
jimport('astroid.framework.helper');
jimport('astroid.framework.constants');
jimport('astroid.framework.astroid');

if (ASTROID_JOOMLA_VERSION == 3) {
   JLoader::register('ModRelatedItemsHelper', JPATH_ROOT . '/modules/mod_related_items/helper.php');
   \JLoader::registerAlias('RelatedItemsHelper', 'ModRelatedItemsHelper');
} else {
   \JLoader::registerAlias('RelatedItemsHelper', '\\Joomla\\Module\\RelatedItems\\Site\\Helper\\RelatedItemsHelper');
}

class AstroidFrameworkArticle
{

   public $type;
   public $article;
   public $params;
   public $attribs;
   public $template;
   public $category_params;

   function __construct($article, $categoryView = false)
   {
      $this->article = $article;
      $attribs = new JRegistry();
      $attribs->loadString($this->article->attribs, 'JSON');
      $this->attribs = $attribs;
      $this->article->params->merge($attribs);
      $this->getCategoryParams();

      $this->type = $this->article->params->get('astroid_article_type', 'regular');
      $this->template = Astroid\Framework::getTemplate();

      $mainframe = JFactory::getApplication();
      $this->params = new JRegistry();
      $itemId = $mainframe->input->get('Itemid', 0, 'INT');
      if ($itemId) {
         $menu = $mainframe->getMenu();
         $item = $menu->getItem($itemId);
         if ($item->query['option'] == 'com_content' && ($item->query['view'] == 'category' || $item->query['view'] == 'article' || $item->query['view'] == 'featured')) {
            $this->params = $item->getParams();
         }
      }
      if (!$categoryView) {
         $this->addMeta();
         $this->renderRating();
      }
   }

   public function addMeta()
   {

      $app = JFactory::getApplication();
      $itemid = $app->input->get('Itemid', '', 'INT');

      $menu = $app->getMenu();
      $item = $menu->getItem($itemid);

      if (!empty($item)) {
         $params = $item->getParams();

         $enabled = $params->get('astroid_opengraph_menuitem', 0);
         $enabled = (int) $enabled;
         if (!empty($enabled)) {
            return;
         }
      }

      if (!(JFactory::getApplication()->input->get('option', '') == 'com_content' && JFactory::getApplication()->input->get('view', '') == 'article')) {
         return;
      }

      $enabled = $this->template->params->get('article_opengraph', 1);
      $fb_id = $this->template->params->get('article_opengraph_facebook', '');
      $tw_id = $this->template->params->get('article_opengraph_twitter', '');

      if (empty($enabled)) {
         return;
      }
      $config = JFactory::getConfig();

      $og_title = $this->article->title;
      if (!empty($this->article->params->get('astroid_og_title', ''))) {
         $og_title = $this->article->params->get('astroid_og_title', '');
      }
      $og_description = $this->article->metadesc;
      if (!empty($this->article->params->get('astroid_og_desc', ''))) {
         $og_description = $this->article->params->get('astroid_og_desc', '');
      }
      $images = json_decode($this->article->images);
      if (isset($images->image_intro) && !empty($images->image_intro)) {
         $og_image = JURI::base() . htmlspecialchars($images->image_intro, ENT_COMPAT, 'UTF-8');
      }
      if (!empty($this->article->params->get('astroid_og_image', ''))) {
         $og_image = JURI::base() . $this->article->params->get('astroid_og_image', '');
      }

      $og_sitename = $config->get('sitename');
      $og_siteurl = JURI::base() . ContentHelperRoute::getArticleRoute($this->article->slug, $this->article->catid, $this->article->language);

      $meta = [];
      $meta[] = '<meta property="og:type" content="article">';
      $meta[] = '<meta name="twitter:card" content="' . $this->template->params->get('twittercardtype', 'summary_large_image') . '" />';
      if (!empty($og_title)) {
         $meta[] = '<meta property="og:title" content="' . $og_title . '">';
      }
      if (!empty($og_sitename)) {
         $meta[] = '<meta property="og:site_name" content="' . $og_sitename . '">';
      }
      if (!empty($og_siteurl)) {
         $meta[] = '<meta property="og:url" content="' . $og_siteurl . '">';
      }
      if (!empty($og_description)) {
         $meta[] = '<meta property="og:description" content="' . substr($og_description, 0, 200) . '">';
      }
      if (!empty($og_image)) {
         $meta[] = '<meta property="og:image" content="' . $og_image . '">';
      }
      if (!empty($fb_id)) {
         $meta[] = '<meta property="fb:app_id" content="' . $fb_id . '" />';
      }
      if (!empty($tw_id)) {
         $meta[] = '<meta name="twitter:creator" content="@' . $tw_id . '" />';
      }
      $meta = implode('', $meta);
      if (!empty($meta)) {
         $document = JFactory::getDocument();
         $document->addCustomTag($meta);
      }
   }

   public function render($position = 'above-title')
   {
      if ($this->type == 'regular') {
         return false;
      }

      $contenPosition = $this->attribs->get('astroid_article_content_position', 'above-title');

      if ($contenPosition != $position) {
         return false;
      }

      Astroid\Framework::getDocument()->include('blog.' . $this->type, ['article' => $this->article]);
   }

   // Read Time
   public function renderReadTime()
   {
      if ($this->showReadTime()) {
         $this->article->readtime = $this->calculateReadTime($this->article->fulltext);
         Astroid\Framework::getDocument()->include('blog.modules.readtime', ['article' => $this->article]);
      }
   }

   public function showReadTime()
   {
      if (JFactory::getApplication()->input->get('tmpl', '') === 'component') {
         return FALSE;
      }

      $view  = JFactory::getApplication()->input->get('view', '');
      if ($view != 'category' && $view != 'featured') {
         // for single
         $article_level = $this->attribs->get('astroid_readtime', ''); // from article
         $category_level = $this->category_params->get('astroid_readtime', ''); // from article
         $astroid_level = $this->template->params->get('astroid_article_readtime', 1);
      } else {
         // for listing
         $article_level = $this->params->get('astroid_readtime', ''); // from menu
         $category_level = '';
         $astroid_level = $this->template->params->get('astroid_readtime', 1);
      }
      return $this->checkPriority($article_level, $category_level, $astroid_level);
   }

   // Social Share
   public function renderSocialShare()
   {
      if ($this->showSocialShare()) {
         Astroid\Framework::getDocument()->include('blog.modules.social', ['article' => $this->article]);
      }
   }

   public function showSocialShare()
   {

      if (JFactory::getApplication()->input->get('tmpl', '') === 'component') {
         return FALSE;
      }

      $article_level = $this->attribs->get('astroid_socialshare', '');
      $article_level = $article_level == 1 ? '' : $article_level;
      $category_level = $this->category_params->get('astroid_socialshare', '');
      $category_level = $category_level == 1 ? '' : $category_level;

      $astroid_level = $this->template->params->get('article_socialshare_type', "none");
      $astroid_level = $astroid_level == 'none' ? 0 : 1;
      return $this->checkPriority($article_level, $category_level, $astroid_level);
   }

   // Comments
   public function renderComments()
   {
      if ($this->showComments()) {
         Astroid\Framework::getDocument()->include('blog.modules.comments', ['article' => $this->article]);
      }
   }

   public function showComments()
   {
      if (JFactory::getApplication()->input->get('tmpl', '') === 'component') {
         return FALSE;
      }
      $category_level = $this->category_params->get('astroid_comments', '');
      $category_level = $category_level == 1 ? '' : $category_level;
      $article_level = $this->article->params->get('astroid_comments', '');
      $article_level = $article_level == 1 ? '' : $article_level;
      $astroid_level = $this->template->params->get('article_comments', "none");
      $astroid_level = $astroid_level == 'none' ? 0 : 1;
      return $this->checkPriority($article_level, $category_level, $astroid_level);
   }

   // Related Posts
   public function renderRelatedPosts()
   {
      if ($this->showRelatedPosts()) {
         $article_relatedposts_count = $this->attribs->get('article_relatedposts_count', '');
         $category_relatedposts_count = $this->category_params->get('article_relatedposts_count', '');

         if ($this->attribs->get('astroid_relatedposts', '') === '' && $this->category_params->get('astroid_relatedposts', '') === '') {
            $count = $this->template->params->get('article_relatedposts_count', 4);
         } else if ($this->attribs->get('astroid_relatedposts', '') === '' && $this->category_params->get('astroid_relatedposts', '') !== '') {
            if ($category_relatedposts_count === '') {
               $count = $this->template->params->get('article_relatedposts_count', 4);
            } else {
               $count = $this->category_params->get('article_relatedposts_count_custom', 4);
            }
         } else if ($this->attribs->get('astroid_relatedposts', '') !== '') {
            if ($article_relatedposts_count === '' && $category_relatedposts_count === '') {
               $count = $this->template->params->get('article_relatedposts_count', 4);
            } else if ($article_relatedposts_count === '' && $category_relatedposts_count !== '') {
               $count = $this->category_params->get('article_relatedposts_count_custom', 4);
            } else if ($article_relatedposts_count !== '') {
               $count = $this->attribs->get('article_relatedposts_count_custom', 4);
            } else {
               $count = $this->template->params->get('article_relatedposts_count', 4);
            }
         }

         $params = new JRegistry();
         $params->loadArray(['maximum' => $count]);
         $items = RelatedItemsHelper::getList($params);
         Astroid\Framework::getDocument()->include('blog.modules.related', ['items' => $items, 'display_posttypeicon' => $this->showRelatedPostTypeIcon(), 'display_badge' => $this->showRelatedArticleBadge()]);
      }
   }

   public function showRelatedPosts()
   {
      if (JFactory::getApplication()->input->get('tmpl', '') === 'component') {
         return FALSE;
      }
      $article_level = $this->attribs->get('astroid_relatedposts', '');
      $category_level = $this->category_params->get('astroid_relatedposts', '');
      $astroid_level = $this->template->params->get('article_relatedposts', 1);
      return $this->checkPriority($article_level, $category_level, $astroid_level);
   }

   // Author Info
   public function renderAuthorInfo()
   {
      if ($this->showAuthorInfo()) {
         Astroid\Framework::getDocument()->include('blog.modules.author_info', ['article' => $this->article]);
      }
   }

   public function showAuthorInfo()
   {
      if (JFactory::getApplication()->input->get('tmpl', '') === 'component') {
         return FALSE;
      }
      $article_level = $this->attribs->get('astroid_authorinfo', '');
      $category_level = $this->category_params->get('astroid_authorinfo', '');
      $astroid_level = $this->template->params->get('article_authorinfo', 1);
      return $this->checkPriority($article_level, $category_level, $astroid_level);
   }

   // menu level article badge
   public function renderArticleBadge()
   {
      if ($this->showArticleBadge()) {
         Astroid\Framework::getDocument()->include('blog.modules.badge', ['article' => $this->article]);
      }
   }

   public function showArticleBadge()
   {
      if (JFactory::getApplication()->input->get('tmpl', '') === 'component') {
         return FALSE;
      }
      if (JFactory::getApplication()->input->get('option', '') === 'com_content' && JFactory::getApplication()->input->get('view', '') === 'article') {
         return FALSE;
      }
      $article_level = $this->article->params->get('astroid_article_badge', 0);
      if (!$article_level) {
         return false;
      }
      $menu_level = $this->params->get('astroid_badge', '');
      $astroid_level = $this->template->params->get('astroid_badge', 1);
      $return =  $this->checkPriority('', $menu_level, $astroid_level);
      return $return;
   }

   public function showRelatedArticleBadge()
   {
      if ($this->attribs->get('astroid_relatedposts', '') === '') {
         $article_level = '';
      } else {
         $article_level = $this->attribs->get('article_relatedposts_badge', '');
      }
      if ($this->category_params->get('astroid_relatedposts', '') === '') {
         $category_level = '';
      } else {
         $category_level = $this->category_params->get('article_relatedposts_badge', '');
      }
      if ($this->template->params->get('article_relatedposts', 1)) {
         $astroid_level = $this->template->params->get('article_relatedposts_badge', 1);
      } else {
         $astroid_level = 0;
      }
      $return =  $this->checkPriority($article_level, $category_level, $astroid_level);
      return $return;
   }


   // Post Type Icon
   public function renderPostTypeIcon()
   {
      if ($this->showPostTypeIcon()) {
         Astroid\Framework::getDocument()->include('blog.modules.posttype', ['article' => $this->article]);
      }
   }

   public function showPostTypeIcon()
   {
      if (JFactory::getApplication()->input->get('tmpl', '') === 'component') {
         return FALSE;
      }
      if (JFactory::getApplication()->input->get('option', '') === 'com_content' && JFactory::getApplication()->input->get('view', '') === 'article') {
         return FALSE;
      }
      $menu_level = $this->params->get('astroid_posttype', '');
      $article_level = $this->article->params->get('astroid_posttype', '');
      $astroid_level = $this->template->params->get('article_posttype', 1);
      $view  = JFactory::getApplication()->input->get('view', '');
      switch ($astroid_level) {
         case 2:
            if ($view === 'article') {
               $astroid_level = 1;
               echo "enterd to article view only";
            }
            break;
         case 3:
            if ($view === 'category' || $view === 'featured') {
               $astroid_level = 1;
            }
            break;
      }
      return $this->checkPriority($menu_level, $article_level, $astroid_level);
   }

   public function showRelatedPostTypeIcon()
   {
      if ($this->attribs->get('astroid_relatedposts', '') === '') {
         $article_level = '';
      } else {
         $article_level = $this->attribs->get('article_relatedposts_posttype', '');
      }
      if ($this->category_params->get('astroid_relatedposts', '') === '') {
         $category_level = '';
      } else {
         $category_level = $this->category_params->get('article_relatedposts_posttype', '');
      }
      if ($this->template->params->get('article_relatedposts', 1)) {
         $astroid_level = $this->template->params->get('article_relatedposts_posttype', 1);
      } else {
         $astroid_level = 0;
      }
      return $this->checkPriority($article_level, $category_level, $astroid_level);
   }

   public function renderRating()
   {
      if ($this->showRating()) {
         $document = Framework::getDocument();
         $document->addScript('//cdn.jsdelivr.net/npm/semantic-ui@2.4.0/dist/components/rating.min.js', 'body');
         $document->addStyleSheet('//cdn.jsdelivr.net/npm/semantic-ui@2.4.0/dist/components/rating.min.css');
      }
   }

   public function showRating()
   {
      if (JFactory::getApplication()->input->get('tmpl', '') === 'component') {
         return FALSE;
      }

      $option = JFactory::getApplication()->input->get('option', '');
      $view = JFactory::getApplication()->input->get('view', '');
      if ($option == 'com_content' && ($view == 'featured' || $view == 'category')) {
         return FALSE;
      }

      if (!$this->article->params->get('show_vote', 0)) {
         return FALSE;
      }

      $astroid_level = $this->template->params->get('article_rating', 1);
      return $astroid_level ? true : false;
   }

   // Utility Functions
   public function checkPriority($firstPriority, $secondPriority, $thirdPriority)
   {
      $firstPriority = $firstPriority === '' ? -1 : (int) $firstPriority;
      $secondPriority = $secondPriority === '' ? -1 : (int) $secondPriority;
      $thirdPriority = $thirdPriority === '' ? -1 : (int) $thirdPriority;

      $enabled = false;
      switch ($firstPriority) {
         case -1:
            switch ($secondPriority) {
               case -1:
                  switch ($thirdPriority) {
                     case 1:
                        $enabled = true;
                        break;
                     case 0:
                        $enabled = false;
                        break;
                  }
                  break;
               case 1:
                  $enabled = true;
                  break;
               case 0:
                  $enabled = false;
                  break;
            }
            break;
         case 1:
            $enabled = true;
            break;
         case 0:
            $enabled = false;
            break;
      }
      return $enabled;
   }

   public function calculateReadTime($string)
   {
      $speed = 170;
      $word = str_word_count(strip_tags($string));
      $m = floor($word / $speed);
      $s = floor($word % $speed / ($speed / 60));

      if ($m < 1) {
         $m = 1;
      } else if ($s > 30) {
         $m = $m;
      } else {
         $m++;
      }
      if ($m == 1) {
         return JText::sprintf('ASTROID_ARTICLE_READTIME_MINUTE', $m);
      } else {
         return JText::sprintf('ASTROID_ARTICLE_READTIME_MINUTES', $m);
      }
   }

   public function getTemplateParams()
   {
      return $this->template->params;
   }

   public function getImage()
   {
      $type = $this->article->params->get('astroid_article_type', 'regular');
      $thumbnail = '';
      switch ($type) {
         case 'video':
            $thumbnail = $this->getVideoThumbnail();
            break;
         case 'gallery':
            $thumbnail = $this->getGalleryThumbnail();
            break;
      }
      $images = json_decode($this->article->images);
      if (isset($images->image_intro) && !empty($images->image_intro) && empty($thumbnail)) {
         $thumbnail = true;
      }
      return $thumbnail;
   }

   public function getGalleryThumbnail()
   {
      $enabled = $this->article->params->get('astroid_article_thumbnail', 1);
      if (!$enabled) {
         return FALSE;
      }
      $items = $this->article->params->get('astroid_article_gallery_items', []);
      if (empty($items)) {
         return '';
      }
      $first_element = NULL;
      foreach ($items as $item) {
         $first_element = $item;
         break;
      }
      return JURI::root() . $first_element['image'];
   }

   public function getVideoThumbnail()
   {
      $enabled = $this->article->params->get('astroid_article_thumbnail', 1);
      if (!$enabled) {
         return FALSE;
      }
      $type = $this->article->params->get('astroid_article_video_type', 'youtube');
      $return = '';
      $id = $this->article->params->get('astroid_article_video_url', '');
      if (empty($id)) {
         return $return;
      }
      $id = self::getVideoId($id, $type);
      switch ($type) {
         case 'youtube':
            $return = '//img.youtube.com/vi/' . $id . '/maxresdefault.jpg';
            break;
         case 'vimeo':
            $return = self::getVimeoThumbnailByID($id);
            break;
      }
      return $return;
   }

   public static function getVimeoThumbnailByID($vid)
   {
      $hash = unserialize(file_get_contents("https://vimeo.com/api/v2/video/" . $vid . ".php"));
      $thumbnail = $hash[0]['thumbnail_large'];
      return $thumbnail;
   }

   public static function getVideoId($url, $type)
   {
      $parts = parse_url($url);
      if ($type == "youtube") {
         parse_str($parts['query'], $query);
         return (isset($query['v']) ? $query['v'] : '');
      } else {
         return (isset($parts['path']) ? str_replace('/', '', $parts['path']) : '');
      }
   }

   public static function getArticleRating($id)
   {
      $db = JFactory::getDbo();
      $query = "SELECT * FROM `#__content_rating` WHERE `content_id`='$id'";
      $db->setQuery($query);
      $result = $db->loadObject();
      if (empty($result)) {
         return 0;
      } else {
         return ceil($result->rating_sum / $result->rating_count);
      }
   }

   public function getCategoryParams()
   {
      $params = new JRegistry();
      if (\JFactory::getApplication()->input->get('view', '') == 'article' && !empty($this->article->catid)) {
         $db = \JFactory::getDbo();
         $query = "SELECT `params` FROM `#__categories` WHERE `id`=" . $this->article->catid;
         $db->setQuery($query);
         $result = $db->loadObject();
         if (!empty($result)) {
            $params->loadString($result->params, 'JSON');
         }
      }
      $this->category_params = $params;
   }
}
