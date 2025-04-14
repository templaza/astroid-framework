<?php
/**
* @package   Astroid Framework
* @author    Astroid Framework Team https://astroidframe.work
* @copyright Copyright (C) 2025 AstroidFrame.work.
* @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

namespace Astroid\Helper;

use Astroid\Helper;
use Joomla\Registry\Registry;

class SubForm {
    public array $data = [];
    public function __construct($data = '{}') {
        $data = json_decode($data);
        if (!empty($data)) {
            foreach ($data as $value) {
                $tmp = (object) [
                    'id' => $value->id,
                    'params' => Helper::loadParams($value->params)
                ];
                $dynamic_data = $this->getDynamicContent($tmp);
                if (!empty($dynamic_data)) {
                    foreach ($dynamic_data as $idx => $dynamic_data_item) {
                        $params_data = new Registry();
                        $params_data->merge($tmp->params);
                        foreach ($dynamic_data_item as $key => $item_value) {
                            $params_data->set($key, $item_value);
                        }
                        $this->data[] = (object) [
                            'id' => $value->id . '_' . $idx,
                            'params' => $params_data
                        ];
                    }
                } else {
                    $this->data[] = $tmp;
                }
            }
        }
    }

    public function getDynamicContent($item = null) {
        $dynamic_data = [];
        if (Helper::isPro() && !empty($item)) {
            $dynamic_params = $item->params->get('dynamic_content_settings');
            if (!empty($dynamic_params)) {
                $dynamic_content = new DynamicContent(
                    $dynamic_params->source,
                    $dynamic_params->start,
                    $dynamic_params->quantity,
                    $dynamic_params->conditions,
                    $dynamic_params->order,
                    $dynamic_params->order_dir,
                    $dynamic_params->dynamic_content,
                    $dynamic_params->options
                );
                $dynamic_data = $dynamic_content->getContent();
            }
        }
        return $dynamic_data;
    }

    public function getData(): array
    {
        return $this->data;
    }
}