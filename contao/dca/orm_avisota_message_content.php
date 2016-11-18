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
    'inputType' => 'avisotaSelectriWithItems',
    'eval'      => array(
        'min'  => 1,
        'max'  => 99,
        'data' => 'Avisota\Contao\Message\Element\News\DataContainer\NewsListDataFactory',
        'canonical' => true

    ),
    'field'     => array(
        'type'     => 'array',
        'nullable' => true,
    ),
);

$GLOBALS['TL_DCA']['orm_avisota_message_content']['fields']['newsTemplate'] = array
(
    'label'            => &$GLOBALS['TL_LANG']['orm_avisota_message_content']['newsTemplate'],
    'exclude'          => true,
    'inputType'        => 'select',
    'field'            => array(
        'type'     => 'string',
        'nullable' => true,
    ),
);
