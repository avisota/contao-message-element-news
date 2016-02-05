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

use SelectriContaoTableDataFactory;
use SelectriLabelFormatter;

/**
 * Class ArticleIdField
 *
 * @package Avisota\Contao\Message\Element\Article\DataContainer
 */
class NewsIdField
{
    /**
     * @return SelectriContaoTableDataFactory
     */
    public static function getDataForSelectri()
    {
        /** @var SelectriContaoTableDataFactory $data */
        $data = SelectriContaoTableDataFactory::create();
        $data->setItemTable('tl_news');
        $data->getConfig()
            ->setItemLabelCallback(
                SelectriLabelFormatter::create('%s (ID %s)', array('headline', 'id'))
                    ->getCallback()
            );
        $data->getConfig()
            ->setItemSearchColumns(array('headline'));
        $data->getConfig()
            ->setItemConditionExpr('tstamp > 0');
        $data->getConfig()
            ->setItemOrderByExpr('date DESC, time DESC');
        return $data;
    }
}
