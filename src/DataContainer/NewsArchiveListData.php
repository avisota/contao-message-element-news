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
use Avisota\Contao\Selectri\Model\Flat\SQLListSelectAbleNode;
use Avisota\Contao\Selectri\Model\Tree\SQLAdjacencyTreeDataConfigWithItems;
use Avisota\Contao\Selectri\Model\Tree\SQLAdjacencyTreeDataWithItems;
use Contao\BackendUser;
use Contao\Database;
use ContaoCommunityAlliance\DcGeneral\Data\DefaultDataProvider;
use Hofff\Contao\Selectri\Exception\SelectriException;
use Hofff\Contao\Selectri\Model\AbstractData;
use Hofff\Contao\Selectri\Model\Flat\SQLListData;
use Hofff\Contao\Selectri\Model\Flat\SQLListDataConfig;
use Hofff\Contao\Selectri\Model\Flat\SQLListNode;
use Hofff\Contao\Selectri\Model\Tree\SQLAdjacencyTreeDataConfig;
use Hofff\Contao\Selectri\Util\Icons;
use Hofff\Contao\Selectri\Util\SQLUtil;
use Hofff\Contao\Selectri\Widget;
use Iterator;

/**
 * Class NewsArchiveListData
 *
 * @package Avisota\Contao\Message\Element\News\DataContainer
 */
class NewsArchiveListData extends AbstractData
{
    use DatabaseTrait;

    /**
     * NewsArchiveListData constructor.
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
     * @param null $key
     *
     * @return array
     *
     * @throws SelectriException
     */
    public function browseFrom($key = null)
    {
        $listData = new SQLListData(
            $this->getWidget(),
            $this->getDatabase(),
            $this->prepareListDataConfig()
        );

        list($newsArchiveLevels, $key) = $listData->browseFrom($key);

        $levels = new \ArrayIterator();
        while ($newsArchive = $newsArchiveLevels->current()) {
            $node = $newsArchive->getData();

            if ($this->isEmptyArchive($node)) {
                $newsArchiveLevels->next();

                continue;
            }

            $node['_isSelectable'] = false;
            $node['_key']          = 'tl_news_archive::' . $node['_key'];

            $listNode = new SQLListSelectAbleNode($listData, $node);

            $levels->append($listNode);

            $newsArchiveLevels->next();
        }

        return array($levels, $key);
    }

    /**
     * check if news archive is empty.
     *
     * @param $node
     *
     * @return bool
     */
    protected function isEmptyArchive($node)
    {
        $dataProvider = new DefaultDataProvider();
        $dataProvider->setBaseConfig(
            array(
                'source' => 'tl_news'
            )
        );

        $count = $dataProvider->getCount(
            $dataProvider->getEmptyConfig()->setFilter(
                array(
                    array(
                        'property'  => 'pid',
                        'value'     => $node['id'],
                        'operation' => '='
                    )
                )
            )
        );

        if (intval($count) <= 0) {
            return true;
        }

        return false;
    }

    /**
     * Prepare the list data configuration.
     *
     * @return SQLListDataConfig
     */
    protected function prepareListDataConfig()
    {
        $user   = BackendUser::getInstance();
        $config = new SQLListDataConfig();

        $config->setTable('tl_news_archive');
        $config->setKeyColumn('id');
        $config->addColumns($this->getColumns());
        // Todo is search column must be configured
        $config->addSearchColumns('title');
        $config->setOrderByExpr('title');
        $config->setLabelCallback($this->prepareLabelCallback($config));
        $config->setIconCallback($this->prepareIconCallback());

        if (!$user->isAdmin
            && count($user->news) > 0
        ) {
            $config->setConditionExpr('id IN (' . implode(', ', $user->news) . ')');
        }

        return $config;
    }

    /**
     * Get the news archive columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        $database = $this->getDatabase();

        $properties = array();
        foreach ($database->listFields('tl_news_archive') as $property) {
            if (!array_key_exists('origtype', $property)) {
                continue;
            }

            array_push($properties, $property['name']);
        }

        return $properties;
    }

    /**
     * Prepare the label callback.
     *
     * @param $config
     *
     * @return callable
     */
    protected function prepareLabelCallback($config)
    {
        $labelFormatter = SQLUtil::createLabelFormatter(
            $this->getDatabase(),
            $config->getTable(),
            $config->getKeyColumn()
        );

        return $labelFormatter->getCallback();
    }

    /**
     * Prepare the icon callback
     *
     * @return array
     */
    public function prepareIconCallback()
    {
        return array(
            __CLASS__,
            'getNewsArchiveIconCallback'
        );
    }

    /**
     * Get news archive table icon callback.
     *
     * @return string
     */
    public function getNewsArchiveIconCallback()
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
        return false;
    }

    /**
     * @see \Hofff\Contao\Selectri\Model\Data::isBrowsable()
     */
    public function isBrowsable()
    {
        return true;
    }
}
