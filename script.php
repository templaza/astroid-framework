<?php
/**
 * @package   Astroid Framework
 * @author    Astroid Framework https://astroidframe.work
 * @copyright Copyright (C) 2009 - 2019 Astroid Framework.
 * @license   GNU/GPLv2 and later
 */
use Joomla\Filesystem\File;
// no direct access
defined('_JEXEC') or die;

class astroid_template_oneInstallerScript {

	/**
	 *
	 * Function to run before installing the component
	 */
	public function preflight($type, $parent) {

	}

	/**
	 *
	 * Function to run when installing the component
	 * @return void
	 */
	public function install($parent) {
		$this->removeUnnecessary();
	}

	/**
	 *
	 * Function to run when un-installing the component
	 * @return void
	 */
	public function uninstall($parent) {

	}

	/**
	 *
	 * Function to run when updating the component
	 * @return void
	 */
	function update($parent) {
		$this->removeUnnecessary();
	}

	/**
	 *
	 * Function to update database schema
	 */
	public function updateDatabaseSchema($update) {

	}

	public function removeUnnecessary() {
		$removefile  =   array(
			'scss/one/frontend-edit.scss'
		);
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');
		foreach ($removefile as $file) {
			if (file_exists(JPATH_ROOT.'/templates/astroid_template_one/'.$file)) {
				File::delete(JPATH_ROOT.'/templates/astroid_template_one/'.$file);
			}
		}
	}

	/**
	 *
	 * Function to run after installing the component
	 */
	public function postflight($type, $parent) {

	}

}