<?php

jimport('astroid.framework.template');

abstract class AstroidFramework
{

   public static $template = null;
   public static $stylesheets = [];
   public static $javascripts = ['head' => [], 'body' => []];
   public static $styles = [];
   public static $scripts = ['head' => [], 'body' => []];

   public static function getTemplate()
   {
      if (!self::$template) {
         self::$template = self::createTemplate();
      }

      return self::$template;
   }

   public static function setTemplate($template)
   {
      self::$template = $template;
   }

   public static function addStyleSheet($url)
   {
      self::$stylesheets[] = $url;
   }

   public static function addStyleDeclaration($css)
   {
      self::$styles[] = $css;
   }

   public static function addScript($url, $position = 'head')
   {
      self::$javascripts[$position][] = $url;
   }

   public static function addScriptDeclaration($js, $position = 'head')
   {
      self::$scripts[$position][] = $js;
   }

   public static function createTemplate()
   {
      return new AstroidFrameworkTemplate(JFactory::getApplication()->getTemplate(true));
   }
}
