<?php
/**
 * @package Jollyany
 * @author TemPlaza http://www.templaza.com
 * @copyright Copyright (c) 2010 - 2022 Jollyany
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

$hideLinks = $app->input->getBool('hidemainmenu');
if ($hideLinks) {
	return;
}
?>
<?php if ($app->getIdentity()->authorise('core.manage', 'com_modules')) : ?>
<a class="header-item-content astroid-clear-cache"
   href="#"
   title="<?php echo Text::_('Astroid Clear Cache'); ?>">
    <div class="header-item-icon">
        <div class="w-auto">
            <span class="icon-refresh" aria-hidden="true"></span>
        </div>
    </div>
    <div class="header-item-text">
		<?php echo Text::_('Astroid Clear Cache'); ?>
    </div>
</a>
<?php endif; ?>