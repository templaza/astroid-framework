<?php
/**
 * @package   Astroid Framework
 * @author    Astroid Framework https://astroidframe.work
 * @copyright Copyright (C) 2023 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */
// No direct access.

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Helper\AuthenticationHelper;

/** @var JDocumentHtml $this */

$app = JFactory::getApplication();


// Output as HTML5
$this->setHtml5(true);

// Add html5 shiv
JHtml::_('script', 'jui/html5.js', array('version' => 'auto', 'relative' => true, 'conditional' => 'lt IE 9'));



// Add JavaScript Frameworks
JHtml::_('bootstrap.framework');

$twofactormethods = JAuthenticationHelper::getTwoFactorMethods();
?>
<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<jdoc:include type="head" />

</head>
<body>
	<jdoc:include type="message" />
	<div id="frame" class="outline">
			<?php if ($app->get('offline_image')) : ?>
				<?php echo HTMLHelper::_('image', $app->get('offline_image'), $sitename, [], false, 0); ?>
			<?php endif; ?>		
		<h1>
			<?php echo htmlspecialchars($app->get('sitename'), ENT_COMPAT, 'UTF-8'); ?>
		</h1>
	<?php if ($app->get('display_offline_message', 1) == 1 && str_replace(' ', '', $app->get('offline_message')) !== '') : ?>
		<p>
			<?php echo $app->get('offline_message'); ?>
		</p>
	<?php elseif ($app->get('display_offline_message', 1) == 2 && str_replace(' ', '', JText::_('JOFFLINE_MESSAGE')) !== '') : ?>
		<p>
			<?php echo JText::_('JOFFLINE_MESSAGE'); ?>
		</p>
	<?php endif; ?>
	<form action="<?php echo JRoute::_('index.php', true); ?>" method="post" id="form-login">
	<fieldset class="input">
		<p id="form-login-username">
			<label for="username"><?php echo JText::_('JGLOBAL_USERNAME'); ?></label>
			<input name="username" id="username" type="text" class="inputbox" alt="<?php echo JText::_('JGLOBAL_USERNAME'); ?>" autocomplete="off" autocapitalize="none" />
		</p>
		<p id="form-login-password">
			<label for="passwd"><?php echo JText::_('JGLOBAL_PASSWORD'); ?></label>
			<input type="password" name="password" class="inputbox" alt="<?php echo JText::_('JGLOBAL_PASSWORD'); ?>" id="passwd" />
		</p>
		<?php if (count($twofactormethods) > 1) : ?>
			<p id="form-login-secretkey">
				<label for="secretkey"><?php echo JText::_('JGLOBAL_SECRETKEY'); ?></label>
				<input type="text" name="secretkey" class="inputbox" alt="<?php echo JText::_('JGLOBAL_SECRETKEY'); ?>" id="secretkey" />
			</p>
		<?php endif; ?>
		<p id="submit-buton">
			<input type="submit" name="Submit" class="button login" value="<?php echo JText::_('JLOGIN'); ?>" />
		</p>
		<input type="hidden" name="option" value="com_users" />
		<input type="hidden" name="task" value="user.login" />
		<input type="hidden" name="return" value="<?php echo base64_encode(JUri::base()); ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</fieldset>
	</form>
	</div>
</body>
<style type="text/css">
body{padding:0;margin:0;font-family:arial,helvetica,sans-serif;font-size:14px;color:#333;text-align:center}img{margin-right:auto;margin-left:auto;border:0}#frame{max-width:400px;padding:20px;margin:20px auto}#frame img{max-width:100%;height:auto}#frame form{text-align:left}.outline{padding:2px;background:#fff;border:1px solid #ccc}form{margin:auto}form br{display:none}form p{padding:.5em 0;margin:0}form fieldset{padding:.2em;margin:0;border:0}label{display:block;margin:5px 0 2px}input{box-sizing:border-box;width:100%;padding:5px 10px;font-family:inherit;font-size:inherit;border:1px solid #0e67a1}input.button{width:auto;color:#fff;cursor:pointer;background-color:#006dcc;border-color:#04c;-webkit-appearance:none;-moz-appearance:none;appearance:none}input.button:hover{background-color:#04c}fieldset.input p{clear:left}#frmlogin{margin:0 10px}#frmlogin fieldset.button{text-align:right}.alert{padding:8px 25px 8px 14px;text-align:left;background:none repeat scroll 0 0 #fff;border:1px solid #ccc}.alert h4{margin:5px 0;color:red}.alert p{padding:0;margin:0}.alert .close{position:relative;top:-2px;right:-20px;float:right;font-size:24px;line-height:18px;cursor:pointer}.login{margin-top:5px}
</style>
</html>
