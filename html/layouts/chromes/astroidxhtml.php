<?php

/**
 * @package   Astroid Framework
 * @author    JoomDev https://www.joomdev.com
 * @copyright Copyright (C) 2009 - 2020 JoomDev.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

defined('_JEXEC') or die;
extract($displayData);

$moduleTag     = $params->get('module_tag', 'div');
$headerTag     = htmlspecialchars($params->get('header_tag', 'h5'), ENT_COMPAT, 'UTF-8');
$bootstrapSize = (int) $params->get('bootstrap_size', 0);
$moduleClass   = $bootstrapSize != 0 ? ' span' . $bootstrapSize : '';

// Temporarily store header class in variable
$headerClass = $params->get('header_class');
$headerClass = $headerClass ? ' class="module-title ' . htmlspecialchars($headerClass, ENT_COMPAT, 'UTF-8') . '"' : ' class="module-title"';

$content = trim($module->content);

if (!empty($content)) :
?> <<?php
    echo $moduleTag;
    ?> class="moduletable <?php
                            echo htmlspecialchars($params->get('moduleclass_sfx'), ENT_COMPAT, 'UTF-8') . $moduleClass;
                            ?>">
        <?php
        if ($module->showtitle != 0) :
        ?>
            <<?php
                echo $headerTag . $headerClass . '>' . $module->title;
                ?></<?php
                    echo $headerTag;
                    ?>> <?php
                    endif;
                        ?> <?php
                            echo $content;
                            ?> </<?php
                                    echo $moduleTag;
                                    ?>> <?php
                                    endif;
                                        ?>