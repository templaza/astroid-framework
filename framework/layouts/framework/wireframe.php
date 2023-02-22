<?php
/**
 * @package   Astroid Framework
 * @author    Astroid Framework Team https://astroidframe.work
 * @copyright Copyright (C) 2023 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */
defined('_JEXEC') or die;
extract($displayData);
?>
<div class="p-5 my-2 text-center bg-dark text-white">
   <h3><i style="color:<?php echo $element->color; ?>" class="<?php echo $element->icon; ?>"></i> <?php echo JText::_($element->title); ?></h3><p><?php echo JText::_($element->description); ?></p>
   <?php
   if (!empty($position = $params->get('position', ''))) {
      echo '<p><em>Position: ' . $position . '</em></p>';
   }
   ?>
</div>