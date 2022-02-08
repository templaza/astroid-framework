<?php
/**
 * @package   Astroid Framework
 * @author    JoomDev https://www.joomdev.com
 * @copyright Copyright (C) 2009 - 2020 JoomDev.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */
defined('_JEXEC') or die;

/** @var JDocumentHtml $this */
$app = JFactory::getApplication();
$lang = JFactory::getLanguage();

// Output as HTML5
$this->setHtml5(true);

// Add JavaScript Frameworks
JHtml::_('bootstrap.framework');

// Add filter polyfill for IE8
JHtml::_('behavior.polyfill', array('filter'), 'lte IE 9');

// The Below CSS & JS is only required for Front end editor editor-xtd popups. 
// This is is because the popups reply on older version of Bootstrap (which doesn't exist in Astroid).
$jinput = $app->input;
$option = $jinput->get('option', '', 'STR');
$function = $jinput->get('function', '', 'STR');
$layout = $jinput->get('layout', '', 'STR');

$view = $jinput->get('view','','STR');
$tmpl = $jinput->get('tmpl','','STR');
$e_name = $jinput->get('e_name','','STR');
$editor = $jinput->get('editor','','STR');

$addtemplatejs = false;

//Editor Module button
if($view == 'modules' && $layout =='modal' && $option =='com_modules' && $editor == 'jform_articletext'){
	$addtemplatejs = true;
}
//Editor Menu button
if($view == 'items' && $layout =='modal' && $option =='com_menus' && $editor=='jform_articletext'){
	$addtemplatejs = true;
}
//Editor Contact button
if($view == 'contacts' && $layout =='modal' && $option =='com_contact'){
	$addtemplatejs = true;
}
//Editor Article button
if($view == 'articles' && $layout =='modal' && $option=='com_content' && $editor=='jform_articletext'){
	$addtemplatejs = true;
}
//Editor Image button
if($option == 'com_media' && $tmpl =='component'){
	$addtemplatejs = false;
}
//Editor Page break button
if($view == 'article' && $option == 'com_content' && $layout == 'pagebreak' && $e_name=='jform_articletext'){
	$addtemplatejs = true;
}
//Content History
if($view == 'history' && $option == 'com_contenthistory' && $layout == 'modal'){
	$addtemplatejs = true;
}
 
if($addtemplatejs){
	JHtml::_('script', juri::root().'media/jui/js/bootstrap.min.js', array('version' => 'auto', 'relative' => true));
	JHtml::_('script', 'isis.js', array('version' => 'auto', 'relative' => true)); 
}

JHtml::_('stylesheet', 'isis/isis.css', array('version' => 'auto', 'relative' => true));
 
// Add html5 shiv
JHtml::_('script', 'jui/html5.js', array('version' => 'auto', 'relative' => true, 'conditional' => 'lt IE 9'));

// Load optional RTL Bootstrap CSS
JHtml::_('bootstrap.loadCss', false, $this->direction);

// Load specific language related CSS
JHtml::_('stylesheet', 'administrator/language/' . $lang->getTag() . '/' . $lang->getTag() . '.css', array('version' => 'auto'));
?>
<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
   <head>
   <jdoc:include type="head" />
   <astroid:include type="head-styles" /> <!-- head styles -->
   <astroid:include type="head-scripts" /> <!-- head scripts -->
</head>
<body class="contentpane component">
<jdoc:include type="message" />
<jdoc:include type="component" />
<?php Astroid\Helper\Head::scripts(); // site scripts ?>
<astroid:include type="body-scripts" /> <!-- body scripts -->
</body>
</html>
