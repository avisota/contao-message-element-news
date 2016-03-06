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

/**
 * Class SelectriNewsDataFactory
 */
class SelectriNewsDataFactory extends \SelectriAbstractDataFactory
{
    /**
     * @return \SelectriData A new data instance
     */
    public function createData()
    {
        $data = new SelectriNewsData();
        $data->setWidget($this->getWidget());
        return $data;
    }
}
