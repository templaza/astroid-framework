<?php

/**
 * @package   Astroid Framework
 * @author    JoomDev https://www.joomdev.com
 * @copyright Copyright (C) 2009 - 2020 JoomDev.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

namespace Astroid\Helper;

defined('_JEXEC') or die;

class Constants 
{
    public static $astroid_version = '2.5.18';
    public static $fontawesome_version = '6.0.0';
    public static $animatecss_version = '3.7.0';
    public static $forum_link = 'https://github.com/templaza/astroid-framework/issues';
    public static $documentation_link = 'https://github.com/templaza/astroid-framework/wiki';
    public static $video_tutorial_link = 'https://www.youtube.com/channel/UCUHl1uU0Ofkyo-1ke-K4_xg';
    public static $github_link = 'https://github.com/templaza/astroid-framework';
    public static $download_link = 'https://github.com/templaza/astroid-framework/releases/latest';
    public static $releases_link = 'https://github.com/templaza/astroid-framework/releases';
    public static $astroid_link = 'https://astroidframe.work/';
    public static $joomdev_products_link = 'https://astroidframe.work/resources/template-providers';
    public static $joomdev_templates_link = 'https://astroidframe.work/resources/template-providers';
    public static $jd_builder_link = 'https://astroidframe.work/';

    public static $bootstrap_colors = [
        '' => 'TPL_COLOR_DEFAULT',
        'blue' => 'TPL_COLOR_BLUE',
        'indigo' => 'TPL_COLOR_INDIGO',
        'purple' => 'TPL_COLOR_PURPLE',
        'pink' => 'TPL_COLOR_PINK',
        'red' => 'TPL_COLOR_RED',
        'orange' => 'TPL_COLOR_ORANGE',
        'yellow' => 'TPL_COLOR_YELLOW',
        'green' => 'TPL_COLOR_GREEN',
        'teal' => 'TPL_COLOR_TEAL',
        'cyan' => 'TPL_COLOR_CYAN',
        'white' => 'TPL_COLOR_WHITE',
        'gray100' => 'TPL_COLOR_LIGHT_GREY',
        'gray600' => 'TPL_COLOR_GREY',
        'gray800' => 'TPL_COLOR_GREY_DARK',
        'custom' => 'TPL_COLOR_CUSTOM'
    ];

    public static $animations = [
        '' => ['' => 'None'],
        'Attention Seekers' => [
            'bounce' => 'bounce',
            'flash' => 'flash',
            'pulse' => 'pulse',
            'rubberBand' => 'rubberBand',
            'shake' => 'shake',
            'swing' => 'swing',
            'tada' => 'tada',
            'wobble' => 'wobble',
            'jello' => 'jello',
            'heartBeat' => 'heartBeat'
        ],
        'Bouncing Entrances' => [
            'bounceIn' => 'bounceIn',
            'bounceInDown' => 'bounceInDown',
            'bounceInLeft' => 'bounceInLeft',
            'bounceInRight' => 'bounceInRight',
            'bounceInUp' => 'bounceInUp'
        ],
        'Fading Entrances' => [
            'fadeIn' => 'fadeIn',
            'fadeInDown' => 'fadeInDown',
            'fadeInDownBig' => 'fadeInDownBig',
            'fadeInLeft' => 'fadeInLeft',
            'fadeInLeftBig' => 'fadeInLeftBig',
            'fadeInRight' => 'fadeInRight',
            'fadeInRightBig' => 'fadeInRightBig',
            'fadeInUp' => 'fadeInUp',
            'fadeInUpBig' => 'fadeInUpBig'
        ],
        'Flippers Entrances' => [
            'flip' => 'flip',
            'flipInX' => 'flipInX',
            'flipInY' => 'flipInY'
        ],
        'Lightspeed Entrances' => [
            'lightSpeedIn' => 'lightSpeedIn',
        ],
        'Rotating Entrances' => [
            'rotateIn' => 'rotateIn',
            'rotateInDownLeft' => 'rotateInDownLeft',
            'rotateInDownRight' => 'rotateInDownRight',
            'rotateInUpLeft' => 'rotateInUpLeft',
            'rotateInUpRight' => 'rotateInUpRight'
        ],
        'Sliding Entrances' => [
            'slideInUp' => 'slideInUp',
            'slideInDown' => 'slideInDown',
            'slideInLeft' => 'slideInLeft',
            'slideInRight' => 'slideInRight',
        ],
        'Zoom Entrances' => [
            'zoomIn' => 'zoomIn',
            'zoomInDown' => 'zoomInDown',
            'zoomInLeft' => 'zoomInLeft',
            'zoomInRight' => 'zoomInRight',
            'zoomInUp' => 'zoomInUp',
        ],
        'Specials' => [
            'hinge' => 'hinge',
            'jackInTheBox' => 'jackInTheBox',
            'rollIn' => 'rollIn',
        ],
    ];

    public static $menu_animations = [
        '' => ['' => 'None'],
        'Bouncing Entrances' => [
            'bounceIn' => 'bounceIn',
            'bounceInDown' => 'bounceInDown',
            'bounceInLeft' => 'bounceInLeft',
            'bounceInRight' => 'bounceInRight',
            'bounceInUp' => 'bounceInUp'
        ],
        'Fading Entrances' => [
            'fadeIn' => 'fadeIn',
            'fadeInDown' => 'fadeInDown',
            'fadeInDownBig' => 'fadeInDownBig',
            'fadeInLeft' => 'fadeInLeft',
            'fadeInLeftBig' => 'fadeInLeftBig',
            'fadeInRight' => 'fadeInRight',
            'fadeInRightBig' => 'fadeInRightBig',
            'fadeInUp' => 'fadeInUp',
            'fadeInUpBig' => 'fadeInUpBig'
        ],
        'Rotating Entrances' => [
            'rotateIn' => 'rotateIn',
            'rotateInDownLeft' => 'rotateInDownLeft',
            'rotateInDownRight' => 'rotateInDownRight',
            'rotateInUpLeft' => 'rotateInUpLeft',
            'rotateInUpRight' => 'rotateInUpRight'
        ],
        'Sliding Entrances' => [
            'slideInUp' => 'slideInUp',
            'slideInDown' => 'slideInDown',
            'slideInLeft' => 'slideInLeft',
            'slideInRight' => 'slideInRight'
        ],
        'Zoom Entrances' => [
            'zoomIn' => 'zoomIn',
            'zoomInDown' => 'zoomInDown',
            'zoomInLeft' => 'zoomInLeft',
            'zoomInRight' => 'zoomInRight',
            'zoomInUp' => 'zoomInUp'
        ],
        'Specials' => [
            'hinge' => 'hinge',
            'jackInTheBox' => 'jackInTheBox',
            'rollIn' => 'rollIn',
            'rollOut' => 'rollOut'
        ],
    ];

    public static $icons = [
        [
            'fas fa-long-arrow-alt-up' => 'Alternate Long Arrow Up',
            'fas fa-arrow-up' => 'arrow-up',
            'fas fa-arrow-circle-up' => 'Arrow Circle Up',
            'fas fa-arrow-alt-circle-up' => 'Alternate Arrow Circle Up',
            'fas fa-angle-double-up' => 'Angle Double Up',
            'fas fa-sort-up' => 'Sort Up (Ascending)',
            'fas fa-level-up-alt' => 'Level Up Alternate',
            'fas fa-cloud-upload-alt' => 'Cloud Upload Alternate',
            'fas fa-chevron-up' => 'chevron-up',
            'fas fa-chevron-circle-up' => 'Chevron Circle Up',
            'fas fa-hand-point-up' => 'Hand Pointing Up',
            'far fa-hand-point-up' => 'Hand Pointing Up',
            'fas fa-caret-square-up' => 'Caret Square Up',
            'far fa-caret-square-up' => 'Caret Square Up',
        ]
    ];

    public static $preloder_animations = [
        [
            'audio' => ['label' => 'Audio', 'image' => 'preloader/audio.svg'],
            'ball_triangle' => ['label' => 'Ball Triangle', 'image' => 'preloader/ball-triangle.svg'],
            'bars' => ['label' => 'Bars', 'image' => 'preloader/bars.svg'],
            'circles' => ['label' => 'Circles', 'image' => 'preloader/circles.svg'],
            'grid' => ['label' => 'Grid', 'image' => 'preloader/grid.svg'],
            'oval' => ['label' => 'Oval', 'image' => 'preloader/oval.svg'],
            'puff' => ['label' => 'Puff', 'image' => 'preloader/puff.svg'],
            'rings' => ['label' => 'Rings', 'image' => 'preloader/rings.svg'],
            'spinning_circles' => ['label' => 'Spinning Circles', 'image' => 'preloader/spinning-circles.svg'],
            'tail_spin' => ['label' => 'Tail Spin', 'image' => 'preloader/tail-spin.svg'],
            'three_dots' => ['label' => 'Three Dots', 'image' => 'preloader/three-dots.svg'],
        ]
    ];

    public static $social_profiles = [
        ['title' => 'Behance', 'link' => '', 'icons' => ['fab fa-behance', 'fab fa-behance-square'], 'color' => '#2252FF', 'enabled' => false, 'icon' => 'fab fa-behance'],
        ['title' => 'Dribbble', 'link' => '', 'icons' => ['fab fa-dribbble', 'fab fa-dribbble-square'], 'color' => '#F10A77', 'enabled' => false, 'icon' => 'fab fa-dribbble'],
        ['title' => 'Facebook', 'link' => '', 'icons' => ['fab fa-facebook-f', 'fab fa-facebook', 'fab fa-facebook-square'], 'color' => '#39539E', 'enabled' => false, 'icon' => 'fab fa-facebook-f'],
        ['title' => 'Flickr', 'link' => '', 'icons' => ['fab fa-flickr'], 'color' => '#0054E3', 'enabled' => false, 'icon' => 'fab fa-flickr'],
        ['title' => 'GitHub', 'link' => '', 'icons' => ['fab fa-github', 'fab fa-github-square', 'fab fa-github-alt'], 'color' => '#171515', 'enabled' => false, 'icon' => 'fab fa-github'],
        ['title' => 'Instagram', 'link' => '', 'icons' => ['fab fa-instagram'], 'color' => '#467FAA', 'enabled' => false, 'icon' => 'fab fa-instagram'],
        ['title' => 'LinkedIn', 'link' => '', 'icons' => ['fab fa-linkedin-in', 'fab fa-linkedin'], 'color' => '#006FB8', 'enabled' => false, 'icon' => 'fab fa-linkedin-in'],
        ['title' => 'Messenger', 'link' => '', 'icons' => ['fab fa-facebook-messenger'], 'color' => '#3876C4', 'enabled' => false, 'icon' => 'fab fa-facebook-messenger'],
        ['title' => 'Pinterest', 'link' => '', 'icons' => ['fab fa-pinterest', 'fab fa-pinterest-square', 'fab fa-pinterest-p'], 'color' => '#DB0000', 'enabled' => false, 'icon' => 'fab fa-pinterest'],
        ['title' => 'reddit', 'link' => '', 'icons' => ['fab fa-reddit', 'fab fa-reddit-square', 'fab fa-reddit-alien'], 'color' => '#FF2400', 'enabled' => false, 'icon' => 'fab fa-reddit'],
        ['title' => 'Skype', 'link' => '', 'icons' => ['fab fa-skype'], 'color' => '#00A6F7', 'enabled' => false, 'icon' => 'fab fa-skype'],
        ['title' => 'Slack', 'link' => '', 'icons' => ['fab fa-slack', 'fab fa-slack-hash'], 'color' => '#50364C', 'enabled' => false, 'icon' => 'fab fa-slack'],
        ['title' => 'SoundCloud', 'link' => '', 'icons' => ['fab fa-soundcloud'], 'color' => '#FF0000', 'enabled' => false, 'icon' => 'fab fa-soundcloud'],
        ['title' => 'Spotify', 'link' => '', 'icons' => ['fab fa-spotify'], 'color' => '#00E155', 'enabled' => false, 'icon' => 'fab fa-spotify'],
        ['title' => 'Twitter', 'link' => '', 'icons' => ['fab fa-twitter', 'fab fa-twitter-square'], 'color' => '#3DA9F6', 'enabled' => false, 'icon' => 'fab fa-twitter'],
        ['title' => 'Telegram', 'link' => '', 'icons' => ['fab fa-telegram-plane', 'fab fa-telegram'], 'color' => '#004056', 'enabled' => false, 'icon' => 'fab fa-telegram-plane'],
        ['title' => 'Tumblr', 'link' => '', 'icons' => ['fab fa-tumblr', 'fab fa-tumblr-square'], 'color' => '#00263C', 'enabled' => false, 'icon' => 'fab fa-tumblr'],
        ['title' => 'VK', 'link' => '', 'icons' => ['fab fa-vk'], 'color' => '#4273AD', 'enabled' => false, 'icon' => 'fab fa-vk'],
        ['title' => 'WhatsApp', 'link' => '', 'icons' => ['fab fa-whatsapp', 'fab fa-whatsapp-square'], 'color' => '#00C033', 'enabled' => false, 'icon' => 'fab fa-whatsapp'],
        ['title' => 'YouTube', 'link' => '', 'icons' => ['fab fa-youtube', 'fab fa-youtube-square'], 'color' => '#DE0000', 'enabled' => false, 'icon' => 'fab fa-youtube'],
    ];

    public static $preloaders = [
        'rotating-plane' => [
            'name' => 'rotating-plane',
            'code' => '<div class="sk-rotating-plane"></div>',
        ],
        'fading-circle' => [
            'name' => 'fading-circle',
            'code' => '<div class="sk-fading-circle"><div class="sk-circle1 sk-circle"></div><div class="sk-circle2 sk-circle"></div><div class="sk-circle3 sk-circle"></div><div class="sk-circle4 sk-circle"></div><div class="sk-circle5 sk-circle"></div><div class="sk-circle6 sk-circle"></div><div class="sk-circle7 sk-circle"></div><div class="sk-circle8 sk-circle"></div><div class="sk-circle9 sk-circle"></div><div class="sk-circle10 sk-circle"></div><div class="sk-circle11 sk-circle"></div><div class="sk-circle12 sk-circle"></div></div>',
        ],
        'folding-cube' => [
            'name' => 'folding-cube',
            'code' => '<div class="sk-folding-cube"><div class="sk-cube1 sk-cube"></div><div class="sk-cube2 sk-cube"></div><div class="sk-cube4 sk-cube"></div><div class="sk-cube3 sk-cube"></div></div>',
        ],
        'double-bounce' => [
            'name' => 'double-bounce',
            'code' => '<div class="sk-double-bounce"><div class="sk-child sk-double-bounce1"></div><div class="sk-child sk-double-bounce2"></div></div>',
        ],
        'wave' => [
            'name' => 'wave',
            'code' => '<div class="sk-wave"><div class="sk-rect sk-rect1"></div><div class="sk-rect sk-rect2"></div><div class="sk-rect sk-rect3"></div><div class="sk-rect sk-rect4"></div><div class="sk-rect sk-rect5"></div></div>',
        ],
        'wandering-cubes' => [
            'name' => 'wandering-cubes',
            'code' => '<div class="sk-wandering-cubes"><div class="sk-cube sk-cube1"></div><div class="sk-cube sk-cube2"></div></div>',
        ],
        'pulse' => [
            'name' => 'pulse',
            'code' => '<div class="sk-spinner sk-spinner-pulse"></div>',
        ],
        'chasing-dots' => [
            'name' => 'chasing-dots',
            'code' => '<div class="sk-chasing-dots"><div class="sk-child sk-dot1"></div><div class="sk-child sk-dot2"></div></div>',
        ],
        'three-bounce' => [
            'name' => 'three-bounce',
            'code' => '<div class="sk-three-bounce"><div class="sk-child sk-bounce1"></div><div class="sk-child sk-bounce2"></div><div class="sk-child sk-bounce3"></div></div>',
        ],
        'circle' => [
            'name' => 'circle',
            'code' => '<div class="sk-circle"><div class="sk-circle1 sk-child"></div><div class="sk-circle2 sk-child"></div><div class="sk-circle3 sk-child"></div><div class="sk-circle4 sk-child"></div><div class="sk-circle5 sk-child"></div><div class="sk-circle6 sk-child"></div><div class="sk-circle7 sk-child"></div><div class="sk-circle8 sk-child"></div><div class="sk-circle9 sk-child"></div><div class="sk-circle10 sk-child"></div><div class="sk-circle11 sk-child"></div><div class="sk-circle12 sk-child"></div></div>',
        ],
        'cube-grid' => [
            'name' => 'cube-grid',
            'code' => '<div class="sk-cube-grid"><div class="sk-cube sk-cube1"></div><div class="sk-cube sk-cube2"></div><div class="sk-cube sk-cube3"></div><div class="sk-cube sk-cube4"></div><div class="sk-cube sk-cube5"></div><div class="sk-cube sk-cube6"></div><div class="sk-cube sk-cube7"></div><div class="sk-cube sk-cube8"></div><div class="sk-cube sk-cube9"></div></div>',
        ],
        'bouncing-loader' => [
            'name' => 'bouncing-loader',
            'code' => '<div class="bouncing-loader"><div></div><div></div><div></div></div>',
        ],
        'donut' => [
            'name' => 'donut',
            'code' => '<div class="donut"></div>',
        ],
    ];

    public static $preloadersFont = [
        'spinner' => [
            'name' => 'fas fa-spinner fa-spin',
            'code' => '<div class="fas fa-spinner fa-spin" style="font-size: 40px; margin: 0 auto;"></div>',
        ],
        'circle-notch' => [
            'name' => 'fas fa-circle-notch fa-spin',
            'code' => '<div class="fas fa-circle-notch fa-spin" style="font-size: 40px;margin: 0 auto;"></div>',
        ],
        'sync' => [
            'name' => 'fas fa-sync fa-spin',
            'code' => '<div class="fas fa-sync fa-spin fa-spin" style="font-size: 40px;margin: 0 auto;"></div>',
        ],
        'cog' => [
            'name' => 'fas fa-cog fa-spin',
            'code' => '<div class="fas fa-cog fa-spin" style="font-size: 40px;margin: 0 auto;"></div>',
        ],
        'spinner fa-pulse' => [
            'name' => 'fas fa-spinner fa-pulse',
            'code' => '<div class="fas fa-spinner fa-pulse" style="font-size: 40px;margin: 0 auto;"></div>',
        ],
        'stroopwafel' => [
            'name' => 'fas fa-stroopwafel fa-spin',
            'code' => '<div class="fas fa-stroopwafel fa-spin" style="font-size: 40px;margin: 0 auto;"></div>',
        ],
        'sun' => [
            'name' => 'fas fa-sun fa-spin',
            'code' => '<div class="fas fa-sun fa-spin" style="font-size: 40px;margin: 0 auto;"></div>'
        ],
        'sun-fr' => [
            'name' => 'far fa-sun fa-spin',
            'code' => '<div class="far fa-sun fa-spin" style="font-size: 40px;margin: 0 auto;"></div>'
        ],
        'asterisk' => [
            'name' => 'fas fa-asterisk fa-spin',
            'code' => '<div class="fas fa-asterisk fa-spin" style="font-size: 40px;margin: 0 auto;"></div>'
        ],
        'atom' => [
            'name' => 'fas fa-atom fa-spin',
            'code' => '<div class="fas fa-atom fa-spin" style="font-size: 40px;margin: 0 auto;"></div>'
        ],
        'certificate' => [
            'name' => 'fas fa-certificate fa-spin',
            'code' => '<div class="fas fa-certificate fa-spin" style="font-size: 40px;margin: 0 auto;"></div>'
        ],
        'compact-disc' => [
            'name' => 'fas fa-compact-disc fa-spin',
            'code' => '<div class="fas fa-compact-disc fa-spin" style="font-size: 40px;margin: 0 auto;"></div>'
        ],
        'compass' => [
            'name' => 'fas fa-compass fa-spin',
            'code' => '<div class="fas fa-compact-compass fa-spin" style="font-size: 40px;margin: 0 auto;"></div>'
        ],
        'compass' => [
            'name' => 'far fa-compass fa-spin',
            'code' => '<div class="far fa-compass fa-spin" style="font-size: 40px;margin: 0 auto;"></div>'
        ],
        'crosshairs' => [
            'name' => 'fas fa-crosshairs fa-spin',
            'code' => '<div class="fas fa-crosshairs fa-spin" style="font-size: 40px;margin: 0 auto;"></div>'
        ],
        'dharmachakra' => [
            'name' => 'fas fa-dharmachakra fa-spin',
            'code' => '<div class="fas fa-dharmachakra fa-spin" style="font-size: 40px;margin: 0 auto;"></div>'
        ],
        'bahai' => [
            'name' => 'fas fa-bahai fa-spin',
            'code' => '<div class="fas fa-bahai fa-spin" style="font-size: 40px;margin: 0 auto;"></div>'
        ],
        'life-ring' => [
            'name' => 'fas fa-life-ring fa-spin',
            'code' => '<div class="fas fa-life-ring fa-spin" style="font-size: 40px;margin: 0 auto;"></div>'
        ],
        'life-ring' => [
            'name' => 'fas fa-life-ring fa-spin',
            'code' => '<div class="fas fa-life-ring fa-spin" style="font-size: 40px;margin: 0 auto;"></div>'
        ],
        'yin-yang' => [
            'name' => 'fas fa-yin-yang fa-spin',
            'code' => '<div class="fas fa-yin-yang fa-spin" style="font-size: 40px;margin: 0 auto;"></div>'
        ],
        'sync-alt' => [
            'name' => 'fas fa-sync-alt fa-spin',
            'code' => '<div class="fas fa-sync-alt fa-spin" style="font-size: 40px;margin: 0 auto;"></div>'
        ],

    ];
    public static $layout_grids = [
        [12],
        [10, 2],
        [9, 3],
        [8, 4],
        [7, 5],
        [6, 6],
        [4, 4, 4],
        [3, 6, 3],
        [2, 6, 4],
        [3, 3, 3, 3],
        [2, 2, 2, 2, 2, 2]
    ];

    public function getConstant($variable)
    {
        if (isset($this->{"$" . $variable})) {
            return $this->{"$" . $variable};
        } else {
            return null;
        }
    }
}
