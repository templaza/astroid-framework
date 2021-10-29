<?php

/**
 * @package   Astroid Framework
 * @author    JoomDev https://www.joomdev.com
 * @copyright Copyright (C) 2009 - 2020 JoomDev.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 * 	DO NOT MODIFY THIS FILE DIRECTLY AS IT WILL BE OVERWRITTEN IN THE NEXT UPDATE
 *  You can easily override all files under /frontend/ folder.
 * 	Just copy the file to ROOT/templates/YOURTEMPLATE/html/frontend/header/ folder to create and override
 */
// No direct access.
defined('_JEXEC') or die;
extract($displayData);

$params = Astroid\Helper::getPluginParams();
if (!$params->get('astroid_manager_loader', 1)) {
    return;
}

$document = Astroid\Framework::getDocument();

$document->addStyleDeclaration('.falling-astroid-container{position:fixed;width:100%;height:100%;top:0;left:0;background:rgba(0,0,0,.7)!important;z-index:9999999;transition:.2s linear}.falling-astroid{position:absolute;width:100%;height:100%;top:0;left:0;transform:rotate(-45deg)}.falling-astroid span{position:absolute;height:20%;width:2px;background:#999}.falling-astroid span:nth-child(1){left:20%;animation:lf .6s linear infinite;animation-delay:-5s}.falling-astroid span:nth-child(2){left:40%;animation:lf2 .8s linear infinite;animation-delay:-1s}.falling-astroid span:nth-child(3){left:60%;animation:lf3 .6s linear infinite}.falling-astroid span:nth-child(4){left:80%;animation:lf4 .5s linear infinite;animation-delay:-3s}@keyframes lf{0%{top:200%}to{top:-200%;opacity:0}}@keyframes lf2{0%{top:200%}to{top:-200%;opacity:0}}@keyframes lf3{0%{top:200%}to{top:-100%;opacity:0}}@keyframes lf4{0%{top:200%}to{top:-100%;opacity:0}}@keyframes fazer1{0%{top:0}to{top:-120px;opacity:0;transform:scale(.5)}}@keyframes fazer2{0%{top:0}to{top:-150px;opacity:0;transform:scale(.4)}}@keyframes fazer3{0%{top:0}to{top:-100px;opacity:0;transform:scale(.3)}}@keyframes fazer4{0%{top:0}to{top:-200px;opacity:0;transform:scale(.2)}}@keyframes speeder{0%,90%{transform:translate(2px,1px) rotate(0)}10%{transform:translate(-1px,-3px) rotate(-1deg)}20%{transform:translate(-2px) rotate(1deg)}30%{transform:translate(1px,2px) rotate(0)}40%{transform:translate(1px,-1px) rotate(1deg)}50%{transform:translate(-1px,3px) rotate(-1deg)}60%{transform:translate(-1px,1px) rotate(0)}70%{transform:translate(3px,1px) rotate(-1deg)}80%{transform:translate(-2px,-1px) rotate(1deg)}to{transform:translate(1px,-2px) rotate(-1deg)}}.falling-astroid-imgs{transform:rotate(-45deg);position:absolute;z-index:1;top:30px;left:10px}.falling-astroid-img{background:url(' . \JURI::root() . 'media/astroid/assets/images/astroid/1.png) center no-repeat;background-size:contain!important;width:90px;height:90px}.falling-astroid-logo{animation:speeder .4s linear infinite;width:100px;height:100px;position:absolute;top:50%;left:50%;margin-left:-75px;margin-top:-75px}.falling-astroid-imgs span{position:absolute;background-size:contain!important}.falling-astroid-imgs span:nth-child(1){background:url(' . \JURI::root() . 'media/astroid/assets/images/astroid/2.png) center no-repeat;width:40px;height:40px;left:-50px;animation:fazer1 .6s linear infinite}.falling-astroid-imgs span:nth-child(2){background:url(' . \JURI::root() . 'media/astroid/assets/images/astroid/3.png) center no-repeat;width:35px;height:35px;left:40px;top:-40px;animation:fazer2 .4s linear infinite}.falling-astroid-imgs span:nth-child(3){background:url(' . \JURI::root() . 'media/astroid/assets/images/astroid/4.png) center no-repeat;width:30px;height:30px;left:-10px;top:-40px;animation:fazer3 .4s linear infinite;animation-delay:-1s}.falling-astroid-imgs span:nth-child(4){background:url(' . \JURI::root() . 'media/astroid/assets/images/astroid/3.png) center no-repeat;width:20px;height:20px;left:0;top:-80px;animation:fazer4 1s linear infinite;animation-delay:-1s}.falling-astroid-imgs span:nth-child(5){background:url(' . \JURI::root() . 'media/astroid/assets/images/astroid/4.png) center no-repeat;width:20px;height:20px;left:-30px;top:-25px;animation:fazer1 .2s linear infinite}.falling-astroid-imgs span:nth-child(6){background:url(' . \JURI::root() . 'media/astroid/assets/images/astroid/3.png) center no-repeat;width:10px;height:10px;left:-50px;top:-90px;animation:fazer4 1s linear infinite;animation-delay:-1s}.falling-astroid-imgs span:nth-child(7){background:url(' . \JURI::root() . 'media/astroid/assets/images/astroid/4.png) center no-repeat;width:15px;height:15px;left:25px;top:-25px;transform:rotate(-45deg);animation:fazer2 .4s linear infinite}.falling-astroid-imgs span:nth-child(8){background:url(' . \JURI::root() . 'media/astroid/assets/images/astroid/9.png) center no-repeat;width:10px;height:15px;left:-50px;top:-60px;animation:fazer3 .4s linear infinite;animation-delay:-1s}');
?>
<div class="astroid-loading falling-astroid-container">
    <div class="falling-astroid-logo">
        <div class="falling-astroid-imgs">
            <span></span>
            <span></span>
            <span></span>
            <span></span>
            <span></span>
            <span></span>
            <span></span>
            <span></span>
        </div>
        <div class="falling-astroid-img"></div>
    </div>
    <div class="falling-astroid">
        <span></span>
        <span></span>
        <span></span>
        <span></span>
    </div>
</div>