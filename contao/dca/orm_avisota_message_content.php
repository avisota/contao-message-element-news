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

/**
 * Table orm_avisota_message_content
 * Entity Avisota\Contao:MessageContent
 */
$GLOBALS['TL_DCA']['orm_avisota_message_content']['metapalettes']['news'] = array
(
    'type'      => array('cell', 'type', 'headline'),
    'include'   => array('newsId', 'newsTemplate'),
    'expert'    => array(':hide', 'cssID', 'space'),
    'published' => array('invisible'),
);

$GLOBALS['TL_DCA']['orm_avisota_message_content']['fields']['newsId']       = array
(
    'label'     => &$GLOBALS['TL_LANG']['orm_avisota_message_content']['newsId'],
    'exclude'   => true,
    'inputType' => 'selectri',
    'eval'      => array(
        'min'  => 1,
        'data' => function () {
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
        },
    ),
    'field'     => array(
        'type'     => 'integer',
        'nullable' => true,
    ),
);
$GLOBALS['TL_DCA']['orm_avisota_message_content']['fields']['newsTemplate'] = array
(
    'label'            => &$GLOBALS['TL_LANG']['orm_avisota_message_content']['newsTemplate'],
    'exclude'          => true,
    'inputType'        => 'select',
    'options_callback' =>
        \ContaoCommunityAlliance\Contao\Events\CreateOptions\CreateOptionsEventCallbackFactory
            ::createTemplateGroupCallback('news_'),
    'field'            => array(
        'type'     => 'string',
        'nullable' => true,
    ),
);
