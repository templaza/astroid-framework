<?php
/**
 * @package   Astroid Framework
 * @author    JoomDev https://www.joomdev.com
 * @copyright Copyright (C) 2009 - 2020 JoomDev.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

defined('_JEXEC') or die;
$params  = $displayData->params;
$attribs 		= json_decode($displayData->attribs);
$images 		= json_decode($displayData->images);
$full_image 	= '';

if(isset($attribs->spfeatured_image) && $attribs->spfeatured_image != '') {
	$full_image = $attribs->spfeatured_image;
} elseif(isset($images->image_fulltext) && !empty($images->image_fulltext)) {
	$full_image = $images->image_fulltext;
}
?>

<?php if(!empty($full_image) || (isset($images->image_fulltext) && !empty($images->image_fulltext))) { ?>
	<?php $imgfloat = (empty($images->float_fulltext)) ? $params->get('float_fulltext') : $images->float_fulltext; ?>
	<figure class="text-<?php echo htmlspecialchars($imgfloat); ?> entry-image full-image"> <img
		<?php if ($images->image_fulltext_caption):
		echo 'class="caption"' . ' title="' . htmlspecialchars($images->image_fulltext_caption) . '"';
		endif; ?>
		src="<?php echo htmlspecialchars($full_image); ?>" alt="<?php echo htmlspecialchars($images->image_fulltext_alt); ?>" itemprop="image" class="img-fluid"/>
		<?php if (ASTROID_JOOMLA_VERSION > 3 && $images->image_fulltext_caption !== '') : ?>
			<figcaption class="caption"><?php echo htmlspecialchars($images->image_fulltext_caption, ENT_COMPAT, 'UTF-8'); ?></figcaption>
		<?php endif; ?>
	</figure>
<?php } ?>
