<?php

/**
 * @package   Astroid Framework
 * @author    JoomDev https://www.joomdev.com
 * @copyright Copyright (C) 2009 - 2020 JoomDev.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

namespace Astroid\Helper;

use Astroid\Helper;

defined('_JEXEC') or die;

class Media
{
    public static function getPath()
    {
        $params = \JComponentHelper::getParams('com_media');
        return $params->get('image_path', 'images');
    }

    public static function library()
    {
        $input = \JFactory::getApplication()->input;
        $folder = $input->get('folder', '', 'RAW');
        $asset = $input->get('asset');
        $author = $input->get('author');

        $user = \JFactory::getUser();

        if (!$user->authorise('core.manage', 'com_media') && (!$asset || (!$user->authorise('core.edit', $asset) && !$user->authorise('core.create', $asset) && count($user->getAuthorisedCategories($asset, 'core.create')) == 0) && !($user->id == $author && $user->authorise('core.edit.own', $asset)))) {
            throw new \JAccessExceptionNotallowed(JText::_('JERROR_ALERTNOAUTHOR'), 403);
        }

        $data = self::getList($folder);
        $data['current_folder'] = self::getPath() . (empty($folder) ? '' : '/' . $folder);

        return $data;
    }

    public static function getList($folder)
    {
        define('COM_MEDIA_BASE', JPATH_ROOT . '/' . self::getPath());
        define('COM_MEDIA_BASEURL', \JUri::root() . self::getPath());

        $current = $folder;
        $basePath = COM_MEDIA_BASE . ((strlen($current) > 0) ? '/' . $current : '');
        $mediaBase = str_replace(DIRECTORY_SEPARATOR, '/', COM_MEDIA_BASE . '/');

        $images = array();
        $folders = array();
        $docs = array();
        $videos = array();

        $fileList = false;
        $folderList = false;

        if (file_exists($basePath)) {
            // Get the list of files and folders from the given folder
            $fileList = \JFolder::files($basePath);
            $folderList = \JFolder::folders($basePath);
        }

        // Iterate over the files if they exist
        if ($fileList !== false) {
            $tmpBaseObject = new \JObject;

            foreach ($fileList as $file) {
                if (is_file($basePath . '/' . $file) && substr($file, 0, 1) != '.' && strtolower($file) !== 'index.html') {
                    $tmp = clone $tmpBaseObject;
                    $tmp->name = $file;
                    $tmp->title = $file;
                    $tmp->path = str_replace(DIRECTORY_SEPARATOR, '/', \JPath::clean($basePath . '/' . $file));
                    $tmp->path_relative = str_replace($mediaBase, '', $tmp->path);
                    $tmp->size = filesize($tmp->path);

                    $ext = strtolower(\JFile::getExt($file));

                    switch ($ext) {
                            // Image
                        case 'jpg':
                        case 'png':
                        case 'gif':
                        case 'xcf':
                        case 'odg':
                        case 'bmp':
                        case 'jpeg':
                        case 'svg':
                        case 'webp':
                        case 'ico':
                        case 'tiff':
                            $info = @getimagesize($tmp->path);
                            if (!is_array($info)) {
                                $tmp->width = 0;
                                $tmp->height = 0;
                                $tmp->type = '';
                                $tmp->mime = '';
                                $tmp->width_60 = 0;
                                $tmp->height_60 = 0;
                                $tmp->width_16 = 0;
                                $tmp->height_16 = 0;
                                $images[] = $tmp;
                                break;
                            }
                            $tmp->width = @$info[0];
                            $tmp->height = @$info[1];
                            $tmp->type = @$info[2];
                            $tmp->mime = @$info['mime'];

                            if (($info[0] > 60) || ($info[1] > 60)) {
                                $dimensions = self::imageResize($info[0], $info[1], 60);
                                $tmp->width_60 = $dimensions[0];
                                $tmp->height_60 = $dimensions[1];
                            } else {
                                $tmp->width_60 = $tmp->width;
                                $tmp->height_60 = $tmp->height;
                            }

                            if (($info[0] > 16) || ($info[1] > 16)) {
                                $dimensions = self::imageResize($info[0], $info[1], 16);
                                $tmp->width_16 = $dimensions[0];
                                $tmp->height_16 = $dimensions[1];
                            } else {
                                $tmp->width_16 = $tmp->width;
                                $tmp->height_16 = $tmp->height;
                            }

                            $images[] = $tmp;
                            break;

                            // Video
                        case 'mp4':
                        case 'webm':
                        case 'ogg':
                            $tmp->icon_32 = 'media/mime-icon-32/' . $ext . '.png';
                            $tmp->icon_16 = 'media/mime-icon-16/' . $ext . '.png';
                            $videos[] = $tmp;
                            break;

                            // Non-image document
                        default:
                            $tmp->icon_32 = 'media/mime-icon-32/' . $ext . '.png';
                            $tmp->icon_16 = 'media/mime-icon-16/' . $ext . '.png';
                            $docs[] = $tmp;
                            break;
                    }
                }
            }
        }

        // Iterate over the folders if they exist
        if ($folderList !== false) {
            $tmpBaseObject = new \JObject;

            foreach ($folderList as $folder) {
                $tmp = clone $tmpBaseObject;
                $tmp->name = basename($folder);
                $tmp->path = str_replace(DIRECTORY_SEPARATOR, '/', \JPath::clean($basePath . '/' . $folder));
                $tmp->path_relative = str_replace($mediaBase, '', $tmp->path);
                $count = self::countFiles($tmp->path);
                $tmp->files = $count[0];
                $tmp->folders = $count[1];

                $folders[] = $tmp;
            }
        }

        $list = array('folders' => $folders, 'docs' => $docs, 'images' => $images, 'videos' => $videos);

        return $list;
    }

    public static function imageResize($width, $height, $target)
    {
        if ($width > $height) {
            $percentage = ($target / $width);
        } else {
            $percentage = ($target / $height);
        }
        $width = round($width * $percentage);
        $height = round($height * $percentage);
        return array($width, $height);
    }

    public static function countFiles($dir)
    {
        $total_file = 0;
        $total_dir = 0;
        if (is_dir($dir)) {
            $d = dir($dir);
            while (($entry = $d->read()) !== false) {
                if ($entry[0] !== '.' && strpos($entry, '.html') === false && strpos($entry, '.php') === false && is_file($dir . DIRECTORY_SEPARATOR . $entry)) {
                    $total_file++;
                }

                if ($entry[0] !== '.' && is_dir($dir . DIRECTORY_SEPARATOR . $entry)) {
                    $total_dir++;
                }
            }
            $d->close();
        }
        return array($total_file, $total_dir);
    }

    public static function upload()
    {
        $input = \JFactory::getApplication()->input;
        $dir = $input->get('dir', '', 'RAW');
        $media = $input->get('media', '', 'images');

        if (empty($dir)) {
            $uploadDir = JPATH_SITE . '/' . 'images' . '/' . date('Y') . '/' . date('m') . '/' . date('d');
            Helper::createDir($uploadDir);
            $uploadFolder = date('Y') . '/' . date('m') . '/' . date('d');
        } else {
            $uploadDir = JPATH_SITE . '/' . $dir;
            if ($dir == 'images') {
                $uploadFolder = '';
            } else {
                $uploadFolder = preg_replace('/images\//', '', $dir, 1);
            }
        }


        $fieldName = 'file';

        $fileError = $_FILES[$fieldName]['error'];
        if ($fileError > 0) {
            switch ($fileError) {
                case 1:
                    throw new \Exception(\JText::_('ASTROID_ERROR_LARGE_FILE'));
                    return;

                case 2:
                    throw new \Exception(\JText::_('ASTROID_ERROR_FILE_HTML_ALLOW'));
                    return;

                case 3:
                    throw new \Exception(\JText::_('ASTROID_ERROR_FILE_PARTIAL_ALLOW'));
                    return;

                case 4:
                    throw new \Exception(\JText::_('ASTROID_ERROR_NO_FILE'));
                    return;
            }
        }


        $pathinfo = pathinfo($_FILES[$fieldName]['name']);
        $uploadedFileExtension = $pathinfo['extension'];
        $uploadedFileExtension = strtolower($uploadedFileExtension);
        if ($media == 'images') {
            $validFileExts = explode(',', 'jpeg,jpg,png,gif,ico,odg,xcf,bmp,tiff,webp,svg');
        } else {
            $validFileExts = explode(',', 'mp4,mpeg,mpg');
        }
        if (!in_array($uploadedFileExtension, $validFileExts)) {
            throw new \Exception(\JText::_('INVALID EXTENSION'));
        }

        $fileTemp = $_FILES[$fieldName]['tmp_name'];

        if ($media == 'images' && $uploadedFileExtension != 'svg') {
            $imageinfo = getimagesize($fileTemp);
            $okMIMETypes = 'image/jpeg,image/pjpeg,image/png,image/x-png,image/gif,image/webp';
            $validFileTypes = explode(",", $okMIMETypes);

            if (!is_int($imageinfo[0]) || !is_int($imageinfo[1]) || !in_array($imageinfo['mime'], $validFileTypes)) {
                throw new \Exception(\JText::_('INVALID FILETYPE'));
            }
        }

        $fileName = preg_replace("/[^A-Za-z0-9]/i", "-", $pathinfo['filename']) . '.' . $pathinfo['extension'];

        $uploadPath = $uploadDir . '/' . $fileName;

        if (file_exists($uploadPath)) {
            $fileName = preg_replace("/[^A-Za-z0-9]/i", "-", $pathinfo['filename']) . '-' . time() . '.' . $pathinfo['extension'];

            $uploadPath = $uploadDir . '/' . $fileName;
        }

        if (!\JFile::upload($fileTemp, $uploadPath)) {
            throw new \Exception(\JText::_('ERROR MOVING FILE'));
        } else {
            return ['filepath' => $uploadPath, 'filename' => $fileName, 'uploadpath' => $uploadDir, 'folder' => $uploadFolder];
        }
    }

    public static function createFolder()
    {
        $input = \JFactory::getApplication()->input;
        $directory = $input->get('dir', '', 'RAW');
        $name = $input->get('name', '', 'RAW');

        $dir = JPATH_SITE . '/' . $directory . '/' . $name;
        if (file_exists($dir)) {
            throw new \Exception("Folder `$name` already exists", 0);
        }

        mkdir($dir, 0777);

        $folder = $name;
        if ($directory != 'images') {
            $directory = preg_replace('/images\//', '', $directory, 1);
            $folder = $directory . '/' . $name;
        }

        return ['message' => "Folder `$name` successfully created.", 'folder' => $folder];
    }
}
