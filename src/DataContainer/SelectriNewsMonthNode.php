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
 * Class SelectriNewsMonthNode
 */
class SelectriNewsMonthNode implements \SelectriNode
{

    /**
     * @var SelectriNewsData
     */
    protected $data;

    /**
     * @var \DateTime
     */
    protected $date;

    /**
     * @var SelectriNewsEventNode[]
     */
    protected $news;

    /**
     * @var bool
     */
    protected $isSorted = false;

    /**
     * SelectriNewsMonthNode constructor.
     *
     * @param SelectriNewsData $data
     * @param \DateTime $date
     */
    public function __construct(SelectriNewsData $data, \DateTime $date)
    {
        $this->data   = $data;
        $this->date   = $date;
        $this->news = array();
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @return SelectriNewsEventNode[]
     */
    public function getNews()
    {
        return $this->news;
    }

    /**
     * @param SelectriNewsEventNode[] $news
     *
     * @return static
     */
    public function setNews(array $news)
    {
        $this->news = array();
        $this->addNews($news);
        return $this;
    }

    /**
     * @param SelectriNewsEventNode[] $news
     *
     * @return static
     */
    public function addNews(array $news)
    {
        foreach ($news as $new) {
            $this->addEvent($new);
        }
        return $this;
    }

    /**
     * @param SelectriNewsEventNode $new
     *
     * @return static
     * @internal param SelectriNewsEventNode $news
     *
     */
    public function addNew(SelectriNewsEventNode $new)
    {
        $this->news[] = $new;
        $new->setMonth($this);
        return $this;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->date->format('Y-m');
    }

    /**
     * @return array
     */
    public function getData()
    {
        return array('date' => $this->date->getTimestamp());
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->date->format('Y F');
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
        return false;
    }

    /**
     * @return bool
     */
    public function isOpen()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function hasPath()
    {
        return false;
    }

    /**
     * @return \EmptyIterator
     */
    public function getPathIterator()
    {
        return new \EmptyIterator();
    }

    /**
     * @return bool
     */
    public function hasItems()
    {
        return (bool) count($this->news);
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
        return true;
    }

    /**
     * @return \ArrayIterator
     */
    public function getChildrenIterator()
    {
        if (!$this->isSorted) {
            usort(
                $this->news,
                function (SelectriNewsEventNode $primary, SelectriNewsEventNode $secondary) {
                    return $primary->getDate()->getTimestamp() - $secondary->getDate()->getTimestamp();
                }
            );
            $this->isSorted = true;
        }

        return new \ArrayIterator($this->news);
    }
}
