<?php

/**
 * @package   Astroid Framework
 * @author    Astroid Framework Team https://astroidframe.work
 * @copyright Copyright (C) 2023 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

namespace Astroid\Helper;

use Astroid\Helper\Constants;
use Astroid\Component\Article;
use Joomla\CMS\Factory;
use Joomla\Database\DatabaseInterface;
use Joomla\CMS\Router\Route;
use Joomla\Component\Content\Site\Helper\RouteHelper;
use Joomla\Registry\Registry;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Event\Content;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Access\Access;

defined('_JEXEC') or die;

class DynamicContent {
    public string $source;
    public int $start;
    public int $quantity;
    public array $conditions;
    public string $order;
    public string $order_dir;
    public mixed $dynamic_content;
    public Registry $options;
    private object $_db;
    private object $_app;
    private mixed $_article;
    private array $_special = [];

    public function __construct($source = '', $start = 1, $quantity = 10, $conditions = [], $order = '', $order_dir = '', $dynamic_content = null, $options = '') {
        $this->source = $source;
        $this->start = $start;
        $this->quantity = $quantity;
        $this->conditions = $conditions;
        $this->order = $order;
        $this->order_dir = $order_dir;
        $this->dynamic_content = $dynamic_content;
        $this->options = new Registry();
        if (is_string($options)) {
            $this->options->loadString($options);
        } elseif (is_array($options)) {
            $this->options->loadArray($options);
        }
        $this->_db = Factory::getContainer()->get(DatabaseInterface::class);
        $this->_app = Factory::getApplication();
    }

    public function getContent() {
        if (empty($this->source) || empty($this->dynamic_content)) {
            return false;
        }
        $query = $this->_db->getQuery(true);
        $query->from('#__' . $this->source . ' AS ' . $this->source);
        $joins = [];

        if (!empty(Constants::DynamicSourceFields()[$this->source]['depends'])) {
            foreach (Constants::DynamicSourceFields()[$this->source]['depends'] as $depend) {
                $joins[] = $depend;
            }
        }
        foreach ($this->dynamic_content as $key => $value) {
            $query->select($this->buildSelect($value, $key));
            if ($value->category->value !== $this->source && !in_array($value->category->value, $joins)) {
                $joins[] = $value->category->value;
            }
        }
        if (!empty(Constants::DynamicSourceFields()[$this->source]['where'])) {
            foreach (Constants::DynamicSourceFields()[$this->source]['where'] as $where) {
                $query->where($where);
            }
        }

        if (!empty($joins)) {
            foreach ($joins as $join) {
                if (isset(Constants::DynamicSourceFields()[$join]['joins'][$this->source])) {
                    $joinObj = Constants::DynamicSourceFields()[$join]['joins'][$this->source];
                    if (!empty(Constants::DynamicSourceFields()[$join]['where'])) {
                        foreach (Constants::DynamicSourceFields()[$join]['where'] as $where) {
                            $query->where($where);
                        }
                    }
                    $query->join($joinObj['join'], '#__' . $join . ' AS ' . $join . ' ON ' . $joinObj['on']);
                }
            }
        }

        if ($this->source === 'content') {
            $this->defaultContentQuery($query);
        } elseif ($this->source === 'categories') {
            $this->defaultCategoryQuery($query);
        }

        if (!empty($this->conditions)) {
            foreach ($this->conditions as $idx => $condition) {
                $condition_query = $this->buildCondition($condition);
                if (empty($condition_query)) {
                    continue;
                }
                if ($idx === 0 || $condition['operator'] === 'AND') {
                    $query->where($condition_query);
                } else {
                    $query->orWhere($condition_query);
                }
            }
        }

        if (!empty($this->order)) {
            $query->order($this->source . '.' . $this->order . ' ' . $this->order_dir);
        }
        $query->setLimit($this->quantity, $this->start - 1);
        $this->_db->setQuery($query);
        $result = $this->_db->loadObjectList();
        if (!empty($this->_special)) {
            foreach ($result as $value) {
                foreach ($this->_special as $specialKey => $specialValue) {
                    if (isset($value->{$specialKey})) {
                        $value->{$specialKey} = $this->getSpecialValue($value->{$specialKey}, $specialValue);
                    }
                }
            }
        }
        return $result;
    }
    private function buildSelect($value, $key): string
    {
        return match ($value->category->value) {
            'content' => match (true) {
                $value->value === 'text' => 'CONCAT(' . $value->category->value . '.introtext, ' . $value->category->value . '.fulltext) AS ' . $key,
                $value->value === 'link' => $this->getContentQuery($value, $key, 'link'),
                $value->value === 'rating' => $this->getContentQuery($value, $key, 'rating'),
                $value->value === 'votes' => $this->getContentQuery($value, $key, 'votes'),
                str_starts_with($value->value, 'images.') => $this->getContentQuery( $value, $key, 'params', 'images'),
                str_starts_with($value->value, 'urls.') => $this->getContentQuery($value, $key, 'params', 'urls'),
                str_starts_with($value->value, 'event.') => $this->getContentQuery($value, $key, 'event'),
                default => $value->category->value . '.' . $value->value . ' AS ' . $key
            },
            'categories' => match (true) {
                in_array($value->value, ['created_time', 'modified_time']) => (function () use ($value, $key) {
                    $this->_special[$key] = [
                        'category' => $value->category->value,
                        'value' => $value->value
                    ];
                    return $value->category->value . '.' . $value->value . ' AS ' . $key;
                })(),
                str_starts_with($value->value, 'params.') => (function () use ($value, $key) {
                    $this->_special[$key] = [
                        'category' => $value->category->value,
                        'value' => $value->value
                    ];
                    return 'categories.params AS ' . $key;
                })(),
                $value->value === 'link' => (function () use ($value, $key) {
                    $this->_special[$key] = [
                        'category' => $value->category->value,
                        'value' => $value->value
                    ];
                    return 'CONCAT(categories.id,' . $this->_db->quote(':') . ', categories.language) AS ' . $key;
                })(),
                $value->value === 'article_count' => (function () use ($value, $key) {
                    $subquery = $this->_db->getQuery(true);
                    $subquery->select('COUNT(*)')
                        ->from('#__content AS content')
                        ->where('content.catid = categories.id');
                    $subquery->where('content.state = 1');
                    $this->defaultContentQuery($subquery, true);
                    return '('.$subquery->__toString().') AS ' . $key;
                })(),
                default => $value->category->value . '.' . $value->value . ' AS ' . $key
            },
            default => $value->category->value . '.' . $value->value . ' AS ' . $key
        };
    }
    private function getContentQuery($value, $key, $type, $param = ''): string
    {
        return match ($type) {
            'link' => (function () use ($value, $key) {
                $this->_special[$key] = [
                    'category' => $value->category->value,
                    'value' => $value->value
                ];
                return 'CONCAT(content.id,' . $this->_db->quote(':') . ', content.alias, ' . $this->_db->quote(':') . ', content.catid, ' . $this->_db->quote(':') . ', content.language) AS ' . $key;
            })(),
            'rating', 'votes', 'event' => (function () use ($value, $key) {
                $this->_special[$key] = [
                    'category' => $value->category->value,
                    'value' => $value->value
                ];
                return 'content.id AS ' . $key;
            })(),
            'params' => (function () use ($value, $key, $param) {
                $this->_special[$key] = [
                    'category' => $value->category->value,
                    'value' => $value->value
                ];
                return 'content.'.$param.' AS ' . $key;
            })(),
            default => ''
        };
    }
    private function getSpecialValue($value, $specialValue) {
        return match ($specialValue['category']) {
            'content' => match (true) {
                $specialValue['value'] === 'link' => (function () use ($value) {
                    $link = explode(':', $value);
                    return Route::_(RouteHelper::getArticleRoute(($link[1] ? ($link[0] . ':' . $link[1]) : $link[0]), $link[2], $link[3]));
                })(),
                ($specialValue['value'] === 'rating' || $specialValue['value'] === 'votes') => (function () use ($value, $specialValue) {
                    $article   = $this->getArticle($value);
                    if ($specialValue['value'] === 'rating') {
                        return $article->rating_count > 0 ? (string) $article->rating : '';
                    } else {
                        return $article->rating_count;
                    }
                })(),
                str_starts_with($specialValue['value'], 'images.') => (function () use ($value, $specialValue) {
                    $attribs = new Registry();
                    $attribs->loadString($value);
                    $field = explode('.', $specialValue['value']);
                    $fieldVal = $attribs->get($field[1], '');
                    if (($field[1] === 'image_intro' || $field[1] === 'image_fulltext') && !empty($fieldVal)) {
                        return Uri::root().$fieldVal;
                    } else {
                        return $fieldVal;
                    }
                })(),
                str_starts_with($specialValue['value'], 'urls.') => (function () use ($value, $specialValue) {
                    $attribs = new Registry();
                    $attribs->loadString($value);
                    $field = explode('.', $specialValue['value']);
                    return $attribs->get($field[1], '');
                })(),
                str_starts_with($specialValue['value'], 'event.') => (function () use ($value, $specialValue) {
                    $article   = $this->getArticle($value);
                    $dispatcher = $this->_app->getDispatcher();

                    // Process the content plugins.
                    PluginHelper::importPlugin('content', null, true, $dispatcher);

                    $contentEventArguments = [
                        'context' => 'com_content.article',
                        'subject' => $article,
                        'params'  => $article->params,
                    ];

                    $field = explode('.', $specialValue['value']);
                    $contentEvents = match($field[1]) {
                        'afterDisplayTitle'    => new Content\AfterTitleEvent('onContentAfterTitle', $contentEventArguments),
                        'beforeDisplayContent' => new Content\BeforeDisplayEvent('onContentBeforeDisplay', $contentEventArguments),
                        'afterDisplayContent'  => new Content\AfterDisplayEvent('onContentAfterDisplay', $contentEventArguments),
                        default => ''
                    };
                    if (!$contentEvents) {
                        return '';
                    }
                    $results = $dispatcher->dispatch($contentEvents->getName(), $contentEvents)->getArgument('result', []);
                    return $results ? trim(implode("\n", $results)) : '';
                })(),
                default => $value
            },
            'categories' => match (true) {
                $specialValue['value'] === 'created_time' => (function () use ($value) {
                    return Text::sprintf('COM_CONTENT_CREATED_DATE_ON', HTMLHelper::_('date', $value, Text::_('DATE_FORMAT_LC3')));;
                })(),
                $specialValue['value'] === 'modified_time' => (function () use ($value) {
                    return Text::sprintf('COM_CONTENT_LAST_UPDATED', HTMLHelper::_('date', $value, Text::_('DATE_FORMAT_LC3')));
                })(),
                str_starts_with($specialValue['value'], 'params.') => (function () use ($value, $specialValue) {
                    $attribs = new Registry();
                    $attribs->loadString($value);
                    $field = explode('.', $specialValue['value']);
                    $fieldVal = $attribs->get($field[1], '');
                    if ($field[1] === 'image' && !empty($fieldVal)) {
                        return Uri::root().$fieldVal;
                    } else {
                        return $fieldVal;
                    }
                })(),
                $specialValue['value'] === 'link' => (function () use ($value) {
                    $link = explode(':', $value);
                    return Route::_(RouteHelper::getCategoryRoute($link[0], $link[1]));
                })(),
                default => $value
            },
            default => $value
        };
    }
    private function buildCondition($condition): string
    {
        $condition_field = match($condition['field']['category']['value']) {
            'categories' =>  match (true) {
                $condition['field']['value'] === 'article_count' => (function () use ($condition) {
                    $subquery = $this->_db->getQuery(true);
                    $subquery->select('COUNT(*)')
                        ->from('#__content AS content')
                        ->where('content.catid = categories.id');
                    $subquery->where('content.state = 1');
                    $this->defaultContentQuery($subquery, true);
                    return '(' . $subquery->__toString() . ')';
                })(),
                str_starts_with($condition['field']['value'], 'params.') => (function () use ($condition) {
                    return $condition['field']['category']['value'] . '.params';
                })(),
                $condition['field']['value'] === 'link' => '',
                default => $condition['field']['category']['value'] . '.' . $condition['field']['value']
            },
            'content' => match (true) {
                str_starts_with($condition['field']['value'], 'images.') => (function () use ($condition) {
                   return $condition['field']['category']['value'] . '.images';
                })(),
                str_starts_with($condition['field']['value'], 'urls.') => (function () use ($condition) {
                    return $condition['field']['category']['value'] . '.urls';
                })(),
                str_starts_with($condition['field']['value'], 'event.'),
                $condition['field']['value'] === 'link',
                ($condition['field']['value'] === 'rating' || $condition['field']['value'] === 'votes') => '',
                default => $condition['field']['category']['value'] . '.' . $condition['field']['value']
            },
            default => $condition['field']['category']['value'] . '.' . $condition['field']['value']
        };
        return !empty($condition_field) ? match ($condition['condition']) {
            '!' => $condition_field . ' IS NULL OR ' . $condition_field . ' = ""',
            '!!' => $condition_field . ' IS NOT NULL AND ' . $condition_field . ' != ""',
            '~=' => $condition_field . ' LIKE ' . $this->_db->quote('%' . $condition['value'] . '%'),
            '!~=' => $condition_field . ' NOT LIKE ' . $this->_db->quote('%' . $condition['value'] . '%'),
            '^=' => $condition_field . ' LIKE ' . $this->_db->quote($condition['value'] . '%'),
            '!^=' => $condition_field . ' NOT LIKE ' . $this->_db->quote($condition['value'] . '%'),
            '$=' => $condition_field . ' LIKE ' . $this->_db->quote('%' . $condition['value']),
            '!$=' => $condition_field . ' NOT LIKE ' . $this->_db->quote('%' . $condition['value']),
            default => $condition_field . ' ' . $condition['condition'] . ' ' . $this->_db->quote($condition['value']),
        } : '';
    }
    private function getArticle($id): object
    {
        if (isset($this->_article->id) && $this->_article->id === $id) {
            return $this->_article;
        }
        $component      = $this->_app->bootComponent('com_content')->getMVCFactory();
        $model          = $component->createModel('Article', 'Site');
        $this->_article = $model->getItem($id);
        return $this->_article;
    }
    private function defaultContentQuery($query, $subquery = false) : void
    {
        $content_catids = $this->options->get('content_catids', []);
        if (!empty($content_catids) && !$subquery)
        {
            $include_subcategories = $this->options->get('content_include_subcategories', 'exclude');
            $catids = [];
            foreach ($content_catids as $category) {
                $catids[] = $category->value;
            }
            $categories = Article::getCategories($catids, $include_subcategories === 'include');
            $categories = array_filter(array_merge($categories, $catids));
            $query->where($this->_db->quoteName('content.catid')." IN (" . implode( ',', $categories ) . ")");
        }
        // publishing
        $nowDate = Factory::getDate()->toSql();
        $query->extendWhere(
            'AND',
            [
                'content.publish_up IS NULL',
                'content.publish_up <= ' . $this->_db->quote($nowDate),
            ],
            'OR'
        )->extendWhere(
            'AND',
            [
                'content.publish_down IS NULL',
                'content.publish_down >= ' . $this->_db->quote($nowDate),
            ],
            'OR'
        );
        // Language filter
        if ($this->_app->isClient('site') && $this->_app->getLanguageFilter()) {
            $query->where('content.language IN (' . $this->_db->Quote(Factory::getLanguage()->getTag()) . ',' . $this->_db->Quote('*') . ')');
        }
        $authorised = Access::getAuthorisedViewLevels($this->_app->getIdentity()->id);
        $query->where($this->_db->quoteName('content.access')." IN (" . implode( ',', $authorised ) . ")");
    }

    private function defaultCategoryQuery($query, $subquery = false) : void
    {
        $category_parent = $this->options->get('category_parent', 1);
        if ($category_parent && !$subquery) {
            $query->where($this->_db->quoteName('categories.parent_id') . ' = ' . (int) $category_parent);
        }
        $query->where($this->_db->quoteName('categories.access')." IN (" . implode( ',', Factory::getUser()->getAuthorisedViewLevels() ) . ")");
        if ($this->_app->isClient('site') && $this->_app->getLanguageFilter()) {
            $query->where('categories.language IN (' . $this->_db->Quote(Factory::getLanguage()->getTag()) . ',' . $this->_db->Quote('*') . ')');
        }
    }
}