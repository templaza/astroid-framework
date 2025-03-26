<?php

/**
 * @package   Astroid Framework
 * @author    Astroid Framework Team https://astroidframe.work
 * @copyright Copyright (C) 2023 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

namespace Astroid\Helper;

use Astroid\Helper\Constants;
use Joomla\CMS\Factory;
use Joomla\Database\DatabaseInterface;

defined('_JEXEC') or die;

class DynamicContent {
    public string $source;
    public int $start;
    public int $quantity;
    public array $conditions;
    public string $order;
    public string $order_dir;
    public mixed $dynamic_content;
    private object $_db;

    public function __construct($source = '', $start = 1, $quantity = 10, $conditions = [], $order = '', $order_dir = '', $dynamic_content = null) {
        $this->source = $source;
        $this->start = $start;
        $this->quantity = $quantity;
        $this->conditions = $conditions;
        $this->order = $order;
        $this->order_dir = $order_dir;
        $this->dynamic_content = $dynamic_content;
        $this->_db = Factory::getContainer()->get(DatabaseInterface::class);
    }

    public function getContent() {
        if (empty($this->source) || empty($this->dynamic_content)) {
            return false;
        }

        $query = $this->_db->getQuery(true);
        $query->from('#__' . $this->source . ' AS ' . $this->source);
        $joins = [];

        foreach ($this->dynamic_content as $key => $value) {
            $query->select($this->buildSelect($value, $key));
            if ($value->category->value !== $this->source && !in_array($value->category->value, $joins)) {
                $joins[] = $value->category->value;
            }
        }

        if (!empty($joins)) {
            foreach ($joins as $join) {
                if (isset(Constants::$dynamic_source_fields[$join]['joins'][$this->source])) {
                    $joinObj = Constants::$dynamic_source_fields[$join]['joins'][$this->source];
                    $query->join($joinObj['join'], '#__' . $join . ' AS ' . $join . ' ON ' . $joinObj['on']);
                }
            }
        }

        if (!empty($this->conditions)) {
            foreach ($this->conditions as $idx => $condition) {
                if ($idx === 0 || $condition['operator'] === 'AND') {
                    $query->where($this->buildCondition($condition));
                } else {
                    $query->orWhere($this->buildCondition($condition));
                }
            }
        }

        if (!empty($this->order)) {
            $query->order($this->source . '.' . $this->order . ' ' . $this->order_dir);
        }
        $query->setLimit($this->quantity, $this->start - 1);
        $this->_db->setQuery($query);
        return $this->_db->loadObjectList();
    }

    private function buildSelect($value, $key) {
        return match ($value->category->value) {
            'content'=> match ($value->value) {
                'text' => 'CONCAT(' . $value->category->value . '.introtext, ' . $value->category->value . '.fulltext) AS ' . $key,
                default => $value->category->value . '.' . $value->value . ' AS ' . $key
            },
            default => $value->category->value . '.' . $value->value . ' AS ' . $key
        };
    }

    private function buildCondition($condition) {
        return match ($condition['condition']) {
            '!' => $condition['field']['category']['value'] . '.' . $condition['field']['value'] . ' IS NULL OR ' . $condition['field']['category']['value'] . '.' . $condition['field']['value'] . ' = ""',
            '!!' => $condition['field']['category']['value'] . '.' . $condition['field']['value'] . ' IS NOT NULL AND ' . $condition['field']['category']['value'] . '.' . $condition['field']['value'] . ' != ""',
            default => $condition['field']['category']['value'] . '.' . $condition['field']['value'] . ' ' . $condition['condition'] . ' ' . $this->_db->quote($condition['value']),
        };
    }
}