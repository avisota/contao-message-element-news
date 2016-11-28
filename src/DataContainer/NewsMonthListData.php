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
use Avisota\Contao\Selectri\Model\Flat\SQLListDataGroupedConfig;
use Avisota\Contao\Selectri\Model\Flat\SQLListSelectAbleNode;
use Contao\BackendUser;
use Contao\Database;
use Contao\Widget;
use Hofff\Contao\Selectri\Exception\SelectriException;
use Hofff\Contao\Selectri\Model\AbstractData;
use Hofff\Contao\Selectri\Model\Flat\SQLListData;
use Hofff\Contao\Selectri\Model\Node;
use Hofff\Contao\Selectri\Util\Icons;
use Iterator;

/**
 * Class NewsMonthListData
 *
 * @package Avisota\Contao\Message\Element\News\DataContainer
 */
class NewsMonthListData extends AbstractData
{
    use DatabaseTrait;

    const SEARCH_ABLE = false;

    const BROWSE_ABLE = true;

    /**
     * NewsYearListData constructor.
     *
     * @param Widget   $widget
     *
     * @param Database $database
     */
    public function __construct(Widget $widget, Database $database)
    {
        parent::__construct($widget);
        $this->setDatabase($database);
    }

    /**
     * @see \Hofff\Contao\Selectri\Model\Data::browseFrom()
     *
     * @param null $chunks
     *
     * @return array
     *
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function browseFrom($chunks = null)
    {
        $listData = new SQLListData(
            $this->getWidget(),
            $this->getDatabase(),
            $this->prepareListDataConfig($chunks)
        );

        list($newsArchiveLevels, $key) = $listData->browseFrom(null);

        $levels = new \ArrayIterator();
        while ($newsArchive = $newsArchiveLevels->current()) {
            $node = $newsArchive->getData();

            $node['_isSelectable'] = false;
            $node['_key']          = implode(
                '::',
                array_merge(
                    $chunks,
                    array('month', $node['month'])
                )
            );

            $listNode = new SQLListSelectAbleNode($listData, $node);

            $levels->append($listNode);

            $newsArchiveLevels->next();
        }

        return array($levels, implode('::', $chunks));
    }

    /**
     * Prepare the list grouped data configuration.
     *
     * @param $chunks
     *
     * @return SQLListDataGroupedConfig
     */
    protected function prepareListDataConfig($chunks)
    {
        $config = new SQLListDataGroupedConfig();

        $config->setTable('tl_news');
        $config->setKeyColumn('id');
        $config->setColumns('DATE_FORMAT(FROM_UNIXTIME(date), \'%%m\') as month');
        $config->setConditionExpr($this->prepareConditionExpression($chunks));
        $config->setGroupByParameter('month');
        $config->setLabelCallback($this->prepareLabelCallback());
        $config->setIconCallback($this->prepareIconCallback());

        return $config;
    }

    /**
     * Prepare the condition expression.
     *
     * @param $chunks
     *
     * @return string
     */
    protected function prepareConditionExpression($chunks)
    {
        $expression = 'pid=' . $chunks[1];

        $date = new \DateTime();

        $date->modify('first day of January' . $chunks[3]);
        $date->modify('yesterday');
        $expression .= ' AND date > ' . $date->getTimestamp();

        $date->modify('last day of December' . $chunks[3]);
        $date->modify('tomorrow');
        $expression .= ' AND date < ' . $date->getTimestamp();

        return $expression;
    }

    /**
     * Prepare the label callback.
     *
     * @return array
     */
    protected function prepareLabelCallback()
    {
        return array(
            __CLASS__,
            'prepareMonthLabel'
        );
    }

    /**
     * Prepare the label for month.
     *
     * @param Node $node
     *
     * @return mixed
     */
    public function prepareMonthLabel(Node $node)
    {
        global $container;

        $translator = $container['translator'];

        return $translator->translate($node->getData()['month'] - 1, 'MONTHS');
    }

    /**
     * Prepare the icon callback.
     *
     * @return array
     */
    public function prepareIconCallback()
    {
        return array(
            __CLASS__,
            'prepareNewsArchiveMonthIconCallback'
        );
    }

    /**
     * Prepare news archive icon callback.
     *
     * @return string
     */
    public function prepareNewsArchiveMonthIconCallback()
    {
        $user = BackendUser::getInstance();

        return sprintf(
            'system/themes/%s/images/%s',
            $user->backendTheme,
            Icons::getTableIcon('tl_news_archive')
        );
    }

    /**
     * @throws SelectriException If this data instance is not configured correctly
     *
     * @return void
     */
    public function validate()
    {
        // Do nothing, is ever valid.
    }

    /**
     * Returns an iterator over nodes identified by the given primary
     * keys.
     *
     * The returned nodes should NOT be traversed recursivly through the node's
     * getChildrenIterator method.
     *
     * @param         array <string> $keys An array of primary key values in their
     *                      string representation
     * @param boolean $selectableOnly
     *
     * @return Iterator<Node> An iterator over the nodes identified by
     *        the given primary keys
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getNodes(array $keys, $selectableOnly = true)
    {
        // The news archive don´t get nodes.
    }

    /**
     * Filters the given primary keys for values identifing only existing
     * records.
     *
     * @param array <string> $keys An array of primary key values in their
     *              string representation
     *
     * @return array<string> The input array with all invalid values removed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function filter(array $keys)
    {
        // The news archive don´t filter nodes.
    }

    /**
     * @see \Hofff\Contao\Selectri\Model\Data::isSearchable()
     */
    public function isSearchable()
    {
        return NewsMonthListData::SEARCH_ABLE;
    }

    /**
     * @see \Hofff\Contao\Selectri\Model\Data::isBrowsable()
     */
    public function isBrowsable()
    {
        return NewsMonthListData::BROWSE_ABLE;
    }
}
