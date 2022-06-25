<?php
/**
 * @package   Astroid Framework
 * @author    TemPlaza https://www.templaza.com
 * @copyright Copyright (C) 2009 - 2022 TemPlaza.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */
defined('JPATH_BASE') or die;

JFormHelper::loadFieldClass('list');

/**
 * Modules Position field.
 *
 * @since  3.4.2
 */
class JFormFieldAstroidPreset extends JFormFieldList {

	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  3.4.2
	 */
	protected $type = 'AstroidPreset';

	protected function getInput() {
		$html       =   array();
		$presets    =   Astroid\Helper::getPresets();
		$html[]     =   '<div class="row mt-4">';
		$colors     =   ['#ffcdd2','#e1bee7','#bbdefb','#b2dfdb','#ffcc80'];

		for ($i = 0; $i<count($presets); $i++) {
			$preset     =   $presets[$i];
			$arrName        =   explode(' ',$preset['title']);
			$avaName        =   '';
			for ($j=0; $j<count($arrName) && $j<3; $j++){
				if ($word = trim($arrName[$j])) {
					$avaName.=$word[0];
				}
			}
			$thumbnail  =   '<div class="astroid_placeholder" style="background-color: '.$colors[rand(0,4)].';">'.$avaName.'</div>';
            if (!empty($preset['thumbnail'])) {
				$thumbnail  =   '<div class="astroid_placeholder" style="background-image: url('.$preset['thumbnail'].');"></div>';
			}
			$demo       =   '';
			if (!empty($preset['demo'])) {
			    $demo   =   ' <a href="'.$preset['demo'].'" target="_blank" class="btn btn-secondary">'.JText::_('TPL_ASTROID_LIVEPREVIEW').'</a>';
            }
			$html[]     =   '<div class="col-12 col-md-6 col-xl-4 col-xxl-3 mb-4">';
			$html[]     =   '<div class="card astroid-preset"><button type="button" class="close astroid-del-preset" aria-label="Close" data-token="'.JSession::getFormToken().'" data-name="'.$preset['name'].'"><span aria-hidden="true">&times;</span></button>'.$thumbnail.'<div class="card-body">';
			$html[]     =   '<h5 class="card-title">'.$preset['title'].'</h5>';
			$html[]     =   !empty($preset['desc']) ? '<p class="card-text">'.$preset['desc'].'</p>' : '';
			$html[]     =   '<a href="#" class="btn btn-primary astroid-load-preset" data-token="'.JSession::getFormToken().'" data-name="'.$preset['name'].'">'.JText::_('TPL_ASTROID_LOAD_PRESET').'</a>'.$demo;
			$html[]     =   '</div></div>';
			$html[]     =   '</div>';
		}
		$html[]     =   '<div class="col-12 col-md-6 col-xl-4 col-xxl-3 mb-4">';
		$html[]     =   '<div class="card astroid-create-preset"><div class="card-header">Create Preset</div><div class="card-body">';
		$html[]     =   '<div class="form-group"><label for="astroid-preset-name">Title</label><input type="text" name="astroid-preset-name" class="form-control" id="astroid-preset-name" placeholder="Name of Preset"></div>';
		$html[]     =   '<div class="form-group"><label for="astroid-preset-desc">Description</label><textarea class="form-control" name="astroid-preset-desc" id="astroid-preset-desc" rows="3"></textarea></div>';
		$html[]     =   '<input type="hidden" name="astroid-preset" id="astroid-preset">';
		$html[]     =   '<input type="hidden" name="astroid-template" id="astroid-template">';
		$html[]     =   '<a href="#" class="btn btn-primary" id="astroid-save-preset">'.JText::_('TPL_ASTROID_SAVE_PRESET').'</a>';
		$html[]     =   '</div></div>';
		$html[]     =   '</div>';
		$html[]     =   '</div>';

		return implode($html);
	}
}