<?php

/**
 * Avisota newsletter and mailing system
 * Copyright (C) 2013 Tristan Lins
 *
 * PHP version 5
 *
 * @copyright  bit3 UG 2013
 * @author     Tristan Lins <tristan.lins@bit3.de>
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
	'type'    => array('type', 'cell', 'headline'),
	'include' => array('news'),
	'expert'  => array(':hide', 'cssID', 'space')
);

$GLOBALS['TL_DCA']['orm_avisota_message_content']['fields']['news'] = array
(
	'label'     => &$GLOBALS['TL_LANG']['orm_avisota_message_content']['news'],
	'exclude'   => true,
	'inputType' => 'newschooser',
	'eval'      => array('mandatory' => true),
	'field'     => array(
		'nullable' => true,
		'type'     => 'serialized',
		'length'   => 65532,
	)
);
