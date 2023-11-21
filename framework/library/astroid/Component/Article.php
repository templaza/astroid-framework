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
}
