<?php
defined('JPATH_BASE') or die;
use Joomla\CMS\Language\Text;

$msgList = $displayData['msgList'];
?>
<div id="system-message-container">
   <?php if (is_array($msgList) && !empty($msgList)) : ?>
      <div id="system-message">
         <?php foreach ($msgList as $type => $msgs) : ?>
            <div class="alert alert-<?php echo $type == "error" ? "danger" : ($type == "message" ? "info" : $type); ?> alert-dismissible fade show">
               <?php // This requires JS so we should add it through JS. Progressive enhancement and stuff. 
               ?>
               <?php if (!empty($msgs)) : ?>
                  <h4 class="alert-heading mb-0"><?php echo Text::_($type); ?></h4>
                  <div>
                     <?php foreach ($msgs as $msg) : ?>
                        <div class="alert-message"><?php echo $msg; ?></div>
                     <?php endforeach; ?>
                  </div>
               <?php endif; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
         <?php endforeach; ?>
      </div>
   <?php endif; ?>
</div>