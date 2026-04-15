<?php
/**
 * @package   Astroid Framework
 * @author    Astroid Framework Team https://astroidframe.work
 * @copyright Copyright (C) 2026 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3 or Later
 */
namespace Astroid\Helper;
use Astroid\Framework;
use Astroid\Helper;
class Transform {
    public mixed $id = null;
    public mixed $params = null;
    public mixed $style = null;
    protected array $scenes = [];
    protected array $scroll_settings = [];
    protected array $timeline_settings = [];
    protected bool $is_loaded = false;
    public function __construct($element)
    {
        $this->id = $element->id;
        $this->params = $element->params;
        $transform_scenes = new SubForm($this->params->get('transform_scenes',''));

        if (!empty($transform_scenes->getData())) {
            foreach ($transform_scenes->getData() as $scene) {
                $animations = $scene->params->toArray();
                $from = [];
                $to = [];
                $config = [];
                foreach ($animations as $animation => $value) {
                    if (!empty($value)) {
                        if (Helper::isJsonString($value)) {
                            $tmp = json_decode($value, true);
                            $animation_name = match ($animation) {
                                'translate_x' => 'x',
                                'translate_y' => 'y',

                                'rotate', 'rotate_z' => 'rotation',
                                'rotate_x' => 'rotateX',
                                'rotate_y' => 'rotateY',

                                'scale' => 'scale',
                                'scale_x' => 'scaleX',
                                'scale_y' => 'scaleY',

                                'skew_x' => 'skewX',
                                'skew_y' => 'skewY',

                                'opacity' => 'opacity',

                                default => $animation
                            };
                            if (isset($tmp['from']) && $tmp['from'] !== '') {
                                $from[$animation_name] = $tmp['from'];
                            }
                            if (isset($tmp['to']) && $tmp['to'] !== '') {
                                $to[$animation_name] = $tmp['to'];
                            }
                        } else {
                            $config[$animation] = $value;
                        }
                    }
                }
                if (!empty($from) && !empty($to)) {
                    $to = array_merge($to, $config);
                    $this->scenes[] = [
                        'from' => $from,
                        'to' => $to
                    ];
                } elseif (!empty($from)) {
                    $from = array_merge($from, $config);
                    $this->scenes[] = [
                        'from' => $from
                    ];
                } elseif (!empty($to)) {
                    $to = array_merge($to, $config);
                    $this->scenes[] = [
                        'to' => $to
                    ];
                }
            }
            $start = $this->params->get('transform_start', '');
            $end = $this->params->get('transform_end', '');
            $transform_scrub = $this->params->get('transform_scrub', 3);
            $transform_repeat = $this->params->get('transform_repeat', 0);
            $transform_pin = $this->params->get('transform_pin', 0);
            $transform_markers = $this->params->get('transform_markers', 0);
            $transform_toggle_actions = $this->params->get('transform_toggle_actions', '');
            $transform_element = $this->params->get('transform_element', '');

            if (!empty($start)) {
                $this->scroll_settings['start'] = $start;
            }
            if (!empty($end)) {
                $this->scroll_settings['end'] = $end;
            }
            if (!empty($transform_scrub)) {
                $this->scroll_settings['scrub'] = $transform_scrub;
            }
            if (!empty($transform_repeat)) {
                $this->timeline_settings['repeat'] = $transform_repeat;
            }
            if (!empty($transform_pin)) {
                $this->scroll_settings['pin'] = true;
                $transform_pin_spacing = $this->params->get('transform_pin_spacing', 1);
                if (empty($transform_pin_spacing)) {
                    $this->scroll_settings['pinSpacing'] = false;
                }
            }
            if (!empty($transform_markers)) {
                $this->scroll_settings['markers'] = true;
            }
            if (!empty($transform_toggle_actions)) {
                $this->scroll_settings['toggleActions'] = $transform_toggle_actions;
            }
            if (!empty($this->scenes)) {
                $element->addAttribute('data-transform-scenes', htmlspecialchars(json_encode($this->scenes), ENT_QUOTES, 'UTF-8'));
                if (!empty($this->scroll_settings)) {
                    $element->addAttribute('data-transform-scroll', htmlspecialchars(json_encode($this->scroll_settings), ENT_QUOTES, 'UTF-8'));
                }
                if (!empty($this->timeline_settings)) {
                    $element->addAttribute('data-transform-timeline', htmlspecialchars(json_encode($this->timeline_settings), ENT_QUOTES, 'UTF-8'));
                }
                if (!empty($transform_element)) {
                    $element->addAttribute('data-transform-trigger', htmlspecialchars($transform_element, ENT_QUOTES, 'UTF-8'));
                }
                $this->addAssets();
                $this->is_loaded = true;
            }
        }
    }
    public function addAssets(): void
    {
        $document = Framework::getDocument();
        $document->loadGSAP('ScrollTrigger');
        $document->getWA()->registerAndUseScript('astroid.transform.js', 'media/astroid/assets/vendor/transform/js/index.min.js', ['relative' => true, 'version' => 'auto'], [], ['astroid.gsap']);
    }

    public function isLoaded(): bool
    {
        return $this->is_loaded;
    }
}