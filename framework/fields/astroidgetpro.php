<?php

/**
 * @package   Astroid Framework
 * @author    Astroid Framework Team https://astroidframe.work
 * @copyright Copyright (C) 2023 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Form\FormField;
use Joomla\CMS\Language\Text;
use Astroid\Helper\Constants;

class JFormFieldAstroidGetPro extends FormField
{

    protected $type = 'astroidgetpro';

    public function getLabel()
    {
        return '';
    }

    public function getInput()
    {
        $promotion_info = Astroid\Helper::getPromotions();
        if (!empty($promotion_info)) {
            $promotion = $promotion_info[0];
            $title = $promotion['title'];
            $description = '<p>' . $promotion['description'] . '</p><a href="' . $promotion['link'] . '" target="_blank" class="btn btn-sm btn-as btn-danger">' . $promotion['link_title'] . '</a>';
        } else {
            $title = Text::_('ASTROID_GET_PRO_TITLE');
            $description = Text::sprintf('ASTROID_GET_PRO_DESCRIPTION', Text::_($this->getAttribute('label')), Constants::$go_pro . '?utm_source=feature_links&utm_medium=getpro_link&utm_campaign=go_pro&utm_id=astroid_signup');
        }
        $json =   [
            'id'      =>  $this->id,
            'name'    =>  $this->name,
            'value'   =>  $this->value,
            'type'    =>  strtolower($this->type),
            'title'   =>  $title,
            'desc'    =>  $description
        ];
        return json_encode($json);
    }
}
