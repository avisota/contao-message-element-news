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

use ContaoCommunityAlliance\Contao\Bindings\ContaoEvents;
use ContaoCommunityAlliance\Contao\Bindings\Events\Image\GenerateHtmlEvent;
use ContaoCommunityAlliance\UrlBuilder\UrlBuilder;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * Class SelectriNewsEventNode
 */
class SelectriNewsEventNode implements \SelectriNode
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
        $label = $this->date->format(\Config::get('dateFormat'));

        if ($this->row['time']) {
            $label .= ' ' . date('H:i', $this->row['time']);
        }

        $label .= ': ' . $this->row['headline'];
        $label .= ' <span style="color: grey;">[' . \NewsArchiveModel::findByPk($this->row['pid'])->title . ']</span>';
        $label .= $this->getInfo();

        return $label;
    }

    protected function getInfo()
    {
        $info = '<div style="margin-left: 16px; padding-top: 6px">';
        $info .= $this->getEditButton();
        $info .= $this->getHeaderButton();
        $info .= $this->getPublishedIcon();
        $info .= '</div>';

        return $info;
    }

    protected function getEditButton()
    {
        $urlBuilder = new UrlBuilder();
        $urlBuilder->setPath('contao/main.php')
            ->setQueryParameter('do', 'news')
            ->setQueryParameter('table', 'tl_content')
            ->setQueryParameter('id', $this->row['id'])
            ->setQueryParameter('popup', 1)
            ->setQueryParameter('rt', \RequestToken::get());

        $button = '<a href="' . $urlBuilder->getUrl() . '" ' . $this->getOnClickOpenModalIFrame() . '>' . $this->getOperationImage('edit.gif') . '</a>';

        return $button;
    }

    protected function getHeaderButton()
    {
        $urlBuilder = new UrlBuilder();
        $urlBuilder->setPath('contao/main.php')
            ->setQueryParameter('do', 'news')
            ->setQueryParameter('table', 'tl_news')
            ->setQueryParameter('act', 'edit')
            ->setQueryParameter('id', $this->row['id'])
            ->setQueryParameter('popup', 1)
            ->setQueryParameter('rt', \RequestToken::get());

        $button = '<a href="' . $urlBuilder->getUrl() . '" ' . $this->getOnClickOpenModalIFrame() . '>' . $this->getOperationImage('header.gif') . '</a>';

        return $button;
    }

    protected function getPublishedIcon()
    {
        $icon = 'visible.gif';
        if ($this->row['published'] < 1) {
            $icon = 'invisible.gif';
        }

        return $this->getOperationImage($icon);
    }

    protected function getOperationImage($icon)
    {
        global $container;
        /** @var EventDispatcher $eventDispatcher */
        $eventDispatcher = $container['event-dispatcher'];

        /** @var GenerateHtmlEvent $imageEvent */
        $imageEvent = $eventDispatcher->dispatch(
            ContaoEvents::IMAGE_GET_HTML,
            new GenerateHtmlEvent(
                $icon,
                '',
                'style="padding-left: 6px;"'
            )
        );

        return $imageEvent->getHtml();
    }

    protected function getOnClickOpenModalIFrame()
    {
        return 'onclick="Backend.openModalIframe({\'width\':768,\'url\':this.href});return false"';
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
