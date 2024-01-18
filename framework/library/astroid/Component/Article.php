<?php

/**
 * @package   Astroid Framework
 * @author    Astroid Framework Team https://astroidframe.work
 * @copyright Copyright (C) 2023 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

namespace Astroid\Component;

use Astroid\Helper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\Database\ParameterType;
use Joomla\Utilities\IpHelper;
use Joomla\Database\DatabaseInterface;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Access\Access;
use Joomla\Component\Content\Site\Helper\RouteHelper;
use Joomla\CMS\Helper\TagsHelper;
use Joomla\Utilities\ArrayHelper;
use Joomla\Registry\Registry;

defined('_JEXEC') or die;

class Article
{
    public array $article;
    public function __construct($params = null)
    {
        if (is_numeric($params)) {
            $this->article['id'] = $params;
        } else if (is_object($params)) {
            $this->load($params);
        }
    }

    protected function load($params)
    {
        foreach ($params as $key => $value) {
            $this->article[$key] = $value;
        }
    }

    public function vote($vote)
    {
        Helper::loadLanguage('com_content');
        $pk   = (int) $this->article['id'];
        $rate = (int) $vote;
        if ($rate >= 1 && $rate <= 5 && $pk > 0) {
            $userIP = IpHelper::getIp();

            // Initialize variables.
            $db    = Factory::getContainer()->get(DatabaseInterface::class);
            $query = $db->getQuery(true);

            // Create the base select statement.
            $query->select('*')
                ->from($db->quoteName('#__content_rating'))
                ->where($db->quoteName('content_id') . ' = :pk')
                ->bind(':pk', $pk, ParameterType::INTEGER);

            // Set the query and load the result.
            $db->setQuery($query);

            // Check for a database error.
            try {
                $rating = $db->loadObject();
            } catch (\RuntimeException $e) {
                throw new \Exception($e->getMessage(), 0);
            }

            // There are no ratings yet, so lets insert our rating
            if (!$rating) {
                $query = $db->getQuery(true);

                // Create the base insert statement.
                $query->insert($db->quoteName('#__content_rating'))
                    ->columns(
                        [
                            $db->quoteName('content_id'),
                            $db->quoteName('lastip'),
                            $db->quoteName('rating_sum'),
                            $db->quoteName('rating_count'),
                        ]
                    )
                    ->values(':pk, :ip, :rate, 1')
                    ->bind(':pk', $pk, ParameterType::INTEGER)
                    ->bind(':ip', $userIP)
                    ->bind(':rate', $rate, ParameterType::INTEGER);

                // Set the query and execute the insert.
                $db->setQuery($query);

                try {
                    $db->execute();
                } catch (\RuntimeException $e) {
                    throw new \Exception($e->getMessage(), 0);
                }
            } else {
                if ($userIP != $rating->lastip) {
                    $query = $db->getQuery(true);

                    // Create the base update statement.
                    $query->update($db->quoteName('#__content_rating'))
                        ->set(
                            [
                                $db->quoteName('rating_count') . ' = ' . $db->quoteName('rating_count') . ' + 1',
                                $db->quoteName('rating_sum') . ' = ' . $db->quoteName('rating_sum') . ' + :rate',
                                $db->quoteName('lastip') . ' = :ip',
                            ]
                        )
                        ->where($db->quoteName('content_id') . ' = :pk')
                        ->bind(':rate', $rate, ParameterType::INTEGER)
                        ->bind(':ip', $userIP)
                        ->bind(':pk', $pk, ParameterType::INTEGER);

                    // Set the query and execute the update.
                    $db->setQuery($query);

                    try {
                        $db->execute();
                    } catch (\RuntimeException $e) {
                        throw new \Exception($e->getMessage(), 0);
                    }
                } else {
                    throw new \Exception(Text::_('COM_CONTENT_ARTICLE_VOTE_FAILURE'), 0);
                }
            }
            $query = $db->getQuery(true);

            // Create the base select statement.
            $query->select('*')
                ->from($db->quoteName('#__content_rating'))
                ->where($db->quoteName('content_id') . ' = :pk')
                ->bind(':pk', $pk, ParameterType::INTEGER);

            // Set the query and load the result.
            $db->setQuery($query);
            // Check for a database error.
            try {
                $rating = $db->loadObject();
            } catch (\RuntimeException $e) {
                throw new \Exception($e->getMessage(), 0);
            }
            $this->load($rating);
            $return = [];
            $return["message"] = Text::_('COM_CONTENT_ARTICLE_VOTE_SUCCESS');
            $return["rating"] = $this->getRating();
            return $return;
        }
    }

    public function getRating()
    {
        if (!isset($this->article['rating_sum']) || $this->article['rating_sum'] === null) {
            return 0;
        }
        return ceil($this->article['rating_sum'] / $this->article['rating_count']);
    }

    public static function getArticles( $count = 5, $ordering = 'latest', $catid = '', $include_subcategories = true, $post_format = '', $tagids = array() ) {

        $app = Factory::getApplication();
        $authorised = Access::getAuthorisedViewLevels($app->getIdentity()->id);

        $db = Factory::getContainer()->get(DatabaseInterface::class);

        $query = $db->getQuery(true);

        $query
            ->select('a.*')
            ->from($db->quoteName('#__content', 'a'))
            ->select($db->quoteName('b.alias', 'category_alias'))
            ->select($db->quoteName('b.title', 'category_title'))
            ->join('LEFT', $db->quoteName('#__categories', 'b') . ' ON (' . $db->quoteName('a.catid') . ' = ' . $db->quoteName('b.id') . ')')
            ->where($db->quoteName('b.extension') . ' = ' . $db->quote('com_content'));

        if($post_format) {
            $query->where('('.$db->quoteName('a.attribs') . ' LIKE ' . $db->quote('%"astroid_article_type":"'. $post_format .'"%') .')');
        }

        $query->where($db->quoteName('a.state') . ' = ' . $db->quote(1));

        // Category filter
        if (!is_array($catid))
        {
            $catid = [$catid];
        }

        if (!empty($catid))
        {

            $categories = self::getCategories($catid, $include_subcategories );

            $categories = array_filter(array_merge($categories, $catid));

            $query->where($db->quoteName('a.catid')." IN (" . implode( ',', $categories ) . ")");
        }

        // tags filter
        if (is_array($tagids) && count($tagids)) {
            $tagId = implode(',', ArrayHelper::toInteger($tagids));
            if ($tagId) {
                $subQuery = $db->getQuery(true)
                    ->select('DISTINCT content_item_id')
                    ->from($db->quoteName('#__contentitem_tag_map'))
                    ->where('tag_id IN (' . $tagId . ')')
                    ->where('type_alias = ' . $db->quote('com_content.article'));

                $query->innerJoin('(' . (string) $subQuery . ') AS tagmap ON tagmap.content_item_id = a.id');
            }
        }

        // publishing
        $nowDate = Factory::getDate()->toSql();
        $query->extendWhere(
            'AND',
            [
                $db->quoteName('a.publish_up') . ' IS NULL',
                $db->quoteName('a.publish_up') . ' <= :publishUp',
            ],
            'OR'
        )->extendWhere(
            'AND',
            [
                $db->quoteName('a.publish_down') . ' IS NULL',
                $db->quoteName('a.publish_down') . ' >= :publishDown',
            ],
            'OR'
        )->bind([':publishUp', ':publishDown'], $nowDate);

        // has order by
        if ($ordering == 'hits') {
            $query->order($db->quoteName('a.hits') . ' DESC');
        } elseif($ordering == 'featured') {
            $query->where($db->quoteName('a.featured') . ' = ' . $db->quote(1));
            $query->order($db->quoteName('a.publish_up') . ' DESC');
        } elseif($ordering == 'oldest') {
            $query->order($db->quoteName('a.publish_up') . ' ASC');
        } elseif($ordering == 'alphabet_asc') {
            $query->order($db->quoteName('a.title') . ' ASC');
        } elseif($ordering == 'alphabet_desc') {
            $query->order($db->quoteName('a.title') . ' DESC');
        } elseif($ordering == 'random') {
            $query->order($query->Rand());
        } else {
            $query->order($db->quoteName('a.publish_up') . ' DESC');
        }

        // Language filter
        if ($app->isClient('site') && $app->getLanguageFilter()) {
            $query->where('a.language IN (' . $db->Quote(Factory::getLanguage()->getTag()) . ',' . $db->Quote('*') . ')');
        }

        // continue query
        $query->where($db->quoteName('a.access')." IN (" . implode( ',', $authorised ) . ")");
        $query->order($db->quoteName('a.created') . ' DESC')
            ->setLimit($count);
        $db->setQuery($query);
        $items = $db->loadObjectList();

        foreach ($items as &$item) {
            $item->slug    	= $item->id . ':' . $item->alias;
            $item->catslug 	= $item->catid . ':' . $item->category_alias;
            $item->author   = Factory::getUser($item->created_by)->name;
            $item->link 	= Route::_(RouteHelper::getArticleRoute($item->slug, $item->catid, $item->language));
            $item->params   = new Registry($item->attribs);

            $item->tags = new TagsHelper;
            $item->tags->getItemTags('com_content.article', $item->id);

            $images = json_decode($item->images);
            if(isset($images->image_intro) && $images->image_intro) {
                if(strpos($images->image_intro, "http://") !== false || strpos($images->image_intro, "https://") !== false){
                    $item->image_thumbnail = $images->image_intro;
                } else {
                    $item->image_thumbnail = Uri::root(true) . '/' . $images->image_intro;
                }
            } elseif (isset($images->image_fulltext) && $images->image_fulltext) {
                if(strpos($images->image_fulltext, "http://") !== false || strpos($images->image_fulltext, "https://") !== false){
                    $item->image_thumbnail = $images->image_fulltext;
                } else {
                    $item->image_thumbnail = Uri::root(true) . '/' . $images->image_fulltext;
                }
            } else {
                $item->image_thumbnail = false;
            }

            // Post Format
            $item->post_format = $item->params->get('astroid_article_type', 'regular');
        }

        return $items;
    }

    public static function getVideoSrc($video_url = '') {
        $video_src = '';
        if(isset($video_url) && $video_url != NULL) {
            $video = parse_url($video_url);
            switch ($video['host']) {
                case 'youtu.be':
                    $video_id = trim($video['path'], '/');
                    $video_src = '//www.youtube.com/embed/' . $video_id;
                    break;

                case 'www.youtube.com':
                case 'youtube.com':
                    parse_str($video['query'], $query);
                    $video_id = $query['v'];
                    $video_src = '//www.youtube.com/embed/' . $video_id;
                    break;

                case 'vimeo.com':
                case 'www.vimeo.com':
                    $video_id = trim($video['path'], '/');
                    $video_src = "//player.vimeo.com/video/" . $video_id;
            }
        }
        return $video_src;
    }

    public static function getCategories($parent_id = [1], $include_subcategories = true, $child = false, $cats = array()) {

        $app = Factory::getApplication();
        $db = Factory::getDbo();
        $query = $db->getQuery(true);

        $query
            ->select('*')
            ->from($db->quoteName('#__categories'))
            ->where($db->quoteName('extension') . ' = ' . $db->quote('com_content'))
            ->where($db->quoteName('published') . ' = ' . $db->quote(1))
            ->where($db->quoteName('access')." IN (" . implode( ',', Factory::getUser()->getAuthorisedViewLevels() ) . ")")
            ->where($db->quoteName('language')." IN (" . $db->Quote(Factory::getLanguage()->getTag()).", ".$db->Quote('*') . ")");

        if (!empty(array_filter($parent_id)))
        {
            $query->where($db->quoteName('parent_id')." IN (" . implode( ',', $parent_id ) . ")");
        }

        $query->order($db->quoteName('lft') . ' ASC');

        $db->setQuery($query);
        $rows = $db->loadObjectList();

        foreach ($rows as $row) {

            if($include_subcategories) {
                array_push($cats, $row->id);
                if (self::hasChildren($row->id)) {
                    $cats = self::getCategories(array($row->id), $include_subcategories, true, $cats);
                }
            }
        }

        return $cats;
    }

    private static function hasChildren($parent_id = 1) {

        $app = Factory::getApplication();
        $db = Factory::getDbo();
        $query = $db->getQuery(true);

        $query
            ->select('*')
            ->from($db->quoteName('#__categories'))
            ->where($db->quoteName('extension') . ' = ' . $db->quote('com_content'))
            ->where($db->quoteName('published') . ' = ' . $db->quote(1))
            ->where($db->quoteName('access')." IN (" . implode( ',', Factory::getUser()->getAuthorisedViewLevels() ) . ")")
            ->where($db->quoteName('language')." IN (" . $db->Quote(Factory::getLanguage()->getTag()).", ".$db->Quote('*') . ")")
            ->where($db->quoteName('parent_id') . ' = ' . $db->quote($parent_id))
            ->order($db->quoteName('created_time') . ' DESC');

        $db->setQuery($query);

        $childrens = $db->loadObjectList();



        if(is_array($childrens) && count($childrens)) {
            return true;
        }

        return false;
    }
}
