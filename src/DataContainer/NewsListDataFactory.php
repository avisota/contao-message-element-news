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

use Avisota\Contao\Selectri\DataContainer\DatabaseTrait;
use Contao\Database;
use Hofff\Contao\Selectri\Model\Data;
use Hofff\Contao\Selectri\Model\DataFactory;
use Hofff\Contao\Selectri\Widget;

/**
 * Class NewsListDataFactory
 *
 * @package Avisota\Contao\Message\Element\News\DataContainer
 */
class NewsListDataFactory implements DataFactory
{
    use DatabaseTrait;

    /**
     * NewsListDataFactory constructor.
     */
    public function __construct()
    {
        $this->setDatabase(Database::getInstance());
    }

    /**
     * @see Widget
     *
     * @param mixed $params Configuration parameters (usally the eval array of
     *                      the DCA field the widget using this factory)
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setParameters($params)
    {
        // TODO: Implement setParameters() method.
    }

    /**
     * @see Widget
     *
     * @param Widget $widget The widget the created data instance will belong to
     *
     * @return Data A new data instance
     */
    public function createData(Widget $widget = null)
    {
        return new NewsController($widget, $this->getDatabase());
    }
}
