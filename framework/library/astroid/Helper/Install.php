<?php

/**
 * @package   Astroid Framework
 * @author    Astroid Framework Team https://astroidframe.work
 * @copyright Copyright (C) 2023 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

namespace Astroid\Helper;
use Joomla\CMS\Factory;
use Joomla\CMS\Installer\Installer;
use Joomla\CMS\Installer\InstallerHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Updater\Update;
use Joomla\CMS\Uri\Uri;

defined('_JEXEC') or die;

class Install extends BaseDatabaseModel
{
    public static function InstallUrl($url = '') : array
    {
        $app        = Factory::getApplication();
        $tmp_dest   = $app->get('tmp_path');
        if (empty($url)) {
            return [
                'msg'   =>  Text::_('ASTROID_INSTALL_ENTER_A_URL'),
                'type'  =>  'error'
            ];
        }
        // We only allow http & https here
        $uri = new Uri($url);

        if (!\in_array($uri->getScheme(), ['http', 'https'])) {
            return [
                'msg'   =>  Text::_('ASTROID_INSTALL_INVALID_URL_SCHEME'),
                'type'  =>  'error'
            ];
        }

        // Handle updater XML file case:
        if (preg_match('/\.xml\s*$/', $url)) {
            $update = new Update();
            $update->loadFromXml($url);
            $package_url = trim($update->get('downloadurl', false)->_data);

            if ($package_url) {
                $url = $package_url;
            }

            unset($update);
        }

        // Download the package at the URL given.
        $p_file = InstallerHelper::downloadPackage($url);

        // Was the package downloaded?
        if (!$p_file) {
            return [
                'msg'   =>  Text::_('ASTROID_INSTALL_INVALID_URL'),
                'type'  =>  'error'
            ];
        }
        // Unpack the downloaded package file.
        $package = InstallerHelper::unpack($tmp_dest . '/' . $p_file, true);
        return self::Install($package, 'url');
    }

    public static function Install($package, $installType) : array
    {
        $app        = Factory::getApplication();
        // Check if package was uploaded successfully.
        if (!\is_array($package)) {
            return [
                'msg'   =>  Text::_('ASTROID_UNABLE_TO_FIND_INSTALL_PACKAGE'),
                'type'  =>  'error'
            ];
        }

        // Get an installer instance.
        $installer = Installer::getInstance();

        /*
         * Check for a Joomla core package.
         * To do this we need to set the source path to find the manifest (the same first step as Installer::install())
         *
         * This must be done before the unpacked check because InstallerHelper::detectType() returns a boolean false since the manifest
         * can't be found in the expected location.
         */
        if (isset($package['dir']) && is_dir($package['dir'])) {
            $installer->setPath('source', $package['dir']);

            if (!$installer->findManifest()) {
                // If a manifest isn't found at the source, this may be a Joomla package; check the package directory for the Joomla manifest
                if (file_exists($package['dir'] . '/administrator/manifests/files/joomla.xml')) {
                    // We have a Joomla package
                    if (\in_array($installType, ['upload', 'url'])) {
                        InstallerHelper::cleanupInstall($package['packagefile'], $package['extractdir']);
                    }

                    return [
                        'msg'   =>  Text::sprintf('ASTROID_UNABLE_TO_INSTALL_JOOMLA_PACKAGE', Route::_('index.php?option=com_joomlaupdate')),
                        'type'  =>  'error'
                    ];
                }
            }
        }

        // Was the package unpacked?
        if (empty($package['type'])) {
            if (\in_array($installType, ['upload', 'url'])) {
                InstallerHelper::cleanupInstall($package['packagefile'], $package['extractdir']);
            }

            return [
                'msg'   =>  Text::_('JLIB_INSTALLER_ABORT_DETECTMANIFEST'),
                'type'  =>  'error'
            ];
        }

        // Install the package.
        if (!$installer->install($package['dir'])) {
            // There was an error installing the package.
            $msg     = Text::sprintf('ASTROID_INSTALL_ERROR', Text::_('ASTROID_TYPE_TYPE_' . strtoupper($package['type'])));
            $msgType = 'error';
        } else {
            // Package installed successfully.
            $msg     = Text::sprintf('COM_INSTALLER_INSTALL_SUCCESS', Text::_('ASTROID_TYPE_TYPE_' . strtoupper($package['type'])));
            $msgType = 'success';
        }

        // Cleanup the install files.
        if (!is_file($package['packagefile'])) {
            $package['packagefile'] = $app->get('tmp_path') . '/' . $package['packagefile'];
        }
        InstallerHelper::cleanupInstall($package['packagefile'], $package['extractdir']);

        return [
            'msg'   =>  $msg,
            'type'  =>  $msgType
        ];
    }
}
