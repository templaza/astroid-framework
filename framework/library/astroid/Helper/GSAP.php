<?php

/**
 * @package   Astroid Framework
 * @author    Astroid Framework Team https://astroidframe.work
 * @copyright Copyright (C) 2023 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

namespace Astroid\Helper;

use Astroid\Framework;

defined('_JEXEC') or die;

class GSAP {
    public ?\Astroid\Document $document = null;
    public string $element = '';
    public function __construct($element = '') {
        $this->document = Framework::getDocument();
        $this->element = $element;
    }
    public function textEffect($type = ''): void
    {
        if (empty($type) || empty($this->element)) {
            return;
        }
        $this->document->loadGSAP('SplitText');
        $this->document->loadGSAP('ScrollTrigger');
        switch ($type) {
            case 'screenreader':
                $this->document->addScriptDeclaration('
document.addEventListener("DOMContentLoaded", function() {
    var splitText = SplitText.create("'.$this->element.'", { type: "words", aria: "hidden" });
    gsap.from(splitText.words, {
        opacity: 0,
        duration: 0.8,
        ease: "sine.out",
        stagger: 0.1,
        scrollTrigger: {
          trigger: "'.$this->element.'",
          start: "top 80%",
          toggleActions: "play none none none"
        }
    });
});');
        }
    }
}