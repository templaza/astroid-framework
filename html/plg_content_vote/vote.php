<?php

/**
 * @package     Joomla.Plugin
 * @subpackage  Content.vote
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

HTMLHelper::_('jquery.framework');

/**
 * Layout variables
 * -----------------
 * @var   string   $context  The context of the content being passed to the plugin
 * @var   object   &$row     The article object
 * @var   object   &$params  The article params
 * @var   integer  $page     The 'page' number
 * @var   array    $parts    The context segments
 * @var   string   $path     Path to this file
 */
jimport('astroid.framework.template');
$template = Astroid\Framework::getTemplate();
if (!$template->params->get('article_rating', 1)) {
   $uri = clone Uri::getInstance();
   $uri->setVar('hitcount', '0');

   // Create option list for voting select box
   $options = array();

   for ($i = 1; $i < 6; $i++) {
      $options[] = HTMLHelper::_('select.option', $i, Text::sprintf('PLG_VOTE_VOTE', $i));
   }
?>
   <form method="post" action="<?php echo htmlspecialchars($uri->toString(), ENT_COMPAT, 'UTF-8'); ?>" class="form-inline">
      <div class="form-group mb-3 mr-sm-3">
         <label class="unseen element-invisible sr-only" for="content_vote_<?php echo (int) $row->id; ?>"><?php echo Text::_('PLG_VOTE_LABEL'); ?></label>
         <?php echo HTMLHelper::_('select.genericlist', $options, 'user_rating', 'class="form-control"', 'value', 'text', '5', 'content_vote_' . (int) $row->id); ?>
      </div>
      <input class="btn btn-primary mb-3" type="submit" name="submit_vote" value="<?php echo Text::_('PLG_VOTE_RATE'); ?>" />
      <input type="hidden" name="task" value="article.vote" />
      <input type="hidden" name="hitcount" value="0" />
      <input type="hidden" name="url" value="<?php echo htmlspecialchars($uri->toString(), ENT_COMPAT, 'UTF-8'); ?>" />
      <?php echo HTMLHelper::_('form.token'); ?>
   </form>

<?php } else { ?>
   <?php
   $rating = (int) $row->rating;
   $rating_count = (int) $row->rating_count;
   ?>
   <div class="article-rating">
      <div class="ui star rating" id="<?php echo 'content_vote_' . (int) $row->id; ?>"></div>
      <div data-votes="<?php echo $rating_count; ?>" class="vote-count article-rating-votecount-<?php echo $row->id; ?>">(<?php echo $rating_count; ?> <?php echo Text::_('TPL_ASTROID_VOTE'); ?><?php echo $rating_count == 1 ? '' : Text::_('TPL_ASTROID_VOTES'); ?>)</div>
      <div class="loading article-rating-loading-<?php echo $row->id; ?> d-none"></div>
      <div class="message d-none article-rating-message-<?php echo $row->id; ?>"></div>
   </div>
   <script>
      (function($) {
         $(function() {
            var ratingtimer = null;
            var ratingtimer2 = null;
            var lastrate = <?php echo $rating; ?>;
            var call = true;
            $('#<?php echo 'content_vote_' . (int) $row->id; ?>').rating({
               initialRating: <?php echo $rating; ?>,
               maxRating: 5,
               onRate: function(value) {
                  if (!call) {
                     call = true;
                     return false;
                  }
                  $.ajax({
                     url: "<?php echo Uri::root(); ?>index.php?option=com_ajax&astroid=rate",
                     option: 'com_ajax',
                     method: 'POST',
                     beforeSend: function() {
                        window.clearTimeout(ratingtimer);
                        window.clearTimeout(ratingtimer2);
                        $('.article-rating-loading-<?php echo $row->id; ?>').removeClass('d-none');
                        $('.article-rating-votecount-<?php echo $row->id; ?>').addClass('d-none');
                        $('.article-rating-message-<?php echo $row->id; ?>').addClass('d-none').text('').removeClass('error').removeClass('success').removeClass('animated').removeClass('fadeIn').removeClass('fadeOut');
                     },
                     data: {
                        vote: value,
                        id: '<?php echo $row->id; ?>',
                        '<?php echo JSession::getFormToken(); ?>': 1
                     },
                     dataType: 'json',
                     error: function() {
                        $('.article-rating-votecount-<?php echo $row->id; ?>').removeClass('d-none');
                        $('.article-rating-loading-<?php echo $row->id; ?>').addClass('d-none');
                     },
                     success: function(response) {
                        $('.article-rating-votecount-<?php echo $row->id; ?>').removeClass('d-none');
                        $('.article-rating-loading-<?php echo $row->id; ?>').addClass('d-none');
                        $('.article-rating-message-<?php echo $row->id; ?>').text(response.message).removeClass('d-none').addClass(response.status).addClass('animated').addClass('fadeIn');
                        ratingtimer = setTimeout(function() {
                           $('.article-rating-message-<?php echo $row->id; ?>').removeClass('fadeIn').addClass('fadeOut');
                           ratingtimer2 = setTimeout(function() {
                              $('.article-rating-message-<?php echo $row->id; ?>').addClass('d-none').text('').removeClass('error').removeClass('success').removeClass('animated').removeClass('fadeIn').removeClass('fadeOut');
                           }, 600);
                        }, 5000);

                        if (response.status == 'success') {
                           var _votes = $('.vote-count').data('votes');
                           _votes = parseInt(_votes) + 1;
                           _text = '<?php echo Text::_('TPL_ASTROID_VOTE'); ?>' + (_votes == 1 ? '' : '<?php echo Text::_('TPL_ASTROID_VOTES'); ?>');
                           $('.vote-count').text('(' + _votes + ' ' + _text + ')').addClass('change');
                           lastrate = response.rating;
                           setTimeout(function() {
                              $('.vote-count').removeClass('change');
                              call = false;
                              $('#<?php echo 'content_vote_' . (int) $row->id; ?>').rating('set rating', lastrate);
                           }, 300);
                        }
                        if (response.status == 'error') {
                           call = false;
                           $('#<?php echo 'content_vote_' . (int) $row->id; ?>').rating('set rating', lastrate);
                        }
                     }
                  });
               }
            });
         });
      })(jQuery);
   </script>
<?php } ?>