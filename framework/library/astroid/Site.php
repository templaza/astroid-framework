<?php

/**
 * @package   Astroid Framework
 * @author    JoomDev https://www.joomdev.com
 * @copyright Copyright (C) 2009 - 2020 JoomDev.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

namespace Astroid;

defined('_JEXEC') or die;

class Site extends Helper\Client
{
    public function onAfterRender()
    {
        if (!Framework::getTemplate()->isAstroid) {
            return;
        }
        Helper::triggerEvent('onBeforeAstroidRender'); // at last process all astroid:include
        Component\Utility::meta(); // site meta
        Component\Utility::layout(); // site layout
        Component\Utility::typography(); // site typography
        Component\Utility::background(); // site background
        Component\Utility::colors(); // site colors
        Component\Utility::smoothScroll(); // smooth scroll utility
        Component\Utility::custom(); // site custom codes
        Component\LazyLoad::run(); // to execute lazy load
        Component\Includer::run(); // at last process all astroid:include
        Framework::getDocument()->compress(); // compress the html
        Helper::triggerEvent('onAfterAstroidRender'); // at last process all astroid:include
    }

    protected function rate()
    {
        $this->checkAuth();

        $app = \JFactory::getApplication();
        $id = $app->input->post->get('id', 0, 'INT');
        $vote = $app->input->post->get('vote', 0, 'INT');

        if (empty($id)) {
            throw new \Exception(\JText::_('ASTROID_ARTICLE_NOT_FOUND'), 404);
        }
        if ($vote < 0 || $vote > 5) {
            throw new \Exception(\JText::_('ASTROID_INVALID_RATING'), 0);
        }

        $article = new Component\Article($id);
        $this->response($article->vote($vote));
    }

    protected function clearCache()
    {
        $template = Framework::getTemplate()->template;
        Helper::clearCacheByTemplate($template);
        $this->response(['message' => \JText::_('TPL_ASTROID_SYSTEM_MESSAGES_CACHE')]);
    }

    protected function clearJoomlaCache()
    {
        Helper::clearJoomlaCache();
        $this->response(['message' => \JText::_('TPL_ASTROID_SYSTEM_MESSAGES_JCACHE')]);
    }
}
