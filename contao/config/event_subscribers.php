<?php

/**
 * Avisota newsletter and mailing system
 * Copyright © 2017 Sven Baumann
 *
 * PHP version 5
 *
 * @copyright  way.vision 2017
 * @author     Sven Baumann <baumann.sv@gmail.com>
 * @package    avisota/contao-message-element-news
 * @license    LGPL-3.0+
 * @filesource
 */

use Avisota\Contao\Message\Element\News\DataContainer\MessageContent;
use Avisota\Contao\Message\Element\News\DataContainer\OptionsBuilder;
use Avisota\Contao\Message\Element\News\DefaultRenderer;

return array(
    new MessageContent(),
    new DefaultRenderer(),
    new OptionsBuilder(),
);
