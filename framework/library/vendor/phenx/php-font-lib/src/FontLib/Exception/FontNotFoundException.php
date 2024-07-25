<?php
/**
 * @package   Astroid Framework
 * @author    Astroid Framework https://astroidframe.work
 * @copyright Copyright (C) 2023 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */
namespace FontLib\Exception;

class FontNotFoundException extends \Exception
{
    public function __construct($fontPath)
    {
        $this->message = 'Font not found in: ' . $fontPath;
    }
}