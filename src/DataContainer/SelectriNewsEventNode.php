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

use Hofff\Contao\Selectri\Model\Node;

/**
 * Class SelectriNewsEventNode
 */
class SelectriNewsEventNode implements Node
{
    /**
     * @var SelectriNewsData
     */
    protected $data;

    /**
     * @var array
     */
    protected $row;

    /**
     * @var \DateTime
     */
    protected $date;

    /**
     * @var SelectriNewsMonthNode
     */
    protected $month;

    /**
     * SelectriNewsNewNode constructor.
     *
     * @param SelectriNewsData   $data
     * @param                    $row
     * @param \DateTime          $date
     */
    public function __construct(SelectriNewsData $data, $row, \DateTime $date)
    {
        $this->data = $data;
        $this->row  = $row;
        $this->date = $date;
    }

    /**
     * @return array
     */
    public function getRow()
    {
        return $this->row;
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @return SelectriNewsMonthNode
     */
    public function getMonth()
    {
        if ($this->month) {
            return $this->month;
        }

        return new SelectriNewsMonthNode($this->data, $this->date);
    }

    /**
     * @param SelectriNewsMonthNode $month
     *
     * @return static
     */
    public function setMonth($month)
    {
        $this->month = $month;
        return $this;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->row['id'] . '@' . $this->date->getTimestamp();
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->row;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        $monthNode = new SelectriNewsMonthNode($this->data, $this->getDate());
        $monthNode->addNews(array($this));

        return $monthNode->getLabel();
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return '';
    }

    /**
     * @param $key
     *
     * @return string
     */
    public function getAdditionalInputName($key)
    {
        $name = $this->data->getWidget()->getAdditionalInputBaseName();
        $name .= '[' . $this->getKey() . ']';
        $name .= '[' . $key . ']';
        return $name;
    }

    /**
     * @return string
     */
    public function getIcon()
    {
        return 'system/modules/news/assets/icon.gif';
    }

    /**
     * @return bool
     */
    public function isSelectable()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function isOpen()
    {
        return false;
    }

    /**
     * @return bool
     */
    public function hasPath()
    {
        return true;
    }

    /**
     * @return \ArrayIterator
     */
    public function getPathIterator()
    {
        return new \ArrayIterator(array($this->getMonth()));
    }

    /**
     * @return bool
     */
    public function hasItems()
    {
        return false;
    }

    /**
     * @return \EmptyIterator
     */
    public function getItemIterator()
    {
        return new \EmptyIterator();
    }

    /**
     * @return bool
     */
    public function hasSelectableDescendants()
    {
        return false;
    }

    /**
     * @return \EmptyIterator
     */
    public function getChildrenIterator()
    {
        return new \EmptyIterator();
    }
}
