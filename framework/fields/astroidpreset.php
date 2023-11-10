<?php
/**
 * @package   Astroid Framework
 * @author    TemPlaza https://www.templaza.com
 * @copyright Copyright (C) 2009 - 2022 TemPlaza.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */
defined('JPATH_BASE') or die;

use Joomla\CMS\Form\FormField;

/**
 * Modules Position field.
 *
 * @since  3.4.2
 */
class JFormFieldAstroidPreset extends FormField {

	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  3.4.2
	 */
	protected $type = 'AstroidPreset';

	protected function getInput() {
		$presets    =   Astroid\Helper::getPresets();
        $data       =   array();
		for ($i = 0; $i<count($presets); $i++) {
			$preset     =   $presets[$i];
            $item       =   array();
            $item['title']  =   $preset['title'];
            $item['desc']   =   $preset['desc'];
			$arrName        =   explode(' ',$preset['title']);
			$avaName        =   '';
			for ($j=0; $j<count($arrName) && $j<3; $j++){
				if ($word = trim($arrName[$j])) {
					$avaName.=$word[0];
				}
			}
            $item['keyword']    = $avaName;
            $item['thumbnail']  = $preset['thumbnail'];
            $item['demo']       = !empty($preset['demo']) ? $preset['demo'] : '';
            $item['name']       = $preset['name'];
            $data[]             = $item;
		}
        $json =   [
            'id'      =>  $this->id,
            'name'    =>  $this->name,
            'value'   =>  $data,
            'type'    =>  strtolower($this->type)
        ];
        return json_encode($json);
	}
}