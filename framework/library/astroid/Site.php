<?php

/**
 * @package   Astroid Framework
 * @author    Astroid Framework Team https://astroidframe.work
 * @copyright Copyright (C) 2023 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

namespace Astroid;
use Joomla\CMS\Factory;

defined('_JEXEC') or die;

class Site extends Helper\Client
{
    public function onAfterRender()
    {
        if (!Framework::getTemplate()->isAstroid) {
            return;
        }
        Helper::triggerEvent('onBeforeAstroidRender'); // at last process all astroid:include
        Component\Utility::layout(); // site layout
        Component\Utility::background(); // site background
        Component\Utility::colors(); // site colors
        Component\Utility::article(); // site article
        Component\Utility::custom(); // site custom codes
        Component\Includer::run(); // at last process all astroid:include
        Framework::getDocument()->compress(); // compress the html
        Helper::triggerEvent('onAfterAstroidRender'); // at last process all astroid:include
    }

    public function onBeforeRender()
    {
        if (!Framework::getTemplate()->isAstroid) {
            return;
        }
        Component\Utility::meta(); // site meta
        Component\Utility::typography(); // site typography
        Helper\Head::styles(); // site Styles
        Component\LazyLoad::run(); // to execute lazy load
        Component\Utility::smoothScroll(); // smooth scroll utility
        Helper\Head::scripts(); // site scripts
    }

    protected function rate()
    {
        $this->checkAuth();

        $app = Factory::getApplication();
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
