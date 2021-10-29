<?php

/**
 * @package   Astroid Framework
 * @author    JoomDev https://www.joomdev.com
 * @copyright Copyright (C) 2009 - 2020 JoomDev.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

namespace Astroid\Component;

use Astroid\Helper;

defined('_JEXEC') or die;

class Article
{
    public function __construct($params = null)
    {
        if (is_numeric($params)) {
            $this->id = $params;
        } else if (is_object($params)) {
            $this->load($params);
        }
    }

    protected function get()
    {
        $db = \JFactory::getDbo();
        $query = "SELECT * FROM `#__content` as `c` LEFT JOIN `#__content_rating` as `r` ON `c`.`id`=`r`.`content_id` WHERE `c`.`id`='$this->id'";
        $db->setQuery($query);
        $result = $db->loadObject();
        if (empty($result)) {
            throw new \Exception('Article not found.', 404);
        } else {
            $this->load($result);
        }
    }

    protected function load($params)
    {
        foreach ($params as $key => $value) {
            $this->{$key} = $value;
        }
    }

    public function vote($vote)
    {
        Helper::loadLanguage('com_content');

        \JModelLegacy::addIncludePath(JPATH_SITE . '/components/com_content/models', 'ContentModel');
        $model = \JModelLegacy::getInstance('Article', 'ContentModel');
        if ($model->storeVote($this->id, $vote)) {
            $return = [];
            $return["message"] = \JText::_('COM_CONTENT_ARTICLE_VOTE_SUCCESS');
            $return["rating"] = $this->getRating();
            return $return;
        } else {
            throw new \Exception(\JText::_('COM_CONTENT_ARTICLE_VOTE_FAILURE'), 0);
        }
    }

    public function getRating()
    {
        $this->get();
        if ($this->rating_sum === null) {
            return 0;
        }
        return ceil($this->rating_sum / $this->rating_count);
    }
}
