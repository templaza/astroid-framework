<?php
/**
 * @package   Astroid Framework
 * @author    JoomDev https://www.joomdev.com
 * @copyright Copyright (C) 2009 - 2019 JoomDev.
 * @license   GNU/GPLv2 and later
 */
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
			if (JFile::exists(JPATH_ROOT.'/templates/astroid_template_one/'.$file)) {
				JFile::delete(JPATH_ROOT.'/templates/astroid_template_one/'.$file);
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