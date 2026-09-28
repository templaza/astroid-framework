<?php
/**
 * @package   Astroid Framework
 * @author    Astroid Framework Team https://astroidframe.work
 * @copyright Copyright (C) 2023 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */
// Check to ensure this file is included in Joomla!
namespace Astroid\Field;
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Helper\TagsHelper;
use Joomla\CMS\Form\Field\TagField;

class AstroidtagField extends TagField {

    public $type = 'AstroidTag';
    protected $ordering;

    public function getInput() {
        $data = $this->collectLayoutData();

        if (!\is_array($this->value) && !empty($this->value)) {
            if ($this->value instanceof TagsHelper) {
                if (empty($this->value->tags)) {
                    $this->value = [];
                } else {
                    $this->value = $this->value->tags;
                }
            }

            // String in format 2,5,4
            if (\is_string($this->value)) {
                $this->value = explode(',', $this->value);
            }

            // Integer is given
            if (\is_int($this->value)) {
                $this->value = [$this->value];
            }

            $data['value'] = $this->value;
        }

        $options = [];
        foreach ($this->getOptions() as $option) {
            $tmp = [
                'value' => $option->value,
                'label'  => $option->text,
            ];
            if ($option->level > 1) {
                $dash = '';
                for($i = 1; $i < $option->level; $i++) {
                    $dash .= '-';
                }

                $tmp['label'] = substr($tmp['label'], strrpos($tmp['label'], '/') + 1);
                $tmp['label'] = $dash . ' ' . $tmp['label'];
            }
            $options[] = $tmp;
        }

        $json =   [
            'id'         =>  $this->id,
            'name'       =>  $this->name,
            'value'      =>  $data['value'],
            'options'    =>  $options,
            'type'       =>  strtolower($this->type),
        ];
        return json_encode($json);
    }
}