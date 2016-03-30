<?php

/**
 * Avisota newsletter and mailing system
 * Copyright © 2016 Sven Baumann
 *
 * PHP version 5
 *
 * @copyright  way.vision 2016
 * @author     Sven Baumann <baumann.sv@gmail.com>
 * @package    avisota/contao-message-element-news
 * @license    LGPL-3.0+
 * @filesource
 */

namespace Avisota\Contao\Message\Element\News\DataContainer;

use Hofff\Contao\Selectri\Model\Flat\SQLListDataFactory;
use Hofff\Contao\Selectri\Widget;

/**
 * Class SelectriNewsDataFactory
 */
class SelectriNewsDataFactory extends SQLListDataFactory
{
    /**
     * @param Widget $widget
     *
     * @return SelectriNewsData A new data instance
     */
    public function createData(Widget $widget = null)
    {
        $data = new SelectriNewsData();
        $data->setWidget($widget);
        return $data;
    }
}
